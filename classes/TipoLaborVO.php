<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoLaborVO extends Master2 {
    public $idTipoLabor = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "id",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE,
            ],
    ];
    public $tipoLabor = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "labor",
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
		$this->setTableName('tiposLabor');
		$this->setFieldIdName('idTipoLabor');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoLabor';
        $data['label'] = 'tipoLabor';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'TipoLaborVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoLaborVO();
	//print_r($kk->getAllRows());
	$kk->idTipoLabor = 116;
	$kk->tipoLabor = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>