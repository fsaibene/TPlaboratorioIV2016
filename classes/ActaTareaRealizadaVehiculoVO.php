<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaRealizadaVehiculoVO extends Master2 {
	public $idActaTareaRealizadaVehiculo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "ID",
	];
	public $idActaTareaRealizada = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "ATR",
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
		$this->setTableName('actaTareasRealizada_vehiculos');
		$this->setFieldIdName('idActaTareaRealizadaVehiculo');
		$this->idActaTareaRealizada['referencia'] =  new ActaTareaRealizadaVO();
		$this->idVehiculo['referencia'] =  new VehiculoVO();
	}

	/*
	 * Funcion que valida cierta logica de negocios
	 */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}

	public function getComboList($data = null){
		$sql = 'select getVehiculo(a.idVehiculo) as label, a.idVehiculo as data, b.idActaTareaRealizada as selected
				from vehiculos as a
				left JOIN actaTareasRealizada_vehiculos as b on a.idVehiculo = b.idVehiculo ';
		if($data['valorCampoWhere'])
			$sql .= ' and b.'.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		else
			$sql .= ' and b.'.$data['nombreCampoWhere'].' is null ';
		$sql .= ' group by data ';
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

	/*public function getVehiculosPor($data = null){
		$sql = 'select getVehiculo(e.idVehiculo) as value, a.idVehiculo as id, b.idActaTareaRealizadaVehiculo as selected
				from proyectosComisiones_vehiculos as a
				left JOIN actaTareasRealizada_proyectosComisionesVehiculos as b on a.idVehiculo = b.idVehiculo  ';
		$sql .= ' inner join vehiculos as e using (idVehiculo)
		         where id = '.$data['id'].'
				group by id
				order by value';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}*/
}
/*if($_GET['action'] == 'json' && $_GET['type'] == 'getVehiculosPor'){
	$aux = new ActaTareaRealizadaVehiculoVO();
	$data = array();
	$data['id'] = $_GET['id'];
	$aux->{$_GET['type']}($data);
}*/

// debug zone
if($_GET['debug'] == 'ActaTareaRealizadaVehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new ActaTareaRealizadaVehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoUnidadEconomica = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
