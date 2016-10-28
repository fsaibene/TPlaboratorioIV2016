<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class CuentaGastoNivel2VO extends Master2 {
    public $idCuentaGastoNivel2 = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idCuentaGastoNivel1 = ["valor" => "",
							"obligatorio" => TRUE,
							"tipo" => "combo",
							"nombre" => "cuenta de gasto nivel 1",
							"referencia" => "",
	];
    public $cuentaGastoNivel2 = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "cuenta de gasto nivel 2",
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

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('cuentasGastosNivel2');
		$this->setFieldIdName('idCuentaGastoNivel2');
		$this->idCuentaGastoNivel1['referencia'] = new CuentaGastoNivel1VO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList($data = NULL){
		$sql = 'select case when cgn2.cuentaGastoNivel2 = "-" then cgn1.cuentaGastoNivel1 else CONCAT(cgn1.cuentaGastoNivel1, "/", cgn2.cuentaGastoNivel2) end as label, cgn2.idCuentaGastoNivel2 as data
				from cuentasGastosNivel2 as cgn2
				inner join cuentasGastosNivel1 as cgn1 using (idCuentaGastoNivel1)
				order by cgn1.orden, cgn2.orden, cuentaGastoNivel1, cuentaGastoNivel2
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

if($_GET['debug'] == 'CuentaGastoNivel2VO' or false){
	echo "DEBUG<br>";
	$kk = new CuentaGastoNivel2VO();
	//print_r($kk->getAllRows());
	$kk->idCuentaGastoNivel2 = 226;
	$kk->CuentaGastoNivel2 = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>