<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanillaVersionVO extends Master2 {
	public $idPlanillaVersion = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $idPlanilla = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "combo",
        "nombre" => "Planilla",
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
    public $idTipoPlanilla = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "combo",
        "nombre" => "Tipo Planilla",
        "referencia" => "",
    ];
    public $idPagina = ["valor" => "",
        "obligatorio" => false,
        "tipo" => "combo",
        "nombre" => "Pagina",
        "referencia" => "",
    ];
    public $archivo = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "archivo",
        "ruta" => "procedimientos/planillas/", // de files/ en adelante
        "tamaño" => 10485760, // 10 * 1048576 = 10 mb
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];

    public $idUsuarioLog;
    public $fechaLog;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('planillasVersiones');
		$this->setFieldIdName('idPlanillaVersion');
		$this->idPlanilla['referencia'] = new PlanillaVO();
		$this->idPagina['referencia'] = new PaginaVO();
		$this->idTipoPlanilla['referencia'] = new TipoPlanillaVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){

        if($operacion == DELETE && $this->tieneProcedimientos()) {
            $resultMessage = 'No puede se puede eliminar una version de una planilla si se encuentra asociada a un procedimiento';
        }
        return $resultMessage;
 	}

    public function getCodigoPlanillaVersion(){
        $codigo = $this->idPlanilla['referencia']->idProcedimiento['referencia']->idTipoProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-'.str_pad($this->idPlanilla['referencia']->idProcedimiento['referencia']->nroProcedimiento['valor'], 2, '0', STR_PAD_LEFT);
        $codigo .= $this->idPlanilla['referencia']->idProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-'.$this->version['valor'].$this->idPlanilla['referencia']->sigla['valor'];
        return $codigo;
    }

    public function tieneProcedimientos(){
        try {

            $sql = "select count(*) cant from procedimientosVersionesPlanillasVersiones
                    where idPlanillaVersion = ".$this->idPlanillaVersion['valor'];
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
            $this->result->setStatus(STATUS_OK);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }

        if($this->result->getData()[0]['cant'] > 0){

            return true;
        }else{

            return false;
        };

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

    public function getAllMaxRows($data = NULL){
        try {

            $sql = "
                    select planillasVersiones.* from planillasVersiones
                    inner join (
                        select idPlanilla, max(version) as version
                        from planillasVersiones group by idPlanilla
                    ) max using(idPlanilla,version)
                    inner join planillas USING(idPlanilla)
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
if($_GET['debug'] == 'PlanillaVersionVO' or false){
	//echo "DEBUG<br>";
	$kk = new PlanillaVersionVO();
	//print_r($kk->getAllRows());
	$kk->idPlanilla['valor'] = 5;
	$html = $kk->getPDF();
	echo $html; die();


	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
