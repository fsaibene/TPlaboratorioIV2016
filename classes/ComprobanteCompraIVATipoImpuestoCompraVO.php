<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ComprobanteCompraIVATipoImpuestoCompraVO extends Master2 {
    public $idComprobanteCompraIVATipoImpuestoCompra = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idComprobanteCompraIVA = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "Comprobante Compra IVA",
                       "referencia" => "",
                       ];
	public $idTipoImpuestoCompra = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "Tipo Impuesto Compra",
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
        $this->setTableName('comprobantesCompraIVA_tiposImpuestoCompra');
		$this->setFieldIdName('idComprobanteCompraIVATipoImpuestoCompra');
        $this->idComprobanteCompraIVA['referencia'] =  new ComprobanteCompraIVAVO();
        $this->idTipoImpuestoCompra['referencia'] =  new TipoImpuestoCompraVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'ComprobanteCompraIVATipoImpuestoCompraVO' or false){
	echo "DEBUG<br>";
	$kk = new ComprobanteCompraIVA_TipoImpuestoCompraVO();
	//print_r($kk->getAllRows());
	$kk->idComprobanteCompraIVATipoImpuestoCompra = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>