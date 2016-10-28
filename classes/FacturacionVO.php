<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

//clases para generar el código de barras.
//requiere clases de barcodephp
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/barcode/class/BCGFontFile.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/barcode/class/BCGColor.php');
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/barcode/class/BCGDrawing.php');
// Including the barcode technology code 128
require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/barcode/class/BCGi25.barcode.php');
//fin clases para generar el código de barras.

define ("WSDLCONEXION", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../tools/FacturacionElectronica/wsaa.wsdl");     # The WSDL corresponding to WSAA
define ("CERT", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../tools/FacturacionElectronica/SIGIwebCSR.pem");       # The X.509 certificate in PEM format
define ("PRIVATEKEY", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../tools/FacturacionElectronica/queDeAquiQueDeAndando"); # The private key correspoding to CERT (PEM)
define ("PASSPHRASE", "queDeAquiQueDeAndando"); # The passphrase (if any) to sign
define ("PROXY_HOST", "10.20.152.112"); # Proxy IP, to reach the Internet
define ("PROXY_PORT", "80");            # Proxy TCP port
define ("URLCONEXION", "https://wsaahomo.afip.gov.ar/ws/services/LoginCms");
define ("WSDLFEV1", dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../tools/FacturacionElectronica/wsdlafiphomov1.wsdl");     # The WSDL corresponding to WSFEV1
define ("URLFEV1", "https://wswhomo.afip.gov.ar/wsfev1/service.asmx");     # The URL corresponding to WSFEV1

/**
 * Created by PhpStorm.
 * User: German
 * Date: 02/04/2016
 * Time: 19:53
 */
class FacturacionVO extends Master2 {
	public $idFacturacion = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $puntoVenta = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "puntos de venta",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => TRUE,
			"admiteMayorAcero" => TRUE
		],
        "longitud" => "4",
	];
    public $idTipoConcepto = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "concepto",
        "referencia" => "",
    ];
    public $idTipoCondicionVenta = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "forma de pago",
        "referencia" => "",
    ];
    public $idTipoComprobante = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "tipo de comprobante",
        "referencia" => "",
    ];
    public $nroComprobante = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "nro comprobante",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE
        ],
        "longitud" => "8",
    ];
    public $idEstablecimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "establecimiento",
        "referencia" => "",
    ];
    //Guardo el idEstablecimiento (para poder completar los datos en el ABM) y el CUIT para (para enviarlo a AFIP).
    public $idZonaAfectacionSedeEmision = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "Sede de emisión orden de pago",
        "referencia" => "",
    ];
    public $nroDocumento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "nroDocumento",
    ];
    public $idTipoDocumento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "tipo de documento",
        "referencia" => "",
    ];
	public $fechaComprobante = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha de emisión",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE
		],
	];
    public $importeTotal = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe total",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $importeNetoGravado = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe neto gravado",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $importeNetoNoGravado = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe neto no gravado",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $importeIva = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe iva",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $importeBonificacion = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe bonificación",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $fechaServicioDesde = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de inicio de servicio",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $fechaServicioHasta = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de fin de servicio",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $fechaVencimientoPago = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de vencimiento",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $idTipoMoneda = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "moneda",
        "referencia" => "",
    ];
    public $cotizacionMoneda = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "cotizacion",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];

    public $cae = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "cae",
    ];

    public $fechaVencimientoCae = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "date",
        "nombre" => "fecha de vencimiento de cae",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $recuperadoAfip = ["valor" => FALSE,
        "obligatorio" => TRUE,
        "tipo" => "bool",
        "nombre" => "recuperado afip",
    ];

    public $facturacionItemArray;
	public $comprobantesAsociadosArray;
	public $tributosArray;
	public $ivaArray;
	public $opcionalesArray;


	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('facturacion');
		$this->setFieldIdName('idFacturacion');
		$this->idTipoComprobante['referencia'] = new AfipTipoComprobanteVO();
		$this->idTipoConcepto['referencia'] = new AfipTipoConceptoVO();
		$this->idTipoConcepto['valor'] = 2; //lo dejo seteado en servicios.
		$this->idTipoDocumento['referencia'] = new AfipTipoDocumentoVO();
		$this->idTipoDocumento['valor'] = 80; //lo dejo seteado en CUIT.
		$this->idTipoMoneda['referencia'] = new AfipTipoMonedaVO();
		$this->idTipoCondicionVenta['referencia'] = new AfipTipoCondicionesVentaVO();
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
		$this->cuit = 20284638841; #Cuit de quien realiza la consulta al WS.
		$this->cantidadRegistros = 1; #Cantidad de registros que se envían por lote.
		//$this->getNroComprobante(); //asigna el nro de comprobante correlativo.

		//inicializo esto para poder probar...
		$this->importeNetoNoGravado['valor'] = 0;
		$this->importeTrib['valor'] = 0;
		$this->importeExento['valor'] = 0;
		$this->importeNetoGravado['valor'] = 0;
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

    public function getNroComprobante($data, $format = null){
        $sql = "select max(nroComprobante) as nroComprobante from ".$this->getTableName();
        $sql .= ' where true ';
        if($data['puntoVenta'])
            $sql .= '  and puntoVenta = '.$data['puntoVenta'];
        if($data['idTipoComprobante'])
            $sql .= '  and idTipoComprobante = '.$data['idTipoComprobante'];
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            if(count($rs) == 1){
                $nroComprobante = $rs[0]['nroComprobante'] + 1;
            } else {
                $nroComprobante = 1;
            }
            if($format == 'json') {
                echo json_encode($nroComprobante);
                return;
            }else{
                $this->nroComprobante['valor'] = $nroComprobante;
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }

    /*
     * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
     */
    public function insertData($validaAfip = TRUE){
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
            if($this->facturacionItemArray) {
                //print_r($this->ordenCompraItemArray); die('tres');
                foreach ($this->facturacionItemArray as $aux){
                    //print_r($aux); die();
                    $aux->idFacturacion['valor'] = $this->idFacturacion['valor'];
                    $aux->insertData();
                    if($aux->result->getStatus()  != STATUS_OK) {
                        //print_r($aux); die('error uno');
                        $this->result = $aux->result;
                        $this->conn->rollBack();
                        return $this;
                    }
                }
            }

            //Pregunto si es necesario validar con AFIP, por defecto si.
            //No hay que validar cuando se llama desde la pantalla de Facturación de Contingencia, dado que ya tiene CAE asignado.
            if($validaAfip){
                //solicito el CAE a AFIP.
                $this->solicitarPermisoWS();
                $this->FECAESolicitar();

                //updateo el valor del CAE recibido en la tabla.
                parent::updateData();
            }

            if($this->result->getStatus() != STATUS_OK) {
                $this->conn->rollBack();
                return $this;
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
            $aux = new FacturacionItemVO();
            $data = array();
            $data['nombreCampoWhere'] = 'idFacturacion';
            $data['valorCampoWhere'] = $this->idFacturacion['valor'];
            $aux->deleteData($data);
            if($aux->result->getStatus() != STATUS_OK) {
                //print_r($aux); die('error uno');
                $this->result = $aux->result;
                $this->conn->rollBack();
                return $this;
            }
            if($this->facturacionItemArray) {
                //print_r($this->ordenCompraItemArray); //die();
                foreach ($this->facturacionItemArray as $aux){
                    //print_r($aux); die();
                    $aux->idFacturacion['valor'] = $this->idFacturacion['valor'];
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

    private function CreateTRA($SERVICE){
        $TRA = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<loginTicketRequest version="1.0">'.
            '</loginTicketRequest>');
        $TRA->addChild('header');
        $TRA->header->addChild('uniqueId',date('U'));
        $TRA->header->addChild('generationTime',date('c',date('U')-60));
        $TRA->header->addChild('expirationTime',date('c',date('U')+60));
        $TRA->addChild('service',$SERVICE);
        $TRA->asXML('TRA.xml');
    }

    # This functions makes the PKCS#7 signature using TRA as input file, CERT and
    # PRIVATEKEY to sign. Generates an intermediate file and finally trims the
    # MIME heading leaving the final CMS required by WSAA.
    private function SignTRA(){
        $STATUS=openssl_pkcs7_sign(realpath("TRA.xml"), getcwd()."/TRA.tmp", "file://".realpath(CERT),
            array("file://".realpath(PRIVATEKEY), PASSPHRASE),
            array(),
            !PKCS7_DETACHED
        );
        if (!$STATUS) {exit("ERROR generating PKCS#7 signature\n");}
        if(!($inf=fopen("TRA.tmp", "r"))) die("Error al abrir TRA.tmp");
        $i=0;
        $CMS="";
        while (!feof($inf))
        {
            $buffer=fgets($inf);
            if ( $i++ >= 4 ) {$CMS.=$buffer;}
        }
        fclose($inf);
#  unlink("TRA.xml");
        unlink("TRA.tmp");
        return $CMS;
    }

    private function CallWSAA($CMS){
        $client=new SoapClient(WSDLCONEXION, array(
            #'proxy_host'     => PROXY_HOST,
            #'proxy_port'     => PROXY_PORT,
            'soap_version'   => SOAP_1_2,
            'location'       => URLCONEXION,
            'trace'          => 1,
            'exceptions'     => 0
        ));
        $results=$client->loginCms(array('in0'=>$CMS));
        file_put_contents("request-loginCms.xml",$client->__getLastRequest());
        //echo "<br>request: ".$client->__getLastRequest();
        file_put_contents("response-loginCms.xml",$client->__getLastResponse());
        //echo "<br>reponse: ". $client->__getLastResponse();
        if (is_soap_fault($results))
        {exit("SOAP Fault: ".$results->faultcode."\n".$results->faultstring."\n");}
        return $results->loginCmsReturn;
    }

    public function solicitarPermisoWS(){
        ini_set("soap.wsdl_cache_enabled", "0");
        if (!file_exists(CERT)) {exit("Failed to open ".CERT."\n");}
        if (!file_exists(PRIVATEKEY)) {exit("Failed to open ".PRIVATEKEY."\n");}
        if (!file_exists(WSDLCONEXION)) {exit("Failed to open ".WSDLCONEXION."\n");}
        $SERVICE = 'wsfe';
        $this->CreateTRA($SERVICE);
        $CMS = $this->SignTRA();
        $TA = $this->CallWSAA($CMS);
        if (!file_put_contents("TA.xml", $TA)) {exit("No se pudo escribir en TA.xml");}

        //guardo los datos que obtuve de la conexión.
        $resultado = new SimpleXMLElement($TA);

        $fecha = $resultado->header->expirationTime;
        $fecha = str_replace('T', ' ', $fecha);

        $fecha = strtotime($fecha);

        $fecha = date("Y-m-d H:i", $fecha);

        $this->token = $resultado->credentials->token;
        //echo "\n"."TOKEN: ".$token."\n";

        $this->sign = $resultado->credentials->sign;
        //echo "\n"."SIGN: ".$sign."\n";

        //borro los archivos que se generaron en la conexión.
        unlink("TA.xml");
        unlink("TRA.xml");
        unlink("request-loginCms.xml");
        unlink("response-loginCms.xml");
    }

    #Función que convierte el Objeto que devuelve el XML de AFIP en un array.
    private function obj2array($obj) {
        $out = array();
        foreach ($obj as $key => $val) {
            switch(true) {
                case is_object($val):
                    $out[$key] = $this->obj2array($val);
                    break;
                case is_array($val):
                    $out[$key] = $this->obj2array($val);
                    break;
                default:
                    $out[$key] = $val;
            }
        }
        return $out;
    }

    private function convertDateEsToAfip ($date){
        if (!$date) {
            return;
        }
        $partes = explode(' ', $date); // esto lo hago por si es un timestamp
        $arrayAbuscar = array('.', '/');
        $date = str_replace($arrayAbuscar, '-', $partes[0]);
        $partesDeFecha = explode('-', $date);
        if(strlen($partesDeFecha[0]) == 4) // valido que la fecha no este ya en el formato db
            return trim($date.' '.$partes[1]);
        else
            return trim($partesDeFecha[2].$partesDeFecha[1].$partesDeFecha[0]);
    }


    public function convertDateAfipToEs ($string){
        if (!$string) {
            return;
        }
        $anio= substr($string, 0, 4);
        $mes = substr($string, 4, 2);
        $dia = substr($string, 6, 2);
        $fecha = $dia."/".$mes."/".$anio;

        return $fecha;
    }

    public function convertCuitDbToAfip ($string){
        if (!$string) {
            return;
        }

        $cuit = str_replace("-", "", $string);
        return $cuit;
    }

    public function convertCuitAfipToDb ($string){
        if (!$string) {
            return;
        }
        $aux1= substr($string, 0, 2);
        $aux2 = substr($string, 2, 8);
        $aux3 = substr($string, 10, 1);
        $cuit = $aux1."-".$aux2."-".$aux3;
        return $cuit;
    }

    //Función que envía los datos de un comprobante para registrar la FE
    public function FECAESolicitar()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos = array(); //parametros de la llamada
        $datos['soap_version'] = "SOAP_1_2";
        $datos['location'] = URLFEV1;
        $datos['trace'] = 1;
        $datos['exceptions'] = 0;

        $client = new SoapClient(WSDLFEV1, $datos);

        $Auth = array(); //parametros de la llamada
        $Auth['Token'] = $this->token;
        $Auth['Sign'] = $this->sign;
        $Auth['Cuit'] = $this->cuit;


        $FeCAEReq = array();
        //Información de la cabecera del comprobante o lote de comprobantes de ingreso
        $FeCAEReq['FeCabReq'] = array();
        //Cantidad de registros del detalle del comprobante o lote de comprobantes de ingreso
        $FeCAEReq['FeCabReq']['CantReg'] = $this->cantidadRegistros;
        //Punto de Venta del comprobante que se está informando. Si se informa más de un comprobante, todos deben corresponder al mismo punto de venta.
        $FeCAEReq['FeCabReq']['PtoVta'] = $this->puntoVenta['valor'];
        //Tipo de comprobante que se está informando. Si se informa más de un comprobante, todos deben ser del mismo tipo.
        $FeCAEReq['FeCabReq']['CbteTipo'] = $this->idTipoComprobante['valor'];

        //Información del detalle del comprobante o lote de comprobantes de ingreso
        $FeCAEReq['FeDetReq'] = array();
        $FeCAEReq['FeDetReq']['FECAEDetRequest'] = array();
        //Concepto del Comprobante
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['Concepto'] = $this->idTipoConcepto['valor'];
        //Código de documento identificatorio del comprador
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['DocTipo'] = $this->idTipoDocumento['valor'];
        //Nro. De identificación del comprador
        //$this->nroDocumento['valor'] = str_replace("-", "", $this->nroDocumento['valor']);
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['DocNro'] = str_replace("-", "", $this->nroDocumento['valor']);
        //Nro. De comprobante desde. Rango 1- 99999999
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['CbteDesde'] = $this->nroComprobante['valor'];
        //Nro. De comprobante registrado hasta. Rango 1- 99999999
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['CbteHasta'] = $this->nroComprobante['valor'];
        //Fecha del comprobante (yyyymmdd).
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['CbteFch'] = $this->convertDateEsToAfip($this->fechaComprobante['valor']);
        //Importe total del comprobante, Debe ser igual a Importe neto no gravado + Importe exento + Importe neto gravado + todos los campos de IVA al XX% + Importe de tributos.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotal'] = $this->importeTotal['valor'];
        //Importe exento.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpOpEx'] = $this->importeExento['valor'];
        //Suma de los importes del array de tributos
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTrib'] = $this->importeTrib['valor'];
        //Suma de los importes del array de IVA.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpIVA'] = $this->importeIva['valor'];
        //Fecha de inicio del abono para el servicio a facturar.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['FchServDesde'] = $this->convertDateEsToAfip($this->fechaServicioDesde['valor']);
        //Fecha de fin del abono para el servicio a facturar.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['FchServHasta'] = $this->convertDateEsToAfip($this->fechaServicioHasta['valor']);
        //Fecha de vencimiento del pago servicio a facturar.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['FchVtoPago'] = $this->convertDateEsToAfip($this->fechaVencimientoPago['valor']);
        //Código de moneda del comprobante.
        //asigno el Id a la refencia de tipo de moneda.
        $this->idTipoMoneda['referencia']->idAfipTipoMoneda['valor'] = $this->idTipoMoneda['valor'];
        $this->idTipoMoneda['referencia']->getRowById();
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['MonId'] = $this->idTipoMoneda['referencia']->codAfipTipoMoneda['valor'];
        //Cotización de la moneda informada.
        $this->FEParamGetCotizacion();//esta función busca la cotización, tiene que estar asignado el codAfipTipoMoneda.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['MonCotiz'] = $this->cotizacionMoneda['valor'];
        //Array para informar los comprobantes asociados
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['CbtesAsoc'] = $this->comprobantesAsociadosArray;
        //Array para informar los tributos asociados a un comprobante <Tributo>.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['Tributos'] = $this->tributosArray;
        //Importe neto no gravado.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'] = 0;
        //Importe neto gravado.
        $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpNeto'] = 0;
        //Array para informar las alícuotas y sus importes asociados a un comprobante <AlicIva>.
        $iva=0; //pongo esta bandera para saber si hay iva.
        foreach ($this->facturacionItemArray as $row){
            //si el iva es 0 no lo pongo en el ARRAY.
            if ($row->idTipoIva['valor']!=3){
                $ivaArray['Id'] = $row->idTipoIva['valor'];
                $ivaArray['BaseImp'] = $row->cantidad['valor']*$row->precioUnitario['valor']-$row->importeBonificacion['valor'];
                $ivaArray['Importe'] = $row->importeIva['valor'];
                $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpNeto'] += $row->cantidad['valor']*$row->precioUnitario['valor']-$row->importeBonificacion['valor'];
                $this->ivaArray[] = $ivaArray;
                $iva++;
            }
            else{
                $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'] += $row->cantidad['valor']*$row->precioUnitario['valor']-$row->importeBonificacion['valor'];
            }
            //Si el comprobante es de Tipo C ImpNeto corresponde al Subtotal, para los otros corresponde ImpTotConc
            /*if($this->idTipoComprobante['valor']==11 || $this->idTipoComprobante['valor']==12 || $this->idTipoComprobante['valor']==13 || $this->idTipoComprobante['valor']==15){
                $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpNeto'] += $row->cantidad['valor']*$row->precioUnitario['valor']-$row->importeBonificacion['valor'];
            }else{
                $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'] += $row->cantidad['valor']*$row->precioUnitario['valor']-$row->importeBonificacion['valor'];
            }*/

        }
        //si hay iva asigno el array.
        if($iva>0){
            $FeCAEReq['FeDetReq']['FECAEDetRequest']['Iva']['AlicIva'] = $this->ivaArray;
        }

        //Si el comprobante es de Tipo C, Importe del concepto tiene que ser 0.
        if($this->idTipoComprobante['valor']==11 || $this->idTipoComprobante['valor']==12 || $this->idTipoComprobante['valor']==13 || $this->idTipoComprobante['valor']==15){
            $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpNeto'] = $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'];
            $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'] = 0;
        }


    //asigno al objeto los valores resultantes de importe gravado y no gravado.
    $this->importeNetoGravado['valor'] = $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpNeto'];
    $this->importeNetoNoGravado['valor'] = $FeCAEReq['FeDetReq']['FECAEDetRequest']['ImpTotConc'];

    //Array de campos auxiliares.
    $FeCAEReq['FeDetReq']['FECAEDetRequest']['Opcionales'] = $this->opcionalesArray;

    $resultado = $client->FECAESolicitar(array('Auth'=>$Auth, 'FeCAEReq'=>$FeCAEReq));
    //echo "<br>Request: ".$client->__getLastRequest();
    //echo "<br>Response: ".$client->__getLastResponse();
    if (is_soap_fault($resultado))
    {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

    //print_r ($resultado->FECAESolicitarResult);
    //die();
    $resultado = $this->obj2array($resultado->FECAESolicitarResult);

    //print_r($resultado);
    $this->cae['valor'] = $resultado['FeDetResp']['FECAEDetResponse']['CAE'];
    $this->fechaVencimientoCae['valor'] = $this->convertDateAfipToEs($resultado['FeDetResp']['FECAEDetResponse']['CAEFchVto']);

    if($resultado['FeCabResp']['Resultado']=='R') {
        $msg = 'Ocurrió un error al ejecutar la consulta. Contacte al Administrador. ';
        if ($resultado['Errors']['Err'] != NULL) {
            $this->result->setStatus(STATUS_ERROR);
            //este if es por si viene un msg o un array.
            if ($resultado['Errors']['Err']['Msg']){
                $msg .= '<br>OBS: '.$resultado['Errors']['Err']['Msg'];
            }
            else{
                foreach($resultado['Errors']['Err'] as $row)
                {
                    $msg .= '<br>ERROR: '.$row['Msg'];
                }
            }
        } else if ($resultado['FeDetResp']['FECAEDetResponse']['Observaciones']['Obs'] != NULL) {
            $this->result->setStatus(STATUS_ERROR);
            //este if es por si viene un msg o un array.
            if ($resultado['FeDetResp']['FECAEDetResponse']['Observaciones']['Obs']['Msg']){
                $msg .= '<br>OBS: '.$resultado['FeDetResp']['FECAEDetResponse']['Observaciones']['Obs']['Msg'];
            }
            else{
                foreach($resultado['FeDetResp']['FECAEDetResponse']['Observaciones']['Obs'] as $row)
                {
                    $msg .= '<br>OBS: '.$row['Msg'];
                }
            }
        }
        $this->result->setMessage($msg);
    }
    //print_r ($resultado);
    //die();
    return $resultado;
}

//función que consulta a la AFIP los datos de un comprobante ya cargado.
public function FECompConsultar($format = null){
    if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
    $datos = array(); //parametros de la llamada
    $datos['soap_version'] = "SOAP_1_2";
    $datos['location'] = URLFEV1;
    $datos['trace'] = 1;
    $datos['exceptions'] = 0;

    $client = new SoapClient(WSDLFEV1, $datos);

    $Auth = array(); //parametros de la llamada
    $Auth['Token'] = $this->token;
    $Auth['Sign'] = $this->sign;
    $Auth['Cuit'] = $this->cuit;

    //Información del comprobante que se desea consultar.
    $FeCompConsReq = array();
    $FeCompConsReq['CbteTipo'] = $this->idTipoComprobante['valor'];
    $FeCompConsReq['CbteNro'] = $this->nroComprobante['valor'];
    $FeCompConsReq['PtoVta'] = $this->puntoVenta['valor'];

    $resultado = $client->FECompConsultar(array('Auth'=>$Auth, 'FeCompConsReq'=>$FeCompConsReq));
    //echo "<br>Request: ".$client->__getLastRequest();
    //echo "<br>Response: ".$client->__getLastResponse();
    if (is_soap_fault($resultado))
    {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

    $resultado = $this->obj2array($resultado->FECompConsultarResult);
    $resultado = $resultado['ResultGet'];

    //asigno los valores devueltos por la AFIP al objeto.
    $this->puntoVenta['valor'] = $resultado['PtoVta'];
    $this->idTipoComprobante['valor'] = $resultado['CbteTipo'];
    $this->idTipoConcepto['valor'] = $resultado['Concepto'];
    $this->idTipoDocumento['valor'] = $resultado['DocTipo'];
    $this->nroDocumento['valor'] = $resultado['DocNro'];
    $this->idEstablecimiento['referencia']->cuit['valor'] = $resultado['DocNro'];
    $this->idEstablecimiento['referencia']->getRowById(array('nombreCampoWhere'=>'cuit', 'valorCampoWhere'=>$this->convertCuitAfipToDb($resultado['DocNro'])));
    $this->idEstablecimiento['valor'] = $this->idEstablecimiento['referencia']->idEstablecimiento['valor'];

    $this->nroComprobante['valor'] = $resultado['CbteDesde'];
    $resultado['CbteFch'] = $this->convertDateAfipToEs($resultado['CbteFch']);
    $this->fechaComprobante['valor'] = convertDateEsToDb($resultado['CbteFch']);
    $this->importeTotal['valor'] = $resultado['ImpTotal'];
    $this->importeNetoNoGravado['valor'] = $resultado['ImpTotConc'];
    $this->importeNetoGravado['valor'] = $resultado['ImpNeto'];
    $this->importeExento['valor'] = $resultado['ImpOpEx'];
    $this->importeTrib['valor'] = $resultado['ImpTrib'];
    $this->importeIva['valor'] = $resultado['ImpIVA'];
    $resultado['FchServDesde'] = $this->convertDateAfipToEs($resultado['FchServDesde']);
    $this->fechaServicioDesde['valor'] = convertDateEsToDb($resultado['FchServDesde']);
    $resultado['FchServHasta'] = $this->convertDateAfipToEs($resultado['FchServHasta']);
    $this->fechaServicioHasta['valor'] = convertDateEsToDb($resultado['FchServHasta']);
    $resultado['FchVtoPago'] = $this->convertDateAfipToEs($resultado['FchVtoPago']);
    $this->fechaVencimientoPago['valor'] = convertDateEsToDb($resultado['FchVtoPago']);
    $this->idTipoMoneda['referencia']->codAfipTipoMoneda['valor'] = $resultado['MonId'];
    $this->idTipoMoneda['referencia']->getRowById(array('nombreCampoWhere'=>'codAfipTipoMoneda', 'valorCampoWhere'=>$resultado['MonId']));
    $this->idTipoMoneda['valor'] = $this->idTipoMoneda['referencia']->idAfipTipoMoneda['valor'];
    $resultado['MonId'] = $this->idTipoMoneda['valor'];
    if($resultado['MonCotiz']==0){
        $resultado['MonCotiz']=1;
    }
    $this->cotizacionMoneda['valor'] = $resultado['MonCotiz'];
    $this->cae['valor'] = $resultado['CodAutorizacion'];
    $resultado['FchVto'] = $this->convertDateAfipToEs($resultado['FchVto']);
    $this->fechaVencimientoCae['valor'] = convertDateEsToDb($resultado['FchVto']);
    $this->comprobantesAsociadosArray = NULL;
    $this->tributosArray = NULL;

    //print_r ($resultado);
    $totalItem = 0;

    if ($resultado['Iva'])
    {
        //este IF es necesario dado que AFIP devuelve un array que contiene un array por cada tipo de IVA cargado,
        //pero si sólo es un tipo de IVA devuelve directamente el array con los datos, lo que hace que el foreach no funcione correctamente.
        if (isset ($resultado['Iva']['AlicIva'][0])){
            foreach ($resultado['Iva']['AlicIva'] as $row){
                $facturacionItem = new FacturacionItemVO();
                //$facturacionItem->item['valor'] = "Recuperado de AFIP";
                $facturacionItem->cantidad['valor'] = 1; //pongo por defecto 1 ya que AFIP no contempla esta info.
                $facturacionItem->precioUnitario['valor'] = $row['BaseImp'];
                $facturacionItem->porcentajeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
                $facturacionItem->importeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
                $facturacionItem->idTipoIva['valor'] =$row['Id'];
                $facturacionItem->importeIva['valor'] =$row['Importe'];
                $this->facturacionItemArray[] = $facturacionItem;
                //acumulo el importe total de items.
                $totalItem += $facturacionItem->precioUnitario['valor']+$facturacionItem->importeIva['valor'];
            }
        }else{
            $facturacionItem = new FacturacionItemVO();
            //$facturacionItem->item['valor'] = "Recuperado de AFIP";
            $facturacionItem->cantidad['valor'] = 1; //pongo por defecto 1 ya que AFIP no contempla esta info.
            $facturacionItem->precioUnitario['valor'] = $resultado['Iva']['AlicIva']['BaseImp'];
            $facturacionItem->porcentajeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
            $facturacionItem->importeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
            $facturacionItem->idTipoIva['valor'] =$resultado['Iva']['AlicIva']['Id'];
            $facturacionItem->importeIva['valor'] =$resultado['Iva']['AlicIva']['Importe'];
            $this->facturacionItemArray[] = $facturacionItem;
            //acumulo el importe total de items.
            $totalItem += $facturacionItem->precioUnitario['valor']+$facturacionItem->importeIva['valor'];
        }
    }

    //si la suma de los items no coincide con el total es porque hay un item con IVA 0 que no es incorporado en el array de IVA de la AFIP.
    //Agrego un item más para el IVA 0 con la diferencia entre ambos importes.
    if ($totalItem != $this->importeTotal['valor']){
        $facturacionItem = new FacturacionItemVO();
        //$facturacionItem->item['valor'] = "Recuperado de AFIP";
        $facturacionItem->cantidad['valor'] = 1; //pongo por defecto 1 ya que AFIP no contempla esta info.
        $facturacionItem->precioUnitario['valor'] = $this->importeTotal['valor']-$totalItem;
        $facturacionItem->porcentajeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
        $facturacionItem->importeBonificacion['valor'] = 0; //pongo por defecto 0 ya que AFIP no contempla esta info.
        $facturacionItem->idTipoIva['valor'] = 3; //IVA 0%
        $facturacionItem->importeIva['valor'] = 0;
        $this->facturacionItemArray[] = $facturacionItem;
    }

    $resultado['Iva'] = $this->facturacionItemArray;

    //Array de campos auxiliares.
    $this->opcionalesArray = $resultado['Opcionales'];

    if ($resultado['Errors']['Err']['Code']){
        $this->result->setStatus(STATUS_ERROR);
        $this->result->setData($resultado['Errors']['Err']['Msg']);
    }
    else{
        $this->result->setStatus(STATUS_OK);
        $msg = "Se incorporó al sistema el Comprobante Nro ".str_pad($this->puntoVenta['valor'], 4, "0", STR_PAD_LEFT)."-".str_pad($this->nroComprobante['valor'], 8, "0", STR_PAD_LEFT)." con CAE ".$resultado['ResultGet']['CodAutorizacion'];
        $this->result->setData($msg);
    }

    if($format == 'json') { // aunque no traiga nada debo devolver un array
        echo json_encode($resultado);
    }
    //print_r ($resultado);
    return $this;
}

//Función para consultar los tipos de comprobantes a la AFIP. Devuelve una tabla por pantalla.
public function FEParamGetTiposCbte()
{
    if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
    $datos=array(); //parametros de la llamada
    $datos['soap_version']="SOAP_1_2";
    $datos['location']=URLFEV1;
    $datos['trace']=1;
    $datos['exceptions']=0;
    $datos['encoding'] = 'ISO-8859-1';

    $client = new SoapClient(WSDLFEV1, $datos);

    $parametros=array(); //parametros de la llamada
    $parametros['Token']=$this->token;
    $parametros['Sign']=$this->sign;
    $parametros['Cuit']=$this->cuit;

    $resultado=$client->FEParamGetTiposCbte(array('Auth'=>$parametros));
    //echo "<br>Request: ".$client->__getLastRequest();
    //echo "<br>Response: ".$client->__getLastResponse();
    if (is_soap_fault($resultado))
    {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

    $resultado = obj2array($resultado);

    echo "<table>";
    echo "<tr><td colspan='4'>TIPOS DE COMPROBANTES</td></tr>";
    echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
    foreach ($resultado['FEParamGetTiposCbteResult']['ResultGet']['CbteTipo'] as $datos){
        echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
    }
    echo "</table>";
    return;
}

//Función para consultar los tipos de documentos a la AFIP. Devuelve una tabla por pantalla.
public function FEParamGetTiposDoc()
{
    if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
    $datos=array(); //parametros de la llamada
    $datos['soap_version']="SOAP_1_2";
    $datos['location']=URLFEV1;
    $datos['trace']=1;
    $datos['exceptions']=0;
    $datos['encoding'] = 'ISO-8859-1';

    $client = new SoapClient(WSDLFEV1, $datos);

    $parametros=array(); //parametros de la llamada
    $parametros['Token']=$this->token;
    $parametros['Sign']=$this->sign;
    $parametros['Cuit']=$this->cuit;

    $resultado=$client->FEParamGetTiposDoc(array('Auth'=>$parametros));
    //echo "<br>Request: ".$client->__getLastRequest();
    //echo "<br>Response: ".$client->__getLastResponse();
    if (is_soap_fault($resultado))
    {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

    $resultado = obj2array($resultado);

    echo "<table>";
    echo "<tr><td colspan='4'>TIPOS DE DOCUMENTOS</td></tr>";
    echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
    foreach ($resultado['FEParamGetTiposDocResult']['ResultGet']['DocTipo'] as $datos){
        echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
    }
    echo "</table>";
    return;
}

//Función para consultar los tipos de cotizaciones a la AFIP.
public function FEParamGetCotizacion(){
    //echo "Moneda: ".$this->idTipoMoneda['referencia']->codAfipTipoMoneda['valor'];
    $moneda = $this->idTipoMoneda['referencia']->codAfipTipoMoneda['valor'];
    if($moneda=='PES'){
        $this->cotizacionMoneda['valor'] = 1;
    }
    else{
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetCotizacion(array('Auth'=>$parametros, 'MonId'=>$moneda));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = $this->obj2array($resultado);

        $this->cotizacionMoneda['valor'] = $resultado['FEParamGetCotizacionResult']['ResultGet']['MonCotiz'];

        /*echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE COTIZACIONES</td></tr>";
        echo "<tr><td>ID</td><td>Cotizacion</td><td>Fecha</td></tr>";
        $datos = $resultado['FEParamGetCotizacionResult']['ResultGet'];
        echo "<tr><td>".$datos['MonId']."</td><td>".$datos['MonCotiz']."</td><td>".$datos['FchCotiz']."</td></tr>";
        //}
        echo "</table>";*/
        }

        return;
    }

    //Función para consultar los tipos de tributos a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposTributos()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetTiposTributos(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE TRIBUTOS</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
        foreach ($resultado['FEParamGetTiposTributosResult']['ResultGet']['TributoTipo'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //Función para consultar los tipos de monedas a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposMonedas()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetTiposMonedas(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = $this->obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE MONEDA</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
        foreach ($resultado['FEParamGetTiposMonedasResult']['ResultGet']['Moneda'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //Función para consultar los tipos de IVA a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposIva()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetTiposIva(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE IVA</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
        foreach ($resultado['FEParamGetTiposIvaResult']['ResultGet']['IvaTipo'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //Función para consultar los tipos de opcionales a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposOpcional()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;


        $resultado=$client->FEParamGetTiposOpcional(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE OPCIONAL</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
        foreach ($resultado['FEParamGetTiposOpcionalResult']['ResultGet']['OpcionalTipo'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //Función para consultar los tipos de concepto a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposConcepto()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetTiposConcepto(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE CONCEPTO</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td><td>Fecha Desde</td><td>Fecha Hasta</td></tr>";
        foreach ($resultado['FEParamGetTiposConceptoResult']['ResultGet']['ConceptoTipo'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td><td>".$datos['FchDesde']."</td><td>".$datos['FchHasta']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //Función para consultar los puntos de venta a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetPtosVenta()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetPtosVenta(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = $this->obj2array($resultado);

        /*echo "<table>";
        echo "<tr><td colspan='4'>PUNTOS DE VENTA</td></tr>";
        echo "<tr><td>Nro</td><td>Emision Tipo</td><td>Bloqueado</td><td>Fecha Baja</td></tr>";
        foreach ($resultado['FEParamGetPtosVentaResult']['ResultGet']['PtoVenta'] as $datos){
            echo "<tr><td>".$datos['Nro']."</td><td>".$datos['EmisionTipo']."</td><td>".$datos['Bloqueado']."</td><td>".$datos['FchBaja']."</td></tr>";
        }
        echo "</table>";*/
        return $resultado['FEParamGetPtosVentaResult']['ResultGet']['PtoVenta'];
    }

    //Función para consultar los tipos de países a la AFIP. Devuelve una tabla por pantalla.
    public function FEParamGetTiposPaises()
    {
        if (!file_exists(WSDLFEV1)) {exit("Failed to open ".WSDLFEV1."\n");}
        $datos=array(); //parametros de la llamada
        $datos['soap_version']="SOAP_1_2";
        $datos['location']=URLFEV1;
        $datos['trace']=1;
        $datos['exceptions']=0;
        $datos['encoding'] = 'ISO-8859-1';

        $client = new SoapClient(WSDLFEV1, $datos);

        $parametros=array(); //parametros de la llamada
        $parametros['Token']=$this->token;
        $parametros['Sign']=$this->sign;
        $parametros['Cuit']=$this->cuit;

        $resultado=$client->FEParamGetTiposPaises(array('Auth'=>$parametros));
        //echo "<br>Request: ".$client->__getLastRequest();
        //echo "<br>Response: ".$client->__getLastResponse();
        if (is_soap_fault($resultado))
        {exit("<br>SOAP Fault: ".$resultado->faultcode."\n".$resultado->faultstring."\n");}

        $resultado = obj2array($resultado);

        echo "<table>";
        echo "<tr><td colspan='4'>TIPOS DE PAÍSES</td></tr>";
        echo "<tr><td>ID</td><td>Desc.</td></tr>";
        foreach ($resultado['FEParamGetTiposPaisesResult']['ResultGet']['PaisTipo'] as $datos){
            echo "<tr><td>".$datos['Id']."</td><td>".$datos['Desc']."</td></tr>";
        }
        echo "</table>";
        return;
    }

    //funciones para el PDF
    //arma el nro del código de barras, generando el dígito verificador.
    public function getNroCodigoBarras(){
        $cadena = $this->cuit.str_pad($this->idTipoComprobante['valor'], 2, "0", STR_PAD_LEFT).str_pad($this->puntoVenta['valor'], 4, "0", STR_PAD_LEFT).$this->cae['valor'].str_replace('-', '', $this->fechaVencimientoCae['valor']);
        $largo = strlen ($cadena);
        $impar = 0;
        $par = 0;

        //calculo de digito verificador de la AFIP.
        //tengo que sumar todas las posiciones impares (toman que una cadena arranca en 1).
        for ($i=0;$i<$largo;$i=$i+2){
            $impar += $cadena[$i];
        }
        //multiplico los impares por 3.
        $impar = $impar * 3;
        //tengo que sumar todas las posiciones pares (toman que una cadena arranca en 1).
        for ($i=1;$i<$largo;$i=$i+2){
            $par += $cadena[$i];
        }
        //sumo pares e impares.
        $suma = $par + $impar;
        //Buscar el menor número que sumado al resultado obtenido en la etapa anterior dé un número múltiplo de 10
        $resto = $suma%10;
        if($resto == 0){
            $digito = $resto;
        }
        else {
            $digito = ((ceil($suma / 10)) * 10) - $suma;
        }
        $cadena .= $digito;
        return $cadena;

    }

    //genera el archivo del código de barras en una ubicación.
    public function getBarcode(){
        //generamos la cadena necesaria para el código de barras.
        $id = $this->getNroCodigoBarras();
        // Loading Font
        $font = new BCGFontFile(dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/barcode/font/Arial.ttf', 17);

        // The arguments are R, G, B for color.
        $color_black = new BCGColor(0, 0, 0);
        $color_white = new BCGColor(255, 255, 255);

        $drawException = null;
        try {
            $code = new BCGi25();
            $code->setScale(2); // Resolution
            $code->setThickness(45); // Thickness
            $code->setLabel($idserial);
            $label2t=$id;

            //agrega label2 - inicio
            $code->label2 = new BCGLabel($label2t, $font, BCGLabel::POSITION_BOTTOM, BCGLabel::ALIGN_CENTER);
            $code->label2->setSpacing(5);
            $code->addLabel($code->label2);
            // agrega label2 - fin
            $code->setForegroundColor($color_black); // Color of bars
            $code->setBackgroundColor($color_white); // Color of spaces
            //$code->setFont($font); // Font (or 0)
            $code->parse($id);
        } catch(Exception $exception) {
            $drawException = $exception;
        }
        //le paso una dirección para que genere el archivo para después poder usarlo.
        $drawing = new BCGDrawing(getFullPath().'/img/barcode.png', $color_white);

        if($drawException) {
            $drawing->drawException($drawException);
        } else {
            $drawing->setBarcode($code);
            $drawing->setRotationAngle(0);
            $drawing->draw();
        }
        //genera una imagen PNG
        //Header that says it is an image (remove it if you save the barcode to a file)
        header('Content-Type: image/png');

        // Draw (or save) the image into PNG format.
        $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    }

    public function getCodigoFacturacion(){
        $this->getBarcode();
        $aux = explode('/', convertDateDbToEs($this->fecha['valor']));
        return 'FE-'.$this->idZonaAfectacionSedeEmision['referencia']->sigla['valor'].substr($aux[2], -2).$aux[1].str_pad($this->puntoVenta['valor'], 4, '0', STR_PAD_LEFT).str_pad($this->nroComprobante['valor'], 8, '0', STR_PAD_LEFT);
    }

    function getFacturacionPDF($valor = 'ORIGINAL'){
        try {
            $htmlBody = $this->getPDF($valor);
            //print_r($htmlBody); die();
            //echo $result->getData();
            // documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
            // para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
            //$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
            $document = '<page backtop="10mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="V">';
            $document .= '<page_header>';
            //$informe .= '<br>';
            //$informe .= 'idprograma: '.$idPrograma;
            //$informe .= '<p align="center">header</p>';
            $document .= '</page_header>';
            $document .= '<page_footer>';
            $document .= '<p align="center">P&aacute;g. [[page_cu]]/[[page_nb]]</p>';
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

    public function getPDF($valor){
        $this->getRowById();

        $facturacionItemArray = new FacturacionItemVO();
        $data = null;
        $data['nombreCampoWhere'] = 'idFacturacion';
        $data['valorCampoWhere'] = $this->idFacturacion['valor'];
        $facturacionItemArray->getAllRows($data);

        $css = '<style>
					table {
						width: 100%;
					}
					td {
						vertical-align: top;
					}
					th {
						background-color: #cccccc;
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
        $html .= '  <table  cellspacing="0" border="0">
                	    <tr>
						    <td colspan="3" style="width: 100%; height: 25px; text-align: center; vertical-align: middle; font-size:17px; font-weight: bold; border: solid">'.$valor.'</td>
                        </tr>
                        <tr>
                            <td style="width:35%; border-right: solid; border-left: solid"></td>
                            <td style="text-align: center;  font-size:30px; font-weight: bold">'.substr($this->idTipoComprobante['referencia']->afipTipoComprobante['valor'], -1).'</td>
                            <td rowspan="2" style="width:35%; vertical-align:middle; border-left: solid; border-right: solid; font-size:22px; font-weight: bold">&nbsp;&nbsp;&nbsp;&nbsp;FACTURA</td>
                        </tr>
                        <tr>
                            <td style="border-right: solid; border-left: solid"></td>
                            <td style="text-align: center; font-size:11px; border-bottom: solid">COD. '.str_pad($this->idTipoComprobante['valor'], 2, "0", STR_PAD_LEFT).'</td>

                        </tr>
                    </table>
                    <table cellspacing="0" border="0">
						<tr>
							<td style="width: 50%; border-left: solid; border-bottom: solid; border-right: solid; font-size: 11px">
								<table>
									<tr>
										<!--<td width="168px"><img src="'.getFullPath().'/img/logo-sinec-168x108.jpg" alt="" /></td>-->
										<td>
											<table>
                                                <tr><td>&nbsp;</td></tr>
												<tr>
													<td><strong>Raz&oacute;n Social:</strong> SINEC S.A.</td>
												</tr>
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td><strong>Domicilio Comercial:</strong> French 3102, Ciudad de Buenos Aires</td>
												</tr>
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td><strong>Condici&oacute;n frente al IVA:</strong> IVA Responsable Inscripto</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td style="width: 50%; border-bottom: solid; border-right: solid; font-size: 11px">
								<table style="margin-left: 50px">
                                    <tr>
                                        <td><strong>Punto de Venta: '.str_pad($this->puntoVenta['valor'], 4, "0",STR_PAD_LEFT).' &nbsp; &nbsp; &nbsp; Comp. Nro: '.str_pad($this->nroComprobante['valor'], 8, "0", STR_PAD_LEFT).'</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha de Emisi&oacute;n: '.convertDateDbToEs($this->fechaComprobante['valor']).'</strong></td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td><strong>CUIT:</strong> 30711150591</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ingresos Brutos:</strong> 901-408490-8</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha de Inicio de Actividades:</strong> 01/10/2009</td>
                                    </tr>
                                </table>
							</td>
						</tr>
					</table>
					<table style="margin-top: 5px; border: solid; font-size: 11px">
					    <tr>
							<td style="width: 100%; text-align: center"><strong>Per&iacute;odo Facturado Desde:</strong> '.convertDateDbToEs($this->fechaServicioDesde['valor']).'   &nbsp;&nbsp;<strong>Hasta:</strong> '.convertDateDbToEs($this->fechaServicioHasta['valor']).'  &nbsp;&nbsp;<strong>Fecha de Vto. para el pago:</strong> '.convertDateDbToEs($this->fechaVencimientoPago['valor']).'</td>
						</tr>
                    </table>
                    <table style="margin-top: 5px; border: solid; font-size: 11px">
					    <tr>
							<td style="width: 50%">
								<table>
									<tr>
										<td>
											<table>
												<tr>
													<td><strong>CUIT:</strong> '.$this->nroDocumento['valor'].'</td>
												</tr>
												<tr>
													<td><strong>Condici&oacute;n frente al IVA:</strong> '.$this->idEstablecimiento['referencia']->idTipoSituacionFiscal['referencia']->tipoSituacionFiscal['valor'].'</td>
												</tr>
												<tr>
													<td><strong>Condici&oacute;n de Venta:</strong> '.$this->idTipoCondicionVenta['referencia']->afipTipoCondicionesVenta['valor'].'</td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</td>
							<td style="width: 50%">
								<table style="margin-left: 20px">
                                    <tr>
                                        <td><strong>Apellido y Nombre / Raz&oacute;n Social:</strong> '.$this->idEstablecimiento['referencia']->establecimiento['valor'].'</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 90%"><strong>Domicilio Comercial:</strong> '.$this->idEstablecimiento['referencia']->getDomicilioFiscalSucursal().'</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nro Remito:</strong> </td>
                                    </tr>
                                </table>
							</td>
						</tr>
                    </table>
                    <table style="margin-top: 5px; font-size: 11px" >
						<thead>
							<tr>
								<th style="border: solid;">Producto / Servicio</th>
								<th style="border: solid;">Cantidad</th>
								<th style="border: solid;">U. Medida</th>
								<th style="border: solid;">Precio Unit.</th>
								<th style="border: solid;">% Bonif</th>
								<th style="border: solid;">Subtotal</th>
								';

        if($this->idTipoComprobante['valor']==1 || $this->idTipoComprobante['valor']==6) {
            $html .= '
                                <th style="border: solid;">% IVA</th>
								<th style="border: solid;">Subtotal c/IVA</th>
                        ';
        }

        $html .= '
							</tr>
						</thead>
						<tbody>';
        $aux = 1;
        $total = 0;
        $totaliva3=0;
        $totaliva9=0;
        $totaliva8=0;
        $totaliva4=0;
        $totaliva5=0;
        $totaliva6=0;

        foreach($facturacionItemArray->result->getData() as $fei){
            $subTotal = $fei->cantidad['valor'] * $fei->precioUnitario['valor'];
            $subTotalBonif = $subTotal - $subTotal*$fei->porcentajeBonificacion['valor']/100;
            $subTotalIVA = $subTotalBonif + $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
            //print_r($fei); die();
            $html .= '<tr>';
            $html .= '<td style="width: 30%">';
            $html .= $fei->item['valor'];
            $html .= '</td>';
            $html .= '<td align="center">';
            $html .= $fei->cantidad['valor'];
            $html .= '</td>';
            $html .= '<td align="center">';
            $html .= 'unidades';
            $html .= '</td>';
            $html .= '<td align="right">';
            $html .= $fei->precioUnitario['valor'];
            $html .= '</td>';
            $html .= '<td align="right">';
            $html .= number_format($fei->porcentajeBonificacion['valor'], 2, ',', '.').' %';
            $html .= '</td>';
            $html .= '<td align="right">';
            $html .= number_format($subTotalBonif, 2, ',', '.');
            $html .= '</td>';

            if($this->idTipoComprobante['valor']==1 || $this->idTipoComprobante['valor']==6) {
                $html .= '<td align="right">';
                $html .= number_format($fei->idTipoIva['referencia']->porcentaje['valor'], 2, ',', '.').' %';
                $html .= '</td>';
                $html .= '<td align="right">';
                $html .= number_format($subTotalIVA, 2, ',', '.');
                $html .= '</td>';
            }

            $html .= '</tr>';
            $aux++;
            $total += $subTotalIVA;

            //acumulo los conceptos con igual IVA.
            switch ($fei->idTipoIva['valor']){
                case 3:
                    $totaliva3 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;
                case 9:
                    $totaliva9 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;
                case 8:
                    $totaliva8 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;
                case 4:
                    $totaliva4 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;
                case 5:
                    $totaliva5 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;
                case 6:
                    $totaliva6 += $subTotalBonif*$fei->idTipoIva['referencia']->porcentaje['valor']/100;
                    break;

            }
        }
        $enLetras = new EnLetras();
        $totalEnLetras = $enLetras->ValorEnLetras($total, $tipoMoneda);
        $html .= '				</tbody>
								<tfoot>
									<tr>
										<td colspan="8" style="width: 100%;">

										</td>
									</tr>
								</tfoot>
							</table>
							<page_footer>
							<table style="margin-top: 5px; margin-left: 38px; border: solid; font-size: 11px; font-weight: bold; width: 90%">
							    <thead>
									<tr>
										<th colspan="2" style="width: 100%;"></th>
									</tr>
								</thead>
								<tbody>';
        if($this->idTipoComprobante['valor']==1 || $this->idTipoComprobante['valor']==6) {
            $html .= '				<tr>
										<td style="width: 73%;" align="right">Importe Neto Gravado: $</td>
										<td style="width: 12%;" align="right">'.number_format(($this->importeTotal['valor']-$this->importeIva['valor']), 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 27%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva6, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 21%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva5, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 10.5%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva4, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 5%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva8, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 2.5%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva9, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">IVA 0%: $</td>
										<td style="width: 12%;" align="right">'.number_format($totaliva3, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">Importe Otros Tributos: $</td>
										<td style="width: 12%;" align="right">'.number_format(0, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">Importe Total: $</td>
										<td style="width: 12%;" align="right">'.number_format($this->importeTotal['valor'], 2, ",", ".").'</td>
									</tr>
									';

        }

        if($this->idTipoComprobante['valor']==6 || $this->idTipoComprobante['valor']==11) {
            $html .= '				<tr>
										<td style="width: 73%;" align="right">Subtotal: $</td>
										<td style="width: 12%;" align="right">'.number_format($total, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">Importe Otros Tributos: $</td>
										<td style="width: 12%;" align="right">'.number_format(0, 2, ",", ".").'</td>
									</tr>
									<tr>
										<td style="width: 73%;" align="right">Importe Total: $</td>
										<td style="width: 12%;" align="right">'.number_format($total, 2, ",", ".").'</td>
									</tr>


									';

        }


        $html .= '				</tbody>
							</table>';


        $html .= '

                            <table style="margin-left: 38px; width: 90%">
                                <tr>
                                    <td style="width: 50%">
                                        <img src="'.getFullPath().'/img/leyenda_AFIP.png" alt="" width="350"/>
                                        <img src="'.getFullPath().'/img/barcode.png" alt="" width="300" />
                                    </td>
                                    <td style="width: 37%">
                                        <table style="margin-top: 5px; font-size: 11px">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" style="width: 100%;"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr><td style=" font-size: 5px">&nbsp;</td></tr>
                                                <tr>
                                                    <td style="width: 60%;" align="right"><strong>CAE Nº: </strong></td>
                                                    <td style="width: 12%;" align="left">'.$this->cae['valor'].'</td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 60%;" align="right"><strong>Fecha de Vto. CAE: </strong></td>
                                                    <td style="width: 12%;" align="left">'.convertDateDbToEs($this->fechaVencimientoCae['valor']).'</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            </page_footer>
                ';

        return html_entity_decode($html, ENT_QUOTES | ENT_IGNORE, "UTF-8");
    }


    public function getReporte($data){
        try {
            $sql = 'select za.zonaAfectacion, e.establecimiento, atcv.afipTipoCondicionesVenta, atc.afipTipoComprobante, f.puntoVenta, f.nroComprobante, f.fechaComprobante, f.importeNetoGravado, f.importeNetoNoGravado, f.importeBonificacion, f.importeIva, f.importeTotal
                    from facturacion f
                    inner join afipTiposCondicionesVenta atcv on f.idTipoCondicionVenta = atcv.idAfipTipoCondicionesVenta
                    inner join afipTiposComprobante atc on f.idTipoComprobante = atc.idAfipTipoComprobante
                    inner join establecimientos e using (idEstablecimiento)
                    inner join zonasAfectacion za on f.idZonaAfectacionSedeEmision = za.idZonaAfectacion
                    where true ';
            $sql .= ' and fechaComprobante >= "'.convertDateEsToDb($data['fechaDesde']).'"';
            $sql .= ' and fechaComprobante <= "'.convertDateEsToDb($data['fechaHasta']).'"';
            $sql .= ' and f.idEstablecimiento = '.$data['idEstablecimiento'];
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

if($_GET['action'] == 'json' && $_GET['type'] == 'getNroComprobante'){
    $aux = new FacturacionVO();
    $data['puntoVenta'] = $_GET['puntoVenta'];
    $data['idTipoComprobante'] = $_GET['idTipoComprobante'];
    $aux->getNroComprobante($data, 'json');
}

if($_GET['action'] == 'json' && $_GET['type'] == 'getConsultarComprobante'){
    $aux = new FacturacionVO();
    $aux->puntoVenta['valor'] = $_GET['puntoVenta'];
    $aux->idTipoComprobante['valor'] = $_GET['idTipoComprobante'];
    $aux->nroComprobante['valor'] = $_GET['nroComprobante'];

    //llamo al método de AFIP que busca los datos ya cargados.
    $aux->solicitarPermisoWS();
    $aux->FECompConsultar('json');

}