<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class MovimientoVehiculoVO extends Master2 {
	public $idMovimientoVehiculo = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					    ];
	public $idVehiculo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "Vehículo",
						"referencia" => "",
	];
	public $idLocacionOrigen = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "locación de origen",
		"referencia" => "",
	];
	public $idLocacionDestino = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "locación de destino",
		"referencia" => "",
	];
	public $idEmpleadoEntrega = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado que entrega el vehículo",
		"referencia" => "",
	];
	public $idEmpleadoRecibe = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado que recibe el vehículo",
		"referencia" => "",
	];
	public $idEstadoVehiculo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "estado del vehículo",
		"referencia" => "",
	];
	public $idTipoMovimientoVehiculo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de movimiento",
		"referencia" => "",
	];
	public $km = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "km",
				    	];
	public $fecha = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de salida / llegada",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE,
						],
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "vehiculos/movimientos/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
    ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('movimientosVehiculo');
		$this->setFieldIdName('idMovimientoVehiculo');
		$this->idVehiculo['referencia'] = new VehiculoVO();
		$this->idLocacionOrigen['referencia'] = new LocacionVO();
		$this->idLocacionDestino['referencia'] = new LocacionVO();
		$this->idEmpleadoEntrega['referencia'] = new EmpleadoVO();
		$this->idEmpleadoRecibe['referencia'] = new EmpleadoVO();
		$this->idEstadoVehiculo['referencia'] = new EstadoVehiculoVO();
		$this->idTipoMovimientoVehiculo['referencia'] = new TipoMovimientoVehiculoVO();
		$this->fecha['valor'] = date('d/m/Y');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getNombreCompleto(){
		$var =  $this->idVehiculo['referencia']->marca['valor'] . '/' .$this->idVehiculo['referencia']->modelo['valor'] . '/' .$this->idVehiculo['referencia']->patente['valor'] . '/';
		$var .= $this->idTipoMovimientoVehiculo['referencia']->tipoMovimientoVehiculo['valor'] . '/' .$this->fecha['valor'];
		return $var;
	}

	public function getComboList($data = NULL){
		$sql = 'select CONCAT(v.marca, "/", v.modelo, "/", v.patente, "/", l.locacion, "/", m.fecha) as label, m.idMovimientoVehiculo as data
				from movimientosVehiculo as m
				inner join (
					select max(fecha) as fecha, idVehiculo
					from movimientosVehiculo
					group by idVehiculo
				) as m2 USING (fecha, idVehiculo)
				inner join vehiculos as v using (idVehiculo)
				inner join locaciones as l using (idLocacion)
				-- where idTipoMovimientoVehiculo in (1, 2) -- tienen que ser movimientos de llegada
				group by idLocacion
            	';
		//echo($sql);
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

	/*
	 * devuelve los movimientos que fueron identificados como Llegada
	 */
	public function getComboList2($data = NULL){
		$sql = 'select CONCAT(v.marca, "/", v.modelo, "/", v.patente, "/", l.tipoMovimientoVehiculo, "/", m.fecha) as label, m.idMovimientoVehiculo as data
				from movimientosVehiculo as m
				inner join (
					select max(fecha) as fecha, idVehiculo
					from movimientosVehiculo
					group by idVehiculo
				) as m2 USING (fecha, idVehiculo)
				inner join vehiculos as v using (idVehiculo)
				inner join tiposMovimientoVehiculo as l on l.idTipoMovimientoVehiculo = m.idTipoMovimientoVehiculo
				where m.idTipoMovimientoVehiculo = 2 -- tienen que ser movimientos de Llegada
				group by data
            	';
		//echo($sql); die();
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

	public function getReporteMovimientoVehiculos($data){
		//print_r($data); //die();
		$sql = 'select getVehiculo(idVehiculo) as vehiculo, DATE_FORMAT(mv.fecha,"%d/%m/%Y") as fecha, tipoMovimientoVehiculo
					, mv.km, lo.locacion as locacionOrigen, ld.locacion as locacionDestino
					, getEmpleado(mv.idEmpleadoEntrega) as empleadoEntrega
					, getEmpleado(mv.idEmpleadoRecibe) as empleadoRecibe, estadoVehiculo, mv.observaciones
				from movimientosVehiculo as mv
				inner join vehiculos as v using (idVehiculo)
				inner join locaciones as lo on lo.idLocacion = mv.idLocacionOrigen
				inner join locaciones as ld on ld.idLocacion = mv.idLocacionDestino
				inner join estadosVehiculo as ev using (idEstadoVehiculo)
				inner join tiposMovimientoVehiculo as tmv using (idTipoMovimientoVehiculo)
				where true ';
		if($data['idVehiculo']){
			$sql .= ' and mv.idVehiculo = '.$data['idVehiculo'];
		}
		if($data['fechaDesde']){
			$sql .= ' and fecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= ' order by vehiculo, fecha desc';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$dataResult['data'] = $rs;
			//print_r($rs);die();
			$this->result->setData($dataResult);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}
}

// debug zone
if($_GET['debug'] == 'MovimientoVehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new MovimientoVehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idMovimientoVehiculo = 116;
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
