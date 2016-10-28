<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ServicioAreaDeAplicacionVO extends Master2 {
    public $idServicioAreaDeAplicacion = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $servicioAreaDeAplicacion = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "denominación del área de aplicación",
	                        "longitud" => "255"
    ];
    public $servicioAreaDeAplicacionSigla = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "sigla",
	                        "longitud" => "3"
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
		$this->setTableName('serviciosAreaDeAplicacion');
		$this->setFieldIdName('idServicioAreaDeAplicacion');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	/*public function insertData(){
		//print_r($this); die('dos');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$aux = new ServicioActividadVO();
			$aux->idServicioAreaDeAplicacion['valor'] = $this->idServicioAreaDeAplicacion['valor'];
			$aux->servicioActividad['valor'] = '-';
			$aux->servicioActividadSigla['valor'] = '-';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}*/

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idServicioAreaDeAplicacion';
        $data['label'] = 'servicioAreaDeAplicacion';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }

	public function getServiciosAreasDeAplicaciones($data, $format = null){
		$sql = 'select servicioAreaDeAplicacion as label, idServicioAreaDeAplicacion as data
				from serviciosAreaDeAplicacion as a
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by orden, label ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			//print_r($rs);
			$items = array();
			if($rs && count($rs) > 0) {
				if($format == 'json') {
					foreach ($rs as $row) {
						$items[] = array('id' => $row['data'], 'value' => $row['label']);
					}
					echo json_encode($items);
					return;
				} else {
					$this->result->setData($rs);
				}
			} else {
				if($format == 'json') { // aunque no traiga nada debo devolver un array
					echo json_encode($items);
				}
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getServiciosAreasDeAplicaciones'){
	$aux = new ServicioAreaDeAplicacionVO();
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->getServiciosAreasDeAplicaciones($data, 'json');
}


if($_GET['debug'] == 'ServicioAreaDeAplicacionVO' or false){
	echo "DEBUG<br>";
	$kk = new ServicioAreaDeAplicacionVO();
	//print_r($kk->getAllRows());
	$kk->idServicioAreaDeAplicacion = 116;
	$kk->ServicioAreaDeAplicacion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>