<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class PlanillaVO extends Master2 {
	public $idPlanilla = ["valor" => "",
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

    public $sigla = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "Sigla",
    ];

    public $planillaVersionArray;

    public $idUsuarioLog;
    public $fechaLog;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('planillas');
		$this->setFieldIdName('idPlanilla');
		$this->idProcedimiento['referencia'] = new ProcedimientoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

    public function getCodigoPlanilla(){
        $codigo = $this->idProcedimiento['referencia']->idTipoProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-'.str_pad($this->idProcedimiento['referencia']->nroProcedimiento['valor'], 2, '0', STR_PAD_LEFT);
        $codigo .= $this->idProcedimiento['referencia']->sigla['valor'];
        $codigo .= '-_'.$this->sigla['valor'];
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
                $auxPlV = $this->planillaVersionArray[0];
                //print_r($auxPlV); die();
                $auxPlV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                //echo '<pre>';

                if ($auxPlV->idTipoPlanilla['valor'] == 1) {
                    $auxPlV->archivo['valor'] = null;
                } elseif ($auxPlV->idTipoPlanilla['valor'] == 2) {
                    $auxPlV->idPagina['valor'] = null;
                }
                //print_r($auxPlV);

                $auxPlV->insertData();
                //print_r($auxPlV);
                if ($auxPlV->result->getStatus() != STATUS_OK) {
                    //print_r($aux); die('error uno');
                    $this->result = $auxPlV->result;
                    if(!$transaction)
                        $this->conn->rollBack();
                    return $this;
                }
            }

            if(!$transaction)
                $this->conn->commit();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }

            public function deleteDataArray($column,$valor, $arrayVO = null){
        $sql = 'delete from '.$this->getTableName().'
				where '.$column.' = '.$valor;
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

    public function getHijasRows($data = NULL){
        try {

            $sql = " select planillas.*
            from procedimientosVersionesPlanillasVersiones
            inner join planillasVersiones USING(idPlanillaVersion)
            inner join planillas using(idPlanilla)
            inner join procedimientosVersiones USING(idProcedimientoVersion)
            where ".$data['nombreCampoWhere']." = ". $data['valorCampoWhere']."
            and procedimientosVersiones.idProcedimiento = planillas.idProcedimiento";
            //print_r($data);
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
if($_GET['debug'] == 'planillaVO' or false){
	//echo "DEBUG<br>";
	$kk = new PlanillaVO();
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
