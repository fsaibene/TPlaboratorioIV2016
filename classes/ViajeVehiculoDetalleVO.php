<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajeVehiculoDetalleVO extends Master2 {
	public $idViajeVehiculoDetalle = ["valor" => "",
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
	public $esVehiculoDeAlquiler = ["valor" => FALSE,
		"obligatorio" => TRUE,
		"tipo" => "bool",
		"nombre" => "vehículo de alquiler",
	];
	public $vehiculoDeAlquiler = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "vehículo de alquiler",
		"longitud" => "64"
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_vehiculosDetalles');
		$this->setFieldIdName('idViajeVehiculoDetalle');
		$this->idViaje['referencia'] =  new ViajeVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		//print_r($this);die();
		if($this->esVehiculoDeAlquiler['valor'] == '1'){
			$this->vehiculoDeAlquiler['obligatorio'] = TRUE;
		}else{
			$this->vehiculoDeAlquiler['obligatorio'] = FALSE;
			$this->vehiculoDeAlquiler['valor'] = null;
		}
        return $resultMessage;
 	}

	public function getComboList2($data){
		$sql = 'select vehiculoDeAlquiler as label, idViajeVehiculoDetalle as data
				from viajes_vehiculosDetalles
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
