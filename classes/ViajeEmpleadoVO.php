<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajeEmpleadoVO extends Master2 {
	public $idViajeEmpleado = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idViaje = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "comisión",
						"referencia" => "",
	];
	public $idEmpleado = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "empleado",
						"referencia" => "",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_empleados');
		$this->setFieldIdName('idViajeEmpleado');
		$this->idViaje['referencia'] =  new ViajeVO();
		$this->idEmpleado['referencia'] =  new EmpleadoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		/*if (strtotime(convertDateEsToDb($this->fechaInicio['valor'])) > strtotime(convertDateEsToDb($this->fechaFin['valor'])) ) {
			$resultMessage = 'La fecha Fin no puede ser menor que la fecha Inicio.';
		}*/
        return $resultMessage;
 	}

	public function getComboList($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idEmpleado as data, b.idViajeEmpleado as selected
				from empleados as a
				left JOIN viajes_empleados as b on a.idEmpleado = b.idEmpleado ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' left join empleadosRelacionLaboral as erl on a.idEmpleado = erl.idEmpleado
		         where true and erl.fechaEgreso is null
		         group by data ';
		$sql .= ' order by label';
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if($ro->execute()){
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				$this->result->setData($rs);
				$this->result->setStatus(STATUS_OK);
			}else{
				$this->result->setData($this);
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			}
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getComboList2($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, idViajeEmpleado as data
				from empleados as a
				inner JOIN viajes_empleados as b using(idEmpleado)';
		$sql .= ' where true ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by label';
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if($ro->execute()){
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				$this->result->setData($rs);
				$this->result->setStatus(STATUS_OK);
			}else{
				$this->result->setData($this);
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			}
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}

	public function deleteDataArray($idViaje, $idEmpleadoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idViaje = '.$idViaje;
		if($idEmpleadoArray){
			$sql .= ' and idEmpleado not in ('.implode(",", $idEmpleadoArray).')';
		}
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if(!$ro->execute()){
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			} else {
				$this->result->setStatus(STATUS_OK);
				$this->result->setMessage("Los datos fueron GUARDADOS con éxito.");
			}
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			//$this->result->setMessage($this->getPDOMessage($e));
			myExceptionHandler($e);
		}
		return $this;
	}

	public function insertDataArray($idViaje, $idEmpleadoArray){
		try{
			foreach($idEmpleadoArray as $idEmpleado){
				$sql = 'insert ignore into '.$this->getTableName().'
						set idViaje = '.$idViaje.', idEmpleado = '.$idEmpleado.', idUsuarioLog = '.$_SESSION['usuarioLogueadoIdUsuario'];
				//echo($sql);
				$ro = $this->conn->prepare($sql);
				if(!$ro->execute()){
					$this->result->setStatus(STATUS_ERROR);
					$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
					break;
				} else {
					$this->result->setStatus(STATUS_OK);
					$this->result->setMessage("Los datos fueron GUARDADOS con éxito.");
				}
			}
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}

	/*public function getViajeEmpleadosPorIdViaje(){
		$data['nombreCampoWhere'] = 'idViaje';
		$data['valorCampoWhere'] = $this->idViaje['valor'];
		$this->getAllRows($data);
		return;
	}*/

}

// debug zone
if($_GET['debug'] == 'ViajeEmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajeEmpleadoVO();
	//print_r($kk->getAllRows());
	$kk->idViajeEmpleado = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
