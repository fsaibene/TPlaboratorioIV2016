<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class GeoPaisVO extends Master2 {
    public $idPais = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $pais = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "pais",
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
                       
	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('geoPaises');
		$this->setFieldIdName('idPais');
		$this->idPais['valor'] = 1372; // ARGENTINA
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
	public function getComboList(){
		$result = new Result();
      	$data['data'] = 'idPais';
      	$data['label'] = 'pais';
      	$data['orden'] = 'orden, pais';
   		$result = parent::getComboList($data); 
   		return $result;
	}
    
    public function getPaises(){
        $sql = "select idPais as data, pais as label
                from ".$this->getTableName()."
                order by orden asc, pais asc 
                ";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);
            $items = array();
            if($rs && count($rs) > 0) {
                foreach ($rs as $row) {
                    $items[] = array('id' => $row['data'], 'value' => $row['label'] ); 
                }  
            } 
            echo json_encode($items);  
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return ;
    }
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getPaises'){
    $aux = new GeoPaisVO();
    $aux->getPaises();
}

if($_GET['debug'] == 'GeoPaisVO' or false){
	echo "DEBUG<br>";
	$kk = new GeoPaisVO();
	//print_r($kk->getAllRows());
	$kk->idPais = 116;
	$kk->titulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>