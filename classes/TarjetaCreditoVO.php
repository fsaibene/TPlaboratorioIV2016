<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class TarjetaCreditoVO extends Master2 {
	public $idTarjetaCredito = ["valor" => "",
       "obligatorio" => FALSE,
       "tipo" => "integer",
       "nombre" => "ID",
	];
    public $idTipoMarcaTarjeta = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "Empresa",
        "referencia" => "",
    ];
    public $idSucursalEstablecimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "Banco",
        "referencia" => "",
    ];
	public $nroTarjetaCredito = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "nro. de la tarjeta",
	];
	public $titular = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "titular",
	];
	public $fechaVencimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de vencimiento",
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
		$this->setTableName('tarjetasCredito');
		$this->setFieldIdName('idTarjetaCredito');
		$this->idTipoMarcaTarjeta['referencia'] = new TipoMarcaTarjetaVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getComboList($data = NULL){
		$sql = 'select concat(e.establecimiento, " - ", se.sucursalEstablecimiento, " - ", tmt.tipoMarcaTarjeta, " ", SUBSTRING(nroTarjetaCredito, -4)) as label, tc.idTarjetaCredito as data
				from tarjetasCredito as tc
				inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
				inner join establecimientos as e using (idEstablecimiento)
				inner join tiposMarcaTarjeta as tmt using (idTipoMarcaTarjeta)
				where tc.habilitado
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
}

// debug zone
if($_GET['debug'] == 'TarjetaCreditoVO' or false){
	echo "DEBUG<br>";
	$kk = new TarjetaCreditoVO();
	//print_r($kk->getAllRows());
	$kk->idTarjetaCredito = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
