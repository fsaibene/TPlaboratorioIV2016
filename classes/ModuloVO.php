<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class ModuloVO extends Master2 {
	public $idModulo = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $modulo = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "módulo",
                       ];
    public $nombreCorto = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "nombre corto",
                       ];
	public $icono = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "icono",
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
	public $superAdmin = ["valor" => FALSE,
						"obligatorio" => TRUE,
						"tipo" => "bool",
						"nombre" => "SUPER ADMIN",
	];
	public $visibleEnMenu = ["valor" => TRUE,
						"obligatorio" => TRUE,
						"tipo" => "bool",
						"nombre" => "Visible en el menú",
	];

    public function __construct(){
        parent::__construct();
        $this->setTableName('modulos');
        $this->setFieldIdName('idModulo');
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

	public function insertData(){
		//print_r($this); die('dos');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			$s = new SeccionVO();
			$s->idModulo['valor'] = $this->idModulo['valor'];
			$s->seccion['valor'] = '-';
			$s->habilitado['valor'] = TRUE;
			$s->orden['valor'] = 0;
			$s->insertData();
			if($s->result->getStatus()  != STATUS_OK) {
				//print_r($s); die('error uno');
				$this->result = $s->result;
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

    public function getComboList($data = null){
        $data['data'] = 'idModulo';
        $data['label'] = 'modulo';
        $data['orden'] = 'orden';
		$data['habilitado'] = 1;
        parent::getComboList($data); 
        return $this;
    }

    public function getModulos(){
	    try {
		    $sql = "select distinct m.*
					from ".$this->getTableName()." as m
					inner join secciones as c using (idModulo)
					inner join paginas as p using (idSeccion) ";
			if(!$_SESSION['usuarioLogueadoSuperAdmin']) {
				$sql .= ' inner join permisos as pe on pe.idPagina = p.idPagina and idUsuario = ' . $_SESSION['usuarioLogueadoIdUsuario'];
			}
			$sql .= ' where nombreCorto != "std" ';
		    if(!$_SESSION['usuarioLogueadoSuperAdmin']){
			    $sql .= ' and m.habilitado and m.visibleEnMenu and not m.superAdmin ';
		    }
		    $sql .= ' order by m.orden ';
		    //die($sql);
		    $ro = $this->conn->prepare($sql);
		    $ro->execute();
		    if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
			    $this->result->setData($rs);
		    } else {
			    $this->result->setStatus(STATUS_ERROR);
			    $this->result->setMessage('ERROR, contacte al administrador.');
		    }
	    }catch(Exception $e) {
		    $this->result->setStatus(STATUS_ERROR);
		    $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
		    myExceptionHandler($e);
	    }
	    //print_r($this); die();
	    return $this;
    }

}
if($_GET['debug'] == 'ModuloVO' or false){
	echo "DEBUG<br>";
	$kk = new ModuloVO();
	print_r($kk->getAllRows());
	//$kk->idModulo['valor'] = 4;
	//$kk->modulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>