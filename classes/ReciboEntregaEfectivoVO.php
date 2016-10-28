<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ReciboEntregaEfectivoVO extends Master2 {
	public $idReciboEntregaEfectivo = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $nroReciboEntregaEfectivo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "nro. recibo",
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
	public $idZonaAfectacion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Sede de emisión",
		"referencia" => "",
	];
	public $idEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "empleado",
		"referencia" => "",
	];
	public $monto = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "float",
		"nombre" => "monto",
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

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('recibosEntregaEfectivo');
		$this->setFieldIdName('idReciboEntregaEfectivo');
		$this->idZonaAfectacion['referencia'] = new ZonaAfectacionVO();
		$this->idEmpleado['referencia'] = new EmpleadoVO();
		$this->fecha['valor'] = date('d/m/Y');
		$this->getNroReciboEntregaEfectivo();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getNroReciboEntregaEfectivo(){
		$sql = "select max(nroReciboEntregaEfectivo) as nroReciboEntregaEfectivo from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroReciboEntregaEfectivo['valor'] = $rs[0]['nroReciboEntregaEfectivo'] + 1;
			} else {
				$this->nroReciboEntregaEfectivo['valor'] = 1;
			}
			$this->result->setStatus(STATUS_OK);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getCodigoReciboEntregaEfectivo(){
		$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		return 'REE-'.$this->idZonaAfectacion['referencia']->sigla['valor'].'-'.substr($aux[2], -2).$aux[1].'-'.str_pad($this->nroReciboEntregaEfectivo['valor'], 4, '0', STR_PAD_LEFT);
	}

	public function getComboList2($data = NULL){
		try{
			$sql = 'select getCodigoReciboEntregaEfectivo(idReciboEntregaEfectivo) as label, idReciboEntregaEfectivo as data
					from recibosEntregaEfectivo as ree
					inner join zonasAfectacion as za using (idZonaAfectacion)
					where ree.idEmpleado = '.$data['idEmpleado'].'
					and idReciboEntregaEfectivo not in (
						select idReciboEntregaEfectivo
						from rendicionesViatico
						where idEmpleado = '.$data['idEmpleado'].' and idReciboEntregaEfectivo is not null ';
			if($data['idReciboEntregaEfectivo']) {
				$sql .= ' and idReciboEntregaEfectivo != ' . $data['idReciboEntregaEfectivo'];
			}
			$sql .= ' group by idReciboEntregaEfectivo
					)
					group by idReciboEntregaEfectivo
					order by idReciboEntregaEfectivo ';
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

	function getReciboEntregaEfectivoPDF(){
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
			$document .= '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>'.$htmlBody;
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
							<td style="width: 392px;">
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
							<td style="width: 270px;">
								<table class="borderYes font8" align="right">
									<thead>
										<tr>
											<th colspan="2">RECIBO</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="width: 130px;">Nº:</td>
											<td style="width: 140px;">'.$this->getCodigoReciboEntregaEfectivo().'</td>
										</tr>
										<tr>
											<td>Fecha de emisión:</td>
											<td>'.convertDateDbToEs($this->fecha['valor']).'</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
					<p class="font8">Observaciones: '.$this->observaciones['valor'].'</p>';


		$html .= '	<br>
					<table class="borderYes font8" style="margin-top: 5px;">
						<thead>
							<tr>
								<th style="width: 75%;">Empleado</th>
								<th style="width: 25%;">Monto</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>' . $this->idEmpleado['referencia']->getNombreCompleto() . '</td>
								<td align="right">$' . number_format($this->monto['valor'], 2, ',', '.') . '.-</td>
							</tr>
						</tbody>
					</table>';

		$html .= '<br>';

		$html .= '  <table style="margin-top: 80px; font-size: 7pt;" align="center">
  						<tr>
  							<td style="padding-right:30px;">';
		$html .= '  			<table>
									<tr>
										<td style="width: 200px;" align="center"><hr/></td>
									</tr>
									<tr>
										<td align="center">Responsable</td>
									</tr>
									<tr>
										<td>Aclaración:</td>
									</tr>
									<tr>
										<td>D.N.I.:</td>
									</tr>
								</table>';
		$html .= '  		</td>
  							<td>';
		$html .= '  			<table>
									<tr>
										<td style="width: 200px;" align="center"><hr/></td>
									</tr>
									<tr>
										<td align="center">Empleado</td>
									</tr>
									<tr>
										<td>Aclaración:</td>
									</tr>
									<tr>
										<td>D.N.I.:</td>
									</tr>
								</table>';
		$html .= '  		</td>
  						</tr>
  					</table>';

		return html_entity_decode($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	 * devuelve datos de un recibo
	 */
	public function getReciboEntregaEfectivoPorIdReciboEntregaEfectivo($data){
		$sql = 'select idReciboEntregaEfectivo, nroReciboEntregaEfectivo, DATE_FORMAT(fecha,"%d/%m/%Y") as fecha, monto,
				getCodigoReciboEntregaEfectivo(idReciboEntregaEfectivo) as codigoReciboEntregaEfectivo
				from recibosEntregaEfectivo
				inner join zonasAfectacion as za using (idZonaAfectacion)
				where idReciboEntregaEfectivo = '.$data['idReciboEntregaEfectivo'].'
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode($ro->fetchAll(PDO::FETCH_ASSOC));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getReciboEntregaEfectivoPorIdReciboEntregaEfectivo'){
	$aux = new ReciboEntregaEfectivoVO();
	$data = array();
	$data['idReciboEntregaEfectivo'] = $_GET['idReciboEntregaEfectivo'];
	$aux->getReciboEntregaEfectivoPorIdReciboEntregaEfectivo($data);
}

// debug zone
if($_GET['debug'] == 'ReciboEntregaEfectivoVO' or false){
	//echo "DEBUG<br>";
	$kk = new ReciboEntregaEfectivoVO();
	//print_r($kk->getAllRows());
	$kk->idReciboEntregaEfectivo['valor'] = 5;
	$html = $kk->getPDF();
	echo $html; die();


	//$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
