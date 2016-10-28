<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanificacionProyectoExternoVO extends Master2 {
    public $idPlanificacionProyectoExterno = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idPlanificacion = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "planificación",
        "referencia" => "",
    ];
    public $idContratoGerenciaProyecto = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "gerencia \ proyecto",
        "referencia" => "",
    ];
    public $idServicioTarea = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "Área de aplicación \ Actividad \ Tarea",
        "referencia" => "",
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
   
    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('planificaciones_proyectosExternos');
        $this->setFieldIdName('idPlanificacionProyectoExterno');
        $this->idPlanificacion['referencia'] =  new PlanificacionVO();
        $this->idContratoGerenciaProyecto['referencia'] =  new ContratoGerenciaProyectoVO();
        $this->idServicioTarea['referencia'] =  new ServicioTareaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

	/*
	 * devuelve un array con la info para ser mostrada luego en un calendario
	 */
	public function getPlanificacionProyectoExternoParaCalendario($data){
		//print_r($data);
		$sql = 'select 
					concat("planificacion-", idPlanificacion) as id, 
					color, 
					title, 
					p.fechaDesde as start, 
					DATE_ADD(p.fechaHasta, interval 1 day) as end, 
					true as allDay, 
					if(p.observaciones is null, description, CONCAT_WS("<br>", description, p.observaciones)) as description';
		if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
			$sql .= ' , null as urlAddress, null urlParamaters, null as url ';
		}
		$sql .= ' from planificaciones as p
					inner join (
						SELECT ppe.idPlanificacion, "#f39c12" as color
						, cg.gerencia as title
						, concat_ws("<br>" 
							, getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto)
							, concat("Proyecto: ", cgp.nombreReferencia)
							, concat("Tarea: ", CONCAT_ws(" \\\ ", sada.servicioAreaDeAplicacion, sa.servicioActividad, st.servicioTarea))
							, concat("Integrantes: ", pe2.integrantes)
							, if(ppe.observaciones is null, "", concat("Obs: ", ppe.observaciones))
						) as description
						from planificaciones_proyectosExternos as ppe 
						left join planificaciones_empleados as pe using (idPlanificacion)
						inner join serviciosTarea as st using (idServicioTarea)
						inner join serviciosActividad as sa using (idServicioActividad)
						inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
						inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
						inner join contratosGerencias as cg using (idContratoGerencia)
						left join (
							select idPlanificacion, GROUP_CONCAT(getEmpleado(idEmpleado) SEPARATOR " - ") as integrantes
							from planificaciones_empleados as pe
							inner join empleados as e using (idEmpleado)
							group by idPlanificacion
						) as pe2 using (idPlanificacion)
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
		if($data['idServicioTarea']){
			$sql .= ' and st.idServicioTarea = '.$data['idServicioTarea'];
		}
		if($data['idServicioActividad']){
			$sql .= ' and sa.idServicioActividad = '.$data['idServicioActividad'];
		}
		if($data['idServicioAreaDeAplicacion']){
			$sql .= ' and sada.idServicioAreaDeAplicacion = '.$data['idServicioAreaDeAplicacion'];
		}
		if($data['idEmpleadoArray'][0]){
			$sql .= ' and pe.idEmpleado in ('.implode(",", $data['idEmpleadoArray']).')';
		}
		$sql .= ' ) as pp USING (idPlanificacion)
		                where true 
		                ';
		$sql .= ' and ((p.fechaDesde BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (p.fechaHasta BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (p.fechaDesde < "'.convertDateEsToDb($data['start']).'" and p.fechaHasta > "'.convertDateEsToDb($data['end']).'")) ';
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

    /*
	* devuelve datos de de una planificacion para una fecha y empleado
	*/
    public function getPlanificacionProyectoExternoParaATD($data){
        try {
            $sql = 'SELECT c.nombreReferencia as contrato, cg.gerencia, cgp.nombreReferencia as proyecto, CONCAT_ws(" \\\ ", sada.servicioAreaDeAplicacion, sa.servicioActividad, st.servicioTarea) as servicioTarea, ppe.observaciones as observacionesPlanificacion
                      , ppe.idContratoGerenciaProyecto, st.idServicioTarea, idPlanificacionProyectoExterno
                    from planificaciones as p
                    inner JOIN planificaciones_empleados as pe using (idPlanificacion)
                    inner join planificaciones_proyectosExternos as ppe using (idPlanificacion)
                    inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
                    inner join serviciosTarea as st using(idServicioTarea)
                    inner join serviciosActividad as sa using(idServicioActividad)
                    inner join serviciosAreaDeAplicacion as sada using(idServicioAreaDeAplicacion)
                    inner join contratosGerencias as cg using (idContratoGerencia)
                    inner join contratos as c using (idContrato)
                    where true and idEmpleado = '.$data['idEmpleado'].' and "'.$data['fecha'].'" BETWEEN fechaDesde and fechaHasta';
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
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getPlanificacionProyectoExternoParaATD'){
    $aux = new PlanificacionProyectoExternoVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		$data['fecha'] = convertDateEsToDb($_GET['fecha']);
		$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		$aux->{$_GET['type']}($data);
	}
}

// debug zone
if($_GET['debug'] == 'PlanificacionProyectoExternoVO' or false){
    echo "DEBUG<br>";
    $kk = new PlanificacionProyectoExternoVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
