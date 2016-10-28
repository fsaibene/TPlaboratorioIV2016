<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoGerenciaLocalizacionVO extends Master2 {
	public $idContratoGerenciaLocalizacion = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idContratoGerencia = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "gerencia",
						"referencia" => "",
	];
	public $idProvincia = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "provincia",
						"referencia" => "",
	];
	public $localizacion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "localización",
		"longitud" => "128"
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
		$this->setTableName('contratosGerenciasLocalizaciones');
		$this->setFieldIdName('idContratoGerenciaLocalizacion');
		$this->idContratoGerencia['referencia'] = new ContratoGerenciaVO();
		$this->idProvincia['referencia'] = new ProvinciaVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getComboList($data = NULL){
		$sql = 'select case when localizacion = "-" then gerencia else CONCAT(gerencia, "/", localizacion) end as label, idContratoGerenciaLocalizacion as data
				from contratosGerenciasLocalizaciones as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join contratos as p using (idContrato)
				where true ';
		if($data)
			$sql .= ' and p.'.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by gerencia, localizacion ';
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

	public function getAllRows2($data = null){
		$sql = 'select a.*, b.gerencia, c.provincia
				from contratosGerenciasLocalizaciones as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join provincias as c using (idProvincia)
				where true ';
		if($data) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
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

			// por cada localizacion agrego siempre los destino BA y SJ por default
			$aux = new ContratoGerenciaLocalizacionDestinoVO();
			$aux->idContratoGerenciaLocalizacion['valor'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->idDestino['valor'] = '2'; // Buenos Aires
			$aux->orden['valor'] = 0;
			$aux->habilitado['valor'] = true;
			$aux->insertData();
			if($aux->result->getStatus()  != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			$aux = new ContratoGerenciaLocalizacionDestinoVO();
			$aux->idContratoGerenciaLocalizacion['valor'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->idDestino['valor'] = '3'; // San Juan
			$aux->orden['valor'] = 0;
			$aux->habilitado['valor'] = true;
			$aux->insertData();
			if($aux->result->getStatus()  != STATUS_OK) {
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

	public function getContratosGerenciasLocalizacionesPorProyecto($data, $format = null){
		$sql = 'select concat_ws(" \\\ ", p.provincia, a.localizacion) as label, idContratoGerenciaLocalizacion as data
                from '.$this->getTableName().' as a
                inner join provincias as p using (idProvincia)
                inner join contratosGerencias as b using (idContratoGerencia)
                inner join contratosGerenciasProyectos as c using (idContratoGerencia)
                ';
		$sql .= ' where true ';
		if($data['idContratoGerenciaProyecto'])
			$sql .= ' and c.idContratoGerenciaProyecto = '.$data['idContratoGerenciaProyecto'];
		if($this->habilitado['valor'])
			$sql .= ' and a.habilitado';
		$sql .= ' order by label asc ';
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
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return ;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratosGerenciasLocalizacionesPorProyecto'){
	$aux = new ContratoGerenciaLocalizacionVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data['idContratoGerenciaProyecto'] = $_GET['idContratoGerenciaProyecto'];
	$aux->getContratosGerenciasLocalizacionesPorProyecto($data, 'json');
}

// debug zone
if($_GET['debug'] == 'ContratoGerenciaLocalizacionVO' or false){
	echo "DEBUG<br>";
	$kk = new ContratoGerenciaLocalizacionVO();
	//print_r($kk->getAllRows());
	$kk->idContratoGerencia = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
