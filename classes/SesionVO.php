<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class SesionVO extends Master2 {
	public $id = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $idSesion = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "sesion",
                       ];
    public $idUsuario = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "usuario",
                       ];
    public $device = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "dispositivo",
                       ];
    public $browserName = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "navegador nombre",
                       ];
    public $browserVersion = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "navegador version",
                       ];
    public $browserPlatform = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "navegador plataforma",
                       ];
    public $browserUserAgent = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "navegador user agent",
                       ];
    public $login = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "timestamp",
                       "nombre" => "login",
                       "validador" => ["admiteMenorAhoy" => FALSE, 
                                        "admiteHoy" => TRUE, 
                                        "admiteMayorAhoy" => TRUE,
                                        ],
                        ];
    public $ultimoAcceso = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "timestamp",
                       "nombre" => "ultimo acceso",
                       "validador" => ["admiteMenorAhoy" => FALSE, 
                                        "admiteHoy" => TRUE, 
                                        "admiteMayorAhoy" => TRUE,
                                        ],
                        ];
    
    public function __construct(){
        parent::__construct();
        $this->setTableName('sesiones');
        $this->setFieldIdName('id');
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
	
    public function insertData(){
        try{
            $this->idSesion['valor'] = session_id();
            $this->idUsuario['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];
            $this->device['valor'] = getDevice();
            $aux = getBrowser();
            $this->browserName['valor'] = $aux['name'];
            $this->browserVersion['valor'] = $aux['version'];
            $this->browserPlatform['valor'] = $aux['platform'];
            $this->browserUserAgent['valor'] = $aux['userAgent'];
            $this->login['valor'] = date("Y-m-d H:i:s");
            $this->ultimoAcceso['valor'] = $this->login['valor'];
            //print_r($this); die();
            parent::insertData();
        }catch(Exception $e){
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $this;
    }           
            
            
    public function updateData(){
        try{
            $this->getSesionPorSessionId();
            $this->ultimoAcceso['valor'] = date("Y-m-d H:i:s");
            parent::updateData();
        }catch(Exception $e){
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return $this;
    }

    /*
     * retorna la cantidad de usuario registrados en el sistema que no sean SuperAdmin
     */
    public function getCantidadUsuarios(){
        $sql = 'select COUNT(1) as cantidad
                from usuarios
                where superAdmin is false
                ';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->result->setData($rs['cantidad']);
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
     * retorna la cantidad de empleados registrados en el sistema
     */
    public function getCantidadEmpleados(){
        $sql = 'select COUNT(1) as cantidad
                from empleados
                ';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->result->setData($rs['cantidad']);
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
     * retorna la cantidad de services registrados en el sistema
     */
    public function getCantidadServices(){
        $sql = 'select COUNT(1) as cantidad
                from misServices
                ';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->result->setData($rs['cantidad']);
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
     * retorna la tasa de vehiculos por usuario registrados en el sistema
     */
    public function getTasaVehiculosPorUsuario(){
        try {
            $this->getCantidadVehiculos();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadVehiculos = $this->result->getData();
            }
            $this->getCantidadUsuarios();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadUsuarios = $this->result->getData();
            }
            if($cantidadUsuarios > 0){
                $tasa = number_format($cantidadVehiculos/$cantidadUsuarios, 2, ',', '.');
                $this->result->setData($tasa);
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
     * retorna la tasa de services por usuario registrados en el sistema
     */
    public function getTasaServicesPorUsuario(){
        try {
            $this->getCantidadServices();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadServices= $this->result->getData();
            }
            $this->getCantidadUsuarios();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadUsuarios = $this->result->getData();
            }
            if($cantidadUsuarios > 0){
                $tasa = number_format($cantidadServices/$cantidadUsuarios, 2, ',', '.');
                $this->result->setData($tasa);
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
     * retorna la tasa de services por vehiculos registrados en el sistema
     */
    public function getTasaServicesPorVehiculo(){
        try {
            $this->getCantidadServices();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadServices= $this->result->getData();
            }
            $this->getCantidadVehiculos();
            if($this->result->getStatus() == STATUS_OK && $this->result->getData()){
                $cantidadVehiculos = $this->result->getData();
            }
            if($cantidadVehiculos > 0){
                $tasa = number_format($cantidadServices/$cantidadVehiculos, 2, ',', '.');
                $this->result->setData($tasa);
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
     * esta funcion recupera de la db una sesion segun el session_id actual
     */
    public function getSesionPorSessionId(){
        $sql = "select * 
                from ".$this->getTableName()." 
                where idsesion = '".session_id()."'";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->mapData($rs);
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
         
    public function getSesionesPorDevices(){
        $sql = "select device, count(*) as cantidad
                from sesiones
                inner join usuarios as u using (idUsuario)
                where u.superAdmin = false
                group by device";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);
            //$items = array();
            $cont = count($rs);
            if($rs && $cont > 0) {
                $json = '[';
                foreach ($rs as $row) {
                    if($aux && $aux <= $cont) {
                        $json .= ', '; // agregamos esta linea porque cada elemento debe estar separado por una coma
                    }
                    $aux++;
                    $json .=  '{ "label" : "'.$row['device'].'", "value" : "'.$row['cantidad'].'"}';
                    //$items[] = $row['material']; 
                }  
                $json .= ']';
            } 
            echo $json;
            //echo json_encode($items);  
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return ;
    }   

    public function getHorasPorUsuario(){
        $sql = 'select usuario, sum(TIMESTAMPDIFF(MINUTE, login, ultimoAcceso)) as minutos
                from sesiones
                inner join usuarios as u using (idUsuario)
                where u.superAdmin = false
                group by idusuario
                order by minutos desc
                limit 10';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);
            //$items = array();
            $cont = count($rs);
            if($rs && $cont > 0) {
                $json = '[';
                foreach ($rs as $row) {
                    if($aux && $aux <= $cont) {
                        $json .= ', '; // agregamos esta linea porque cada elemento debe estar separado por una coma
                    }
                    $aux++;
                    $json .=  '{ "label" : "'.$row['usuario'].'", "value" : "'.$row['minutos'].'"}';
                    //$items[] = $row['material']; 
                }  
                $json .= ']';
            } 
            echo $json;
            //echo json_encode($items);  
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return ;
    }   
}

if($_GET['action'] == 'json' && $_GET['reporte'] ){ 
    $aux = new SesionVO();
    $reporte = 'get'.$_GET['reporte'];
    $aux->${reporte}();
}

if($_GET['debug'] == 'SesionVO' or false){
	echo "DEBUG<br>";
	$kk = new SesionVO();
	print_r($kk->getAllRows());
	//$kk->idSesion['valor'] = 4;
	//$kk->sesion = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>