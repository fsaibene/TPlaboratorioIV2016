<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ElementoProteccionPersonalVO extends Master2 {
    public $idElementoProteccionPersonal = ["valor" => "",
					        "obligatorio" => FALSE,
					        "tipo" => "integer",
					        "nombre" => "id",
					        "validador" => ["admiteMenorAcero" => FALSE,
					            "admiteCero" => FALSE,
					            "admiteMayorAcero" => TRUE,
					            ],
    ];
    public $elementoProteccionPersonal = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "Elemento de Protección Personal",
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
		$this->setTableName('elementosProteccionPersonal');
		$this->setFieldIdName('idElementoProteccionPersonal');
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    public function getComboList(){
        $result = new Result();
        $data['data'] = 'idElementoProteccionPersonal';
        $data['label'] = 'elementoProteccionPersonal';
        $data['orden'] = 'orden';
        $data['nombreCampoWhere'] = 'habilitado';
        $data['valorCampoWhere'] = true;
        $result = parent::getComboList($data);
        return $result;
    }

	public function getComboList3($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select epp.idElementoProteccionPersonal, epp.elementoProteccionPersonal, eepp.fechaEntrega
                    from elementosProteccionPersonal as epp
                    left join empleadosElementosProteccionPersonal as eepp on eepp.idElementoProteccionPersonal = epp.idElementoProteccionPersonal';
			if($data['fechaEntrega'])
				$sql .= ' and eepp.fechaEntrega = "'.$data['fechaEntrega'].'"';
			$sql .= ' where epp.habilitado';
			$sql .= ' order by epp.elementoProteccionPersonal';
			//die($sql);

			$ro = $this->conn->prepare($sql);
			if($ro->execute()){
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				$this->result->setData($rs);
				$this->result->setStatus(STATUS_OK);
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
		return $this;
	}
}

if($_GET['debug'] == 'ElementoProteccionPersonalVO' or false){
	echo "DEBUG<br>";
	$kk = new ElementoProteccionPersonalVO();
	//print_r($kk->getAllRows());
	$kk->idElementoProteccionPersonal = 116;
	$kk->elementoProteccionPersonal = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>