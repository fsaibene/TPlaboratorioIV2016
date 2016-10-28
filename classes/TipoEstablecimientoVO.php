<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoEstablecimientoVO extends Master2 {
	public $idTipoEstablecimiento = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "id",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $tipoEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "rubro",
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
		$this->setTableName('tiposEstablecimiento');
		$this->setFieldIdName('idTipoEstablecimiento');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		return;
	}

	public function getComboList(){
		$result = new Result();
		$data['data'] = 'idTipoEstablecimiento';
		$data['label'] = 'tipoEstablecimiento';
		$data['orden'] = 'orden, tipoEstablecimiento';
		$result = parent::getComboList($data);
		return $result;
	}

	public function getComboList3($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select tc.idTipoEstablecimiento, tc.tipoEstablecimiento, ctc.idEstablecimientoTipoEstablecimiento
                    from tiposEstablecimiento as tc
                    left join establecimientos_tiposEstablecimiento as ctc on ctc.idTipoEstablecimiento = tc.idTipoEstablecimiento';
			if($data['idEstablecimiento'])
				$sql .= ' and idEstablecimiento = '.$data['idEstablecimiento'];
			$sql .= ' where tc.habilitado';
			$sql .= ' order by tc.tipoEstablecimiento';
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

	public function getComboList2($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select tc.idTipoEstablecimiento, tc.tipoEstablecimiento, sctc.idSucursalEstablecimientoTipoEstablecimiento
                    from tiposEstablecimiento as tc
                    inner join (select ctc.idTipoEstablecimiento
                        from establecimientos_tiposEstablecimiento as ctc
                        where idEstablecimiento = '.$data['idEstablecimiento'].') as c using (idTipoEstablecimiento)
                    left join sucursalesEstablecimiento_tiposEstablecimiento as sctc on sctc.idTipoEstablecimiento = tc.idTipoEstablecimiento';
			if($data['idSucursalEstablecimiento'])
				$sql .= ' and idSucursalEstablecimiento = '.$data['idSucursalEstablecimiento'];
			$sql .= ' where tc.habilitado';
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

	public function getTiposEstablecimiento($data, $format = null){
		$sql = 'select te.idTipoEstablecimiento as data, tipoEstablecimiento as label
                from '.$this->getTableName().' as te';
		$sql .= ' where true ';
		if($this->habilitado['valor'])
			$sql .= ' and te.habilitado';
		$sql .= ' order by tipoEstablecimiento ';
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
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getTiposEstablecimiento'){
	$aux = new TipoEstablecimientoVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->getTiposEstablecimiento($data, 'json');
}

if($_GET['debug'] == 'TipoEstablecimientoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoEstablecimientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoEstablecimiento = 116;
	$kk->tipoEstablecimiento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>