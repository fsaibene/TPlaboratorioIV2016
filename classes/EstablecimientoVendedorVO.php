<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class EstablecimientoVendedorVO extends Master2 {
	public $idEstablecimientoVendedor = ["valor" => "",
       "obligatorio" => FALSE,
       "tipo" => "integer",
       "nombre" => "ID",
	];
	public $idEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "proveedor",
		"referencia" => "",
	];
	public $vendedor = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "vendedor",
	];
	public $telefono = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "teléfono",
	];
	public $email = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "email",
	];
	public $habilitado = ["valor" => TRUE,
		"obligatorio" => FALSE,
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
		$this->setTableName('establecimientosVendedores');
		$this->setFieldIdName('idEstablecimientoVendedor');
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getComboList($data = null){
		$data['data'] = 'idEstablecimientoVendedor';
		$data['label'] = 'vendedor';
		$data['orden'] = 'vendedor';

		parent::getComboList($data);
		return $this;
	}

	public function getEstablecimientoVendedores($data = null, $format = null){
		$sql = 'select se.idEstablecimientoVendedor as data, se.vendedor as label
				from establecimientosVendedores as se
				where se.idEstablecimiento = '.$this->idEstablecimiento['valor'].'
				and se.habilitado = '.$this->habilitado['valor'].'
				order by se.vendedor
				';
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
					echo json_encode(array_map('setHtmlEntityDecode', $items));
					return;
				} else {
					$this->result->setData($rs);
				}
			} else {
				if($format == 'json') { // aunque no traiga nada debo devolver un array
					echo json_encode(array_map('setHtmlEntityDecode', $items));
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getEstablecimientoVendedores'){
	$aux = new EstablecimientoVendedorVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->idEstablecimiento['valor'] = $_GET['idEstablecimiento'];
	$aux->getEstablecimientoVendedores($data, 'json');
}

// debug zone
if($_GET['debug'] == 'EstablecimientoVendedorVO' or false){
	echo "DEBUG<br>";
	$kk = new EstablecimientoVendedorVO();
	//print_r($kk->getAllRows());
	$kk->idEstablecimientoVendedor = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
