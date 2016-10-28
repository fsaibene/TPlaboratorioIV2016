<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoGerenciaInspectorVO extends Master2 {
	public $idContratoGerenciaInspector = ["valor" => "",
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
    public $idTipoCaracterResponsable = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "carácter del responsable",
        "referencia" => "",
    ];
    public $fechaVigencia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "date",
        "nombre" => "fecha de vigencia",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
	public $apellido = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "apellido",
		"longitud" => "64"
	];
    public $nombres = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "nombres",
        "longitud" => "64"
    ];
    public $idTipoDocumento = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "tipo de documento",
        "referencia" => "",
    ];
    public $numeroDocumento = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "nro. de documento",
        "longitud" => "8"
    ];
    public $telefono = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "teléfono",
        "longitud" => "64"
    ];
    public $celular = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "celular",
        "longitud" => "64"
    ];
    public $email = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "email",
        "longitud" => "128"
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
		$this->setTableName('contratosGerenciasInspectores');
		$this->setFieldIdName('idContratoGerenciaInspector');
		$this->idContratoGerencia['referencia'] = new ContratoGerenciaVO();
		$this->idTipoCaracterResponsable['referencia'] = new TipoCaracterResponsableVO();
		$this->idTipoDocumento['referencia'] = new TipoDocumentoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getNombreCompleto(){
		return $this->apellido['valor'] . ', ' .$this->nombres['valor'];
	}

    public function getAllRows2($data = null){
        $sql = 'select a.*, a.fechaVigencia, b.gerencia, c.tipoCaracterResponsable
				from contratosGerenciasInspectores as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join tiposCaracterResponsable as c using (idTipoCaracterResponsable)
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

	/*
	 * retorna una array con los inspectores vigentes para una UE y fecha
	 */
    public function getInspectoresVigentes($fecha, $idContratoGerencia){
	    //$idContratoGerencia = null;
        $sql = 'select cgi.*, tipoCaracterResponsable as caracter
				FROM contratosGerenciasInspectores as cgi
				inner join tiposCaracterResponsable as tc using (idTipoCaracterResponsable)
				INNER JOIN (
					SELECT idContratoGerencia, idTipoCaracterResponsable, MAX(fechaVigencia) as maxFechaVigencia
					FROM contratosGerenciasInspectores as cgi
					where cgi.fechaVigencia <= "'.convertDateEsToDb($fecha).'"
					GROUP BY cgi.idContratoGerencia, idTipoCaracterResponsable
				) AS x 
					ON x.maxFechaVigencia = cgi.fechaVigencia 
					AND x.idContratoGerencia = cgi.idContratoGerencia 
					AND x.idTipoCaracterResponsable = cgi.idTipoCaracterResponsable 
				where cgi.idContratoGerencia = '.$idContratoGerencia;
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
	        $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
	    //print_r($sql); die();
        return $this;
    }
}

// debug zone
if($_GET['debug'] == 'ContratoGerenciaInspectorVO' or false){
	echo "DEBUG<br>";
	$kk = new ContratoGerenciaInspectorVO();
	//print_r($kk->getAllRows());
	$kk->idContratoGerencia = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
