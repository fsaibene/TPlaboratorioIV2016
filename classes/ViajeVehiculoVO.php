<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajeVehiculoVO extends Master2 {
	public $idViajeVehiculo = ["valor" => "", 
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
	public $idVehiculo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "vehículo",
		"referencia" => "",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_vehiculos');
		$this->setFieldIdName('idViajeVehiculo');
		$this->idViaje['referencia'] =  new ViajeVO();
		$this->idVehiculo['referencia'] =  new VehiculoVO();
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
		$sql = 'select CONCAT(a.marca, " / ", a.modelo, " / ", a.patente) as label, a.idVehiculo as data, b.idViajeVehiculo as selected
				from vehiculos as a
				left JOIN viajes_vehiculos as b on a.idVehiculo = b.idVehiculo ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' where a.habilitado ';
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

	public function getComboList2($data){
		$sql = 'select CONCAT(a.marca, " / ", a.modelo, " / ", a.patente) as label, idViajeVehiculo as data
				from vehiculos as a
				inner JOIN viajes_vehiculos as b using(idVehiculo)
				where true and idViaje = '.$data['idViaje'].'
				order by label';
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

	public function deleteDataArray($idViaje, $idVehiculoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idViaje = '.$idViaje;
		if($idVehiculoArray){
			$sql .= ' and idVehiculo not in ('.implode(",", $idVehiculoArray).')';
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
			myExceptionHandler($e);
		}
		return $this;
	}

	public function insertDataArray($idViaje, $idVehiculoArray){
		try{
			foreach($idVehiculoArray as $idVehiculo){
				$sql = 'insert ignore into '.$this->getTableName().'
						set idViaje = '.$idViaje.', idVehiculo = '.$idVehiculo.', idUsuarioLog = '.$_SESSION['usuarioLogueadoIdUsuario'];
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
}

// debug zone
if($_GET['debug'] == 'ViajeVehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajeVehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idViajeVehiculo = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
