<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoRetencionPagoVO extends Master2 {
    public $idTipoRetencionPago = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoRetencionPago = ["valor" => "",
					        "obligatorio" => TRUE,
					        "tipo" => "string",
					        "nombre" => "tipo de retención",
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
		$this->setTableName('tiposRetencionPago');
		$this->setFieldIdName('idTipoRetencionPago');
	}

	/*
     * FuncionCompra que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacionCompra){
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
			$aux = new TipoRegimenRetencionPagoVO();
			$aux->idTipoRetencionPago['valor'] = $this->idTipoRetencionPago['valor'];
			$aux->tipoRegimenRetencionPago['valor'] = '-';
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
        $data['data'] = 'idTipoRetencionPago';
        $data['label'] = 'tipoRetencionPago';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }
}

if($_GET['debug'] == 'TipoRetencionPagoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoRetencionPagoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoRetencionPago = 116;
	$kk->TipoRetencionPago = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>