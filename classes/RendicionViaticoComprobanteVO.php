<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class RendicionViaticoComprobanteVO extends Master2 {
	public $idRendicionViaticoComprobante = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "ID",
	];
	public $idRendicionViatico = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "rendición",
		"referencia" => "",
	];
	public $idTipoRubroGasto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "concepto",
		"referencia" => "",
	];
	public $idTipoComprobanteRendicionViatico = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de comprobante",
		"referencia" => "",
	];
	public $idTipoFormaDePago = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "forma de pago",
		"referencia" => "",
	];
	public $cantidad = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "cantidad",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $monto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];

	public function __construct(){
		parent::__construct();
		$this->result = new Result();
		$this->setTableName('rendicionesViatico_comprobantes');
		$this->setFieldIdName('idRendicionViaticoComprobante');
		$this->idRendicionViatico['referencia'] =  new RendicionViaticoVO();
		$this->idTipoRubroGasto['referencia'] =  new TipoRubroGastoVO();
		$this->idTipoComprobanteRendicionViatico['referencia'] =  new TipoComprobanteRendicionViaticoVO();
		$this->idTipoFormaDePago['referencia'] =  new TipoFormaDePagoVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}
}

// debug zone
if($_GET['debug'] == 'RendicionViaticoComprobanteVO' or false){
	//echo "DEBUG<br>";
	include_once("../../tools/dompdf/dompdf_config.inc.php");
	$kk = new RendicionViaticoComprobanteVO();
	//print_r($kk->getAllRows());
	$kk->idRendicionViaticoComprobante['valor'] = 1;
	$html = $kk->getRendicionViaticoComprobantePDF();
	//echo $html; die();
	$dompdf = new DOMPDF();
	$dompdf->set_paper("A4", "landscape");
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("OC-". $objectVO->nroRendicionViaticoComprobante['valor'].".pdf", array('Attachment'=>0));

	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}