<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaRealizadaEmpleadoVO extends Master2 {
	public $idActaTareaRealizadaEmpleado = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "ID",
	];
	public $idActaTareaRealizada = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "ATR",
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
		$this->setTableName('actaTareasRealizada_empleados');
		$this->setFieldIdName('idActaTareaRealizadaEmpleado');
		$this->idActaTareaRealizada['referencia'] =  new ActaTareaRealizadaVO();
		$this->idEmpleado['referencia'] =  new EmpleadoVO();
	}

	/*
	 * Funcion que valida cierta logica de negocios
	 */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}

	public function getComboList($data = null){
		$sql = 'select getEmpleado(a.idEmpleado) as label, a.idEmpleado as data, b.idActaTareaRealizada as selected
				from empleados as a
				left JOIN actaTareasRealizada_empleados as b on a.idEmpleado = b.idEmpleado ';
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
	/*public function getEmpleadosPorProyectoComision($data = null){
		$sql = 'select getEmpleado(e.idEmpleado) as value, a.idEmpleado as id, b.idActaTareaRealizadaEmpleado as selected
				from empleados as a
				left JOIN actaTareasRealizada_empleados as b on a.idEmpleado = b.idEmpleado ';
		$sql .= ' inner join empleados as e using (idEmpleado)
		         where idProyectoComision = '.$data['idProyectoComision'].'
				group by id
				order by value';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}*/
}
/*if($_GET['action'] == 'json' && $_GET['type'] == 'getEmpleadosPorProyectoComision'){
	$aux = new ActaTareaRealizadaEmpleadoVO();
	$data = array();
	$data['idProyectoComision'] = $_GET['idProyectoComision'];
	$aux->{$_GET['type']}($data);
}*/

// debug zone
if($_GET['debug'] == 'ActaTareaRealizadaEmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new ActaTareaRealizadaVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoUnidadEconomica = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
