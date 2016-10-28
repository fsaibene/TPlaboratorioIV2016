<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenPagoMECComprobanteCompraMECVO extends Master2 {
    public $idOrdenPagoMECComprobanteCompraMEC = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idOrdenPagoMEC = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "orden de pago",
                       "referencia" => "",
                       ];
	public $idComprobanteCompraMEC = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "comprobante compra iva",
                       "referencia" => "",
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
        $this->setTableName('ordenesPagoMEC_comprobantesCompraMEC');
		$this->setFieldIdName('idOrdenPagoMECComprobanteCompraMEC');
        $this->idOrdenPagoMEC['referencia'] =  new OrdenPagoMECVO();
        $this->idComprobanteCompraMEC['referencia'] =  new ComprobanteCompraMECVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenPagoMECComprobanteCompraMECVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenPagoMEC_ComprobanteCompraMECVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPagoMECComprobanteCompraMEC = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>