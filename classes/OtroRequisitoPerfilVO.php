<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class OtroRequisitoPerfilVO extends Master2 {
    public $idOtroRequisitoPerfil = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
	public $idAreaPerfil = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "área de perfil",
						"referencia" => "",
	];
    public $otroRequisitoPerfil = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "otro requisito",
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
		$this->setTableName('otrosRequisitosPerfil');
		$this->setFieldIdName('idOtroRequisitoPerfil');
		$this->idAreaPerfil['referencia'] = new AreaPerfilVO();
	}
	
	/*
     * OtroRequisito que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idOtroRequisitoPerfil';
      	$data['label'] = 'otroRequisitoPerfil';
	    $data['orden'] = 'orden';
	    $data['nombreCampoWhere'] = 'habilitado';
	    $data['valorCampoWhere'] = '1';

	    parent::getComboList($data);
	    return $this;
	}

	public function getComboList2($data){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select orp.idOtroRequisitoPerfil as data, orp.otroRequisitoPerfil as label, epporp.idEmpleadoPerfilPuestoOtroRequisitoPerfil
                    from otrosRequisitosPerfil as orp
                    left join empleadosPerfilPuesto_otrosRequisitosPerfil as epporp on epporp.idOtroRequisitoPerfil = orp.idOtroRequisitoPerfil ';
			if($data['idEmpleadoPerfilPuesto'])
				$sql .= ' and idEmpleadoPerfilPuesto = '.$data['idEmpleadoPerfilPuesto'];
			$sql .= ' where orp.habilitado';
			if($data['idAreaPerfil'])
				$sql .= ' and idAreaPerfil = '.$data['idAreaPerfil'];
			//echo($sql);

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
if($_GET['debug'] == 'OtroRequisitoPerfilVO' or false){
	echo "DEBUG<br>";
	$kk = new OtroRequisitoPerfilVO();
	//print_r($kk->getAllRows());
	$kk->idOtroRequisitoPerfil = 116;
	$kk->otroRequisitoPerfil = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>