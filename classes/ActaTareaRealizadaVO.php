<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaRealizadaVO extends Master2 {
    public $idActaTareaRealizada = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
	public $nroActaTareaRealizada = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "Nro. Acta Tarea Realizada",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $nroOrdenTrabajo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "Nro. Orden de Trabajo",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $fechaDesde = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha desde",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $fechaHasta = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha hasta",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $idContratoGerenciaProyecto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Proyecto",
		"referencia" => "",
	];
	public $idProyectoComision = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "Comisión",
		"referencia" => "",
	];
	public $idTipoActaTareaRealizada = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Tipo Acta Tarea Realizada",
		"referencia" => "",
	];
    public $detalle = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "detalle",
    ];
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado",
		"referencia" => "",
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones (uso interno)",
    ];
	public $recepcionFecha = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "date",
		"nombre" => "fecha de recepción",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $recepcionIdEmpleado = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "empleado que recepciona",
		"referencia" => "",
	];

	public $actaTareaRealizadaEmpleadoArray = [
		'tipo' => 'comboMultiple',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaRealizadaEmpleadoVO', // es el nombre de la clase a la que hace referencia el array
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idEmpleado', // es el campo por el que se filtra... Ver funcion deleteDataArray
		'filterGroupKeyName' => 'idActaTareaRealizada'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $actaTareaRealizadaEquipamientoArray = [
		'tipo' => 'comboMultiple',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaRealizadaEquipamientoVO', // es el nombre de la clase a la que hace referencia el array
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idEquipamiento', // es el campo por el que se filtra... Ver funcion deleteDataArray
		'filterGroupKeyName' => 'idActaTareaRealizada'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $actaTareaRealizadaVehiculoArray = [
		'tipo' => 'comboMultiple',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaRealizadaVehiculoVO', // es el nombre de la clase a la que hace referencia el array
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idVehiculo', // es el campo por el que se filtra... Ver funcion deleteDataArray
		'filterGroupKeyName' => 'idActaTareaRealizada'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $actaTareaRealizadaContratoItemArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaRealizadaContratoItemVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ic', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaRealizadaContratoItem', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaRealizada'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('actaTareasRealizada');
	    $this->setFieldIdName('idActaTareaRealizada');
	    $this->idContratoGerenciaProyecto['referencia'] = new ContratoGerenciaProyectoVO();
	    $this->idProyectoComision['referencia'] = new ProyectoComisionVO();
	    $this->idTipoActaTareaRealizada['referencia'] = new TipoActaTareaRealizadaVO();
	    $this->idEmpleado['referencia'] = new EmpleadoVO();
	    $this->recepcionIdEmpleado['referencia'] = new EmpleadoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
	    if (strtotime(convertDateEsToDb($this->fechaDesde['valor'])) > strtotime(convertDateEsToDb($this->fechaHasta['valor'])) ) {
		    $resultMessage = 'La fecha Hasta no puede ser menor que la fecha Desde.';
	    }
	    //if($operacion == INSERT){
	        $this->idEmpleado['valor'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	    //}
	    return $resultMessage;
    }

	public function getCodigoActaTareaRealizada(){
		$codigo = 'ATR';
		$codigo .= '-'.$this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->sigla['valor'];
		if($this->nroActaTareaRealizada['valor']){
			$codigo .= '-'.str_pad($this->nroActaTareaRealizada['valor'], 5, '0', STR_PAD_LEFT);
		}
		return $codigo;
	}

	public function getNroActaTareaRealizada(){
		$sql = "select max(nroActaTareaRealizada) as nroActaTareaRealizada from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroActaTareaRealizada['valor'] = $rs[0]['nroActaTareaRealizada'] + 1;
			} else {
				$this->nroActaTareaRealizada['valor'] = 1;
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	/*
	 * este método lo uso ya que necesito solo updatear la recepcion de la misma y no todas la entidades anexas a ella...
	 */
	public function updateData2(){
		parent::updateData();
	}

	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'select idActaTareaRealizada, fechaDesde, "ATR"  as tipoActaTarea
					, "" as empleado, recepcionFecha
					, getEmpleado(recepcionIdEmpleado) as recepcionEmpleado
				from actaTareasRealizada as atd';
		$sql .= ' where true ';
		if($data['idTipoActaTarea']){
			//$sql .= ' and rv.idTipoRendicionViatico = '.$data['idTipoRendicionViatico'];
		}
		if($data['idEmpleado']){
			$sql .= ' and idEmpleado = '.$data['idEmpleado'];
		}
		if($data['estadoRecepcion'] == 'recepcionadaSI'){
			$sql .= ' and recepcionFecha is not null';
		}
		if($data['estadoRecepcion'] == 'recepcionadaNO'){
			$sql .= ' and recepcionFecha is null';
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

	function getActaTareaRealizadaPDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();
			//fc_print($this->getPDF()); die();
			$pageBodyBody = $this->getPDF();
//			print_r($pageBodyBody); die();
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
			$this->getRowWithItemsById();
//			fc_print($this, true);
			$cgi = new ContratoGerenciaInspectorVO();
			$cgi->getInspectoresVigentes($this->fechaHasta['valor'], $this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['valor']);
//			fc_print($cgi->result->getData(), true);
			$cgiTitular = new ContratoGerenciaInspectorVO();
			if($cgi->result->getData()){
				foreach ($cgi->result->getData() as $i){
					if($i['idTipoCaracterResponsable'] == 1 ){ // titular
						$cgiTitular = new ContratoGerenciaInspectorVO();
						$cgiTitular->{$cgiTitular->getFieldIdName()}['valor'] = $i['idContratoGerenciaInspector'];
						$cgiTitular->getRowById();
						continue;
					}
				}
			}

			$cgrt = new ContratoGerenciaResponsableTecnicoVO();
			$cgrt->getResponsablesTecnicosVigentes($this->fechaHasta['valor'], $this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['valor']);
//			fc_print($cgrt->result->getData(), true);
			$cgrtTitular = new ContratoGerenciaResponsableTecnicoVO();
			$cgrtSuplente = new ContratoGerenciaResponsableTecnicoVO();
			if($cgrt->result->getData()){
				foreach ($cgrt->result->getData() as $i){
					if($i['idTipoCaracterResponsable'] == 1 ){ // titular
						$cgrtTitular = new ContratoGerenciaResponsableTecnicoVO();
						$cgrtTitular->{$cgrtTitular->getFieldIdName()}['valor'] = $i['idContratoGerenciaResponsableTecnico'];
						$cgrtTitular->getRowById();
					}
					if($i['idTipoCaracterResponsable'] == 2 ){ // suplente
						$cgrtSuplente = new ContratoGerenciaResponsableTecnicoVO();
						$cgrtSuplente->{$cgrtSuplente->getFieldIdName()}['valor'] = $i['idContratoGerenciaResponsableTecnico'];
						$cgrtSuplente->getRowById();
					}
				}
			}

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
						
						.tituloTabla {
							border: 2px; 
							font-size: 11pt; 
							background-color: #ddd; 
							text-align: right; 
							padding: 2px;
						}
						.tdBorderBottom {
							border-bottom:1px solid #000;
						}
						.tdBorderRight {
							border-right:1px solid #000;
						}
						.titulo{
							background-color: #ddd;
						}
					</style>';

			$pageHeader  = $css;
			$pageHeader .= '<page_header>';
			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 20%; background-color: #fff;"><img src="'.getFullPath().'/img/logo-sinec-nuevo-295x217.jpg" alt="" width="120" alt="" /></td>
										<td style="width: 50%;">
											<div align="center" style="border-bottom:1px solid #000; padding-bottom: 10px; background-color: #fff;">
												<p>Sistema de Gestión de Calidad</p>
											</div>
											<div align="center" style="background-color: #fff;">
												<p>Acta de Tareas Realizadas</p>
											</div>
										</td>
										<td style="width: 30%;">
											<div class="tdBorderBottom" style="background-color: #fff; padding-bottom: 10px;">
												<p>Nº: '.$this->getCodigoActaTareaRealizada().'</p>
											</div>
											<div style="background-color: #fff; margin-top: 8px;">
												<table style="border-collapse: collapse;">
													<tr>
														<td style="width: 50%;" class="tdBorderBottom tdBorderRight">Fecha Inicio</td>
														<td style="width: 50%;" class="tdBorderBottom">'.convertDateDbToEs($this->fechaDesde['valor']).'</td>
													</tr>
													<tr>
														<td style="width: 50%;" class="tdBorderRight">Fecha Fin</td>
														<td style="width: 50%;">'.convertDateDbToEs($this->fechaHasta['valor']).'</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 12%;" class="titulo">Cliente: </td>
										<td style="width: 38%;">'.$this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idEstablecimiento['referencia']->establecimiento['valor'].'</td>
										<td style="width: 12%;" class="titulo">Contrato Nº: </td>
										<td style="width: 38%;">'.$this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idContrato['referencia']->nombreReferencia['valor'].'</td>
									</tr>
									<tr>
										<td style="width: 12%;" class="titulo">Gerencia: </td>
										<td style="width: 38%;">'.$this->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'].'</td>
										<td style="width: 12%;" class="titulo">Proyecto: </td>
										<td style="width: 38%;">'.$this->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'].'</td>
									</tr>
									<tr>
										<td style="width: 12%;" class="titulo">Inspector: </td>
										<td style="width: 38%;">'.$cgiTitular->getNombreCompleto().' Tel: '.$cgiTitular->telefono['valor'].' - '.$cgiTitular->celular['valor'].'</td>
										<td style="width: 12%;" class="titulo">Nº OT: </td>
										<td style="width: 38%;">'.$this->nroOrdenTrabajo['valor'].'</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 30%; text-align: center;" class="titulo">Tipo de Tarea</td>
										<td style="width: 70%; text-align: center;" class="titulo">Equipo de Trabajo</td>
									</tr>
									<tr>
										<td style="width: 30%;">';
			$tatrs = new TipoActaTareaRealizadaVO();
			$tatrs->getAllRows();
			if($tatrs->result->getData()){
				foreach ($tatrs->result->getData() as $tatr){
					if($tatr->idTipoActaTareaRealizada['valor'] == $this->idTipoActaTareaRealizada['valor']){
						$pageHeader .= '<img src="'.getFullPath().'/img/radiobutton-checked-12x12.jpg" alt="12" width="12" alt="" style="margin: 1 3px;" />'.$tatr->tipoActaTareaRealizada['valor'].'<br>';
					} else {
						$pageHeader .= '<img src="'.getFullPath().'/img/radiobutton-unchecked-12x12.jpg" alt="12" width="12" alt="" style="margin: 1 3px;" />'.$tatr->tipoActaTareaRealizada['valor'].'<br>';
					}
				}
			}
			$pageHeader .= '			</td>
										<td style="width: 70%;">';
			$atrpces = new ActaTareaRealizadaEmpleadoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idActaTareaRealizada';
			$data['valorCampoWhere'] = $this->idActaTareaRealizada['valor'];
			$atrpces->getAllRows($data);
			//fc_print($atrpces->result->getData(), true);
			if($atrpces->result->getData()){
				foreach ($atrpces->result->getData() as $atrpce){
					$pageHeader .= $atrpce->idEmpleado['referencia']->getNombreCompleto().'<br>';
				}
			}
			$pageHeader .= '			</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 100%; text-align: center;" class="titulo">Vehículos</td>
									</tr>
									<tr>
										<td style="width: 100%;">';
			$atrpcvs = new ActaTareaRealizadaVehiculoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idActaTareaRealizada';
			$data['valorCampoWhere'] = $this->idActaTareaRealizada['valor'];
			$atrpcvs->getAllRows($data);
			//fc_print($atrpcvs->result->getData(), true);
			if($atrpcvs->result->getData()){
				foreach ($atrpcvs->result->getData() as $atrpcv){
					$pageHeader .= $atrpcv->idVehiculo['referencia']->getNombreCompleto().'<br>';
				}
			} else {
				$pageHeader .= 'Ninguno<br>';
			}
			$pageHeader .= '			</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 100%; text-align: center;" class="titulo">Equipamiento</td>
									</tr>
									<tr>
										<td style="width: 100%;">';
			$atrpces = new ActaTareaRealizadaEquipamientoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idActaTareaRealizada';
			$data['valorCampoWhere'] = $this->idActaTareaRealizada['valor'];
			$atrpces->getAllRows($data);
			//fc_print($atrpces->result->getData(), true);
			if($atrpces->result->getData()){
				foreach ($atrpces->result->getData() as $atrpce){
					$pageHeader .= $atrpce->idEquipamiento['referencia']->getNombreCompleto().'<br>';
				}
			} else {
				$pageHeader .= 'Ninguno<br>';
			}
			$pageHeader .= '			</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 100%; text-align: center;" class="titulo">Descripción de la/s Tarea/s</td>
									</tr>
									<tr>
										<td style="width: 100%; background-color: #fff;">
											'.$this->detalle['valor'].'
										</td>
									</tr>
								</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>
										<td style="width: 10%; text-align: center;" class="titulo">Cantidad</td>
										<td style="width: 10%; text-align: center;" class="titulo">Posición</td>
										<td style="width: 50%; text-align: center;" class="titulo">Descripción</td>
										<td style="width: 30%; text-align: center;" class="titulo">Observaciones</td>
									</tr>';

			$atrcis = new ActaTareaRealizadaContratoItemVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idActaTareaRealizada';
			$data['valorCampoWhere'] = $this->idActaTareaRealizada['valor'];
			$atrcis->getAllRows($data);
			//fc_print($atrcis->result->getData(), true);
			if($atrcis->result->getData()){
				foreach ($atrcis->result->getData() as $atrci){
					$pageHeader .= '<tr>';
					$pageHeader .= '	<td style="width: 10%;">'.$atrci->cantidad['valor'].'</td>';
					$pageHeader .= '	<td style="width: 10%;">'.$atrci->idContratoItem['referencia']->posicion['valor'].'</td>';
					$pageHeader .= '	<td style="width: 50%;">'.$atrci->idContratoItem['referencia']->item['valor'].'</td>';
					$pageHeader .= '	<td style="width: 30%;">'.$atrci->observaciones['valor'].'</td>';
					$pageHeader .= '</tr>';
				}
			} else {
				$pageHeader .= 'Ninguna<br>';
			}
			$pageHeader .= '	</table>
								<br>';

			$pageHeader .= '	<table class="borderYes" style="width: 750px;">
									<tr>';
			$pageHeader .= '			<td style="width: 50%; background-color: #fff; text-align: center; vertical-align: bottom;">';
			if($cgrtSuplente->idEmpleado['referencia']->archivo['valor']){
				$path = getFullPath().'/files/'.$cgrtSuplente->idEmpleado['referencia']->archivo['ruta'].$cgrtSuplente->idEmpleado['referencia']->archivo['valor'];
				//fc_print($path, true);
				$pageHeader .=  '           <img src="'.$path.'" alt="" width="150" alt="" />';
			}
			$pageHeader .=  '				<br>Responsable Técnico Sup.<br>'.$cgrtSuplente->idEmpleado['referencia']->getNombreCompleto().'<br>';
			$pageHeader .= '			</td>';
			$pageHeader .= '			<td style="width: 50%; background-color: #fff; text-align: center; vertical-align: bottom;">';
			if($cgrtTitular->idEmpleado['referencia']->archivo['valor']){
				$path = getFullPath().'/files/'.$cgrtTitular->idEmpleado['referencia']->archivo['ruta'].$cgrtTitular->idEmpleado['referencia']->archivo['valor'];
				//fc_print($path, true);
				$pageHeader .=  '           <img src="'.$path.'" alt="" width="150" alt="" />';
			}
			$pageHeader .=  '				<br>Responsable Técnico<br>'.$cgrtTitular->idEmpleado['referencia']->getNombreCompleto().'<br>';
			$pageHeader .= '			</td>';
			$pageHeader .= '		</tr>
								</table>';

			$pageHeader .= '</page_header>';

			$pageFooter = '<page_footer>';
			$pageFooter .= '	<p align="center">P&aacute;gina [[page_cu]]/[[page_nb]]</p>';
			$pageFooter .= '</page_footer>';

			$pageBegin = '<page backtop="35mm" backbottom="10mm" backleft="20mm" backright="20mm" format="A4" orientation="P">';


			$pageEnd = '</page>';

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
//			fc_print($page);
			$pages .= $page;
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return html_entity_decode($pages, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	 * si me viene un idEmpleado traigo todas las ATR en las que el empleado logueado esta como integrante de una comision o estuvo como responsable técnico.
	 * si no viene muestro todas las ATR
	 */
	public function getAllRows2($data = null){
		if($data['idEmpleado']){
			$sql = 'select atr.*
					from actaTareasRealizada as atr
					inner join (
						select atr.idActaTareaRealizada
						FROM actaTareasRealizada as atr
						inner join actaTareasRealizada_empleados as pce using (idEmpleado)
						where idEmpleado = '.$data['idEmpleado'].'
						
						union 
					
						SELECT atr.idActaTareaRealizada
						FROM actaTareasRealizada as atr
						inner join proyectosComisiones as pc using (idProyectoComision)
						inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
						inner join contratosGerencias as cg using (idContratoGerencia)
						inner join (
							select cgrt.*
							FROM contratosGerenciasResponsablesTecnicos as cgrt
							INNER JOIN (
								SELECT cgrt.idContratoGerencia, cgrt.idTipoCaracterResponsable, MAX(fechaVigencia) as maxFechaVigencia, pc.idProyectoComision
								FROM contratosGerenciasResponsablesTecnicos as cgrt
								inner join contratosGerenciasProyectos as cgp using (idContratoGerencia)
								inner join proyectosComisiones as pc using (idContratoGerenciaProyecto)
								where cgrt.fechaVigencia <= pc.fechaFin
								GROUP BY cgrt.idContratoGerencia, cgrt.idTipoCaracterResponsable, pc.idProyectoComision
							) AS x 
								ON x.maxFechaVigencia = cgrt.fechaVigencia 
								AND x.idContratoGerencia = cgrt.idContratoGerencia 
								AND x.idTipoCaracterResponsable = cgrt.idTipoCaracterResponsable
							where idEmpleado = '.$data['idEmpleado'].'
						)
						as cgrt using (idContratoGerencia)
					) as atr using (idActaTareaRealizada)
					';
		} else {
			$sql = 'select atr.*
					from actaTareasRealizada as atr
					';
		}
//		die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)) {
				foreach ($rs as $data) {
					//fc_print($data);
					$auxName = get_class($this);
					$aux = new $auxName;
					$aux->mapData($data);
					foreach (getOnlyChildVars($aux) as $atributo) {
						if ($this->atributoPermitido($atributo)) {
							if ($aux->{$atributo}['referencia'] && $aux->{$atributo}['valor']) {
								$aux->{$atributo}['referencia']->{$aux->{$atributo}['referencia']->getFieldIdName()}['valor'] = $aux->{$atributo}['valor'];
								//fc_print($this->{$atributo}['referencia']);die();
								$aux->{$atributo}['referencia']->getRowById();
							}
						}
					}
					$aux2[] = $aux;
				}
				$this->result->setStatus(STATUS_OK);
				$this->result->setData($aux2);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getRegistroDeActaTareaRealizadaParaGantt($data){
		//fc_print($data, true);
		try{
			if($data['mod'] == 'resources') {
				//fc_print($data, true);
				$sql = 'SELECT CONCAT_WS("_", "e", e.idEmpleado) as id, getEmpleado(e.idEmpleado) as title, null as eventColor, null as children
						from empleados as e
						LEFT JOIN empleadosRelacionLaboral as erl using (idEmpleado)
						where erl.fechaEgreso is null
						order by title';
				//die($sql);
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				//fc_print($rs, true);
			} else if($data['mod'] == 'events') {
				$sql = 'select * 
						from (
							SELECT 
							CONCAT_WS("_", "z", atd.idActaTareaRealizada) as id
							, idActaTareaRealizada
							, "" as title
							, CONCAT_WS("_", "e", atd.idEmpleado) as resourceId
							, atd.fechaDesde as start
							, DATE_ADD(atd.fechaDesde, interval 1 day) as end
							, case when atd.recepcionFecha is null then "yellow"
									when atd.recepcionFecha is not null then "green" end as backgroundColor ';
				if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
					$sql .= ' , "ActaTareaRealizadaPDF.php?" as urlAddress, concat("id=", idActaTareaRealizada) as urlParamaters, null as url ';
				}
				$sql .= 'from actaTareasRealizada as atd ';
				$sql .= ') as atd
						where true ';
				$sql .= ' and ((start BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (end BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (start < "'.convertDateEsToDb($data['start']).'" and end > "'.convertDateEsToDb($data['end']).'")) ';
				//die($sql);
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rs as &$aux){
					if($aux['urlAddress']){
						$aux['url'] = $aux['urlAddress'] . codificarGets($aux['urlParamaters']);
						$aux['urlParamaters'] = null;
					}
				}
			}
			//fc_print($rs, true);
			echo json_encode(setHtmlEntityDecode($rs));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}

	public function getExcelFile($fileName, $data = null){
		$sql = 'SELECT
					atr.idActaTareaRealizada "Id ATR",
					getCodigoATR(atr.idActaTareaRealizada) "Código de acta",
					tatr.tipoActaTareaRealizada "Tipo de Tarea",
					atr.fechaDesde "Fecha Inicio",
					atr.fechaHasta "Fecha Fin",
					IF(atr.recepcionFecha, "Recepcionada", "Pendiente")   Estado,
					atr.recepcionFecha  "Fecha de Recepción",
					getEmpleado(atr.recepcionIdEmpleado)" Recepcionó",
					cgp.nombreReferencia "Nombre Referencia",
					getCodigoContratoGerenciaProyecto(cgp.idContratoGerenciaProyecto) "Código Proyecto",
					getEmpleado(atr.idEmpleado) "Empleado ATR",
					getContratoGerenciaProyecto (atr.idContratoGerenciaProyecto)  "Contrato/Gerencia/Proyecto",
					atr.nroOrdenTrabajo "Nro OT",
					atr.detalle "Detalles",
					getEmpleado(atre.idEmpleado)  "Empleados Implicados",
					getVehiculo(atrv.idVehiculo)  "Vehículos Implicados",
					getEquipamiento(atreq.idEquipamiento)  "Equipamientos Implicados",
					atr.observaciones "Observaciones Uso Interno",
					ci.item "Item",
					tuml.tipoUnidadMedidaLabor "Tipo Unidad Medida",
					atrci.cantidad "Cantidad",
					atrci.observaciones "Item obs."
				FROM
					actaTareasRealizada atr
				LEFT JOIN proyectosComisiones AS pc ON pc.idProyectoComision = atr.idProyectoComision
				LEFT JOIN contratosGerenciasProyectos AS cgp ON cgp.idContratoGerenciaProyecto = atr.idContratoGerenciaProyecto
				LEFT JOIN actaTareasRealizada_contratosItems as atrci on atrci.idActaTareaRealizada = atr.idActaTareaRealizada
				LEFT JOIN contratosItems as ci on ci.idContratoItem = atrci.idContratoItem
				LEFT JOIN tiposUnidadMedidaLabor AS tuml ON ci.idTipoUnidadMedidaLabor = tuml.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasRealizada_empleados AS atre ON atre.idActaTareaRealizada = atr.idActaTareaRealizada
				LEFT JOIN actaTareasRealizada_equipamientos as atreq on atreq.idActaTareaRealizada = atr.idActaTareaRealizada
				LEFT JOIN actaTareasRealizada_vehiculos as atrv on atrv.idActaTareaRealizada = atr.idActaTareaRealizada
				LEFT JOIN tiposActaTareaRealizada as tatr on tatr.idTipoActaTareaRealizada = atr.idTipoActaTareaRealizada
				where true';

		if($data['idEmpleado']){
			$sql .= ' and atr.idEmpleado = '. $data['idEmpleado'];
		}
		if($data['fechaDesde']){
			$sql .= ' and atr.fechaDesde >= "'. convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and atr.fechaDesde <= "'. convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= ' order by atr.fechaDesde desc';
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$arrayRows = setHtmlEntityDecode($rs);
				$sheet_name = 'Hoja1';
				$writer = new XLSXWriter();
				foreach ($arrayRows[0] as $key => $value) {
					$header[$key] = 'string';// indica cómo será tomado el campo por el Excel
				}
				$header['Cantidad'] = '#,##0.00';// indica cómo será tomado el campo por el Excel

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
				$this->result->setMessage("La consulta no tiene registros.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}
	/*
	 * Esta func recibe un parametro para el listado asincronico, si tiene datos hace la consulta con la base, si es null trae los parametros y nombres del columnas para el datatable
	 */
	public function getDataTableData($postData, $data = null){
		try{
			$sql = 'select atr.*, 
						cgp.nombreReferencia ,
						DATE_FORMAT(atr.fechaDesde,"%d/%m/%Y") as fechaDesdeES,
						DATE_FORMAT(atr.fechaHasta,"%d/%m/%Y") as fechaHastaES,
						getCodigoATR(atr.idActaTareaRealizada) codigoATR,
						getEmpleado(atr.idEmpleado) empleadoCarga,
						getCodigoContratoGerenciaProyecto(cgp.idContratoGerenciaProyecto) getCodigoContratoGerenciaProyecto,
						getEmpleado(e.idEmpleado) as recepcionEmpleado,
						tatr.tipoActaTareaRealizada
					from actaTareasRealizada as atr
					left join empleados as e on e.idEmpleado = atr.recepcionIdEmpleado
					INNER join contratosGerenciasProyectos as cgp on cgp.idContratoGerenciaProyecto = atr.idContratoGerenciaProyecto
					inner join tiposActaTareaRealizada as tatr on tatr.idTipoActaTareaRealizada = atr.idTipoActaTareaRealizada
					where true and atr.idEmpleado ='.$data['idEmpleado'];
//			die($sql);
			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				foreach ($dataSql['data'] as $row) {
					$auxRow['idActaTareaRealizada'] = $row['idActaTareaRealizada'];
					$auxRow['nroActaTareaRealizada'] = $row['nroActaTareaRealizada'];
					$auxRow['nombreReferencia'] = $row['nombreReferencia'];
					$auxRow['getCodigoContratoGerenciaProyecto'] = $row['getCodigoContratoGerenciaProyecto'];
					$auxRow['tipoActaTareaRealizada'] = $row['tipoActaTareaRealizada'];
					$auxRow['nroOrdenTrabajo'] = $row['nroOrdenTrabajo'];
					$auxRow['fechaDesde'] = $row['fechaDesde'];
					$auxRow['fechaDesdeES'] = $row['fechaDesdeES'];
					$auxRow['fechaHasta'] = $row['fechaHasta'];
					$auxRow['fechaHastaES'] = $row['fechaHastaES'];
					$auxRow['empleadoCarga'] = $row['empleadoCarga'];
//					$auxRow['codigoATR'] = $row['codigoATR'];
					$auxRow['codigoATR'] = $row['recepcionFecha'] ? 'Código: '.$row['codigoATR'].' \ Fecha recepción:'.convertDateDbToEs($row['recepcionFecha']).' \ Recepcionó: '.$row['recepcionEmpleado'] : 'No Asignado';
					$auxRow['recepcionFecha'] = $row['recepcionFecha'];
					$auxRow['recepcionIdEmpleado'] = $row['recepcionIdEmpleado'];
					$auxRow['recepcionEmpleado'] = $row['recepcionEmpleado'];
					$auxRow['detalle'] = $row['detalle'];
					$auxRow['observaciones'] = limpiarCampoWysihtml5($row['observaciones']);
					$auxRow['accion'] = '';
					$auxRow['accion'] .= '
								<a class="text-black" href="../pdfs/ActaTareaRealizadaPDF.php?'.codificarGets('id='.$row['idActaTareaRealizada'].'&action=pdf'). '" target="_blank" title="Guía de Ruta en PDF"><span class="fa fa-file-pdf-o fa-lg"></span></a>&nbsp;&nbsp;';
					$auxRow['accion'] .= !$row['recepcionFecha'] ? '<a class="text-black"  href= "../pages/ABMactaTareasRealizada.php?' . codificarGets('id=' . $row['idActaTareaRealizada'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
                                <a class="text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idActaTareaRealizada'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>' : '';
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
		public function getDataTableProperties(){

		$objectPropierties = null;   //Nombre: Indica nombre de los campos que se mostraran en el excel y el Datatable
		$objectPropierties[] = array('nombre' => 'idActaTareaRealizada',                 'dbFieldName' => 'idActaTareaRealizada',  'visibleDT' => false, 'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'nro Acta Tarea Realizada',             'dbFieldName' => 'nroActaTareaRealizada', 'visibleDT' => false, 'visibleDTexport' => false ,  'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'nombre de referencia',                 'dbFieldName' => 'nombreReferencia',      'visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'tipo de Tarea Realizada',              'dbFieldName' => 'tipoActaTareaRealizada','visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'nro Orden de Trabajo',                 'dbFieldName' => 'nroOrdenTrabajo',       'visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'fechaDesdeEN' ,                        'dbFieldName' => 'fechaDesde',            'visibleDT' => false, 'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fecha desde',                          'dbFieldName' => 'fechaDesdeES' ,         'visibleDT' => true,  'visibleDTexport' => true ,   'className' => false,       'aDataSort' => 'fechaDesdeEN','bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fechaHastaEN',                         'dbFieldName' => 'fechaHasta',            'visibleDT' => false, 'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,          'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fecha Hasta',                          'dbFieldName' => 'fechaHastaES',          'visibleDT' => true,  'visibleDTexport' => true,    'className' => false,        'aDataSort' => 'fechaHastaEN','bSortable' => true,     'searchable' => true   );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'Proyecto',                 'dbFieldName' => 'getCodigoContratoGerenciaProyecto', 'visibleDT' => true, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false  );   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'código',                               'dbFieldName' => 'codigoATR',             'visibleDT' => true,  'visibleDTexport' => true,    'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fecha Recepción',                      'dbFieldName' => 'recepcionFecha',        'visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'recepcionIdEmpleado',                  'dbFieldName' => 'recepcionIdEmpleado',   'visibleDT' => false, 'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'detalle',                              'dbFieldName' => 'detalle',               'visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'observaciones',                        'dbFieldName' => 'observaciones',         'visibleDT' => false, 'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true  );   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'opciones',                             'dbFieldName' => 'accion',                'visibleDT' => true,  'visibleDTexport' => false,   'className' => 'center',    'aDataSort' => false,         'bSortable' => false,    'searchable' => false  );

		$listadoDatosCargados['columnas'] = $objectPropierties;


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
		$aaSorting = '"aaSorting": [[7,"desc"]],';
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

		return $listadoDatosCargados;// Retorna campos,columns, aoColumnDefs y aaSorting
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getActaTareaRealizadaAlerta') {
	$aux = new ActaTareaRealizadaVO();
	$data['idProyectoComision'] = $_GET['idProyectoComision'];
	//print_r($data); die();
	$aux->{$_GET['type']}($data);
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getActaTareaRealizadaParaGantt'){
	$aux = new ActaTareaRealizadaVO();
	$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	$data['start'] = $_POST['start'];
	$data['end'] = $_POST['end'];
	$data['mod'] = $_POST['mod'];
	//print_r($data); die();
	$aux->{$_POST['type']}($data);
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getRegistroDeActaTareaRealizadaParaGantt'){
	$aux = new ActaTareaRealizadaVO();
	//$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	$data['start'] = $_POST['start'];
	$data['end'] = $_POST['end'];
	$data['mod'] = $_POST['mod'];
	//print_r($data); die();
	$aux->{$_POST['type']}($data);
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMactaTareasRealizada.php')){
	$aux = new ActaTareaRealizadaVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		if (empty($_POST) ) {
			$aux->getDataTableProperties();
		} else {
			$data = array();
			$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
			$aux->getDataTableData($_POST, $data);
		}
	}
}

// debug zone
if($_GET['debug'] == 'ActaTareaRealizadaVO' or false){
    echo "DEBUG<br>";
    $kk = new ActaTareaRealizadaVO();
	//fc_print($kk->getAtributosPermitidos());
    //fc_print($kk->getAllRows());
    //$kk->idProyectoUnidadEconomica = 116;
    //$kk->usuario = 'hhh2';
    //fc_print($kk->getRowById());
    //fc_print($kk->insertData());
    //fc_print($kk->updateData());
    //fc_print($kk->deleteData());
    //echo $kk->getResultMessage();
}
