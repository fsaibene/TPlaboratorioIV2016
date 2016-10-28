<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoMovimientoEquipamientoVO extends Master2 {
    public $idTipoMovimientoEquipamiento = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
                                "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
                                ],
                             ];
    public $tipoMovimientoEquipamiento = ["valor" => "",
                            "obligatorio" => TRUE,
                            "tipo" => "string",
                            "nombre" => "tipo de movimiento",
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
		$this->setTableName('tiposMovimientoEquipamiento');
		$this->setFieldIdName('idTipoMovimientoEquipamiento');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idTipoMovimientoEquipamiento';
        $data['label'] = 'tipoMovimientoEquipamiento';
        $data['orden'] = 'orden';
        $result = parent::getComboList($data);
        return $result;
    }

	public function getTiposMovimientoEquipamiento($data, $format = null){
		$sql = 'select tipoMovimientoEquipamiento as label, idTipoMovimientoEquipamiento as data
				from tiposMovimientoEquipamiento
				where true ';
		if($this->habilitado['valor'])
			$sql .= ' and habilitado';
		$sql .= ' order by orden ';
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
					echo json_encode(array_map('setHtmlEntityDecode', $items));
					return;
				} else {
					$this->result->setData($rs);
				}
			} else {
				if($format == 'json') { // aunque no traiga nada debo devolver un array
					echo json_encode(array_map('setHtmlEntityDecode', $items));
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getTiposMovimientoEquipamiento'){
	$aux = new TipoMovimientoEquipamientoVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->getTiposMovimientoEquipamiento($data, 'json');
}

if($_GET['debug'] == 'TipoMovimientoEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new TipoMovimientoEquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoMovimientoEquipamiento = 116;
	$kk->tipoMovimientoEquipamiento = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>