<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class MovimientoVO extends Master2 {
	public $idMovimiento = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					    ];
	public $idEquipo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "equipo",
						"referencia" => "",
	];
	public $idTipoMovimiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de movimiento",
						"referencia" => "",
	];
	public $idEstadoEquipo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "estado del equipo",
						"referencia" => "",
	];
	public $fecha = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE,
						],
	];
	public $nroRemito = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "nro. de remito",
				    	];
	public $destino = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "destino",
				    	];
	public $idResponsable = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "responsable",
						"referencia" => "",
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
		$this->setTableName('movimientos');
		$this->setFieldIdName('idMovimiento');
		$this->idEquipo['referencia'] = new EquipoVO();
		$this->idTipoMovimiento['referencia'] = new TipoMovimientoVO();
		$this->idResponsable['referencia'] = new EmpleadoVO();
		$this->idEstadoEquipo['referencia'] = new EstadoEquipoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if($this->idTipoMovimiento['valor'] == '16' || $this->idTipoMovimiento['valor'] == '17' || $this->idTipoMovimiento['valor'] == '20'){
			$this->nroRemito['obligatorio'] = TRUE;
			$this->destino['obligatorio'] = TRUE;
			$this->idResponsable['obligatorio'] = TRUE;
		} else {
			$this->nroRemito['obligatorio'] = FALSE;
			$this->nroRemito['valor'] = "";
			$this->destino['obligatorio'] = FALSE;
			$this->destino['valor'] = "";
			$this->idResponsable['obligatorio'] = FALSE;
			$this->idResponsable['valor'] = "";
		}
        return $resultMessage;
 	}

}

// debug zone
if($_GET['debug'] == 'MovimientoVO' or false){
	echo "DEBUG<br>";
	$kk = new MovimientoVO();
	//print_r($kk->getAllRows());
	$kk->idMovimiento = 116;
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
