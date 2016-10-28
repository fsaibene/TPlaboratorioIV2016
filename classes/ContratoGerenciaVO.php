<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoGerenciaVO extends Master2 {
	public $idContratoGerencia = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idContrato = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "Contrato",
						"referencia" => "",
	];
	public $idEstablecimiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "cliente",
						"referencia" => "",
	];
	public $gerencia = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "gerencia",
		"longitud" => "128"
	];
	public $sigla = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "sigla",
		"longitud" => "2"
	];
	public $orden = ["valor" => "0",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "orden",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $habilitado = ["valor" => TRUE,
		"obligatorio" => TRUE,
		"tipo" => "bool",
		"nombre" => "habilitado",
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
		$this->setTableName('contratosGerencias');
		$this->setFieldIdName('idContratoGerencia');
		$this->idContrato['referencia'] = new ContratoVO();
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getComboList($data = null){
		$data['data'] = 'idContratoGerencia';
		$data['label'] = 'gerencia';
		$data['orden'] = 'gerencia';

		parent::getComboList($data);
		return $this;
	}

	public function getContratosGerencias($data, $format = null){
		$sql = 'select gerencia as label, idContratoGerencia as data
				from contratosGerencias as a
				inner join contratos as b using (idContrato)
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		if($this->habilitado['valor'])
			$sql .= ' and a.habilitado';
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

	/*
	 * devuelve datos del contrato gerencia
	 */
	public function getInfoContratoGerenciaParaATR($data){
		$sql = 'select e.establecimiento
				from contratosGerencias as cg
				inner JOIN establecimientos as e using (idEstablecimiento)
				inner JOIN contratos as c using (idContrato)
				where idContratoGerencia = '.$data['idContratoGerencia'];
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
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratosGerencias'){
	$aux = new ContratoGerenciaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$aux->getContratosGerencias($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getInfoContratoGerenciaParaATR'){
	$aux = new ContratoGerenciaVO();
	$data = array();
	$data['idContratoGerencia'] = $_GET['idContratoGerencia'];
	$aux->{$_GET['type']}($data);
}

// debug zone
if($_GET['debug'] == 'ContratoGerenciaVO' or false){
	echo "DEBUG<br>";
	$kk = new ContratoGerenciaVO();
	//print_r($kk->getAllRows());
	$kk->idContratoGerencia = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
