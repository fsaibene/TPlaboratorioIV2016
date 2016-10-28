<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ClaseCapacitacionVO extends Master2 {
    public $idClaseCapacitacion = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "clase" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $claseCapacitacion = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "clase" => "string",
                       "nombre" => "clase de contratación",
                       ];
	public $orden = ["valor" => "0",
						"obligatorio" => TRUE,
						"clase" => "integer",
						"nombre" => "orden",
						"validador" => ["admiteMenorAcero" => FALSE,
							"admiteCero" => TRUE,
							"admiteMayorAcero" => TRUE
						],
	];
	public $habilitado = ["valor" => TRUE,
						"obligatorio" => TRUE,
						"clase" => "bool",
						"nombre" => "habilitado",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('clasesCapacitacion');
		$this->setFieldIdName('idClaseCapacitacion');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idClaseCapacitacion';
      	$data['label'] = 'claseCapacitacion';
	    $data['orden'] = 'orden';
	    $data['nombreCampoWhere'] = 'habilitado';
	    $data['valorCampoWhere'] = '1';

	    parent::getComboList($data);
	    return $this;
	}
}
if($_GET['debug'] == 'ClaseCapacitacionVO' or false){
	echo "DEBUG<br>";
	$kk = new ClaseCapacitacionVO();
	//print_r($kk->getAllRows());
	$kk->idClaseCapacitacion = 116;
	$kk->claseCapacitacion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>