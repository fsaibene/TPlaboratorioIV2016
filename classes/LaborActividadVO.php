<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class LaborActividadVO extends Master2 {
    public $idLaborActividad = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idLaborAreaDeAplicacion = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => " Área de aplicación",
							"referencia" => "",
	];
    public $laborActividad = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "denominación de la actividad",
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
		$this->setTableName('laboresActividad');
		$this->setFieldIdName('idLaborActividad');
		$this->idLaborAreaDeAplicacion['referencia'] = new LaborAreaDeAplicacionVO();
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
				//$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$aux = new LaborTareaVO();
			$aux->idLaborActividad['valor'] = $this->idLaborActividad['valor'];
			$aux->laborTarea['valor'] = 'Tareas de oficina';
			$aux->laborTareaSigla['valor'] = 'TA';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				//$this->conn->rollBack();
				return $this;
			}
			$aux = new LaborTareaVO();
			$aux->idLaborActividad['valor'] = $this->idLaborActividad['valor'];
			$aux->laborTarea['valor'] = 'Estadía';
			$aux->laborTareaSigla['valor'] = 'ES';
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				//$this->conn->rollBack();
				return $this;
			}
			$aux = new LaborTareaVO();
			$aux->idLaborActividad['valor'] = $this->idLaborActividad['valor'];
			$aux->laborTarea['valor'] = 'Transporte';
			$aux->laborTareaSigla['valor'] = 'TR';
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
	}*/

	public function getComboList($data = NULL){
		$sql = 'select case when a1.laborActividad = "-" then a2.laborAreaDeAplicacion else CONCAT(a2.laborAreaDeAplicacion, "\\\", a1.laborActividad) end as label, a1.idLaborActividad as data
				from laboresActividad as a1
				inner join laboresAreaDeAplicacion as a2 using (idLaborAreaDeAplicacion)
				order by a2.orden, a1.orden, laborAreaDeAplicacion, laborActividad
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
	 * retorna el combo para seleccionar de un proyecto x, los idLaborActividad que corresponden a los idLaborTarea asociados el contrato
	 */
	public function getComboList2($data){
		$sql = 'select case when sa.laborActividad = "-" then sada.laborAreaDeAplicacion else CONCAT(sada.laborAreaDeAplicacion, "\\\", sa.laborActividad) end as label
					, sa.idLaborActividad as data
				from contratosItems as pst
				inner join laboresTarea as st using (idLaborTarea)
				inner join laboresActividad as sa using (idLaborActividad)
				inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
				left join contratosGerencias as p using (idContratoGerencia)
				where true ';
		if($data['idContrato'])
			$sql .= ' and pst.idContrato = '.$data['idContrato'];
		$sql .= ' group by sa.idLaborActividad  ';
		$sql .= ' order by sada.orden, sada.laborAreaDeAplicacion, sa.orden, sa.laborActividad ';
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

	public function getLaborActividadPorProyecto($data, $format = null){
		$sql = 'select CONCAT_ws(" \\\ ", sada.laborAreaDeAplicacion, sa.laborActividad) as label
					, sa.idLaborActividad as data
				from contratosItems_laboresTareas as cist
				inner join contratosItems as pst using (idContratoItem)
				inner join laboresTarea as st using (idLaborTarea)
				inner join laboresActividad as sa using (idLaborActividad)
				inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
				inner join contratosGerencias as cg using (idContratoGerencia)
				inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
				where true ';
		if($data['idContratoGerenciaProyecto'])
			$sql .= ' and cgp.idContratoGerenciaProyecto = '.$data['idContratoGerenciaProyecto'];
		if($this->habilitado['valor'])
			$sql .= ' and sa.habilitado';
		$sql .= ' group by sa.idLaborActividad  
				order by sada.orden, sada.laborAreaDeAplicacion, sa.orden, sa.laborActividad ';
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

	public function getLaboresActividades($data, $format = null){
		$sql = 'select laborActividad as label, idLaborActividad as data
				from laboresActividad as a
				inner join laboresAreaDeAplicacion as b using (idLaborAreaDeAplicacion)
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getLaborActividadPorProyecto'){
	$aux = new LaborActividadVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data['idContratoGerenciaProyecto'] = $_GET['idContratoGerenciaProyecto'];
	$aux->getLaborActividadPorProyecto($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getLaboresActividades'){
	$aux = new LaborActividadVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->getLaboresActividades($data, 'json');
}

if($_GET['debug'] == 'LaborActividadVO' or false){
	echo "DEBUG<br>";
	$kk = new LaborActividadVO();
	//print_r($kk->getAllRows());
	$kk->idLaborActividad = 226;
	$kk->LaborActividad = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>