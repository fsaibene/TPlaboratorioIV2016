<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class ServicioActividadVO extends Master2 {
    public $idServicioActividad = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idServicioAreaDeAplicacion = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => " Área de aplicación",
							"referencia" => "",
	];
    public $servicioActividad = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "denominación de la actividad",
	                        "longitud" => "255"
    ];
    public $servicioActividadSigla = ["valor" => "",
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
		$this->setTableName('serviciosActividad');
		$this->setFieldIdName('idServicioActividad');
		$this->idServicioAreaDeAplicacion['referencia'] = new ServicioAreaDeAplicacionVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function insertData(){
		//print_r($this); die('dos');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				//$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$aux = new ServicioTareaVO();
			$aux->idServicioActividad['valor'] = $this->idServicioActividad['valor'];
			$aux->servicioTarea['valor'] = 'Tareas de oficina';
			$aux->servicioTareaSigla['valor'] = 'TA';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				//$this->conn->rollBack();
				return $this;
			}
			$aux = new ServicioTareaVO();
			$aux->idServicioActividad['valor'] = $this->idServicioActividad['valor'];
			$aux->servicioTarea['valor'] = 'Estadía';
			$aux->servicioTareaSigla['valor'] = 'ES';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				//$this->conn->rollBack();
				return $this;
			}
			$aux = new ServicioTareaVO();
			$aux->idServicioActividad['valor'] = $this->idServicioActividad['valor'];
			$aux->servicioTarea['valor'] = 'Transporte';
			$aux->servicioTareaSigla['valor'] = 'TR';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				//$this->conn->rollBack();
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
	}

	public function getComboList($data = NULL){
		$sql = 'select case when a1.servicioActividad = "-" then a2.servicioAreaDeAplicacion else CONCAT(a2.servicioAreaDeAplicacion, "\\\", a1.servicioActividad) end as label, a1.idServicioActividad as data
				from serviciosActividad as a1
				inner join serviciosAreaDeAplicacion as a2 using (idServicioAreaDeAplicacion)
				order by a2.orden, a1.orden, servicioAreaDeAplicacion, servicioActividad
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

	/*
	 * retorna el combo para seleccionar de un proyecto x, los idServicioActividad que corresponden a los idServicioTarea asociados el contrato
	 */
	public function getComboList2($data){
		$sql = 'select case when sa.servicioActividad = "-" then sada.servicioAreaDeAplicacion else CONCAT(sada.servicioAreaDeAplicacion, "\\\", sa.servicioActividad) end as label
					, sa.idServicioActividad as data
				from contratosItems as pst
				inner join serviciosTarea as st using (idServicioTarea)
				inner join serviciosActividad as sa using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
				left join contratosGerencias as p using (idContratoGerencia)
				where true ';
		if($data['idContrato'])
			$sql .= ' and pst.idContrato = '.$data['idContrato'];
		$sql .= ' group by sa.idServicioActividad  ';
		$sql .= ' order by sada.orden, sada.servicioAreaDeAplicacion, sa.orden, sa.servicioActividad ';
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

	public function getServicioActividadPorProyecto($data, $format = null){
		$sql = 'select CONCAT_ws(" \\\ ", sada.servicioAreaDeAplicacion, sa.servicioActividad) as label
					, sa.idServicioActividad as data
				from contratosItems_serviciosTareas as cist
				inner join contratosItems as pst using (idContratoItem)
				inner join serviciosTarea as st using (idServicioTarea)
				inner join serviciosActividad as sa using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
				inner join contratosGerencias as cg using (idContratoGerencia)
				inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
				where true ';
		if($data['idContratoGerenciaProyecto'])
			$sql .= ' and cgp.idContratoGerenciaProyecto = '.$data['idContratoGerenciaProyecto'];
		if($this->habilitado['valor'])
			$sql .= ' and sa.habilitado';
		$sql .= ' group by sa.idServicioActividad  
				order by sada.orden, sada.servicioAreaDeAplicacion, sa.orden, sa.servicioActividad ';
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
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return ;
	}

	public function getServiciosActividades($data, $format = null){
		$sql = 'select servicioActividad as label, idServicioActividad as data
				from serviciosActividad as a
				inner join serviciosAreaDeAplicacion as b using (idServicioAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		if($this->habilitado['valor'])
			$sql .= ' and a.habilitado and b.habilitado';
		$sql .= ' order by label ';
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getServicioActividadPorProyecto'){
	$aux = new ServicioActividadVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data['idContratoGerenciaProyecto'] = $_GET['idContratoGerenciaProyecto'];
	$aux->getServicioActividadPorProyecto($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getServiciosActividades'){
	$aux = new ServicioActividadVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->getServiciosActividades($data, 'json');
}

if($_GET['debug'] == 'ServicioActividadVO' or false){
	echo "DEBUG<br>";
	$kk = new ServicioActividadVO();
	//print_r($kk->getAllRows());
	$kk->idServicioActividad = 226;
	$kk->ServicioActividad = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>