<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class AfipTipoOpcionalVO extends Master2 {
    public $idAfipTipoOpcional = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $afipTipoOpcional = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "tipo de opcional AFIP",
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
		$this->setTableName('tiposOpcionalAFIP');
		$this->setFieldIdName('idAfipTipoOpcional');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idAfipTipoOpcional';
        $data['label'] = 'afipTipoOpcional';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'AfipTipoOpcionalVO' or false){
	echo "DEBUG<br>";
	$kk = new AfipTipoOpcionalVO();
	//print_r($kk->getAllRows());
	$kk->idAfipTipoOpcional = 116;
	$kk->afipTipoOpcional = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>