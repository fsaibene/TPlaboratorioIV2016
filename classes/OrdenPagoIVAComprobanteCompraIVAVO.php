<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenPagoIVAComprobanteCompraIVAVO extends Master2 {
    public $idOrdenPagoComprobanteCompraIVA = ["valor" => "",
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
	public $idComprobanteCompraIVA = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "comprobante compra iva",
                       "referencia" => "",
                       ];
	public $monto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto",
		"validador" => ["admiteMenorAcero" => TRUE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
        $this->setTableName('ordenesPagoIVA_comprobantesCompraIVA');
		$this->setFieldIdName('idOrdenPagoComprobanteCompraIVA');
        $this->idOrdenPago['referencia'] =  new OrdenPagoIVAVO();
        $this->idComprobanteCompraIVA['referencia'] =  new ComprobanteCompraIVAVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenPagoComprobanteCompraIVAVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenPago_ComprobanteCompraIVAVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPagoComprobanteCompraIVA = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>