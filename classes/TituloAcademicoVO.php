<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TituloAcademicoVO extends Master2 {
    public $idTituloAcademico = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $tituloAcademico = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "título académico",
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
		$this->setTableName('titulosAcademicos');
		$this->setFieldIdName('idTituloAcademico');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idTituloAcademico';
      	$data['label'] = 'tituloAcademico';
	    $data['orden'] = 'orden';
	    $data['nombreCampoWhere'] = 'habilitado';
	    $data['valorCampoWhere'] = '1';

	    parent::getComboList($data);
	    return $this;
	}
}
if($_GET['debug'] == 'TituloAcademicoVO' or false){
	echo "DEBUG<br>";
	$kk = new TituloAcademicoVO();
	//print_r($kk->getAllRows());
	$kk->idTituloAcademico = 116;
	$kk->tipoEstudioMedico = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>