<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * Created by PhpStorm.
 * User: German
 * Date: 08/02/2016
 * Time: 19:33
 */
class NotificacionVO extends Master2 {
    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
    }

    //obtiene las notificaciones de escritorio pendientes de mostrar para el usuario logueado.
    public function getNotificaciones(){
    	//echo 'a'.$_SESSION['usuarioLogueadoIdUsuario'].'a';
        if($_SESSION['usuarioLogueadoIdUsuario']){ // agrego esta validación porque esta funcion la llama un script js que no valida la perdida de sesion
            $sql = 'SELECT idNotificacion, pagina, seccion, modulo, n.fechaCarga as fecha, m.icono, u.usuario as usuarioCarga
	                FROM notificaciones n
	                LEFT JOIN paginas p USING (idPagina)
	                LEFT JOIN secciones s using (idSeccion)
	                LEFT JOIN modulos m using (idModulo)
	                LEFT JOIN usuarios u ON u.idUsuario = n.idUsuarioCarga
	                WHERE n.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].' AND escritorio AND not notificadoEscritorio';
            //die($sql);
	        try {
	            $ro = $this->conn->prepare($sql);
		        //fc_print($ro);
	            $ro->execute();
		        $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
		        //fc_print($rs, true);
		        $this->result->setData($rs);
	        }catch(Exception $e) {
	            $this->result->setStatus(STATUS_ERROR);
	            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
	            myExceptionHandler($e);
	        }
        }
        //print_r($sql); die();
        return $this;
    }

    //obtiene las notificaciones que se mostrarán en el listado que se encuentra en el header.
    //trae las que son notificaciones de escritorio y ya han sido notificadas
    public function getListadoNotificaciones(){
        $sql = 'SELECT pagina, notificadoListado, n.fechaCarga as fecha, m.icono, u.usuario as usuarioCarga
                FROM notificaciones n
                LEFT JOIN paginas p USING (idPagina)
                LEFT JOIN secciones s using (idSeccion)
                LEFT JOIN modulos m using (idModulo)
                LEFT JOIN usuarios u ON u.idUsuario = n.idUsuarioCarga
                WHERE n.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].' AND escritorio and notificadoEscritorio
                ORDER BY n.fechaCarga DESC
                LIMIT 10';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //obtiene el historial de las notificaciones para mostrarlas en "Mis Notificaciones" en Perfil.php.
    public function getHistorialNotificaciones(){
        $sql = 'SELECT u.usuario, pagina, seccion, modulo, n.fechaCarga as fechaIngreso
                FROM notificaciones n
                LEFT JOIN paginas p USING (idPagina)
                LEFT JOIN secciones s using (idSeccion)
                LEFT JOIN modulos m using (idModulo)
                LEFT JOIN usuarios u ON u.idUsuario = n.idUsuarioCarga
                WHERE n.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].' AND escritorio
                ORDER BY n.fechaCarga DESC
                ';
//        echo $sql;
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }
    public function getListadoDatosCargados($data = null){ //Esta func recibe un parametro para el listado asincronico, si tiene datos hace la consulta con la base, si es null trae los parametros y nombres del columnas para el datatable
        if($data) {
            $sql = 'SELECT u.usuario, pagina, seccion, modulo, n.fechaCarga as fechaCarga, DATE_FORMAT(n.fechaCarga,"%d/%m/%Y %H:%i:%S" ) as fechaCargaES
                FROM notificaciones n
                LEFT JOIN paginas p USING (idPagina)
                LEFT JOIN secciones s using (idSeccion)
                LEFT JOIN modulos m using (idModulo)
                LEFT JOIN usuarios u ON u.idUsuario = n.idUsuarioCarga
                WHERE n.idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].' AND escritorio
                ORDER BY n.fechaCarga DESC
                ';
            $data = getDataTableSqlDataFilter($data, $sql);
            if ($data['data']) {
                //DATOS
                foreach ($data['data'] as $row) {
                    $auxRow['detallePagina'] = 'Nuevo registro en el módulo <b>'.$row['modulo'].'</b>'; $auxRow['detallePagina'] .= ($row['seccion'] != '-')? ', sección <b>'.$row['seccion'].'</b>' : ''; $auxRow['detallePagina'] .= ', página <b>'.$row['pagina'].'</b>';
                    $auxRow['pagina'] = $row['pagina'];
                    $auxRow['seccion'] = $row['seccion'];
                    $auxRow['modulo'] = $row['modulo'];
                    $auxRow['usuario'] = $row['usuario'];
                    $auxRow['fechaCarga'] = $row['fechaCarga'];
                    $auxRow['fechaCargaES'] = $row['fechaCargaES'];
                    $auxData[] = $auxRow;
                }
                $data['data'] = $auxData;
            }
            echo json_encode($data);
        } else { // si es NULL devuelve los parametros del datatable
                //Parametros columnas
            $objColumns = null;   //Nombre: Indica nombre de los campos que se mostraran en el excel y el Datatable
            $objColumns[] = array('nombre' => 'Detalle página',  'bVisible' => true,    'className' => false,       'aDataSort' => 'pagina',    'bSortable' => true,    'searchable' => false,  'exportable' => false);   //className: Indica de que lado estara ordenado el elmento de esa columna
            $objColumns[] = array('nombre' => 'pagina',          'bVisible' => false,   'className' => false,       'aDataSort' => false,       'bSortable' => true,    'searchable' => true,   'exportable' => false);   //bVIsible:Indica si la columna se muestra
            $objColumns[] = array('nombre' => 'seccion',         'bVisible' => false,   'className' => false,       'aDataSort' => false,       'bSortable' => false,   'searchable' => true,   'exportable' => false);   //bSorteable: Indica si se podra ordenar por esa columna
            $objColumns[] = array('nombre' => 'modulo',          'bVisible' => false,   'className' => false,       'aDataSort' => false,       'bSortable' => false,   'searchable' => true,   'exportable' => false);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
            $objColumns[] = array('nombre' => 'usuario',         'bVisible' => true,    'className' => false,       'aDataSort' => false,       'bSortable' => true,    'searchable' => true,   'exportable' => false);
            $objColumns[] = array('nombre' => 'fecha IngresoEN', 'bVisible' => false,   'className' => false,       'aDataSort' => false,       'bSortable' => true,    'searchable' => true,   'exportable' => false);
            $objColumns[] = array('nombre' => 'fecha Ingreso',   'bVisible' => true,    'className' => 'right',     'aDataSort' => 'fecha IngresoEN',       'bSortable' => true,    'searchable' => true,   'exportable' => false);

            $listadoDatosCargados['columnas'] = $objColumns;
            $columnNames = array('detallePagina','pagina','seccion','modulo','usuario','fechaCarga','fechaCargaES');//muy importante el orden
            //Debe tener el mismo orden y nombre que tienen en el llamado asincrono

            foreach ($listadoDatosCargados['columnas'] as $campo) {
                $campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
            }
            for ($i = 0; $i < count($listadoDatosCargados['columnas']); $i++) {
                if ($listadoDatosCargados['columnas'][$i]['bVisible'] == false) {//Se carga un array con los campos que nos seran visibles
                    $bVisible[] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['bSortable'] == false) {//Se carga un array con los campos que nos seran ordebables
                    $bSortable[] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['searchable'] == false) {//Se carga un array con los campos que no seran searcheables
                    $searchable[] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['className'] == 'left') {//Se carga un array con los campos que se ordenan a la izquierda
                    $lefts[] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['className'] == 'center') {//Se carga un array con los campos que se ordenan al centro
                    $centers[] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['className'] == 'right') {//Se carga un array con los campos que se ordenan a la derecha
                    $rights[] = $i;
                }
                if (is_array($listadoDatosCargados['columnas'][$i]['aaSorting'])) {
                    $orden = $listadoDatosCargados['columnas'][$i]['aaSorting'][0];
                    $criterio[$orden] = $listadoDatosCargados['columnas'][$i]['aaSorting'][1];
                    $col[$orden] = $i;
                }
                if ($listadoDatosCargados['columnas'][$i]['exportable'] == true) {
                    $exportables[] = $i;
                }

                if ($listadoDatosCargados['columnas'][$i]['aDataSort'] != false) {
                    foreach ($campos as $campo) {
                        if (strtolower($listadoDatosCargados['columnas'][$i]['aDataSort']) == strtolower($campo)) {
                            $aDataSorts[] = '{"aDataSort":[' . array_search($campo, $campos) . '], "aTargets": [' . $i . ']}';// en cada elemento del array aDataSorts hay un string con la estructura del aDataSort para agregar a las propiedades del datatable
                        }
                    }
                }
            }

            if (is_array($bVisible)) {
                $bVisible = implode(', ', $bVisible);
            }
            if (is_array($bSortable)) {
                $bSortable = implode(', ', $bSortable);
            }
            if (is_array($searchable)) {
                $searchable = implode(', ', $searchable);
            }
            if (is_array($lefts)) {
                $lefts = implode(', ', $lefts);
            }
            if (is_array($centers)) {
                $centers = implode(', ', $centers);
            }
            if (is_array($rights)) {
                $rights = implode(', ', $rights);
            }
            if (is_array($exportables)) {
                $exportables = implode(', ', $exportables);
            }
            //Se pasan todos los arrays creados a la variable de retorno
            $listadoDatosCargados['campos'] = $campos;//Estos seran los nombres de las columnas

            $parametros['aDataSorts']  = $aDataSorts;
            $parametros['bVisible']    = '{"bVisible": false, "aTargets":[' . $bVisible . ']},';
            $parametros['bSortable']   = '{"bSortable": false, "aTargets":[' . $bSortable . ']},';
            $parametros['searchable']  = '{"searchable" : false, "aTargets":[' . $searchable . ']},';
            $parametros['lefts']       = '{"className": "dt-left", "aTargets":[' . $lefts . ']},';
            $parametros['centers']     = '{"className": "dt-center", "aTargets":[' . $centers . ']},';
            $parametros['rights']      = '{"className": "dt-right", "aTargets":[' . $rights . ']},';
            $parametros['exportables'] = '[' . $exportables . ']';

            //COLUMNDEFS
            $aoColumnDefs = '"aoColumnDefs": [
            { "width": "70%", "targets": 0 },
            ';//agregado para el ancho de las columnas

            (isset($parametros['bVisible']))? $aoColumnDefs .= $parametros['bVisible'] : '';
            (isset($parametros['bSortable']))? $aoColumnDefs .= $parametros['bSortable'] : '';
            (isset($parametros['searchable']))? $aoColumnDefs .= $parametros['searchable'] : '';
            (isset($parametros['lefts']))? $aoColumnDefs .= $parametros['lefts']: '';
            (isset($parametros['centers']))? $aoColumnDefs .= $parametros['centers'] : '';
            (isset($parametros['rights']))? $aoColumnDefs .= $parametros['rights'] : '';
            (is_array($parametros['aDataSorts']))? $aoColumnDefs .= implode(',', $parametros['aDataSorts']) : '';//Importante que los DataSort esten al final
            $aoColumnDefs .= '],';
            //COLUMNDEFS

            //AASORTING
            $aaSorting = '"aaSorting": [[5, "desc"]],';
            //AASORTING

            //COLUMN NAMES
            $columns = '"columns": [';
            foreach ($columnNames as $name) {
                $columns .= '{"data": "'.$name.'"},';
            }
            $columns = rtrim($columns,',');
            $columns .= '],';
            //COLUMN NAMES

            $listadoDatosCargados['columns'] = $columns;
            $listadoDatosCargados['aaSorting'] = $aaSorting;
            $listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

            return $listadoDatosCargados;// Retorna campos,columns, aoColumnDefs y aaSorting
        }
    }

    //obtiene el nro de notificaciones que están en el listado del header y que no han sido vistas por el usuario (no desplegó el menú).
    //son las notificaciones que ya han sido notificadas pero el usuario no desplegó el listado todavía.
    //es llamada desde header.php
    public function getCantidadNotificacionesPendientes(){
        $sql = 'SELECT count(idNotificacion) as cantidad
                FROM notificaciones
                WHERE idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'].' AND NOT notificadoListado AND notificadoEscritorio';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetch(PDO::FETCH_ASSOC);
            //si la cantidad es mayor a 9 hacemos que devuelva +9.
            $rs['cantidad']=($rs['cantidad']>9?'+9':$rs['cantidad']);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs['cantidad'];
    }

    //cambia el estado de la notificación indicando que ya fue notificada la alerta de escritorio. Pone la fecha de notificación.
    public function updateNotificacion($id){
        $sql = 'UPDATE notificaciones SET notificadoEscritorio = 1, fechaNotificadoEscritorio = now() WHERE idNotificacion = '.$id.'';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }

    //cambia el estado de la notificación, indicando que ya fue vista por el usuario en el listado del header.
    //se ejecuta al tocar el botón que despliega el listado en el header.
    public function updateListadoNotificaciones(){
    	if($_SESSION['usuarioLogueadoIdUsuario']){
	        $sql = 'UPDATE notificaciones SET notificadoListado = 1 WHERE idUsuario = '.$_SESSION['usuarioLogueadoIdUsuario'];
	        //die($sql);
	        try {
	            $ro = $this->conn->prepare($sql);
	            $ro->execute();
	        }catch(Exception $e) {
	            $this->result->setStatus(STATUS_ERROR);
	            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
	            myExceptionHandler($e);
	        }
        }
        return $this;
    }

    //arma el listado de notificaciones del header y llama a la función para obtener las mismas.
    //es llamada desde init.php
    public function generarListadoNotificaciones(){
        $notificaciones = $this->getListadoNotificaciones();
        $posicion = 1;
        $datos = "";
        foreach ($notificaciones as $row) {

            $datos .='<li id = "posicion_'.$posicion.'" style="padding: 1px 0; border-bottom: 1px solid #f4f4f4;">
                        <span class="info-box-icon bg-blue" style="border-radius: 2px; height: 40px !important; width: 30px !important; line-height:40px !important; font-size: 16px;">
                            <i class="fa '.$row['icono'].'" style="margin-top: 22px"></i>
                        </span>
						<div>
						    <span class="label label-warning" style="margin-left: -20px">+1</span>
							<span id="pagina_'.$posicion.'" style="font-weight: '.(!$row['notificadoListado']?'bold':'normal').'">'.html_entity_decode($row['pagina']).".". '</span>
							</br>
							<span style="color: grey; font-size: 12px; padding-left: 4px;">'.convertDateDbToEs($row['fecha']).' por @'.html_entity_decode($row['usuarioCarga']).'</span>
						</div>
					</li>';
            $posicion++;
        }
        return $datos;
    }

    //obtiene los mails de los usuarios a los que hay que enviarle el mail que con el resumen diario de notificaciones.
    //es llamada desde envioResumenDiario.php
    public function getMailsResumenDiario(){
        $sql = 'SELECT DISTINCT idUsuario, email
                    FROM notificaciones n
                    LEFT JOIN usuarios u USING (idUsuario)
                    WHERE mailDiario AND NOT notificadoMailDiario';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //obtiene las notificaciones que debe incluir el mail con el resumen diario de notificaciones para cada usuario.
    //es llamada desde envioResumenDiario.php
    public function getNotificacionResumenDiario($idUsuario){
        $sql = "SELECT modulo, seccion, pagina, count(*) as cantidad
                    FROM notificaciones n
                    LEFT JOIN paginas p USING (idPagina)
                    LEFT JOIN secciones s USING (idSeccion)
                    LEFT JOIN modulos m USING (idModulo)
                    LEFT JOIN usuarios u USING (idUsuario)
                    WHERE mailDiario AND NOT notificadoMailDiario AND n.idUsuario = ".$idUsuario."
                    GROUP BY pagina";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //cambia el estado de las notificaciones indicando que están notificadas al enviar el mail con el resumen diario de notificaciones para cada usuario.
    //es llamada desde envioResumenDiario.php
    public function updateNotificacionResumenDiario($idUsuario){
        $sql = 'UPDATE notificaciones SET notificadoMailDiario = 1, fechaNotificadoMailDiario = now()
                  WHERE mailDiario AND NOT notificadoMailDiario AND idUsuario = '.$idUsuario;
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $this;
    }

    //obtiene los mails de los usuarios a los que hay que enviarle el mail que con el resumen semanal de notificaciones.
    //es llamada desde envioResumenSemanal.php
    public function getMailsResumenSemanal(){
        $sql = 'SELECT DISTINCT idUsuario, email
                FROM notificaciones n
                LEFT JOIN usuarios u USING (idUsuario)
                WHERE mailSemanal AND NOT notificadoMailSemanal';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //obtiene las notificaciones que debe incluir el mail con el resumen semanal de notificaciones para cada usuario.
    //es llamada desde envioResumenSemanal.php
    public function getNotificacionResumenSemanal($idUsuario){
        $sql = "SELECT modulo, seccion, pagina, count(*) as cantidad
                    FROM notificaciones n
                    LEFT JOIN paginas p USING (idPagina)
                    LEFT JOIN secciones s USING (idSeccion)
                    LEFT JOIN modulos m USING (idModulo)
                    LEFT JOIN usuarios u USING (idUsuario)
                    WHERE mailSemanal AND NOT notificadoMailSemanal AND n.idUsuario = ".$idUsuario."
                    GROUP BY pagina";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //cambia el estado de las notificaciones indicando que están notificadas al enviar el mail con el resumen semanal de notificaciones para cada usuario.
    //es llamada desde envioResumenSemanal.php
    public function updateNotificacionResumenSemanal($idUsuario){
        $sql = 'UPDATE notificaciones SET notificadoMailSemanal = 1, fechaNotificadoMailSemanal = now()
                  WHERE mailSemanal AND NOT notificadoMailSemanal AND idUsuario = '.$idUsuario;
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }

        return $this;
    }
    
    public function getUsuariosSuscriptos($idPagina, $idUsuario){
        $sql = "SELECT idUsuario
                    FROM notificacionesSuscripcion ns
                    WHERE idPagina =$idPagina AND ns.idUsuario != $idUsuario";
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs;
    }

    //depura la tabla notificaciones, dejando los últimos "$cant" registros para cada $idUsuario recibido que tenga notificaciones.
    //en la query el limit se hace desde el valor que queremos que quede en la base hasta un nro lo suficientemente grande (999999).
    //es llamada desde depuracionTablaNotificaciones.php
    public function depurarNotificaciones($cant){
        try {
            //Busco los idUsuarios que tengan más notificaciones que las indicadas.
            $sql = 'SELECT idUsuario
	                FROM notificaciones
	                GROUP BY idUsuario
	                HAVING count(1) > '.$cant;
            //die($sql);
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
                foreach($rs as $usuarios) {
                    //Elimino las notificaciones si el usuario tiene más de las indicadas. Se dejan las últimas.
                    $sql = 'DELETE n.*
							from notificaciones AS n
							LEFT JOIN (
								select idNotificacion
								FROM notificaciones
								WHERE idUsuario = ' . $usuarios['idUsuario'] . '
					            ORDER BY idNotificacion DESC LIMIT ' . $cant . '
							) as x using (idNotificacion)
							WHERE idUsuario = ' . $usuarios['idUsuario'] . ' AND x.idNotificacion is null';
                    //die($sql);
                    $ro = $this->conn->prepare($sql);
                    $ro->execute();
                }
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
    //FIN funciones para notificaciones.
}
//Cuando llega por notificacion es para devolver las notificaciones que tiene que mostrar.
if($_GET['notificacion'] == 'insert') {
	try {
	    $notificacion = new NotificacionVO();
	    $notificacion->getNotificaciones();
		//fc_print($notificacion, true);

		if($notificacion->result->getStatus() == STATUS_OK && is_array($notificacion->result->getData())) {
	        $datos = array();
			//fc_print($notificacion->result->getData(), true);
			foreach ($notificacion->result->getData() as $row) {
				//fc_print($row, true);
				//$datos .= html_entity_decode($row['pagina']) . ", " . html_entity_decode($row['seccion']) . ", " . html_entity_decode($row['modulo']) . ", " . convertDateDbToEs($row['fecha']) . ", " . html_entity_decode($row['icono']) . ", " . html_entity_decode($row['usuarioCarga']) . "|";
				$datos[] = $row;
				$notificacion->updateNotificacion($row['idNotificacion']);
			}
			//$datos = trim($datos, '|');
			echo json_encode(array_map('setHtmlEntityDecode', $datos));
		}
	}catch(Exception $e) {
		$this->result->setStatus(STATUS_ERROR);
		$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
		myExceptionHandler($e);
	}
}

//Cuando llega por listado es para cambiar el estado de las notificaciones cuando se desplegó el listado de notificaciones de la barra.
if($_POST['listado']) {
	//session_start();
    $notificacion = new NotificacionVO();
    //seteo como leído las notificaciones
    $notificacion->updateListadoNotificaciones();
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'Perfil.php')){
    $aux = new NotificacionVO();
    if (!empty($_POST) ) {
        $aux->getListadoDatosCargados($_POST);
    }
}

// debug zone
if($_GET['debug'] == 'NotificacionVO' or false){
    echo "DEBUG<br>";
    $kk = new NotificacionVO();
    //print_r($kk->getAllRows());
    $kk->idUsuario = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}