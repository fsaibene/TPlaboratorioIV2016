<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class GmapsVO extends Master2 {
    public $idGmaps = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "idGmaps",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $search_street_address = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "search_street_address",
                       ];
    public $street_address = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "street_address",
                       ];
    public $street_number = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "street_number",
                       ];
    public $route = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "route",
                       ];
    public $neighborhood = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "neighborhood",
                       ];
    public $locality = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "locality",
                       ];
    public $administrative_area_level_3 = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "administrative_area_level_3",
                       ];
    public $administrative_area_level_2 = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "administrative_area_level_2",
                       ];
    public $administrative_area_level_1 = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "administrative_area_level_1",
                       ];
    public $country = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "country",
                       ];
    public $postal_code = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "postal_code",
                       ];
    public $lat = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "lat",
                       ];
    public $lng = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "lng",
                       ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('gmaps');
		$this->setFieldIdName('idGmaps');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        //$this->idUsuario['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];
        return;
    }
	/*
    public function getRowByIdSucursalEstablecimiento(){
        try {
            $sql = 'select * from '.$this->getTableName().' where idSucursalEstablecimiento = '.$this->idSucursalEstablecimiento['valor'];
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->mapData($rs);
                //print_r($this);
            } else {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage("ERROR, contacte al administrador.");
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }*/
}

if($_GET['debug'] == 'GmapsVO' or false){
	echo "DEBUG<br>";
	$kk = new GmapsVO();
	//print_r($kk->getAllRows());
	$kk->idPais = 116;
	$kk->titulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>