<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class AreaCapacitacionVO extends Master2 {
    public $idAreaCapacitacion = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "area" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $areaCapacitacion = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "area" => "string",
                       "nombre" => "area de capacitación",
                       ];
	public $orden = ["valor" => "0",
						"obligatorio" => TRUE,
						"area" => "integer",
						"nombre" => "orden",
						"validador" => ["admiteMenorAcero" => FALSE,
							"admiteCero" => TRUE,
							"admiteMayorAcero" => TRUE
						],
	];
	public $habilitado = ["valor" => TRUE,
						"obligatorio" => TRUE,
						"area" => "bool",
						"nombre" => "habilitado",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('areasCapacitacion');
		$this->setFieldIdName('idAreaCapacitacion');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idAreaCapacitacion';
      	$data['label'] = 'areaCapacitacion';
	    $data['orden'] = 'orden, areaCapacitacion';

	    parent::getComboList($data);
	    return $this;
	}

	public function getComboList3($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select ac.idAreaCapacitacion, areaCapacitacion, cac.idCapacitacionAreaCapacitacion
                    from areasCapacitacion as ac
                    left join capacitaciones_areasCapacitacion as cac on cac.idAreaCapacitacion = ac.idAreaCapacitacion';
			if($data['idCapacitacion'])
				$sql .= ' and idCapacitacion = '.$data['idCapacitacion'];
			//$sql .= ' where e.habilitado';
			$sql .= ' order by areaCapacitacion';
			//die($sql);

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
if($_GET['debug'] == 'AreaCapacitacionVO' or false){
	echo "DEBUG<br>";
	$kk = new AreaCapacitacionVO();
	//print_r($kk->getAllRows());
	$kk->idAreaCapacitacion = 116;
	$kk->areaCapacitacion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>