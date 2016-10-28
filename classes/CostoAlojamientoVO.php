<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class CostoAlojamientoVO extends Master2 {
    public $idCostoAlojamiento = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idDestino = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "destino",
		"referencia" => "",
	];
	public $idTipoAlojamiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo alojamiento",
		"referencia" => "",
	];
	public $costo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "costo",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $fechaVigencia = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha vigencia",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('costosAlojamiento');
		$this->setFieldIdName('idCostoAlojamiento');
		$this->idDestino['referencia'] = new DestinoVO();
		$this->idTipoAlojamiento['referencia'] = new TipoAlojamientoVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idCostoAlojamiento';
        $data['label'] = 'costoAlojamiento';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }

}

if($_GET['debug'] == 'CostoAlojamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new CostoAlojamientoVO();
	//print_r($kk->getAllRows());
	$kk->idCostoAlojamiento = 116;
	$kk->tipoDocumento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>