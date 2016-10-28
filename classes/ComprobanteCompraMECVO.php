<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ComprobanteCompraMECVO extends Master2 {
	public $idComprobanteCompraMEC = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $fechaCarga = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de ingreso al sistema",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $fechaCompra = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha real de compra",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $idZonaAfectacionSedeEmision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Sede de carga",
		"referencia" => "",
	];
	public $monto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto de la compra",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idCuentaGastoNivel2 = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Cuenta de gastos",
		"referencia" => "",
	];
	public $idTipoComprobanteMEC = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "tipo de comprobante",
		"referencia" => "",
	];
	public $idTipoAsociacionOrdenCompra = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Orden de compra",
		"referencia" => "",
	];
	public $idOrdenCompra = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "código de orden de compra",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $nroComprobante = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "nro. de comprobante",
	];
	public $idTipoOrigenCompra = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Origen de la compra",
		"referencia" => "",
	];
	public $idRendicionViatico = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "código de rendición",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $idTipoFormaDePago = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Forma de pago",
		"referencia" => "",
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "compras/comprobantesCompra/MEC/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('comprobantesCompraMEC');
		$this->setFieldIdName('idComprobanteCompraMEC');
		$this->idCuentaGastoNivel2['referencia'] = new CuentaGastoNivel2VO();
		$this->idTipoOrigenCompra['referencia'] = new TipoOrigenCompraVO();
		$this->idZonaAfectacionSedeEmision['referencia'] = new ZonaAfectacionVO();
		$this->idTipoComprobanteMEC['referencia'] = new TipoComprobanteMECVO();
		$this->idTipoAsociacionOrdenCompra['referencia'] = new TipoAsociacionOrdenCompraVO();
		$this->idRendicionViatico['referencia'] = new RendicionViaticoVO();
		$this->idTipoFormaDePago['referencia'] = new TipoFormaDePagoVO();
		$this->idOrdenCompra['referencia'] = new OrdenCompraVO();
		$this->fechaCarga['valor'] = date('d/m/Y');
		$this->fechaCompra['valor'] = date('d/m/Y');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if($this->idTipoAsociacionOrdenCompra['valor'] == '1'){ // POSEE OC
			$this->idOrdenCompra['obligatorio'] = TRUE;
			$data = null;
			$data['nombreCampoWhere'] = 'nroOrdenCompra';
			$data['valorCampoWhere'] = substr($this->idOrdenCompra['valor'], -4) + 0;
			$this->idOrdenCompra['referencia']->getRowById($data);
			$this->idOrdenCompra['valor'] = $this->idOrdenCompra['referencia']->idOrdenCompra['valor'];
		} else {
			$this->idOrdenCompra['obligatorio'] = FALSE;
			$this->idOrdenCompra['valor'] = NULL;
		}
		if($this->idTipoOrigenCompra['valor'] == '2'){ // RENDICION DE VIATICOS
			$this->idRendicionViatico['obligatorio'] = TRUE;
			$this->idRendicionViatico['valor'] = substr($this->idRendicionViatico['valor'], -4) + 0;
			$this->idTipoFormaDePago['obligatorio'] = TRUE;
		} else {
			$this->idRendicionViatico['obligatorio'] = FALSE;
			$this->idRendicionViatico['valor'] = NULL;
			$this->idTipoFormaDePago['obligatorio'] = FALSE;
			$this->idTipoFormaDePago['valor'] = NULL;
		}
		if($this->idTipoComprobanteMEC['valor'] == '1'){ // POSEE NRO DE COMPROBANTE
			$this->nroComprobante['obligatorio'] = TRUE;
		} else {
			$this->nroComprobante['obligatorio'] = FALSE;
			$this->nroComprobante['valor'] = NULL;
		}
		$fechaCompra = explode("-", convertDateEsToDb($this->fechaCompra['valor']));
		$fechaCarga = explode("-", convertDateEsToDb($this->fechaCarga['valor']));
		if($fechaCompra[0] == $fechaCarga[0] && $fechaCompra[1] > $fechaCarga[1]){
			$resultMessage = 'El mes de la fecha REAL no puede ser mayor que el mes de la fecha de INGRESO.';
		}

        return $resultMessage;
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
			$this->conn->commit();
			$this->getRowById(); // tengo que hacer esto para que traiga todos los objetos refrenciados.
			//print_r($this);die();
			/*
			 * se solicitó que en el caso de un comprobante que tiene asosciado una rendicion se genere automaticamente la OP correspondiente
			 */
			if($this->idTipoOrigenCompra['valor'] == 2){ // rendicion de viaticos
				$opMEC = new OrdenPagoMECVO();
				//$opMEC->idEstablecimiento['valor'] = $this->idSucursalEstablecimiento['referencia']->idEstablecimiento['valor'];
				//$opMEC->referencia['valor'] = $this->idRendicionViatico['referencia']->getCodigoRendicionViatico();
				//$opMEC->descuento['valor'] = 0.00;
				$opMEC->idZonaAfectacionSedeEmision['valor'] = $this->idZonaAfectacionSedeEmision['valor'];
				$opMEC->observaciones['valor'] = 'OP MEC generada automáticamente desde la carga de CC MEC - '.$this->idRendicionViatico['referencia']->getCodigoRendicionViatico();

				$opMECccMEC = new OrdenPagoMECComprobanteCompraMECVO();
				$opMECccMEC->idComprobanteCompraMEC['valor'] = $this->idComprobanteCompraMEC['valor'];
				$data = array();
				$data['idComprobanteCompraMEC'] = $this->idComprobanteCompraMEC['valor'];
				$this->getComprobanteCompraMEC($data);
				if($this->result->getStatus() != STATUS_OK) {
					//print_r($etd); die('error uno');
					$this->result = $this->result;
					$this->conn->rollBack();
					return $this;
				}
				$result = $this->result->getData();
				$opMECccMEC->monto['valor'] = $result[0]['total'];
				$opMEC->ordenPagoMECComprobanteCompraMECArray[] = $opMECccMEC;

				if($this->idTipoFormaDePago['valor'] == 1){ // efectivo
					$opMECfp = new OrdenPagoMECEfectivoVO();
					$opMECfp->fechaPago['valor'] = $this->fechaCompra['valor'];
					$opMECfp->monto['valor'] = $opMECccMEC->monto['valor'];
					$opMEC->ordenPagoMECEfectivoArray[] = $opMECfp;
				} else if($this->idTipoFormaDePago['valor'] == 2){ // tarjeta debito
					$etd = new EmpleadoTarjetaDebitoVO();
					$etd->idEmpleado['valor'] = $this->idRendicionViatico['referencia']->idEmpleado['valor'];
					$etd->getEmpleadoTarjetaDebitoPorIdEmpleado();
					if($etd->result->getStatus()  != STATUS_OK) {
						//print_r($etd); die('error uno');
						$this->result = $etd->result;
						$this->conn->rollBack();
						return $this;
					}
					$opMECfp = new OrdenPagoMECTarjetaDebitoVO();
					$opMECfp->idEmpleadoTarjetaDebito['valor'] = $etd->idEmpleadoTarjetaDebito['valor'];
					$opMECfp->fechaPago['valor'] = $this->fechaCompra['valor'];
					$opMECfp->monto['valor'] = $opMECccMEC->monto['valor'];
					$opMEC->ordenPagoMECTarjetaDebitoArray[] = $opMECfp;
				}
				//print_r($opMEC); die();
				$opMEC->insertData();
				if($opMEC->result->getStatus()  != STATUS_OK) {
					//print_r($opMEC); die('error uno');
					$this->result = $opMEC->result;
					$this->conn->rollBack();
					return $this;
				} else {
					$this->result->setData(null);
					$this->result->setMessage($this->result->getMessage().'<br>Fue generada la OP del comprobante automáticamente. Tenga en cuenta que al editar este comprobante la OP no se verá afectada. Para ver o editar la OP generada deberá dirigirse a la página correspondiente.');
				}
			}
			//die('fin');
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

	public function getComprobanteCompraMEC($data, $format = null){
		$sql = "select ccMEC.idComprobanteCompraMEC,
				case when ccMEC.monto is null then 0.00 else ccMEC.monto end total,
				case when abonado.abonado is null then 0.00 else abonado.abonado end as abonado
				from comprobantesCompraMEC as ccMEC
				left join (
					SELECT sum(monto) as abonado, idComprobanteCompraMEC
					from ordenesPagoMEC_comprobantesCompraMEC as opccMEC
					group by idComprobanteCompraMEC
				) as abonado using (idComprobanteCompraMEC)
				where true ";
		if($data['idComprobanteCompraMEC']) {
			$sql .= " and idComprobanteCompraMEC = " . $data['idComprobanteCompraMEC'];
		}
		if($data['term']) {
			$sql .= " and idComprobanteCompraMEC like '%" . $data['term'] . "%'";
		}
		//$sql .= " order by nroFactura asc";
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			//print_r($rs);
			//$items = array();
			if($format == 'json') {
				$cont = count($rs);
				$aux = 0;
				if($rs && count($rs) > 0) {
					$json = '[';
					foreach ($rs as $row) {
						if ($aux > 0 && $aux <= $cont) {
							$json .= ', '; // agregamos esta linea porque cada elemento debe estar separado por una coma
						}
						$aux++;
						$json .= '{ "label" : "' . $row['idComprobanteCompraMEC'] . '", "value" : { "idComprobanteCompraMEC" : "' . $row['idComprobanteCompraMEC'] . '", "idComprobanteCompraMEC" : "' . $row['idComprobanteCompraMEC'] . '", "total" : "' . $row['total'] . '", "abonado" : "' . $row['abonado'] . '" } }';
						//$items[] = $row['Articulo'];
					}
					$json .= ']';
					echo $json;
				}
			} else {
				//die('asd');
				$this->result->setData($rs);
			}
			//echo json_encode($items);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return ;
	}

	public function getReporteMECCompras($data){
		//print_r($data); //die();
		$sql = 'SELECT
				DATE_FORMAT(cc.fechaCarga,"%d/%m/%Y") as fechaCarga
				, DATE_FORMAT(cc.fechaCompra,"%d/%m/%Y") as fechaCompra
				, CONCAT_WS(" - ", m.mesEnNumeros, m.mesEnLetras) AS mesImputacion
				, if(tcm.idTipoComprobanteMEC = 2, tcm.tipoComprobanteMEC, cc.nroComprobante) nroComprobante
				, za.zonaAfectacion as sedeCarga
				, cc.monto
				, abonado.abonado
				, CONCAT_WS(" \ ",cgn1.cuentaGastoNivel1,cgn2.cuentaGastoNivel2) AS cuentaGastos
				, if(taoc.idTipoAsociacionOrdenCompra = 2,	taoc.tipoAsociacionOrdenCompra, getCodigoOrdenCompra(idOrdenCompra)) as ordenCompra
				, getCodigosOrdenPagoMECPorCCMEC(idComprobanteCompraMEC) as detallesOrdenesPagoMec
				, if(toc.idTipoOrigenCompra = 1,	toc.tipoOrigenCompra, origenCompra.origenCompra) as origenCompra
				, formaPago.formaPago
				, cc.observaciones
				from comprobantesCompraMEC as cc
				inner join zonasAfectacion as za on za.idZonaAfectacion = cc.idZonaAfectacionSedeEmision
				inner join cuentasGastosNivel2 as cgn2 using (idCuentaGastoNivel2)
				inner join cuentasGastosNivel1 as cgn1 using (idCuentaGastoNivel1)
				inner join tiposComprobanteMEC as tcm using (idTipoComprobanteMEC)
				inner join tiposOrigenCompra as toc using (idTipoOrigenCompra)
				inner join tiposAsociacionOrdenCompra as taoc using (idTipoAsociacionOrdenCompra)
				left join (
					select idRendicionViatico,
							CONCAT_ws("-", getCodigoRendicionViatico(idRendicionViatico), getEmpleado(rdv.idEmpleado))
							as origenCompra
					from rendicionesViatico AS rdv
					left JOIN zonasAfectacion AS za2 ON za2.idZonaAfectacion = rdv.recepcionIdZonaAfectacion
				) as origenCompra USING (idRendicionViatico)
				inner join meses as m on m.idMes = mesImputacion(fechaCarga, fechaCompra)
				left join (
				SELECT CAST(sum(opccmec.monto) AS decimal(10,2)) as abonado, opccmec.idComprobanteCompraMEC
					from ordenesPagoMEC_comprobantesCompraMEC as opccmec
					group by idComprobanteCompraMEC
				) as abonado using (idComprobanteCompraMEC)
				left join (
					select opcc.idComprobanteCompraMEC, CONCAT_WS(" / ",
						if(opc.idOrdenPagoMEC, "CHEQUE", null),
						if(ope.idOrdenPagoMEC, "EFECTIVO", null),
						if(optd.idOrdenPagoMEC, "T.DÉBITO", null)) as formaPago
					from ordenesPagoMEC_comprobantesCompraMEC opcc
					left join ordenesPagoMEC_cheques as opc using (idOrdenPagoMEC)
					left join ordenesPagoMEC_efectivo as ope using (idOrdenPagoMEC)
					left join ordenesPagoMEC_tarjetasDebito as optd using(idOrdenPagoMEC)
					GROUP BY opcc.idComprobanteCompraMEC
				) as formaPago using (idComprobanteCompraMEC)
				where true';
		if($data['idZonaAfectacionSedeEmision']){
			$sql .= ' and cc.idZonaAfectacionSedeEmision = '.$data['idZonaAfectacionSedeEmision'];
		}
		if($data['idMes']){
			$sql .= ' and mesImputacion(fechaCarga, fechaCompra) = '.$data['idMes'];
		}
		if($data['fechaDesde']){
			$sql .= ' and fechaCompra >= "'.convertDateEsToDb($data['fechaDesde']).'"';
		}
		if($data['fechaHasta']){
			$sql .= ' and fechaCompra <= "'.convertDateEsToDb($data['fechaHasta']).'"';
		}
		if($data['anio']){
			$sql .= ' and extract(YEAR from fechaCompra) = '.$data['anio'];
		}
		$sql .= ' order by m.mesEnNumeros desc';
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

	public function getDataTableSqlData($dataPOST){
		$sqlData = 'SELECT idComprobanteCompraMEC, fechaCarga, fechaCompra, 
						za.zonaAfectacion, tipoOrigenCompra, cc.archivo, monto
					FROM comprobantesCompraMEC as cc
					inner join zonasAfectacion as za on za.idZonaAfectacion = cc.idZonaAfectacionSedeEmision
					inner join tiposOrigenCompra as toc using (idTipoOrigenCompra)
					where idZonaAfectacionSedeEmision = '.$_SESSION['usuarioLogueadoIdZonaAfectacion'];

		$data = getDataTableSqlDataFilter($dataPOST, $sqlData);
		if($data['data']) {
			foreach ($data['data'] as $row) {
				//print_r($row); die();
				$auxRow['fechaCarga'] = convertDateDbToEs($row['fechaCarga']);
				$auxRow['fechaCompra'] = convertDateDbToEs($row['fechaCompra']);
				$auxRow['zonaAfectacion'] = $row['zonaAfectacion'];
				$auxRow['monto'] = '<div align="right">' . $row['monto'] . '</div>';
				$auxRow['idComprobanteCompraMEC'] = $row['idComprobanteCompraMEC'];
				$auxRow['tipoOrigenCompra'] = $row['tipoOrigenCompra'];
				$opciones = '<div align="center">';
				if ($row['archivo']) {
					$opciones .= '<a class="text-black" target="_blank" href="' . getPath() . '/files/' . $this->archivo['ruta'] . $row['archivo'] . '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp;';
				}
				$opciones .= '<a class="text-black" href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idComprobanteCompraMEC'] . '&action=edit') . '" title="Ver"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;';
				$opciones .= '<a class="text-black btn-compose-modal-confirm" href="#" data-href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idComprobanteCompraMEC'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
				$opciones .= '</div>';
				$auxRow['opciones'] = $opciones;
				$auxData[] = $auxRow;
			}
			$data['data'] = $auxData;
			//print_r($data); die();
		}
		echo json_encode(array_map('setHtmlEntityDecode', $data));
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getComprobanteCompraMEC'){
	$aux = new ComprobanteCompraMECVO();
	$data['term'] = $_GET['term'];
	$aux->getComprobanteCompraMEC($data, 'json');
}
if($_POST['action'] == 'dtSQL' && $_POST['page'] == 'ABMcomprobantesCompraMEC.php'){
	$aux = new ComprobanteCompraMECVO();
	//print_r($_POST); die();
	if (!empty($_POST) ) {
		$aux->getDataTableSqlData($_POST);
	}
}

// debug zone
if($_GET['debug'] == 'ComprobanteCompraMECVO' or false){
	echo "DEBUG<br>";
	$kk = new ComprobanteCompraMECVO();
	//print_r($kk->getAllRows());
	$kk->idComprobanteCompraMEC = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
