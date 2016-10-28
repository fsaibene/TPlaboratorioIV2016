<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/GenerarPdf.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class MovimientoEquipamientoVO extends Master2 {
	public $idMovimientoEquipamiento = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					    ];
	public $idTipoMovimientoEquipamiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de movimiento",
						"referencia" => "",
	];
	public $idSucursalEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "sucursal",
		"referencia" => "",
	];
	public $idLocacionOrigen = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "locación equipamiento origen",
						"referencia" => "",
	];
	public $idLocacionDestino = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "locación equipamiento destino",
						"referencia" => "",
	];
	public $idEmpleadoEntrega = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "empleado que entrega el equipamiento",
						"referencia" => "",
	];
	public $idEmpleadoRecibe = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "empleado que recibe el equipamiento",
						"referencia" => "",
	];
	public $idEstadoEquipamiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "estado del equipamiento",
						"referencia" => "",
	];
	public $fecha = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => TRUE,
						],
	];
	public $idProyectoComision = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "Nro. de comisión",
		"referencia" => "",
	];
	public $nroRemito = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "nro. de remito",
				    	];
	public $archivo = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "file",
						"nombre" => "archivo",
						"ruta" => "equipamientos/movimientos/", // de files/ en adelante
						"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $idTipoTransporteEquipamiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de transporte",
						"referencia" => "",
	];
	public $idSucursalEstablecimientoTransporteExterno1 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "empresa transportista tramo 1",
		"referencia" => "",
	];
	public $archivoTransporteExterno1 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "equipamientos/movimientos/archivoTransporteExterno1/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $idSucursalEstablecimientoTransporteExterno2 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "empresa transportista tramo 2",
		"referencia" => "",
	];
	public $archivoTransporteExterno2 = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "equipamientos/movimientos/archivoTransporteExterno2/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
    ];
	public $idZonaAfectacionSedeEmision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Sede de emisión del remito",
		"referencia" => "",
	];

	public $movimientoEquipamientoEquipamientoArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('movimientosEquipamiento');
		$this->setFieldIdName('idMovimientoEquipamiento');
		$this->idTipoMovimientoEquipamiento['referencia'] = new TipoMovimientoEquipamientoVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
		$this->idLocacionOrigen['referencia'] = new LocacionVO();
		$this->idLocacionDestino['referencia'] = new LocacionVO();
		$this->idEmpleadoEntrega['referencia'] = new EmpleadoVO();
		$this->idEmpleadoRecibe['referencia'] = new EmpleadoVO();
		$this->idEstadoEquipamiento['referencia'] = new EstadoEquipamientoVO();
		$this->idTipoTransporteEquipamiento['referencia'] = new TipoTransporteEquipamientoVO();
		$this->idSucursalEstablecimientoTransporteExterno1['referencia'] = new SucursalEstablecimientoVO();
		$this->idSucursalEstablecimientoTransporteExterno2['referencia'] = new SucursalEstablecimientoVO();
		$this->idZonaAfectacionSedeEmision['referencia'] = new ZonaAfectacionVO();
		$this->idProyectoComision['referencia'] = new ProyectoComisionVO();
		$this->fecha['valor'] = date('d/m/Y');
		$this->nroRemito['valor'] = $this->getNroRemito();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if($this->idTipoMovimientoEquipamiento['valor'] == '1') {   // COMISION
			$this->idProyectoComision['obligatorio'] = TRUE;
			$this->idEmpleadoRecibe['obligatorio'] = TRUE;
			$data = null;
			$data['nombreCampoWhere'] = 'nroProyectoComision';
			$data['valorCampoWhere'] = substr($this->idProyectoComision['valor'], -4) + 0;
			$this->idProyectoComision['referencia']->getRowById($data);
			if($this->result->getStatus() == STATUS_OK){
				$this->idProyectoComision['valor'] = $this->idProyectoComision['referencia']->idProyectoComision['valor'];
			} else {
				$resultMessage = 'No se encontró Comisión con ese código. Verifique por favor.';
			}
		} else {                                                    // OTRO
			$this->idProyectoComision['obligatorio'] = FALSE;
			$this->idProyectoComision['valor'] = NULL;
			$this->idEmpleadoRecibe['obligatorio'] = FALSE;
			$this->idEmpleadoRecibe['valor'] = NULL;
		}

		if($this->idTipoTransporteEquipamiento['valor'] == '1'){ // propio
			$this->idSucursalEstablecimientoTransporteExterno1['valor'] = NULL;
			$this->archivoTransporteExterno1['valor'] = NULL;
			$this->idSucursalEstablecimientoTransporteExterno2['valor'] = NULL;
			$this->archivoTransporteExterno2['valor'] = NULL;
		} else { // externo
			$this->idSucursalEstablecimientoTransporteExterno1['obligatorio'] = TRUE;
		}

		if($operacion != DELETE && !$this->movimientoEquipamientoEquipamientoArray) {
			$resultMessage = 'Debe seleccionar al menos UN equipamiento.';
		}
        return $resultMessage;
 	}

	public function getNroRemito(){
		$sql = "select max(nroRemito) as nroRemito from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetch(PDO::FETCH_ASSOC);
			if($rs['nroRemito']){
				$aux = explode('-', $rs['nroRemito']);
				$nroRemito = $aux[0].'-'.str_pad(($aux[1] + 1), 8, '0', STR_PAD_LEFT);
			} else {
				$nroRemito = '0001-00000001';
			}
			$this->result->setStatus(STATUS_OK);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $nroRemito;
	}

	/*
     * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
     */
	public function insertData(){
		//print_r($this); die('uno');
		try{
			$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			if($this->movimientoEquipamientoEquipamientoArray) {
				//print_r($this->establecimientoTipoEstablecimientoArray); die('tres');
				foreach ($this->movimientoEquipamientoEquipamientoArray as $aux){
					//print_r($aux); die();
					$aux->idMovimientoEquipamiento['valor'] = $this->idMovimientoEquipamiento['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
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

	/*
     * Hago el update de la tabla padre y luego borro los registros se la tabla muchos a muchos y los vuelvo a insertar.
     * Tiene que ser asi (borrar y crear) porque quiza me eliminaron un registro de la tabla muchos a muchos.
     */
	public function updateData(){
		//print_r($this); die('uno');
		try{
			//$aux = clone $this;
			$this->conn->beginTransaction();
			//print_r($this); //die();
			parent::updateData();
			if($this->result->getStatus() != STATUS_OK) {
				//print_r($this); die('error cero');
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); //die();
			$aux = new MovimientoEquipamientoEquipamientoVO();
			$data = NULL;
			$data['nombreCampoWhere'] = 'idMovimientoEquipamiento';
			$data['valorCampoWhere'] = $this->idMovimientoEquipamiento['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->movimientoEquipamientoEquipamientoArray) {
				//print_r($this->establecimientoTipoEstablecimientoArray); die('tres');
				foreach ($this->movimientoEquipamientoEquipamientoArray as $aux){
					//print_r($aux); die();
					$aux->idMovimientoEquipamiento['valor'] = $this->idMovimientoEquipamiento['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
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

	public function getReporteMovimientoEquipamientos($data){
		//print_r($data); //die();
		$sql = 'select getEquipamiento(idEquipamiento) as equipamiento
					, DATE_FORMAT(mv.fecha,"%d/%m/%Y") as fecha
					, nroRemito
					, lo.locacion as locacionOrigen
					, ld.locacion as locacionDestino
					, getEmpleado(mv.idEmpleadoEntrega) as empleadoEntrega
					, getEmpleado(mv.idEmpleadoRecibe) as empleadoRecibe
					, estadoEquipamiento
					, mv.observaciones
					, tipoTransporteEquipamiento
					, CONCAT_WS("\\\", e1.establecimiento, se1.sucursalEstablecimiento) as transporteTramoNro1
					, CONCAT_WS("\\\", e2.establecimiento, se2.sucursalEstablecimiento) as transporteTramoNro2
				from movimientosEquipamiento as mv
				inner join movimientosEquipamiento_equipamientos as mee using (idMovimientoEquipamiento)
				inner join equipamientos as v using (idEquipamiento)
				inner join locaciones as lo on lo.idLocacion = mv.idLocacionOrigen
				inner join locaciones as ld on ld.idLocacion = mv.idLocacionDestino
				inner join estadosEquipamiento as ev using (idEstadoEquipamiento)
				inner join tiposTransporteEquipamiento as tte using (idTipoTransporteEquipamiento)
				left join sucursalesEstablecimiento as se1 on mv.idSucursalEstablecimientoTransporteExterno1 = se1.idSucursalEstablecimiento
				left join establecimientos as e1 on e1.idEstablecimiento = se1.idEstablecimiento
				left join sucursalesEstablecimiento as se2 on mv.idSucursalEstablecimientoTransporteExterno2 = se2.idSucursalEstablecimiento
				left join establecimientos as e2 on e2.idEstablecimiento = se2.idEstablecimiento
				where true
				 ';
		if($data['idEquipamiento']){
			$sql .= ' and v.idEquipamiento = '.$data['idEquipamiento'];
		}
		if($data['fechaDesde']){
			$sql .= ' and fecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= ' order by nroRemito desc';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$dataResult['data'] = $rs;
			//print_r($rs);die();
			$this->result->setData($dataResult);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	function getMovimientoEquipamientoPDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();
			//fc_print($this->getPDF()); die();
			$pageBodyBody = $this->getPDF();
			//print_r($pageBodyBody); die();
			//echo $result->getData();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $pageBodyBody;
	}

	public function getPDF(){
		try {
			$this->getRowById();
			//fc_print($this, true);
			$rmtoFecha = explode('-', $this->fecha['valor']);

			$meeArray = new MovimientoEquipamientoEquipamientoVO();
			$data = null;
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->idMovimientoEquipamiento['valor'];
			$meeArray->getAllRows($data);
			//fc_print($meeArray, true);

			// documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
			// para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
			//$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
			$pages = '';
			$page = '';
			$css = '<style>
						.font8 {
							font-size: 8pt;
						}
						table {
							width: 100%;
						}
						td {
							vertical-align: top;
						}
						th {
							background-color: #eee;
							text-align: center;
						}
	
						table.borderYes {
							background-color: #000;
						}
						table.borderYes td {
							background-color: #fff;
						}
						table.borderNo td, th {
							border:0px solid black;
						}
					</style>';

			$pageHeader  = $css;
			$pageHeader .= '<page_header>';
			$pageHeader .= '</page_header>';

			$pageFooter = '<page_footer>';
			$pageFooter .= '</page_footer>';

			$pageBegin = '<page backtop="20mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="P">';
			$pageEnd = '</page>';

			$pageBody = '
						<table class="borderNo" style="width:142px; padding-top: 58px; padding-right: 55px; text-align: center;" align="right">
							<tr>
								<td style="width: 33%; height: 28px; vertical-align: middle;">'.$rmtoFecha[2].'</td>
								<td style="width: 33%; height: 28px; vertical-align: middle;">'.$rmtoFecha[1].'</td>
								<td style="width: 34%; height: 28px; vertical-align: middle;">'.substr($rmtoFecha[0],2,2).'</td>
							</tr>
						</table>
						<br>';
			$pageBody .= '<table class="borderNo" style="margin-top: 72px;">
							<tr>
								<td style="padding-left: 80px;" colspan="2">'.$this->idSucursalEstablecimiento['referencia']->idEstablecimiento['referencia']->establecimiento['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 50%; padding-left: 80px; padding-top: 10px;">'.$this->idSucursalEstablecimiento['referencia']->idGmaps['referencia']->route['valor']
									.', '.$this->idSucursalEstablecimiento['referencia']->idGmaps['referencia']->street_number['valor']. '</td>
								<td style="width: 50%; padding-left: 250px; padding-top: 10px;">'.$this->idSucursalEstablecimiento['referencia']->idGmaps['referencia']->locality['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 50%; padding-left: 80px; padding-top: 15px;">&nbsp;</td>
								<td style="width: 50%; padding-left: 90px; padding-top: 15px;">30-71115059-1</td>
							</tr>
						</table>
					<br>
					<br>
					<br>
			';
			if($meeArray->result->getData()) {
				$pageBody .= '<table style="margin-top: 50px; padding-left: 25px;" class="borderNo">';
				$count = 1;
				foreach ($meeArray->result->getData() as $mee) {
					//print_r($pce);die();
					$pageBody .= '	<tr>
										<td style="width: 5%;">1</td>
										<td style="width: 15%;">' . $mee->idEquipamiento['referencia']->codigo['valor'] . '</td>
										<td style="width: 80%;">' . $mee->idEquipamiento['referencia']->modelo['valor'] . ' ' . $mee->idEquipamiento['referencia']->nroSerie['valor'] .'</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			}

			$pageBody .= '<div style="position: absolute; bottom: 200px;  margin-left: 140px;">
					<p style="border-bottom: 1px solid black; margin-bottom: 0">Equipamiento:</p>
					<table class="borderNo" >
						<tr>
							<td style="width: 10%;">Origen: </td>
							<td style="width: 90%;">'.$this->idLocacionOrigen['referencia']->locacion['valor'].'</td>
						</tr>
						<tr>
							<td style="width: 10%;">Destino: </td>
							<td style="width: 90%;">'.$this->idLocacionDestino['referencia']->locacion['valor'].'</td>
						</tr>
					</table>
					<p style="border-bottom: 1px solid black; margin-bottom: 0">Responsable:</p>
					<table class="borderNo" >
						<tr>
							<td style="width: 10%;">Entrega: </td>
							<td style="width: 90%;">'.$this->idEmpleadoEntrega['referencia']->getNombreCompleto().'</td>
						</tr>
						<tr>
							<td style="width: 10%;">Recibe: </td>
							<td style="width: 90%;">'.$this->idEmpleadoRecibe['referencia']->getNombreCompleto().'</td>
						</tr>
					</table>';
			if($this->idTipoTransporteEquipamiento['valor'] == 2) { // externo
				$pageBody .= '<p style="border-bottom: 1px solid black; margin-bottom: 0">Transporte:</p>
							<table class="borderNo" >
							<tr>
								<td style="width: 20%;">Empresa tramo #1: </td>
								<td style="width: 80%;">'.$this->idSucursalEstablecimientoTransporteExterno1['referencia']->idEstablecimiento['referencia']->establecimiento['valor']. ' '.$this->idSucursalEstablecimientoTransporteExterno1['referencia']->sucursalEstablecimiento['valor'].'</td>
							</tr>';
				if($this->idSucursalEstablecimientoTransporteExterno2['valor']) {
					$pageBody .= '<tr>
									<td style="width: 20%;">Empresa tramo #2: </td>
									<td style="width: 80%;">'.$this->idSucursalEstablecimientoTransporteExterno2['referencia']->idEstablecimiento['referencia']->establecimiento['valor']. ' '.$this->idSucursalEstablecimientoTransporteExterno2['referencia']->sucursalEstablecimiento['valor'].'</td>
								</tr>
								';
				}
				$pageBody .= '</table>';
			}
			$pageBody .= '<p style="border-bottom: 1px solid black; margin-bottom: 0; margin-right: 30px;">Observaciones:<br>'.$this->observaciones['valor'].'</p>';
			$pageBody .= '</div>';

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return html_entity_decode($pages, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	public function getExcelFile($fileName, $data = null){
		$tablePropierties = $this->getObjectPropierties();
		foreach ($tablePropierties as $campo) {
			if($campo['visibleDTexport']){
				$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
				$header[ucfirst($campo['nombre'])] = 'string';// indica cómo será tomado el campo por el Excel
			}
		}

		$camposAux = implode(',', $dbFieldNames);
		$sql2 = $this->getSqlForTableExport($data);
		$sql = 'select '.$camposAux . ' from ('.$sql2.') as subConsulta';
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$arrayRows = setHtmlEntityDecode($rs);
				$sheet_name = 'Hoja1';
				$writer = new XLSXWriter();
				$writer->writeSheetHeader($sheet_name, $header);
				foreach ($arrayRows as $row) {
					$writer->writeSheetRow($sheet_name, $row);
				}
				$writer->setAuthor('SIGIweb');
				$fileName = html_entity_decode($fileName, ENT_QUOTES | ENT_IGNORE, "UTF-8")."-".date('Ymd-His')."-all.xlsx";
				header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($fileName).'"');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				$writer->writeToStdOut();
				exit(0);
			}else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("La consulta no retornó registros.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

	}

	public function getObjectPropierties(){

		$objectPropierties[] = array('nombre' => 'idMovimientoEquipamiento',   'dbFieldName' => 'idMovimientoEquipamiento',    'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fechaEN',                    'dbFieldName' => 'fecha',                       'visibleDT' => false,'visibleDTexport' => false ,  'className' => false,  'aDataSort' => false,       'bSortable' => true, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'fecha',                      'dbFieldName' => 'fechaES',                     'visibleDT' => true, 'visibleDTexport' => true ,  'className' => false,  'aDataSort' => 'fechaEN',       'bSortable' => true, 'searchable' => true);
		$objectPropierties[] = array('nombre' => 'locacion Origen',            'dbFieldName' => 'locacionOrigen',              'visibleDT' => true, 'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //aDataSort: Hay que indicar el nombre de la columna de la cual se desea ordenar al clickear sobre el campo actual
		$objectPropierties[] = array('nombre' => 'locacion Destino',           'dbFieldName' => 'locacionDestino',             'visibleDT' => true, 'visibleDTexport' => true,  'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'Empleado Emisor',            'dbFieldName' => 'empleadoEntrega',             'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'Empleado Recepción',         'dbFieldName' => 'empleadoRecibe',              'visibleDT' => false,'visibleDTexport' => true,  'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'estado del Equipamiento',    'dbFieldName' => 'estadoEquipamiento',          'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'nro Remito',                 'dbFieldName' => 'nroRemito',                   'visibleDT' => true, 'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => true, 'searchable' => true);
		$objectPropierties[] = array('nombre' => 'archivo',                    'dbFieldName' => 'archivo',                     'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'TipoTransporteEquipamiento', 'dbFieldName' => 'TipoTransporteEquipamiento',  'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'sucursalEstablecimiento',    'dbFieldName' => 'se1',                         'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'archivoTransporteExterno1',  'dbFieldName' => 'archivoTransporteExterno1',   'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'sucursalEstablecimiento',    'dbFieldName' => 'se2',                         'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'archivoTransporteExterno2',  'dbFieldName' => 'archivoTransporteExterno2',   'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'observaciones',              'dbFieldName' => 'observaciones',               'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'zonaAfectacion',             'dbFieldName' => 'zonaAfectacion',              'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'acción',                     'dbFieldName' => 'accion',                      'visibleDT' => true, 'visibleDTexport' => false,  'className' => 'center','aDataSort' => false,      'bSortable' => false, 'searchable' => false);

		return $objectPropierties;
	}

	public function getDataTableProperties(){
		$listadoDatosCargados['columnas'] = $this->getObjectPropierties();
		foreach ($listadoDatosCargados['columnas'] as $campo) {
			$campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
			$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
		}
		for ($i = 0; $i < count($listadoDatosCargados['columnas']); $i++) {
			if ($listadoDatosCargados['columnas'][$i]['visibleDT'] == false) {//Se carga un array con los campos que nos seran visibles
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
			if ($listadoDatosCargados['columnas'][$i]['visibleDTexport'] == true) {
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

		$listadoDatosCargados['exportables'] = '[' . $exportables . ']';

		//COLUMNDEFS
		$aoColumnDefs = '"aoColumnDefs": [';
		if(isset($parametros['bVisible'])) $aoColumnDefs .= $parametros['bVisible'];
		if(isset($parametros['bSortable'])) $aoColumnDefs .= $parametros['bSortable'];
		if(isset($parametros['searchable'])) $aoColumnDefs .= $parametros['searchable'];
		if(isset($parametros['lefts'])) $aoColumnDefs .= $parametros['lefts'];
		if(isset($parametros['centers'])) $aoColumnDefs .= $parametros['centers'];
		if(isset($parametros['rights'])) $aoColumnDefs .= $parametros['rights'];
		if(is_array($parametros['aDataSorts'])) $aoColumnDefs .= implode(',', $parametros['aDataSorts']);   //Importante que los DataSort esten al final
		$aoColumnDefs .= '],';
		//COLUMNDEFS

		//AASORTING
		$aaSorting = '"aaSorting": [ [1,"desc"] ],';
		//AASORTING

		//COLUMN NAMES
		$columns = '"columns": [';
		foreach ($dbFieldNames as $name) {
			$columns .= '{"data": "'.$name.'"},';
		}
		$columns = rtrim($columns,',');
		$columns .= '],';
		//COLUMN NAMES
		$listadoDatosCargados['columns'] = $columns;
		$listadoDatosCargados['aaSorting'] = $aaSorting;
		$listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

		return $listadoDatosCargados;   // Retorna campos,columns, aoColumnDefs y aaSorting
	}

	public function getDataTableData($postData, $data = null){
		try{
			$sql = $this->getSqlForTableExport($data);
			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				foreach ($dataSql['data'] as $row) {
					$auxRow['idMovimientoEquipamiento'] = $row['idMovimientoEquipamiento'];
					$auxRow['fecha'] = $row['fecha'];
					$auxRow['fechaES'] = $row['fechaES'];
					$auxRow['locacionOrigen'] = $row['locacionOrigen'];
					$auxRow['locacionDestino'] = $row['locacionDestino'];
					$auxRow['empleadoEntrega'] = $row['empleadoEntrega'];
					$auxRow['empleadoRecibe'] = $row['empleadoRecibe'];
					$auxRow['estadoEquipamiento'] = $row['estadoEquipamiento'];
					$auxRow['nroRemito'] = $row['nroRemito'];
					$auxRow['archivo'] = $row['archivo'];
					$auxRow['TipoTransporteEquipamiento'] = $row['TipoTransporteEquipamiento'];
					$auxRow['se1'] = $row['se1'];
					$auxRow['archivoTransporteExterno1'] = $row['archivoTransporteExterno1'];
					$auxRow['se2'] = $row['se2'];
					$auxRow['archivoTransporteExterno2'] = $row['archivoTransporteExterno2'];
					$auxRow['observaciones'] = limpiarCampoWysihtml5($row['observaciones']);
					$auxRow['zonaAfectacion'] = $row['zonaAfectacion'];
					$auxRow['accion'] = '';
					if($auxRow['archivo']){
						$auxRow['accion'] ='<a class="text-black" target="_blank" href="'.getPath().'/files/equipamientos/movimientos/'.$row['archivo']. '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp';
					}
					$auxRow['accion'] .= '<a class="text-black" href="'.$postData['page'].'?' . codificarGets('id=' . $row['idMovimientoEquipamiento'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
	                                      <a class=" text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idMovimientoEquipamiento'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
					$auxData[] = $auxRow;
				}
				$dataSql['data'] = $auxData;
			}
			echo json_encode($dataSql);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}

	public function getSqlForTableExport($data = null){
		$sql = 'SELECT
					me.idMovimientoEquipamiento,
					lo.locacion as locacionOrigen,
					ld.locacion as locacionDestino,
					getEmpleado(me.idEmpleadoEntrega) as empleadoEntrega,
					getEmpleado(me.idEmpleadoRecibe) as empleadoRecibe,
					ee.estadoEquipamiento,
					me.fecha,
					DATE_FORMAT(me.fecha, "%d/%m/%Y") as fechaES,
					me.nroRemito,
					me.archivo,
					tte.TipoTransporteEquipamiento,
					se1.sucursalEstablecimiento as se1,
					me.archivoTransporteExterno1,
					se2.sucursalEstablecimiento as se2,
					me.archivoTransporteExterno2,
					me.observaciones,
					za.zonaAfectacion
				FROM
					movimientosEquipamiento AS me
				LEFT JOIN locaciones AS lo ON lo.idLocacion = me.idLocacionOrigen
				LEFT JOIN locaciones AS ld ON ld.idLocacion = me.idLocacionDestino
				LEFT JOIN empleados AS e1 ON e1.idEmpleado = me.idEmpleadoEntrega
				LEFT JOIN empleados AS e2 ON e2.idEmpleado = me.idEmpleadoRecibe
				LEFT JOIN estadosEquipamiento AS ee ON ee.idEstadoEquipamiento = me.idEstadoEquipamiento
				LEFT JOIN tiposTransporteEquipamiento AS tte ON tte.idTipoTransporteEquipamiento = me.idTipoTransporteEquipamiento
				LEFT JOIN sucursalesEstablecimiento AS se1 ON se1.idSucursalEstablecimiento = me.idSucursalEstablecimientoTransporteExterno1
				LEFT JOIN sucursalesEstablecimiento AS se2 ON se2.idSucursalEstablecimiento = me.idSucursalEstablecimientoTransporteExterno2
				LEFT JOIN zonasAfectacion AS za ON za.idZonaAfectacion = me.idZonaAfectacionSedeEmision
				WHERE
					TRUE ';
		if ($data['usuarioLogueadoIdZonaAfectacion']){
			$sql .= 'and za.idZonaAfectacion = '.$data['usuarioLogueadoIdZonaAfectacion'];
		}
		return $sql;
	}
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMmovimientosEquipamiento.php' )){
	$aux = new MovimientoEquipamientoVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		if (empty($_POST) ) {
			$aux->getDataTableProperties();
		} else {
			$data = array();
			$data['usuarioLogueadoIdZonaAfectacion'] = $_SESSION['usuarioLogueadoIdZonaAfectacion'];
			$aux->getDataTableData($_POST, $data);
		}
	}
}
// debug zone
if($_GET['debug'] == 'MovimientoEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new MovimientoEquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idMovimientoEquipamiento = 116;
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
