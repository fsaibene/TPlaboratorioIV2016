<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class OrdenCompraVO extends Master2 {
	public $idOrdenCompra = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "ID",
	];
	public $nroOrdenCompra = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. orden de compra",
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
	public $idEstablecimientoVendedor = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "vendedor",
		"referencia" => "",
	];
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "comprador",
		"referencia" => "",
	];
	public $idZonaAfectacion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "domicilio de facturación",
		"referencia" => "",
	];
	public $idZonaAfectacionSedeEmision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Sede de emisión orden de compra",
		"referencia" => "",
	];
	public $idTipoSeguroFlete = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "flete/seguro",
		"referencia" => "",
	];
	public $idTipoLugarEntrega = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "lugar de entrega",
		"referencia" => "",
	];
	public $otroLugarEntrega = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "lugar de entrega",
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "archivo",
		"ruta" => "compras/ordenesCompra/", // de files/ en adelante
		"tamaño" => 10485760, // 10 * 1048576 = 10 mb
	];
	public $formaDePago = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "forma de pago",
	];
	public $idTipoMoneda = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "moneda",
		"referencia" => "",
	];
	public $tipoDeCambio = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "tipo de cambio",
	];
	public $idCuentaBancaria = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "combo",
		"nombre" => "cuenta bancaria",
		"referencia" => "",
	];
	public $plazoEntrega = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "plazo de entrega",
	];
	public $garantia = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "garantia",
	];
	public $notaAlProveedor = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "nota al proveedor",
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public $ordenCompraItemArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ordenesCompra');
		$this->setFieldIdName('idOrdenCompra');
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
		$this->idEstablecimientoVendedor['referencia'] = new EstablecimientoVendedorVO();
		$this->idEmpleado['referencia'] = new EmpleadoVO();
		$this->idZonaAfectacion['referencia'] = new ZonaAfectacionVO();
		$this->idZonaAfectacionSedeEmision['referencia'] = new ZonaAfectacionVO();
		$this->idTipoLugarEntrega['referencia'] = new TipoLugarEntregaVO();
		$this->idTipoSeguroFlete['referencia'] = new TipoSeguroFleteVO();
		$this->idTipoMoneda['referencia'] = new TipoMonedaVO();
		$this->idCuentaBancaria['referencia'] = new CuentaBancariaVO();
		$this->fecha['valor'] = date('d/m/Y');
		$this->getNroOrdenCompra();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		return $resultMessage;
	}

	public function getNroOrdenCompra(){
		$sql = "select max(nroOrdenCompra) as nroOrdenCompra from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroOrdenCompra['valor'] = $rs[0]['nroOrdenCompra'] + 1;
			} else {
				$this->nroOrdenCompra['valor'] = 1;
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getCodigoOrdenCompra(){
		$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		return 'OC-'.$this->idZonaAfectacionSedeEmision['referencia']->sigla['valor'].'-'.substr($aux[2], -2).$aux[1].'-'.str_pad($this->nroOrdenCompra['valor'], 4, '0', STR_PAD_LEFT);
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
				//print_r($this); die('www');
				$this->conn->rollBack();
				return $this;
			}
			//print_r($this); die('dos');
			if($this->ordenCompraItemArray) {
				//print_r($this->ordenCompraItemArray); die('tres');
				foreach ($this->ordenCompraItemArray as $aux){
					//print_r($aux); die();
					$aux->idOrdenCompra['valor'] = $this->idOrdenCompra['valor'];
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
			$aux = new OrdenCompraItemVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idOrdenCompra';
			$data['valorCampoWhere'] = $this->idOrdenCompra['valor'];
			$aux->deleteData($data);
			if($aux->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->result = $aux->result;
				$this->conn->rollBack();
				return $this;
			}
			if($this->ordenCompraItemArray) {
				//print_r($this->ordenCompraItemArray); //die();
				foreach ($this->ordenCompraItemArray as $aux){
					//print_r($aux); die();
					$aux->idOrdenCompra['valor'] = $this->idOrdenCompra['valor'];
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

	function getOrdenCompraPDF(){
		try {
			$htmlBody = $this->getPDF();
			//print_r($htmlBody); die();
			//echo $result->getData();
			// documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
			// para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
			//$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
			$document = '<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="L">';
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

		$ordenCompraItemArray = new OrdenCompraItemVO();
		$data = null;
		$data['nombreCampoWhere'] = 'idOrdenCompra';
		$data['valorCampoWhere'] = $this->idOrdenCompra['valor'];
		$ordenCompraItemArray->getAllRows($data);

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
										<td>
											<table>
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
							<td style="width: 50%">
								<table class="borderYes">
									<thead>
										<tr>
											<th colspan="2" style="width: 100%">ORDEN DE COMPRA</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Nº: '.$this->getCodigoOrdenCompra().'</td>
											<td>Fecha de emisión: '.convertDateDbToEs($this->fecha['valor']).'</td>
										</tr>
										<tr>
											<td>Dirección de facturación:</td>
											<td>'.$this->idZonaAfectacion['referencia']->direccion['valor'].'</td>
										</tr>
										<tr>
											<td>Lugar de entrega:</td>
											<td>'.$this->idTipoLugarEntrega['referencia']->tipoLugarEntrega['valor'].'</td>
										</tr>
										<tr>
											<td>Flete/Seguro a cargo de:</td>
											<td>'.$this->idTipoSeguroFlete['referencia']->tipoSeguroFlete['valor'].'</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
					<table >
						<tr>
							<td style="width: 50%">
								<table>
									<tr>
										<td>Proveedor: </td>
										<td>'.$this->idEstablecimiento['referencia']->establecimiento['valor'].'
											<br>'.$this->idEstablecimientoVendedor['referencia']->vendedor['valor'].'
											<br>'.$this->idEstablecimientoVendedor['referencia']->email['valor'].'
										</td>
									</tr>
								</table>
							</td>
							<td style="width: 50%">
								<table>
									<tr>
										<td>Comprador: </td>
										<td>'.$this->idEmpleado['referencia']->getNombreCompleto().'
											<br>Tel.: 4822-0709
											<br>E-mail: '.$this->idEmpleado['referencia']->emailEmpresa['valor'].'
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					<table class="borderYes" style="margin-top: 5px;">
						<thead>
							<tr>
								<th>Item</th>
								<th>Cantidad</th>
								<th>Descripción</th>
								<th>Precio Unitario</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>';
		$aux = 1;
		$total = 0;
		foreach($ordenCompraItemArray->result->getData() as $oci){
			$tipoMonedaSimbolo = $oci->idTipoMoneda['referencia']->simbolo['valor'];
			$tipoMoneda = $oci->idTipoMoneda['referencia']->tipoMoneda['valor'];
			$subTotal = $oci->cantidad['valor'] * $oci->precioUnitario['valor'];
			//print_r($oci->cantidad['valor']); die();
			$html .= '<tr>';
			$html .= '<td align="center">#'.$aux.'</td>';
			$html .= '<td align="center">';
			$html .= $oci->cantidad['valor'];
			$html .= '</td>';
			$html .= '<td style="width: 68%">';
			$html .= $oci->item['valor'];
			$html .= '</td>';
			$html .= '<td align="right">';
			$html .= $oci->idTipoMoneda['referencia']->simbolo['valor'];
			$html .= ' '.$oci->precioUnitario['valor'];
			$html .= '</td>';
			$html .= '<td align="right">';
			$html .= $oci->idTipoMoneda['referencia']->simbolo['valor'];
			$html .= ' '.number_format($subTotal, 2, ',', '.');
			$html .= '</td>';
			$html .= '</tr>';
			$aux++;
			$total += $subTotal;
		}
		$enLetras = new EnLetras();
		$totalEnLetras = $enLetras->ValorEnLetras($total, $tipoMoneda);
		$html .= '				</tbody>
								<tfoot>
									<tr>
										<td colspan="5" style="width: 100%;">
											<table>
												<tr>
													<td style="width: 60%;">Los importes aquí consignados NO INCLUYEN el Impuesto al Valor Agregado (IVA)</td>
													<td style="width: 40%;" align="right">
														Monto total: '.$tipoMonedaSimbolo.' '.number_format($total, 2, ',', '.').'
														<br>(son '.$totalEnLetras.')
													</td>
												</tr>
											</table>
										</td>
									</tr>
								</tfoot>
							</table>
							<table class="borderYes" style="margin-top: 5px;">
								<thead>
									<tr>
										<th colspan="2" style="width: 100%;">Condiciones comerciales</th>
									</tr>
								</thead>
								<tbody>';
		if($this->formaDePago['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Forma de pago:</td>
										<td style="width: 88%;">'.$this->formaDePago['valor'].'</td>
									</tr>';
		}
		if($this->idTipoMoneda['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Moneda:</td>
										<td style="width: 88%;">'.$this->idTipoMoneda['referencia']->tipoMoneda['valor'].'</td>
									</tr>';
		}
		if($this->tipoDeCambio['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Tipo de cambio:</td>
										<td style="width: 88%;">'.$this->tipoDeCambio['valor'].'</td>
									</tr>';
		}
		if($this->idCuentaBancaria['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Cuenta bancaria:</td>
										<td style="width: 88%;">'.$this->idCuentaBancaria['referencia']->getNombreCompleto().'</td>
									</tr>';
		}
		if($this->plazoEntrega['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Plazo de entrega:</td>
										<td style="width: 88%;">'.$this->plazoEntrega['valor'].'</td>
									</tr>';
		}
		if($this->garantia['valor']) {
			$html .= '				<tr>
										<td style="width: 12%;">Garantia:</td>
										<td style="width: 88%;">'.$this->garantia['valor'].'</td>
									</tr>';
		}
		$html .= '				</tbody>
							</table>';
		if($this->notaAlProveedor['valor']){
			$html .= '<table class="borderYes" style="margin-top: 5px;">
							<thead>
								<tr>
									<th style="width: 100%;">Notas al proveedor</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>'.$this->notaAlProveedor['valor'].'</td>
								</tr>
							</tbody>
						</table>';
		}
		$html .= '<table style="margin-top: 40px; font-size: 7pt;">
								<tr>
									<td style="width: 60%;" rowspan="5">&nbsp;</td>
									<td style="width: 40%;" align="center"><hr/></td>
								</tr>
								<tr>
									<td align="center">Firma del proveedor</td>
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
							</table>
					';
		return html_entity_decode($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	* devuelve datos de una OC
	*/
	public function getOrdenCompraPorCodigoOrdenCompra($data){
		$sql = 'SELECT CONCAT(e.establecimiento, " [", e.cuit, "] - ", DATE_FORMAT(oc.fecha,"%d/%m/%Y")) as info, oc.idOrdenCompra, oc.nroOrdenCompra
				from ordenesCompra as oc
				inner join establecimientos as e using (idEstablecimiento)
				inner join zonasAfectacion as za on za.idZonaAfectacion = oc.idZonaAfectacionSedeEmision
				where CONCAT_ws("-", "OC", za.sigla, concat(SUBSTR(EXTRACT(YEAR FROM fecha) FROM 3 FOR 2), LPAD(EXTRACT(month FROM fecha), 2, "0")), LPAD(nroOrdenCompra, 4, "0")) = "'.$data['codigoOrdenCompra'].'"
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
if($_GET['action'] == 'json' && $_GET['type'] == 'getOrdenCompraPorCodigoOrdenCompra'){
	$aux = new OrdenCompraVO();
	$data = array();
	$data['codigoOrdenCompra'] = $_GET['codigoOrdenCompra'];
	$aux->getOrdenCompraPorCodigoOrdenCompra($data);
}

// debug zone
if($_GET['debug'] == 'OrdenCompraVO' or false){
	//echo "DEBUG<br>";
	include_once("../../tools/dompdf/dompdf_config.inc.php");
	$kk = new OrdenCompraVO();
	//print_r($kk->getAllRows());
	$kk->idOrdenCompra['valor'] = 1;
	$html = $kk->getOrdenCompraPDF();
	//echo $html; die();
	$dompdf = new DOMPDF();
	$dompdf->set_paper("A4", "landscape");
	$dompdf->load_html($html);
	$dompdf->render();
	$dompdf->stream("OC-". $objectVO->nroOrdenCompra['valor'].".pdf", array('Attachment'=>0));

	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}