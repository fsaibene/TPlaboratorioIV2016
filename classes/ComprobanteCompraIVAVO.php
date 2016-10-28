<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ComprobanteCompraIVAVO extends Master2 {
	public $idComprobanteCompraIVA = ["valor" => "",
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
	public $idSucursalEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "sucursal",
		"referencia" => "",
	];
	public $idTipoComprobanteFiscal = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "comprobante fiscal",
		"referencia" => "",
	];
	public $nroFactura = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "Nro. factura",
	];
	public $idCuentaGastoNivel2 = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Cuenta de gastos",
		"referencia" => "",
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
	public $monto = ["valor" => "0.00",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto no gravado",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "file",
		"nombre" => "archivo",
		"ruta" => "compras/comprobantesCompra/IVA/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public $comprobanteCompraIVATipoGravamenCompraArray;
	public $comprobanteCompraIVATipoPercepcionCompraArray;
	public $comprobanteCompraIVATipoImpuestoCompraArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('comprobantesCompraIVA');
		$this->setFieldIdName('idComprobanteCompraIVA');
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
		$this->idCuentaGastoNivel2['referencia'] = new CuentaGastoNivel2VO();
		$this->idTipoComprobanteFiscal['referencia'] = new TipoComprobanteFiscalVO();
		$this->idTipoOrigenCompra['referencia'] = new TipoOrigenCompraVO();
		$this->idTipoAsociacionOrdenCompra['referencia'] = new TipoAsociacionOrdenCompraVO();
		$this->idRendicionViatico['referencia'] = new RendicionViaticoVO();
		$this->idTipoFormaDePago['referencia'] = new TipoFormaDePagoVO();
		$this->idOrdenCompra['referencia'] = new OrdenCompraVO();
		$this->idZonaAfectacionSedeEmision['referencia'] = new ZonaAfectacionVO();
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
			//print_r($this); die('dos');
			if($this->comprobanteCompraIVATipoGravamenCompraArray) {
				//print_r($this->comprobanteCompraIVATipoGravamenCompraArray); die('tres');
				foreach ($this->comprobanteCompraIVATipoGravamenCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			//print_r($this); die('dos');
			if($this->comprobanteCompraIVATipoPercepcionCompraArray) {
				//print_r($this->comprobanteCompraIVATipoPercepcionCompraArray); die('tres');
				foreach ($this->comprobanteCompraIVATipoPercepcionCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			//print_r($this); die('dos');
			if($this->comprobanteCompraIVATipoImpuestoCompraArray) {
				//print_r($this->comprobanteCompraIVATipoImpuestoCompraArray); die('tres');
				foreach ($this->comprobanteCompraIVATipoImpuestoCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			$this->conn->commit();
			//print_r($this->result);die();
			$this->getRowById(); // tengo que hacer esto para que traiga todos los objetos refrenciados.
			//print_r($this);die();
			/*
			 * se solicitó que en el caso de un comprobante que tiene asosciado una rendicion se genere automaticamente la OP correspondiente
			 */
			if($this->idTipoOrigenCompra['valor'] == 2){ // rendicion de viaticos
				$this->conn->beginTransaction();
				$opIVA = new OrdenPagoIVAVO();
				$opIVA->idEstablecimiento['valor'] = $this->idSucursalEstablecimiento['referencia']->idEstablecimiento['valor'];
				$opIVA->referencia['valor'] = $this->idRendicionViatico['referencia']->getCodigoRendicionViatico();
				$opIVA->descuento['valor'] = 0.00;
				$opIVA->idZonaAfectacionSedeEmision['valor'] = $this->idZonaAfectacionSedeEmision['valor'];
				$opIVA->observaciones['valor'] = 'OP IVA generada automáticamente desde la carga de CC IVA';

				$opIVAccIVA = new OrdenPagoIVAComprobanteCompraIVAVO();
				$opIVAccIVA->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
				$data = array();
				$data['idComprobanteCompraIVA'] = $this->idComprobanteCompraIVA['valor'];
				$this->getComprobanteCompraIVA($data);
				if($this->result->getStatus() != STATUS_OK) {
					//print_r($etd); die('error uno');
					$this->result = $this->result;
					$this->conn->rollBack();
					return $this;
				}
				$result = $this->result->getData();
				$opIVAccIVA->monto['valor'] = $result[0]['total'];
				$opIVA->ordenPagoIVAComprobanteCompraIVAArray[] = $opIVAccIVA;

				if($this->idTipoFormaDePago['valor'] == 1){ // efectivo
					$opIVAfp = new OrdenPagoIVAEfectivoVO();
					$opIVAfp->fechaPago['valor'] = $this->fechaCompra['valor'];
					$opIVAfp->monto['valor'] = $opIVAccIVA->monto['valor'];
					$opIVA->ordenPagoIVAEfectivoArray[] = $opIVAfp;
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
					$opIVAfp = new OrdenPagoIVATarjetaDebitoVO();
					$opIVAfp->idEmpleadoTarjetaDebito['valor'] = $etd->idEmpleadoTarjetaDebito['valor'];
					$opIVAfp->fechaPago['valor'] = $this->fechaCompra['valor'];
					$opIVAfp->monto['valor'] = $opIVAccIVA->monto['valor'];
					$opIVA->ordenPagoIVATarjetaDebitoArray[] = $opIVAfp;
				}
				//print_r($opIVA); die();
				$opIVA->insertData();
				if($opIVA->result->getStatus() != STATUS_OK) {
					//print_r($opIVA); die('error uno');
					$this->result = $opIVA->result;
					$this->conn->rollBack();
					return $this;
				} else {
					$this->conn->commit();
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
		//print_r($this->result); die();
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
			$aux = new ComprobanteCompraIVATipoGravamenCompraVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idComprobanteCompraIVA';
			$data['valorCampoWhere'] = $this->idComprobanteCompraIVA['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->comprobanteCompraIVATipoGravamenCompraArray) {
				//print_r($this->comprobanteCompraIVATipoGravamenCompraArray); //die();
				foreach ($this->comprobanteCompraIVATipoGravamenCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new ComprobanteCompraIVATipoPercepcionCompraVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idComprobanteCompraIVA';
			$data['valorCampoWhere'] = $this->idComprobanteCompraIVA['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->comprobanteCompraIVATipoPercepcionCompraArray) {
				//print_r($this->comprobanteCompraIVATipoPercepcionCompraArray); //die();
				foreach ($this->comprobanteCompraIVATipoPercepcionCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new ComprobanteCompraIVATipoImpuestoCompraVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idComprobanteCompraIVA';
			$data['valorCampoWhere'] = $this->idComprobanteCompraIVA['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->comprobanteCompraIVATipoImpuestoCompraArray) {
				//print_r($this->comprobanteCompraIVATipoImpuestoCompraArray); //die();
				foreach ($this->comprobanteCompraIVATipoImpuestoCompraArray as $aux){
					//print_r($aux); die();
					$aux->idComprobanteCompraIVA['valor'] = $this->idComprobanteCompraIVA['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
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

	public function getComprobanteCompraIVA($data, $format = null){
		$sql = "select cciva.idComprobanteCompraIVA, tcf.sigla, cciva.nroFactura,
				IFNULL(impuestos.impuestos * tcf.factorNegativo,0.00) as impuestos,
				IFNULL(percepciones.percepciones * tcf.factorNegativo,0.00) as percepciones,
				IFNULL(gravamenes.iva * tcf.factorNegativo, 0.00) as iva,
				(IFNULL(cciva.monto * tcf.factorNegativo,0.00) + IFNULL(gravamenes.neto * tcf.factorNegativo,0.00)) as neto,
				(IFNULL(gravamenes.iva * tcf.factorNegativo, 0.00) + IFNULL(cciva.monto * tcf.factorNegativo,0.00) + IFNULL(impuestos.impuestos * tcf.factorNegativo,0.00) + IFNULL(percepciones.percepciones * tcf.factorNegativo,0.00) + IFNULL(gravamenes.neto * tcf.factorNegativo,0.00)) as total,
				IFNULL(abonado.abonado * tcf.factorNegativo,0.00) as abonado
				from comprobantesCompraIVA as cciva
				inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
				inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
				left join (
					SELECT sum(monto) as impuestos, idComprobanteCompraIVA
					from comprobantesCompraIVA_tiposImpuestoCompra as impuestos
					group by idComprobanteCompraIVA
				) as impuestos using (idComprobanteCompraIVA)
				left join (
					SELECT sum(monto) as percepciones, idComprobanteCompraIVA
					from comprobantesCompraIVA_tiposPercepcionCompra as percepciones
					group by idComprobanteCompraIVA
				) as percepciones using (idComprobanteCompraIVA)
				left join (
					SELECT sum(montoIva) as iva, sum(montoNeto) as neto, idComprobanteCompraIVA
					from comprobantesCompraIVA_tiposGravamenCompra as gravamenes
					group by idComprobanteCompraIVA
				) as gravamenes using (idComprobanteCompraIVA)
				left join (
					SELECT sum(opcciva.monto) as abonado, opcciva.idComprobanteCompraIVA
					from ordenesPagoIVA_comprobantesCompraIVA as opcciva
					group by idComprobanteCompraIVA
				) as abonado using (idComprobanteCompraIVA)
				where true ";
		if($data['idEstablecimiento']){
			$sql .= " and se.idEstablecimiento = " . $data['idEstablecimiento'];
		}
		if($data['term']) {
			$sql .= " and nroFactura like '%" . $data['term'] . "%'";
		} elseif ($data['idComprobanteCompraIVA']) {
			$sql .= " and cciva.idComprobanteCompraIVA = ". $data['idComprobanteCompraIVA'];
		}
		if($data['abonadas'] == 'false'){ // solo traigo las que no fueron abonadas en su totalidad
			$sql .= " and IFNULL(abonado.abonado,0) != (IFNULL(gravamenes.iva,0) + IFNULL(cciva.monto,0) + IFNULL(impuestos.impuestos,0) + IFNULL(percepciones.percepciones,0) + IFNULL(gravamenes.neto,0))";
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
						$json .= '{ "label" : "' . $row['nroFactura'] . '", "value" : { "nroFactura" : "' . $row['nroFactura'] . '",  "sigla" : "' . $row['sigla'] . '", "idComprobanteCompraIVA" : "' . $row['idComprobanteCompraIVA'] . '", "impuestos" : "' . $row['impuestos'] . '", "percepciones" : "' . $row['percepciones'] . '", "iva" : "' . $row['iva'] . '", "neto" : "' . $row['neto'] . '", "total" : "' . $row['total'] . '", "abonado" : "' . $row['abonado'] . '" } }';
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
		//print_r($this);
		return ;
	}

	public function getReporteIVACompras($data){
		//print_r($data); //die();
		$dataResult = null;
		$sqlTGC = 'select idTipoGravamenCompra, tipoGravamenCompra
					from tiposGravamenCompra as tgc
					where habilitado
					order by orden, tipoGravamenCompra';
		//die($sqlTGC);
		try {
			$ro = $this->conn->prepare($sqlTGC);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$dataResult['tgc'] = $rs;
			$auxTGC = 'SELECT
					tgcc.idComprobanteCompraIVA ';
			foreach($rs as $tgc){
				$auxTGC .= ' , sum(if(tgc.idTipoGravamenCompra = '.$tgc['idTipoGravamenCompra'].', tgcc.montoNeto * tcf.factorNegativo, NULL)) AS "TGC_'.$tgc['idTipoGravamenCompra'].'_NETO"';
			}
			foreach($rs as $tgc){
				$auxTGC .= ' , sum(if(tgc.idTipoGravamenCompra = '.$tgc['idTipoGravamenCompra'].', tgcc.montoIva * tcf.factorNegativo, NULL)) AS "TGC_'.$tgc['idTipoGravamenCompra'].'_IVA"';
			}
			$auxTGC .= ' from tiposGravamenCompra as tgc
					inner join comprobantesCompraIVA_tiposGravamenCompra as tgcc using (idTipoGravamenCompra)
					INNER JOIN comprobantesCompraIVA AS cciva USING (idComprobanteCompraIVA)
					inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
					WHERE true
					GROUP BY tgcc.idComprobanteCompraIVA ';
			//die($auxTGC);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		$sqlTPC = 'select idTipoPercepcionCompra, tipoPercepcionCompra
					from tiposPercepcionCompra as tgc
					where habilitado
					order by orden, tipoPercepcionCompra';
		try {
			$ro = $this->conn->prepare($sqlTPC);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$dataResult['tpc'] = $rs;
			$auxTPC = 'SELECT
					tpcc.idComprobanteCompraIVA ';
			foreach($rs as $tpc){
				$auxTPC .= ' , sum(if(tpc.idTipoPercepcionCompra = '.$tpc['idTipoPercepcionCompra'].', tpcc.monto * tcf.factorNegativo, NULL)) AS "TPC_'.$tpc['idTipoPercepcionCompra'].'"';
			}
			$auxTPC .= ' from tiposPercepcionCompra as tpc
					inner join comprobantesCompraIVA_tiposPercepcionCompra as tpcc using (idTipoPercepcionCompra)
					INNER JOIN comprobantesCompraIVA AS cciva USING (idComprobanteCompraIVA)
					inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
					WHERE true
					GROUP BY tpcc.idComprobanteCompraIVA ';
			//die($auxTPC);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		$sqlTIC = 'select idTipoImpuestoCompra, tipoImpuestoCompra
					from tiposImpuestoCompra as tic
					where habilitado
					order by orden, tipoImpuestoCompra';
		try {
			$ro = $this->conn->prepare($sqlTIC);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			$dataResult['tic'] = $rs;
			$auxTIC = 'SELECT
					ticc.idComprobanteCompraIVA ';
			foreach($rs as $tic){
				$auxTIC .= ' , sum(if(tic.idTipoImpuestoCompra = '.$tic['idTipoImpuestoCompra'].', ticc.monto * tcf.factorNegativo, NULL)) AS "TIC_'.$tic['idTipoImpuestoCompra'].'"';
			}
			$auxTIC .= ' from tiposImpuestoCompra as tic
					inner join comprobantesCompraIVA_tiposImpuestoCompra as ticc using (idTipoImpuestoCompra)
					INNER JOIN comprobantesCompraIVA AS cciva USING (idComprobanteCompraIVA)
					inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
					WHERE true
					GROUP BY ticc.idComprobanteCompraIVA ';
			//die($auxTIC);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		$sql = 'SELECT
				DATE_FORMAT(cc.fechaCarga,"%d/%m/%Y") as fechaCarga
				, DATE_FORMAT(cc.fechaCompra,"%d/%m/%Y") as fechaCompra
				, CONCAT_WS(" - ", m.mesEnNumeros, m.mesEnLetras) AS mesImputacion
				, tcf.tipoComprobanteFiscal
				, cc.nroFactura
				, e.establecimiento
				, e.cuit
				, tsf.tipoSituacionFiscal
				, za.zonaAfectacion as sedeCarga
				, p.provincia as jurisdiccion
				, "" as subtotalNeto
				, tgc.*
				, cc.monto * tcf.factorNegativo as montoNoGravado
				, tpc.*
				, "" as subtotalPercepciones
				, tic.*
				, "" as subtotalImpuestos
				, "" as total
				, abonado.abonado
				, CONCAT_WS(" \ ",cgn1.cuentaGastoNivel1,cgn2.cuentaGastoNivel2) AS cuentaGastos
				, if(taoc.idTipoAsociacionOrdenCompra = 2,	taoc.tipoAsociacionOrdenCompra, getCodigoOrdenCompra(idOrdenCompra)) as ordenCompra
				, getCodigosOrdenPagoIVAPorCCIVA(idComprobanteCompraIVA) as detallesOrdenesPagoIva
				, if(toc.idTipoOrigenCompra = 1,	toc.tipoOrigenCompra, origenCompra.origenCompra) as origenCompra
				, formaPago.formaPago
				, cc.observaciones
				from comprobantesCompraIVA as cc
				inner join tiposComprobanteFiscal as tcf using (idTipoComprobanteFiscal)
				inner join sucursalesEstablecimiento as se using (idSucursalEstablecimiento)
				inner join establecimientos as e using (idEstablecimiento)
				inner join tiposSituacionFiscal as tsf using (idTipoSituacionFiscal)
				inner join zonasAfectacion as za on za.idZonaAfectacion = cc.idZonaAfectacionSedeEmision
				inner join provincias as p using (idProvincia)
				inner join cuentasGastosNivel2 as cgn2 using (idCuentaGastoNivel2)
				inner join cuentasGastosNivel1 as cgn1 using (idCuentaGastoNivel1)
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
					SELECT CAST(sum(opcciva.monto) AS decimal(10,2)) as abonado, opcciva.idComprobanteCompraIVA
					from ordenesPagoIVA_comprobantesCompraIVA as opcciva
					group by idComprobanteCompraIVA
				) as abonado using (idComprobanteCompraIVA)
				left join ('.$auxTGC.') as tgc using (idComprobanteCompraIVA)
				left join ('.$auxTPC.') as tpc using (idComprobanteCompraIVA)
				left join ('.$auxTIC.') as tic using (idComprobanteCompraIVA)
				left join (
					select opcc.idComprobanteCompraIVA, CONCAT_WS(" / ",
						if(opc.idOrdenPago, "CHEQUE", null),
						if(ope.idOrdenPago, "EFECTIVO", null),
						if(optd.idOrdenPago, "T.DÉBITO", null),
						if(optc.idOrdenPago, "T.CRÉDITO", null),
						if(opdc.idOrdenPago, "DÉBITO EN CUENTA", null),
						if(opt.idOrdenPago, "TRANSFERENCIA", null)) as formaPago
					from ordenesPagoIVA_comprobantesCompraIVA opcc
					left join ordenesPagoIVA_cheques as opc using(idOrdenPago)
					left join ordenesPagoIVA_efectivo as ope using(idOrdenPago)
					left join ordenesPagoIVA_tarjetasDebito as optd using(idOrdenPago)
					left join ordenesPagoIVA_tarjetasCredito as optc using(idOrdenPago)
					left join ordenesPagoIVA_transferencias as opt using(idOrdenPago)
					left join ordenesPagoIVA_debitosEnCuenta as opdc using(idOrdenPago)
					GROUP BY opcc.idComprobanteCompraIVA
				) as formaPago using (idComprobanteCompraIVA)
				where true ';
		if($data['idEstablecimiento']){
			$sql .= ' and e.idEstablecimiento = '.$data['idEstablecimiento'];
		}
		if($data['idZonaAfectacionSedeEmision']){
			$sql .= ' and cc.idZonaAfectacionSedeEmision = '.$data['idZonaAfectacionSedeEmision'];
		}
		if($data['idProvincia']){
			$sql .= ' and p.idProvincia = '.$data['idProvincia'];
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
		$sqlData = 'SELECT idComprobanteCompraIVA, fechaCarga, fechaCompra, mesImputacion(fechaCarga, fechaCompra) as mesImputacion,
					za.zonaAfectacion, e.cuit, e.establecimiento, se.sucursalEstablecimiento, tipoOrigenCompra, nroFactura, cc.archivo
				FROM comprobantesCompraIVA as cc
				inner join zonasAfectacion as za on za.idZonaAfectacion = cc.idZonaAfectacionSedeEmision
				inner join sucursalesEstablecimiento as se USING (idSucursalEstablecimiento)
				inner join establecimientos as e using (idEstablecimiento)
				inner join tiposOrigenCompra as toc using (idTipoOrigenCompra)
				where idZonaAfectacionSedeEmision = '.$_SESSION['usuarioLogueadoIdZonaAfectacion'];

		$data = getDataTableSqlDataFilter($dataPOST, $sqlData);
		if($data['data']) {
			foreach ($data['data'] as $row) {
				//print_r($row); die();
				$auxRow['fechaCarga'] = convertDateDbToEs($row['fechaCarga']);
				$auxRow['fechaCompra'] = convertDateDbToEs($row['fechaCompra']);
				$auxRow['mesImputacion'] = $row['mesImputacion'] . ' - ' . getMesEnLetras($row['mesImputacion']);
				$auxRow['zonaAfectacion'] = $row['zonaAfectacion'];
				$auxRow['cuit'] = $row['cuit'];
				$auxRow['establecimiento'] = $row['establecimiento'] . '/' . $row['sucursalEstablecimiento'];
				$auxRow['tipoOrigenCompra'] = $row['tipoOrigenCompra'];
				$auxRow['nroFactura'] = $row['nroFactura'];
				$opciones = '<div align="center">';
				if ($row['archivo']) {
					$opciones .= '<a class="text-black" target="_blank" href="' . getPath() . '/files/' . $this->archivo['ruta'] . $row['archivo'] . '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp;';
				}
				$opciones .= '<a class="text-black" href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idComprobanteCompraIVA'] . '&action=edit') . '" title="Ver"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;';
				$opciones .= '<a class="text-black btn-compose-modal-confirm" href="#" data-href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idComprobanteCompraIVA'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getComprobanteCompraIVA'){
	$aux = new ComprobanteCompraIVAVO();
	$data['term'] = $_GET['term'];
	$data['idEstablecimiento'] = $_GET['idEstablecimiento'];
	$data['abonadas'] = $_GET['abonadas'];
	$aux->getComprobanteCompraIVA($data, 'json');
}
if($_POST['action'] == 'dtSQL' && $_POST['page'] == 'ABMcomprobantesCompraIVA.php'){
	$aux = new ComprobanteCompraIVAVO();
	//print_r($_POST); die();
	if (!empty($_POST) ) {
		$aux->getDataTableSqlData($_POST);
	}
}

// debug zone
if($_GET['debug'] == 'ComprobanteCompraIVAVO' or false){
	echo "DEBUG<br>";
	$kk = new ComprobanteCompraIVAVO();
	//print_r($kk->getAllRows());
	$kk->idComprobanteCompraIVA = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
