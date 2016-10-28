<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanificacionVO extends Master2 {
    public $idPlanificacion = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
	public $fechaDesde = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha desde",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $fechaHasta= ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha hasta",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];

	public $planificacionEmpleadoArray = [
		'tipo' => 'comboMultiple',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'PlanificacionEmpleadoVO', // es el nombre de la clase a la que hace referencia el array
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idEmpleado', // es el campo por el que se filtra... Ver funcion deleteDataArray
		'filterGroupKeyName' => 'idPlanificacion'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $planificacionProyectoExternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'PlanificacionProyectoExternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppe', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idPlanificacionProyectoExterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idPlanificacion'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $planificacionProyectoInternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'PlanificacionProyectoInternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppi', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idPlanificacionProyectoInterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idPlanificacion'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('planificaciones');
	    $this->setFieldIdName('idPlanificacion');
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
	    if (strtotime(convertDateEsToDb($this->fechaDesde['valor'])) > strtotime(convertDateEsToDb($this->fechaHasta['valor'])) ) {
		    $resultMessage = 'La fecha Hasta no puede ser menor que la fecha Desde.';
	    }
        return $resultMessage;
    }

	public function getAllRows2($data = null){
		$sql = 'select p.*
					, pe.pes
					, ppe.ppes
					, ppi.ppis
				from planificaciones as p
				left join (
					select idPlanificacion, GROUP_CONCAT(getEmpleado(e.idEmpleado) SEPARATOR " - ") as pes
					from planificaciones_empleados as pe
				  	inner join empleados as e using (idEmpleado)
					group by idPlanificacion
				) as pe using (idPlanificacion)
				left join (
					select idPlanificacion, GROUP_CONCAT(cgp.nombreReferencia SEPARATOR " - ") as ppes
					from planificaciones_proyectosExternos as ppe
				  	inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
					group by idPlanificacion
				) as ppe using (idPlanificacion)
				left join (
					select idPlanificacion, GROUP_CONCAT(sa.laborActividad SEPARATOR " - ") as ppis
					from planificaciones_proyectosInternos as ppi
				  	inner join laboresActividad as sa using (idLaborActividad)
					group by idPlanificacion
				) as ppi using (idPlanificacion)
				group by idPlanificacion';
		//die($sql);
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

	public function getPlanificacionParaCalendarioGerencial($data){
		//print_r($data);
		try{
			$aux2 = array();
			if($data['tipoPlanificacion'] == '' || $data['tipoPlanificacion'] == 'com') {
				$aux = new ProyectoComisionVO();
				$aux->getProyectosComisionesParaCalendario($data);
				if($aux->result->getStatus() != STATUS_OK){
					$this->result = $aux->result;
					return $this;
				}
				$aux2 = array_merge($aux2, $aux->result->getData());
			}

			if($data['tipoPlanificacion'] == '' || $data['tipoPlanificacion'] == 'ppe') {
				$aux = new PlanificacionProyectoExternoVO();
				$aux->getPlanificacionProyectoExternoParaCalendario($data);
				if ($aux->result->getStatus() != STATUS_OK) {
					$this->result = $aux->result;
					return $this;
				}
				$aux2 = array_merge($aux2, $aux->result->getData());
			}

			if($data['tipoPlanificacion'] == '' || $data['tipoPlanificacion'] == 'ppi') {
				$aux = new PlanificacionProyectoInternoVO();
				$aux->getPlanificacionProyectoInternoParaCalendario($data);
				if ($aux->result->getStatus() != STATUS_OK) {
					$this->result = $aux->result;
					return $this;
				}
				$aux2 = array_merge($aux2, $aux->result->getData());
			}

			if($data['tipoPlanificacion'] == '' || $data['tipoPlanificacion'] == 'via') {
				$aux = new ViajeVO();
				$aux->getViajesParaCalendario($data);
				if ($aux->result->getStatus() != STATUS_OK) {
					$this->result = $aux->result;
					return $this;
				}
				$aux2 = array_merge($aux2, $aux->result->getData());
			}

			echo json_encode($aux2);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}


	public function getPlanificacionParaGanttGerencial($data){
		//fc_print($data, true);
		try{
			if($data['mod'] == 'resources') {
				//fc_print($data, true);
				$sql = 'select CONCAT_WS("_", "a", c.idContrato) as id, c.idContrato, c.nombreReferencia as title
							, null as eventColor, null as children
						from contratos as c
						inner join contratosGerencias as cg using (idContrato)
						inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
						inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
						where true';
				$sql .= ' group by id';
				//die($sql);
				//echo $sql;
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				if ($rs) {
					foreach ($rs as &$aux1) {
						$sql = 'select CONCAT_WS("_", "b", cg.idContratoGerencia) as id, cg.idContratoGerencia, cg.gerencia as title
									, null as eventColor, null as children
								from contratos as c
								inner join contratosGerencias as cg using (idContrato)
								inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
								inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
								where c.idContrato = ' . $aux1['idContrato'];
						$sql .= ' group by id';
						//die($sql);
						//echo $sql;
						$ro = $this->conn->prepare($sql);
						$ro->execute();
						$rs2 = $ro->fetchAll(PDO::FETCH_ASSOC);
						if ($rs2) {
							$aux1['children'] = $rs2;
							foreach ($aux1['children'] as &$aux2) {
								$sql = 'select CONCAT_WS("_", "c", cgp.idContratoGerenciaProyecto) as id, cgp.nombreReferencia as title
											, null as eventColor, null as children
										from contratos as c
										inner join contratosGerencias as cg using (idContrato)
										inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
										inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
										where cg.idContratoGerencia = ' . $aux2['idContratoGerencia'];
								$sql .= ' group by id';
								//die($sql);
								//echo $sql;
								$ro = $this->conn->prepare($sql);
								$ro->execute();
								$rs3 = $ro->fetchAll(PDO::FETCH_ASSOC);
								if ($rs3) {
									$aux2['children'] = $rs3;
								}
							}
						}
					}
				}
			} else if($data['mod'] == 'events') {
				$sql = 'select CONCAT_WS("_", "d", pc.idProyectoComision) as id, getCodigoProyectoComision(pc.idProyectoComision) as title
						, CONCAT_WS("_", "c", cgp.idContratoGerenciaProyecto )as resourceId, null as backgroundColor
						, pc.fechaInicio as start, DATE_ADD(pc.fechaFin, interval 1 day) as end
						, concat_ws("<br>"
								, getCodigoProyectoComision(idProyectoComision)
								, concat("Localización: ", cgl.localizacion, " (", p.provincia, ")")
								, concat("Proyecto: ", cgp.nombreReferencia)
								, concat("Actividad: ", sada.servicioAreaDeAplicacion, " - ", sa.servicioActividad)
								, concat("Integrantes: ", pce2.integrantes)
								, concat("Equipamientos: ", pceq2.equipamientos)
								, concat("Vehículos: ", pcv2.vehiculos)
								, if(pc.observaciones is null, "", concat("Obs: ", pc.observaciones))
								) as description  ';
				if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
					$sql .= ' , "ProyectoComisionPDF.php?" as urlAddress, concat("id=", idProyectoComision) as urlParamaters, null as url ';
				}
				$sql .= 'from contratos as c
						inner join contratosGerencias as cg using (idContrato)
						inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
						inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
						inner join contratosGerenciasLocalizaciones as cgl using (idContratoGerenciaLocalizacion)
						inner join provincias as p using (idProvincia)
						inner join serviciosActividad as sa using (idServicioActividad)
						inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
						left join proyectosComisiones_empleados as pce using (idProyectoComision)
						left join proyectosComisiones_equipamientos as pceq using (idProyectoComision)
						left join proyectosComisiones_vehiculos as pcv using (idProyectoComision)
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
						where true';
				if($data['idContrato']){
					$sql .= ' and c.idContrato = '.$data['idContrato'];
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
				$sql .= ' group by id';
				//die($sql);
				//echo $sql;
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rs as &$aux){
					if($aux['urlAddress']){
						$aux['url'] = $aux['urlAddress'] . codificarGets($aux['urlParamaters']);
						$aux['urlParamaters'] = null;
					}
				}
			}
			//fc_print($rs, true);
			echo json_encode(setHtmlEntityDecode($rs));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getPlanificacionParaCalendario'){
	$aux = new PlanificacionVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		$data['start'] = $_GET['start'];
		$data['end'] = $_GET['end'];
		$data['idEmpleadoArray'][] = $_SESSION['usuarioLogueadoIdEmpleado'];
		//$data['print'] = $_GET['print'];
		$aux->getPlanificacionParaCalendarioGerencial($data);
	}
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getPlanificacionParaGanttGerencial'){
	$aux = new PlanificacionVO();
	$data['idContrato'] = ($_POST['idContrato'] != '__jc__')? $_POST['idContrato'] : '';
	$data['idContratoGerencia'] = ($_POST['idContratoGerencia'] != '__jc__')? $_POST['idContratoGerencia'] : '';
	$data['idContratoGerenciaProyecto'] = ($_POST['idContratoGerenciaProyecto'] != '__jc__')? $_POST['idContratoGerenciaProyecto'] : '';
	$data['idServicioAreaDeAplicacion'] = ($_POST['idServicioAreaDeAplicacion'] != '__jc__')? $_POST['idServicioAreaDeAplicacion'] : '';
	$data['idServicioActividad'] = ($_POST['idServicioActividad'] != '__jc__')? $_POST['idServicioActividad'] : '';
	$data['idServicioTarea'] = ($_POST['idServicioTarea'] != '__jc__')? $_POST['idServicioTarea'] : '';
	$data['idEmpleadoArray'] = $_POST['idEmpleadoArray'];
	$data['idEquipamientoArray'] = $_POST['idEquipamientoArray'];
	$data['idVehiculoArray'] = $_POST['idVehiculoArray'];
	//$data['tipoPlanificacion'] = $_POST['tipoPlanificacion'];
	$data['start'] = $_POST['start'];
	$data['end'] = $_POST['end'];
	$data['mod'] = $_POST['mod'];
	//print_r($data); die();
	$aux->{$_POST['type']}($data);
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getPlanificacionParaCalendarioGerencial'){
	//print_r($_POST); die();
	$aux = new PlanificacionVO();
	$data['idContrato'] = ($_POST['idContrato'] != '__jc__')? $_POST['idContrato'] : '';
	$data['idContratoGerencia'] = ($_POST['idContratoGerencia'] != '__jc__')? $_POST['idContratoGerencia'] : '';
	$data['idContratoGerenciaProyecto'] = ($_POST['idContratoGerenciaProyecto'] != '__jc__')? $_POST['idContratoGerenciaProyecto'] : '';
	$data['idServicioAreaDeAplicacion'] = ($_POST['idServicioAreaDeAplicacion'] != '__jc__')? $_POST['idServicioAreaDeAplicacion'] : '';
	$data['idServicioActividad'] = ($_POST['idServicioActividad'] != '__jc__')? $_POST['idServicioActividad'] : '';
	$data['idServicioTarea'] = ($_POST['idServicioTarea'] != '__jc__')? $_POST['idServicioTarea'] : '';
	$data['tipoPlanificacion'] = $_POST['tipoPlanificacion'];
	$data['idEmpleadoArray'] = $_POST['idEmpleadoArray'];
	$data['idEquipamientoArray'] = $_POST['idEquipamientoArray'];
	$data['idVehiculoArray'] = $_POST['idVehiculoArray'];
	$data['start'] = $_POST['start'];
	$data['end'] = $_POST['end'];
	//print_r($data); die();
	$aux->{$_POST['type']}($data);
}

// debug zone
if($_GET['debug'] == 'PlanificacionVO' or false){
    echo "DEBUG<br>";
    $kk = new PlanificacionVO();
	//print_r($kk->getAtributosPermitidos());
    //print_r($kk->getAllRows());
    //$kk->idProyectoUnidadEconomica = 116;
    //$kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
