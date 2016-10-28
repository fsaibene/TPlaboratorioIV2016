<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoArchivoVO extends Master2 {
	public $idTipoArchivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "id",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idModulo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "módulo",
		"referencia" => "",
	];
	public $tipoArchivo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "tipo de documento",
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
		$this->setTableName('tiposArchivo');
		$this->setFieldIdName('idTipoArchivo');
		$this->idModulo['referencia'] = new ModuloVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		return;
	}

	public function getComboList($data = null){
		$result = new Result();
		$data['data'] = 'idTipoArchivo';
		$data['label'] = 'tipoArchivo';
		$data['orden'] = 'orden';
		$data['nombreCampoWhere'] = 'habilitado';
		$data['valorCampoWhere'] = true;
		$result = parent::getComboList($data);
		return $result;
	}

	public function getComboListPorModulo($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select ta.idTipoArchivo as data, ta.tipoArchivo as label
                    from tiposArchivo as ta
                    where true ';
			if($data['idModulo'])
				$sql .= ' and idModulo = '.$data['idModulo'];
			$sql .= ' where ta.habilitado';
			//die($sql);

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

if($_GET['debug'] == 'TipoArchivoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoArchivoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoArchivo = 116;
	$kk->tipoArchivo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>