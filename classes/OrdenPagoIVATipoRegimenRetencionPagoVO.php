<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenPagoIVATipoRegimenRetencionPagoVO extends Master2 {
    public $idOrdenPagoTipoRegimenRetencionPago = ["valor" => "",
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
                       "nombre" => "orden pago",
                       "referencia" => "",
                       ];
	public $idTipoRegimenRetencionPago = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "Tipo Régimen Retención Pago",
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
        $this->setTableName('ordenesPagoIVA_tiposRegimenRetencionPago');
		$this->setFieldIdName('idOrdenPagoTipoRegimenRetencionPago');
        $this->idOrdenPago['referencia'] =  new OrdenPagoIVAVO();
        $this->idTipoRegimenRetencionPago['referencia'] =  new TipoRegimenRetencionPagoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenPagoTipoRegimenRetencionPagoVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenPago_TipoRegimenRetencionPagoVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPagoTipoRegimenRetencionPago = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>