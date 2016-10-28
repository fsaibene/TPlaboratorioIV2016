<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoDefinicionVO extends Master2 {
    public $idContratoDefinicion = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idContrato = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "Contrato",
        "referencia" => "",
    ];
    public $fechaInicio = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de inicio",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $fechaFin = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de fin",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $idTipoMoneda = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "tipo de moneda",
        "referencia" => "",
    ];
    public $monto = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "float",
        "nombre" => "monto del proyecto",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $archivo = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "file",
        "nombre" => "archivo",
        "ruta" => "proyectos/definicion/", // de files/ en adelante
        "tamaño" => 10485760, // 10 * 1048576 = 10 mb
    ];
    public $condicionesComerciales = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "condiciones comerciales",
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('contratosDefinicion');
	    $this->setFieldIdName('idContratoDefinicion');
	    $this->idContrato['referencia'] = new ContratoVO();
	    $this->idTipoMoneda['referencia'] = new TipoMonedaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        if($this->fechaInicio['valor'] && $this->fechaFin['valor']){
            if (strtotime(convertDateEsToDb($this->fechaInicio['valor'])) > strtotime(convertDateEsToDb($this->fechaFin['valor'])) ) {
                $resultMessage = 'La fecha Fin no puede ser menor que la fecha Inicio.';
            }
        }
        return $resultMessage;
    }

    public function getContratoDefinicionPorIdContrato(){
        $data['nombreCampoWhere'] = 'idContrato';
        $data['valorCampoWhere'] = $this->idContrato['valor'];
        $this->getRowById($data);
        return;
    }

}

// debug zone
if($_GET['debug'] == 'ContratoDefinicionVO' or false){
    echo "DEBUG<br>";
    $kk = new ContratoDefinicionVO();
    //print_r($kk->getAllRows());
    $kk->$idContratoDefinicion = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
