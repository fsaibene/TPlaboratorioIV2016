<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoVO extends Master2 {
	public $idContrato = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $nroContrato = ["valor" => "1",
					    "obligatorio" => FALSE,
					    "tipo" => "integer",
					    "nombre" => "Nro. de Contrato",
					    "validador" => ["admiteMenorAcero" => FALSE,
						    "admiteCero" => FALSE,
						    "admiteMayorAcero" => TRUE,
					    ],
				    ];
	public $nombreReferencia = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "Nombre de referencia",
						"longitud" => "128"
	];
	public $observaciones = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "observaciones",
					    ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('contratos');
		$this->setFieldIdName('idContrato');
		//$this->getNroContrato();
		//$this->excluirAtributo('nroContrato');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	/*public function getNroContrato(){
		$sql = "select max(nroContrato) as nroContrato from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroContrato['valor'] = $rs[0]['nroContrato'] + 1;
			} else {
				$this->nroContrato['valor'] = 1;
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}*/

	public function getCodigoContrato(){
		//$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		$codigo = 'SNC';
		$codigo .= '-'.str_pad($this->nroContrato['valor'], 3, '0', STR_PAD_LEFT);
		/*$pd = new ContratoDefinicionVO();
		$pd->idContrato['valor'] = $this->idContrato['valor'];
		$pd->getContratoDefinicionPorIdContrato();
		if($pd->fechaInicio['valor']){
			$codigo .= '-'.substr(convertDateEsToDb($pd->fechaInicio['valor']), 0, 4);
			$codigo .= '-'.date("W", strtotime(convertDateEsToDb($pd->fechaInicio['valor'])));
		}
		$codigo .= '-'.$pd->nroReferencia['valor'];*/
		return $codigo;
	}

	/*public function insertData(){
		//print_r($this); die('dos');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$aux = new ContratoGerenciaVO();
			$aux->idContrato['valor'] = $this->idContrato['valor'];
			$aux->unidadEconomica['valor'] = 'Contrato';
			$aux->habilitado['valor'] = 1;
			$aux->orden['valor'] = 0;
			$aux->insertData();
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
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
	
	public function getComboList($data = null){
		$data['data'] = 'idContrato';
		$data['label'] = 'getCodigoContrato(idContrato)';
		$data['orden'] = 'nroContrato';

		parent::getComboList($data);
		return $this;
	}

	public function getContratos($data, $format = null){
		$sql = 'select nombreReferencia as label, idContrato as data
				from contratos as a
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratos'){
	$aux = new ContratoVO();
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->getContratos($data, 'json');
}

// debug zone
if($_GET['debug'] == 'ContratoVO' or false){
	echo "DEBUG<br>";
	$kk = new ContratoVO();
	//print_r($kk->getAllRows());
	$kk->idContrato = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
