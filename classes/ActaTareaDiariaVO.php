<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR .'../../plugins/PHP_XLSXWriter-master/xlsxwriter.class.php');
/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaDiariaVO extends Master2 implements  iListadoExportableDinamico{
    public $idActaTareaDiaria = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
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
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado",
		"referencia" => "",
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
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

	public $planificacionProyectoExternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaPlanificacionProyectoExternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppe', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaPlanificacionProyectoExterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $planificacionProyectoInternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaPlanificacionProyectoInternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppi', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaPlanificacionProyectoInterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $planificacionProyectoComisionArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaProyectoComisionVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppc', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaProyectoComision', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $planificacionViajeArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaViajeVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'pv', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaViaje', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $proyectoExternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaProyectoExternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'pe', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaProyectoExterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $proyectoInternoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaProyectoInternoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'pi', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaProyectoInterno', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiaria'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('actaTareasDiaria');
	    $this->setFieldIdName('idActaTareaDiaria');
	    $this->idEmpleado['referencia'] = new EmpleadoVO();
	    $this->recepcionIdEmpleado['referencia'] = new EmpleadoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
    	//if($operacion == INSERT){
		    $this->idEmpleado['valor'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	    //}
        return $resultMessage;
    }

	public function getCodigoActaTareaDiaria($nombre = null, $apellido, $fecha = null){
		$codigo = 'ATD';
		if($nombre && $apellido && $fecha){
			$codigo .= '-' .strtoupper(substr($nombre,0,1).substr($apellido,0,1));
			$codigo .= '-' . str_replace('-', '', convertDateEsToDb($fecha));
		}else {
			$codigo .= '-' . $this->idEmpleado['referencia']->getIniciales();
			$codigo .= '-' . str_replace('-', '', convertDateEsToDb($this->fechaDesde['valor']));
		}
		return $codigo;
	}

	/*
	 * este método lo uso ya que necesito solo updatear la recepcion de la misma y no todas la entidades anexas a ella...
	 */
	public function updateData2(){
		parent::updateData();
	}

	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'select idActaTareaDiaria, fechaDesde, "ATD"  as tipoActaTarea, getEmpleado(idEmpleado) as empleado, recepcionFecha, getEmpleado(recepcionIdEmpleado) as recepcionEmpleado
				from actaTareasDiaria as atd';
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

	function getActaTareaDiariaPDF(){
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
			$this->getRowWithItemsById();
			//fc_print($this, true);

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
					</style>';

			$pageHeader  = $css;
			$pageHeader .= '<page_header>';
			$pageHeader .= '	<table cellspacing="0" style="margin: 10px 40px;">
									<tr>
										<td style="width: 50%">
											<table>
												<tr>
													<td width="168px"><img src="'.getFullPath().'/img/logo-sinec-nuevo-295x217.jpg" alt="" width="120" style="margin: -10px 0 0 -20px;" alt="" /></td>
												</tr>
											</table>
										</td>
										<td style="width: 50%">
											<table class="borderYes">
												<thead>
													<tr>
														<th colspan="2" style="width: 300px;">ACTA DE TAREA DIARIA</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Fecha:</td>
														<td>'.convertDateDbToEs($this->fechaDesde['valor']).'</td>
													</tr>
													<tr>
														<td>Empleado:</td>
														<td>'.$this->idEmpleado['referencia']->getNombreCompleto().'</td>
													</tr>
													<tr>
														<td>Código de acta:</td>
														<td>'.$this->getCodigoActaTareaDiaria().'</td>
													</tr>';
			if($this->recepcionFecha['valor']) {
				$pageHeader .= ' 					<tr>
														<td>Aprobó:</td>
														<td>'.$this->recepcionIdEmpleado['referencia']->getNombreCompleto().'</td>
													</tr>
													<tr>
														<td>Fecha:</td>
														<td>'.convertDateDbToEs($this->recepcionFecha['valor']).'</td>
													</tr>';
			}
			$pageHeader .= '					</tbody>
											</table>
										</td>
									</tr>
								</table>';
			$pageHeader .= '</page_header>';

			$pageFooter = '<page_footer>';
			$pageFooter .= '	<p align="center">P&aacute;gina [[page_cu]]/[[page_nb]]</p>';
			$pageFooter .= '</page_footer>';

			$pageBegin = '<page backtop="35mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="P">';

			$pageBody = '<table >
								<tr>
									<td>Obs: '.$this->observaciones['valor'].'</td>
								</tr>';
			$pageBody .= ' </table>';


			$pageBody .= '<br><div class="tituloTabla" style="">Labores Planificadas en Proyectos Externos</div>';
			//print_r($arrayName);die();
			if($this->planificacionProyectoExternoArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->planificacionProyectoExternoArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Contrato:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idContrato['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Gerencia:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Proyecto:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->idServicioAreaDeAplicacion['referencia']->servicioAreaDeAplicacion['valor'] . ' \ '. $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->servicioActividad['valor']  . ' \ '. $arrayName->idServicioTarea['referencia']->servicioTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' ('. $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
								';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div class="tituloTabla" style="">Labores en Proyectos Internos</div>';
			//print_r($arrayName);die();
			if($this->planificacionProyectoInternoArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->planificacionProyectoInternoArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
								';
					if($arrayName->planificacionProyectoInternoLaborTareaArray['objectVOArray']) {
						foreach ($arrayName->planificacionProyectoInternoLaborTareaArray['objectVOArray'] as $arrayName) {
							$pageBody .= '
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->idLaborAreaDeAplicacion['referencia']->laborAreaDeAplicacion['valor'] . ' \ ' . $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->laborActividad['valor'] . ' \ ' . $arrayName->idLaborTarea['referencia']->laborTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' (' . $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
								';
						}
					}
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div class="tituloTabla" style="">Comisiones de Viaje</div>';
			//fc_print($arrayName);die();
			if($this->planificacionProyectoComisionArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->planificacionProyectoComisionArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Contrato:</td>
										<td style="width: 85%; ">' . $arrayName->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idContrato['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Gerencia:</td>
										<td style="width: 85%; ">' . $arrayName->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Proyecto:</td>
										<td style="width: 85%; ">' . $arrayName->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Localización:</td>
										<td style="width: 85%; ">' . $arrayName->idProyectoComision['referencia']->idContratoGerenciaLocalizacion['referencia']->idProvincia['referencia']->provincia['valor'] . ' \ ' . $arrayName->idProyectoComision['referencia']->idContratoGerenciaLocalizacion['referencia']->localizacion['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
									';
					if($arrayName->planificacionProyectoComisionServicioTareaArray['objectVOArray']) {
						foreach ($arrayName->planificacionProyectoComisionServicioTareaArray['objectVOArray'] as $arrayName) {
							$pageBody .= '
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->idServicioAreaDeAplicacion['referencia']->servicioAreaDeAplicacion['valor'] . ' \ ' . $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->servicioActividad['valor'] . ' \ ' . $arrayName->idServicioTarea['referencia']->servicioTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' (' . $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
								';
						}
					}
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div class="tituloTabla" style="">Viajes SINEC</div>';
			//fc_print($arrayName);die();
			if($this->planificacionViajeArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->planificacionViajeArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Destino:</td>
										<td style="width: 85%; ">' . $arrayName->idViaje['referencia']->idDestino['referencia']->idProvincia['referencia']->provincia['valor'] . ' \ ' . $arrayName->idViaje['referencia']->idDestino['referencia']->destino['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
									';
					if($arrayName->planificacionViajeLaborTareaArray['objectVOArray']) {
						foreach ($arrayName->planificacionViajeLaborTareaArray['objectVOArray'] as $arrayName) {
							$pageBody .= '
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->idLaborAreaDeAplicacion['referencia']->laborAreaDeAplicacion['valor'] . ' \ ' . $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->laborActividad['valor'] . ' \ ' . $arrayName->idLaborTarea['referencia']->laborTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' (' . $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
								';
						}
					}
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div class="tituloTabla" style="">Otras Labores en Proyectos Externos</div>';
			//print_r($arrayName);die();
			if($this->proyectoExternoArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->proyectoExternoArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Contrato:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idContrato['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Gerencia:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Proyecto:</td>
										<td style="width: 85%; ">' . $arrayName->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->idServicioAreaDeAplicacion['referencia']->servicioAreaDeAplicacion['valor'] . ' \ '. $arrayName->idServicioTarea['referencia']->idServicioActividad['referencia']->servicioActividad['valor']  . ' \ '. $arrayName->idServicioTarea['referencia']->servicioTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' ('. $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
								';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div class="tituloTabla" style="">Otras Labores en Proyectos Internos</div>';
			//print_r($arrayName);die();
			if($this->proyectoInternoArray['objectVOArray']){
				$pageBody .= '<table style="" class="borderYes">';
				foreach ($this->proyectoInternoArray['objectVOArray'] as $arrayName) {
					$pageBody .= '	
									<tr>
										<td style="width: 15%; background-color: #ddd;">Tarea:</td>
										<td style="width: 85%; ">' . $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->idLaborAreaDeAplicacion['referencia']->laborAreaDeAplicacion['valor'] . ' \ '. $arrayName->idLaborTarea['referencia']->idLaborActividad['referencia']->laborActividad['valor']  . ' \ '. $arrayName->idLaborTarea['referencia']->laborTarea['valor'] . '</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Cantidad:</td>
										<td style="width: 85%; ">' . number_format($arrayName->cantidad['valor'], 2, ',', '') . ' ('. $arrayName->idTipoUnidadMedidaLabor['referencia']->tipoUnidadMedidaLabor['valor'] . ')</td>
									</tr>
									<tr>
										<td style="width: 15%; background-color: #ddd;">Obs.:</td>
										<td style="width: 85%; ">' . $arrayName->observaciones['valor'] . '</td>
									</tr>
								';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			$pageEnd = '</page>';

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return html_entity_decode($pages, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	public function getActaTareaDiariaParaGantt($data){
		//fc_print($data, true);
		try{
			if($data['mod'] == 'resources') {
				//fc_print($data, true);

				// primero traemos lo referente a proyectos externos y comisiones
				$sql = 'SELECT id, idHijo, title, null as eventColor, null as children
						from actaTareasDiaria
						inner join (
							select CONCAT_WS("_", "a", c.idContrato) as id, c.idContrato as idHijo, c.nombreReferencia as title, idActaTareaDiaria
							FROM actaTareasDiaria_planificacionProyectosExternos as ppe
							inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
							inner join contratosGerencias as cg using (idContratoGerencia)
							INNER JOIN contratos as c using (idContrato)
							inner join serviciosTarea as st using (idServicioTarea)
							inner join serviciosActividad as sa using (idServicioActividad)
							inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
						
							union 
						
							select CONCAT_WS("_", "a", c.idContrato) as id, c.idContrato as idHijo, c.nombreReferencia as title, idActaTareaDiaria
							FROM actaTareasDiaria_proyectosComisiones as apc
							INNER JOIN actaTareasDiariaProyectosComisiones_serviciosTarea as apcst using (idActaTareaDiariaProyectoComision)
							INNER JOIN proyectosComisiones as pc using (idProyectoComision)
							inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
							inner join contratosGerencias as cg using (idContratoGerencia)
							INNER JOIN contratos as c using (idContrato)
							inner join serviciosTarea as st on apcst.idServicioTarea = st.idServicioTarea
							inner join serviciosActividad as sa on st.idServicioActividad = sa.idServicioActividad
							inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
						
							union 
						
							select CONCAT_WS("_", "a", c.idContrato) as id, c.idContrato as idHijo, c.nombreReferencia as title, idActaTareaDiaria
							FROM actaTareasDiaria_proyectosExternos as pe
							inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
							inner join contratosGerencias as cg using (idContratoGerencia)
							INNER JOIN contratos as c using (idContrato)
							inner join serviciosTarea as st using (idServicioTarea)
							inner join serviciosActividad as sa using (idServicioActividad)
							inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
						
						) as c using (idActaTareaDiaria)
						where idEmpleado = ' . $data['idEmpleado'] .'
						group by idHijo';
				//die($sql);
				//echo $sql;
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				if ($rs) {
					foreach ($rs as &$aux1) {
						$sql = 'SELECT concat_ws("_", id, "' . $aux1['id'] .'") as id, idHijo, idPadre, title, null as eventColor, null as children
								from actaTareasDiaria
								inner join (
									select CONCAT_WS("_", "b", cg.idContratoGerencia) as id, cg.idContratoGerencia as idHijo, c.idContrato as idPadre, cg.gerencia as title, idActaTareaDiaria
									FROM actaTareasDiaria_planificacionProyectosExternos as ppe
									inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
									inner join contratosGerencias as cg using (idContratoGerencia)
									INNER JOIN contratos as c using (idContrato)
									inner join serviciosTarea as st using (idServicioTarea)
									inner join serviciosActividad as sa using (idServicioActividad)
									inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								
									union 
								
									select CONCAT_WS("_", "b", cg.idContratoGerencia) as id, cg.idContratoGerencia as idHijo, c.idContrato as idPadre, cg.gerencia as title, idActaTareaDiaria
									FROM actaTareasDiaria_proyectosComisiones as apc
									INNER JOIN actaTareasDiariaProyectosComisiones_serviciosTarea as apcst using (idActaTareaDiariaProyectoComision)
									INNER JOIN proyectosComisiones as pc using (idProyectoComision)
									inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
									inner join contratosGerencias as cg using (idContratoGerencia)
									INNER JOIN contratos as c using (idContrato)
									inner join serviciosTarea as st on apcst.idServicioTarea = st.idServicioTarea
									inner join serviciosActividad as sa on st.idServicioActividad = sa.idServicioActividad
									inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								
									union 
								
									select CONCAT_WS("_", "b", cg.idContratoGerencia) as id, cg.idContratoGerencia as idHijo, c.idContrato as idPadre, cg.gerencia as title, idActaTareaDiaria
									FROM actaTareasDiaria_proyectosExternos as pe
									inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
									inner join contratosGerencias as cg using (idContratoGerencia)
									INNER JOIN contratos as c using (idContrato)
									inner join serviciosTarea as st using (idServicioTarea)
									inner join serviciosActividad as sa using (idServicioActividad)
									inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								
								) as g using (idActaTareaDiaria)
								where idEmpleado = ' . $data['idEmpleado'] .' and idPadre = ' . $aux1['idHijo'] .'
								group by idHijo';
						//die($sql);
						//echo $sql;
						$ro = $this->conn->prepare($sql);
						$ro->execute();
						$rs2 = $ro->fetchAll(PDO::FETCH_ASSOC);
						if ($rs2) {
							$aux1['children'] = $rs2;
							foreach ($aux1['children'] as &$aux2) {
								$sql = 'SELECT concat_ws("_", id, "' . $aux2['id'] .'") as id, idHijo, idPadre, title, null as eventColor, null as children
										from actaTareasDiaria
										inner join (
											select CONCAT_WS("_", "c", cgp.idContratoGerenciaProyecto) as id, cgp.idContratoGerenciaProyecto as idHijo, cg.idContratoGerencia as idPadre, cgp.nombreReferencia as title, idActaTareaDiaria
											FROM actaTareasDiaria_planificacionProyectosExternos as ppe
											inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
											inner join contratosGerencias as cg using (idContratoGerencia)
											INNER JOIN contratos as c using (idContrato)
											inner join serviciosTarea as st using (idServicioTarea)
											inner join serviciosActividad as sa using (idServicioActividad)
											inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
										
											union 
										
											select CONCAT_WS("_", "c", cgp.idContratoGerenciaProyecto) as id, cgp.idContratoGerenciaProyecto as idHijo, cg.idContratoGerencia as idPadre, cgp.nombreReferencia as title, idActaTareaDiaria
											FROM actaTareasDiaria_proyectosComisiones as apc
											INNER JOIN actaTareasDiariaProyectosComisiones_serviciosTarea as apcst using (idActaTareaDiariaProyectoComision)
											INNER JOIN proyectosComisiones as pc using (idProyectoComision)
											inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
											inner join contratosGerencias as cg using (idContratoGerencia)
											INNER JOIN contratos as c using (idContrato)
											inner join serviciosTarea as st on apcst.idServicioTarea = st.idServicioTarea
											inner join serviciosActividad as sa on st.idServicioActividad = sa.idServicioActividad
											inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
										
											union 
										
											select CONCAT_WS("_", "c", cgp.idContratoGerenciaProyecto) as id, cgp.idContratoGerenciaProyecto as idHijo, cg.idContratoGerencia as idPadre, cgp.nombreReferencia as title, idActaTareaDiaria
											FROM actaTareasDiaria_proyectosExternos as pe
											inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
											inner join contratosGerencias as cg using (idContratoGerencia)
											INNER JOIN contratos as c using (idContrato)
											inner join serviciosTarea as st using (idServicioTarea)
											inner join serviciosActividad as sa using (idServicioActividad)
											inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
										
										) as g using (idActaTareaDiaria)
										where idEmpleado = ' . $data['idEmpleado'] .' and idPadre = ' . $aux2['idHijo'] .'
										group by idHijo';
								//die($sql);
								//echo $sql;
								$ro = $this->conn->prepare($sql);
								$ro->execute();
								$rs3 = $ro->fetchAll(PDO::FETCH_ASSOC);
								if ($rs3) {
									$aux2['children'] = $rs3;
									foreach ($aux2['children'] as &$aux3) {
										$sql = 'SELECT concat_ws("_", id, "' . $aux3['id'] .'") as id, idHijo, idPadre, title, null as eventColor, null as children
												from actaTareasDiaria
												inner join (
													select CONCAT_WS("_", "d", sa.idServicioActividad) as id, sa.idServicioActividad as idHijo, cgp.idContratoGerenciaProyecto as idPadre, servicioActividad as title, idActaTareaDiaria
													FROM actaTareasDiaria_planificacionProyectosExternos as ppe
													inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
													inner join contratosGerencias as cg using (idContratoGerencia)
													INNER JOIN contratos as c using (idContrato)
													inner join serviciosTarea as st using (idServicioTarea)
													inner join serviciosActividad as sa using (idServicioActividad)
													inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
												
													union 
												
													select CONCAT_WS("_", "d", sa.idServicioActividad) as id, sa.idServicioActividad as idHijo, cgp.idContratoGerenciaProyecto as idPadre, servicioActividad as title, idActaTareaDiaria
													FROM actaTareasDiaria_proyectosComisiones as apc
													INNER JOIN actaTareasDiariaProyectosComisiones_serviciosTarea as apcst using (idActaTareaDiariaProyectoComision)
													INNER JOIN proyectosComisiones as pc using (idProyectoComision)
													inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
													inner join contratosGerencias as cg using (idContratoGerencia)
													INNER JOIN contratos as c using (idContrato)
													inner join serviciosTarea as st on apcst.idServicioTarea = st.idServicioTarea
													inner join serviciosActividad as sa on st.idServicioActividad = sa.idServicioActividad
													inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
												
													union 
												
													select CONCAT_WS("_", "d", sa.idServicioActividad) as id, sa.idServicioActividad as idHijo, cgp.idContratoGerenciaProyecto as idPadre, servicioActividad as title, idActaTareaDiaria
													FROM actaTareasDiaria_proyectosExternos as pe
													inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
													inner join contratosGerencias as cg using (idContratoGerencia)
													INNER JOIN contratos as c using (idContrato)
													inner join serviciosTarea as st using (idServicioTarea)
													inner join serviciosActividad as sa using (idServicioActividad)
													inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
												
												) as g using (idActaTareaDiaria)
												where idEmpleado = ' . $data['idEmpleado'] .' and idPadre = ' . $aux3['idHijo'] .'
												group by id';
										//die($sql);
										//echo $sql;
										$ro = $this->conn->prepare($sql);
										$ro->execute();
										$rs4 = $ro->fetchAll(PDO::FETCH_ASSOC);
										if ($rs4) {
											$aux3['children'] = $rs4;
										}
									}
								}
							}
						}
					}
				}
				//fc_print($rs, true);
				$arrayData = $rs;

				// para lo que es Proyectos internos el manejo es diferente
				$sql = 'SELECT id, idHijo, title, null as eventColor, null as children
						from actaTareasDiaria
						inner join (
							select CONCAT_WS("_", "aa", sada.idLaborAreaDeAplicacion) as id, sada.idLaborAreaDeAplicacion as idHijo, laborAreaDeAplicacion as title, idActaTareaDiaria
							FROM actaTareasDiaria_planificacionProyectosInternos as ppi
							inner join actaTareasDiariaPlanificacionProyectosInternos_laboresTarea as atdppilt using (idActaTareaDiariaPlanificacionProyectoInterno)
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
						
							union 
						
							select CONCAT_WS("_", "aa", sada.idLaborAreaDeAplicacion) as id, sada.idLaborAreaDeAplicacion as idHijo, laborAreaDeAplicacion as title, idActaTareaDiaria
							FROM actaTareasDiaria_proyectosInternos as pi
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
							
							union 
						
							select CONCAT_WS("_", "aa", sada.idLaborAreaDeAplicacion) as id, sada.idLaborAreaDeAplicacion as idHijo, laborAreaDeAplicacion as title, idActaTareaDiaria
							FROM actaTareasDiaria_viajes as atdv
							inner JOIN actaTareasDiariaViajes_laboresTarea as atdvlt using (idActaTareaDiariaViaje)
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
						
						) as ppi using (idActaTareaDiaria)
						where idEmpleado = ' . $data['idEmpleado'] .'
						group by idHijo, title';
				//die($sql);
				//echo $sql;
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				if ($rs) {
					foreach ($rs as &$aux1) {
						$sql = 'SELECT concat_ws("_", id, "' . $aux1['id'] .'") as id, idHijo, idPadre, title, null as eventColor, null as children
								from actaTareasDiaria
								inner join (
									select CONCAT_WS("_", "bb", sa.idLaborActividad) as id, sa.idLaborActividad as idHijo, sada.idLaborAreaDeAplicacion as idPadre, laborActividad as title, idActaTareaDiaria
									FROM actaTareasDiaria_planificacionProyectosInternos as ppi
									inner join actaTareasDiariaPlanificacionProyectosInternos_laboresTarea as atdppilt using (idActaTareaDiariaPlanificacionProyectoInterno)
									inner join laboresTarea as st using (idLaborTarea)
									inner join laboresActividad as sa using (idLaborActividad)
									inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
								
									union 
								
									select CONCAT_WS("_", "bb", sa.idLaborActividad) as id, sa.idLaborActividad as idHijo, sada.idLaborAreaDeAplicacion as idPadre, laborActividad as title, idActaTareaDiaria
									FROM actaTareasDiaria_proyectosInternos as pi
									inner join laboresTarea as st using (idLaborTarea)
									inner join laboresActividad as sa using (idLaborActividad)
									inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
									
									union 
								
									select CONCAT_WS("_", "bb", sa.idLaborActividad) as id, sa.idLaborActividad as idHijo, sada.idLaborAreaDeAplicacion as idPadre, laborActividad as title, idActaTareaDiaria
									FROM actaTareasDiaria_viajes as atdv
									inner JOIN actaTareasDiariaViajes_laboresTarea as atdvlt using (idActaTareaDiariaViaje)
									inner join laboresTarea as st using (idLaborTarea)
									inner join laboresActividad as sa using (idLaborActividad)
									inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
								
								) as ppi using (idActaTareaDiaria)
								where idEmpleado = ' . $data['idEmpleado'] .' and idPadre = ' . $aux1['idHijo'] .'
								group by idHijo, title';
						//die($sql);
						//echo $sql;
						$ro = $this->conn->prepare($sql);
						$ro->execute();
						$rs2 = $ro->fetchAll(PDO::FETCH_ASSOC);
						if ($rs2) {
							$aux1['children'] = $rs2;
						}
					}
				}
				$arrayData = array_merge($arrayData, $rs);
				//$arrayData[] = null;
				//fc_print($arrayData, true);
				//$arrayData = array_unique($arrayData);
			} else if($data['mod'] == 'events') {
				// traigo los eventos para los proyectos externos y comisiones
				$sql = 'SELECT id, idHijo, idPadre, title, idActaTareaDiaria, resourceId,  backgroundColor, start, end
							, concat_ws("<br>"
								, title
								, concat("Obs: ", description)
								) as description ';
				if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
					$sql .= ' , "ActaTareaDiariaPDF.php?" as urlAddress, concat("id=", idActaTareaDiaria) as urlParamaters, null as url ';
				}
				$sql .= '   from actaTareasDiaria
							inner join (
								select CONCAT_WS("_", "z", st.idServicioTarea) as id
									, st.idServicioTarea as idHijo
									, sa.idServicioActividad as idPadre
									, concat("(", sum(cantidad), ") (p) ", servicioTarea) as title
									, idActaTareaDiaria
									, CONCAT("d_", sa.idServicioActividad, "_c_", cgp.idContratoGerenciaProyecto, "_b_", cg.idContratoGerencia, "_a_", c.idContrato) as resourceId
									, "#F39C12" as backgroundColor
									, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
									, ppe.observaciones as description
								FROM actaTareasDiaria_planificacionProyectosExternos as ppe
								inner join actaTareasDiaria as atd using (idActaTareaDiaria)
								inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
								inner join contratosGerencias as cg using (idContratoGerencia)
								INNER JOIN contratos as c using (idContrato)
								inner join serviciosTarea as st using (idServicioTarea)
								inner join serviciosActividad as sa using (idServicioActividad)
								inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								group by idEmpleado, resourceId, idHijo, fechaDesde
							
								union 
							
								select CONCAT_WS("_", "z", st.idServicioTarea) as id
									, st.idServicioTarea as idHijo
									, sa.idServicioActividad as idPadre
									, concat("(", sum(cantidad), ") ", servicioTarea) as title
									, idActaTareaDiaria
									, CONCAT("d_", sa.idServicioActividad, "_c_", cgp.idContratoGerenciaProyecto, "_b_", cg.idContratoGerencia, "_a_", c.idContrato) as resourceId
									, "#00A65A" as backgroundColor
									, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
									, apcst.observaciones as description
								FROM actaTareasDiaria_proyectosComisiones as apc
								inner join actaTareasDiaria as atd using (idActaTareaDiaria)
								INNER JOIN actaTareasDiariaProyectosComisiones_serviciosTarea as apcst using (idActaTareaDiariaProyectoComision)
								INNER JOIN proyectosComisiones as pc using (idProyectoComision)
								inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
								inner join contratosGerencias as cg using (idContratoGerencia)
								INNER JOIN contratos as c using (idContrato)
								inner join serviciosTarea as st on apcst.idServicioTarea = st.idServicioTarea
								inner join serviciosActividad as sa on st.idServicioActividad = sa.idServicioActividad
								inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								group by idEmpleado, resourceId, idHijo, fechaDesde
							
								union 
							
								select CONCAT_WS("_", "z", st.idServicioTarea) as id
									, st.idServicioTarea as idHijo
									, sa.idServicioActividad as idPadre
									, concat("(", sum(cantidad), ") ", servicioTarea) as title
									, idActaTareaDiaria
									, CONCAT("d_", sa.idServicioActividad, "_c_", cgp.idContratoGerenciaProyecto, "_b_", cg.idContratoGerencia, "_a_", c.idContrato) as resourceId
									, "#F39C12" as backgroundColor
									, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
									, pe.observaciones as description
								FROM actaTareasDiaria_proyectosExternos as pe
								inner join actaTareasDiaria as atd using (idActaTareaDiaria)
								inner join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
								inner join contratosGerencias as cg using (idContratoGerencia)
								INNER JOIN contratos as c using (idContrato)
								inner join serviciosTarea as st using (idServicioTarea)
								inner join serviciosActividad as sa using (idServicioActividad)
								inner join serviciosAreaDeAplicacion as sada using (idServicioAreaDeAplicacion)
								group by idEmpleado, resourceId, idHijo, fechaDesde
							
							) as g using (idActaTareaDiaria) ';
				$sql .= ' where idEmpleado = ' . $data['idEmpleado'];
				$sql .= ' and ((start BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (end BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (start < "'.convertDateEsToDb($data['start']).'" and end > "'.convertDateEsToDb($data['end']).'")) ';
				//die($sql);
				//echo $sql;
				$ro = $this->conn->prepare($sql);
				$ro->execute();
				$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rs as &$aux){
					if($aux['urlAddress']){
						$aux['url'] = $aux['urlAddress'] . codificarGets($aux['urlParamaters']);
						$aux['urlParamaters'] = null;
					}
				}
				$arrayData = $rs;

				// traigo los eventos para los proyectos internos
				$sql = 'SELECT id, idHijo, idPadre, title, idActaTareaDiaria, resourceId,  backgroundColor, start, end
							, concat_ws("<br>"
								, title
								, concat("Obs: ", description)
								) as description ';
				if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
					$sql .= ' , "ActaTareaDiariaPDF.php?" as urlAddress, concat("id=", idActaTareaDiaria) as urlParamaters, null as url ';
				}
				$sql .= ' from actaTareasDiaria
						inner join (
							select CONCAT_WS("_", "zz", st.idLaborTarea) as id
										, st.idLaborTarea as idHijo
										, sa.idLaborActividad as idPadre
										, concat("(", sum(cantidad), ") (p) ", laborTarea) as title
										, idActaTareaDiaria
										, CONCAT("bb_", sa.idLaborActividad, "_aa_", sada.idLaborAreaDeAplicacion) as resourceId
										, "#00C0EF" as backgroundColor
										, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
										, ppi.observaciones as description
							FROM actaTareasDiaria_planificacionProyectosInternos as ppi
							inner join actaTareasDiariaPlanificacionProyectosInternos_laboresTarea as atdppilt using (idActaTareaDiariaPlanificacionProyectoInterno)
							inner join actaTareasDiaria as atd using (idActaTareaDiaria)
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
							group by idEmpleado, resourceId, idHijo, fechaDesde
						
							union 
						
							select CONCAT_WS("_", "zz", st.idLaborTarea) as id
										, st.idLaborTarea as idHijo
										, sa.idLaborActividad as idPadre
										, concat("(", sum(cantidad), ") ", laborTarea) as title
										, idActaTareaDiaria
										, CONCAT("bb_", sa.idLaborActividad, "_aa_", sada.idLaborAreaDeAplicacion) as resourceId
										, "#00C0EF" as backgroundColor
										, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
										, pi.observaciones as description
							FROM actaTareasDiaria_proyectosInternos as pi
							inner join actaTareasDiaria as atd using (idActaTareaDiaria)
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
							group by idEmpleado, resourceId, idHijo, fechaDesde
							
							union
							
							select CONCAT_WS("_", "zz", st.idLaborTarea) as id
										, st.idLaborTarea as idHijo
										, sa.idLaborActividad as idPadre
										, concat("(", sum(cantidad), ") ", laborTarea) as title
										, idActaTareaDiaria
										, CONCAT_WS("_", "bb", sa.idLaborActividad) as resourceId
										, "#337AB7" as backgroundColor
										, atd.fechaDesde as start, DATE_ADD(atd.fechaDesde, interval 1 day) as end
										, atdv.observaciones as description
							FROM actaTareasDiaria_viajes as atdv
							inner join actaTareasDiariaViajes_laboresTarea as atdvlt using (idActaTareaDiariaViaje)
							inner join actaTareasDiaria as atd using (idActaTareaDiaria)
							inner join laboresTarea as st using (idLaborTarea)
							inner join laboresActividad as sa using (idLaborActividad)
							inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
							group by idEmpleado, resourceId, idHijo, fechaDesde
						
						) as ppi using (idActaTareaDiaria) ';
				$sql .= ' where idEmpleado = ' . $data['idEmpleado'];
				$sql .= ' and ((start BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (end BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (start < "'.convertDateEsToDb($data['start']).'" and end > "'.convertDateEsToDb($data['end']).'")) ';
				//die($sql);
				//echo $sql;
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
			$arrayData = array_merge($arrayData, $rs);
			//fc_print($rs, true);
			echo json_encode(setHtmlEntityDecode($arrayData));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}

	public function getRegistroDeActaTareaDiariaParaGantt($data){
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
							CONCAT_WS("_", "z", atd.idActaTareaDiaria) as id
							, idActaTareaDiaria
							, "" as title
							, CONCAT_WS("_", "e", atd.idEmpleado) as resourceId
							, atd.fechaDesde as start
							, DATE_ADD(atd.fechaDesde, interval 1 day) as end
							, case when atd.recepcionFecha is null then "yellow"
									when atd.recepcionFecha is not null then "green" end as backgroundColor ';
				if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
					$sql .= ' , "ActaTareaDiariaPDF.php?" as urlAddress, concat("id=", idActaTareaDiaria) as urlParamaters, null as url ';
				}
				$sql .= 'from actaTareasDiaria as atd ';
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

	/*
	 * este método chequea si ya existe una ATD cargada para una fecha y empleado dado
	 */
	public function getActaTareaDiariaAlerta($data){
		//fc_print($data, true);
		try{
			$sql = 'select idActaTareaDiaria
					from actaTareasDiaria as atd
					where fechaDesde = "'.convertDateEsToDb($data['fecha']).'" and idEmpleado = '.$data['idEmpleado'];
			//die($sql);
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$jsonResult['message'] = 'Ya existe una ATD informada para el '.convertDateDbToEs($data['fecha']).'.';
				$jsonResult['status'] = STATUS_ERROR;
			} else {
				$jsonResult['status'] = STATUS_OK;
			}
			echo json_encode(setHtmlEntityDecode($jsonResult));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}

	public function getExcelFile($fileName, $data = null){
		$sql = 'SELECT
					atd.idActaTareaDiaria as "Id ATD",
					DATE_FORMAT(atd.fechaDesde, "%d/%m/%Y") as Fecha,
					getCodigoATD (atd.idActaTareaDiaria) AS "Código de acta",
					IF(atd.recepcionFecha, "Recepcionada", "Pendiente")  as Estado,
					atd.recepcionFecha as "Fecha de recepción",
					getEmpleado (e1.idEmpleado) AS "Empleado ATD",
					getEmpleado (e2.idEmpleado) AS "Empleado Recepcion",

					atdppi.idActaTareaDiariaPlanificacionProyectoInterno AS "Id Planificacion PI",
					getLaborTarea (atdppilt.idLaborTarea) AS "PPI Labor",
					tuml1.tipoUnidadMedidaLabor AS "PPI Unidad de medida",
					atdppilt.cantidad AS "PPI cantidad",
					atdppi.observaciones as "PPI obs.",

					atdppe.idActaTareaDiariaPlanificacionProyectoExterno AS "Id Planificacion PE",
					getContratoGerenciaProyecto (atdppe.idContratoGerenciaProyecto) AS "PPE Contrato/Gerencia/Proyecto",
					getServicioTarea (atdppe.idServicioTarea) AS "PPE Servicio Tarea",
					tuml2.tipoUnidadMedidaLabor AS "PPE Unidad de medida",
					atdppe.cantidad AS "PPE Cantidad",
					atdppe.observaciones as "PPE obs.",

					atdpc.idActaTareaDiariaProyectoComision as "Id Comisión",
					getCodigoProyectoComision (pc1.idProyectoComision) AS "Código de comisión",
					pc1.fechaInicio AS "C Inicio",
					pc1.fechaFin AS "C Fin",
					getServicioTarea (atdpcst.idServicioTarea) AS "C Servicio Tarea",
					tuml3.tipoUnidadMedidaLabor AS "C Unidad de medida",
					atdpcst.cantidad as "C Cantidad",
					atdpcst.observaciones as "C obs.",

					v.idViaje as "Id Viaje",
					getCodigoViaje (v.idViaje) AS "Código Viaje",
					v.fechaInicio AS "V inicio",
					v.fechaFin AS "V Fin",
					getLaborTarea (atdvlt.idLaborTarea) AS "V Labor Tarea",
					tuml4.tipoUnidadMedidaLabor AS "V Unidad Medida",
					atdvlt.cantidad AS "V Cantidad",
					v.observaciones as "V obs.",

					atdpe.idActaTareaDiariaProyectoExterno AS "ID P.Externo",
					getContratoGerenciaProyecto (atdpe.idContratoGerenciaProyecto) AS "PE Contrato/Gerencia/Proyecto",
					getServicioTarea (atdpe.idServicioTarea) AS "PE Servicio Tarea",
					tuml5.tipoUnidadMedidaLabor AS "PE Unidad de Medida",
					atdpe.cantidad AS "PE Cantidad",
					atdpe.observaciones AS "PE obs",

					atdpi.idActaTareaDiariaProyectoInterno AS "ID P.Interno",
					getLaborTarea (atdpi.idLaborTarea) AS "PI Labor Tarea",
					tuml6.tipoUnidadMedidaLabor AS "PI Unidad de Medida",
					atdpi.cantidad AS "PI Cantidad",
					atdpi.observaciones AS "PI obs"
				FROM
					actaTareasDiaria AS atd
				LEFT JOIN empleados AS e1 ON atd.idEmpleado = e1.idEmpleado
				LEFT JOIN empleados AS e2 ON atd.recepcionIdEmpleado = e2.idEmpleado
				LEFT JOIN actaTareasDiaria_planificacionProyectosInternos AS atdppi ON atdppi.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN actaTareasDiariaPlanificacionProyectosInternos_laboresTarea AS atdppilt ON atdppilt.idActaTareaDiariaPlanificacionProyectoInterno = atdppi.idActaTareaDiariaPlanificacionProyectoInterno
				LEFT JOIN planificaciones_proyectosInternos AS ppi ON atdppi.idPlanificacionProyectoInterno = ppi.idPlanificacionProyectoInterno
				LEFT JOIN tiposUnidadMedidaLabor AS tuml1 ON atdppilt.idTipoUnidadMedidaLabor = tuml1.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasDiaria_planificacionProyectosExternos AS atdppe ON atdppe.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN planificaciones_proyectosExternos AS ppe ON atdppe.idPlanificacionProyectoExterno = ppe.idPlanificacionProyectoExterno
				LEFT JOIN tiposUnidadMedidaLabor AS tuml2 ON atdppe.idTipoUnidadMedidaLabor = tuml2.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasDiaria_proyectosComisiones AS atdpc ON atdpc.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN proyectosComisiones AS pc1 ON pc1.idProyectoComision = atdpc.idProyectoComision
				LEFT JOIN actaTareasDiariaProyectosComisiones_serviciosTarea AS atdpcst ON atdpcst.idActaTareaDiariaProyectoComision = atdpc.idActaTareaDiariaProyectoComision
				LEFT JOIN tiposUnidadMedidaLabor AS tuml3 ON atdpcst.idTipoUnidadMedidaLabor = tuml3.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasDiaria_viajes AS atdv ON atdv.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN viajes AS v ON v.idViaje = atdv.idViaje
				LEFT JOIN actaTareasDiariaViajes_laboresTarea AS atdvlt ON atdvlt.idActaTareaDiariaViaje = atdv.idActaTareaDiariaViaje
				LEFT JOIN tiposUnidadMedidaLabor AS tuml4 ON atdvlt.idTipoUnidadMedidaLabor = tuml4.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasDiaria_proyectosExternos AS atdpe ON atdpe.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN tiposUnidadMedidaLabor AS tuml5 ON atdpe.idTipoUnidadMedidaLabor = tuml5.idTipoUnidadMedidaLabor
				LEFT JOIN actaTareasDiaria_proyectosInternos AS atdpi ON atdpi.idActaTareaDiaria = atd.idActaTareaDiaria
				LEFT JOIN tiposUnidadMedidaLabor AS tuml6 ON atdpi.idTipoUnidadMedidaLabor = tuml6.idTipoUnidadMedidaLabor 
				where TRUE ';
		if($data['idEmpleado']){
			$sql .= ' and e1.idEmpleado = '. $data['idEmpleado'];
		}
		if($data['fechaDesde']){
			$sql .= ' and atd.fechaDesde >= "'. convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and atd.fechaDesde <= "'. convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= ' order by atd.idActaTareaDiaria desc';
//		die($sql);
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

	public function getDataTableData($postData, $data = null){
		try{
			$sql = 'SELECT
					atd.idActaTareaDiaria,
					atd.fechaDesde,
					DATE_FORMAT(atd.fechaDesde, "%d/%m/%Y") as fechaDesdeES,
					getCodigoATD (atd.idActaTareaDiaria) AS getCodigoATD,
					IF(atd.recepcionFecha, "Recepcionada", "Pendiente")  as estado,
					atd.recepcionFecha,
					getEmpleado (e1.idEmpleado) AS empleadoATD,
					getEmpleado (e2.idEmpleado) AS empleadoRecepcionATD
				FROM
					actaTareasDiaria AS atd
				LEFT JOIN empleados AS e1 ON atd.idEmpleado = e1.idEmpleado
				LEFT JOIN empleados AS e2 ON atd.recepcionIdEmpleado = e2.idEmpleado
				
				where TRUE ';
			if($data['idEmpleado']){
				$sql .= ' and e1.idEmpleado = '. $data['idEmpleado'];
			}

			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				//DATOS
				foreach ($dataSql['data'] as $row) {
					$auxRow['idActaTareaDiaria'] = $row['idActaTareaDiaria'];
					$auxRow['fechaDesde'] = $row['fechaDesde'];
					$auxRow['fechaDesdeES'] = $row['fechaDesdeES'];
					$auxRow['getCodigoATD'] = $row['getCodigoATD'];
					$auxRow['recepcionFecha'] = $row['recepcionFecha'];
					$auxRow['estado'] = $row['recepcionFecha'] ? 'Fecha recepción: '.convertDateDbToEs($row['recepcionFecha']).' \ Recepcionó: '.$row['empleadoRecepcionATD'] : 'Pendiente';
					$auxRow['empleadoATD'] = $row['empleadoATD'];
					$auxRow['empleadoRecepcionATD'] = $row['empleadoRecepcionATD'];
					$auxRow['accion'] = '';
					$auxRow['accion'] .= '
								<a class="text-black" href="../pdfs/ActaTareaDiariaPDF.php?'.codificarGets('id='.$row['idActaTareaDiaria'].'&action=pdf'). '" target="_blank" title="Guía de Ruta en PDF"><span class="fa fa-file-pdf-o fa-lg"></span></a>&nbsp;&nbsp;';
					$auxRow['accion'] .= !$row['recepcionFecha'] ? '<a class="text-black"  href= "../pages/ABMactaTareasDiaria.php?' . codificarGets('id=' . $row['idActaTareaDiaria'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
                                <a class="text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idActaTareaDiaria'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Borrar"><span class="fa fa-trash-o fa-lg"></span></a>' : '';
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
		$objectPropierties[] = array('nombre' => 'idActaTareaDiaria',    'dbFieldName' => 'idActaTareaDiaria',      'visibleDT' => false,  'visibleDTexport' => true,   'className' => false, 'aDataSort' => false,                'bSortable' => true, 'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fechaDesdeEN',         'dbFieldName' => 'fechaDesde',             'visibleDT' => false,  'visibleDTexport' => false,  'className' => false, 'aDataSort' => false,                'bSortable' => true, 'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fecha',                'dbFieldName' => 'fechaDesdeES',           'visibleDT' => true,   'visibleDTexport' => true,   'className' => false, 'aDataSort' => 'fechaDesdeEN',       'bSortable' => true, 'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'código de acta',       'dbFieldName' => 'getCodigoATD',           'visibleDT' => true,   'visibleDTexport' => true,   'className' => false, 'aDataSort' => 'fechaDesdeEN',       'bSortable' => true, 'searchable' => false);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'estado',               'dbFieldName' => 'estado',                 'visibleDT' => true,   'visibleDTexport' => true,   'className' => false, 'aDataSort' => 'fecha de recepción', 'bSortable' => true, 'searchable' => false);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fecha de recepción',   'dbFieldName' => 'recepcionFecha',         'visibleDT' => false,  'visibleDTexport' => true,   'className' => false, 'aDataSort' => 'fechaDesdeEN',       'bSortable' => true, 'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'empleado ATD',         'dbFieldName' => 'empleadoATD',            'visibleDT' => false,  'visibleDTexport' => true,   'className' => false, 'aDataSort' => false,                'bSortable' => true, 'searchable' => false);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'empleado recepción',   'dbFieldName' => 'empleadoRecepcionATD',   'visibleDT' => false,  'visibleDTexport' => true,   'className' => false, 'aDataSort' => false,                'bSortable' => true, 'searchable' => false);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'opciones',             'dbFieldName' => 'accion',                 'visibleDT' => true,   'visibleDTexport' => false,  'className'=>'center','aDataSort' => false,                'bSortable' => false,'searchable' => false);

		$listadoDatosCargados['columnas'] = $objectPropierties;

		foreach ($listadoDatosCargados['columnas'] as $campo) {
			$campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
			$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
		}
		for ($i = 0; $i < count($listadoDatosCargados['columnas']); $i++) {
			if ($listadoDatosCargados['columnas'][$i]['visibleDT'] == false) {//Se carga un array con los campos que nos seran visibles
				$visibleDT[] = $i;
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

		if (is_array($visibleDT)) {
			$visibleDT = implode(', ', $visibleDT);
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

		$parametros['aDataSorts'] = $aDataSorts;
		$parametros['bVisible'] = '{"bVisible": false, "aTargets":[' . $visibleDT . ']},';
		$parametros['bSortable'] = '{"bSortable": false, "aTargets":[' . $bSortable . ']},';
		$parametros['searchable'] = '{"searchable" : false, "aTargets":[' . $searchable . ']},';
		$parametros['lefts'] = '{"className": "dt-left", "aTargets":[' . $lefts . ']},';
		$parametros['centers'] = '{"className": "dt-center", "aTargets":[' . $centers . ']},';
		$parametros['rights'] = '{"className": "dt-right", "aTargets":[' . $rights . ']},';

		$listadoDatosCargados['exportables'] = '[' . $exportables . ']';
		//COLUMNDEFS
		$aoColumnDefs = '"aoColumnDefs": [';

		(isset($parametros['bVisible'])) ? $aoColumnDefs .= $parametros['bVisible'] : '';
		(isset($parametros['bSortable'])) ? $aoColumnDefs .= $parametros['bSortable'] : '';
		(isset($parametros['searchable'])) ? $aoColumnDefs .= $parametros['searchable'] : '';
		(isset($parametros['lefts'])) ? $aoColumnDefs .= $parametros['lefts'] : '';
		(isset($parametros['centers'])) ? $aoColumnDefs .= $parametros['centers'] : '';
		(isset($parametros['rights'])) ? $aoColumnDefs .= $parametros['rights'] : '';
		(is_array($parametros['aDataSorts'])) ? $aoColumnDefs .= implode(',', $parametros['aDataSorts']) : '';//Importante que los DataSort esten al final
		$aoColumnDefs .= '],';
		//COLUMNDEFS

		//AASORTING
		$aaSorting = '"aaSorting": [[1,"desc"]],';
		//AASORTING

		//COLUMN NAMES
		$columns = '"columns": [';
		foreach ($dbFieldNames as $name) {
			$columns .= '{"data": "' . $name . '"},';
		}
		$columns = rtrim($columns, ',');
		$columns .= '],';
		//COLUMN NAMES

		$listadoDatosCargados['columns'] = $columns;
		$listadoDatosCargados['aaSorting'] = $aaSorting;
		$listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

		return $listadoDatosCargados;// Retorna campos,columns, aoColumnDefs y aaSorting
	}

}
if($_GET['action'] == 'json' && $_GET['type'] == 'getActaTareaDiariaAlerta'){
	$aux = new ActaTareaDiariaVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']){
		$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		$data['fecha'] = $_GET['fecha'];
		//print_r($data); die();
		$aux->{$_GET['type']}($data);
	}
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getActaTareaDiariaParaGantt'){
	$aux = new ActaTareaDiariaVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']){
		$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		$data['start'] = $_POST['start'];
		$data['end'] = $_POST['end'];
		$data['mod'] = $_POST['mod'];
		//print_r($data); die();
		$aux->{$_POST['type']}($data);
	}
}
if($_POST['action'] == 'json' && $_POST['type'] == 'getRegistroDeActaTareaDiariaParaGantt'){
	$aux = new ActaTareaDiariaVO();
	//$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
	$data['start'] = $_POST['start'];
	$data['end'] = $_POST['end'];
	$data['mod'] = $_POST['mod'];
	//print_r($data); die();
	$aux->{$_POST['type']}($data);
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMactaTareasDiaria.php')){
	$aux = new ActaTareaDiariaVO();
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
if($_GET['debug'] == 'ActaTareaDiariaVO' or false){
    echo "DEBUG<br>";
    $kk = new ActaTareaDiariaVO();
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
