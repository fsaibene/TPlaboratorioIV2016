<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProyectoComisionPlanificacionTrasladoBaseProyectoComisionEmpleadoVO extends Master2 {
	public $idProyectoComisionPtbPce = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idProyectoComisionPlanificacionTrasladoBase = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "comisión",
						"referencia" => "",
	];
	public $idProyectoComisionEmpleado = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "empleado",
						"referencia" => "",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('proyectosComisiones_Ptb_Pce');
		$this->setFieldIdName('idProyectoComisionPtbPce');
		$this->idProyectoComisionPlanificacionTrasladoBase['referencia'] =  new ProyectoComisionPlanificacionTrasladoBaseVO();
		$this->idProyectoComisionEmpleado['referencia'] =  new ProyectoComisionEmpleadoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){

        return $resultMessage;
 	}

	public function deleteDataArray($idProyectoComisionPlanificacionTrasladoBase, $idProyectoComisionEmpleadoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idProyectoComisionPlanificacionTrasladoBase = '.$idProyectoComisionPlanificacionTrasladoBase;
		if($idProyectoComisionEmpleadoArray){
			$sql .= ' and idProyectoComisionEmpleado not in ('.implode(",", $idProyectoComisionEmpleadoArray).')';
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
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, b.idProyectoComisionEmpleado as data, GROUP_CONCAT(c.idProyectoComisionPlanificacionTrasladoBase SEPARATOR ",") as idProyectoComisionPlanificacionTrasladoBaseArray
				from empleados as a
				inner JOIN proyectosComisiones_empleados as b using(idEmpleado)
				left join proyectosComisiones_ptb_pce as c ON c.idProyectoComisionEmpleado = b.idProyectoComisionEmpleado';
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

	public function insertDataArray($idProyectoComisionPlanificacionTrasladoBase, $idProyectoComisionEmpleadoArray){
		try{
			foreach($idProyectoComisionEmpleadoArray as $idProyectoComisionEmpleado){
				$sql = 'insert ignore into '.$this->getTableName().'
						set
						idProyectoComisionPlanificacionTrasladoBase = '.$idProyectoComisionPlanificacionTrasladoBase.',
						idProyectoComisionEmpleado = '.$idProyectoComisionEmpleado.',
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
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idPlanificacionTrasladoBase as data, b.idProyectoComisionPlanificacionTrasladoBasePtbPce as selected
				from empleados as a
				left JOIN proyectosComisiones_empleados as b on a.idPlanificacionTrasladoBase = b.idPlanificacionTrasladoBase ';
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
if($_GET['debug'] == 'ProyectoComisionPlanificacionTrasladoBaseProyectoComisionEmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new ProyectoComisionPlanificacionTrasladoBaseProyectoComisionEmpleadoVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoComisionPlanificacionTrasladoBasePtbPce = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
