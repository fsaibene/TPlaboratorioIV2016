<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
include_once('TipoBancoVO.php');
include_once('TipoBancoMovimientoVO.php');

/**
 * Created by PhpStorm.
 * User: German
 * Date: 28/08/2016
 * Time: 16:11
 */
class TipoBancoConceptoVO extends Master2 {
    public $idTipoBancoConcepto = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoBancoConcepto = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "tipo de concepto",
    ];
    public $idTipoBanco = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "banco",
        "referencia" => "",
    ];
    public $idTipoBancoMovimiento = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "movimiento",
        "referencia" => "",
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
		$this->setTableName('tiposBancoConcepto');
		$this->setFieldIdName('idTipoBancoConcepto');
		$this->idTipoBanco['referencia'] = new TipoBancoVO();
		$this->idTipoBancoMovimiento['referencia'] = new TipoBancoMovimientoVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoBancoConcepto';
        $data['label'] = 'tipoBancoConcepto';
        $data['orden'] = 'tipoBancoConcepto';
        $result = parent::getComboList($data);
        return $result;
    }

    public function getIdTipoBancoConcepto ($idTipoBanco, $tipoBancoConcepto){
        $sql = 'select idTipoBancoConcepto, idTipoBancoMovimiento
				from tiposBancoConcepto
				where tipoBancoConcepto = '."'".$tipoBancoConcepto."'".' and idTipoBanco = '."'".$idTipoBanco."'";
        //echo($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if($ro->execute()){
                $rs = $ro->fetch(PDO::FETCH_ASSOC);
                if ($rs['idTipoBancoMovimiento']){
                    $this->result->setData($rs);
                    $this->result->setStatus(STATUS_OK);
                }else{
                    $this->result->setData($this);

                    //si no existe... pongo STATUS_ERROR para que luego se haga el insert.
                    if(!$rs['idTipoBancoConcepto']) {
                        $this->result->setStatus(STATUS_ERROR);
                        $this->result->setMessage('ERROR: No se encontró el concepto "' . $tipoBancoConcepto . '".');
                    }
                    //si existe pero no está completo el movimiento... pongo STATUS_ADVERTENCIA para que no se haga el insert, pero muestre la alerta.
                    else if(!$rs['idTipoBancoMovimiento']){
                        $this->result->setStatus(STATUS_ADVERTENCIA);
                        $this->result->setMessage('ERROR: El concepto "'.$tipoBancoConcepto.'" no está asociado a ningún Movimiento.');
                    }
                }
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
        return $rs;
    }

    public function insertTipoBancoConcepto($idTipoBanco, $tipoBancoConcepto)
    {
	    $this->idTipoBanco['valor'] = $idTipoBanco;
	    $this->tipoBancoConcepto['valor'] = $tipoBancoConcepto;

	    $this->result = new Result();
	    $this->setHasNotification(true); //reseteo el result para que no me tome el resultado anterior.
	    $this->insertData();

	    return $this;
    }
}

if($_GET['debug'] == 'TipoBancoConceptoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoBancoConceptoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoBancoConcepto = 116;
	$kk->tipoBancoConcepto = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>