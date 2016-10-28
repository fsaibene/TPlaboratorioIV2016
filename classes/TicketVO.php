<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TicketVO extends Master2 {
	public $idTicket = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "id",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idTicketEstado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "estado del ticket",
		"referencia" => "",
	];
	public $idTicketTipo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de ticket",
		"referencia" => "",
	];
	public $idUsuarioCreador = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "usuario",
		"referencia" => "",
	];
	public $fecha = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "timestamp",
		"nombre" => "fecha",
		"validador" => ["admiteMenorAhoy" => FALSE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];

	public $ticketDetalleObject;
	public $idUsuarioLog;
	public $fechaLog;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('tickets');
		$this->setFieldIdName('idTicket');
		$this->fecha['valor'] = date('Y-m-d H:i:s');
		$this->excluirAtributo('ticketDetalleObject');
		//$this->ticketDetalleObject = new TicketDetalleVO();
		$this->idTicketEstado['referencia'] = new TicketEstadoVO();
		$this->idTicketTipo['referencia'] = new TicketTipoVO();
		$this->idTicketEstado['valor'] = 1; // ABIERTO
		$this->idUsuarioCreador['referencia'] = new UsuarioVO();
		$this->idUsuarioCreador['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		//$this->idUsuarioCreador['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];
		return;
	}

	public function insertData(){
		//print_r($_SESSION); die();
		//print_r($this);die();
		try{
			$this->businessLogic(INSERT);
			if($this->result->getStatus () != STATUS_OK) return $this;

			$this->conn->beginTransaction();
			if (!$this->idTicket['valor']) {
				parent::insertData();
			} else {
				$this->idTicketEstado['valor'] = 1; // ABIERTO
				parent::updateData();
			}
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			} else {
				//print_r($this->idTicket); die();
				$this->ticketDetalleObject->idTicket['valor'] = $this->idTicket['valor'];
				$this->ticketDetalleObject->insertData();
				if($this->ticketDetalleObject->result->getStatus() != STATUS_OK) {
					//print_r($this->ticketDetalleObject->result); die();
					$this->conn->rollBack();
					$this->result = $this->ticketDetalleObject->result;
					//print_r($this);die();
					return $this;
				} else {
					$this->conn->commit();
				}
			}
		}catch(Exception $e){
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $this->getPDOMessage($e));
			myExceptionHandler($e);
		}
		//print_r($this);die();
		return $this;
	}

	/*
	 * Devuelve en el result->data un array con los correos a quienes debe enviarse un mail notificando la respuesta al ticket
	 */
	public function getEmailsAvisoDeRespuesta(){
		try{
			$sql = "SELECT u.email
                    FROM ticketDetalles as td
                    inner join usuarios as u on td.idusuariolog = u.idusuario
                    WHERE idticket = ".$this->idTicket['valor']." and not u.superAdmin and idusuario != ".$_SESSION['usuarioLogueadoIdUsuario'] ."
                    group by u.email ";
			//echo $sql;
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$rs[]['email'] = SOPORTE_MAIL.'@'.DOMINIO;
			$this->result->setData($rs);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this);die();
		return $this;
	}

	public function sendMail(){
		try{
			$this->getRowByIdValidate();
			$detalle = html_entity_decode($this->ticketDetalleObject->detalle['valor'], ENT_QUOTES | ENT_IGNORE, "UTF-8");
			$usuario = new UsuarioVO();
			$usuario->idUsuario['valor'] = $_SESSION['usuarioLogueadoIdUsuario'];
			$usuario->getRowByIdValidate();

			$link =  getPath().'/pages/ABMtickets.php?id='.$this->idTicket['valor'].'&action=edit';
			$subject = CLIENTE.' - Gestión de Tickets de Soporte';
			$titulo = "Mensaje de ".CLIENTE;
			$mensaje = "
                        <p>Se ha actualizado el ticket Nro. ".$this->idTicket['valor']."</p>
                        <p>Enviado por: ".$usuario->getNombreCompleto()."</p>
                        <p>Detalle: ".$detalle."<p>
                        <p>Puede visualizar y responder el ticket haciendo click <a target='_blank' href='".$link."'>AQUÍ</a></p>
                        <p>No responder este correo.</p>
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
			$mail->AddReplyTo(SOPORTE_MAIL.'@'.DOMINIO, 'Soporte - '.CLIENTE);
			$mail->SetFrom(SOPORTE_MAIL.'@'.DOMINIO, 'Soporte - '.CLIENTE);

			if($this->getEmailsAvisoDeRespuesta()->result->getStatus() == STATUS_OK){
				foreach ($this->getEmailsAvisoDeRespuesta()->result->getData() as $emails) {
					$mail->AddAddress($emails['email']);
				}
			} else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, no se pudo enviar el E-mail. Reintente nuevamente o contacte al administrador.");
				return $this;
			}
			$mail->Subject = $subject;
			$mail->AltBody = $body;
			$mail->MsgHTML($body);

			for($i=0; $i<5; $i++){
				if($mail->Send()) {
					$this->result->setStatus(STATUS_OK);
					$this->result->setMessage('Se envió una notificación.');
					$this->result->setData(null);
					$flag = true;
					break;
				} else {
					sleep(1);
				}
			}
			if(!$flag) {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, no se pudo enviar el E-mail. Reintente nuevamente o contacte al administrador.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	/*
	 * esta funcion recupera todos los tickets y sus detalles agrupados para poder mostrarlo facilmente en un listado
	 */
//	public function getTickets($data = NULL){
////		try{
////			$sql = "select *
////                    from v_tickets as vt";
////			if(!$_SESSION['usuarioLogueadoSuperAdmin']) {
////				$sql .= " where vt.idUsuarioCreador = " . $_SESSION['usuarioLogueadoIdUsuario'];
////			}
////			if($data)
////				$sql .= " and vt.".$data['nombreCampoWhere']." = ".$data['valorCampoWhere'];
////			$sql .= " order by ticketestado asc, idticket desc";
////			//die($sql);
////			$ro = $this->conn->prepare($sql);
////			if($ro->execute()){
////				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
////				$this->result->setData($rs);
////			} else {
////				$this->result->setStatus(STATUS_ERROR);
////				$this->result->setMessage("ERROR, contacte al administrador.");
////			}
////		}catch(Exception $e) {
////			$this->result->setStatus(STATUS_ERROR);
////			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
////			myExceptionHandler($e);
////		}
////		//print_r($result);die();
////		return $result;
//		$sql = "select *
//                    from v_tickets as vt
//                    where true ";
////        if ($data)
////            $sql .= " and vt." . $data['nombreCampoWhere'] . " = " . $data['valorCampoWhere']." and ";
//		if(!$_SESSION['usuarioLogueadoSuperAdmin']) {
//				$sql .= " and vt.idUsuarioCreador = " . $_SESSION['usuarioLogueadoIdUsuario'];
//			}
////        $sql .= " order by ticketestado asc, idticket desc";
//		$data = getDataTableSqlDataFilter($data, $sql);
//		if ($data['data']) {
//			foreach ($data['data'] as $row) {
//				$auxRow['detalle'] = $row['detalle'];
//				$auxRow['idticket'] = $row['idticket'];
//				$auxRow['ticketestado'] = $row['ticketestado'];
//				$auxRow['tickettipo'] = $row['tickettipo'];
//				$auxRow['usuariocreador'] = $row['usuariocreador'];
//				$auxRow['fechacreacion'] = $row['fechacreacion'];
//				$auxRow['detallecreacion'] = '['.$row['fechacreacion'].'] '.$row['usuariocreador'];
//				$auxRow['usuarioultimaactualizacion'] = $row['usuarioultimaactualizacion'];
//				$auxRow['fechaultimaactualizacion'] = $row['fechaultimaactualizacion'];
//				$auxRow['detalleultimaactualizacion'] = '['.$row['fechaultimaactualizacion'].'] '.$row['usuarioultimaactualizacion'];
//				$auxRow['accion'] = '<a href="ABMtickets.php?' . codificarGets('id=' . $row['idticket'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
//                                                            <a class="btn-compose-modal-confirm" href="#" data-href="ABMtickets.php?' . codificarGets('id=' . $row['idticket'] . '&action=close') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Cerrar"><span class="fa fa-flag fa-lg"></span></a>';
//				$auxData[] = $auxRow;
//			}
//			$data['data'] = $auxData;
//		}
//		echo json_encode($data);
//	}
	public function getListadoDatosCargados($data = null){ //Esta func recibe un parametro para el listado asincronico, si tiene datos hace la consulta con la base, si es null trae los parametros y nombres del columnas para el datatable
		if($data) {
			$sql = "select *
                    from v_tickets as vt 
                    where true ";
//        if ($data)
//            $sql .= " and vt." . $data['nombreCampoWhere'] . " = " . $data['valorCampoWhere']." and ";
			if (!$_SESSION['usuarioLogueadoSuperAdmin']) {
				$sql .= " and vt.idUsuarioCreador = " . $_SESSION['usuarioLogueadoIdUsuario'];
			}
//        $sql .= " order by ticketestado asc, idticket desc";
			$data = getDataTableSqlDataFilter($data, $sql);
			if ($data['data']) {
				//DATOS
				foreach ($data['data'] as $row) {
					$auxRow['detalle'] = $row['detalle'];
					$auxRow['idticket'] = $row['idticket'];
					$auxRow['ticketestado'] = $row['ticketestado'];
					$auxRow['tickettipo'] = $row['tickettipo'];
					$auxRow['usuariocreador'] = $row['usuariocreador'];
					$auxRow['fechacreacion'] = $row['fechacreacion'];
					$auxRow['detallecreacion'] = '[' . $row['fechacreacion'] . '] ' . $row['usuariocreador'];
					$auxRow['usuarioultimaactualizacion'] = $row['usuarioultimaactualizacion'];
					$auxRow['fechaultimaactualizacion'] = $row['fechaultimaactualizacion'];
					$auxRow['detalleultimaactualizacion'] = '[' . $row['fechaultimaactualizacion'] . '] ' . $row['usuarioultimaactualizacion'];
					$auxRow['accion'] = '<a href="'.$data['page'].'?' . codificarGets('id=' . $row['idticket'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
                                                            <a class="btn-compose-modal-confirm" href="#" data-href="'.$data['page'].'?' . codificarGets('id=' . $row['idticket'] . '&action=close') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Cerrar"><span class="fa fa-flag fa-lg"></span></a>';
					$auxData[] = $auxRow;
				}
				$data['data'] = $auxData;
			}
			echo json_encode($data);
		} else { // si es NULL devuelve los parametros del datatable
			    //Parametros columnas
				$objColumns = null;   //Nombre: Indica nombre de los campos que se mostraran en el excel y el Datatable
				$objColumns[] = array('nombre' => 'detalle',                    'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => false,    'searchable' => true,   'exportable' =>false);   //className: Indica de que lado estara ordenado el elmento de esa columna
				$objColumns[] = array('nombre' => 'idticket',                   'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => false,    'searchable' => false,  'exportable' =>false);   //bVIsible:Indica si la columna se muestra
				$objColumns[] = array('nombre' => 'estado',                     'bVisible' => true,     'className' => false,       'aDataSort' => false,                     'bSortable' => true,     'searchable' => true,   'exportable' =>false);   //bSorteable: Indica si se podra ordenar por esa columna
				$objColumns[] = array('nombre' => 'tipo',                       'bVisible' => true,     'className' => false,       'aDataSort' => false,                     'bSortable' => true,     'searchable' => true,   'exportable' => false);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
				$objColumns[] = array('nombre' => 'usuariocreador',             'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => false,    'searchable' => true,   'exportable' => false);
				$objColumns[] = array('nombre' => 'fechacreacion',              'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => false,    'searchable' => true,   'exportable' => false);
				$objColumns[] = array('nombre' => 'creado por',                 'bVisible' => true,     'className' => false,       'aDataSort' => 'fechacreacion',           'bSortable' => true,     'searchable' => false,  'exportable' => false);
				$objColumns[] = array('nombre' => 'usuarioultimaactualizacion', 'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => false,    'searchable' => true,   'exportable' => false);
				$objColumns[] = array('nombre' => 'fechaultimaactualizacion',   'bVisible' => false,    'className' => false,       'aDataSort' => false,                     'bSortable' => true,     'searchable' => true,   'exportable' => false);
				$objColumns[] = array('nombre' => 'última actualizacion',       'bVisible' => true,     'className' => false,       'aDataSort' => 'fechaultimaactualizacion','bSortable' => true,     'searchable' => false,  'exportable' => false);
				$objColumns[] = array('nombre' => 'accion',                     'bVisible' => true,     'className' => 'center',    'aDataSort' => false,                     'bSortable' => false,    'searchable' => false,  'exportable' => false);

				$listadoDatosCargados['columnas'] = $objColumns;
				$columnNames = array('detalle','idticket','ticketestado','tickettipo','usuariocreador','fechacreacion','detallecreacion','usuarioultimaactualizacion','fechaultimaactualizacion','detalleultimaactualizacion','accion');//muy importante el orden
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
//				if ($listadoDatosCargados['columnas'][$i]['aaSorting'] == 'asc') {
//					$sortingsAsc = $i;
//				}
//				if ($listadoDatosCargados['columnas'][$i]['aaSorting'] == 'desc') {
//					$sortingsDesc = $i;
//				}
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
			$aoColumnDefs = '"aoColumnDefs": [';

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
			$aaSorting = '"aaSorting": [[2, "asc"], [8, "desc"]],';
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

}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMtickets.php' || $_POST['page'] == 'ABMticketsSuperAdmin.php')){
	$aux = new TicketVO();
	if (!empty($_POST) ) {
		$aux->getListadoDatosCargados($_POST);
	}
}
if($_GET['debug'] == 'TicketVO' or false){
	echo "DEBUG<br>";
	$kk = new TicketVO();
	//print_r($kk->getAllRows());
	$kk->idTicket = 116;
	$kk->ticket = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>