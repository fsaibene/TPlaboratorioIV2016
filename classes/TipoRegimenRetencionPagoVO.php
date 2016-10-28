<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.2
 * @created 23-ene-2025
 */
class TipoRegimenRetencionPagoVO extends Master2 {
    public $idTipoRegimenRetencionPago = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idTipoRetencionPago = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de retención",
		"referencia" => "",
	];
    public $tipoRegimenRetencionPago = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "régimen de la retención",
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
		$this->setTableName('tiposRegimenRetencionPago');
		$this->setFieldIdName('idTipoRegimenRetencionPago');
		$this->idTipoRetencionPago['referencia'] = new TipoRetencionPagoVO();
	}

	/*
     * FuncionCompra que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacionCompra){
        return;
    }

	public function getComboList2($data = NULL){
		$sql = 'select case when tipoRegimenRetencionPago = "-" then tipoRetencionPago else CONCAT(tipoRetencionPago, "/", tipoRegimenRetencionPago) end as label, idTipoRegimenRetencionPago as data
				from tiposRegimenRetencionPago as t1
				inner join tiposRetencionPago as t2 using (idTipoRetencionPago)
				order by t1.orden, t2.orden, tipoRetencionPago, tipoRegimenRetencionPago
            	';
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
}

if($_GET['debug'] == 'TipoRegimenRetencionPagoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoRegimenRetencionPagoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoRegimenRetencionPago = 226;
	$kk->TipoRegimenRetencionPago = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>