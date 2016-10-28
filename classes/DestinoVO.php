<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class DestinoVO extends Master2 {
    public $idDestino = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
	public $idProvincia = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "provincia",
		"referencia" => "",
	];
    public $destino = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "destino",
	    "longitud" => "128"
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
		$this->setTableName('destinos');
		$this->setFieldIdName('idDestino');
		$this->idProvincia['referencia'] = new ProvinciaVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function getComboList($data = null){
		$sql = 'select CONCAT_ws("\\\", provincia, destino) as label, idDestino as data
				from destinos as a
				inner join provincias as p using (idProvincia)
				/*inner join contratosGerenciasLocalizacionesDestinos as b using (idDestino)
				inner join contratosGerenciasLocalizaciones as c using (idContratoGerenciaLocalizacion)
				inner join contratosGerencias as d using (idContratoGerencia)*/
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by p.orden, p.provincia, a.orden, a.destino ';
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

	/*public function getComboList4($data = NULL){
		//$sql = 'select CONCAT(gerencia, "/", localizacion, "/", destino) as label, idContratoGerenciaLocalizacionDestino as data
		$sql = 'select destino as label, idContratoGerenciaLocalizacionDestino as data
				from destinos as a
				inner join contratosGerenciasLocalizacionesDestinos as b using (idDestino)
				inner join contratosGerenciasLocalizaciones as c using (idContratoGerenciaLocalizacion)
				inner join contratosGerencias as d using (idContratoGerencia)
				where true ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by localizacion, destino ';
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
	}*/

	public function getComboList2($data){
		$sql = 'select DISTINCT CONCAT_ws("\\\", provincia, destino, tipoAlojamiento) as label, idDestino as data
				from (
					select a.*, b.tipoAlojamiento
					from destinos as a, tiposAlojamiento as b
				) as a
				inner join provincias as p using (idProvincia)
				/*inner join contratosGerenciasLocalizacionesDestinos as b using (idDestino)
				inner join contratosGerenciasLocalizaciones as c using (idContratoGerenciaLocalizacion)
				inner join contratosGerencias as d using (idContratoGerencia)*/
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by a.orden, a.destino ';
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
	/*public function getComboList3($data){
		$sql = 'select DISTINCT destino as label, idDestino as data
				from destinos as a
				inner join contratosGerenciasLocalizacionesDestinos as b using (idDestino)
				inner join contratosGerenciasLocalizaciones as c using (idContratoGerenciaLocalizacion)
				inner join contratosGerencias as d using (idContratoGerencia)
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' order by a.orden, a.destino ';
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
	}*/
}

if($_GET['debug'] == 'DestinoVO' or false){
	echo "DEBUG<br>";
	$kk = new DestinoVO();
	//print_r($kk->getAllRows());
	$kk->idDestino = 116;
	$kk->tipoDocumento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>