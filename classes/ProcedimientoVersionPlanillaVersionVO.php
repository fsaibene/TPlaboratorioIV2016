<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProcedimientoVersionPlanillaVersionVO extends Master2 {
	public $idProcedimientoVersionPlanillaVersion = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $idProcedimientoVersion = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "Procedimiento version",
        "referencia" => "",
    ];
    public $idPlanillaVersion = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "combo",
        "nombre" => "Planilla Version",
        "referencia" => "",
    ];

    public $idUsuarioLog;
    public $fechaLog;

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('procedimientosVersionesPlanillasVersiones');
		$this->setFieldIdName('idProcedimientoVersionPlanillaVersion');
		$this->idProcedimientoVersion['referencia'] =  new ProcedimientoVersionVO();
		$this->idPlanillaVersion ['referencia'] =  new PlanillaVersionVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

    /*
   * esta funcion devuelve todas las paginas que no estas
   */
    public function getPlanillasProcedimientos($idProcedimiento = NULL){
        //die('asd'.$idModulo);

        //die($urlPathPagina);
        $sql = " select  planillasVersiones.idPlanillaVersion as data ,
                CONCAT(tiposProcedimiento.sigla,' - ',procedimientos.nroProcedimiento,procedimientos.sigla,'-',planillasVersiones.version,planillas.sigla) as label
                  FROM
                procedimientosVersionesPlanillasVersiones
                INNER JOIN procedimientosVersiones ON procedimientosVersionesPlanillasVersiones.idProcedimientoVersion = procedimientosVersiones.idProcedimientoVersion
                INNER JOIN planillasVersiones ON procedimientosVersionesPlanillasVersiones.idPlanillaVersion = planillasVersiones.idPlanillaVersion
                INNER JOIN planillas ON planillasVersiones.idPlanilla = planillas.idPlanilla
                INNER JOIN procedimientos ON procedimientosVersiones.idProcedimiento = procedimientos.idProcedimiento AND planillas.idProcedimiento = procedimientos.idProcedimiento
                inner join tiposProcedimiento using(idTipoProcedimiento)
                inner join (
                    select idProcedimiento, MAX(idProcedimientoVersion) as idProcedimientoVersion from procedimientosVersiones GROUP BY idProcedimiento
                ) as maxvers on  maxvers.idProcedimiento = procedimientos.idProcedimiento and maxvers.idProcedimientoVersion = procedimientosVersiones.idProcedimientoVersion";
                if($idProcedimiento){
                    $sql.= " where procedimientos.idProcedimiento != ".$idProcedimiento;
                }
        $sql .= " order by tiposProcedimiento.sigla,procedimientos.nroProcedimiento ,procedimientos.sigla,planillas.sigla,planillasVersiones.version ";

        //die($sql);
        try {
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
        $this->result;
    }

    public function getPlanillasProcedimientosVersiones($idProcedimiento = NULL){
        //die('asd'.$idModulo);

        //die($urlPathPagina);
        $sql = "  select  planillasVersiones.idPlanillaVersion as data ,
                CONCAT(tiposProcedimiento.sigla,' - ',procedimientos.nroProcedimiento,procedimientos.sigla,'-',planillasVersiones.version,planillas.sigla) as label
                FROM planillas
                INNER JOIN planillasVersiones ON planillasVersiones.idPlanilla = planillas.idPlanilla
                INNER JOIN procedimientos ON planillas.idProcedimiento = procedimientos.idProcedimiento
                inner join tiposProcedimiento using(idTipoProcedimiento) ";
        if($idProcedimiento){
            $sql.= " where procedimientos.idProcedimiento = ".$idProcedimiento;
        }
        $sql .= " order by tiposProcedimiento.sigla,procedimientos.nroProcedimiento ,procedimientos.sigla,planillas.sigla,planillasVersiones.version ";

        //die($sql);
        try {
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
        $this->result;
    }

    public function getReferenciadasRows($data = NULL){
        try {

            $sql = " select ".$this->getTableName().".*
            from ".$this->getTableName()."
            inner join planillasVersiones USING(idPlanillaVersion)
            inner join planillas using(idPlanilla)
            inner join procedimientosVersiones USING(idProcedimientoVersion)
            where ".$data['nombreCampoWhere']." = ". $data['valorCampoWhere']."
            and procedimientosVersiones.idProcedimiento != planillas.idProcedimiento";
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

    public function getHijasRows($data = NULL){
        try {

            $sql = " select ".$this->getTableName().".*
            from ".$this->getTableName()."
            inner join planillasVersiones USING(idPlanillaVersion)
            inner join planillas using(idPlanilla)
            inner join procedimientosVersiones USING(idProcedimientoVersion)
            where ".$data['nombreCampoWhere']." = ". $data['valorCampoWhere']."
            and procedimientosVersiones.idProcedimiento = planillas.idProcedimiento";
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

    public function deleteDataArray($column,$valor, $arrayVO = null){

        $sql = 'delete '.$this->getTableName().'.* from '.$this->getTableName().'
                inner join planillasVersiones USING(idPlanillaVersion)
                inner join planillas using(idPlanilla)
                inner join procedimientosVersiones USING(idProcedimientoVersion)
				where '.$column.' = '.$valor.'
                and procedimientosVersiones.idProcedimiento != planillas.idProcedimiento ';
        if($arrayVO){
            $notIn = '';
            foreach($arrayVO as $VO){
                $notIn.= $VO->{$VO->getFieldIdName()}['valor'].',';
            }
            if(trim($notIn,',') != '') {
                $sql .= ' and ' . $this->getFieldIdName() . ' not in (' . trim($notIn,','). ')';
            }
        }
        //die($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if(!$ro->execute()){
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            }
            $this->result->setMessage('Los datos fueron ACTUALIZADOS con éxito.');
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }

    public function deleteDataArrayHijas($column,$valor, $arrayVO = null){

        $sql = 'delete '.$this->getTableName().'.* from '.$this->getTableName().'
                inner join planillasVersiones USING(idPlanillaVersion)
                inner join planillas using(idPlanilla)
                inner join procedimientosVersiones USING(idProcedimientoVersion)
				where '.$column.' = '.$valor.'
                and procedimientosVersiones.idProcedimiento = planillas.idProcedimiento ';
        if($arrayVO){
            $notIn = '';
            foreach($arrayVO as $VO){
                $notIn.= $VO->{$VO->getFieldIdName()}['valor'].',';
            }
            if(trim($notIn,',') != '') {
                $sql .= ' and ' . $this->getFieldIdName() . ' not in (' . trim($notIn,','). ')';
            }
        }
        //die($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if(!$ro->execute()){
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            }
            $this->result->setMessage('Los datos fueron ACTUALIZADOS con éxito.');
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }
}


// debug zone
if($_GET['debug'] == 'ProcedimientoVersionPlanillaVersionVO' or false){
	//echo "DEBUG<br>";
	$kk = new ProcedimientoVersionPlanillaVersionVO();
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
