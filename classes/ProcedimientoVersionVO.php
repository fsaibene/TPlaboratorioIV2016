<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProcedimientoVersionVO extends Master2 {
	public $idProcedimientoVersion = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $idProcedimiento = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "combo",
        "nombre" => "Procedimiento",
        "referencia" => "",
    ];
    public $version  = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "version",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $fechaRevision = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "date",
        "nombre" => "fecha de revision",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
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
    public $archivo = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "archivo",
        "ruta" => "procedimientos/", // de files/ en adelante
        "tamaño" => 10485760, // 10 * 1048576 = 10 mb
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];

    public $planillaVersionArray;
    public $procedimientoVersionPlanillaVersionArray;

    public $idUsuarioLog;
    public $fechaLog;

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('procedimientosVersiones');
		$this->setFieldIdName('idProcedimientoVersion');
		$this->idProcedimiento['referencia'] =  new ProcedimientoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

    public function getCodigoProcedimiento(){
        $codigo = $this->idProcedimiento['referencia']->idTipoProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-'.str_pad($this->idProcedimiento['referencia']->nroProcedimiento['valor'], 2, '0', STR_PAD_LEFT).$this->idProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-'.$this->version['valor'];
        return $codigo;
    }

    public function insertData(){
        //print_r($this); die('uno');
        try{
            if($this->conn->inTransaction())
                $transaction = true;


            if(!$transaction)
                $this->conn->beginTransaction();

            parent::insertData();
            if($this->result->getStatus() != STATUS_OK) {
                if(!$transaction)
                    $this->conn->rollBack();
                return $this;
            }

            if($this->planillaVersionArray) {
                foreach ($this->planillaVersionArray as $auxPlV){
                    //print_r($aux); die();
                    $auxPlV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                    $auxPlV->insertData();
                    if($auxPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrVPlV); die('error uno');
                        $this->result = $auxPlV->result;
                        if(!$transaction)
                            $this->conn->rollBack();
                        return $this;
                    }
                }
            }

            if($this->procedimientoVersionPlanillaVersionArray) {
                foreach ($this->procedimientoVersionPlanillaVersionArray as $auxPrVPlV){
                    //print_r($aux); die();
                    $auxPrVPlV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                    $auxPrVPlV->insertData();
                    if($auxPrVPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrVPlV); die('error uno');
                        $this->result = $auxPrVPlV->result;
                        if(!$transaction)
                            $this->conn->rollBack();
                        return $this;
                    }
                }
            }


            //die('fin');
            if(!$transaction)
                $this->conn->commit();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }

        return $this;
    }

    public function updateData(){
        //print_r($this); die('uno');
        try{
            //$aux = clone $this;
            if($this->conn->inTransaction())
                $transaction = true;

            if(!$transaction)
                $this->conn->beginTransaction();
            //print_r($this); //die();
            parent::updateData();
            if($this->result->getStatus() != STATUS_OK) {
                //print_r($this); die('error cero');
                if(!$transaction)
                    $this->conn->rollBack();
                return $this;
            }

            //planillas hijas
            $aux = new ProcedimientoVersionPlanillaVersionVO();
            $aux->deleteDataArrayHijas($this->getFieldIdName(),$this->{$this->getFieldIdName()}['valor'],$this->planillaVersionArray);
            if($aux->result->getStatus() != STATUS_OK) {
                //print_r($aux); die('error uno');
                $this->result = $aux->result;
                if(!$transaction)
                    $this->conn->rollBack();
                return $this;
            }

            if($this->planillaVersionArray) {
                foreach ($this->planillaVersionArray as $auxPlV){
                    //print_r($aux); die();
                    $auxPlV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];

                    if($auxPlV->{$auxPlV->getFieldIdName()}['valor']) {
                        $auxPlV->updateData();
                    }else{
                        $auxPlV->insertData();
                    }

                    if($auxPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrVPlV); die('error uno');
                        $this->result = $auxPlV->result;
                        if(!$transaction)
                            $this->conn->rollBack();
                        return $this;
                    }
                }
            }

            //planillas referenciadas
            $aux = new ProcedimientoVersionPlanillaVersionVO();
            $aux->deleteDataArray($this->getFieldIdName(),$this->{$this->getFieldIdName()}['valor'],$this->procedimientoVersionPlanillaVersionArray);
            if($aux->result->getStatus() != STATUS_OK) {
                //print_r($aux); die('error uno');
                $this->result = $aux->result;
                if(!$transaction)
                    $this->conn->rollBack();
                return $this;
            }

            if($this->procedimientoVersionPlanillaVersionArray) {
                foreach ($this->procedimientoVersionPlanillaVersionArray as $auxPrVPlV){
                    //print_r($aux); die();
                    $auxPrVPlV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];

                    if($auxPrVPlV->{$auxPrVPlV->getFieldIdName()}['valor']) {
                        $auxPrVPlV->updateData();
                    }else{
                        $auxPrVPlV->insertData();
                    }

                    if($auxPrVPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrVPlV); die('error uno');
                        $this->result = $auxPrVPlV->result;
                        if(!$transaction)
                            $this->conn->rollBack();
                        return $this;
                    }
                }
            }

            //die('fin');
            if(!$transaction)
                $this->conn->commit();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }

        return $this;
    }

    public function getMaxRows($data = NULL){
        try {
            $sql = "select * from ".$this->getTableName()."
                    inner join (
                        select ".$data['nombreCampoWhere'].", max(".$data['nombreCampoMax'].") as ".$data['nombreCampoMax']."
                        from  ".$this->getTableName()."
                        where ".$data['nombreCampoWhere']." = ". $data['valorCampoWhere']."
                    ) max using(".$data['nombreCampoWhere'].",".$data['nombreCampoMax'].")";
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rs as $data){
                //print_r($data);
                $auxName = get_class($this);
                $aux = new $auxName;
                $aux->mapData($data);
                foreach (getOnlyChildVars($aux) as $atributo ) {
                    if($aux->atributoPermitido($atributo)) {
                        if($aux->{$atributo}['referencia'] && $aux->{$atributo}['valor']) {
                            $aux->{$atributo}['referencia']->{$aux->{$atributo}['referencia']->getFieldIdName()}['valor'] = $aux->{$atributo}['valor'];
                            //print_r($this->{$atributo}['referencia']);die();
                            $aux->{$atributo}['referencia']->getRowById();
                        }
                    }
                }
                $aux2[] = $aux;
            }
            $this->result->setStatus(STATUS_OK);
            $this->result->setData($aux2);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }


    public function getAllMaxRows(){
        try {
            $sql = "  select *
                      from procedimientosVersiones
                      inner join (
                          select idprocedimiento, max(version) as version
                          from  procedimientosVersiones
                          GROUP BY  idprocedimiento
                      ) max using(idprocedimiento, version)
                  ";
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rs as $data){
                //print_r($data);
                $auxName = get_class($this);
                $aux = new $auxName;
                $aux->mapData($data);
                foreach (getOnlyChildVars($aux) as $atributo ) {
                    if($aux->atributoPermitido($atributo)) {
                        if($aux->{$atributo}['referencia'] && $aux->{$atributo}['valor']) {
                            $aux->{$atributo}['referencia']->{$aux->{$atributo}['referencia']->getFieldIdName()}['valor'] = $aux->{$atributo}['valor'];
                            //print_r($this->{$atributo}['referencia']);die();
                            $aux->{$atributo}['referencia']->getRowById();
                        }
                    }
                }
                $aux2[] = $aux;
            }
            $this->result->setStatus(STATUS_OK);
            $this->result->setData($aux2);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
}

// debug zone
if($_GET['debug'] == 'ProcedimientoVersionVO' or false){
	//echo "DEBUG<br>";
	$kk = new ProcedimientoVersionVO();
	//print_r($kk->getAllRows());
	$kk->idProcedimiento['valor'] = 5;
	$html = $kk->getPDF();
	echo $html; die();
	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
