<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajePlanificacionTrasladoOperativoVO extends Master2 {
	public $idViajePlanificacionTrasladoOperativo = ["valor" => "", 
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
	public $idTipoBien = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de vehículo",
						"referencia" => "",
	];
	public $idViajeVehiculo = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "vehículo",
						"referencia" => "",
	];
	public $idViajeVehiculoDetalle = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "vehículo de alquiler",
						"referencia" => "",
	];
	public $kmDiarios = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "float",
		"nombre" => "km diarios",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $cantidadDias = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "cantidad de días",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $costo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "float",
		"nombre" => "monto de la compra",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_planificacionTrasladoOperativo');
		$this->setFieldIdName('idViajePlanificacionTrasladoOperativo');
		$this->idViaje['referencia'] =  new ViajeVO();
		$this->idTipoBien['referencia'] =  new TipoBienVO();
		$this->idViajeVehiculoDetalle['referencia'] =  new ViajeVehiculoDetalleVO();
		$this->idViajeVehiculo['referencia'] =  new ViajeVehiculoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if($this->idTipoBien['valor'] == '1'){ // PROPIO
			$this->idViajeVehiculo['obligatorio'] = TRUE;
			$this->idViajeVehiculoDetalle['valor'] = null;
			$this->idViajeVehiculoDetalle['obligatorio'] = FALSE;
		} else {                                // ALQUILADO
			$this->idViajeVehiculoDetalle['obligatorio'] = TRUE;
			$this->idViajeVehiculo['valor'] = null;
			$this->idViajeVehiculo['obligatorio'] = FALSE;
		}
		if (strtotime(convertDateEsToDb($this->fechaSalida['valor'])) > strtotime(convertDateEsToDb($this->fechaLlegada['valor'])) ) {
			$resultMessage = 'La fecha de Salida no puede ser menor que la fecha de Llegada.';
		}
        return $resultMessage;
 	}

	public function deleteDataArray($idViaje, $idViajePlanificacionTrasladoOperativoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idViaje = '.$idViaje;
		if($idViajePlanificacionTrasladoOperativoArray){
			$sql .= ' and idViajePlanificacionTrasladoOperativo not in ('.implode(",", $idViajePlanificacionTrasladoOperativoArray).')';
		}
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if(!$ro->execute()){
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			}
			$this->result->setMessage('Los datos fueron ACTUALIZADOS con éxito.');
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
	/*public function getComboList($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idPlanificacionTrasladoOperativo as data, b.idViajePlanificacionTrasladoOperativo as selected
				from empleados as a
				left JOIN viajes_empleados as b on a.idPlanificacionTrasladoOperativo = b.idPlanificacionTrasladoOperativo ';
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
if($_GET['debug'] == 'ViajePlanificacionTrasladoOperativoVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajePlanificacionTrasladoOperativoVO();
	//print_r($kk->getAllRows());
	$kk->idViajePlanificacionTrasladoOperativo = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
