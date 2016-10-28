<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoEstadoContratoVO extends Master2 {
	public $idContratoEstadoContrato = ["valor" => "", 
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
	public $idEstadoContrato = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "estado del contrato",
						"referencia" => "",
	];
	public $fechaVigencia = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de vigencia",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE
						],
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "proyectos/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
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
		$this->setTableName('contratosEstadosContrato');
		$this->setFieldIdName('idContratoEstadoContrato');
		$this->idContrato['referencia'] = new ContratoVO();
		$this->idEstadoContrato['referencia'] = new EstadoContratoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getEstadoActual($data = null){
		if(!$data['fechaVigencia']){
			$data['fechaVigencia'] = date('Y-m-d');
		}
		$sql = 'select p.*
				from contratosEstadosContrato as p
				where fechaVigencia <= "'.$data['fechaVigencia'].'" and idContrato = '.$this->idContrato['valor'].'
				ORDER BY fechaVigencia desc
				limit 1
				';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
				$this->mapData($rs);
				foreach (getOnlyChildVars($this) as $atributo ) {
					if($this->atributoPermitido($atributo)) {
						if($this->{$atributo}['referencia'] && $this->{$atributo}['valor']) {
							$this->{$atributo}['referencia']->{$this->{$atributo}['referencia']->getFieldIdName()}['valor'] = $this->{$atributo}['valor'];
							//print_r($this->{$atributo}['referencia']);die();
							$this->{$atributo}['referencia']->getRowById();
						}
					}
				}
			} else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, contacte al administrador.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($result); die();
		return $this;
	}

}

// debug zone
if($_GET['debug'] == 'ContratoEstadoContratoVO' or false){
	echo "DEBUG<br>";
	$kk = new ContratoEstadoContratoVO();
	//print_r($kk->getAllRows());
	$kk->idContratoEstadoContrato = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
