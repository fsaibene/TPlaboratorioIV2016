<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class FondoFijoTarjetaDebitoVO extends Master2 {
    public $idFondoFijoTarjetaDebito = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idZonaAfectacion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "fondo fijo",
		"referencia" => "",
	];
	public $idEmpleadoTarjetaDebito = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "tarjeta de débito",
                       "referencia" => "",
                       ];
	public $fechaPago = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de extracción",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $monto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
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
        $this->setTableName('fondoFijo_tarjetasDebito');
		$this->setFieldIdName('idFondoFijoTarjetaDebito');
		$this->idZonaAfectacion['referencia'] =  new ZonaAfectacionVO();
        $this->idEmpleadoTarjetaDebito['referencia'] =  new EmpleadoTarjetaDebitoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenPagoTarjetaDebitoVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenPago_EmpleadoTarjetaDebitoVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPagoTarjetaDebito = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>