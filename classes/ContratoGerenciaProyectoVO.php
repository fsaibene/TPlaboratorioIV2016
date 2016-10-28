<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoGerenciaProyectoVO extends Master2 {
    public $idContratoGerenciaProyecto = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
	public $nroContratoGerenciaProyecto = ["valor" => "1",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "Nro. de proyecto",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
    public $idContratoGerencia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "gerencia",
        "referencia" => "",
    ];
    public $fechaInicio = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "date",
        "nombre" => "fecha de inicio",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $fechaFin = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de finalización",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
	public $nombreReferencia = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "Nombre de referencia",
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
	    $this->setTableName('contratosGerenciasProyectos');
	    $this->setFieldIdName('idContratoGerenciaProyecto');
	    $this->idContratoGerencia['referencia'] = new ContratoGerenciaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
	    if($this->fechaInicio['valor'] && $this->fechaFin['valor']){
		    if (strtotime(convertDateEsToDb($this->fechaInicio['valor'])) > strtotime(convertDateEsToDb($this->fechaFin['valor'])) ) {
			    $resultMessage = 'La fecha Fin no puede ser menor que la fecha Inicio.';
		    }
	    }
        return $resultMessage;
    }

	public function getCodigoContratoGerenciaProyecto(){
		/*$codigo = $this->idContratoGerencia['referencia']->idEstablecimiento['referencia']->establecimiento['valor'];
		$codigo .= '-'.$this->idContratoGerencia['referencia']->gerencia['valor'];
		$codigo .= '-'.$this->proyecto['valor'];*/
		$codigo = 'SNC';
		$codigo .= '-'.str_pad($this->idContratoGerencia['referencia']->idContrato['referencia']->nroContrato['valor'], 3, '0', STR_PAD_LEFT);
		$codigo .= '-'.str_pad($this->nroContratoGerenciaProyecto['valor'], 4, '0', STR_PAD_LEFT);
		$codigo .= '-'.substr(str_replace('-', '', convertDateEsToDb($this->fechaInicio['valor'])), -6);
		return $codigo;
	}

	public function getComboList2($data){
		try{
			$sql = 'select getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto) as label, idContratoGerenciaProyecto as data
					from contratosGerenciasProyectos as a
					inner join contratosGerencias as b using (idContratoGerencia)
					inner join establecimientos as c using (idEstablecimiento)
					where true ';
			if($data['valorCampoWhere']) {
				$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
			}
			$sql .= ' order by label';
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

	public function getAllRows2($data = null){
		$sql = 'select a.*, b.gerencia, getCodigoContratoGerenciaProyecto(idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto
				from contratosGerenciasProyectos as a
				inner join contratosGerencias as b using (idContratoGerencia)
				where true ';
		if($data) {
			$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
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

	public function getContratosGerenciasProyectos($data, $format = null){
		//print_r($data);
		if($data['labelConNivelAnterior']){
			$sql = 'select concat_ws(" \\\ ", gerencia, a.nombreReferencia) as label, idContratoGerenciaProyecto as data ';
		}else{
			$sql = 'select a.nombreReferencia as label, idContratoGerenciaProyecto as data ';
		}
		$sql .= ' from contratosGerenciasProyectos as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join contratos as c using (idContrato)
				where true ';
		if($data['valorCampoWhere'])
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		if($this->habilitado['valor'])
			$sql .= ' and a.habilitado and b.habilitado';
		$sql .= ' order by label ';
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getContratosGerenciasProyectos'){
	$aux = new ContratoGerenciaProyectoVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$data = array();
	$data['nombreCampoWhere'] = $_GET['nombreCampoWhere'];
	$data['valorCampoWhere'] = $_GET['valorCampoWhere'];
	$data['labelConNivelAnterior'] = $_GET['labelConNivelAnterior'];
	$aux->getContratosGerenciasProyectos($data, 'json');
}

// debug zone
if($_GET['debug'] == 'ContratoGerenciaProyectoVO' or false){
    echo "DEBUG<br>";
    $kk = new ContratoGerenciaProyectoVO();
    //print_r($kk->getAllRows());
    $kk->idContratoGerencia = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
