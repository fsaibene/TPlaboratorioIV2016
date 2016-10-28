<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProcedimientoVO extends Master2 {
	public $idProcedimiento = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $idTipoProcedimiento = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "combo",
        "nombre" => "Tipo de Procedimiento",
        "referencia" => "",
    ];
    public $sigla = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "Sigla",
	    "longitud" => 4
    ];
	public $nroProcedimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. de Procedimiento",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
		],
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];

	public $procedimientoVersionArray;
	public $planillaArray;
	public $planillaVersionArray;
	public $procedimientoVersionPlanillaVersionArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('procedimientos');
		$this->setFieldIdName('idProcedimiento');
		$this->idTipoProcedimiento['referencia'] = new TipoProcedimientoVO();
		//$this->getNroProcedimiento();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getCodigoProcedimiento(){
		$codigo = $this->idTipoProcedimiento['referencia']->sigla['valor'];
		$codigo .= '-'.$this->sigla['valor'];
		$codigo .= '-'.str_pad($this->nroProcedimiento['valor'], 2, '0', STR_PAD_LEFT);
		return $codigo;
	}

	/*
     * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
     */
	public function insertData(){
		//print_r($this); die('uno');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}

			//print_r($this); //die('dos');
			if($this->procedimientoVersionArray) {
				foreach ($this->procedimientoVersionArray as $auxPrV){
					//print_r($auxPrV); die();
					$auxPrV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                    //print_r($auxPrV); die();
					$auxPrV->insertData();
                    //print_r($auxPrV); die();
					if($auxPrV->result->getStatus()  != STATUS_OK) {
						//print_r($auxPrV); die('error uno');
						$this->result = $auxPrV->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

            if($this->planillaArray) {
                $i=0;
				foreach ($this->planillaArray as $auxPl){
					//print_r($auxPl); die();
					$auxPl->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];

                    //print_r($auxPl);
					$auxPl->insertData();
                    //print_r($auxPl); //die();
					if($auxPl->result->getStatus()  != STATUS_OK) {
						//print_r($auxPl); die('error uno');
						$this->result = $auxPl->result;
						$this->conn->rollBack();
						return $this;
					}

                    $auxPlV = $this->planillaVersionArray[$i];
                    //print_r($auxPlV); die();
                    $auxPlV->{$auxPl->getFieldIdName()}['valor'] = $auxPl->{$auxPl->getFieldIdName()}['valor'];
                    //echo '<pre>';

                    if($auxPlV->idTipoPlanilla['valor']==1){
                        $auxPlV->archivo['valor']=null;
                    }elseif($auxPlV->idTipoPlanilla['valor']==2){
                        $auxPlV->idPagina['valor']=null;
                    }
                    //print_r($auxPlV);

                    $auxPlV->insertData();
                    //print_r($auxPlV);
                    if($auxPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($aux); die('error uno');
                        $this->result = $auxPlV->result;
                        $this->conn->rollBack();
                        return $this;
                    }

                    $auxPrVPlV = new ProcedimientoVersionPlanillaVersionVO();
                    $auxPrVPlV->{$auxPrV->getFieldIdName()}['valor'] = $auxPrV->{$auxPrV->getFieldIdName()}['valor'];
                    $auxPrVPlV->{$auxPlV->getFieldIdName()}['valor'] = $auxPlV->{$auxPlV->getFieldIdName()}['valor'];

                    $auxPrVPlV->insertData();
                    //print_r($auxPrVPlV);
                    if($auxPrVPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($aux); die('error uno');
                        $this->result = $auxPrVPlV->result;
                        $this->conn->rollBack();
                        return $this;
                    }

                    $i++;

				}

			}


            if($this->procedimientoVersionPlanillaVersionArray) {
				foreach ($this->procedimientoVersionPlanillaVersionArray as $auxPrVPlV){
					//print_r($aux); die();
					$auxPrVPlV->{$auxPrV->getFieldIdName()}['valor'] = $auxPrV->{$auxPrV->getFieldIdName()}['valor'];
					$auxPrVPlV->insertData();
					if($auxPrVPlV->result->getStatus()  != STATUS_OK) {
						//print_r($auxPrVPlV); die('error uno');
						$this->result = $auxPrVPlV->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}
    public function updateDataCabecera(){
        //print_r($this); die('uno');
        try{
            //$aux = clone $this;
            $this->conn->beginTransaction();
            //print_r($this); //die();
            parent::updateData();
            if($this->result->getStatus() != STATUS_OK) {
                //print_r($this); die('error cero');
                $this->conn->rollBack();
                return $this;
            }
            //die('fin');
            $this->conn->commit();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
	/*
     * Hago el update de la tabla padre y luego borro los registros se la tabla muchos a muchos y los vuelvo a insertar.
     * Tiene que ser asi (borrar y crear) porque quiza me eliminaron un registro de la tabla muchos a muchos.
     */
	public function updateData(){
		//print_r($this); die('uno');
		try{
			//$aux = clone $this;
			$this->conn->beginTransaction();
			//print_r($this); //die();
			parent::updateData();
			if($this->result->getStatus() != STATUS_OK) {
				//print_r($this); die('error cero');
				$this->conn->rollBack();
				return $this;
			}

			//print_r($this); //die();
            if($this->procedimientoVersionArray) {
                foreach ($this->procedimientoVersionArray as $auxPrV){
                    //print_r($auxPrV); die();
                    $auxPrV->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                    //print_r($auxPrV); die();
                    $auxPrV->updateData();
                    //print_r($auxPrV); die();
                    if($auxPrV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrV); die('error uno');
                        //$this->result = $auxPrV->result;
                        $this->conn->rollBack();
                        return $this;
                    }
                }
            }

            $aux = new PlanillaVO();

            $aux->deleteDataArray($this->getFieldIdName(),$this->{$this->getFieldIdName()}['valor'],$this->planillaArray);
            if($aux->result->getStatus() != STATUS_OK) {
                //print_r($aux); die('error uno');
                $this->result = $aux->result;
                $this->conn->rollBack();
                return $this;
            }

            if($this->planillaArray) {

                $i=0;
                foreach ($this->planillaArray as $auxPl){
                    //print_r($auxPl); die();
                    $auxPl->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
                    //print_r($auxPl);

                    if($auxPl->{$auxPl->getFieldIdName()}['valor']) {
                        $auxPl->updateData();
                    }else{
                        $auxPl->insertData();
                    }//print_r($auxPl); //die();
                    if($auxPl->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPl); die('error uno');
                        $this->result = $auxPl->result;
                        $this->conn->rollBack();
                        return $this;
                    }

                    $auxPlV = $this->planillaVersionArray[$i];
                    //print_r($auxPlV); die();
                    $auxPlV->{$auxPl->getFieldIdName()}['valor'] = $auxPl->{$auxPl->getFieldIdName()}['valor'];
                    //echo '<pre>';
                    //print_r($auxPlV);

                    if($auxPlV->idTipoPlanilla['valor']==1){
                        $auxPlV->archivo['valor']=null;
                    }elseif($auxPlV->idTipoPlanilla['valor']==2){
                        $auxPlV->idPagina['valor']=null;
                    }

                    if($auxPlV->{$auxPlV->getFieldIdName()}['valor']) {
                        $auxPlV->updateData();
                    }else{
                        $auxPlV->insertData();

                        $auxPrVPlV = new ProcedimientoVersionPlanillaVersionVO();
                        $auxPrVPlV->{$auxPrV->getFieldIdName()}['valor'] = $auxPrV->{$auxPrV->getFieldIdName()}['valor'];
                        $auxPrVPlV->{$auxPlV->getFieldIdName()}['valor'] = $auxPlV->{$auxPlV->getFieldIdName()}['valor'];

                        $auxPrVPlV->insertData();

                        //print_r($auxPrVPlV);
                        //die();
                        if ($auxPrVPlV->result->getStatus() != STATUS_OK) {
                            //print_r($aux); die('error uno');
                            $this->result = $auxPrVPlV->result;
                            $this->conn->rollBack();
                            return $this;
                        }

                    }
                    //print_r($auxPlV);
                    if($auxPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($aux); die('error uno');
                        $this->result = $auxPlV->result;
                        $this->conn->rollBack();
                        return $this;
                    }

                    $i++;

                }

            }

            $aux = new ProcedimientoVersionPlanillaVersionVO();
            $aux->deleteDataArray($auxPrV->getFieldIdName(),$auxPrV->{$auxPrV->getFieldIdName()}['valor'],$this->procedimientoVersionPlanillaVersionArray);
            if($aux->result->getStatus() != STATUS_OK) {
                //print_r($aux); die('error uno');
                $this->result = $aux->result;
                $this->conn->rollBack();
                return $this;
            }

            if($this->procedimientoVersionPlanillaVersionArray) {
                foreach ($this->procedimientoVersionPlanillaVersionArray as $auxPrVPlV){
                    //print_r($aux); die();
                    $auxPrVPlV->{$auxPrV->getFieldIdName()}['valor'] = $auxPrV->{$auxPrV->getFieldIdName()}['valor'];

                    if($auxPrVPlV->{$auxPrVPlV->getFieldIdName()}['valor']) {
                        $auxPrVPlV->updateData();
                    }else{
                        $auxPrVPlV->insertData();
                    }

                    if($auxPrVPlV->result->getStatus()  != STATUS_OK) {
                        //print_r($auxPrVPlV); die('error uno');
                        $this->result = $auxPrVPlV->result;
                        $this->conn->rollBack();
                        return $this;
                    }
                }
            }

			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

    public function getReporte($data){
        //print_r($data); //die();
        $sql = "select tiposProcedimiento.tipoProcedimiento ,procedimientos.sigla, getCodigoProcedimiento(procedimientos.idProcedimiento) as codigoProcedimiento,
	                CONCAT(tiposProcedimientoPadre.sigla,'-',procedimientosPadre.nroProcedimiento,procedimientosPadre.sigla,'-',planillasVersiones.version,planillas.sigla) as codigoPlanilla,
	                case WHEN(planillas.idProcedimiento = procedimientos.idProcedimiento) then 'Planilla Hija' else 'Planilla Referenciada' end tipoPlanilla ,
	                procedimientosVersiones.archivo , planillasVersiones.idPlanillaVersion
                from procedimientosVersiones
                inner join (
                        select idprocedimiento, max(version) as version
                        from  procedimientosVersiones
                        GROUP BY  idprocedimiento
                ) max_vers using(idprocedimiento, version)
                INNER JOIN procedimientos USING(idProcedimiento)
                inner join tiposProcedimiento using(idTipoProcedimiento)
                INNER JOIN procedimientosVersionesPlanillasVersiones using(idProcedimientoVersion)
                INNER JOIN planillasVersiones ON procedimientosVersionesPlanillasVersiones.idPlanillaVersion = planillasVersiones.idPlanillaVersion
                INNER JOIN planillas ON planillasVersiones.idPlanilla = planillas.idPlanilla
                INNER JOIN procedimientos as procedimientosPadre on procedimientosPadre.idProcedimiento = planillas.idProcedimiento
                inner join tiposProcedimiento as tiposProcedimientoPadre on tiposProcedimientoPadre.idTipoProcedimiento = procedimientosPadre.idTipoProcedimiento
                where true  ";
        if($data['idProcedimiento']){
            $sql .= ' and procedimientos.idProcedimiento = '.$data['idProcedimiento'];
        }

        $sql .= " order by 1,2,3,4 ";

        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);die();
            $this->result->setData($rs);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }


}


// debug zone
if($_GET['debug'] == 'ProcedimientoVO' or false){
	//echo "DEBUG<br>";
	$kk = new ProcedimientoVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPago['valor'] = 5;
	$html = $kk->getPDF();
	echo $html; die();


	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
