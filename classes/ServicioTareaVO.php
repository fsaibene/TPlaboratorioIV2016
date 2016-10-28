<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class ServicioTareaVO extends Master2 {
    public $idServicioTarea = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idServicioActividad = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => " Área de aplicación \\ actividad",
							"referencia" => "",
	];
    public $servicioTarea = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "denominación de la tarea",
	                        "longitud" => "255"
    ];
    public $servicioTareaSigla = ["valor" => "",
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
		$this->setTableName('serviciosTarea');
		$this->setFieldIdName('idServicioTarea');
		$this->idServicioActividad['referencia'] = new ServicioActividadVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList($data = NULL){
		$sql = 'select CONCAT_ws(" \\\ ", a3.servicioAreaDeAplicacion, a2.servicioActividad, a1.servicioTarea) as label, a1.idServicioTarea as data
				from serviciosTarea as a1
				inner join serviciosActividad as a2 using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as a3 using (idServicioAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
		$sql .= ' order by a3.orden, a2.orden, a1.orden, servicioAreaDeAplicacion, servicioActividad, servicioTarea ';
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

	/*
	 * esta funcion retorna los Servicios Tareas que tienen asociado algun Contrato Item
	 */
	public function getServiciosTareasContratoItems($data, $format = null){
		$sql = 'select CONCAT_WS(" \\\ ", servicioAreaDeAplicacion, servicioActividad, servicioTarea) as value, st.idServicioTarea as id
				from contratosItems as pst
				inner join contratosGerencias as cg using (idContratoGerencia)
				inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
				inner join contratosItems_ServiciosTareas as cist using (idContratoItem)
				inner join serviciosTarea as st using (idServicioTarea)
				inner join serviciosActividad as sa using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as saa using (idServicioAreaDeAplicacion)
				where true ';
		if($data['nombreCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' group by idServicioTarea ';
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
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return ;
	}

	public function getServiciosTareas($data, $format = null){
		$sql = 'select servicioTarea as value, idServicioTarea as id
				from serviciosTarea as a
				inner join serviciosActividad as b using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as c using (idServicioAreaDeAplicacion)
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getServiciosTareasContratoItems'){
	$aux = new ServicioTareaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->{$_GET['type']}($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getServiciosTareas'){
	$aux = new ServicioTareaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->{$_GET['type']}($data, 'json');
}

if($_GET['debug'] == 'ServicioTareaVO' or false){
	echo "DEBUG<br>";
	$kk = new ServicioTareaVO();
	//print_r($kk->getAllRows());
	$kk->idServicioTarea = 226;
	$kk->ServicioTarea = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>