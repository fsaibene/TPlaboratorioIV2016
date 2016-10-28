<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ControlEquipamientoVehiculoVO extends Master2 {
    public $idControlEquipamientoVehiculo = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idMovimientoVehiculo = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => "movimiento de llegada",
							"referencia" => "",
	];
	public $idEquipamientoVehiculo = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => "equipamiento",
							"referencia" => "",
	];
	public $observacion = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "string",
							"nombre" => "observación",
	];
	public $solucion = ["valor" => "",
							"obligatorio" => FALSE,
							"tipo" => "string",
							"nombre" => "solución",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('controlesEquipamientoVehiculo');
		$this->setFieldIdName('idControlEquipamientoVehiculo');
		$this->idMovimientoVehiculo['referencia'] = new MovimientoVehiculoVO();
		$this->idEquipamientoVehiculo['referencia'] = new EquipamientoVehiculoVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getCantidadEquipamientoVehiculoConObservaciones(){
		$sql = 'SELECT cev.idControlEquipamientoVehiculo
				from controlesEquipamientoVehiculo as cev
				inner join movimientosVehiculo as mv using (idMovimientoVehiculo)
				inner join vehiculos as v using (idVehiculo)
				where solucion is null and v.habilitado
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

if($_GET['debug'] == 'ControlEquipamientoVehiculoVO' or false){
	echo "DEBUG<br>";
	$kk = new ControlEquipamientoVehiculoVO();
	//print_r($kk->getAllRows());
	$kk->idControlEquipamientoVehiculo = 116;
	$kk->controlEquipamientoVehiculo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>