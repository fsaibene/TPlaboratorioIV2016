<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoGerenciaResponsableTecnicoVO extends Master2 {
    public $idContratoGerenciaResponsableTecnico = ["valor" => "",
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
    public $idEmpleado = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "empleado",
        "referencia" => "",
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
	    $this->setTableName('contratosGerenciasResponsablesTecnicos');
	    $this->setFieldIdName('idContratoGerenciaResponsableTecnico');
	    $this->idContratoGerencia['referencia'] = new ContratoGerenciaVO();
	    $this->idTipoCaracterResponsable['referencia'] = new TipoCaracterResponsableVO();
	    $this->idEmpleado['referencia'] = new EmpleadoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

    public function getAllRows2($data = null){
        $sql = 'select a.*, a.fechaVigencia, b.gerencia, c.tipoCaracterResponsable, CONCAT(upper(d.apellido), ", ", d.nombres) as empleado
				from contratosGerenciasResponsablesTecnicos as a
				inner join contratosGerencias as b using (idContratoGerencia)
				inner join tiposCaracterResponsable as c using (idTipoCaracterResponsable)
				inner join empleados as d using(idEmpleado)
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
	 * retorna una array con los Responsables tecnicos vigentes para una UE y fecha
	 */
	public function getResponsablesTecnicosVigentes($fecha, $idContratoGerencia){
		$sql = 'select cgi.*, tipoCaracterResponsable as caracter, getEmpleado(idEmpleado) as empleado, e.celularEmpresa, e.celularParticular, e.emailEmpresa
				FROM contratosGerenciasResponsablesTecnicos as cgi
				inner join tiposCaracterResponsable as tc using (idTipoCaracterResponsable)
				INNER JOIN (
					SELECT idContratoGerencia, idTipoCaracterResponsable, MAX(fechaVigencia) as maxFechaVigencia
					FROM contratosGerenciasResponsablesTecnicos as cgi
					where cgi.fechaVigencia <= "'.convertDateEsToDb($fecha).'"
					GROUP BY cgi.idContratoGerencia, idTipoCaracterResponsable
				) AS x 
					ON x.maxFechaVigencia = cgi.fechaVigencia 
					AND x.idContratoGerencia = cgi.idContratoGerencia 
					AND x.idTipoCaracterResponsable = cgi.idTipoCaracterResponsable 
				inner join empleados as e using (idEmpleado)
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
if($_GET['debug'] == 'ContratoGerenciaResponsableTecnicoVO' or false){
    echo "DEBUG<br>";
    $kk = new ContratoGerenciaResponsableTecnicoVO();
    //print_r($kk->getAllRows());
    $kk->idContratoGerencia = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
