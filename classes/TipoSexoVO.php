<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoSexoVO extends Master2 {
    public $idTipoSexo = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $tipoSexo = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "sexo",
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
		$this->setTableName('tiposSexo');
		$this->setFieldIdName('idTipoSexo');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList(){
		$result = new Result();
		$data['data'] = 'idTipoSexo';
		$data['label'] = 'tipoSexo';
		$data['orden'] = 'orden';
		$data['nombreCampoWhere'] = 'habilitado';
		$data['valorCampoWhere'] = true;
		$result = parent::getComboList($data);
		return $result;
	}
}

if($_GET['debug'] == 'TipoSexoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoSexoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoSexo = 116;
	$kk->titulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>