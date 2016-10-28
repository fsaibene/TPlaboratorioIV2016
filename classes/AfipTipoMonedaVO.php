<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class AfipTipoMonedaVO extends Master2 {
    public $idAfipTipoMoneda = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "id",
    ];
    public $afipTipoMoneda = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "tipo de moneda AFIP",
    ];
    public $codAfipTipoMoneda = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "código tipo de moneda AFIP",
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
		$this->setTableName('afipTiposMoneda');
		$this->setFieldIdName('idAfipTipoMoneda');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idAfipTipoMoneda';
        $data['label'] = 'afipTipoMoneda';
        $data['orden'] = 'afipTipoMoneda';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'AfipTipoMonedaVO' or false){
	echo "DEBUG<br>";
	$kk = new AfipTipoMonedaVO();
	//print_r($kk->getAllRows());
	$kk->idAfipTipoMoneda = 116;
	$kk->afipTipoMoneda = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>