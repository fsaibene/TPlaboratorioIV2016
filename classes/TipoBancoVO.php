<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * Created by PhpStorm.
 * User: German
 * Date: 28/08/2016
 * Time: 16:11
 */
class TipoBancoVO extends Master2 {
    public $idTipoBanco = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoBanco = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "banco",
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
		$this->setTableName('tiposBanco');
		$this->setFieldIdName('idTipoBanco');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoBanco';
        $data['label'] = 'tipoBanco';
        $data['orden'] = 'tipoBanco';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'TipoBancoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoBancoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoBanco = 116;
	$kk->tiposBanco = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>