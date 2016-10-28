<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajePlanificacionTrasladoBaseVO extends Master2 {
	public $idViajePlanificacionTrasladoBase = ["valor" => "", 
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
	public $idTipoTraslado = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de traslado",
						"referencia" => "",
	];
	public $idTipoTransporte = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de transporte",
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
	public $km = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "km",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idDestinoSalida = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "destino de salida",
						"referencia" => "",
	];
	public $idDestinoLlegada = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "destino de llegada",
						"referencia" => "",
	];
	public $fechaArribo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "date",
		"nombre" => "fecha arribo",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $fechaPartida = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "date",
		"nombre" => "fecha partida",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
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

	public $viajeEmpleadoArray;

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('viajes_planificacionTrasladoBase');
		$this->setFieldIdName('idViajePlanificacionTrasladoBase');
		$this->idViaje['referencia'] =  new ViajeVO();
		$this->idViajeVehiculoDetalle['referencia'] =  new ViajeVehiculoDetalleVO();
		$this->idViajeVehiculo['referencia'] =  new ViajeVehiculoVO();
		$this->idTipoTraslado['referencia'] =  new TipoTrasladoVO();
		$this->idTipoTransporte['referencia'] =  new TipoTransporteVO();
		$this->idDestinoSalida['referencia'] =  new DestinoVO();
		$this->idDestinoLlegada['referencia'] =  new DestinoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if (strtotime(convertDateEsToDb($this->fechaSalida['valor'])) > strtotime(convertDateEsToDb($this->fechaLlegada['valor'])) ) {
			$resultMessage = 'La fecha de Salida no puede ser menor que la fecha de Llegada.';
		}
		if($this->idTipoTransporte['valor'] != 6 || $this->idTipoTransporte['valor'] != 7) {
			$this->idViajeVehiculo['referencia'] =  new ViajeVehiculoVO();
			$this->idViajeVehiculoDetalle['referencia'] =  new ViajeVehiculoDetalleVO();
			$this->km['valor'] = '';
		}
        return $resultMessage;
 	}

	public function deleteDataArray($idViaje, $idViajePlanificacionTrasladoBaseArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idViaje = '.$idViaje;
		if($idViajePlanificacionTrasladoBaseArray){
			$sql .= ' and idViajePlanificacionTrasladoBase not in ('.implode(",", $idViajePlanificacionTrasladoBaseArray).')';
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

	public function insertData(){
		//print_r($this); die('uno');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				//print_r($this); die('www');
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			if($this->viajeEmpleadoArray) {
				//print_r($viajeEmpleadoArray); die('tres');
				foreach ($this->viajeEmpleadoArray as $viajeEmpleado){
					//print_r($aux); die();
					$pce = new ViajePlanificacionTrasladoBaseViajeEmpleadoVO();
					$pce->idViajePlanificacionTrasladoBase['valor'] = $this->idViajePlanificacionTrasladoBase['valor'];
					$pce->idViajeEmpleado['valor'] = $viajeEmpleado;
					$pce->insertData();
					if($pce->result->getStatus()  != STATUS_OK) {
						//print_r($pce->result); die('error uno');
						$this->result = $pce->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

	public function updateData(){
		//print_r($this); die('uno');
		try{
			$this->conn->beginTransaction();
			parent::updateData();
			if($this->result->getStatus() != STATUS_OK) {
				//print_r($this); die('www');
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$pce = new ViajePlanificacionTrasladoBaseViajeEmpleadoVO();
			if($this->viajeEmpleadoArray) {
				$pce->insertDataArray($this->idViajePlanificacionTrasladoBase['valor'], $this->viajeEmpleadoArray);
				if($pce->result->getStatus()  != STATUS_OK) {
					//print_r($pce->result); die('error uno');
					$this->result = $pce->result;
					$this->conn->rollBack();
					return $this;
				}
			}
			$pce->deleteDataArray($this->idViajePlanificacionTrasladoBase['valor'], $this->viajeEmpleadoArray);
			if($pce->result->getStatus()  != STATUS_OK) {
				//print_r($pce->result); die('error uno');
				$this->result = $pce->result;
				$this->conn->rollBack();
				return $this;
			}
			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}
	/*public function getComboList($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idPlanificacionTrasladoBase as data, b.idViajePlanificacionTrasladoBase as selected
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
if($_GET['debug'] == 'ViajePlanificacionTrasladoBaseVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajePlanificacionTrasladoBaseVO();
	//print_r($kk->getAllRows());
	$kk->idViajePlanificacionTrasladoBase = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
