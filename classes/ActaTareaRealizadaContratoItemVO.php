<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaRealizadaContratoItemVO extends Master2 {
	public $idActaTareaRealizadaContratoItem = ["valor" => "",
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
	public $idContratoItem = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Contrato ítem",
		"referencia" => "",
	];
	public $cantidad = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "cantidad",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct(){
		parent::__construct();
		$this->result = new Result();
		$this->setTableName('actaTareasRealizada_contratosItems');
		$this->setFieldIdName('idActaTareaRealizadaContratoItem');
		$this->idActaTareaRealizada['referencia'] =  new ActaTareaRealizadaVO();
		$this->idContratoItem['referencia'] =  new ContratoItemVO();
	}

	/*
	 * Funcion que valida cierta logica de negocios
	 */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}

	public function getComboList($data = null){
		$sql = 'select CONCAT("(", posicion, ")", item) as label, b.idActaTareaRealizadaContratoItem as data, b.idActaTareaRealizada as selected
				from contratosItems as a
				inner JOIN actaTareasRealizada_contratosItems as b on a.idContratoItem = b.idContratoItem ';
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
}

// debug zone
if($_GET['debug'] == 'ActaTareaRealizadaContratoItemVO' or false){
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
