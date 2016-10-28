<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoProcedimientoVO extends Master2 {
    public $idTipoProcedimiento = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoProcedimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "tipo de Procedimiento",
    ];

    public $sigla = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "Sigla",
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
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
		$this->setTableName('tiposProcedimiento');
		$this->setFieldIdName('idTipoProcedimiento');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoProcedimiento';
        $data['label'] = 'concat_ws(" - ", sigla, tipoProcedimiento)';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }
    public function getTipoProcedimiento($data = null, $format = null){
	    if(!$data){
		    $data['label'] = 'concat_ws(" - ", sigla, tipoProcedimiento)';
		    $data['data'] = 'idTipoProcedimiento';
		    $data['orden'] = 'orden';
	    }
        $sql = 'select '.$data['label'].' as label, '.$data['data'].' as data
                from '.$this->getTableName().' as e';
        $sql .= ' where true ';
        if($this->habilitado['valor'])
            $sql .= ' and e.habilitado';
        //$sql .= ' group by data ';
        $sql .= ' order by '.$data['orden'];
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



if($_GET['action'] == 'json' && $_GET['type'] == 'getTipoProcedimiento'){
    $aux = new TipoProcedimientoVO();
    $aux->habilitado['valor'] = $_GET['habilitado'];
    /*$data['data'] = 'idTipoProcedimiento';
    $data['label'] = 'tipoProcedimiento';
    $data['orden'] = 'orden';*/
    $aux->getTipoProcedimiento($data, 'json');
}

if($_GET['debug'] == 'TipoProcedimientoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoProcedimientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoProcedimiento = 116;
	$kk->tipoProcedimiento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>