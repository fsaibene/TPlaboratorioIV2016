<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * Created by PhpStorm.
 * User: German
 * Date: 09/04/2016
 * Time: 17:42
 */
class FacturacionItemVO extends Master2 {
    public $idFacturacionItem = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "id",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $idFacturacion = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "factura",
        "referencia" => "",
    ];
    public $item = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "descripción",
        "longitud" => "255"
    ];
    public $cantidad= ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "cantidad",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $precioUnitario = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "P/U",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $porcentajeBonificacion = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "bonificación",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $importeBonificacion = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe bonificación",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $idTipoIva = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "IVA",
        "referencia" => "",
    ];
    public $importeIva = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe IVA",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $idUsuarioLog;
    public $fechaLog;

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('facturacion_items');
	    $this->setFieldIdName('idFacturacionItem');
	    $this->idFacturacion['referencia'] = new FacturacionVO();
	    //$this->idTipoBonificacion['referencia'] =  new TipoBonificacionVO();
	    $this->idTipoIva['referencia'] = new AfipTipoIvaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'FacturacionItemVO' or false){
    echo "DEBUG<br>";
    $kk = new FacturacionItemVO();
    //print_r($kk->getAllRows());
    $kk->idFacturacionItem = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    echo $kk->getResultMessage();
}
?>