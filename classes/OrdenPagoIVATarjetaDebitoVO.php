<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenPagoIVATarjetaDebitoVO extends Master2 {
    public $idOrdenPagoTarjetaDebito = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idOrdenPago = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "orden de pago",
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
		"nombre" => "fecha de pago",
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
	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
        $this->setTableName('ordenesPagoIVA_tarjetasDebito');
		$this->setFieldIdName('idOrdenPagoTarjetaDebito');
        $this->idOrdenPago['referencia'] =  new OrdenPagoIVAVO();
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