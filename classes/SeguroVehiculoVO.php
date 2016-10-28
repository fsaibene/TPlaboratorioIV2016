<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class SeguroVehiculoVO extends Master2 {
	public $idSeguroVehiculo = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idVehiculo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "vehiculo",
						"referencia" => "",
	];

    public $idSucursalEstablecimiento= ["valor" => "",
                        "obligatorio" => TRUE,
                        "tipo" => "combo",
                        "nombre" => "sucursal del establecimiento",
                        "referencia" => "",
                        ];

    public $montoAsegurado = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "float",
                            "nombre" => "Monto asegurado",
                        ];
    public $nroPoliza = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "string",
                            "nombre" => "Nro. de póliza",
                        ];
	public $fechaVigencia = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de vigencia",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE
						],
	];
	public $fechaVencimiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de vencimiento",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE
						],
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "archivo",
		"ruta" => "vehiculos/segurosVehiculo/", // de files/ en adelante
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
		$this->setTableName('segurosVehiculo');
		$this->setFieldIdName('idSeguroVehiculo');
		$this->idVehiculo['referencia'] = new VehiculoVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        if (strtotime(convertDateEsToDb($this->fechaVigencia['valor'])) > strtotime(convertDateEsToDb($this->fechaVencimiento['valor'])) ) {
            $resultMessage = 'La fecha de vencimiento no puede ser menor que la fecha de vigencia.';
        }
        return $resultMessage;
 	}

    public function getNombreCompleto(){
        return $this->idVehiculo['referencia']->marca['valor'] . '/'.$this->idVehiculo['referencia']->modelo['valor'] . '/'.$this->idVehiculo['referencia']->patente['valor'] . '/'.$this->nroPoliza['valor'];
    }

    public function getComboList(){
        $sql = 'SELECT idSeguroVehiculo AS data, concat(marca,"/",modelo,"/",patente,"/",nroPoliza)  AS label
				FROM segurosVehiculo as tabla
				inner join vehiculos ON tabla.idVehiculo = vehiculos.idVehiculo';
	    //echo($sql);
	    try{
		    $ro = $this->conn->prepare($sql);
		    if($ro->execute()){
			    $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			    $this->result->setData($rs);
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

	public function getSegurosVehiculoProximosAVencer() {
		$sql = 'select CONCAT(v.marca, "/", v.modelo, "/", v.patente, "/", sv.nroPoliza) as seguro, DATEDIFF(fechavencimiento, NOW()) as diasFaltantes
				from vehiculos as v
				inner join (
					select max(fechaVencimiento) as fechaVencimiento, idVehiculo
					from segurosVehiculo
					group by idVehiculo
				) as cv using (idVehiculo)
				inner join segurosVehiculo as sv USING (idVehiculo, fechaVencimiento)
				WHERE DATEDIFF(fechavencimiento, NOW()) <= 60 and v.habilitado
				';
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$this->result->setData($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $this;
	}
}

// debug zone
if($_GET['debug'] == 'SeguroVehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new SeguroVehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idSeguroVehiculo = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
