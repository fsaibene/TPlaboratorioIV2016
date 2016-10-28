<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoCategoriaLicenciaConducirVO extends Master2 {
    public $idTipoCategoriaLicenciaConducir = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
                                "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
                                ],
                             ];
    public $tipoCategoriaLicenciaConducir = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "string",
                            "nombre" => "categoría",
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
		$this->setTableName('tiposCategoriaLicenciaConducir');
		$this->setFieldIdName('idTipoCategoriaLicenciaConducir');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList(){
		$result = new Result();
		$data['data'] = 'idTipoCategoriaLicenciaConducir';
		$data['label'] = 'tipoCategoriaLicenciaConducir';
		$data['orden'] = 'orden, tipoCategoriaLicenciaConducir';
		$result = parent::getComboList($data);
		return $result;
	}

	/*
	 * esta version filtra para un tipo de situacion fiscal según el establecimiento que se recibe como parámetro
	 */
	public function getComboList2($idEstablecimiento){
		$sql = 'SELECT tcf.idTipoCategoriaLicenciaConducir as data, tcf.tipoCategoriaLicenciaConducir as label
				from tiposCategoriaLicenciaConducir as tcf
				inner join tiposSituacionFiscal_tiposCategoriaLicenciaConducir as tsftcf using (idTipoCategoriaLicenciaConducir)
				inner join tiposSituacionFiscal as tsf using (idTipoSituacionFiscal)
				inner join establecimientos as e using (idTipoSituacionFiscal)
				-- inner join sucursalesEstablecimiento as se using (idEstablecimiento)
				where idEstablecimiento = '.$idEstablecimiento.'
				group by data
				order by tcf.orden, tcf.tipoCategoriaLicenciaConducir
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

if($_GET['debug'] == 'TipoCategoriaLicenciaConducirVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoCategoriaLicenciaConducirVO();
	//print_r($kk->getAllRows());
	$kk->idTipoCategoriaLicenciaConducir = 116;
	$kk->tipoCategoriaLicenciaConducir = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>