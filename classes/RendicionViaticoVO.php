<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class RendicionViaticoVO extends Master2 {
	public $idRendicionViatico = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "ID",
	];
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado",
		"referencia" => "",
	];
	public $idTipoRendicionViatico = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de rendición",
		"referencia" => "",
	];
	public $idReciboEntregaEfectivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "recibo entrega de efectivo",
		"referencia" => "",
	];
	public $idProyectoComisionEmpleado = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "proyecto comisión",
		"referencia" => "",
	];
	public $idViajeEmpleado = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "viaje SINEC",
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
	public $recepcionIdZonaAfectacion = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "sede de recepción",
		"referencia" => "",
	];
	public $rendicionesViaticoComprobantesArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'RendicionViaticoComprobanteVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'rvc', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idRendicionViaticoComprobante', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idRendicionViatico'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $rendicionesViaticoMovimientosTarjetaDebitoArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'RendicionViaticoMovimientoTarjetaDebitoVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'rvmtd', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idRendicionViaticoMovimientoTarjetaDebito', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idRendicionViatico'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('rendicionesViatico');
		$this->setFieldIdName('idRendicionViatico');
		$this->idEmpleado['referencia'] = new EmpleadoVO();
		$this->idTipoRendicionViatico['referencia'] = new TipoRendicionViaticoVO();
		$this->idProyectoComisionEmpleado['referencia'] = new ProyectoComisionEmpleadoVO();
		$this->idViajeEmpleado['referencia'] = new ViajeEmpleadoVO();
		$this->idReciboEntregaEfectivo['referencia'] = new ReciboEntregaEfectivoVO();
		$this->recepcionIdEmpleado['referencia'] = new EmpleadoVO();
		$this->recepcionIdZonaAfectacion['referencia'] = new ZonaAfectacionVO();
		$this->getCodigoRendicionViatico();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if($this->idTipoRendicionViatico['valor'] == 1) { // comision
			$this->idProyectoComisionEmpleado['obligatorio'] = TRUE;
			$this->idViajeEmpleado['obligatorio'] = FALSE;
			$this->idViajeEmpleado['valor'] = null;
		} else if($this->idTipoRendicionViatico['valor'] == 3) { // viaje sinec
			$this->idViajeEmpleado['obligatorio'] = TRUE;
			$this->idProyectoComisionEmpleado['obligatorio'] = FALSE;
			$this->idProyectoComisionEmpleado['valor'] = null;
		} else if($this->idTipoRendicionViatico['valor'] == 2) { // otro
			$this->idProyectoComisionEmpleado['obligatorio'] = FALSE;
			$this->idProyectoComisionEmpleado['valor'] = null;
			$this->idViajeEmpleado['obligatorio'] = FALSE;
			$this->idViajeEmpleado['valor'] = null;
		}
		if($this->idEmpleado['valor'] == $this->recepcionIdEmpleado['valor']){
			//$resultMessage = 'Error, el empleado que genera la RDV no puede ser el mismo que la recepciona. Se dará aviso al Administrador.';
		}
		if($this->recepcionIdEmpleado['valor'] || $this->recepcionFecha['valor'] || $this->recepcionIdZonaAfectacion['valor']){
			$this->recepcionIdEmpleado['obligatorio'] = TRUE;
			$this->recepcionFecha['obligatorio'] = TRUE;
			$this->recepcionIdZonaAfectacion['obligatorio'] = TRUE;
		}

		return $resultMessage;
	}

	public function getCodigoRendicionViatico(){
		if($this->recepcionFecha['valor']) {
			$aux = explode('/', convertDateDbToEs($this->recepcionFecha['valor']));
			return 'RDV-' . $this->recepcionIdZonaAfectacion['referencia']->sigla['valor'] .'-'. substr($aux[2], -2) . $aux[1] .'-'. str_pad($this->idRendicionViatico['valor'], 4, '0', STR_PAD_LEFT);
		} else {
			return 'RDV-' . str_pad($this->idRendicionViatico['valor'], 4, '0', STR_PAD_LEFT);
		}
	}

	/*
	 * este método lo uso por ejemplo desde el ReporteRendicionViaticos.php ya que necesito solo updatear la recepcion de la misma y no todas la entidades anexas a ella...
	 */
	public function updateData2(){
		parent::updateData();
	}

	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'select idRendicionViatico,
				getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionVIatico,
				concat(upper(e.apellido), ", ", e.nombres) as empleado,
				getCodigoContrato(p.idContrato) as codigoContrato,
				getCodigoProyectoComision(pc.idProyectoComision) as codigoProyectoComision,
				getCodigoViaje(v.idViaje) as codigoViaje,
				rv.recepcionFecha,
				concat(upper(e2.apellido), ", ", e2.nombres) as recepcionEmpleado,
				za.zonaAfectacion as recepcionZonaAfectacion,
				idTipoRendicionViatico, tipoRendicionViatico
				from rendicionesViatico as rv
				inner join tiposRendicionViatico as trv using (idTipoRendicionViatico)
				inner join empleados as e using (idEmpleado)
				left join empleados as e2 on e2.idEmpleado = rv.recepcionIdEmpleado
				left join zonasAfectacion as za on za.idZonaAfectacion = rv.recepcionIdZonaAfectacion
				left join proyectosComisiones_empleados as pce using (idProyectoComisionEmpleado)
				left join proyectosComisiones as pc using (idProyectoComision)
				left join viajes_empleados as ve using (idViajeEmpleado)
				left join viajes as v using (idViaje)
				left join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				left join contratosGerencias as cg using (idContratoGerencia)
				left join contratos as p using (idContrato)';
		$sql .= ' where true ';
		if($data['idTipoRendicionViatico']){
			$sql .= ' and rv.idTipoRendicionViatico = '.$data['idTipoRendicionViatico'];
		}
		if($data['idEmpleado']){
			$sql .= ' and rv.idEmpleado = '.$data['idEmpleado'];
		}
		if($data['estadoRecepcion'] == 'recepcionadaSI'){
			$sql .= ' and rv.recepcionFecha is not null';
		}
		if($data['estadoRecepcion'] == 'recepcionadaNO'){
			$sql .= ' and rv.recepcionFecha is null';
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

	/*
	 * permite identificar cuantos comprobantes de los informados en una rendicion fueron efectivamente ingresados al sigi.
	 */
	public function getReporte2($data){
		//print_r($data); //die();
		$sql = 'select idRendicionViatico, sum(cantidad) as cantidadComprobantesInformados, case when cantidadComprobantesCargados is null then 0 else cantidadComprobantesCargados end as cantidadComprobantesCargados,
				getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionVIatico,
				concat(upper(e.apellido), ", ", e.nombres) as empleado,
				getCodigoContrato(p.idContrato) as codigoContrato,
				getCodigoProyectoComision(pc.idProyectoComision) as codigoProyectoComision,
				getCodigoViaje(v.idViaje) as codigoViaje,
				rv.recepcionFecha,
				concat(upper(e2.apellido), ", ", e2.nombres) as recepcionEmpleado,
				za.zonaAfectacion as recepcionZonaAfectacion,
				idTipoRendicionViatico, tipoRendicionViatico
				from rendicionesViatico_comprobantes as rvc
				inner join rendicionesViatico as rv using (idRendicionViatico)
				inner join tiposRendicionViatico as trv using (idTipoRendicionViatico)
				inner join empleados as e using (idEmpleado)
				left join empleados as e2 on e2.idEmpleado = rv.recepcionIdEmpleado
				left join zonasAfectacion as za on za.idZonaAfectacion = rv.recepcionIdZonaAfectacion
				left join proyectosComisiones_empleados as pce using (idProyectoComisionEmpleado)
				left join proyectosComisiones as pc using (idProyectoComision)
				left join viajes_empleados as ve using (idViajeEmpleado)
				left join viajes as v using (idViaje)
				left join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				left join contratosGerencias as cg using (idContratoGerencia)
				left join contratos as p using (idContrato)
				LEFT JOIN (
					select sum(cantidadComprobantesCargados) as cantidadComprobantesCargados, idRendicionViatico
					from (
						select 1 as cantidadComprobantesCargados, idRendicionViatico
						from comprobantesCompraIVA
						UNION all
						select 1 as cantidadComprobantesCargados, idRendicionViatico
						from comprobantesCompraMEC
					) as cantidadComprobantesCargados
					group by idRendicionViatico
				) as cantidadComprobantesCargados using (idRendicionViatico)
				';
		$sql .= ' where true ';
		$sql .= ' and recepcionFecha is not null ';
		if($data['recepcionIdZonaAfectacion']){
			$sql .= ' and rv.recepcionIdZonaAfectacion = '.$data['recepcionIdZonaAfectacion'];
		}
		if($data['idTipoRendicionViatico']){
			$sql .= ' and rv.idTipoRendicionViatico = '.$data['idTipoRendicionViatico'];
		}
		if($data['idEmpleado']){
			$sql .= ' and rv.idEmpleado = '.$data['idEmpleado'];
		}
		$sql .= ' group by idRendicionViatico';
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
	 * permite identificar para comisiones y viajes internos qué empleados poseen rendiciones recepcioandas
	 */
	public function getReporte4($data){
		//fc_print($data, true);
		if($data['tipoRendicionViatico'] == 'vdc'){
			$sql = 'select a.idProyectoComision, aa.idEmpleado, getEmpleado(aa.idEmpleado) as empleado, b.idRendicionViatico, a.fechaInicio, a.fechaFin, getCodigoProyectoComision(a.idProyectoComision) as codigo
						, a2.idContratoGerenciaProyecto, a3.idContratoGerencia, a4.idContrato
						, CONCAT_WS(" - ",
					        a3.gerencia, 
					        getCodigoContratoGerenciaProyecto(a2.idContratoGerenciaProyecto), 
					        DATE_FORMAT(a.fechaInicio, "%d/%m/%Y"), 
					        DATE_FORMAT(a.fechaFin, "%d/%m/%Y")
				        ) as detalle
						, concat_ws(" \ ", 
							concat("Fecha recepción: ", DATE_FORMAT(b.recepcionFecha, "%d/%m/%Y")), 
							concat("Sede: ", z.zonaAfectacion), 
							concat("Recepconó: ", getEmpleado(e.idEmpleado))
						) as detalleRDV,
						getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionViatico
					from proyectosComisiones as a
					inner join proyectosComisiones_empleados as aa using (idProyectoComision)
					inner join contratosGerenciasProyectos as a2 using (idContratoGerenciaProyecto)
					inner join contratosGerencias as a3 using (idContratoGerencia)
					inner join contratos as a4 using (idContrato)
					left join rendicionesViatico as b using (idProyectoComisionEmpleado)
					LEFT JOIN empleados as e on b.recepcionIdEmpleado = e.idEmpleado
					LEFT JOIN zonasAfectacion as z on b.recepcionIdZonaAfectacion = z.idZonaAfectacion
					';
			$sql .= ' where true ';
			if($data['idEmpleado']){
				$sql .= ' and aa.idEmpleado = '.$data['idEmpleado'];
			}
			if($data['fechaDesde']){
				$sql .= ' and a.fechaInicio >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			}
			if($data['fechaHasta']){
				$sql .= ' and a.fechaFin <= "'.convertDateEsToDb($data['fechaHasta']).'"';
			}
			if($data['idProyectoComision'] && $data['idProyectoComision'] != '__jc__'){
				$sql .= ' and a.idProyectoComision = '.$data['idProyectoComision'];
			}
			if($data['idContratoGerenciaProyecto'] && $data['idContratoGerenciaProyecto'] != '__jc__'){
				$sql .= ' and a2.idContratoGerenciaProyecto = '.$data['idContratoGerenciaProyecto'];
			}
			if($data['idContratoGerencia'] && $data['idContratoGerencia'] != '__jc__'){
				$sql .= ' and a3.idContratoGerencia = '.$data['idContratoGerencia'];
			}
			if($data['idContrato'] && $data['idContrato'] != '__jc__'){
				$sql .= ' and a4.idContrato = '.$data['idContrato'];
			}
			$sql .= ' order by idProyectoComision desc';
		} else if($data['tipoRendicionViatico'] == 'vpi'){
			$sql = 'select a.idViaje, aa.idEmpleado, getEmpleado(aa.idEmpleado) as empleado, b.idRendicionViatico, a.fechaInicio, a.fechaFin, getCodigoViaje(a.idViaje) as codigo
						, CONCAT_WS(" - ", a3.provincia, a2.destino, DATE_FORMAT(a.fechaInicio, "%d/%m/%Y"), DATE_FORMAT(a.fechaFin, "%d/%m/%Y")) as detalle
						, concat_ws(" \ ", 
							concat("Fecha recepción: ", DATE_FORMAT(b.recepcionFecha, "%d/%m/%Y")), 
							concat("Sede: ", z.zonaAfectacion), 
							concat("Recepconó: ", getEmpleado(e.idEmpleado))
						) as detalleRDV,
						getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionViatico
					from viajes as a
					inner join viajes_empleados as aa using (idViaje)
					inner JOIN destinos as a2 using (idDestino)
					inner JOIN provincias as a3 using (idProvincia)
					left join rendicionesViatico as b using (idViajeEmpleado)
					LEFT JOIN empleados as e on b.recepcionIdEmpleado = e.idEmpleado
					LEFT JOIN zonasAfectacion as z on b.recepcionIdZonaAfectacion = z.idZonaAfectacion
				';
			$sql .= ' where true ';
			if($data['idEmpleado']){
				$sql .= ' and aa.idEmpleado = '.$data['idEmpleado'];
			}
			if($data['fechaDesde']){
				$sql .= ' and fechaInicio >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			}
			if($data['fechaHasta']){
				$sql .= ' and fechaFin <= "'.convertDateEsToDb($data['fechaHasta']).'"';
			}
			$sql .= ' order by idViaje desc';
		} else {
			$sql = 'select * 
					from (
						select a.idProyectoComision, aa.idEmpleado, getEmpleado(aa.idEmpleado) as empleado, b.idRendicionViatico, a.fechaInicio, a.fechaFin, getCodigoProyectoComision(a.idProyectoComision) as codigo
							, CONCAT_WS(" - ",
						        a3.gerencia, 
						        getCodigoContratoGerenciaProyecto(a2.idContratoGerenciaProyecto), 
						        DATE_FORMAT(a.fechaInicio, "%d/%m/%Y"), 
						        DATE_FORMAT(a.fechaFin, "%d/%m/%Y")
					        ) as detalle
							, concat_ws(" \ ", 
								concat("Fecha recepción: ", DATE_FORMAT(b.recepcionFecha, "%d/%m/%Y")), 
								concat("Sede: ", z.zonaAfectacion), 
								concat("Recepconó: ", getEmpleado(e.idEmpleado))
							) as detalleRDV,
							getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionViatico
						from proyectosComisiones as a
						inner join proyectosComisiones_empleados as aa using (idProyectoComision)
						inner join contratosGerenciasProyectos as a2 using (idContratoGerenciaProyecto)
						inner join contratosGerencias as a3 using (idContratoGerencia)
						inner join contratos as a4 using (idContrato)
						left join rendicionesViatico as b using (idProyectoComisionEmpleado)
						LEFT JOIN empleados as e on b.recepcionIdEmpleado = e.idEmpleado
						LEFT JOIN zonasAfectacion as z on b.recepcionIdZonaAfectacion = z.idZonaAfectacion
					
						union 
					
						select a.idViaje, aa.idEmpleado, getEmpleado(aa.idEmpleado) as empleado, b.idRendicionViatico, a.fechaInicio, a.fechaFin, getCodigoViaje(a.idViaje) as codigo
							, CONCAT_WS(" - ", a3.provincia, a2.destino, DATE_FORMAT(a.fechaInicio, "%d/%m/%Y"), DATE_FORMAT(a.fechaFin, "%d/%m/%Y")) as detalle
							, concat_ws(" \ ", 
								concat("Fecha recepción: ", DATE_FORMAT(b.recepcionFecha, "%d/%m/%Y")), 
								concat("Sede: ", z.zonaAfectacion), 
								concat("Recepconó: ", getEmpleado(e.idEmpleado))
							) as detalleRDV,
							getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionViatico
						from viajes as a
						inner join viajes_empleados as aa using (idViaje)
						inner JOIN destinos as a2 using (idDestino)
						inner JOIN provincias as a3 using (idProvincia)
						left join rendicionesViatico as b using (idViajeEmpleado)
						LEFT JOIN empleados as e on b.recepcionIdEmpleado = e.idEmpleado
						LEFT JOIN zonasAfectacion as z on b.recepcionIdZonaAfectacion = z.idZonaAfectacion
					) as rdv
				';
			$sql .= ' where true ';
			if($data['idEmpleado']){
				$sql .= ' and idEmpleado = '.$data['idEmpleado'];
			}
			if($data['fechaDesde']){
				$sql .= ' and fechaInicio >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			}
			if($data['fechaHasta']){
				$sql .= ' and fechaFin <= "'.convertDateEsToDb($data['fechaHasta']).'"';
			}
			$sql .= ' order by fechaInicio desc';
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

	/*
	 * da un detalle de los gastos de una rendicion sumarizando los montos por rubros
	 */
	public function getReporte3($data){
		//print_r($data); //die();
		$sql = 'select idRendicionViatico, 
				getCodigoRendicionViatico(idRendicionViatico) as codigoRendicionVIatico,
				getEmpleado(rv.idEmpleado) as empleado,
				getCodigoContrato(p.idContrato) as codigoContrato,
				getCodigoContratoGerenciaProyecto(cgp.idContratoGerenciaProyecto) as codigoContratoGerenciaProyecto,
				getCodigoProyectoComision(pc.idProyectoComision) as codigoProyectoComision,
				getCodigoViaje(v.idViaje) as codigoViaje,
				rv.recepcionFecha,
				getEmpleado(recepcionIdEmpleado) as recepcionEmpleado,
				za.zonaAfectacion as recepcionZonaAfectacion,
				tipoRendicionViatico, idTipoRendicionViatico, 
				pc.fechaInicio as pcFechaInicio, pc.fechaFin as pcFechaFin,
				v.fechaInicio as vFechaInicio, v.fechaFin as vFechaFin
				, montos.*
				, CONCAT_WS("\\\", prov.provincia, cgl.localizacion) as localizacionDetalle
				, CONCAT_WS("\\\", prov2.provincia, d.destino) as destinoDetalle
				from rendicionesViatico as rv
				inner join tiposRendicionViatico as trv using (idTipoRendicionViatico)
				left join zonasAfectacion as za on za.idZonaAfectacion = rv.recepcionIdZonaAfectacion
				left join proyectosComisiones_empleados as pce using (idProyectoComisionEmpleado)
				left join proyectosComisiones as pc using (idProyectoComision)
				left join contratosGerenciasLocalizaciones as cgl using (idContratoGerenciaLocalizacion)
				left join provincias as prov on prov.idProvincia = cgl.idProvincia
				left join viajes_empleados as ve using (idViajeEmpleado)
				left join viajes as v using (idViaje)
				left join destinos as d using (idDestino)
				left join provincias as prov2 on prov2.idProvincia = d.idProvincia
				left join contratosGerenciasProyectos as cgp using (idContratoGerenciaProyecto)
				left join contratosGerencias as cg on cg.idContratoGerencia = cgp.idContratoGerencia
				left join contratos as p using (idContrato)
				left join (
					select idRendicionViatico, 
						sum(if(idTipoRubroGasto = 1, monto, 0)) as montoRubro1,
						sum(if(idTipoRubroGasto = 2, monto, 0)) as montoRubro2,
						sum(if(idTipoRubroGasto = 3, monto, 0)) as montoRubro3,
						sum(if(idTipoRubroGasto = 4, monto, 0)) as montoRubro4,
						sum(if(idTipoRubroGasto = 5, monto, 0)) as montoRubro5,
						sum(monto) as total
					from rendicionesViatico_comprobantes as rvc
					inner join tiposRubroGasto as trg using (idTipoRubroGasto)
					group by idRendicionViatico
				) as montos using (idRendicionViatico)
				';
		$sql .= ' where true ';
		if($data['recepcionIdZonaAfectacion']){
			$sql .= ' and rv.recepcionIdZonaAfectacion = '.$data['recepcionIdZonaAfectacion'];
		}
		if($data['idTipoRendicionViatico']){
			$sql .= ' and rv.idTipoRendicionViatico = '.$data['idTipoRendicionViatico'];
		}
		if($data['idEmpleado']){
			$sql .= ' and rv.idEmpleado = '.$data['idEmpleado'];
		}
		if($data['estadoRecepcion'] == 'recepcionadaSI'){
			$sql .= ' and rv.recepcionFecha is not null';
		}
		if($data['estadoRecepcion'] == 'recepcionadaNO'){
			$sql .= ' and rv.recepcionFecha is null';
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

	/*
	 * devuelve un array con los tipos de comprobante y monto sumarizado segun un idRendicionViatico
	 */
	public function getResumenPorTipoComprobante(){
		try{
			$sql = 'SELECT idRendicionViatico, idTipoComprobanteRendicionViatico, tipoComprobanteRendicionViatico, sum(monto) as monto
					from rendicionesViatico as rv
					inner join rendicionesViatico_comprobantes as rvc using (idRendicionViatico)
					inner join tiposComprobanteRendicionViatico as tcrv using (idTipoComprobanteRendicionViatico)
					where idRendicionViatico = '.$this->idRendicionViatico['valor'].'
					group by idRendicionViatico, tipoComprobanteRendicionViatico';
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

	/*
	 * devuelve un array con las formas de pago y monto sumarizado segun un idRendicionViatico
	 */
	public function getResumenPorTipoFormaDePago(){
		try{
			$sql = 'SELECT idRendicionViatico, idTipoFormaDePago, tipoFormaDePago, sum(monto) as monto
					from rendicionesViatico as rv
					inner join rendicionesViatico_comprobantes as rvc using (idRendicionViatico)
					inner join tiposFormaDePago as rfp using (idTipoFormaDePago)
					where idRendicionViatico = '.$this->idRendicionViatico['valor'].'
					group by idRendicionViatico, tipoFormaDePago';
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

	function getRendicionViaticoPDF(){
		try {
			$htmlBody = $this->getPDF();
			//print_r($htmlBody); die();
			//echo $result->getData();
			// documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
			// para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
			//$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
			$document = '<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="P">';
			$document .= '<page_header>';
			//$informe .= '<br>';
			//$informe .= 'idprograma: '.$idPrograma;
			//$informe .= '<p align="center">header</p>';
			$document .= '</page_header>';
			$document .= '<page_footer>';
			$document .= '<p align="center">P&aacute;gina [[page_cu]]/[[page_nb]]</p>';
			$document .= '</page_footer>';
			$document .= '<nobreak>';
			//$document .= '<p style="font-size:16"><b>lalala</b></p>';
			$document .= $htmlBody;
			$document .= '</nobreak>';
			$document .= '</page>';
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $document;
	}

	public function getPDF(){
		$this->getRowById();

		$rvcArray = new RendicionViaticoComprobanteVO();
		$data = null;
		$data['nombreCampoWhere'] = $this->getFieldIdName();
		$data['valorCampoWhere'] = $this->idRendicionViatico['valor'];
		$rvcArray->getAllRows($data);

		$this->getResumenPorTipoComprobante();
		if($this->result->getStatus() == STATUS_OK && $this->result->getData()) {
			$resumenPorTipoComprobante = $this->result->getData();
		}
		$this->getResumenPorTipoFormaDePago();
		if($this->result->getStatus() == STATUS_OK && $this->result->getData()) {
			$resumenPorTipoFormaDePago = $this->result->getData();
		}

		$rvmtdArray = new RendicionViaticoMovimientoTarjetaDebitoVO();
		$data = null;
		$data['nombreCampoWhere'] = $this->getFieldIdName();
		$data['valorCampoWhere'] = $this->idRendicionViatico['valor'];
		$rvmtdArray->getAllRows($data);

		$css = '<style>
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
				</style>';

		$html = $css;
		$html .= '	<table cellspacing="0">
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
											<th colspan="2" style="width: 100%">RENDICI&Oacute;N DE VI&Aacute;TICOS</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Nº de rendición:</td>
											<td>'.$this->getCodigoRendicionViatico().'</td>
										</tr>
										<tr>
											<td>Empleado:</td>
											<td>'.$this->idEmpleado['referencia']->getNombreCompleto().'</td>
										</tr>';
		if($this->recepcionFecha['valor']) {
			$html .= ' 					<tr>
											<td>Aprobó:</td>
											<td>'.$this->recepcionIdEmpleado['referencia']->getNombreCompleto().'</td>
										</tr>
										<tr>
											<td>Sede:</td>
											<td>'.$this->recepcionIdZonaAfectacion['referencia']->zonaAfectacion['valor'].'</td>
										</tr>
										<tr>
											<td>Fecha:</td>
											<td>'.convertDateDbToEs($this->recepcionFecha['valor']).'</td>
										</tr>';
		}
		$html .= '	                </tbody>
								</table>
							</td>
						</tr>
					</table>
					<br>
					<table >
						<tr>
							<td colspan="2">Tipo de rendición: '.$this->idTipoRendicionViatico['referencia']->tipoRendicionViatico['valor'].'</td>
						</tr>';
		if($this->idTipoRendicionViatico['valor'] == 1) { // comision
			$html .= ' 	<tr>
							<td style="width: 50%">Contrato: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->idContrato['referencia']->nombreReferencia['valor'].'</td>
							<td style="width: 50%">Código Comisión: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->getCodigoProyectoComision().'</td>
						</tr>
						<tr>
							<td>Fecha de inicio: '.convertDateDbToEs($this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->fechaInicio['valor']).'</td>
							<td>Fecha de finalización: '.convertDateDbToEs($this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->fechaFin['valor']).'</td>
						</tr>
						<tr>
							<td>Gerencia: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->idContratoGerencia['referencia']->gerencia['valor'].'</td>
							<td>Código Proyecto: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->getCodigoContratoGerenciaProyecto().'</td>
						</tr>
						<tr>
							<td colspan="2">Proyecto: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaProyecto['referencia']->nombreReferencia['valor'].'</td>
						</tr>
						<tr>
							<td colspan="2">Actividad: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idServicioActividad['referencia']->servicioActividad['valor'].'\\'.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idServicioActividad['referencia']->idServicioAreaDeAplicacion['referencia']->servicioAreaDeAplicacion['valor'].'</td>
						</tr>
						<tr>
							<td colspan="2">Localización: '.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaLocalizacion['referencia']->idProvincia['referencia']->provincia['valor'].'\\'.$this->idProyectoComisionEmpleado['referencia']->idProyectoComision['referencia']->idContratoGerenciaLocalizacion['referencia']->localizacion['valor'].'</td>
						</tr>';
		}
		if($this->idTipoRendicionViatico['valor'] == 3) { // viaje sinec
			$html .= ' 	<tr>
							<td style="width: 50%">Código Viaje: '.$this->idViajeEmpleado['referencia']->idViaje['referencia']->getCodigoViaje().'</td>
						</tr>
						<tr>
							<td>Fecha de inicio: '.convertDateDbToEs($this->idViajeEmpleado['referencia']->idViaje['referencia']->fechaInicio['valor']).'</td>
							<td>Fecha de finalización: '.convertDateDbToEs($this->idViajeEmpleado['referencia']->idViaje['referencia']->fechaFin['valor']).'</td>
						</tr>
						<tr>
							<td colspan="2">Actividad: '.$this->idViajeEmpleado['referencia']->idViaje['referencia']->idLaborActividad['referencia']->idLaborAreaDeAplicacion['referencia']->laborAreaDeAplicacion['valor'].'\\'.$this->idViajeEmpleado['referencia']->idViaje['referencia']->idLaborActividad['referencia']->laborActividad['valor'].'</td>
						</tr>
						<tr>
							<td colspan="2">Destino: '.$this->idViajeEmpleado['referencia']->idViaje['referencia']->idDestino['referencia']->idProvincia['referencia']->provincia['valor'].'\\'.$this->idViajeEmpleado['referencia']->idViaje['referencia']->idDestino['referencia']->destino['valor'].'</td>
						</tr>';
		}
		$html .= ' </table>';

		$html .= '<br>
					<table class="borderYes" style="margin-top: 5px;">
						<thead>
							<tr>
								<th colspan="5" style="width: 100%">DETALLE COMPROBANTES</th>
							</tr>
							<tr>
								<th>Concepto</th>
								<th>Tipo de comprobante</th>
								<th>Forma de pago</th>
								<th>Cantidad</th>
								<th>Monto total</th>
							</tr>
						</thead>
						<tbody>';
		$total = 0;
		foreach($rvcArray->result->getData() as $rvc){
			$total += $rvc->monto['valor'];
			$html .= '<tr>';
				$html .= '<td>'.$rvc->idTipoRubroGasto['referencia']->tipoRubroGasto['valor'].'</td>';
				$html .= '<td>'.$rvc->idTipoComprobanteRendicionViatico['referencia']->tipoComprobanteRendicionViatico['valor'].'</td>';
				$html .= '<td>'.$rvc->idTipoFormaDePago['referencia']->tipoFormaDePago['valor'].'</td>';
				$html .= '<td align="right">'.$rvc->cantidad['valor'].'</td>';
				$html .= '<td align="right">$'.number_format($rvc->monto['valor'], 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= '<tr>';
			$html .= '<td colspan="4" align="right">TOTAL</td>';
			$html .= '<td align="right">$'.number_format($total, 2, ',', '.').'.-</td>';
		$html .= '</tr>';
		$html .= ' </tbody>
				</table>';

		$html .= '<br>';
		$html .= ' <table class="borderYes" style="margin-top: 5px;">
						<thead>
							<tr>
								<th colspan="4" style="width: 100%">DETALLE MOVIMIENTOS TARJETA DÉBITO</th>
							</tr>
							<tr>
								<th>Fecha</th>
								<th>Operación</th>
								<th>Ticket</th>
								<th>Monto</th>
							</tr>
						</thead>
						<tbody>';
		$total = 0;
		foreach($rvmtdArray->result->getData() as $rvmtd){
			$total += $rvmtd->monto['valor'];
			if($rvmtd->idTipoOperacionTarjetaDebito['referencia']->idTipoOperacionTarjetaDebito['valor'] == 1) { // extraccion
				$totalTipoOperacionTarjetaDebitoExtraccion += $rvmtd->monto['valor'];
			} else if($rvmtd->idTipoOperacionTarjetaDebito['referencia']->idTipoOperacionTarjetaDebito['valor'] == 2) { // compra
				$totalTipoOperacionTarjetaDebitoCompra += $rvmtd->monto['valor'];
			}
			$html .= '<tr>';
				$html .= '<td>'.convertDateDbToEs($rvmtd->fecha['valor']).'</td>';
				$html .= '<td>'.$rvmtd->idTipoOperacionTarjetaDebito['referencia']->tipoOperacionTarjetaDebito['valor'].'</td>';
				$html .= '<td align="right">'.$rvmtd->nroComprobante['valor'].'</td>';
				$html .= '<td align="right">$'.number_format($rvmtd->monto['valor'], 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= '<tr>';
			$html .= '<td colspan="3" align="right">TOTAL</td>';
			$html .= '<td align="right">$'.number_format($total, 2, ',', '.').'.-</td>';
		$html .= '</tr>';
		$html .= ' </tbody>
				</table>
				';

		$html .= '<br>
					<table class="borderYes" style="margin-top: 5px;">
						<thead>
							<tr>
								<th style="width: 100%">OBSERVACIONES</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>'.$this->observaciones['valor'].'&nbsp;</td>
							</tr>
						</tbody>
					</table>
					';

		$html .= '<br>';
		$html .= '<table style="width: 100%;">
					<tr>
						<td style="width: 33%;">
							<table class="borderYes" style="margin-top: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="width: 100%;">RESUMEN COMPROBANTES</th>
									</tr>
									<tr>
										<th>Tipo de comprobante</th>
										<th>Monto</th>
									</tr>
								</thead>
								<tbody>';
		foreach($resumenPorTipoComprobante as $tipoComprobante){
			$html .= '<tr>';
				$html .= '<td>'.$tipoComprobante['tipoComprobanteRendicionViatico'].'</td>';
				$html .= '<td align="right">$'.number_format($tipoComprobante['monto'], 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= ' </tbody>
				</table>';

		$html .= '		</td>
						<td style="width: 33%;">';
		$html .= '			<table class="borderYes" style="margin-top: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="width: 100%;">RESUMEN FORMAS DE PAGO</th>
									</tr>
									<tr>
										<th>Forma de pago</th>
										<th>Monto</th>
									</tr>
								</thead>
								<tbody>';
		foreach($resumenPorTipoFormaDePago as $tipoFormaDePago){
			if($tipoFormaDePago['idTipoFormaDePago'] == 1) { // efectivo
				$totalFormaPagoEfectivo += $tipoFormaDePago['monto'];
			}
			$html .= '<tr>';
				$html .= '<td>'.$tipoFormaDePago['tipoFormaDePago'].'</td>';
				$html .= '<td align="right">$'.number_format($tipoFormaDePago['monto'], 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= ' </tbody>
				</table>';
		$html .= '		</td>
						<td style="width: 33%;">';
		$html .= '			<table class="borderYes" style="margin-top: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="width: 100%;">RESUMEN GENERAL</th>
									</tr>
									<tr>
										<th>Ítem</th>
										<th>Monto</th>
									</tr>
								</thead>
								<tbody>';
		if($this->idReciboEntregaEfectivo['valor']){
			$totalEfectivoEntregado = $this->idReciboEntregaEfectivo['referencia']->monto['valor'];
			$html .= '<tr>';
				$html .= '<td>'.$this->idReciboEntregaEfectivo['referencia']->getCodigoReciboEntregaEfectivo().'</td>';
				$html .= '<td align="right">$'.number_format($totalEfectivoEntregado, 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= '<tr>';
			$html .= '<td>Extracciones TD</td>';
			$html .= '<td align="right">$'.number_format($totalTipoOperacionTarjetaDebitoExtraccion, 2, ',', '.').'.-</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td>Compras TD</td>';
			$html .= '<td align="right">$'.number_format($totalTipoOperacionTarjetaDebitoCompra * -1, 2, ',', '.').'.-</td>';
		$html .= '</tr>';
		$html .= '<tr>';
			$html .= '<td>Compras Efectivo</td>';
			$html .= '<td align="right">$'.number_format($totalFormaPagoEfectivo * -1, 2, ',', '.').'.-</td>';
		$html .= '</tr>';
		$total = $totalTipoOperacionTarjetaDebitoExtraccion + $totalEfectivoEntregado - $totalFormaPagoEfectivo;
		if($total > 0){
			$html .= '<tr>';
				$html .= '<td>Reintegrar a SINEC</td>';
				$html .= '<td align="right">$'.number_format($total, 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		} else {
			$html .= '<tr>';
				$html .= '<td>Reintegrar al empleado</td>';
				$html .= '<td align="right">$'.number_format($total * -1, 2, ',', '.').'.-</td>';
			$html .= '</tr>';
		}
		$html .= ' </tbody>
				</table>';

		$html .= '		</td>
					</tr>
				</table>';
		if($this->recepcionFecha['valor']) {
			$html .= '  <table style="margin-top: 80px; font-size: 7pt;" align="center">
	                        <tr>
	                            <td style="padding-right:30px;">
	                                <table>
										<tr>
											<td style="width: 200px;" align="center"><hr/></td>
										</tr>
										<tr>
											<td align="center">Responsable</td>
										</tr>
										<tr>
											<td>Aclaración:</td>
										</tr>
									</table>
								</td>
	                            <td style="padding-right:30px;">
	                                <table>
										<tr>
											<td style="width: 200px;" align="center"><hr/></td>
										</tr>
										<tr>
											<td align="center">Empleado</td>
										</tr>
										<tr>
											<td>Aclaración:</td>
										</tr>
									</table>
								</td>
	                        </tr>
	                    </table>';
		}
		return html_entity_decode($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	* devuelve datos de una rendicion de viaticos
	*/
	public function getRendicionViaticoPorCodigoRendicionViatico($data){
		$sql = 'SELECT CONCAT_WS(" - ", getEmpleado(e.idEmpleado), 
				case 
					when rv.idTipoRendicionViatico = 1 then getCodigoProyectoComision(idProyectoComision) 
					when rv.idTipoRendicionViatico = 3 then getCodigoViaje(idViaje) 
				end
				, DATE_FORMAT(rv.recepcionFecha,"%d/%m/%Y")) as info
				, rv.idRendicionViatico
				, getCodigoRendicionViatico(rv.idRendicionViatico)
				from rendicionesViatico as rv
				inner join empleados as e using (idEmpleado)
				inner join zonasAfectacion as za on za.idZonaAfectacion = rv.recepcionIdZonaAfectacion
				left JOIN proyectosComisiones_empleados as pce using (idProyectoComisionEmpleado)
				left join proyectosComisiones as pc using (idProyectoComision)
				left join viajes_empleados as ve using (idViajeEmpleado)
				left join viajes as v using (idViaje)
				where getCodigoRendicionViatico(rv.idRendicionViatico) = "'.$data['codigoRendicionViatico'].'"
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getRendicionViaticoPorCodigoRendicionViatico'){
	$aux = new RendicionViaticoVO();
	$data = array();
	$data['codigoRendicionViatico'] = $_GET['codigoRendicionViatico'];
	$aux->getRendicionViaticoPorCodigoRendicionViatico($data);
}

// debug zone
if($_GET['debug'] == 'RendicionViaticoVO' or false){
	//echo "DEBUG<br>";
	include_once("../../tools/dompdf/dompdf_config.inc.php");
	$kk = new RendicionViaticoVO();
	//print_r($kk->getAllRows());
	$kk->idRendicionViatico['valor'] = 1;
	$html = $kk->getRendicionViaticoPDF();
	//echo $html; die();
	$dompdf = new DOMPDF();
	$dompdf->set_paper("A4", "landscape");
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("OC-". $objectVO->nroRendicionViatico['valor'].".pdf", array('Attachment'=>0));

	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}