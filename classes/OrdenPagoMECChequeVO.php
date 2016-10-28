<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenPagoMECChequeVO extends Master2 {
    public $idOrdenPagoMECCheque = ["valor" => "",
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
	public $idChequera = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "combo",
                       "nombre" => "chequera",
                       "referencia" => "",
                       ];
	public $nroCheque = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. de cheque",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE
		],
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
	public $fechaEmision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de emisión",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $idTipoCheque = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de cheque",
		"referencia" => "",
	];
	public $idTipoFormaEmisionCheque = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "forma de emisión",
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
        $this->setTableName('ordenesPagoMEC_cheques');
		$this->setFieldIdName('idOrdenPagoMECCheque');
        $this->idOrdenPagoMEC['referencia'] =  new OrdenPagoMECVO();
        $this->idChequera['referencia'] =  new ChequeraVO();
        $this->idTipoCheque['referencia'] =  new TipoChequeVO();
        $this->idTipoFormaEmisionCheque['referencia'] =  new TipoFormaEmisionChequeVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
	    //$resultMessage = 'asd: '. $this->nroCheque['valor'];
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenPagoMECChequeVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenPagoMEC_ChequeVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPagoMECCheque = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>