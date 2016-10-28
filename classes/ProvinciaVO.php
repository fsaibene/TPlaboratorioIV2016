<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ProvinciaVO extends Master2 {
    public $idProvincia = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $provincia = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "provincia",
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
                       
	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('provincias');
		$this->setFieldIdName('idProvincia');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
	public function getComboList(){
		$result = new Result();
      	$data['data'] = 'idProvincia';
      	$data['label'] = 'provincia';
      	$data['orden'] = 'orden desc, provincia asc';
   		$result = parent::getComboList($data); 
   		return $result;
	}

}

if($_GET['debug'] == 'ProvinciaVO' or false){
	echo "DEBUG<br>";
	$kk = new ProvinciaVO();
	//print_r($kk->getAllRows());
	$kk->idProvincia = 116;
	$kk->titulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>