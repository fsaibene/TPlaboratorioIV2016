<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class PuntoVentaVO extends Master2 {
    public $idPuntoVenta = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];

    public $nroPuntoVenta = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "nro punto venta",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $nombreFantasia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "nombre de fantasía",
        "longitud" => "255"
    ];
    public $domicilio = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "domicilio",
        "longitud" => "255"
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
		$this->setTableName('puntosVenta');
		$this->setFieldIdName('idPuntoVenta');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idPuntoVenta';
        $data['label'] = 'nroPuntoVenta';
        $data['orden'] = 'nroPuntoVenta';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'PuntoVentaVO' or false){
	echo "DEBUG<br>";
	$kk = new PuntoVentaVO();
	//print_r($kk->getAllRows());
	$kk->idPuntoVenta = 116;
	$kk->nroPuntoVenta = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>