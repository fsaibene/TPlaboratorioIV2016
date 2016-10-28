<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class CalibracionEquipamientoVO extends Master2 {
	public $idCalibracionEquipamiento = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idEquipamiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "equipamiento",
						"referencia" => "",
	];
	public $idSucursalEstablecimiento= ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "sucursal del establecimiento",
						"referencia" => "",
	];

	public $fechaCalibracion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de calibracion",
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
						"ruta" => "equipamientos/calibraciones/", // de files/ en adelante
						"tamaño" => 10485760, // 10 * 1048576 = 10 mb
					];
	public $idTipoTransporteEquipamiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de transporte",
		"referencia" => "",
	];
	public $idSucursalEstablecimientoTransporteExterno1 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "empresa transportista tramo 1",
		"referencia" => "",
	];
	public $archivoTransporteExterno1 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "equipamientos/calibraciones/archivoTransporteExterno1/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $idSucursalEstablecimientoTransporteExterno2 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "empresa transportista tramo 2",
		"referencia" => "",
	];
	public $archivoTransporteExterno2 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "equipamientos/calibraciones/archivoTransporteExterno2/", // de files/ en adelante
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
		$this->setTableName('calibracionesEquipamiento');
		$this->setFieldIdName('idCalibracionEquipamiento');
		$this->idEquipamiento['referencia'] = new EquipamientoVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
		$this->idTipoTransporteEquipamiento['referencia'] = new TipoTransporteEquipamientoVO();
		$this->idSucursalEstablecimientoTransporteExterno1['referencia'] = new SucursalEstablecimientoVO();
		$this->idSucursalEstablecimientoTransporteExterno2['referencia'] = new SucursalEstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getCalibracionesProximasAVencer() {
		$sql = 'select CONCAT(e.marca, "/", e.modelo) as equipamiento, DATEDIFF(fechavencimiento, NOW()) as diasFaltantes
				from equipamientos as e
				inner join (
					select max(fechaVencimiento) as fechaVencimiento, idEquipamiento
					from calibracionesEquipamiento
					group by idEquipamiento
				) as cv using (idEquipamiento)
				WHERE DATEDIFF(fechavencimiento, NOW()) <= 60 and e.habilitado
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
if($_GET['debug'] == 'CalibracionEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new CalibracionVO();
	//print_r($kk->getAllRows());
	$kk->idCalibracion = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
