<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OrdenCompraItemVO extends Master2 {
    public $idOrdenCompraItem = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idOrdenCompra = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "orden de compra",
                       "referencia" => "",
                       ];
	public $item = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "descripción"
	];
	public $cantidad= ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "cantidad",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
			],
	];
	public $idTipoMoneda = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "moneda",
		"referencia" => "",
	];
	public $precioUnitario = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "C/U",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ordenesCompra_items');
		$this->setFieldIdName('idOrdenCompraItem');
		$this->idOrdenCompra['referencia'] = new OrdenCompraVO();
		$this->idTipoMoneda['referencia'] = new TipoMonedaVO();
	}

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }
}


if($_GET['debug'] == 'OrdenCompraItemVO' or false){
	echo "DEBUG<br>";
	$kk = new OrdenCompra_ItemVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenCompraItem = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>