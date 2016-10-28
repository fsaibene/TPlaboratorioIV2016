<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoEquipamientoVO extends Master2 {
    public $idTipoEquipamiento = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
                                "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
                                ],
                             ];
    public $tipoEquipamiento = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "string",
                            "nombre" => "tipo de equipamiento",
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
		$this->setTableName('tiposEquipamiento');
		$this->setFieldIdName('idTipoEquipamiento');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoEquipamiento';
        $data['label'] = 'tipoEquipamiento';
        $data['orden'] = 'orden';
        $data['nombreCampoWhere'] = 'habilitado';
        $data['valorCampoWhere'] = true;
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'TipoEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoEquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoEquipamiento = 116;
	$kk->tipoEquipamiento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>