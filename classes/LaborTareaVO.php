<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class LaborTareaVO extends Master2 {
    public $idLaborTarea = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idLaborActividad = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => " Área de aplicación \\ actividad",
							"referencia" => "",
	];
    public $laborTarea = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "denominación de la tarea",
	                        "longitud" => "255"
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
		$this->setTableName('laboresTarea');
		$this->setFieldIdName('idLaborTarea');
		$this->idLaborActividad['referencia'] = new LaborActividadVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList($data = NULL){
		$sql = 'select CONCAT_ws(" \\\ ", a3.laborAreaDeAplicacion, a2.laborActividad, a1.laborTarea) as label, a1.idLaborTarea as data
				from laboresTarea as a1
				inner join laboresActividad as a2 using (idLaborActividad)
				inner join laboresAreaDeAplicacion as a3 using (idLaborAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
		$sql .= ' order by a3.orden, a2.orden, a1.orden, laborAreaDeAplicacion, laborActividad, laborTarea ';
		//die($sql);
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

	public function getLaboresTareas($data, $format = null){
		$sql = 'select CONCAT_WS(" \\\ ", laborAreaDeAplicacion, laborActividad, laborTarea) as value, idLaborTarea as id
				from laboresTarea as a
				inner join laboresActividad as b using (idLaborActividad)
				inner join laboresAreaDeAplicacion as c using (idLaborAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		if($this->habilitado['valor'])
			$sql .= ' and a.habilitado and b.habilitado and c.habilitado';
		$sql .= ' order by value ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($format == 'json') {
				echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
			} else {
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				$this->result->setData($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getLaboresTareas'){
	$aux = new LaborTareaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->{$_GET['type']}($data, 'json');
}

if($_GET['debug'] == 'LaborTareaVO' or false){
	echo "DEBUG<br>";
	$kk = new LaborTareaVO();
	//print_r($kk->getAllRows());
	$kk->idLaborTarea = 226;
	$kk->LaborTarea = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>