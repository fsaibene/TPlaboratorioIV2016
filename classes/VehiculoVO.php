<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class VehiculoVO extends Master2 {
    public $idVehiculo = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					    ];
    public $marca = ["valor" => "",
                        "obligatorio" => TRUE,
                        "tipo" => "string",
                        "nombre" => "marca",
	                    "longitud" => "32"
                        ];
    public $modelo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "modelo",
	                    "longitud" => "32"
				    	];
	public $patente = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "patente",
				    	];
    public $idCombustible = ["valor" => "",
                        "obligatorio" => TRUE,
                        "tipo" => "combo",
                        "nombre" => "tipo de combustible",
                        "referencia" => "",
    ];
    public $idTipoOrigenBien = ["valor" => "",
                        "obligatorio" => TRUE,
                        "tipo" => "combo",
                        "nombre" => "tipo de origen del vehículo",
                        "referencia" => "",
    ];
    public $idSucursalEstablecimiento = ["valor" => "",
                    "obligatorio" => FALSE,
                    "tipo" => "combo",
                    "nombre" => "sucursal del establecimiento",
                    "referencia" => "",
    ];
    public $origenAlquilerDesde = ["valor" => "",
                    "obligatorio" => FALSE,
                    "tipo" => "date",
                    "nombre" => "fecha alquiler desde",
                    "validador" => ["admiteMenorAhoy" => TRUE,
                        "admiteHoy" => TRUE,
                        "admiteMayorAhoy" => TRUE,
                    ],
    ];
    public $origenAlquilerHasta = ["valor" => "",
                    "obligatorio" => FALSE,
                    "tipo" => "date",
                    "nombre" => "fecha alquiler hasta",
                    "validador" => ["admiteMenorAhoy" => FALSE,
                        "admiteHoy" => TRUE,
                        "admiteMayorAhoy" => TRUE,
                    ],
    ];
	public $habilitado = ["valor" => TRUE,
						"obligatorio" => FALSE,
						"tipo" => "bool",
						"nombre" => "habilitado",
	];
    public $archivo = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "archivo",
	    "ruta" => "vehiculos/", // de files/ en adelante
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
		$this->setTableName('vehiculos');
		$this->setFieldIdName('idVehiculo');
		$this->idTipoOrigenBien['referencia'] = new TipoOrigenBienVO();
		$this->idCombustible['referencia'] = new CombustibleVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){

        if($this->idTipoOrigenBien['valor'] == '2'){
            $this->idSucursalEstablecimiento['obligatorio'] = TRUE;
            $this->origenAlquilerDesde['obligatorio'] = TRUE;
            $this->origenAlquilerHasta['obligatorio'] = TRUE;
            if (strtotime(convertDateEsToDb($this->origenAlquilerDesde['valor'])) > strtotime(convertDateEsToDb($this->origenAlquilerHasta['valor'])) ) {
                $resultMessage = 'La fecha de alquiler HASTA no puede ser menor que la fecha de alquiler DESDE.';
            }
        } else {
            $this->idSucursalEstablecimiento['obligatorio'] = FALSE;
            $this->idSucursalEstablecimiento['valor'] = NULL;
            $this->origenAlquilerDesde['obligatorio'] = FALSE;
            $this->origenAlquilerDesde['valor'] = NULL;
            $this->origenAlquilerHasta['obligatorio'] = FALSE;
            $this->origenAlquilerHasta['valor'] = NULL;
        }
        return $resultMessage;
 	}

	public function getNombreCompleto(){
		return $this->marca['valor'] . '/' .$this->modelo['valor']. '/' .$this->patente['valor'];
	}

	public function getCantidadVehiculosHabilitados() {
		$data = null;
		$data['nombreCampoWhere'] = 'habilitado';
		$data['valorCampoWhere'] = '1';
		$this->getAllRows($data);
		return count($this->result->getData());
	}

	public function getCantidadVehiculos() {
		$this->getAllRows();
		return count($this->result->getData());
	}

	public function getComboList(){
		$data['data'] = 'idVehiculo';
		$data['label'] = 'getVehiculo(idVehiculo)';
		$data['orden'] = 'marca, modelo, patente';
		//$data['nombreCampoWhere'] = 'habilitado';
		//$data['valorCampoWhere'] = '1';

		parent::getComboList($data);
		return $this;
	}

	public function getCantidadVehiculosEnComision() {
		$sql = 'select m.*
				from movimientosVehiculo  as m
				inner join (
					select max(fecha) as fecha, idVehiculo
					from movimientosVehiculo 
					group by idVehiculo
				) as m2 USING (fecha, idVehiculo)
				where idTipoMovimientoVehiculo != 18 and idTipoMovimientoVehiculo != 19 -- no tiene llegada a ninguna base
				group by idTipoMovimientoVehiculo 
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}

	public function getCantidadVehiculosEnBaseBA() {
		$sql = 'select m.*
				from movimientosVehiculo  as m
				inner join (
					select max(fecha) as fecha, idVehiculo
					from movimientosVehiculo 
					group by idVehiculo
				) as m2 USING (fecha, idVehiculo)
				where idTipoMovimientoVehiculo = 19  -- llegada base BA
				group by idTipoMovimientoVehiculo 
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}

	public function getCantidadVehiculosEnBaseSJ() {
		$sql = 'select m.*
				from movimientosVehiculo  as m
				inner join (
					select max(fecha) as fecha, idVehiculo
					from movimientosVehiculo 
					group by idVehiculo
				) as m2 USING (fecha, idVehiculo)
				where idTipoMovimientoVehiculo = 18 -- llegada base SJ
				group by idTipoMovimientoVehiculo 
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}
}

// debug zone
if($_GET['debug'] == 'VehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new VehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idVehiculo = 116;
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
