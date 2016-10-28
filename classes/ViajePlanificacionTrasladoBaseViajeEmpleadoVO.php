<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajePlanificacionTrasladoBaseViajeEmpleadoVO extends Master2 {
	public $idViajePtbPce = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idViajePlanificacionTrasladoBase = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "comisión",
						"referencia" => "",
	];
	public $idViajeEmpleado = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "empleado",
						"referencia" => "",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_Ptb_Pce');
		$this->setFieldIdName('idViajePtbPce');
		$this->idViajePlanificacionTrasladoBase['referencia'] =  new ViajePlanificacionTrasladoBaseVO();
		$this->idViajeEmpleado['referencia'] =  new ViajeEmpleadoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){

        return $resultMessage;
 	}

	public function deleteDataArray($idViajePlanificacionTrasladoBase, $idViajeEmpleadoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idViajePlanificacionTrasladoBase = '.$idViajePlanificacionTrasladoBase;
		if($idViajeEmpleadoArray){
			$sql .= ' and idViajeEmpleado not in ('.implode(",", $idViajeEmpleadoArray).')';
		}
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if(!$ro->execute()){
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

	public function getComboList2($data){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, b.idViajeEmpleado as data, GROUP_CONCAT(c.idViajePlanificacionTrasladoBase SEPARATOR ",") as idViajePlanificacionTrasladoBaseArray
				from empleados as a
				inner JOIN viajes_empleados as b using(idEmpleado)
				left join viajes_ptb_pce as c ON c.idViajeEmpleado = b.idViajeEmpleado';
		$sql .= ' where true and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' group by label, data';
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

	public function insertDataArray($idViajePlanificacionTrasladoBase, $idViajeEmpleadoArray){
		try{
			foreach($idViajeEmpleadoArray as $idViajeEmpleado){
				$sql = 'insert ignore into '.$this->getTableName().'
						set
						idViajePlanificacionTrasladoBase = '.$idViajePlanificacionTrasladoBase.',
						idViajeEmpleado = '.$idViajeEmpleado.',
						idUsuarioLog = '.$_SESSION['usuarioLogueadoIdUsuario'];
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

	/*public function getComboList($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idPlanificacionTrasladoBase as data, b.idViajePlanificacionTrasladoBasePtbPce as selected
				from empleados as a
				left JOIN viajes_empleados as b on a.idPlanificacionTrasladoBase = b.idPlanificacionTrasladoBase ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' where true';
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
	}*/
}

// debug zone
if($_GET['debug'] == 'ViajePlanificacionTrasladoBaseViajeEmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajePlanificacionTrasladoBaseViajeEmpleadoVO();
	//print_r($kk->getAllRows());
	$kk->idViajePlanificacionTrasladoBasePtbPce = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
