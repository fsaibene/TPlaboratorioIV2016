<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class CapacitacionVO extends Master2 {
	public $idCapacitacion = ["valor" => "", 
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
	];
	public $idTipoCapacitacion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de capacitación",
						"referencia" => "",
	];
	public $idClaseCapacitacion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "clase de capacitación",
						"referencia" => "",
	];
	public $capacitacion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "nombre de la capacitación",
	];
	public $objetivo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "objetivo de la capacitación",
	];
	public $horasDuracion = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "float",
						"nombre" => "horas de duración",
						"validador" => ["admiteMenorAcero" => FALSE,
							"admiteCero" => FALSE,
							"admiteMayorAcero" => TRUE,
						],
	];
	public $fechaReal = ["valor" => "",
						"obligatorio" => false,
						"tipo" => "date",
						"nombre" => "fecha real",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE
						],
	];
	public $otrosAsistentes = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "otros asistentes",
	];
	public $instructores = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "instructores",
	];
	public $instructoresExperiencia = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "experiencia de los instructores",
	];
	public $eficaciaFecha = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "date",
						"nombre" => "fecha de medición de eficacia",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => FALSE
						],
	];
	public $eficaciaResultado = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "resultado de medición de eficacia",
	];
	public $metodoEvaluacionEvidencia = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "método de evaluación de evidencias",
	];
	public $observaciones = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "observaciones",
	];
	public $archivo = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "archivo",
		"ruta" => "capacitaciones/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $capacitacionEmpleadoArray;
	public $capacitacionEficaciaResponsableArray;
	public $capacitacionAreaCapacitacionArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('capacitaciones');
		$this->setFieldIdName('idCapacitacion');
		$this->idTipoCapacitacion['referencia'] = new TipoCapacitacionVO();
		$this->idClaseCapacitacion['referencia'] = new ClaseCapacitacionVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	/*
	 * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
	 */
	public function insertData(){
		//print_r($this); die('dos');
		try{
			//echo $this->idEstablecimiento['valor']; die();
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			if($this->capacitacionEmpleadoArray) {
				//print_r($this->capacitacionEmpleadoArray); die('tres');
				foreach ($this->capacitacionEmpleadoArray as $capacitacionEmpleado){
					//print_r($capacitacionEmpleado); die();
					$capacitacionEmpleado->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionEmpleado->insertData();
					if($capacitacionEmpleado->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionEmpleado); die('error uno');
						$this->result = $capacitacionEmpleado->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->capacitacionEficaciaResponsableArray) {
				//print_r($this->capacitacionEficaciaResponsableArray); die('tres');
				foreach ($this->capacitacionEficaciaResponsableArray as $capacitacionEficaciaResponsable){
					//print_r($capacitacionEmpleado); die();
					$capacitacionEficaciaResponsable->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionEficaciaResponsable->insertData();
					if($capacitacionEficaciaResponsable->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionEficaciaResponsable); die('error uno');
						$this->result = $capacitacionEficaciaResponsable->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->capacitacionAreaCapacitacionArray) {
				//print_r($this->capacitacionAreaCapacitacionArray); die('tres');
				foreach ($this->capacitacionAreaCapacitacionArray as $capacitacionAreaCapacitacion){
					//print_r($capacitacionAreaCapacitacion); die();
					$capacitacionAreaCapacitacion->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionAreaCapacitacion->insertData();
					if($capacitacionAreaCapacitacion->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionAreaCapacitacion); die('error uno');
						$this->result = $capacitacionAreaCapacitacion->result;
						$this->conn->rollBack();
						return $this;
					}
				}
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

	/*
	 * Hago el update de la tabla padre y luego borro los registros se la tabla muchos a muchos y los vuelvo a insertar.
	 * Tiene que ser asi (borrar y crear) porque quiza me eliminaron un registro de la tabla muchos a muchos.
	 */
	public function updateData(){
		//print_r($this); die('uno');
		try{
			//$aux = clone $this;
			$this->conn->beginTransaction();
			//print_r($this); //die();
			parent::updateData();
			if($this->result->getStatus() != STATUS_OK) {
				//print_r($this); die('error cero');
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); //die();
			$ce = new CapacitacionEmpleadoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idCapacitacion';
			$data['valorCampoWhere'] = $this->idCapacitacion['valor'];
			$ce->deleteData($data);
			if($ce->result->getStatus() != STATUS_OK) {
				//print_r($ce); die('error uno');
				$this->result = $ce->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->capacitacionEmpleadoArray) {
				//print_r($this->capacitacionEmpleadoArray); die('tres');
				foreach ($this->capacitacionEmpleadoArray as $capacitacionEmpleado){
					//print_r($capacitacionEmpleado); die();
					$capacitacionEmpleado->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionEmpleado->insertData();
					if($capacitacionEmpleado->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionEmpleado); die('error uno');
						$this->result = $capacitacionEmpleado->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			//print_r($this); //die();
			$cer = new CapacitacionEficaciaResponsableVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idCapacitacion';
			$data['valorCampoWhere'] = $this->idCapacitacion['valor'];
			$cer->deleteData($data);
			if($cer->result->getStatus() != STATUS_OK) {
				//print_r($cer); die('error uno');
				$this->result = $cer->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->capacitacionEficaciaResponsableArray) {
				//print_r($this->capacitacionEficaciaResponsableArray); die('tres');
				foreach ($this->capacitacionEficaciaResponsableArray as $capacitacionEficaciaResponsable){
					//print_r($capacitacionEmpleado); die();
					$capacitacionEficaciaResponsable->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionEficaciaResponsable->insertData();
					if($capacitacionEficaciaResponsable->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionEficaciaResponsable); die('error uno');
						$this->result = $capacitacionEficaciaResponsable->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			//print_r($this); //die();
			$cap = new CapacitacionAreaCapacitacionVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idCapacitacion';
			$data['valorCampoWhere'] = $this->idCapacitacion['valor'];
			$cap->deleteData($data);
			if($cap->result->getStatus() != STATUS_OK) {
				//print_r($cap); die('error uno');
				$this->result = $cap->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->capacitacionAreaCapacitacionArray) {
				//print_r($this->capacitacionAreaCapacitacionArray); die('tres');
				foreach ($this->capacitacionAreaCapacitacionArray as $capacitacionAreaCapacitacion){
					//print_r($capacitacionAreaCapacitacion); die();
					$capacitacionAreaCapacitacion->idCapacitacion['valor'] = $this->idCapacitacion['valor'];
					$capacitacionAreaCapacitacion->insertData();
					if($capacitacionAreaCapacitacion->result->getStatus()  != STATUS_OK) {
						//print_r($capacitacionAreaCapacitacion); die('error uno');
						$this->result = $capacitacionAreaCapacitacion->result;
						$this->conn->rollBack();
						return $this;
					}
				}
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

	/*
	 * este método lo uso por ejemplo desde el ABMcapacitacionArchivo.php ya que necesito solo updatear la capacitacion y no todas la entidades anexas a ella...
	 */
	public function updateData2(){
		parent::updateData();
	}

	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'select c.idCapacitacion, c.capacitacion, c.fechaReal, c.archivo
				from capacitaciones as c ';
		if($data['idEmpleado']){
			$sql .= ' inner join capacitaciones_empleados as ce using (idCapacitacion)';
		}
		$sql .= ' where true ';
		if($data['idEmpleado']){
			$sql .= ' and ce.idEmpleado = '.$data['idEmpleado'];
		}
		if($data['fechaReal']){
			$sql .= ' and c.fechaReal = "'.convertDateEsToDb($data['fechaReal']).'"';
		}
		if($data['capacitacion']){
			$sql .= ' and c.capacitacion like "%'.$data['capacitacion'].'%"';
		}
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			//print_r($rs);die();
			$this->result->setData($rs);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}
}

// debug zone
if($_GET['debug'] == 'CapacitacionVO' or false){
	echo "DEBUG<br>";
	$kk = new CapacitacionVO();
	//print_r($kk->getAllRows());
	$kk->idCapacitacion = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
