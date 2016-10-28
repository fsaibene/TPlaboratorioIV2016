<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanificacionProyectoInternoVO extends Master2 {
    public $idPlanificacionProyectoInterno = ["valor" => "",
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
    public $idLaborActividad = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "labor tarea",
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
        $this->setTableName('planificaciones_proyectosInternos');
        $this->setFieldIdName('idPlanificacionProyectoInterno');
        $this->idPlanificacion['referencia'] =  new PlanificacionVO();
        $this->idLaborActividad['referencia'] =  new LaborActividadVO();
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
	public function getPlanificacionProyectoInternoParaCalendario($data){
		//print_r($data);
		$sql = 'select 
					concat("planificacion-", idPlanificacion) as id
					, color
					, title
					, p.fechaDesde as start
					, DATE_ADD(p.fechaHasta, interval 1 day) as end
					, true as allDay, 
					if(p.observaciones is null, description, CONCAT_WS("<br>", description, p.observaciones)) as description';
		if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
			$sql .= ' , null as urlAddress, null urlParamaters, null as url ';
		}
		$sql .= ' from planificaciones as p
					inner join (
						SELECT ppi.idPlanificacion, "#00c0ef" as color
						, sa.laborActividad as title
						, concat_ws("<br>" 
							, concat("Actividad: ", CONCAT_ws(" \\\ ", sada.laborAreaDeAplicacion, sa.laborActividad))
							, concat("Integrantes: ", pe2.integrantes)
							, if(ppi.observaciones is null, "", concat("Obs: ", ppi.observaciones))
						) as description
						from planificaciones_proyectosInternos as ppi
						left join planificaciones_empleados as pe using (idPlanificacion)
						inner join laboresActividad as sa using (idLaborActividad)
						inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
						left join (
							select idPlanificacion, GROUP_CONCAT(getEmpleado(idEmpleado) SEPARATOR " - ") as integrantes
							from planificaciones_empleados as pe
							inner join empleados as e using (idEmpleado)
							group by idPlanificacion
						) as pe2 using (idPlanificacion)
						where true ';
		if($data['idLaborActividad']){
			$sql .= ' and sa.idLaborActividad = '.$data['idLaborActividad'];
		}
		if($data['idLaborAreaDeAplicacion']){
			$sql .= ' and sada.idLaborAreaDeAplicacion = '.$data['idLaborAreaDeAplicacion'];
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
    public function getPlanificacionProyectoInternoParaATD($data){
        try {
            $sql = 'SELECT CONCAT_ws(" \\\ ", sada.laborAreaDeAplicacion, sa.laborActividad) as laborActividad, ppi.observaciones as observacionesPlanificacion
                    , ppi.idLaborActividad, idPlanificacionProyectoInterno
                    from planificaciones as p
                    inner JOIN planificaciones_empleados as pe using (idPlanificacion)
                    inner join planificaciones_proyectosInternos as ppi using (idPlanificacion)
                    inner join laboresActividad as sa using(idLaborActividad)
                    inner join laboresAreaDeAplicacion as sada using(idLaborAreaDeAplicacion)
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getPlanificacionProyectoInternoParaATD'){
    $aux = new PlanificacionProyectoInternoVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']){
	    $data['fecha'] = convertDateEsToDb($_GET['fecha']);
	    $data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	    $aux->{$_GET['type']}($data);
	}
}
// debug zone
if($_GET['debug'] == 'PlanificacionProyectoInternoVO' or false){
    echo "DEBUG<br>";
    $kk = new PlanificacionProyectoInternoVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
