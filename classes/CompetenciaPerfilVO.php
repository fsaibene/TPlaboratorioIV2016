<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class CompetenciaPerfilVO extends Master2 {
    public $idCompetenciaPerfil = ["valor" => "",
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
    public $competenciaPerfil = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "competencia y habilidad",
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
		$this->setTableName('competenciasPerfil');
		$this->setFieldIdName('idCompetenciaPerfil');
		$this->idAreaPerfil['referencia'] = new AreaPerfilVO();
	}
	
	/*
     * Competencia que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idCompetenciaPerfil';
      	$data['label'] = 'competenciaPerfil';
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
			$sql = 'select fp.idCompetenciaPerfil as data, fp.competenciaPerfil as label, eppfp.idEmpleadoPerfilPuestoCompetenciaPerfil
                    from competenciasPerfil as fp
                    left join empleadosPerfilPuesto_competenciasPerfil as eppfp on eppfp.idCompetenciaPerfil = fp.idCompetenciaPerfil ';
			if($data['idEmpleadoPerfilPuesto'])
				$sql .= ' and idEmpleadoPerfilPuesto = '.$data['idEmpleadoPerfilPuesto'];
			$sql .= ' where fp.habilitado';
			if($data['idAreaPerfil'])
				$sql .= ' and idAreaPerfil = '.$data['idAreaPerfil'];
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
if($_GET['debug'] == 'CompetenciaPerfilVO' or false){
	echo "DEBUG<br>";
	$kk = new CompetenciaPerfilVO();
	//print_r($kk->getAllRows());
	$kk->idCompetenciaPerfil = 116;
	$kk->competenciaPerfil = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>