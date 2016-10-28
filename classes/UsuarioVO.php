<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class UsuarioVO extends Master2 {
	public $idUsuario = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
	                   ];
    public $superAdmin = ["valor" => "0",
                       "obligatorio" => FALSE,
                       "tipo" => "bool",
                       "nombre" => "superadmin",
                       ];
	public $usuario = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "nombre de usuario",
                       ];
	public $email = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "email",
                       "nombre" => "email",
                       ];
	public $clave = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "clave",
                       ];
	public $habilitado = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "bool",
                       "nombre" => "habilitado",
                       ];
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado",
		"referencia" => "",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('usuarios');
		$this->setFieldIdName('idUsuario');
		$this->idEmpleado['referencia'] = new EmpleadoVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        //$this->idUsuario['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];

        if($operacion != DELETE) {
            if ($operacion == INSERT) {
            }
            if (strlen($this->clave['valor']) < 6) {
                $resultMessage = 'La clave debe contener como mínimo 6 caracteres.';
            }
        }
        return $resultMessage;
 	}

	public function getComboList2($data = null){
		try{
			$sql = 'SELECT getUsuario(u.idUsuario) as label, u.idUsuario as data
                    from usuarios as u 
					where true and u.habilitado and not u.superAdmin ';
			if($data['valorCampoWhere']) {
				$sql .= ' and '.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
			}
			$sql .= ' order by label ';
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
	
    public function getNombreCompleto(){
	    //$clave = md5($clave);
	    $sql = 'select getEmpleado('.$this->idEmpleado['valor'].') as nombreCompleto';
	    //die($sql);
	    try {
		    $ro = $this->conn->prepare($sql);
		    $ro->execute();
		    if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
			    $nombreCompleto = $rs['nombreCompleto'];
		    }
	    }catch(Exception $e) {
		    $this->result->setStatus(STATUS_ERROR);
		    $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
		    myExceptionHandler($e);
	    }
	    //print_r($this); die();
	    return $nombreCompleto;
    }
    
    public function getIniciales(){
	    //return 'iniciales';
        return strtoupper(substr($this->nombres['valor'],0,1).substr($this->apellido['valor'],0,1));
    }
    
    public function getAvatar(){
        if(file_exists(getFullPath().'/files/avatars/'.$this->idUsuario['valor'].'.jpg')){
            return $this->idUsuario['valor'].'.jpg';
        } else {
            return 'nobody.png';
        }
    }

    /*
     * funcion que retorna un array que permite armar el menu del sistema de manera dinamica
     */ 
    public function getMenu($path = null, $idModulo = null){
        $sql = 'select m.idModulo, p.idSeccion, seccion, s.icono as iconoSeccion, p.idPagina, pagina, p.icono as iconoPagina, p.path as pathPagina, poseeAyuda,
                    p.superAdmin, p.orden
                from paginas as p
                inner join secciones as s using (idSeccion)
                inner join modulos as m using (idModulo)';
	    if(!$_SESSION['usuarioLogueadoSuperAdmin']){
		    $sql .= ' inner join permisos as ps on ps.idPagina = p.idPagina and ps.idUsuario = '.$this->idUsuario['valor'];
        }
        $sql .= ' where p.visibleEnMenu and p.habilitado and (s.idSeccion is null or (s.idSeccion != 24 and s.idSeccion != 38 and s.idSeccion != 50))';
	    if($idModulo){
		    $sql .= ' and idModulo = '.$idModulo;
	    } else if($path) {
		    $sql .= ' and idModulo = (
		                    select idModulo
		                    from modulos
		                    inner join secciones using (idmodulo)
		                    inner join paginas using (idseccion)
		                    where paginas.path = "'.$path.'"
		            )';
	    }

	    $sql .= ' order by m.orden, s.orden, p.orden';
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

    /*
     * funcion que retorna un array que permite armar el menu del sistema de manera dinamica
     * para la parte de empleado
     * menu que se muestra si se seleccionó un empleado en el Selector de Empleados.
     * debe existir la variable de sessión $_SESSION['selectorEmpleadoIdEmpleado']
     */
    public function getMenuEmpleados($idModulo = null){
        $sql = 'select m.idModulo, p.idSeccion, seccion, s.icono as iconoSeccion, p.idPagina, pagina, p.icono as iconoPagina, p.path as pathPagina, poseeAyuda,
                    p.superAdmin, p.orden
                from paginas as p
                left join secciones as s using (idseccion)
                left join modulos as m using (idmodulo)';
	    if(!$_SESSION['usuarioLogueadoSuperAdmin']){
		    $sql .= ' inner join permisos as ps on ps.idPagina = p.idPagina and ps.idUsuario = '.$this->idUsuario['valor'];
	    }
	    $sql .= ' where p.visibleEnMenu and (s.idSeccion = 24 or s.idSeccion = 38 or s.idSeccion = 50)';
	    if($idModulo){
		    $sql .= ' and idModulo = '.$idModulo;
	    }
        $sql .= ' order by m.orden, s.orden, p.orden';
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

     public function insertData(){
        try{
	        $this->clave['valor'] = getAleatoryKey(8);
            $this->businessLogic(INSERT);
            if($this->result->getStatus () != STATUS_OK) return $this;

            // primero chequeo que el usuario no se haya registrado ya con ese mail
            $usuarioAux = new UsuarioVO();
            $usuarioAux->email['valor'] = $this->email['valor'];
            $usuarioAux->getUsuarioByEmail();
            if($usuarioAux->idUsuario['valor']) {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('Ya existe una cuenta con ese E-mail.');
                return $this;
            }
            // primnero chequeo que el usuario no se haya registrado ya con ese usuario
            $usuarioAux = new UsuarioVO();
            $usuarioAux->usuario['valor'] = $this->usuario['valor'];
            $usuarioAux->getUsuarioByUsuario();
            if($usuarioAux->idUsuario['valor']) {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('Ya existe una cuenta con ese Usuario.');
                return $this;
            }
            //print_r($this);die();
            // si no existe lo inserto en la db
            //$this->conn->beginTransaction();
            //$clave = $this->clave['valor'];
            $sql = "insert into ".$this->getTableName()." (usuario, email, clave, habilitado, superAdmin, idEmpleado, idUsuarioLog) values
                    ('".$this->usuario['valor']."', '".$this->email['valor']."', '".md5($this->clave['valor'])."',
                    ".$this->habilitado['valor'].",".$this->superAdmin['valor'].", ".$this->idEmpleado['valor'].", 1)
                    ";
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $this->{$this->getFieldIdName()}['valor'] = $this->conn->lastInsertId();
            //die();
            if($this->result->getStatus() == STATUS_ERROR){
                $this->conn->rollBack();
                return $this;
            }
            $this->result->setStatus(STATUS_OK);
            $this->result->setMessage("El registro fue GUARDADO con éxito.");
            // ahora le mando el mail con los datos y el codigo para que active la cuenta
            /*$this->confirmRegisterByEmail($clave);
            if($this->result->getStatus() == STATUS_ERROR){
                $this->conn->rollBack();
                return $this;
            }*/
            //$this->conn->commit();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }     
     
    /*
     * funcion que se ejecuta cuando un usuario intenta loguearse en el sistema
     */
    public function validarLogin($usuario, $clave, $hash){
        //$clave = md5($clave);
        $sql = "select u.*, za.idZonaAfectacion
                from usuarios as u
				left join empleados as e using (idEmpleado)
				left join empleadosRelacionLaboral as erl using (idEmpleado)
				left join zonasAfectacion as za using (idZonaAfectacion)
                where usuario = '".mysql_escape_mimic($usuario)."' and u.habilitado";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                //print_r($rs); echo $clave; echo '<br>'; echo $hash; die();
                if($clave == md5($hash.$rs['clave'])){
                    $this->result->setData($rs);
                } else {
                    $this->result->setStatus(STATUS_ERROR);
                    $this->result->setMessage('Verifique los datos ingresados.');
                }
            } else {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage('No existe el usuario o no se encuentra habilitado.');
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $this;
    }

	public function cambioDeClave($data){
        if($data['claveActual'] == '' || $data['claveNueva'] == ''  || $data['repitaClaveNueva'] == ''){
            $resultMessage = 'Debe completar todos los campos.';
        }
        else if ($data['claveNueva'] != $data['repitaClaveNueva']) {
            $resultMessage = 'Las claves nuevas no coinciden.';
        }
        if($resultMessage){
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage($resultMessage);
            return $this;
        }
        try {
            $sql = 'select clave 
                    from usuarios
                    where idusuario = '.$this->idUsuario['valor'];
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetch();
            if ($rs['clave'] != md5($data['claveActual'])) {
                $resultMessage = 'La clave actual no coincide.';
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage($resultMessage);
                return $this;
            }
            
            $sql = "update usuarios set clave = '".md5($data['claveNueva'])."'
                    where idusuario = ".$this->idUsuario['valor'];
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $this->result->setStatus(STATUS_OK);
            $this->result->setMessage('La clave fue cambiada con EXITO.');
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }

    /*
     * esta funcion envia un correo con una nueva clave aleatoria al usuario que la solicita
     */
    public function recuperarClave(){
        //print_r($this); die();
        try{
	        $this->getRowById();
            //fc_print($this);
	        if($this->result->getStatus() != STATUS_OK){
		        $this->result->setStatus(STATUS_ERROR);
		        $this->result->setMessage("ERROR, contacte al administrador.");
		        return $this;
	        }
	        $clave = getAleatoryKey(8);  // generar una clave aleatoria
            $this->clave['valor'] = md5($clave);
	        $sql = "update usuarios set clave = '".$this->clave['valor']."'
                    where idusuario = ".$this->idUsuario['valor'];
	        $ro = $this->conn->prepare($sql);
	        $ro->execute();

            $link = getPath().'/login.php';
            $titulo = "Mensaje de ".PRODUCTO." - ".CLIENTE;
            $mensaje = "<p>Te hemos generado una nueva clave para ingresar al sistema.</p>
                        <p>Para ingresar a ".PRODUCTO." hacé clic <a href='".$link."'>aquí</a>.</p>
                        <p><b>Usuario:</b> ".$this->usuario['valor']."<br>
                        <b>Clave:</b> ".$clave."</p>
                        <p>Una vez dentro del sistema podrás cambiar tu clave.</p>
                        ";
            $body = bodyEmail($titulo, $mensaje);
            $body = preg_replace('/\\\\/','', $body); //Strip backslashes

	        $mail = new PHPMailer();
	        //$mail->SMTPDebug  = 1;
	        $mail->CharSet = 'UTF-8';
	        $mail->IsSMTP();
	        $mail->Port = 587;
	        $mail->IsHTML(true);                          // send as HTML
	        $mail->Host = "mail.sinec.com.ar";
	        $mail->SMTPAuth = true;                       // turn on SMTP authentication
	        $mail->Username = 'soportesigi';        // SMTP username
	        $mail->Password = "sopsigsi";
            
            $mail->AddReplyTo(SOPORTE_MAIL.'@'.DOMINIO);
            $mail->SetFrom(SOPORTE_MAIL.'@'.DOMINIO, CLIENTE.' - Soporte');
            
            $mail->AddAddress($this->email['valor']);
            
            $mail->Subject  = CLIENTE.' - Datos de acceso';
            $mail->MsgHTML($body);
			//fc_print($mail, true);
            for($i=0; $i<5; $i++){
                if($mail->Send()) {
                    $this->result->setStatus(STATUS_OK);
                    $this->result->setMessage('Se envió un E-mail con los datos de acceso a '.CLIENTE.'. Si no visualizas el correo en la bandeja de entrada, recuerda revisar la carpeta de SPAM.');
                    $flag = true;
                    break;
                } else {
                    sleep(1);
                }
            }
            //fc_print($mail, true);
            if(!$flag) {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage("ERROR, no se pudo enviar el E-mail. Reintente nuevamente o contacte al administrador.");
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $this;
    }

    /*
     * esta funcion recupera de la db el usuario segun un email
     */
    public function getUsuarioByEmail(){
        $sql = "select *
                from ".$this->getTableName()." 
                where email = '".$this->email['valor']."'";
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
        //print_r($this); die();
        return $this;
    }
    /*
     * esta funcion recupera de la db el usuario segun un nombre de usuario
     */
    public function getUsuarioByUsuario(){
        $sql = "select *
                from ".$this->getTableName()."
                where usuario = '".$this->usuario['valor']."'";
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
        //print_r($this); die();
        return $this;
    }
}

// debug zone
if($_GET['debug'] == 'UsuarioVO' or false){
	echo "DEBUG<br>";
	$kk = new UsuarioVO();
	//print_r($kk->getAllRows());
	$kk->idUsuario = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
