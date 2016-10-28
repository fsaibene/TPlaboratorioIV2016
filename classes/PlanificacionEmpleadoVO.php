<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanificacionEmpleadoVO extends Master2 {
    public $idPlanificacionEmpleado = ["valor" => "",
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
    public $idEmpleado = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "empleado",
        "referencia" => "",
    ];

    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('planificaciones_empleados');
        $this->setFieldIdName('idPlanificacionEmpleado');
        $this->idPlanificacion['referencia'] =  new PlanificacionVO();
        $this->idEmpleado['referencia'] =  new EmpleadoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

	public function getComboList($data = null){
		$sql = 'select CONCAT_ws(", ", a.apellido,a.nombres) as label, a.idEmpleado as data, b.idPlanificacion as selected
				from empleados as a
				left JOIN planificaciones_empleados as b on a.idEmpleado = b.idEmpleado ';
		if($data['valorCampoWhere'])
			$sql .= ' and b.'.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		else
			$sql .= ' and b.'.$data['nombreCampoWhere'].' is null ';
		$sql .= ' left join empleadosRelacionLaboral as erl on a.idEmpleado = erl.idEmpleado
		         where true and erl.fechaEgreso is null
		         group by data ';
		$sql .= ' order by label';
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

	

	/*public function getPlanificacionEmpleado(){
		$sql = 'select a.idEmpleado as value, CONCAT_WS(", ", apellido, nombres) as empleado, idPlanificacion
				from empleados as a
				left join planificaciones_empleados as b on a.idEmpleado = b.idEmpleado ';
		if($this->idPlanificacion['valor']){
			$sql .= ' and b.idPlanificacion = '.$this->idPlanificacion['valor'];
		}
		$sql .= ' where true
				  group by a.idEmpleado ';
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
	}*/
}
/*if($_GET['action'] == 'json' && $_GET['type'] == 'getPlanificacionEmpleado'){
	$aux = new PlanificacionEmpleadoVO();
	//$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->idPlanificacion['valor'] = $_GET['idPlanificacion'];
	$aux->getPlanificacionEmpleado();
}*/

// debug zone
if($_GET['debug'] == 'PlanificacionEmpleadoVO' or false){
    echo "DEBUG<br>";
    $kk = new PlanificacionVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
