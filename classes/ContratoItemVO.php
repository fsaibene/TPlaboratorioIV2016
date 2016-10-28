<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoItemVO extends Master2 {
    public $idContratoItem = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idContratoGerencia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "gerencia",
        "referencia" => "",
    ];
    public $idTipoUnidadMedidaLabor = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "unidad de medida",
        "referencia" => "",
    ];
    public $idTipoMoneda = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "moneda",
        "referencia" => "",
    ];
	public $item = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => " Ítem del contrato",
		"longitud" => "255"
	];
	public $md5Item = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => " md5 del item",
		"longitud" => "32"
	];
    public $posicion = ["valor" => "",
        "obligatorio" => TRUE,
	    "tipo" => "integer",
	    "nombre" => "posición",
	    "validador" => ["admiteMenorAcero" => FALSE,
		    "admiteCero" => FALSE,
		    "admiteMayorAcero" => TRUE,
	    ],
    ];
	public $monto = ["valor" => "0.00",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
	public $contratoItemServicioTareaArray = [
		'tipo' => 'comboMultiple',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ContratoItemServicioTareaVO', // es el nombre de la clase a la que hace referencia el array
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idServicioTarea', // es el campo por el que se filtra... Ver funcion deleteDataArray
		'filterGroupKeyName' => 'idContratoItem'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('contratosItems');
	    $this->setFieldIdName('idContratoItem');
	    $this->idContratoGerencia['referencia'] = new ContratoGerenciaVO();
	    $this->idTipoUnidadMedidaLabor['referencia'] = new TipoUnidadMedidaLaborVO();
	    $this->idTipoMoneda['referencia'] = new TipoMonedaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
	    $this->md5Item['valor'] = md5($this->item['valor']);
        return $resultMessage;
    }

	public function getAllRows2($data = null){
		$sql = 'select a.*, b.gerencia, d.simbolo
				from contratosItems as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner JOIN tiposMoneda as d using (idTipoMoneda)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' .$data['valorCampoWhere'];
		}
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$this->result->setData($rs);
				//print_r($this); die();
			} else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, contacte al administrador.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getComboList($data = NULL){
		$sql = 'select concat_ws(" - ", getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto), concat(item, " (", posicion, ")")) as label, idContratoItem as data
				from contratosItems as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join contratosGerenciasProyectos as c using (idContratoGerencia)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
		$sql .= ' group by idContratoItem
				  order by label
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

	public function getContratoItemsPorServicioActividad($data){
		try{
			$sql = 'select concat(item, " (Pos: ", posicion, " - U/M: ", tipoUnidadMedidaLabor, ")") as label, idContratoItem as data
					FROM contratosItems as ci
					inner join tiposUnidadMedidaLabor as tum using (idTipoUnidadMedidaLabor)
					inner join contratosItems_serviciosTareas as cist using (idContratoItem)
					inner join serviciosTarea as st using (idServicioTarea)
					-- inner join serviciosActividad as sa using (idServicioActividad)
					inner join proyectosComisiones as pc using (idServicioActividad)
					where true and idServicioActividad = ' . $data['idServicioActividad'].'
					group by idContratoItem
					order by label
					';
			//die($sql);
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getContratoItemsPorContratoGerenciaProyecto($data){
		try{
			$sql = 'select concat_ws(" - ", getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto), concat(item, " (Pos: ", posicion, " - U/M: ", tipoUnidadMedidaLabor, ")")) as value, idContratoItem as id
					FROM contratosItems as ci
					inner join tiposUnidadMedidaLabor as tum using (idTipoUnidadMedidaLabor)
					inner join contratosItems_serviciosTareas as cist using (idContratoItem)
					inner join serviciosTarea as st using (idServicioTarea)
					inner join proyectosComisiones as pc using (idServicioActividad)
					-- inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
					where true and idContratoGerenciaProyecto = '. $data['idContratoGerenciaProyecto'].'
					group by idContratoItem
					order by value
					';
			//die($sql);
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getContratoItemsPorIdContratoGerencia($data){
		try{
			$sql = 'select concat(item, " (Pos: ", posicion, " - U/M: ", tipoUnidadMedidaLabor, ")") as value, idContratoItem as id
					FROM contratosItems as ci
					inner join tiposUnidadMedidaLabor as tum using (idTipoUnidadMedidaLabor)
					where true and idContratoGerencia = '. $data['idContratoGerencia'].'
					order by value
					';
			//die($sql);
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratoItemsPorServicioActividad'){
	$aux = new ContratoItemVO();
	$data['idServicioActividad'] = $_GET['idServicioActividad'];
	$aux->{$_GET['type']}($data);
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratoItemsPorContratoGerenciaProyecto'){
	$aux = new ContratoItemVO();
	$data['idContratoGerenciaProyecto'] = $_GET['idContratoGerenciaProyecto'];
	$aux->{$_GET['type']}($data);
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratoItemsPorIdContratoGerencia'){
	$aux = new ContratoItemVO();
	$data['idContratoGerencia'] = $_GET['idContratoGerencia'];
	$aux->{$_GET['type']}($data);
}

// debug zone
if($_GET['debug'] == 'ContratoItemVO' or false){
    echo "DEBUG<br>";
    $kk = new ContratoItemVO();
    //print_r($kk->getAllRows());
    $kk->idContratoGerencia = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
