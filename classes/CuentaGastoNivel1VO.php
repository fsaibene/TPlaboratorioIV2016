<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class CuentaGastoNivel1VO extends Master2 {
    public $idCuentaGastoNivel1 = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $cuentaGastoNivel1 = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "cuenta de gasto nivel 1",
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
		$this->setTableName('cuentasGastosNivel1');
		$this->setFieldIdName('idCuentaGastoNivel1');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function insertData(){
		//print_r($this); die('dos');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$aux = new CuentaGastoNivel2VO();
			$aux->idCuentaGastoNivel1['valor'] = $this->idCuentaGastoNivel1['valor'];
			$aux->cuentaGastoNivel2['valor'] = '-';
			$aux->orden['valor'] = 0;
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
	}

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idCuentaGastoNivel1';
        $data['label'] = 'cuentaGastoNivel1';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'CuentaGastoNivel1VO' or false){
	echo "DEBUG<br>";
	$kk = new CuentaGastoNivel1VO();
	//print_r($kk->getAllRows());
	$kk->idCuentaGastoNivel1 = 116;
	$kk->CuentaGastoNivel1 = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>