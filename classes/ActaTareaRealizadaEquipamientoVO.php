<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaRealizadaEquipamientoVO extends Master2 {
	public $idActaTareaRealizadaEquipamiento = ["valor" => "",
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
	public $idEquipamiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "equipamiento",
		"referencia" => "",
	];

	public function __construct(){
		parent::__construct();
		$this->result = new Result();
		$this->setTableName('actaTareasRealizada_equipamientos');
		$this->setFieldIdName('idActaTareaRealizadaEquipamiento');
		$this->idActaTareaRealizada['referencia'] =  new ActaTareaRealizadaVO();
		$this->idEquipamiento['referencia'] =  new EquipamientoVO();
	}

	/*
	 * Funcion que valida cierta logica de negocios
	 */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}

	public function getComboList($data = null){
		$sql = 'select getEquipamiento(a.idEquipamiento) as label, a.idEquipamiento as data, b.idActaTareaRealizada as selected
				from equipamientos as a
				left JOIN actaTareasRealizada_equipamientos as b on a.idEquipamiento = b.idEquipamiento ';
		if($data['valorCampoWhere'])
			$sql .= ' and b.'.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		else
			$sql .= ' and b.'.$data['nombreCampoWhere'].' is null ';
		$sql .= ' group by data ';
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

	/*public function getEquipamientosPor($data = null){
		$sql = 'select getEquipamiento(e.idEquipamiento) as value, a.idEquipamiento as id, b.idActaTareaRealizadaEquipamiento as selected
				from proyectosComisiones_equipamientos as a
				left JOIN actaTareasRealizada_proyectosComisionesEquipamientos as b on a.idEquipamiento = b.idEquipamiento  ';
		$sql .= ' inner join equipamientos as e using (idEquipamiento)
		         where id = '.$data['id'].'
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
/*if($_GET['action'] == 'json' && $_GET['type'] == 'getEquipamientosPor'){
	$aux = new ActaTareaRealizadaEquipamientoVO();
	$data = array();
	$data['id'] = $_GET['id'];
	$aux->{$_GET['type']}($data);
}*/

// debug zone
if($_GET['debug'] == 'ActaTareaRealizadaEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new ActaTareaRealizadaEquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoUnidadEconomica = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
