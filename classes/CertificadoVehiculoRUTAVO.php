<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class CertificadoVehiculoRUTAVO extends Master2 {
	public $idCertificadoVehiculoRUTA = ["valor" => "",
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
		"ruta" => "vehiculos/certificadosVehiculoRUTA/", // de files/ en adelante
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
		$this->setTableName('CertificadosVehiculosRUTA');
		$this->setFieldIdName('idCertificadoVehiculoRUTA');
		$this->idVehiculo['referencia'] = new VehiculoVO();
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

	public function getCertificadosVehiculoRUTAProximosAVencer() {
		$sql = 'select CONCAT(v.marca, "/", v.modelo, "/", v.patente) as vehiculo, DATEDIFF(fechavencimiento, NOW()) as diasFaltantes
				from vehiculos as v
				inner join (
					select max(fechaVencimiento) as fechaVencimiento, idVehiculo
					from certificadosVehiculosRUTA
					group by idVehiculo
				) as cv using (idVehiculo)
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
if($_GET['debug'] == 'CertificadoVehiculoRUTAVO' or false){
	echo "DEBUG<br>";
	$kk = new CertificadoVehiculoRUTAVO();
	//print_r($kk->getAllRows());
	$kk->idCertificadoVehiculoRUTA = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
