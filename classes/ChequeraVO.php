<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ChequeraVO extends Master2 {
	public $idChequera = ["valor" => "",
       "obligatorio" => FALSE,
       "tipo" => "integer",
       "nombre" => "ID",
	];
    public $idSucursalEstablecimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "sucursal del establecimiento",
        "referencia" => "",
        ];
	public $nroDesde = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. desde",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $nroHasta = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. hasta",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $fechaAdquisicion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de adquisición",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
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
		$this->setTableName('chequeras');
		$this->setFieldIdName('idChequera');
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        if ($this->nroDesde['valor'] > $this->nroHasta['valor']) {
            $resultMessage = 'Verifique la numeración informada.';
        }
        return $resultMessage;
 	}

	public function getComboList($data = NULL){
		$sql = 'select concat(e.establecimiento, " - ", se.sucursalEstablecimiento, " (", nroDesde, "/", nroHasta, ")") as label, c.idChequera as data
				from chequeras as c
				inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
				inner join establecimientos as e using (idEstablecimiento)
				where c.habilitado
				order by label
            	';
		//echo($sql);
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

	/*
	 * esta funcion valida si un nro de cheque X pertenece a una chequera Y y si ya fue usado o no.
	 */
	public function validarCheque($data, $format = null){
		$this->getRowById();
		$nroCheque = $data['nroCheque'];
		if($nroCheque < $this->nroDesde['valor'] || $nroCheque > $this->nroHasta['valor']){
			$rs['msg'] = 'El Nro. de cheque ingresado no pertenece a la chequera seleccionada.';
			//echo json_encode($aux);
		} else {
			$sql = 'select "El Nro. de cheque ingresado ya fue usado en: Fondo fijo" as msg
					from fondoFijo_cheques ffc
					where nroCheque = "'.$nroCheque.'"
					union
					select "El Nro. de cheque ingresado ya fue usado en: Orden de pago IVA" as msg
					from ordenesPagoIVA_cheques
					where nroCheque = "'.$nroCheque.'"
					union
					select "El Nro. de cheque ingresado ya fue usado en: Orden de pago MEC" as msg
					from ordenesPagoMEC_cheques
					where nroCheque = "'.$nroCheque.'"';
			//die($sql);
			try {
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetch(PDO::FETCH_ASSOC);
				//print_r($rs);
			}catch(Exception $e) {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
				myExceptionHandler($e);
			}
		}
		if($format == 'json') {
			echo json_encode($rs);
		} else {
			$this->result->setData($rs);
		}
		return ;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'validarCheque'){
	$aux = new ChequeraVO();
	$aux->idChequera['valor'] = $_GET['idChequera'];
	$data['nroCheque'] = $_GET['nroCheque'];
	$aux->validarCheque($data, 'json');
}

// debug zone
if($_GET['debug'] == 'ChequeraVO' or false){
	echo "DEBUG<br>";
	$kk = new ChequeraVO();
	//print_r($kk->getAllRows());
	$kk->idChequera = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
