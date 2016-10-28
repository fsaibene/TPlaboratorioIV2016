<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class PaginaVO extends Master2 {
	public $idPagina = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $path = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "path",
                       ];
    public $pagina = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "nombre de la página",
                       ];
    public $idSeccion = ["valor" => "",
				        "obligatorio" => FALSE,
				        "tipo" => "combo",
				        "nombre" => "sección",
				        "referencia" => "",
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
     public $ayuda = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "ayuda",
                       ];
    public $poseeAyuda = ["valor" => TRUE,
                       "obligatorio" => TRUE,
                       "tipo" => "bool",
                       "nombre" => "posee ayuda",
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
    public $poseeNotificacion = ["valor" => TRUE,
                       "obligatorio" => TRUE,
                       "tipo" => "bool",
                       "nombre" => "posee notificación",
                       ];

    public function __construct(){
        parent::__construct();
        $this->setTableName('paginas');
        $this->setFieldIdName('idPagina');
        $this->idSeccion['referencia'] =  new SeccionVO();
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
	
	public function getComboList(){
        $data['data'] = 'idPagina';
        $data['label'] = 'pagina';
        $data['orden'] = 'pagina';
        $data['nombreCampoWhere'] = 'habilitado';
        $data['valorCampoWhere'] = '1';
        
        parent::getComboList($data); 
        return $this;
    }

    public function getComboList2($data){
        try{
            $sql = 'SELECT getPagina(pa.idPagina) as label, pa.idPagina as data
                    from paginas as pa 
                    inner join secciones as s using (idSeccion)
                    inner join modulos as m using (idModulo)
					where true and pa.habilitado and s.habilitado and m.habilitado and not pa.superAdmin and pa.visibleEnMenu';
            if($data['valorCampoWhere']) {
                $sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
            }
            $sql .= ' order by m.orden, m.modulo, s.orden, s.seccion, pa.orden, pa.pagina ';
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

    /*
     * esta funcion recupera de la db una pagina segun la url en la que esta parado el usuario
     */
    public function getPaginaPorURL($idModulo = null){
	    //die('asd'.$idModulo);
	    $urlPathPagina = getUrlPathPagina();
		//die($urlPathPagina);
        $sql = "select p.*
                from ".$this->getTableName()." as p
                left join secciones as s on s.idseccion = p.idseccion
                where p.path = '".$urlPathPagina."'";
	    if($idModulo) {
		    $sql .= ' and idModulo = ' . $idModulo;
	    }
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->mapData($rs);
                foreach (getOnlyChildVars($this) as $atributo ) {
                    if(in_array($atributo, $this->getAtributosPermitidos())) {
                        if($this->{$atributo}['referencia'] && $this->{$atributo}['valor']) {
                            $this->{$atributo}['referencia']->{$this->{$atributo}['referencia']->getFieldIdName()}['valor'] = $this->{$atributo}['valor'];
                            //print_r($this->{$atributo}['referencia']);die();
                            $this->{$atributo}['referencia']->getRowById();
                        }
                    }
                }
                //print_r($this);
            } else {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage("ERROR, contacte al administrador.");
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
   /*
   * esta funcion devuelve todas las paginas que no estas
   */
    public function getPaginaSinProcedimiento($valor = NULL){
        //die('asd'.$idModulo);

        //die($valor);
        $sql = " SELECT  p.idPagina AS data , CONCAT_ws(' - ', m.modulo, if(s.seccion = '-', null, s.seccion), p.pagina) AS label
                FROM paginas as p
                INNER JOIN secciones as s using (idSeccion)
                INNER JOIN modulos as m using (idModulo)
                where p.idPagina not in (
                    select idPagina 
                    from planillasVersiones as p1
                    inner join planillas as p2 using(idPlanilla)
                    INNER JOIN procedimientos as p3 using(idProcedimiento)
                    where p3.habilitado and idPagina is not null ";
        if($valor) {
            $sql .= " AND p2.idProcedimiento != ".$valor;
		}
        $sql .= " ) and m.modulo != 'STD' and p.pagina not in ('inicio', 'tablero') and !p.superAdmin
                    order by m.orden, s.orden, p.orden ";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
            $this->result->setStatus(STATUS_OK);

        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        $this->result;
    }
}
if($_GET['debug'] == 'PaginaVO' or false){
	echo "DEBUG<br>";
	$kk = new PaginaVO();
	print_r($kk->getAllRows());
	//$kk->idPagina['valor'] = 4;
	//$kk->pagina = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>