<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class NotificacionSuscripcionVO extends Master2 {
    public $idNotificacionSuscripcion = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idPagina = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "pagina",
        "referencia" => "",
    ];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('notificacionesSuscripcion');
	    $this->setFieldIdName('idNotificacionSuscripcion');
	    $this->idPagina['referencia'] = new PaginaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

    public function getRowsParaVista(){
	    if($_SESSION['usuarioLogueadoSuperAdmin']){
		    $sql = 'select getPagina(p.idPagina) as label, p.idPagina as data,  ns.escritorio, ns.mailDiario, ns.mailSemanal
				from paginas as p
				left join notificacionesSuscripcion ns ON p.idPagina = ns.idPagina AND ns.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].'
				where p.pagina not in ("inicio", "tablero")
				and p.poseeNotificacion
				';
	    } else {
	        $sql = 'select getPagina(p.idPagina) as label, p.idPagina as data,  ns.escritorio, ns.mailDiario, ns.mailSemanal
	                from permisos as ps
	                inner join paginas as p using (idPagina)
	                left join secciones as s using (idseccion)
	                left join modulos as m using (idModulo)
	                left join notificacionesSuscripcion ns ON ps.idPagina = ns.idPagina AND ps.idUsuario = ns.idUsuario
	        		where ps.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].'
	        		and p.poseeNotificacion
                    and p.pagina not in ("inicio", "tablero") -- "inicio", "tablero"
					and s.idSeccion not in (24) -- "empleados"
					and m.idModulo not in (5, 6) -- "STD", "soporte técnico"
	                ';
	    }
        //die($sql);
        try{
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

    public function deleteDataArray($idUsuario, $paginas=null){
        //se borran todas las suscripciones del usuario.
        $sql = 'delete from '.$this->getTableName().' where idUsuario = '.$idUsuario;
        if ($paginas){
            $sql .= ' and idPagina not in ('.implode(",", $paginas).')';
        }

        //$sql .= ' and idPagina not in ('.implode(",", $idEscritorioArray).', '.implode(",", $idMailDiario).', '.implode(",", $idMailSemanal).')';
        //die($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if(!$ro->execute()){
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

    public function insertDataArray($idUsuario, $idPaginaArray, $idEscritorioArray, $idMailDiario, $idMailSemanal){
        try{
            foreach($idPaginaArray as $idPagina){
                //la query se arma en forma dinámica según si están tildada cada opción.
                if($idEscritorioArray AND in_array($idPagina, $idEscritorioArray))                {
                    $sqlEscritorio = 'escritorio = 1,';
                }
                else{
                    $sqlEscritorio = '';
                }
                if($idMailDiario AND in_array($idPagina, $idMailDiario)){
                    $sqlMailDiario = 'mailDiario = 1,';
                }
                else{
                    $sqlMailDiario = '';
                }
                if($idMailSemanal AND in_array($idPagina, $idMailSemanal)){
                    $sqlMailSemanal = 'mailSemanal = 1,';
                }
                else{
                    $sqlMailSemanal = '';
                }

                if ($sqlEscritorio != '' || $sqlMailDiario !='' || $sqlMailSemanal != ''){
                    $sql = 'insert ignore into '.$this->getTableName().' set idUsuario = '.$idUsuario.', idPagina = '.$idPagina.', ';
                    $sql .= $sqlEscritorio;
                    $sql .= $sqlMailDiario;
                    $sql .= $sqlMailSemanal;
                    $sql .= 'idUsuarioLog = '.$_SESSION['usuarioLogueadoIdUsuario'].', fechaLog = CURRENT_TIMESTAMP';
                    //die($sql);
                    $ro = $this->conn->prepare($sql);
                    if(!$ro->execute()){
                        $this->result->setStatus(STATUS_ERROR);
                        $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
                        break;
                    }
                }
            }
            $this->result->setMessage("Los datos fueron GUARDADOS con éxito.");
        }catch(Exception $e){
            $this->result->setData($this);
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }
}

// debug zone
if($_GET['debug'] == 'royectoServicioTareaVO' or false){
    echo "DEBUG<br>";
    $kk = new royectoServicioTareaVO();
    //print_r($kk->getAllRows());
    $kk->idroyectoServicioTarea = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
