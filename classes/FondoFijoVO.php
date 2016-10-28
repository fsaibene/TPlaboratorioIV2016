<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class FondoFijoVO extends Master2 {

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
	}

	/*
	 * este reporte necesita traer el saldo al dia anterior al del inicio del reporte, por eso se dividió en dos metodos.
	 */
	public function getReporte($data){
		try {
			$sql = $this->getReporteSQL($data, $tipo = 'acumulado');
			$sql .= ' union all '. $this->getReporteSQL($data) . ' order by fecha ASC';
			//echo $sql; die();
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

	public function getReporteSQL($data, $tipo = null){
		$fechaMinima = "2016-02-01";
		if(!$data['fechaDesde'] || (strtotime(convertDateEsToDb($data['fechaDesde'])) < strtotime($fechaMinima))){
			$data['fechaDesde'] = $fechaMinima;
		}
		if(!$data['fechaHasta']){
			$data['fechaHasta'] = date('Y-m-d');
		}
		if($tipo == 'acumulado'){
			$sql = 'select idZonaAfectacion, sigla, "saldo apertura" as operacion, "" as detalle1, "" as detalle2, "" as detalle3, "" as detalle4, DATE_ADD("'.convertDateEsToDb($data['fechaDesde']).'", INTERVAL -1 DAY) as fecha, cast(sum(ingreso) + sum(egreso) as DECIMAL(10,2)) as ingreso, null as egreso, null as saldo';
		} else {
			$sql = 'select idZonaAfectacion, sigla, operacion, detalle1, detalle2, detalle3, detalle4, fecha, ingreso, egreso, null as saldo ';
		}
		$sql .= '
				from (
					select idZonaAfectacion, operacion, detalle1, detalle2, detalle3, detalle4, fecha, if(flujo = "ingreso", monto, null) as "ingreso", if(flujo = "egreso", monto, null) as "egreso"
					from (
						/* ingreso de dinero al fondo fijo por medio de cheques */
						select idZonaAfectacion, "ingreso" as flujo, "cheques" as operacion, concat(e.establecimiento, " - ", nroCheque) as detalle1, "" as detalle2, "" as detalle3, "" as detalle4, fechaCobro as fecha, monto
						from fondoFijo_cheques
						inner join chequeras as c using (idChequera)
						inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
						inner join establecimientos as e using (idEstablecimiento)
						where true
						UNION ALL
						/* ingreso de dinero al fondo fijo por medio de extracciones mediante tarjetas de debito */
						select idZonaAfectacion, "ingreso" as flujo, "tarjetas dÃ©bito" as operacion, concat(e.establecimiento, " - ", apellido, ", ", nombres) as detalle1, "" as detalle2, "" as detalle3, "" as detalle4, fechaPago as fecha, monto
						from fondoFijo_tarjetasDebito
						inner join empleadosTarjetaDebito as etd using (idEmpleadoTarjetaDebito)
						inner join empleados as em using (idEmpleado)
						inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
						inner join establecimientos as e using (idEstablecimiento)
						where true
						UNION ALL
						/* egreso de dinero del fondo fijo por medio de recibo entrega efectivo */
						select ree.idZonaAfectacion, "egreso" as flujo, "recibo entrega efectivo" as operacion,
							concat(CONCAT_ws("-", "REE", za2.sigla, concat(SUBSTR(fecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	fecha), 2, "0")),LPAD(nroReciboEntregaEfectivo, 4, "0")), 
							concat(" - ", apellido, ", ", nombres)) as detalle1,
							"" as detalle2, "" as detalle3, "" as detalle4,
							fecha, ROUND(monto * -1 , 2) as monto
						from recibosEntregaEfectivo as ree
						inner JOIN zonasAfectacion AS za2 using (idZonaAfectacion)
						inner join empleados as em using (idEmpleado)
						where true
						UNION ALL
						/* egreso de dinero del fondo fijo por ordenes de pago mec en efectivo */
						select op.idZonaAfectacionSedeEmision as idZonaAfectacion, "egreso" as flujo, "op mec" as operacion,
							CONCAT_ws("-", "OP", "MEC", za2.sigla, concat(SUBSTR(op.fecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	op.fecha), 2, "0")),	LPAD(nroOrdenPagoMEC, 4, "0")) as detalle1,
							detalle2, detalle3, detalle4,
							fechaPago as fecha, ROUND(sum(ope.monto) * -1 , 2) as monto
						from ordenesPagoMEC as op
						inner join ordenesPagoMEC_efectivo ope using (idOrdenPagoMEC)
						inner JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = op.idZonaAfectacionSedeEmision
						inner join (
							select idOrdenPagoMEC, GROUP_CONCAT(CONCAT("ID ", idComprobanteCompraMEC, " - NRO ", if(tcm.idTipoComprobanteMEC = 2, tcm.tipoComprobanteMEC, cc.nroComprobante)) separator " \\ ") detalle2
							from ordenesPagoMEC as op
							INNER JOIN ordenesPagoMEC_comprobantesCompraMEC as opcc using (idOrdenPagoMEC)
							inner join comprobantesCompraMEC as cc using (idComprobanteCompraMEC)
							inner join tiposComprobanteMEC as tcm using (idTipoComprobanteMEC)
							-- inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
							group by idOrdenPagoMEC
						) as detalle2 using (idOrdenPagoMEC)
						inner join (
							select idOrdenPagoMEC, GROUP_CONCAT(CONCAT(if(toc.idTipoOrigenCompra = 1,	toc.tipoOrigenCompra, origenCompra.origenCompra)) separator " \\ ") as detalle3
							from ordenesPagoMEC as op
							INNER JOIN ordenesPagoMEC_comprobantesCompraMEC as opcc using (idOrdenPagoMEC)
							inner join comprobantesCompraMEC as cc using (idComprobanteCompraMEC)
							inner join tiposOrigenCompra as toc using (idTipoOrigenCompra)
							left join (
								select idRendicionViatico,
									concat(CONCAT_ws("-", "RDV", za2.sigla, concat(SUBSTR(rdv.recepcionFecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	rdv.recepcionFecha), 2, "0")),
										LPAD(rdv.idRendicionViatico, 4, "0")), concat(" - ", apellido, ", ", nombres))
									as origenCompra
								from rendicionesViatico AS rdv
								inner JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = rdv.recepcionIdZonaAfectacion
								inner join empleados as em using (idEmpleado)
							) as origenCompra USING (idRendicionViatico)
							group by idOrdenPagoMEC
						) as detalle3 using (idOrdenPagoMEC)
						inner join (
							select idOrdenPagoMEC, GROUP_CONCAT(CONCAT_WS(" \ ", cgn1.cuentaGastoNivel1, cgn2.cuentaGastoNivel2) separator " \\ ") as detalle4
							from ordenesPagoMEC as op
							INNER JOIN ordenesPagoMEC_comprobantesCompraMEC as opcc using (idOrdenPagoMEC)
							inner join comprobantesCompraMEC as cc using (idComprobanteCompraMEC)
							inner join cuentasGastosNivel2 as cgn2 using (idCuentaGastoNivel2)
							inner join cuentasGastosNivel1 as cgn1 using (idCuentaGastoNivel1)
							group by idOrdenPagoMEC
						) as detalle4 using (idOrdenPagoMEC)
						where true ';
			$sql .= ' and fechaPago >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			$sql .= ' and fechaPago <= "'.convertDateEsToDb($data['fechaHasta']).'"';
			$sql .= ' and idZonaAfectacion = '.$data['idZonaAfectacion'];
			$sql .= ' group by op.idOrdenPagoMEC, fechaPago
						UNION ALL
						/* egreso de dinero del fondo fijo por ordenes de pago iva en efectivo */
						select op.idZonaAfectacionSedeEmision as idZonaAfectacion, "egreso" as flujo, "op iva" as operacion,
							concat(CONCAT_ws("-", "OP", "IVA", za2.sigla,	concat(SUBSTR(op.fecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	op.fecha), 2, "0")),
								LPAD(nroOrdenPago, 4, "0")), if(op.referencia is not null, concat(" - ", op.referencia), "")) as detalle1,
							detalle2, detalle3, detalle4,
							fechaPago as fecha, ROUND(sum(ope.monto) * -1 , 2) as monto
						from ordenesPagoIVA as op
						inner join ordenesPagoIVA_efectivo ope using (idOrdenPago)
						inner JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = op.idZonaAfectacionSedeEmision
						inner join (
							select idOrdenPago, GROUP_CONCAT(distinct CONCAT("ID ", idComprobanteCompraIVA, " - NRO ", nroFactura, " - ", tcf.tipoComprobanteFiscal) separator " \\ ") detalle2
							from ordenesPagoIVA as op
							INNER JOIN ordenesPagoIVA_comprobantesCompraIVA as opcc using (idOrdenPago)
							inner join comprobantesCompraIVA as cc using (idComprobanteCompraIVA)
							inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
							group by idOrdenPago
						) as detalle2 using (idOrdenPago)
						inner join (
							select idOrdenPago, GROUP_CONCAT(distinct if(toc.idTipoOrigenCompra = 1,	toc.tipoOrigenCompra, origenCompra.origenCompra) separator " \\ ") as detalle3
							from ordenesPagoIVA as op
							INNER JOIN ordenesPagoIVA_comprobantesCompraIVA as opcc using (idOrdenPago)
							inner join comprobantesCompraIVA as cc using (idComprobanteCompraIVA)
							inner join tiposOrigenCompra as toc using (idTipoOrigenCompra)
							left join (
								select idRendicionViatico,
									concat(concat(CONCAT_ws("-", "RDV", za2.sigla, concat(SUBSTR(rdv.recepcionFecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	rdv.recepcionFecha), 2, "0")),
										LPAD(rdv.idRendicionViatico, 4, "0"))), concat(" - ", apellido, ", ", nombres))
									as origenCompra
								from rendicionesViatico AS rdv
								inner JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = rdv.recepcionIdZonaAfectacion
								inner join empleados as em using (idEmpleado)
							) as origenCompra USING (idRendicionViatico)
							group by idOrdenPago
						) as detalle3 using (idOrdenPago)
						inner join (
							select idOrdenPago, GROUP_CONCAT(distinct CONCAT_WS(" \ ",cgn1.cuentaGastoNivel1,cgn2.cuentaGastoNivel2) separator " \\ ") as detalle4
							from ordenesPagoIVA as op
							INNER JOIN ordenesPagoIVA_comprobantesCompraIVA as opcc using (idOrdenPago)
							inner join comprobantesCompraIVA as cc using (idComprobanteCompraIVA)
							inner join cuentasGastosNivel2 as cgn2 using (idCuentaGastoNivel2)
							inner join cuentasGastosNivel1 as cgn1 using (idCuentaGastoNivel1)
							group by idOrdenPago
						) as detalle4 using (idOrdenPago)
						where true ';
			$sql .= ' and fechaPago >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			$sql .= ' and fechaPago <= "'.convertDateEsToDb($data['fechaHasta']).'"';
			$sql .= ' and idZonaAfectacion = '.$data['idZonaAfectacion'];
			$sql .= ' group by op.idOrdenPago, fechaPago
					) as ff
					where true

					union all

					select idZonaAfectacion, "rendicion de viáticos" as operacion, origenCompra.origenCompra as detalle1, "" as detalle2, "" as detalle3, "" as detalle4, fecha, if(sum(monto) >= 0, sum(monto), null) as "ingreso", if(sum(monto) < 0, sum(monto), null) as "egreso"
					from (
						/* egreso de dinero por medio de compras en efectivo */
						select recepcionIdZonaAfectacion as idZonaAfectacion, "egreso" as flujo, "compras efectivo" as operacion, recepcionFecha as fecha, ROUND((sum(monto) * -1) , 2) as monto, idRendicionViatico
						from rendicionesViatico_comprobantes
						inner join rendicionesViatico using (idRendicionViatico)
						where idTipoFormaDePago = 1 and recepcionFecha is not null
						group by idRendicionViatico
						union ALL
						/* ingreso de dinero por medio de extracciones mediante tarjetas de debito */
						select recepcionIdZonaAfectacion as idZonaAfectacion, "ingreso" as flujo, "extracciones tarjetas dÃ©bito" as operacion, recepcionFecha as fecha, sum(monto) as monto, idRendicionViatico
						from rendicionesViatico_movimientosTarjetaDebito
						inner join rendicionesViatico using (idRendicionViatico)
						where idTipoOperacionTarjetaDebito = 1 and recepcionFecha is not null
						group by idRendicionViatico
						union all
						/* ingreso de dinero medio de entrega en efectivo con recibo */
						select idZonaAfectacion, "ingreso" as flujo, "recibo entrega efectivo" as operacion, recepcionFecha as fecha, monto, idRendicionViatico
						from recibosEntregaEfectivo
						inner join rendicionesViatico using (idReciboEntregaEfectivo)
						where recepcionFecha is not null
					) as rdv
					inner join (
						select idRendicionViatico,
								concat(CONCAT_ws("-", "RDV", za2.sigla, concat(SUBSTR(rdv.recepcionFecha FROM	3 FOR 2), LPAD(EXTRACT(MONTH FROM	rdv.recepcionFecha), 2, "0")),
									LPAD(rdv.idRendicionViatico, 4, "0")), concat(" - ", apellido, ", ", nombres))
								as origenCompra
						from rendicionesViatico AS rdv
						left JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = rdv.recepcionIdZonaAfectacion
						inner join empleados as em using (idEmpleado)
					) as origenCompra USING (idRendicionViatico)
					where true
					group by idRendicionViatico
				) as fondoFijo
				inner join zonasAfectacion using (idZonaAfectacion)
				where true ';
		if($tipo == 'acumulado'){
			$sql .= ' and fecha <= DATE_ADD("'.convertDateEsToDb($data['fechaDesde']).'", INTERVAL -1 DAY) ';
		} else {
			$sql .= ' and fecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
			$sql .= ' and fecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= ' and idZonaAfectacion = '.$data['idZonaAfectacion'];
		return $sql;
	}

	/*
	 * dejo comentado este getReporte que fue una primera version por las dudas
	 */
	/*
	public function getReporte($data){
		//print_r($data); //die();
		$sql = 'select zonaAfectacion, operacion, detalle, fecha, monto
				from (
					-- ingreso de dinero al fondo fijo por medio de cheques
					select idZonaAfectacion, "ingreso" as operacion, "cheques" as detalle, fechaCobro as fecha, monto
					from fondoFijo_cheques
					where true ';
		if($data['fechaDesde']){
			$sql .= ' and fechaCobro >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fechaCobro <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= '   UNION ALL
					-- ingreso de dinero al fondo fijo por medio de extracciones mediante tarjetas de debito
					select idZonaAfectacion, "ingreso" as operacion, "tarjetas debito" as detalle, fechaPago as fecha, monto
					from fondoFijo_tarjetasDebito
					where true ';
		if($data['fechaDesde']){
			$sql .= ' and fechaPago >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fechaPago <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= '   UNION ALL
					-- egreso de dinero del fondo fijo por medio de recibo entrega efectivo
					select idZonaAfectacion, "egreso" as operacion, "recibo entrega efectivo" as detalle, fecha, ROUND(monto * -1 , 2) as monto
					from recibosEntregaEfectivo
					where true ';
		if($data['fechaDesde']){
			$sql .= ' and fecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= '   UNION ALL
					-- egreso de dinero del fondo fijo por ordenes de pago mec en efectivo
					select idZonaAfectacionSedeEmision as idZonaAfectacion, "egreso" as operacion, "op mec" as detalle, fechaPago as fecha, ROUND(monto * -1 , 2) as monto
					from ordenesPagoMEC_efectivo
					inner join ordenesPagoMEC using (idOrdenPagoMEC)
					where true ';
		if($data['fechaDesde']){
			$sql .= ' and fechaPago >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fechaPago <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= '   UNION ALL
					-- egreso de dinero del fondo fijo por ordenes de pago iva en efectivo
					select idZonaAfectacionSedeEmision as idZonaAfectacion, "egreso" as operacion, "op iva" as detalle, fechaPago as fecha, ROUND(monto * -1 , 2) as monto
					from ordenesPagoIVA_efectivo
					inner join ordenesPagoIVA using (idOrdenPago)
					where true ';
		if($data['fechaDesde']){
			$sql .= ' and fechaPago >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fechaPago <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}

		$sql .= ' ) as ff
				inner join zonasAfectacion using (idZonaAfectacion)
				where true ';

		if($data['idZonaAfectacion']){
			$sql .= ' and idZonaAfectacion = '.$data['idZonaAfectacion'];
		}
		if($data['operacion']){
			$sql .= ' and operacion = "'.$data['operacion'].'"';
		}

		// ahora le concateno la parte de rendicion de viaticos... es un bardo pero bue...
		$sql .= ' union all
				select zonaAfectacion, case when sum(monto) >= 0 then "ingreso" else "egreso" end as operacion, "rendicion de viáticos" as detalle, fecha, sum(monto) as monto
				from (
					-- egreso de dinero por medio de compras en efectivo
					select recepcionIdZonaAfectacion as idZonaAfectacion, "egreso" as operacion, "compras efectivo" as detalle, recepcionFecha as fecha, ROUND((sum(monto) * -1) , 2) as monto
					from rendicionesViatico_comprobantes
					inner join rendicionesViatico using (idRendicionViatico)
					where idTipoFormaDePago = 1 and recepcionFecha is not null ';
		if($data['fechaDesde']){
			$sql .= ' and recepcionFecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and recepcionFecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= '   group by recepcionIdZonaAfectacion, operacion, recepcionFecha

					union ALL
				    -- ingreso de dinero por medio de extracciones mediante tarjetas de debito
					select recepcionIdZonaAfectacion as idZonaAfectacion, "ingreso" as operacion, "extracciones tarjetas debito" as detalle, recepcionFecha as fecha, sum(monto) as monto
					from rendicionesViatico_movimientosTarjetaDebito
					inner join rendicionesViatico using (idRendicionViatico)
					where idTipoOperacionTarjetaDebito = 1 and recepcionFecha is not null';
		if($data['fechaDesde']){
			$sql .= ' and recepcionFecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and recepcionFecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= '   group by recepcionIdZonaAfectacion, operacion, recepcionFecha

					union all
					-- ingreso de dinero medio de entrega en efectivo con recibo
					select idZonaAfectacion, "ingreso" as operacion, "recibo entrega efectivo" as detalle, recepcionFecha as fecha, monto
					from recibosEntregaEfectivo
					inner join rendicionesViatico using (idReciboEntregaEfectivo)
					where recepcionFecha is not null ';
		if($data['fechaDesde']){
			$sql .= ' and recepcionFecha >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and recepcionFecha <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		$sql .= ' ) as rdv
				inner join zonasAfectacion using (idZonaAfectacion)';
		$sql .= ' where true ';
		if($data['idZonaAfectacion']){
			$sql .= ' and idZonaAfectacion = '.$data['idZonaAfectacion'];
		}
		if($data['operacion']){
			$sql .= ' and operacion = "'.$data['operacion'].'"';
		}
		$sql .= ' group by zonaAfectacion, fecha';

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
	}*/
}

// debug zone
if($_GET['debug'] == 'ProyectoComisionVO' or false){
	echo "DEBUG<br>";
	$kk = new ProyectoComisionVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoComision = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
