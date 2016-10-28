<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ComprobanteCompraIVATipoGravamenCompraVO extends Master2 {
    public $idComprobanteCompraIVATipoGravamenCompra = ["valor" => "",
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
	public $idTipoGravamenCompra = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "Tipo Gravamen Compra",
                       "referencia" => "",
                       ];
	public $montoNeto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto neto",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $montoIva = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto iva",
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
        $this->setTableName('comprobantesCompraIVA_tiposGravamenCompra');
		$this->setFieldIdName('idComprobanteCompraIVATipoGravamenCompra');
        $this->idComprobanteCompraIVA['referencia'] =  new ComprobanteCompraIVAVO();
        $this->idTipoGravamenCompra['referencia'] =  new TipoGravamenCompraVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'ComprobanteCompraIVATipoGravamenCompraVO' or false){
	echo "DEBUG<br>";
	$kk = new ComprobanteCompraIVA_TipoGravamenCompraVO();
	//print_r($kk->getAllRows());
	$kk->idComprobanteCompraIVATipoGravamenCompra = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>