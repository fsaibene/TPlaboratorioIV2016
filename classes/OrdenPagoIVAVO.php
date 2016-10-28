<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class OrdenPagoIVAVO extends Master2 {
	public $idOrdenPago = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $nroOrdenPago = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. orden de pago",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE
		],
	];
	public $fecha = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
	public $idEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "establecimiento",
		"referencia" => "",
	];
	public $idZonaAfectacionSedeEmision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Sede de emisión orden de pago",
		"referencia" => "",
	];
	public $referencia = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "string",
		"nombre" => "referencia",
	];
	public $descuento = ["valor" => "0.00",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "descuento",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public $ordenPagoIVAComprobanteCompraIVAArray;
	public $ordenPagoIVATipoRegimenRetencionPagoArray;
	public $ordenPagoIVAChequeArray;
	public $ordenPagoIVATransferenciaArray;
	public $ordenPagoIVATarjetaCreditoArray;
	public $ordenPagoIVATarjetaDebitoArray;
	public $ordenPagoIVAEfectivoArray;
	public $ordenPagoIVADebitoEnCuentaArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ordenesPagoIVA');
		$this->setFieldIdName('idOrdenPago');
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
		$this->idZonaAfectacionSedeEmision['referencia'] = new ZonaAfectacionVO();
		$this->fecha['valor'] = date('d/m/Y');
		$this->getNroOrdenPago();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getNroOrdenPago(){
		$sql = "select max(nroOrdenPago) as nroOrdenPago from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroOrdenPago['valor'] = $rs[0]['nroOrdenPago'] + 1;
			} else {
				$this->nroOrdenPago['valor'] = 1;
			}
			$this->result->setStatus(STATUS_OK);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getCodigoOrdenPagoIVA(){
		$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		return 'OP-IVA-'.$this->idZonaAfectacionSedeEmision['referencia']->sigla['valor'].'-'.substr($aux[2], -2).$aux[1].'-'.str_pad($this->nroOrdenPago['valor'], 4, '0', STR_PAD_LEFT);
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
			if($this->ordenPagoIVAComprobanteCompraIVAArray) {
				foreach ($this->ordenPagoIVAComprobanteCompraIVAArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVATipoRegimenRetencionPagoArray) {
				foreach ($this->ordenPagoIVATipoRegimenRetencionPagoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVAChequeArray) {
				foreach ($this->ordenPagoIVAChequeArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVATransferenciaArray) {
				foreach ($this->ordenPagoIVATransferenciaArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVATarjetaCreditoArray) {
				foreach ($this->ordenPagoIVATarjetaCreditoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVATarjetaDebitoArray) {
				foreach ($this->ordenPagoIVATarjetaDebitoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVAEfectivoArray) {
				foreach ($this->ordenPagoIVAEfectivoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error uno');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}
			if($this->ordenPagoIVADebitoEnCuentaArray) {
				foreach ($this->ordenPagoIVADebitoEnCuentaArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
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
			$aux = new OrdenPagoIVAComprobanteCompraIVAVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVAComprobanteCompraIVAArray) {
				foreach ($this->ordenPagoIVAComprobanteCompraIVAArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVATipoRegimenRetencionPagoVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVATipoRegimenRetencionPagoArray) {
				foreach ($this->ordenPagoIVATipoRegimenRetencionPagoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVAChequeVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVAChequeArray) {
				foreach ($this->ordenPagoIVAChequeArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVATransferenciaVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVATransferenciaArray) {
				foreach ($this->ordenPagoIVATransferenciaArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVATarjetaCreditoVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVATarjetaCreditoArray) {
				foreach ($this->ordenPagoIVATarjetaCreditoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVATarjetaDebitoVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVATarjetaDebitoArray) {
				foreach ($this->ordenPagoIVATarjetaDebitoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVAEfectivoVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVAEfectivoArray) {
				foreach ($this->ordenPagoIVAEfectivoArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
					$aux->insertData();
					if($aux->result->getStatus()  != STATUS_OK) {
						//print_r($aux); die('error dos');
						$this->result = $aux->result;
						$this->conn->rollBack();
						return $this;
					}
				}
			}

			$aux = new OrdenPagoIVADebitoEnCuentaVO();
			$data = array();
			$data['nombreCampoWhere'] = $this->getFieldIdName();
			$data['valorCampoWhere'] = $this->{$this->getFieldIdName()}['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenPagoIVADebitoEnCuentaArray) {
				foreach ($this->ordenPagoIVADebitoEnCuentaArray as $aux){
					//print_r($aux); die();
					$aux->{$this->getFieldIdName()}['valor'] = $this->{$this->getFieldIdName()}['valor'];
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

	function getOrdenPagoIVAPDF(){
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

		$data = null;
		$data['nombreCampoWhere'] = 'idOrdenPago';
		$data['valorCampoWhere'] = $this->idOrdenPago['valor'];
		$opComprobanteCompraIVAArray = new OrdenPagoIVAComprobanteCompraIVAVO();
		$opComprobanteCompraIVAArray->getAllRows($data);
		$opTipoRegimenRetencionPagoArray = new OrdenPagoIVATipoRegimenRetencionPagoVO();
		$opTipoRegimenRetencionPagoArray->getAllRows($data);
		$opChequeArray = new OrdenPagoIVAChequeVO();
		$opChequeArray->getAllRows($data);
		$opTransferenciaArray = new OrdenPagoIVATransferenciaVO();
		$opTransferenciaArray->getAllRows($data);
		$opTarjetaCreditoArray = new OrdenPagoIVATarjetaCreditoVO();
		$opTarjetaCreditoArray->getAllRows($data);
		$opTarjetaDebitoArray = new OrdenPagoIVATarjetaDebitoVO();
		$opTarjetaDebitoArray->getAllRows($data);
		$opEfectivoArray = new OrdenPagoIVAEfectivoVO();
		$opEfectivoArray->getAllRows($data);
		$opDebitoEnCuentaArray = new OrdenPagoIVADebitoEnCuentaVO();
		$opDebitoEnCuentaArray->getAllRows($data);

		$css = '<style>
					.font8 {
						font-size: 8pt;
					}
					table {
						width:  100%;
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
		$html .= '	<table cellspacing="0" style="" class="font8">
						<tr>
							<td style="width: 380px;">
								<table>
									<tr>
										<td><img src="'.getFullPath().'/img/logo-sinec-nuevo-295x217.jpg" alt="" width="120" style="margin: -10px 0 0 -20px;" alt="" /></td>
										<td>
											<table class="font8">
												<tr>
													<td>SINEC S.A.</td>
												</tr>
												<tr>
													<td>CUIT: 30-71115059-1</td>
												</tr>
												<tr>
													<td>French 3102, Ciudad Autónoma de Buenos Aires</td>
												</tr>
												<tr>
													<td>República Argentina</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td style="width: 290px;">
								<table class="borderYes font8" align="right">
									<thead>
										<tr>
											<th colspan="2">ORDEN DE PAGO</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="width: 60px;">Nº:</td>
											<td style="width: 210px;">'.$this->getCodigoOrdenPagoIVA().'</td>
										</tr>
										<tr>
											<td>Fecha:</td>
											<td>'.convertDateDbToEs($this->fecha['valor']).'</td>
										</tr>
										<tr>
											<td>Razón Social:</td>
											<td>'.$this->idEstablecimiento['referencia']->establecimiento['valor'].'</td>
										</tr>
										<tr>
											<td>C.U.I.T.:</td>
											<td>'.$this->idEstablecimiento['referencia']->cuit['valor'].'</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
					<p class="font8">Referencia: '.$this->referencia['valor'].'</p>
					<br><p class="font8" style="text-decoration: underline;">Comprobantes que originan el pago:</p>
					<table class="borderYes font8" style="margin-top: 5px;">
						<thead>
							<tr>
								<th style="width: 88px;">Fecha de factura</th>
								<th style="width: 80px;">Tipo</th>
								<th style="width: 98px;">Número</th>
								<th style="width: 80px;">Neto</th>
								<th style="width: 79px;">IVA</th>
								<th style="width: 79px;">Percep. / Otros</th>
								<th style="width: 79px;">Total factura</th>
								<th style="width: 79px;">TOTAL A PAGAR</th>
							</tr>
						</thead>';
		$totalComprobantesCompraIVA = 0;
		if($opComprobanteCompraIVAArray->result->getData()) {
			foreach ($opComprobanteCompraIVAArray->result->getData() as $ocComprobanteCompraIVA) {
				$subTotal = $ocComprobanteCompraIVA->monto['valor'];
				$data['idComprobanteCompraIVA'] = $ocComprobanteCompraIVA->idComprobanteCompraIVA['valor'];
				$ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->getComprobanteCompraIVA($data);
				$subtotales = $ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->result->getData();
				//print_r($subtotales); die('asasdasd');
				$html .= '<tbody>';
					$html .= '<tr>';
						$html .= '<td align="center">' . $ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->fechaCompra['valor'] . '</td>';
						$html .= '<td align="center">' . $ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->idTipoComprobanteFiscal['referencia']->tipoComprobanteFiscal['valor'] . '</td>';
						$html .= '<td align="center">' . $ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->nroFactura['valor'] . '</td>';
						$html .= '<td align="right">$' . number_format($subtotales[0]['neto'], 2, ',', '.') . '.-</td>';
						$html .= '<td align="right">$' . number_format($subtotales[0]['iva'], 2, ',', '.') . '.-</td>';
						$html .= '<td align="right">$' . number_format($subtotales[0]['percepciones'], 2, ',', '.') . '.-</td>';
						$html .= '<td align="right">$' . number_format($subtotales[0]['total'], 2, ',', '.') . '.-</td>';
						$html .= '<td align="right">$' . number_format($subTotal, 2, ',', '.') . '.-</td>';
					$html .= '</tr>';
					$html .= '<tr>';
						$html .= '<td colspan="8">Obs.: ' . $ocComprobanteCompraIVA->idComprobanteCompraIVA['referencia']->observaciones['valor'] . '</td>';
					$html .= '</tr>';
				$html .= '</tbody>';
				$totalComprobantesCompraIVA += $subTotal;
			}
		}
		$html .= '  </table>';

		$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
						<thead>
							<tr>
								<th style="width: 260px;">Retención</th>
								<th style="width: 332px;">Regimen</th>
								<th style="width: 80px;">Monto de Retención</th>
							</tr>
						</thead>';
		$totalTiposRegimenRetencionPago = 0;
		if($opTipoRegimenRetencionPagoArray->result->getData()) {
			foreach ($opTipoRegimenRetencionPagoArray->result->getData() as $ocTipoRegimenRetencionPago) {
				$subTotal = $ocTipoRegimenRetencionPago->monto['valor'];
				$html .= '<tbody>';
					$html .= '<tr>';
						$html .= '<td>' . $ocTipoRegimenRetencionPago->idTipoRegimenRetencionPago['referencia']->idTipoRetencionPago['referencia']->tipoRetencionPago['valor'] . '</td>';
						$html .= '<td>' . $ocTipoRegimenRetencionPago->idTipoRegimenRetencionPago['referencia']->tipoRegimenRetencionPago['valor'] . '</td>';
						$html .= '<td align="right">$' . number_format($subTotal, 2, ',', '.') . '.-</td>';
					$html .= '</tr>';
				$html .= '</tbody>';
				$totalTiposRegimenRetencionPago += $subTotal;
			}
		}
		$html .= '	</table>';
		$descuento = $this->descuento['valor'];
		$html .= '  <table class="borderYes font8" style="margin-top: 5px;" align="right">
						<tr>
							<td align="center" style="width: 100px; background-color: #eee;">Descuentos</td>
							<td align="right" style="width: 80px;">$' . number_format($descuento, 2, ',', '.') . '.-</td>
						</tr>
					</table>';
		$total = $totalComprobantesCompraIVA - $totalTiposRegimenRetencionPago - $descuento;
		$html .= '  <table class="borderYes font8" style="margin-top: 5px;" align="right">
						<tr>
							<td align="center" style="width: 100px; background-color: #eee;">Neto a pagar</td>
							<td align="right" style="width: 80px;">$' . number_format($total, 2, ',', '.') . '.-</td>
						</tr>
					</table>';

		$html .= '<br><p class="font8" style="text-decoration: underline;">Condiciones y formas de pago:</p>';
		$subTotal = 0;
		if($opChequeArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Cheques</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 332px;">Banco</th>
									<th style="width: 160px;">Cheque Nº</th>
									<th style="width: 100px;">Fecha emisión</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opChequeArray->result->getData() as $ocCheque) {
						$html .= '<tr>';
							$html .= '<td>' . $ocCheque->idChequera['referencia']->idSucursalEstablecimiento['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] . '</td>';
							$html .= '<td>' . $ocCheque->nroCheque['valor'] . '</td>';
							$html .= '<td align="center">' . convertDateDbToEs($ocCheque->fechaEmision['valor']) . '</td>';
							$html .= '<td align="right">$' . number_format($ocCheque->monto['valor'], 2, ',', '.') . '.-</td>';
						$html .= '</tr>';
				$subTotal += $ocCheque->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		if($opTransferenciaArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Transferencias bancarias</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 170px;">Cuenta origen</th>
									<th style="width: 322px;">Cuenta destino</th>
									<th style="width: 100px;">Fecha pago</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opTransferenciaArray->result->getData() as $ocTransferencia) {
						$html .= '<tr>';
							$html .= '<td>' . $ocTransferencia->idCuentaBancariaOrigen['referencia']->idSucursalEstablecimientoBanco['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] . '</td>';
							$html .= '<td>' . $ocTransferencia->idCuentaBancariaDestino['referencia']->idSucursalEstablecimientoBanco['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] .' CBU: '. $ocTransferencia->idCuentaBancariaDestino['referencia']->cbu['valor'] . '</td>';
							$html .= '<td align="center">' . convertDateDbToEs($ocTransferencia->fechaPago['valor']) . '</td>';
							$html .= '<td align="right">$' . number_format($ocTransferencia->monto['valor'], 2, ',', '.') . '.-</td>';
						$html .= '</tr>';
				$subTotal += $ocTransferencia->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		if($opTarjetaCreditoArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Tarjetas de crédito</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 492px;">Tarjeta</th>
									<th style="width: 100px;">Cuotas</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opTarjetaCreditoArray->result->getData() as $ocTarjetaCredito) {
						$html .= '<tr>';
							$html .= '<td>' . $ocTarjetaCredito->idTarjetaCredito['referencia']->idSucursalEstablecimiento['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] . ' ' .$ocTarjetaCredito->idTarjetaCredito['referencia']->idTipoMarcaTarjeta['referencia']->tipoMarcaTarjeta['valor'] . ' ' . $ocTarjetaCredito->idTarjetaCredito['referencia']->nroTarjetaCredito['valor'] . '</td>';
							$html .= '<td align="right">' . $ocTarjetaCredito->cuotas['valor'] . '</td>';
							$html .= '<td align="right">$' . number_format($ocTarjetaCredito->monto['valor'], 2, ',', '.') . '.-</td>';
						$html .= '</tr>';
				$subTotal += $ocTarjetaCredito->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		if($opTarjetaDebitoArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Tarjetas de débito</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 232px;">Empleado</th>
									<th style="width: 360px;">Tarjeta</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opTarjetaDebitoArray->result->getData() as $ocTarjetaDebito) {
						$html .= '<tr>';
							$html .= '<td>' . $ocTarjetaDebito->idEmpleadoTarjetaDebito['referencia']->idEmpleado['referencia']->getNombreCompleto() . '</td>';
							$html .= '<td>' . $ocTarjetaDebito->idEmpleadoTarjetaDebito['referencia']->idSucursalEstablecimiento['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] . ' ' . $ocTarjetaDebito->idEmpleadoTarjetaDebito['referencia']->idTipoMarcaTarjeta['referencia']->tipoMarcaTarjeta['valor'] . ' ' . $ocTarjetaDebito->idEmpleadoTarjetaDebito['referencia']->nroTarjetaDebito['valor'] . '</td>';
							$html .= '<td align="right">$' . number_format($ocTarjetaDebito->monto['valor'], 2, ',', '.') . '.-</td>';
						$html .= '</tr>';
				$subTotal += $ocTarjetaDebito->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		if($opEfectivoArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Efectivo</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;" align="right">
							<thead>
								<tr>
									<th style="width: 100px;">Fecha pago</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opEfectivoArray->result->getData() as $ocEfectivo) {
						$html .= '<tr>';
							$html .= '<td align="center">' . convertDateDbToEs($ocEfectivo->fechaPago['valor']) . '</td>';
							$html .= '<td align="right">$' . number_format($ocEfectivo->monto['valor'], 2, ',', '.') . '.-</td>';
						$html .= '</tr>';
				$subTotal += $ocEfectivo->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		if($opDebitoEnCuentaArray->result->getData()) {
			$html .= '<br><p class="font8" style="text-decoration: underline;">Débitos en cuenta</p>';
			$html .= '  <table class="borderYes font8" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 492px;">Cuenta origen</th>
									<th style="width: 100px;">Fecha pago</th>
									<th style="width: 80px;">Monto</th>
								</tr>
							</thead>';
			$html .= '		<tbody>';
			foreach ($opDebitoEnCuentaArray->result->getData() as $ocDebitoEnCuenta) {
				$html .= '<tr>';
				$html .= '<td>' . $ocDebitoEnCuenta->idCuentaBancariaOrigen['referencia']->idSucursalEstablecimientoBanco['referencia']->idEstablecimiento['referencia']->establecimiento['valor'] . '</td>';
				$html .= '<td align="center">' . convertDateDbToEs($ocDebitoEnCuenta->fechaPago['valor']) . '</td>';
				$html .= '<td align="right">$' . number_format($ocDebitoEnCuenta->monto['valor'], 2, ',', '.') . '.-</td>';
				$html .= '</tr>';
				$subTotal += $ocDebitoEnCuenta->monto['valor'];
			}
			$html .= '		</tbody>';
			$html .= '	</table>';
		}

		$html .= '<br>';
		$html .= '  <table class="borderYes font8" style="margin-top: 5px;" align="right">
						<tr>
							<td align="center" style="width: 100px; background-color: #eee;">TOTAL</td>
							<td align="right" style="width: 80px;">$' . number_format($subTotal, 2, ',', '.') . '.-</td>
						</tr>
					</table>';

		$html .= '  <table style="margin-top: 80px; font-size: 7pt;">
  						<tr>
  							<td style="padding-right:30px;">';
		$html .= '  			<table>
									<tr>
										<td style="width: 200px;" align="center"><hr/></td>
									</tr>
									<tr>
										<td align="center">Emisor de la OP</td>
									</tr>
									<tr>
										<td>Aclaración:</td>
									</tr>
									<tr>
										<td>D.N.I.:</td>
									</tr>
									<tr>
										<td>Carácter del firmante:</td>
									</tr>
								</table>';
		$html .= '  		</td>
  							<td style="padding-right:30px;">';
		$html .= '  			<table>
									<tr>
										<td style="width: 200px;" align="center"><hr/></td>
									</tr>
									<tr>
										<td align="center">Revisado y autorizado de la OP</td>
									</tr>
									<tr>
										<td>Aclaración:</td>
									</tr>
									<tr>
										<td>D.N.I.:</td>
									</tr>
									<tr>
										<td>Carácter del firmante:</td>
									</tr>
								</table>';
		$html .= '  		</td>
  							<td>';
		$html .= '  			<table>
									<tr>
										<td style="width: 200px;" align="center"><hr/></td>
									</tr>
									<tr>
										<td align="center">Proveedor</td>
									</tr>
									<tr>
										<td>Aclaración:</td>
									</tr>
									<tr>
										<td>D.N.I.:</td>
									</tr>
									<tr>
										<td>Carácter del firmante:</td>
									</tr>
								</table>';
		$html .= '  		</td>
  						</tr>
  					</table>';

		return html_entity_decode($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	public function getDataTableSqlData($dataPOST){
		$sqlData = 'select a.idOrdenPago, CONCAT_ws("-", "OP", "IVA", b.sigla, concat(SUBSTR(EXTRACT(YEAR FROM fecha) FROM 3 FOR 2), LPAD(EXTRACT(month FROM fecha), 2, "0")), LPAD(nroOrdenPago, 4, "0")) as codigo,
					a.fecha, b.zonaAfectacion, f.cuit, f.establecimiento
					from ordenesPagoIVA as a
					inner join zonasAfectacion as b on b.idZonaAfectacion = a.idZonaAfectacionSedeEmision
					inner join ordenesPagoIVA_comprobantesCompraIVA as c using (idOrdenPago)
					inner join comprobantesCompraIVA as d using (idComprobanteCompraIVA)
					inner join establecimientos as f using (idEstablecimiento)
					where a.idZonaAfectacionSedeEmision = '.$_SESSION['usuarioLogueadoIdZonaAfectacion'].'
					group by idOrdenPago
					';

		$data = getDataTableSqlDataFilter($dataPOST, $sqlData);
		if($data['data']) {
			foreach ($data['data'] as $row) {
				//print_r($row); die();
				$auxRow['codigo'] = $row['codigo'];
				$auxRow['fecha'] = convertDateDbToEs($row['fecha']);
				$auxRow['zonaAfectacion'] = $row['zonaAfectacion'];
				$auxRow['cuit'] = $row['cuit'];
				$auxRow['establecimiento'] = $row['establecimiento'];
				$opciones = '<div align="center">';
				$opciones .= '<a class="text-black" target="_blank" href="' . getPath() . '/pdfs/OrdenPagoIVAPDF.php?' . codificarGets('id=' . $row['idOrdenPago'] . '&action=pdf') . '" title="Descargar documento"><span class="fa fa-file-pdf-o fa-lg"></span></a>&nbsp;&nbsp;';
				$opciones .= '<a class="text-black" href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idOrdenPago'] . '&action=edit') . '" title="Ver"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;';
				$opciones .= '<a class="text-black btn-compose-modal-confirm" href="#" data-href="' . $dataPOST['page'] . '?' . codificarGets('id=' . $row['idOrdenPago'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
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
if($_POST['action'] == 'dtSQL' && $_POST['page'] == 'ABMordenesPagoIVA.php'){
	$aux = new OrdenPagoIVAVO();
	//print_r($_POST); die();
	if (!empty($_POST) ) {
		$aux->getDataTableSqlData($_POST);
	}
}

// debug zone
if($_GET['debug'] == 'OrdenPagoIVAVO' or false){
	//echo "DEBUG<br>";
	$kk = new OrdenPagoIVAVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenPago['valor'] = 5;
	$html = $kk->getPDF();
	echo $html; die();


	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
