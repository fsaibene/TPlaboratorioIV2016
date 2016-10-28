<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class SeccionVO extends Master2 {
	public $idSeccion = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idModulo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "módulo",
						"referencia" => "",
	];
    public $seccion = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "seccion",
                       ];
    public $icono = ["valor" => "", 
                       "obligatorio" => false,
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

    public function __construct(){
        parent::__construct();
        $this->setTableName('secciones');
        $this->setFieldIdName('idSeccion');
	    $this->idModulo['referencia'] =  new ModuloVO();
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
	
    public function getComboList(){
        $data['data'] = 'idSeccion';
        $data['label'] = 'seccion';
        $data['orden'] = 'orden';

        parent::getComboList($data); 
        return $this;
    }

	public function getComboList2(){
		try{
			/*$sql = 'select * from (
						select idSeccion as data, CONCAT(m.modulo, " / ", s.seccion) as label, s.orden as orden_seccion, m.orden as orden_modulo
						from secciones as s
						left join modulos as m using (idModulo)
						union
						select null, modulo as label, null as orden_seccion, orden as orden_modulo
						from modulos
					) as asd
					order by orden_modulo, orden_seccion
                ';*/
			$sql = 'select idSeccion as data, CONCAT(m.modulo, " / ", s.seccion) as label, s.orden as orden_seccion, m.orden as orden_modulo
					from secciones as s
					inner join modulos as m using (idModulo)
					order by modulo, seccion
                ';
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
		return $this->result;
	}
}
if($_GET['debug'] == 'SeccionVO' or false){
	echo "DEBUG<br>";
	$kk = new SeccionVO();
	print_r($kk->getAllRows());
	//$kk->idSeccion['valor'] = 4;
	//$kk->seccion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>