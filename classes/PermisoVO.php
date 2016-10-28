<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.0
 * @created 24-oct-2014
 */
class PermisoVO extends Master2 {
	public $idPermiso = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
    public $idUsuario = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "usuario",
                       ];
    public $idPagina = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "pagina",
                       ];
    public $editaAyuda = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "bool",
                       "nombre" => "edita ayuda",
                       ];
                       
    public function __construct(){
        parent::__construct();
        $this->setTableName('permisos');
        $this->setFieldIdName('idPermiso');
    }
    
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
	
	public function getComboList($data = NULL){
	    //print_r($this);die();
	    //print_r($data);die();
        try{
            $sql = 'select p.idpagina, ps.idusuario, s.seccion, p.pagina, ps.idpermiso, ps.editaayuda, m.idModulo, m.modulo
                    from paginas as p
                    left join secciones as s using (idseccion)
                    left join modulos as m using (idmodulo)
                    left join permisos as ps on ps.idpagina = p.idpagina and ps.idusuario = '.$this->idUsuario['valor'];
	        $sql .= ' WHERE true';
            if($data)
                $sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
	        $sql .= ' and modulo != "STD" /*and p.path not like "Inicio_%"*/';
            $sql .= ' order by m.orden, s.orden, p.orden ';
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


    public function insertData($idUsuario, $paginasArray){
        try{
            $this->conn->beginTransaction();
            //saco la suscripción a notificaciones a las paginas a las que el usuario ya no tendría permiso.
            $notificacionSuscripcion = new NotificacionSuscripcionVO();
            $notificacionSuscripcion->deleteDataArray($idUsuario, $paginasArray);
            $sql = 'DROP TEMPORARY TABLE IF EXISTS permisos_temporary;
                    ';
            //echo($sql);
            $ro = $this->conn->prepare($sql);        
            if(!$ro->execute()){
                $this->conn->rollBack();
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                return $this;
            }
            $sql = 'CREATE TEMPORARY TABLE permisos_temporary SELECT * FROM permisos where idusuario = '.$idUsuario.' and editaayuda;
                    ';
            //echo($sql);
            $ro = $this->conn->prepare($sql);        
            if(!$ro->execute()){
                $this->conn->rollBack();
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                return $this;
            }
            $sql = 'delete from permisos where idusuario = '.$idUsuario.';
                    ';
            //echo($sql);
            $ro = $this->conn->prepare($sql);        
            if(!$ro->execute()){
                $this->conn->rollBack();
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                return $this;
            }
            $count = count($paginasArray);
            //echo $count; die();
            //print_r($paginasArray);die();
            for($i = 0; $i < $count; $i++) {
                $this->idPermiso = NULL; // si no pongo esto pincha luego del primer insert.
                $this->idUsuario['valor'] = $idUsuario;
                $this->idPagina['valor'] = $paginasArray[$i];
                //print_r($this);die();
                parent::insertData();
                //print_r($this);
                //die();
                if($this->result->getStatus() == STATUS_ERROR){
                    $this->conn->rollBack();
                    break;
                }
            }
            //print_r($this->result);die();
            if($this->result->getStatus() == STATUS_OK){
                $sql = 'update permisos as p, permisos_temporary as pt set p.editaayuda = true WHERE p.idusuario = pt.idusuario and p.idpagina = pt.idpagina;
                        ';
                //echo($sql);
                $ro = $this->conn->prepare($sql);        
                if(!$ro->execute()){
                    $this->conn->rollBack();
                    $this->result->setStatus(STATUS_ERROR);
                    $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                    return $this;
                } else {
                    $this->conn->commit();
                }
            }
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return $this;
        
        
        $result = new Result();
        $data['data'] = 'idPagina';
        $data['label'] = 'pagina';
        $data['orden'] = 'pagina';
        //$data['nombreCampoWhere'] = 'habilitado';
        //$data['valorCampoWhere'] = '1';
        
        $result = parent::getComboList($data); 
        return $result;
    }

    public function updateData($idUsuario, $permisosArray){
        try{
            $this->conn->beginTransaction();
            $sql = 'update permisos set editaayuda = false where idusuario = '.$idUsuario;
            //die($sql);
            $ro = $this->conn->prepare($sql);        
            if(!$ro->execute()){
                $this->conn->rollBack();
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                return $this;
            }
            $count = count($permisosArray);
            //echo $count; die();
            //print_r($paginasArray);die();
            for($i = 0; $i < $count; $i++) {
                $sql = 'update permisos set editaayuda = true where idpermiso = '.$permisosArray[$i];
                //die($sql);
                $ro = $this->conn->prepare($sql);        
                if(!$ro->execute()){
                    $this->conn->rollBack();
                    $this->result->setStatus(STATUS_ERROR);
                    $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                    return $this;
                }
            }
            $this->conn->commit();
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return $this;
        
        
        $result = new Result();
        $data['data'] = 'idPagina';
        $data['label'] = 'pagina';
        $data['orden'] = 'pagina';
        //$data['nombreCampoWhere'] = 'habilitado';
        //$data['valorCampoWhere'] = '1';
        
        $result = parent::getComboList($data); 
        return $result;
    }

    /*
     * esta funcion valida si existe o no el permiso para una pagina X y un usuario Y
     */
    public function validarPermiso($idUsuario, $idPagina){
        //$clave = md5($clave);
        $sql = 'select *
                from '.$this->getTableName().'
                inner join paginas as pa USING (idPagina)
                where idUsuario = '.$idUsuario.' and idPagina = '.$idPagina;
//        die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->mapData($rs);
            } else {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage("No posee permisos.");
            }
        }catch(Exception $e) {
	        //echo $sql;
	        //print_r($e);die();
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return $this;
    }
    
    /*
     * funcion que genera los permisos correspondientes cuando se registra un nuevo usuario
     */
    public function setPermisos($idUsuario){
        //$clave = md5($clave);
        $sql = "insert into ".$this->getTableName()." (idUsuario, idPagina, editaAyuda, idUsuarioLog) 
                select ".$idUsuario.", p.idpagina, 0, 1 
                from paginas as p
                inner join secciones as s using(idSeccion)
                where s.idSeccion != 4
                ";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');   
            myExceptionHandler($e);
        }
        return;
    }

    public function getReporte($data){
        //print_r($data); //die();
        $sql = 'SELECT u.usuario, getEmpleado(u.idEmpleado) as empleado, getPagina(pe.idPagina) as pagina, pa.idPagina, u.idUsuario, u.idEmpleado
                from permisos as pe
                inner join paginas as pa using (idPagina)
                inner join secciones as s using (idSeccion)
                inner join modulos as m using (idModulo)
                inner join usuarios as u using (idUsuario)';
        $sql .= ' where true ';
        if($data['idEmpleado']){
            $sql .= ' and u.idEmpleado = '.$data['idEmpleado'];
        }
        if($data['idUsuario']){
            $sql .= ' and pe.idUsuario = '.$data['idUsuario'];
        }
        if($data['idPagina']){
            $sql .= ' and pe.idPagina = '.$data['idPagina'];
        }
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);die();
            $this->result->setData($rs);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
}
if($_GET['debug'] == 'PermisoVO' or false){
	echo "DEBUG<br>";
	$kk = new PermisoVO();
	print_r($kk->getAllRows());
	//$kk->idPermiso['valor'] = 4;
	//$kk->permiso = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk;
}
?>