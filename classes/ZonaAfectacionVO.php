<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ZonaAfectacionVO extends Master2 {
    public $idZonaAfectacion = ["valor" => "",
       "obligatorio" => FALSE,
       "tipo" => "integer",
       "nombre" => "id",
       "validador" => ["admiteMenorAcero" => FALSE,
                        "admiteCero" => FALSE,
                        "admiteMayorAcero" => TRUE,
                        ],
    ];
    public $zonaAfectacion = ["valor" => "",
       "obligatorio" => TRUE,
       "tipo" => "string",
       "nombre" => "zona de afectación",
    ];
    public $direccion = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "dirección",
    ];
    public $sigla = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "sigla",
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
		$this->setTableName('zonasAfectacion');
		$this->setFieldIdName('idZonaAfectacion');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idZonaAfectacion';
      	$data['label'] = 'zonaAfectacion';
	    $data['orden'] = 'orden';

	    parent::getComboList($data);
	    return $this;
	}

    public function getComboList2(){
      	$data['data'] = 'idZonaAfectacion';
      	$data['label'] = 'direccion';
	    $data['orden'] = 'orden';

	    parent::getComboList($data);
	    return $this;
	}

	public function getZonaAfectacionPorIdUsuario($idUsuario){
		//$clave = md5($clave);
		//$idUsuario = 555;
		$sql = 'select za.*
				from zonasAfectacion as za
				INNER JOIN empleadosRelacionLaboral as erl using (idZonaAfectacion)
				inner join usuarios as u using (idEmpleado)
				where idUsuario = '.$idUsuario;
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
				$this->mapData($rs);
			} else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("No posee permisos.");
			}
		}catch(Exception $e) {
			//echo $sql;
			//print_r($e);die();
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
}
if($_GET['debug'] == 'ZonaAfectacionVO' or false){
	echo "DEBUG<br>";
	$kk = new ZonaAfectacionVO();
	//print_r($kk->getAllRows());
	$kk->idZonaAfectacion = 116;
	$kk->zonaAfectacion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>