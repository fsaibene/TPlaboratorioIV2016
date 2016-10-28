<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class AfipTipoIvaVO extends Master2 {
    public $idAfipTipoIva = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $afipTipoIva = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "tipo de iva AFIP",
    ];
    public $porcentaje = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "porcentaje",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
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
		$this->setTableName('afipTiposIva');
		$this->setFieldIdName('idAfipTipoIva');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList($data = NULL){
        $sql = 'select afipTipoIva as label, idAfipTipoIva as data, porcentaje
				from afipTiposIva
				where habilitado
				order by orden, afipTipoIva
            	';
        //echo($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if($ro->execute()){
                $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
                $this->result->setData($rs);
                $this->result->setStatus(STATUS_OK);
            }else{
                $this->result->setData($this);
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            }
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }
}

if($_GET['debug'] == 'AfipTipoIvaVO' or false){
	echo "DEBUG<br>";
	$kk = new AfipTipoIvaVO();
	//print_r($kk->getAllRows());
	$kk->idAfipTipoIva = 116;
	$kk->afipTipoIva = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>