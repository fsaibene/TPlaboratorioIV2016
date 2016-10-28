<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class RendicionViaticoMovimientoTarjetaDebitoVO extends Master2 {
	public $idRendicionViaticoMovimientoTarjetaDebito = ["valor" => "",
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
	public $idTipoOperacionTarjetaDebito = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "concepto",
		"referencia" => "",
	];
	public $nroComprobante = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "sigla",
		"longitud" => "16"
	];
	public $fecha = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha",
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

	public function __construct(){
		parent::__construct();
		$this->result = new Result();
		$this->setTableName('rendicionesViatico_movimientosTarjetaDebito');
		$this->setFieldIdName('idRendicionViaticoMovimientoTarjetaDebito');
		$this->idRendicionViatico['referencia'] =  new RendicionViaticoVO();
		$this->idTipoOperacionTarjetaDebito['referencia'] =  new TipoOperacionTarjetaDebitoVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}
}

// debug zone
if($_GET['debug'] == 'RendicionViaticoMovimientoTarjetaDebitoVO' or false){
	//echo "DEBUG<br>";
	include_once("../../tools/dompdf/dompdf_config.inc.php");
	$kk = new RendicionViaticoMovimientoTarjetaDebitoVO();
	//print_r($kk->getAllRows());
	$kk->idRendicionViaticoMovimientoTarjetaDebito['valor'] = 1;
	$html = $kk->getRendicionViaticoMovimientoTarjetaDebitoPDF();
	//echo $html; die();
	$dompdf = new DOMPDF();
	$dompdf->set_paper("A4", "landscape");
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("OC-". $objectVO->nroRendicionViaticoMovimientoTarjetaDebito['valor'].".pdf", array('Attachment'=>0));

	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}