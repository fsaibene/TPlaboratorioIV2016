<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProyectoComisionVO extends Master2 implements  iListadoExportableDinamico{
	public $idProyectoComision = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idContratoGerenciaProyecto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "gerencia \ Proyecto",
		"referencia" => "",
	];
	public $idContratoGerenciaLocalizacion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "provincia \ Localización",
		"referencia" => "",
	];
	public $nroProyectoComision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "Nro. de comisión",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idServicioActividad = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => " Área de aplicación \ Actividad",
						"referencia" => "",
	];
	public $fechaInicio = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha inicio",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $fechaFin = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha fin",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "observaciones",
					];

	public $idProyectoComisionEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionEmpleado",
		"referencia" => "",
	];
	public $idProyectoComisionVehiculo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionVehiculo",
		"referencia" => "",
	];
	public $idProyectoComisionEquipamiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionEquipamiento",
		"referencia" => "",
	];
	public $idProyectoComisionEmpleadoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionEmpleadoDetalle",
		"referencia" => "",
	];
	public $idProyectoComisionVehiculoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionVehiculoDetalle",
		"referencia" => "",
	];
	public $idProyectoComisionEquipamientoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idProyectoComisionEquipamientoDetalle",
		"referencia" => "",
	];
	public $proyectoComisionEmpleadoArray;
	public $proyectoComisionVehiculoArray;
	public $proyectoComisionEquipamientoArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('proyectosComisiones');
		$this->setFieldIdName('idProyectoComision');
		$this->idContratoGerenciaProyecto['referencia'] = new ContratoGerenciaProyectoVO();
		$this->idContratoGerenciaLocalizacion['referencia'] = new ContratoGerenciaLocalizacionVO();
		$this->idServicioActividad['referencia'] = new ServicioActividadVO();
		//$this->idProyectoComisionEmpleadoDetalle['referencia'] =  new ProyectoComisionEmpleadoDetalleVO();
		//$this->idProyectoComisionVehiculoDetalle['referencia'] =  new ProyectoComisionVehiculoDetalleVO();
		$this->excluirAtributo('idProyectoComisionEmpleado');
		$this->excluirAtributo('idProyectoComisionVehiculo');
		$this->excluirAtributo('idProyectoComisionEquipamiento');
		$this->excluirAtributo('idProyectoComisionEmpleadoDetalle');
		$this->excluirAtributo('idProyectoComisionVehiculoDetalle');
		$this->excluirAtributo('idProyectoComisionEquipamientoDetalle');
		$this->getNroProyectoComision();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if (strtotime(convertDateEsToDb($this->fechaInicio['valor'])) > strtotime(convertDateEsToDb($this->fechaFin['valor'])) ) {
			$resultMessage = 'La fecha Fin no puede ser menor que la fecha Inicio.';
		}
		if ($this->nroProyectoComision['valor'] > 9999 ) {
			$resultMessage = 'El Nro. de guía de ruta no puede ser mayor que 9999.';
		}
        return $resultMessage;
 	}

	public function getNroProyectoComision(){
		$sql = "select max(nroProyectoComision) as nroProyectoComision from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroProyectoComision['valor'] = $rs[0]['nroProyectoComision'] + 1;
			} else {
				$this->nroProyectoComision['valor'] = 1;
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getCodigoProyectoComision(){
		//$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		$codigo = 'VDC';
		$codigo .= '-'.str_pad($this->nroProyectoComision['valor'], 4, '0', STR_PAD_LEFT);
		return $codigo;
	}

	public function getAllRows2($data = null){
		$sql = 'select a.*, c.gerencia, z.nombreReferencia, saa.servicioAreaDeAplicacion, sa.servicioActividad, l.localizacion
					, getCodigoContratoGerenciaProyecto(a.idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto
					, getCodigoProyectoComision(a.idProyectoComision) as codigoProyectoComision
				from proyectosComisiones as a
				inner join contratosGerenciasProyectos as z using (idContratoGerenciaProyecto)
				inner join contratosGerencias as c on z.idContratoGerencia = c.idContratoGerencia
				inner join contratosGerenciasLocalizaciones as l using (idContratoGerenciaLocalizacion)
				inner join serviciosActividad as sa using (idServicioActividad)
				inner join serviciosAreaDeAplicacion as saa using (idServicioAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and c.'.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
//		die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$this->result->setData($rs);
				//print_r($this); die();
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
	}

	/*
	 * se armó esta función para encapsular el insert y el update que viene desde el form de ABMproyectoComision.php
	 * se quería devolver un solo result para simpleificar el manejo.
	 */
	public function insertDataRecursos($post){
		try{
			$this->conn->beginTransaction();

			// EMPLEADOS
			$this->idProyectoComisionEmpleado['referencia']->deleteDataArray($post['idProyectoComision'], $post['proyectoComisionEmpleadoArray']);
			$this->result = $this->idProyectoComisionEmpleado['referencia']->result;
			if($this->idProyectoComisionEmpleado['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['proyectoComisionEmpleadoArray']){
				$this->idProyectoComisionEmpleado['referencia']->insertDataArray($post['idProyectoComision'], $post['proyectoComisionEmpleadoArray']);
				$this->result = $this->idProyectoComisionEmpleado['referencia']->result;
				if($this->idProyectoComisionEmpleado['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pcemd';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idProyectoComisionEmpleadoDetalle['referencia']->{$postName})) {
						if ($this->idProyectoComisionEmpleadoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idProyectoComisionEmpleadoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idProyectoComisionEmpleadoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idProyectoComisionEmpleadoDetalle['referencia']->idProyectoComision['valor'] = $_POST['idProyectoComision'];
			//print_r($this->idProyectoComisionEmpleadoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idProyectoComisionEmpleadoDetalle['referencia'];
			if ($this->idProyectoComisionEmpleadoDetalle['referencia']->{$this->idProyectoComisionEmpleadoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idProyectoComisionEmpleadoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idProyectoComisionEmpleadoDetalle['referencia']->insertData();
			}
			$this->result = $this->idProyectoComisionEmpleadoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idProyectoComisionEmpleadoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idProyectoComisionEmpleadoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			// VEHICULOS
			$this->idProyectoComisionVehiculo['referencia']->deleteDataArray($post['idProyectoComision'], $post['proyectoComisionVehiculoArray']);
			$this->result = $this->idProyectoComisionVehiculo['referencia']->result;
			if($this->idProyectoComisionVehiculo['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['proyectoComisionVehiculoArray']){
				$this->idProyectoComisionVehiculo['referencia']->insertDataArray($post['idProyectoComision'], $post['proyectoComisionVehiculoArray']);
				$this->result = $this->idProyectoComisionVehiculo['referencia']->result;
				if($this->idProyectoComisionVehiculo['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pcved';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idProyectoComisionVehiculoDetalle['referencia']->{$postName})) {
						if ($this->idProyectoComisionVehiculoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idProyectoComisionVehiculoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idProyectoComisionVehiculoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idProyectoComisionVehiculoDetalle['referencia']->idProyectoComision['valor'] = $_POST['idProyectoComision'];
			//print_r($this->idProyectoComisionVehiculoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idProyectoComisionVehiculoDetalle['referencia'];
			if ($this->idProyectoComisionVehiculoDetalle['referencia']->{$this->idProyectoComisionVehiculoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idProyectoComisionVehiculoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idProyectoComisionVehiculoDetalle['referencia']->insertData();
			}
			$this->result = $this->idProyectoComisionVehiculoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idProyectoComisionVehiculoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idProyectoComisionVehiculoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			// EQUIPAMIENTOS
			$this->idProyectoComisionEquipamiento['referencia']->deleteDataArray($post['idProyectoComision'], $post['proyectoComisionEquipamientoArray']);
			$this->result = $this->idProyectoComisionEquipamiento['referencia']->result;
			if($this->idProyectoComisionEquipamiento['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['proyectoComisionEquipamientoArray']){
				$this->idProyectoComisionEquipamiento['referencia']->insertDataArray($post['idProyectoComision'], $post['proyectoComisionEquipamientoArray']);
				$this->result = $this->idProyectoComisionEquipamiento['referencia']->result;
				if($this->idProyectoComisionEquipamiento['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pceqd';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idProyectoComisionEquipamientoDetalle['referencia']->{$postName})) {
						if ($this->idProyectoComisionEquipamientoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idProyectoComisionEquipamientoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idProyectoComisionEquipamientoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idProyectoComisionEquipamientoDetalle['referencia']->idProyectoComision['valor'] = $_POST['idProyectoComision'];
			//print_r($this->idProyectoComisionEquipamientoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idProyectoComisionEquipamientoDetalle['referencia'];
			if ($this->idProyectoComisionEquipamientoDetalle['referencia']->{$this->idProyectoComisionEquipamientoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idProyectoComisionEquipamientoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idProyectoComisionEquipamientoDetalle['referencia']->insertData();
			}
			$this->result = $this->idProyectoComisionEquipamientoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idProyectoComisionEquipamientoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idProyectoComisionEquipamientoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

	public function getComboList2($data){
		try{
			$sql = 'select CONCAT("SNC-", p3.nroContrato, " VDC-", pc.nroProyectoComision, " F.inicio:", pc.fechaInicio, " F.fin: ", pc.fechaFin) as label, pce.idProyectoComisionEmpleado as data
					from proyectosComisiones as pc
					inner join contratosGerenciasProyectos as p using (idContratoGerenciaProyecto)
					inner join contratosGerencias as p2 using (idContratoGerencia)
					inner join contratos p3 using (idContrato)
					inner join proyectosComisiones_empleados as pce using (idProyectoComision)
					where pce.idEmpleado = '.$data['idEmpleado'].'
					and pce.idProyectoComisionEmpleado not in (
						select idProyectoComisionEmpleado
						from rendicionesViatico
						where idEmpleado = '.$data['idEmpleado'].' and idProyectoComisionEmpleado is not null';
			if($data['idProyectoComisionEmpleado']) {
				$sql .= ' and idProyectoComisionEmpleado != ' . $data['idProyectoComisionEmpleado'];
			}
			$sql .= ' )
					group by idProyectoComision
					order by pc.fechaInicio';
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

	/*
	 * me retorna las comisiones en las que está un empleado ya sea como integrante de la misma o como responsable técnico
	 */
	public function getComboListParaATR($data){
		try{
			$sql = 'select *
					from (
						select CONCAT("SNC-", p3.nroContrato, " VDC-", pc.nroProyectoComision, " F.inicio: ", DATE_FORMAT(pc.fechaInicio,"%d/%m/%Y"), " F.fin: ", DATE_FORMAT(pc.fechaFin,"%d/%m/%Y")) as label
							, pc.idProyectoComision as data, pc.nroProyectoComision
						FROM proyectosComisiones as pc
						inner join contratosGerenciasProyectos as p using (idContratoGerenciaProyecto)
						inner join contratosGerencias as p2 using (idContratoGerencia)
						inner join contratos p3 using (idContrato)
						inner join proyectosComisiones_empleados as pce using (idProyectoComision)
						where idEmpleado = '.$data['idEmpleado'].'
						
					union 
					
						select CONCAT("SNC-", p3.nroContrato, " VDC-", pc.nroProyectoComision, " F.inicio: ", DATE_FORMAT(pc.fechaInicio,"%d/%m/%Y"), " F.fin: ", DATE_FORMAT(pc.fechaFin,"%d/%m/%Y")) as label
							, pc.idProyectoComision as data, pc.nroProyectoComision
						FROM proyectosComisiones as pc
						inner join contratosGerenciasProyectos as p using (idContratoGerenciaProyecto)
						inner join contratosGerencias as p2 using (idContratoGerencia)
						inner join contratos p3 using (idContrato)
						inner join (
							select cgrt.*
							FROM contratosGerenciasResponsablesTecnicos as cgrt
							INNER JOIN (
								SELECT cgrt.idContratoGerencia, cgrt.idTipoCaracterResponsable, MAX(fechaVigencia) as maxFechaVigencia, pc.idProyectoComision
								FROM contratosGerenciasResponsablesTecnicos as cgrt
								inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
								inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
								where cgrt.fechaVigencia <= pc.fechaFin
								GROUP BY cgrt.idContratoGerencia, cgrt.idTipoCaracterResponsable, pc.idProyectoComision
							) AS x 
								ON x.maxFechaVigencia = cgrt.fechaVigencia 
								AND x.idContratoGerencia = cgrt.idContratoGerencia 
								AND x.idTipoCaracterResponsable = cgrt.idTipoCaracterResponsable
							where idEmpleado = '.$data['idEmpleado'].'
						)
						as cgrt using (idContratoGerencia)
					) as x
					group by data
					order by nroProyectoComision desc';
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

	/*
	 * tengo que cambiar el nombre de la duncion por un problema de recursividad
	 */
	public function getRowById2($data = null){
		parent::getRowById();
		if($this->result->getStatus() == STATUS_OK) {
			// EMPLEADOS
			$this->idProyectoComisionEmpleado['referencia'] = new ProyectoComisionEmpleadoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionEmpleado['referencia']->getAllRows($data);
			//$result = $proyectoComisionEmpleado->result;
			if($this->idProyectoComisionEmpleado['referencia']->result->getStatus() == STATUS_OK) {
				//$this->proyectoComisionEmpleadoArray = $this->idProyectoComisionEmpleado['referencia']->result->getData();
			}
			//print_r($this->proyectoComisionEmpleadoArray);die();
			$this->idProyectoComisionEmpleadoDetalle['referencia'] = new ProyectoComisionEmpleadoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionEmpleadoDetalle['referencia']->getRowById($data);
			//$this->idProyectoComisionEmpleadoDetalle['referencia'] = $proyectoComisionEmpleadoDetalle;
			$this->idProyectoComisionEmpleadoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idProyectoComisionEmpleadoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...

			// VEHICULOS
			$this->idProyectoComisionVehiculo['referencia'] = new ProyectoComisionVehiculoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionVehiculo['referencia']->getAllRows($data);
			//$result = $proyectoComisionVehiculo->result;
			if($this->idProyectoComisionVehiculo['referencia']->result->getStatus() == STATUS_OK) {
				//$this->proyectoComisionVehiculoArray = $proyectoComisionVehiculo->result->getData();
			}
			//print_r($this->proyectoComisionVehiculoArray);die();
			$this->idProyectoComisionVehiculoDetalle['referencia'] = new ProyectoComisionVehiculoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionVehiculoDetalle['referencia']->getRowById($data);
			//$this->idProyectoComisionVehiculoDetalle['referencia'] = $proyectoComisionVehiculoDetalle;
			$this->idProyectoComisionVehiculoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idProyectoComisionVehiculoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...

			// EQUIPAMIENTOS
			$this->idProyectoComisionEquipamiento['referencia'] = new ProyectoComisionEquipamientoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionEquipamiento['referencia']->getAllRows($data);
			//$result = $proyectoComisionEquipamiento->result;
			if($this->idProyectoComisionEquipamiento['referencia']->result->getStatus() == STATUS_OK) {
				//$this->proyectoComisionEquipamientoArray = $proyectoComisionEquipamiento->result->getData();
			}
			//print_r($this->proyectoComisionEquipamientoArray);die();
			$this->idProyectoComisionEquipamientoDetalle['referencia'] = new ProyectoComisionEquipamientoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$this->idProyectoComisionEquipamientoDetalle['referencia']->getRowById($data);
			//$this->idProyectoComisionEquipamientoDetalle['referencia'] = $proyectoComisionEquipamientoDetalle;
			$this->idProyectoComisionEquipamientoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idProyectoComisionEquipamientoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...
		}
		return $this;
	}

	/*
	 * devuelve un array con la info para ser mostrada luego en un calendario
	 */
	public function getProyectosComisionesParaCalendario($data){
		//fc_print($data);
		$sql = 'SELECT
					concat("proyectoComision-", idProyectoComision) as id
					, "#00a65a" as color
					, cg.gerencia as title
					, pc.fechaInicio as start
					, DATE_ADD(pc.fechaFin, interval 1 day) as end
					, true as allDay
					, concat_ws("<br>"
						, getCodigoProyectoComision(idProyectoComision)
						, concat("Localización: ", cgl.localizacion, " (", p.provincia, ")")
						, concat("Proyecto: ", cgp.nombreReferencia)
						, concat("Actividad: ", sada.servicioAreaDeAplicacion, " - ", sa.servicioActividad)
						, concat("Integrantes: ", pce2.integrantes)
						, concat("Equipamientos: ", pceq2.equipamientos)
						, concat("Vehículos: ", pcv2.vehiculos)
						, if(pc.observaciones is null, "", concat("Obs: ", pc.observaciones))
						) as description ';
		if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
			$sql .= ' , "ProyectoComisionPDF.php?" as urlAddress, concat("id=", idProyectoComision) as urlParamaters, null as url ';
		}
		$sql .= ' FROM proyectosComisiones as pc
					left join proyectosComisiones_empleados as pce using (idProyectoComision)
					left join proyectosComisiones_equipamientos as pceq using (idProyectoComision)
					left join proyectosComisiones_vehiculos as pcv using (idProyectoComision)
					INNER JOIN contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
					inner join contratosGerencias as cg using (idContratoGerencia)
					inner join contratosGerenciasLocalizaciones as cgl using (idContratoGerenciaLocalizacion)
					inner join provincias as p using (idProvincia)
					inner join serviciosActividad as sa using (idServicioActividad)
					inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
					left join (
						select idProyectoComision, GROUP_CONCAT(getEmpleado(idEmpleado) SEPARATOR " - ") as integrantes
						from proyectosComisiones_empleados as pce
						inner join empleados as e using (idEmpleado)
						group by idProyectoComision
					) as pce2 using (idProyectoComision)
					left join (
						select idProyectoComision, GROUP_CONCAT(getEquipamiento(idEquipamiento) SEPARATOR " - ") as equipamientos
						from proyectosComisiones_equipamientos as pceq
						inner join equipamientos as e using (idEquipamiento)
						group by idProyectoComision
					) as pceq2 using (idProyectoComision)
					left join (
						select idProyectoComision, GROUP_CONCAT(getVehiculo(idVehiculo) SEPARATOR " - ") as vehiculos
						from proyectosComisiones_vehiculos as pcv
						inner join vehiculos as v using (idVehiculo)
						group by idProyectoComision
					) as pcv2 using (idProyectoComision)
					where true ';
		if($data['idContrato']){
			$sql .= ' and cg.idContrato = '.$data['idContrato'];
		}
		if($data['idContratoGerencia']){
			$sql .= ' and cg.idContratoGerencia = '.$data['idContratoGerencia'];
		}
		if($data['idContratoGerenciaProyecto']){
			$sql .= ' and cgp.idContratoGerenciaProyecto = '.$data['idContratoGerenciaProyecto'];
		}
		if($data['idServicioActividad']){
			$sql .= ' and sa.idServicioActividad = '.$data['idServicioActividad'];
		}
		if($data['idServicioAreaDeAplicacion']){
			$sql .= ' and sada.idServicioAreaDeAplicacion = '.$data['idServicioAreaDeAplicacion'];
		}
		if($data['idEmpleadoArray'][0]){
			$sql .= ' and pce.idEmpleado in ('.implode(",", $data['idEmpleadoArray']).')';
		}
		if($data['idEquipamientoArray'][0]){
			$sql .= ' and pceq.idEquipamiento in ('.implode(",", $data['idEquipamientoArray']).')';
		}
		if($data['idVehiculoArray'][0]){
			$sql .= ' and pcv.idVehiculo in ('.implode(",", $data['idVehiculoArray']).')';
		}
		$sql .= ' and ((pc.fechaInicio BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (pc.fechaFin BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (pc.fechaInicio < "'.convertDateEsToDb($data['start']).'" and pc.fechaFin > "'.convertDateEsToDb($data['end']).'")) ';
		$sql .= ' GROUP BY id';
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC));
			foreach ($rs as &$aux){
				if($aux['urlAddress']){
					$aux['url'] = $aux['urlAddress'] . codificarGets($aux['urlParamaters']);
					$aux['urlParamaters'] = null;
				}
			}
			$this->result->setData($rs);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'SELECT getCodigoContrato(idContrato) as codigoContrato, 
					getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto,
					getCodigoProyectoComision (idProyectoComision) AS codigoProyectoComision,
	 				pc.idProyectoComision, pc.fechaInicio, pc.fechaFin
				from contratosGerenciasProyectos as p
				inner join contratosGerencias as c using (idContratoGerencia)
				inner join establecimientos as e using (idEstablecimiento)
				left join proyectosComisiones as pc using (idContratoGerenciaProyecto)
				left join proyectosComisiones_empleados as pce using (idProyectoComision)';
		$sql .= ' where idEmpleado = '.$_SESSION['usuarioLogueadoIdEmpleado'];
		if($data['idContrato']){
			$sql .= ' and c.idContrato = '.$data['idContrato'];
		}
		if($data['idCliente']){
			$sql .= ' and e.idEstablecimiento = '.$data['idCliente'];
		}
		if($data['fechaDesde']){
			$sql .= ' and pc.fechaInicio >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and pc.fechaInicio <= "'.convertDateEsToDb($data['fechaHasta']).'"';
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

	/*
	 * devuelve datos de la comision
	 */
	public function getProyectoComisionPorIdProyectoComisionEmpleado($data){
		$sql = 'select idProyectoComision, nroProyectoComision, DATE_FORMAT(pc.fechaInicio,"%d/%m/%Y") as fechaInicio, DATE_FORMAT(pc.fechaFin,"%d/%m/%Y") as fechaFin,
				idProyectoComisionEmpleado, 
				getCodigoContrato(idContrato) as codigoContrato,
				getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto,
				getCodigoProyectoComision(idProyectoComision) as codigoProyectoComision,
				gerencia, servicioActividad
				from proyectosComisiones as pc
				inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				inner join contratosGerencias as cg using (idContratoGerencia)
				inner join contratos as p using (idContrato)
				inner join serviciosActividad as sa using (idServicioActividad)
				inner join proyectosComisiones_empleados as pce using (idProyectoComision)
				where idProyectoComisionEmpleado = '.$data['idProyectoComisionEmpleado'].'
				group by nroProyectoComision
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}

	/*
	 * devuelve datos de la comision
	 */
	public function getInfoProyectoComisionParaATR($data){
		$sql = 'select e.establecimiento
					, getCodigoContrato(c.idContrato) as codigoContrato
					, cg.gerencia
					, getCodigoContratoGerenciaProyecto(cgp.idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto
					, concat(p.provincia, "\\\", cgl.localizacion) as localizacion
					, concat(cgi.apellido, ", ", cgi.nombres) as inspector
					, servicioActividad
					, pc.idProyectoComision
				from proyectosComisiones as pc
				inner JOIN serviciosActividad as sa using (idServicioActividad)
				inner JOIN contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				inner JOIN contratosGerencias as cg using (idContratoGerencia)
				inner JOIN contratosGerenciasLocalizaciones as cgl using (idContratoGerenciaLocalizacion)
				left join (
					select cgi.*, pc.idProyectoComision
					FROM contratosGerenciasInspectores as cgi
					inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
					inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
					INNER JOIN (
						SELECT cgi.idContratoGerencia, MAX(fechaVigencia) as maxFechaVigencia, pc.idProyectoComision
						FROM contratosGerenciasInspectores as cgi
						inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
						inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
						where cgi.fechaVigencia <= pc.fechaFin
						GROUP BY cgi.idContratoGerencia, pc.idProyectoComision
					) AS x 
						ON x.maxFechaVigencia = cgi.fechaVigencia 
						AND x.idContratoGerencia = cgi.idContratoGerencia 
						AND x.idProyectoComision = pc.idProyectoComision 
				)
				as cgi using (idProyectoComision)
				
				inner JOIN provincias as p using (idProvincia)
				inner JOIN establecimientos as e using (idEstablecimiento)
				inner JOIN contratos as c using (idContrato)
				where idProyectoComision = '.$data['idProyectoComision'].'
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}

	/*
	 * devuelve datos de la comision
	 */
	public function getInfoProyectoComisionParaMovimientoEquipamiento($data){
		$sql = 'select CONCAT_WS(" - ", 
					DATE_FORMAT(pc.fechaInicio,"%d/%m/%Y"),
					DATE_FORMAT(pc.fechaFin,"%d/%m/%Y"),
					getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto),
					gerencia, 
					servicioActividad) as detalleProyectoComision
				from proyectosComisiones as pc
				inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				inner join contratosGerencias as cg using (idContratoGerencia)
				inner join contratos as p using (idContrato)
				inner join serviciosActividad as sa using (idServicioActividad)
				where nroProyectoComision = '.$data['nroProyectoComision'].'
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}

	public function getProyectoComisionPDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();
			$pageBodyBody = $this->getPDF();
			//print_r($pageBodyBody); die();
			//echo $result->getData();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $pageBodyBody;
	}

	public function getPDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();
			$pcs = new ContratoGerenciaProyectoVO();
			$pcs->idContratoGerenciaProyecto['valor'] = $this->idContratoGerenciaProyecto['valor'];
			//$pcs->getRowById();

			$pueis = new ContratoGerenciaInspectorVO();
			//echo $this->fechaInicio['valor']; die();
			$pueis->getInspectoresVigentes($fecha = $this->fechaInicio['valor'], $idContratoGerencia = $this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['valor']);
			//print_r($pueis);die();

			$puerts = new ContratoGerenciaResponsableTecnicoVO();
			$puerts->getResponsablesTecnicosVigentes($fecha = $this->fechaInicio['valor'], $idContratoGerencia = $this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['valor']);
			//print_r($puerts);die();

			$pces = new ProyectoComisionEmpleadoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pces->getAllRows($data);

			$pcemd = new ProyectoComisionEmpleadoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcemd->getRowById($data);

			$pcvs = new ProyectoComisionVehiculoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcvs->getAllRows($data);

			$pcved = new ProyectoComisionVehiculoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcved->getRowById($data);

			$pceqs = new ProyectoComisionEquipamientoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pceqs->getAllRows($data);

			$pceqd = new ProyectoComisionEquipamientoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pceqd->getRowById($data);

			$pcptbs = new ProyectoComisionPlanificacionTrasladoBaseVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcptbs->getAllRows($data);
			//print_r($pcptbs); die();

			$pcptos = new ProyectoComisionPlanificacionTrasladoOperativoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcptos->getAllRows($data);
			//print_r($pcptos); die();

			$pcpalos = new ProyectoComisionPlanificacionAlojamientoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcpalos->getAllRows($data);
			//print_r($pcpalos); die();

			$pcpalis = new ProyectoComisionPlanificacionAlimentoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idProyectoComision';
			$data['valorCampoWhere'] = $this->idProyectoComision['valor'];
			$pcpalis->getAllRows($data);
			//print_r($pcpalis); die();

			// documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
			// para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
			//$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
			$pages = '';
			$page = '';
			$css = '<style>
						.font8 {
							font-size: 8pt;
						}
						table {
							width: 100%;
						}
						td {
							vertical-align: top;
						}
						th {
							background-color: #eee;
							text-align: center;
						}
	
						table.borderYes {
							background-color: #000;
						}
						table.borderYes td {
							background-color: #fff;
						}
					</style>';

			$pageHeader  = $css;
			$pageHeader .= '<page_header>';
			$pageHeader .= '	<table cellspacing="0" style="font-size: 8pt; margin: 10px 40px;">
										<tr>
											<td style="width: 70%; font-size: 18pt;">Código de comisión<br>'.$this->getCodigoProyectoComision().'</td>
											<td style="width: 20%; text-align: right;"><img src="'.getFullPath().'/img/logo-sinec-nuevo-295x217.jpg" alt="" width="120" style="margin: -10px -16px 0 0;" alt="" /></td>
										</tr>
									</table>';
			$pageHeader .= '</page_header>';

			$pageFooter = '<page_footer>';
			$pageFooter .= '	<p align="center">P&aacute;gina [[page_cu]]/[[page_nb]]</p>';
			$pageFooter .= '</page_footer>';

			$pageBegin = '<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="P">';
			$pageEnd = '</page>';

			$pageBody = '	<table style="" class="borderYes">
							<tr>
								<td style="width: 20%; background-color: #ddd;">Código de proyecto</td>
								<td style="width: 80%; ">'.$this->idContratoGerenciaProyecto['referencia']->getCodigoContratoGerenciaProyecto().'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Gerencia</td>
								<td style="width: 80%;">'.$this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Proyecto</td>
								<td style="width: 80%;">'.$this->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Localización</td>
								<td style="width: 80%;">'.$this->idContratoGerenciaLocalizacion['referencia']->localizacion['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Actividad</td>
								<td style="width: 80%;">'.$this->idServicioActividad['referencia']->idServicioAreaDeAplicacion['referencia']->servicioAreaDeAplicacion['valor'].'\\'.$this->idServicioActividad['referencia']->servicioActividad['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">F. Inicio</td>
								<td style="width: 80%;">'.convertDateDbToEs($this->fechaInicio['valor']).'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">F. Fin</td>
								<td style="width: 80%;">'.convertDateDbToEs($this->fechaFin['valor']).'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Obs:</td>
								<td style="width: 80%;">'.$this->observaciones['valor'].'</td>
							</tr>
						</table>';

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Inspectores de la Gerencia</div>';
			if($pueis->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($pueis->result->getData() as $puei) {
					//print_r($pc);die();
					$pageBody .= '	<tr>
									<td style="width: 15%; background-color: #ddd;">' . strtoupper($puei['caracter']) . '</td>
									<td style="width: 85%; ">' . $puei['apellido'] . ', ' . $puei['nombres'] . '<br>Teléfono: ' . $puei['telefono'] . '<br>Celular: ' . $puei['celular'] . '<br>Email: ' . $puei['email'] . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Responsables Técnicos de la Gerencia</div>';
			if($puerts->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($puerts->result->getData() as $puert) {
					//print_r($pc);die();
					$pageBody .= '	<tr>
									<td style="width: 15%; background-color: #ddd;">' . strtoupper($puert['caracter']) . '</td>
									<td style="width: 85%; ">' . $puert['empleado'] . '<br>Celular SINEC: ' . $puert['celularEmpresa'] . '<br>Celular personal: ' . $puert['celularParticular'] . '<br>Email: ' . $puert['emailEmpresa'] . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;

			$pageBody = '<div style="border: 1px; background-color: #ddd; text-align: center;">Recursos de la Comisión</div>';
			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Empleados</div>';
			if($pces->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pces->result->getData() as $pce) {
					//print_r($pce);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Empleado # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pce->idEmpleado['referencia']->getNombreCompleto() . '<br>
										Celular SINEC: ' . $pce->idEmpleado['referencia']->celularEmpresa['valor'] . '<br>
										Celular personal: ' . $pce->idEmpleado['referencia']->celularParticular['valor'] . '<br>
										Email: ' . $pce->idEmpleado['referencia']->emailEmpresa['valor'] . '
									</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pcemd->esEmpleadoTemporario['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Empleado temporario</td>
									<td style="width: 80%; ">' . $pcemd->empleadoTemporario['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';
			}
			if($pcemd->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pcemd->observaciones['valor'].'</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Vehículos</div>';
			if($pcvs->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pcvs->result->getData() as $pcv) {
					//print_r($pcv);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Vehículo propio # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pcv->idVehiculo['referencia']->getNombreCompleto() . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pcved->esVehiculoDeAlquiler['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Vehículo de alquiler</td>
									<td style="width: 80%; ">' . $pcved->vehiculoDeAlquiler['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';

			}
			if($pcved->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pcved->observaciones['valor'].'</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Equipamientos</div>';
			if($pceqs->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pceqs->result->getData() as $pceq) {
					//print_r($pcv);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Equipamiento # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pceq->idEquipamiento['referencia']->getNombreCompleto() . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pceqd->esEquipamientoDeAlquiler['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Equipamiento de alquiler</td>
									<td style="width: 80%; ">' . $pceqd->equipamientoDeAlquiler['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';

			}
			if($pceqd->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pceqd->observaciones['valor'].'</div>';
			}

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;

			$pageBody = '	<div style="border: 1px; background-color: #ddd; text-align: center;">Planificación de la Comisión</div>';
			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Traslados base</div>';
			if($pcptbs->result->getData()) {
				//print_r($pcptbs->result->getData());die();
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pcptbs->result->getData() as $pcptb) {

					$pcptbpces = new ProyectoComisionPlanificacionTrasladoBaseProyectoComisionEmpleadoVO();
					$data = array();
					$data['nombreCampoWhere'] = 'idProyectoComisionPlanificacionTrasladoBase';
					$data['valorCampoWhere'] = $pcptb->idProyectoComisionPlanificacionTrasladoBase['valor'];
					$pcptbpces->getAllRows($data);
					$empleados = '';
					foreach ($pcptbpces->result->getData() as $pcptbpce) {
						//print_r($pcptbpce); die();
						$empleados .= 'Empleado: '.$pcptbpce->idProyectoComisionEmpleado['referencia']->idEmpleado['referencia']->getNombreCompleto().'<br>';
					}
					//print_r($pcptbs); die();

					//print_r($pcptb);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Traslado Base # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= $empleados;
					$pageBody .= '  		Tipo de Traslado: ' . $pcptb->idTipoTraslado['referencia']->tipoTraslado['valor'];
					$pageBody .= '  		<br>Tipo de Transporte: ' . $pcptb->idTipoTransporte['referencia']->tipoTransporte['valor'];
					if($pcptb->idTipoTransporte['referencia']->idTipoTransporte['valor'] == 6){
						$pageBody .= '		<br>Vehículo: ' . $pcptb->idProyectoComisionVehiculo['referencia']->idVehiculo['referencia']->getNombreCompleto();
						$pageBody .= '		<br>Km a recorrer: ' . $pcptb->km['valor'];
					}
					if($pcptb->idTipoTransporte['referencia']->idTipoTransporte['valor'] == 7){
						$pageBody .= '		<br>Vehículo: ' . $pcptb->idProyectoComisionVehiculoDetalle['referencia']->vehiculoDeAlquiler['valor'];
						$pageBody .= '		<br>Km a recorrer: ' . $pcptb->km['valor'];
					}
					$pageBody .= '	    	<br>Fecha de Arribo: ' . convertDateDbToEs($pcptb->fechaArribo['valor']);
					$pageBody .= '	    	<br>Destino de Salida: ' . $pcptb->idDestinoSalida['referencia']->destino['valor'];
					$pageBody .= '  		<br>Destino de Llegada: ' . $pcptb->idDestinoLlegada['referencia']->destino['valor'];
					if($pcptb->idTipoTraslado['referencia']->idTipoTraslado['valor'] == 5){
						$pageBody .= '  	<br>Fecha de Partida: ' . convertDateDbToEs($pcptb->fechaPartida['valor']);
					}
					$pageBody .= '  		<br>Observaciones: ' . $pcptb->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Traslados operativos</div>';
			if($pcptos->result->getData()) {
				//print_r($pcptos->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcptos->result->getData() as $pcpto) {
					//print_r($pcptb);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Traslado Operativo # '. $count++ .'</td>
										<td style="width: 78%; ">';
					if($pcpto->idTipoBien['referencia']->idTipoBien['valor'] == 1){
						$pageBody .= '  		Vehículo propio: '.$pcpto->idProyectoComisionVehiculo['referencia']->idVehiculo['referencia']->getNombreCompleto();
					}
					if($pcpto->idTipoBien['referencia']->idTipoBien['valor'] == 2){
						$pageBody .= '  		Vehículo de alquiler: '.$pcpto->idProyectoComisionVehiculoDetalle['referencia']->vehiculoDeAlquiler['valor'];
					}
					$pageBody .= '  		<br>Km diarios: ' . $pcpto->kmDiarios['valor'];
					$pageBody .= '  		<br>Cantidad de días: ' . $pcpto->cantidadDias['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpto->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Alojamientos</div>';
			if($pcpalos->result->getData()) {
				//print_r($pcpalos->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcpalos->result->getData() as $pcpalo) {
					//print_r($pcpalo);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Alojamiento # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= '  		Alojamiento: '.$pcpalo->idDestino['referencia']->destino['valor'];
					$pageBody .= '  		<br>Fecha de arribo: ' . convertDateDbToEs($pcpalo->fechaArribo['valor']);
					$pageBody .= '  		<br>Fecha de partida: ' . convertDateDbToEs($pcpalo->fechaPartida['valor']);
					$pageBody .= '  		<br>Cantidad de huéspedes: ' . $pcpalo->huespedes['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpalo->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Comidas</div>';
			if($pcpalis->result->getData()) {
				//print_r($pcpalis->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcpalis->result->getData() as $pcpali) {
					//print_r($pcpali);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Alimentos # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= '  		Alimento: '.$pcpali->idDestino['referencia']->destino['valor'];
					$pageBody .= '  		<br>Cantidad de días: ' . $pcpali->cantidadDias['valor'];
					$pageBody .= '  		<br>Cantidad de empleados: ' . $pcpali->cantidadEmpleados['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpali->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 2
			$pages .= $page;
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return html_entity_decode($pages, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	* devuelve datos de de una comision para una fecha y empleado
	*/
	public function getProyectoComisionParaATD($data){
		try {
			$sql = 'SELECT c.nombreReferencia as contrato, cg.gerencia, cgp.nombreReferencia as proyecto, pc.idProyectoComision, 
						getCodigoProyectoComision(pc.idProyectoComision) as codigoProyectoComision, 
						idServicioActividad, 
						CONCAT_ws(" \\\ ", sada.servicioAreaDeAplicacion, sa.servicioActividad) as servicioActividad, 
						pc.observaciones as observacionesPlanificacion
					from proyectosComisiones as pc
					inner JOIN proyectosComisiones_empleados as pce using (idProyectoComision)
					inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
					inner join contratosGerencias as cg using (idContratoGerencia)
					inner join contratos as c using (idContrato)
					inner join serviciosActividad as sa using(idServicioActividad)
					inner join serviciosAreaDeAplicacion as sada using(idServicioAreaDeAplicacion)
                    where true and idEmpleado = '.$data['idEmpleado'].' and "'.$data['fecha'].'" BETWEEN pc.fechaInicio and pc.fechaFin';
			//die($sql);
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}

	public function getExcelFile($fileName, $data = null){
		$sql = 'SELECT
					pc.idProyectoComision as "Id Proyecto Comisión",
					getContratoGerenciaProyecto(pc.idContratoGerenciaProyecto) as "Contrato/Gerencia/Proyecto",
					getServicioActividad(idServicioActividad) as  "Servicio Actividad",
					pc.fechaInicio as "Fecha Inicio",
					pc.fechaFin as "Fecha Fin",
					contratosGerenciasLocalizaciones.localizacion "Localización",
					provincias.provincia "Provincia",
					getEmpleado(idEmpleado) "Empleados Implicados",
					proyectosComisiones_empleadosDetalles.empleadoTemporario "Empleado Temporario",
					getVehiculo(idVehiculo) "Vehículos Implicados",
					proyectosComisiones_vehiculosDetalles.vehiculoDeAlquiler "Vehículo de Alquiler",
					getEquipamiento(idEquipamiento) "Equipamentos Implicados",
					proyectosComisiones_equipamientosDetalles.equipamientoDeAlquiler "Equipamento de Alquiler"
				FROM
					proyectosComisiones AS pc
				LEFT JOIN contratosGerenciasProyectos ON pc.idContratoGerenciaProyecto = contratosGerenciasProyectos.idContratoGerenciaProyecto
				LEFT JOIN proyectosComisiones_empleados ON proyectosComisiones_empleados.idProyectoComision = pc.idProyectoComision
				LEFT JOIN proyectosComisiones_empleadosDetalles ON proyectosComisiones_empleadosDetalles.idProyectoComision = pc.idProyectoComision
				LEFT JOIN proyectosComisiones_equipamientos ON proyectosComisiones_equipamientos.idProyectoComision = pc.idProyectoComision
				LEFT JOIN proyectosComisiones_equipamientosDetalles ON proyectosComisiones_equipamientosDetalles.idProyectoComision = pc.idProyectoComision
				LEFT JOIN proyectosComisiones_vehiculos ON proyectosComisiones_vehiculos.idProyectoComision = pc.idProyectoComision
				LEFT JOIN proyectosComisiones_vehiculosDetalles ON proyectosComisiones_vehiculosDetalles.idProyectoComision = pc.idProyectoComision
				LEFT JOIN contratosGerenciasLocalizaciones ON pc.idContratoGerenciaLocalizacion = contratosGerenciasLocalizaciones.idContratoGerenciaLocalizacion
				LEFT JOIN provincias ON contratosGerenciasLocalizaciones.idProvincia = provincias.idProvincia
				where true 
				ORDER BY pc.idProyectoComision desc';
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$arrayRows = setHtmlEntityDecode($rs);
				$sheet_name = 'Hoja1';
				$writer = new XLSXWriter();
				foreach ($arrayRows[0] as $key => $value) {
					$header[$key] = 'string';// indica cómo será tomado el campo por el Excel
				}
				$writer->writeSheetHeader($sheet_name, $header);
				foreach ($arrayRows as $row) {
					$writer->writeSheetRow($sheet_name, $row);
				}
				$writer->setAuthor('SIGIweb');
				$fileName = html_entity_decode($fileName, ENT_QUOTES | ENT_IGNORE, "UTF-8")."-".date('Ymd-His')."-all.xlsx";
				header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($fileName).'"');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				$writer->writeToStdOut();
				exit(0);
			}else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("La consulta no retornó registros.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

	}

	/*
	 * Esta func recibe un parametro para el listado asincronico, si tiene datos hace la consulta con la base, si es null trae los parametros y nombres del columnas para el datatable
	 */
	public function getDataTableData($postData, $data = null){
		//fc_print($data, true);
		try{
			$sql = "SELECT
						a.idProyectoComision,
						getCodigoProyectoComision (a.idProyectoComision) AS codigoProyectoComision,
					    c.gerencia,
						l.localizacion,
						sa.servicioActividad,
						saa.servicioAreaDeAplicacion,
						z.nombreReferencia,
						a.fechaInicio,
						DATE_FORMAT(a.fechaInicio,\"%d/%m/%Y\") as fechaInicioES,
						a.fechaFin,
						DATE_FORMAT(a.fechaFin,\"%d/%m/%Y\") as fechaFinES,
						getCodigoContratoGerenciaProyecto (
							a.idContratoGerenciaProyecto
						) AS codigoContratoGerenciaProyecto
					FROM
						proyectosComisiones AS a
					INNER JOIN contratosGerenciasProyectos AS z USING (idContratoGerenciaProyecto)
					INNER JOIN contratosGerencias AS c ON z.idContratoGerencia = c.idContratoGerencia
					INNER JOIN contratosGerenciasLocalizaciones AS l USING (idContratoGerenciaLocalizacion)
					INNER JOIN serviciosActividad AS sa USING (idServicioActividad)
					INNER JOIN serviciosAreaDeAplicacion AS saa USING (idServicioAreaDeAplicacion)
					WHERE
						TRUE";
			if ($data['nombreCampoWhere'] && $data['valorCampoWhere']) {
				$sql .= " and c." . $data['nombreCampoWhere'] . " = " . $data['valorCampoWhere'];
			}
			//die($sql);
			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				foreach ($dataSql['data'] as $row) {
					$auxRow['idProyectoComision'] = $row['idProyectoComision'];
					$auxRow['codigoProyectoComision'] = $row['codigoProyectoComision'];
					$auxRow['gerencia'] = $row['gerencia'];
					$auxRow['localizacion'] = $row['localizacion'];
					$auxRow['nombreReferencia'] = $row['nombreReferencia'];
					$auxRow['servicioActividad'] = $row['servicioActividad']." \ ".$row['servicioAreaDeAplicacion'];
					$auxRow['servicioAreaDeAplicacion'] = $row['servicioAreaDeAplicacion'];
					$auxRow['fechaInicio'] = $row['fechaInicio'];
					$auxRow['fechaInicioES'] = $row['fechaInicioES'];
					$auxRow['fechaFin'] = $row['fechaFin'];
					$auxRow['fechaFinES'] = $row['fechaFinES'];
					$auxRow['codigoContratoGerenciaProyecto'] = $row['codigoContratoGerenciaProyecto'];
					$auxRow['accion'] = '
								<a class="text-black" href="../pdfs/ProyectoComisionPDF.php?'.codificarGets('id='.$row['idProyectoComision'].'&action=pdf'). '" target="_blank" title="Guía de Ruta en PDF"><span class="fa fa-file-pdf-o fa-lg"></span></a>&nbsp;&nbsp;
								<a class="text-black"  href= "../pages/ABMproyectoComision.php?' . codificarGets('id=' . $row['idProyectoComision'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
                                <a class="text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idProyectoComision'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Borrar"><span class="fa fa-trash-o fa-lg"></span></a>';
					$auxData[] = $auxRow;
				}
				$dataSql['data'] = $auxData;
			}
			echo json_encode($dataSql);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}
	public function getDataTableProperties(){

		$objectPropierties = null;   //Nombre: Indica nombre de los campos que se mostraran en el excel y el Datatable
		$objectPropierties[] = array('nombre' => 'idProyectoComision' ,              'dbFieldName' => 'idProyectoComision',            'visibleDT' => false, 'visibleDTexport' => false,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false);   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'código de comisión' ,              'dbFieldName' => 'codigoProyectoComision',        'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'gerencia' ,                        'dbFieldName' => 'gerencia',                      'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'localización' ,                    'dbFieldName' => 'localizacion',                  'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'proyecto',                         'dbFieldName' => 'nombreReferencia',              'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'Área De Aplicación \ actividad',   'dbFieldName' => 'servicioActividad',             'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'servicioAreaDeAplicacion',         'dbFieldName' => 'servicioAreaDeAplicacion',      'visibleDT' => false, 'visibleDTexport' => false,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fechaInicioEN',                    'dbFieldName' => 'fechaInicio',                   'visibleDT' => false, 'visibleDTexport' => false,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fecha Inicio' ,                    'dbFieldName' => 'fechaInicioES',                 'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => 'fechaInicioEN','bSortable' => true,    'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fechaFinEN',                       'dbFieldName' => 'fechaFin',                      'visibleDT' => false, 'visibleDTexport' => false,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fecha Fin',                        'dbFieldName' => 'fechaFinES',                    'visibleDT' => true,  'visibleDTexport' => true,  'className' => false,       'aDataSort' => 'fechaFinEN',  'bSortable' => true,     'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'codigoContratoGerenciaProyecto',   'dbFieldName' => 'codigoContratoGerenciaProyecto','visibleDT' => false, 'visibleDTexport' => true,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'opciones',                         'dbFieldName' => 'accion',                        'visibleDT' => true,  'visibleDTexport' => false,  'className' => 'center',    'aDataSort' => false,         'bSortable' => false,    'searchable' => false);

		$listadoDatosCargados['columnas'] = $objectPropierties;

		foreach ($listadoDatosCargados['columnas'] as $campo) {
			$campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
			$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
		}
		for ($i = 0; $i < count($listadoDatosCargados['columnas']); $i++) {
			if ($listadoDatosCargados['columnas'][$i]['visibleDT'] == false) {//Se carga un array con los campos que nos seran visibles
				$bVisible[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['bSortable'] == false) {//Se carga un array con los campos que nos seran ordebables
				$bSortable[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['searchable'] == false) {//Se carga un array con los campos que no seran searcheables
				$searchable[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'left') {//Se carga un array con los campos que se ordenan a la izquierda
				$lefts[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'center') {//Se carga un array con los campos que se ordenan al centro
				$centers[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'right') {//Se carga un array con los campos que se ordenan a la derecha
				$rights[] = $i;
			}
			if (is_array($listadoDatosCargados['columnas'][$i]['aaSorting'])) {
				$orden = $listadoDatosCargados['columnas'][$i]['aaSorting'][0];
				$criterio[$orden] = $listadoDatosCargados['columnas'][$i]['aaSorting'][1];
				$col[$orden] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['visibleDTexport'] == true) {
				$exportables[] = $i;
			}

			if ($listadoDatosCargados['columnas'][$i]['aDataSort'] != false) {
				foreach ($campos as $campo) {
					if (strtolower($listadoDatosCargados['columnas'][$i]['aDataSort']) == strtolower($campo)) {
						$aDataSorts[] = '{"aDataSort":[' . array_search($campo, $campos) . '], "aTargets": [' . $i . ']}';// en cada elemento del array aDataSorts hay un string con la estructura del aDataSort para agregar a las propiedades del datatable
					}
				}
			}
		}

		if (is_array($bVisible)) {
			$bVisible = implode(', ', $bVisible);
		}
		if (is_array($bSortable)) {
			$bSortable = implode(', ', $bSortable);
		}
		if (is_array($searchable)) {
			$searchable = implode(', ', $searchable);
		}
		if (is_array($lefts)) {
			$lefts = implode(', ', $lefts);
		}
		if (is_array($centers)) {
			$centers = implode(', ', $centers);
		}
		if (is_array($rights)) {
			$rights = implode(', ', $rights);
		}
		if (is_array($exportables)) {
			$exportables = implode(', ', $exportables);
		}
		//Se pasan todos los arrays creados a la variable de retorno
		$listadoDatosCargados['campos'] = $campos;//Estos seran los nombres de las columnas

		$parametros['aDataSorts']  = $aDataSorts;
		$parametros['bVisible']    = '{"bVisible": false, "aTargets":[' . $bVisible . ']},';
		$parametros['bSortable']   = '{"bSortable": false, "aTargets":[' . $bSortable . ']},';
		$parametros['searchable']  = '{"searchable" : false, "aTargets":[' . $searchable . ']},';
		$parametros['lefts']       = '{"className": "dt-left", "aTargets":[' . $lefts . ']},';
		$parametros['centers']     = '{"className": "dt-center", "aTargets":[' . $centers . ']},';
		$parametros['rights']      = '{"className": "dt-right", "aTargets":[' . $rights . ']},';

		$listadoDatosCargados['exportables'] = '[' . $exportables . ']';
		//COLUMNDEFS
		$aoColumnDefs = '"aoColumnDefs": [';

		(isset($parametros['bVisible']))? $aoColumnDefs .= $parametros['bVisible'] : '';
		(isset($parametros['bSortable']))? $aoColumnDefs .= $parametros['bSortable'] : '';
		(isset($parametros['searchable']))? $aoColumnDefs .= $parametros['searchable'] : '';
		(isset($parametros['lefts']))? $aoColumnDefs .= $parametros['lefts']: '';
		(isset($parametros['centers']))? $aoColumnDefs .= $parametros['centers'] : '';
		(isset($parametros['rights']))? $aoColumnDefs .= $parametros['rights'] : '';
		(is_array($parametros['aDataSorts']))? $aoColumnDefs .= implode(',', $parametros['aDataSorts']) : '';//Importante que los DataSort esten al final
		$aoColumnDefs .= '],';
		//COLUMNDEFS

		//AASORTING
		$aaSorting = '"aaSorting": [[1,"desc"]],';
		//AASORTING

		//COLUMN NAMES
		$columns = '"columns": [';
		foreach ($dbFieldNames as $name) {
			$columns .= '{"data": "'.$name.'"},';
		}
		$columns = rtrim($columns,',');
		$columns .= '],';
		//COLUMN NAMES

		$listadoDatosCargados['columns'] = $columns;
		$listadoDatosCargados['aaSorting'] = $aaSorting;
		$listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

		return $listadoDatosCargados;// Retorna campos,columns, aoColumnDefs y aaSorting
	}

	public function getProyectoComisiones($data, $format = null){
		//print_r($data);
		$sql = 'select getCodigoProyectoComision(idProyectoComision) as label, idProyectoComision as data 
				from proyectosComisiones as a
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getProyectoComisionPorIdProyectoComisionEmpleado'){
	$aux = new ProyectoComisionVO();
	$data = array();
	$data['idProyectoComisionEmpleado'] = $_GET['idProyectoComisionEmpleado'];
	$aux->{$_GET['type']}($data);
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getProyectoComisionParaATD'){
	$aux = new ProyectoComisionVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		$data = array();
		$data['fecha'] = convertDateEsToDb($_GET['fecha']);
		$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		$aux->{$_GET['type']}($data);
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getInfoProyectoComisionParaATR'){
	$aux = new ProyectoComisionVO();
	$data = array();
	$data['idProyectoComision'] = $_GET['idProyectoComision'];
	$aux->{$_GET['type']}($data);
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getInfoProyectoComisionParaMovimientoEquipamiento'){
	$aux = new ProyectoComisionVO();
	$data = array();
	$data['nroProyectoComision'] = substr($_GET['nroProyectoComision'], -4) + 0;
	$aux->{$_GET['type']}($data);
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ListadoProyectoComisiones.php')){
	$aux = new ProyectoComisionVO();
	if($_SESSION['selectorContratoIdContrato']) {
		if (empty($_POST) ) {
			$aux->getDataTableProperties();
		} else {
			$data = array();
			$data['nombreCampoWhere'] = 'idContrato';
			$data['valorCampoWhere'] = $_SESSION['selectorContratoIdContrato'];
			$aux->getDataTableData($_POST, $data);
		}
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getProyectoComisiones'){
	$aux = new ProyectoComisionVO();
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->{$_GET['type']}($data, $_GET['action']);
}

// debug zone
if($_GET['debug'] == 'ProyectoComisionVO' or false){
	echo "DEBUG<br>";
	$kk = new ProyectoComisionVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoComision = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
