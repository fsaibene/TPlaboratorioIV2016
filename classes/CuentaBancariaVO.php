<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class CuentaBancariaVO extends Master2 {
    public $idCuentaBancaria = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $idSucursalEstablecimientoCuenta = ["valor" => "",
                            "obligatorio" => true,
                            "tipo" => "combo",
                            "nombre" => "establecimiento dueño de la cuenta",
                            "referencia" => "",
                            ];
    public $idSucursalEstablecimientoBanco  = ["valor" => "",
                            "obligatorio" => true,
                            "tipo" => "combo",
                            "nombre" => "banco de la cuenta",
                            "referencia" => "",
                        ];
    public $idTipoCuentaBancaria  = ["valor" => "",
                            "obligatorio" => true,
                            "tipo" => "combo",
                            "nombre" => "tipo de cuenta",
                            "referencia" => "",
                        ];
    public $cbu = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "string",
                            "nombre" => "cbu",
                        ];
    public $nroCuenta = ["valor" => "",
	                        "obligatorio" => TRUE,
	                        "tipo" => "string",
	                        "nombre" => "nro Cuenta",
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
		$this->setTableName('cuentasBancarias');
		$this->setFieldIdName('idCuentaBancaria');
		$this->idSucursalEstablecimientoCuenta['referencia'] = new SucursalEstablecimientoVO();
		$this->idSucursalEstablecimientoBanco['referencia'] = new SucursalEstablecimientoVO();
		$this->idTipoCuentaBancaria['referencia'] = new TipoCuentaBancariaVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }


	public function getComboList($data = NULL){
		$sql = 'select cb.idCuentaBancaria as data, concat(estBanco.establecimiento, " Cuenta: ", tcb.tipoCuentaBancaria, " ", cb.nroCuenta, " CBU: ", cb.cbu) as label
				from cuentasBancarias as cb
				inner join sucursalesEstablecimiento as sucCuenta on sucCuenta.idSucursalEstablecimiento = cb.idSucursalEstablecimientoCuenta
				inner join sucursalesEstablecimiento as sucBanco on sucBanco.idSucursalEstablecimiento = cb.idSucursalEstablecimientoBanco
				inner join establecimientos as estCuenta on estCuenta.idEstablecimiento = sucCuenta.idEstablecimiento
				inner join establecimientos as estBanco on estBanco.idEstablecimiento = sucBanco.idEstablecimiento
				inner join tiposCuentaBancaria as tcb using (idTipoCuentaBancaria)
				where cb.habilitado
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

	public function getNombreCompleto(){
		$this->getRowById();
		$nombreCompleto = $this->idSucursalEstablecimientoBanco['referencia']->idEstablecimiento['referencia']->establecimiento['valor'];
		$nombreCompleto .= ' Cuenta: '.$this->idTipoCuentaBancaria['referencia']->tipoCuentaBancaria['valor'] .' '. $this->nroCuenta['valor'];
		$nombreCompleto .= ' CBU: '.$this->cbu['valor'];
		return $nombreCompleto;
	}

	public function getCuentasBancariasPorEstablecimiento($data = null, $format = null){
		$sql = 'select cb.idCuentaBancaria as value, concat(estBanco.establecimiento, " Cuenta: ", tcb.tipoCuentaBancaria, " ", cb.nroCuenta, " CBU: ", cb.cbu) as label
				from cuentasBancarias as cb
				inner join sucursalesEstablecimiento as sucCuenta on sucCuenta.idSucursalEstablecimiento = cb.idSucursalEstablecimientoCuenta
				inner join sucursalesEstablecimiento as sucBanco on sucBanco.idSucursalEstablecimiento = cb.idSucursalEstablecimientoBanco
				inner join establecimientos as estCuenta on estCuenta.idEstablecimiento = sucCuenta.idEstablecimiento
				inner join establecimientos as estBanco on estBanco.idEstablecimiento = sucBanco.idEstablecimiento
				inner join tiposCuentaBancaria as tcb using (idTipoCuentaBancaria)
				where estCuenta.idEstablecimiento = '.$data["idEstablecimiento"].'
				and cb.habilitado = '.$this->habilitado['valor'].'
				order by label
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
						$items[] = array('value' => $row['value'], 'label' => $row['label']);
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getCuentasBancariasPorEstablecimiento'){
	$aux = new CuentaBancariaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data['idEstablecimiento'] = $_GET['idEstablecimiento'];
	$aux->getCuentasBancariasPorEstablecimiento($data, 'json');
}

if($_GET['debug'] == 'CuentaBancariaVO' or false){
	echo "DEBUG<br>";
	$kk = new CuentaBancariaVO();
	//print_r($kk->getAllRows());
	$kk->idCuentaBancaria = 116;
	$kk->tipoDocumento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>