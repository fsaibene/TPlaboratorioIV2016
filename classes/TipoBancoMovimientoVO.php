<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * Created by PhpStorm.
 * User: German
 * Date: 28/08/2016
 * Time: 16:11
 */
class TipoBancoMovimientoVO extends Master2 {
    public $idTipoBancoMovimiento = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoBancoMovimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "movimiento",
    ];
    public $signo = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "signo",
    ];
    public $orden = ["valor" => "0",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "orden",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $habilitado = ["valor" => TRUE,
        "obligatorio" => TRUE,
        "tipo" => "bool",
        "nombre" => "habilitado",
    ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('tiposBancoMovimiento');
		$this->setFieldIdName('idTipoBancoMovimiento');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoBancoMovimiento';
        $data['label'] = 'tipoBancoMovimiento';
        $data['orden'] = 'tipoBancoMovimiento';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'TipoBancoMovimientoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoBancoMovimientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoBancoMovimiento = 116;
	$kk->tipoBancoMovimiento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>