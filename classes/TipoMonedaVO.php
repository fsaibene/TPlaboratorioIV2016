<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoMonedaVO extends Master2 {
    public $idTipoMoneda = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $tipoMoneda = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "moneda",
    ];
    public $tipoMonedaPlural = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "moneda (plural)",
    ];
    public $simbolo = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "simbolo",
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
		$this->setTableName('tiposMoneda');
		$this->setFieldIdName('idTipoMoneda');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoMoneda';
        $data['label'] = 'concat(tipoMoneda, " (", simbolo, ")")';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }

	public function getTiposMoneda($data = null, $format = null){
		$sql = 'select idTipoMoneda as id, concat(tipoMoneda, " (", simbolo, ")") as value
				from tiposMoneda
				where habilitado = '.$this->habilitado['valor'].'
				order by orden, tipoMoneda
				';
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
						$items[] = array('id' => $row['id'], 'value' => $row['value']);
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
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return ;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getTiposMoneda'){
	$aux = new TipoMonedaVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->getTiposMoneda($data, 'json');
}

if($_GET['debug'] == 'TipoMonedaVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoMonedaVO();
	//print_r($kk->getAllRows());
	$kk->idTipoMoneda = 116;
	$kk->tipoMoneda = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>