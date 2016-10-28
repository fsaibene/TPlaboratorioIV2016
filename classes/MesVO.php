<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class MesVO extends Master2 {
	public $idMes = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $mesEnLetras = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "mes en letras",
                       ];
	public $mesEnNumeros = ["valor" => "0",
						"obligatorio" => TRUE,
						"tipo" => "integer",
						"nombre" => "mes en números",
						"validador" => ["admiteMenorAcero" => FALSE,
							"admiteCero" => FALSE,
							"admiteMayorAcero" => TRUE
						],
	];

    public function __construct(){
        parent::__construct();
        $this->setTableName('meses');
        $this->setFieldIdName('idMes');
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }


    public function getComboList($data = null){
        $data['data'] = 'idMes';
        $data['label'] = 'concat(mesEnNumeros, " - ", mesEnLetras)';
        $data['orden'] = 'mesEnNumeros';
        parent::getComboList($data);
        return $this;
    }

}
if($_GET['debug'] == 'MesVO' or false){
	echo "DEBUG<br>";
	$kk = new MesVO();
	print_r($kk->getAllRows());
	//$kk->idMes['valor'] = 4;
	//$kk->mes = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>