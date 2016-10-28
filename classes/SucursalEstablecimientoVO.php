<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class SucursalEstablecimientoVO extends Master2 {
    public $idSucursalEstablecimiento = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "id",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE
        ],
    ];
	public $idEstablecimiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "establecimiento",
		"referencia" => "",
	];
    public $idProvincia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "provincia",
        "referencia" => "",
    ];
    public $idGmaps = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "establecimiento",
        "referencia" => "",
    ];
	public $sucursalEstablecimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "nombre de la sucursal",
    ];
    public $contacto = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "persona de contacto",
    ];
    public $telefono = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "teléfono",
    ];
    public $email = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "email",
        "nombre" => "email",
    ];
	public $piso = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "piso",
		"longitud" => "32"
	];
	public $depto = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "departamento",
		"longitud" => "32"
	];
    public $esDomicilioFiscal = ["valor" => FALSE,
        "obligatorio" => TRUE,
        "tipo" => "bool",
        "nombre" => "es domicilio fiscal",
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
    public $habilitado = ["valor" => TRUE,
        "obligatorio" => TRUE,
        "tipo" => "bool",
        "nombre" => "habilitado",
    ];

    public $sucursalEstablecimientoTipoEstablecimientoArray;
    //public $idGmaps;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('sucursalesEstablecimiento');
		$this->setFieldIdName('idSucursalEstablecimiento');
		$this->idEstablecimiento['referencia'] = new EstablecimientoVO();
		$this->idGmaps['referencia'] = new GmapsVO();
		$this->idProvincia['referencia'] = new ProvinciaVO();
	}
	
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        if($operacion != DELETE && !$this->sucursalEstablecimientoTipoEstablecimientoArray) {
            $resultMessage = 'Debe seleccionar al menos UN tipo de Establecimiento.';
        }
        return $resultMessage;
    }
    
    public function getDomicilioCompleto() {
        //return $this->domicilio['valor'] . ', '. $this->idLocalidad['referencia']->localidad['valor'] . ', '. $this->idProvincia['referencia']->provincia['valor'] .' - ' . $this->codigoPostal['valor'];
    }

    public function getDescripcionSucursalEstablecimiento(){
        $this->getRowById();
        //print_r($this);die();
        $sucursalEstablecimiento = $this->idEstablecimiento['referencia']->establecimiento['valor'];
        $sucursalEstablecimiento .= ' / '.$this->sucursalEstablecimiento['valor'];
        return $sucursalEstablecimiento;
    }

    /*
     * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
     */
    public function insertData(){
        //print_r($this); die('dos');
        try{
            //echo $this->idEstablecimiento['valor']; die();
	        //$gmaps = clone $this->idGmaps['referencia'];

            $this->conn->beginTransaction();
	        if($this->idGmaps['referencia']->street_address['valor']) {
		        $this->idGmaps['referencia']->insertData();
		        //print_r($this->idGmaps['referencia']);
		        if ($this->idGmaps['referencia']->result->getStatus() != STATUS_OK) {
			        $this->result = $this->idGmaps['referencia']->result;
			        $this->conn->rollBack();
			        return $this;
		        }
		        //print_r($this->idGmaps['referencia']); die();
		        $this->idGmaps['valor'] = $this->idGmaps['referencia']->idGmaps['valor'];
	        }
            parent::insertData();
            if($this->result->getStatus() != STATUS_OK) {
                $this->conn->rollBack();
                return $this;
            }

            //print_r($this); die('tres');
            //echo $this->idSucursalEstablecimiento['valor']; die();
            //print_r($this); die('dos');
            if($this->sucursalEstablecimientoTipoEstablecimientoArray) {
                //print_r($this->sucursalEstablecimientoTipoEstablecimientoArray); die('tres');
                foreach ($this->sucursalEstablecimientoTipoEstablecimientoArray as $sucursalEstablecimientoTipoEstablecimiento){
                    //print_r($sucursalEstablecimientoTipoEstablecimiento); die();
                    $sucursalEstablecimientoTipoEstablecimiento->idSucursalEstablecimiento['valor'] = $this->idSucursalEstablecimiento['valor'];
                    $sucursalEstablecimientoTipoEstablecimiento->insertData();
                    if($sucursalEstablecimientoTipoEstablecimiento->result->getStatus()  != STATUS_OK) {
                        //print_r($sucursalEstablecimientoTipoEstablecimiento); die('error uno');
                        $this->result = $sucursalEstablecimientoTipoEstablecimiento->result;
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
	        if($this->idGmaps['valor'])
	            $this->idGmaps['referencia']->updateData();
	        else
		        $this->idGmaps['referencia']->insertData();
	        //print_r($this->idGmaps['referencia']);
	        if($this->idGmaps['referencia']->result->getStatus() != STATUS_OK) {
		        $this->result =  $this->idGmaps['referencia']->result;
		        $this->conn->rollBack();
		        return $this;
	        }
	        //print_r($this->idGmaps['referencia']); die();
	        $this->idGmaps['valor'] = $this->idGmaps['referencia']->idGmaps['valor'];
	        //print_r($this); die();
            parent::updateData();
            if($this->result->getStatus() != STATUS_OK) {
                //print_r($this); die('error cero');
                $this->conn->rollBack();
                return $this;
            }

            //print_r($this); //die();
            $sctc = new SucursalEstablecimientoTipoEstablecimientoVO();
            $data = array();
            $data['nombreCampoWhere'] = 'idSucursalEstablecimiento';
            $data['valorCampoWhere'] = $this->idSucursalEstablecimiento['valor'];
            $sctc->deleteData($data);
            if($sctc->result->getStatus() != STATUS_OK) {
                //print_r($pm); die('error uno');
                $this->result = $sctc->result;
                $this->conn->rollBack();
                return $this;
            }
            if($this->sucursalEstablecimientoTipoEstablecimientoArray) {
                //print_r($this->sucursalEstablecimientoTipoEstablecimientoArray); //die();
                foreach ($this->sucursalEstablecimientoTipoEstablecimientoArray as $sucursalEstablecimientoTipoEstablecimiento){
                    //print_r($sucursalEstablecimientoTipoEstablecimiento); die();
                    $sucursalEstablecimientoTipoEstablecimiento->idSucursalEstablecimiento['valor'] = $this->idSucursalEstablecimiento['valor'];
                    $sucursalEstablecimientoTipoEstablecimiento->insertData();
                    if($sucursalEstablecimientoTipoEstablecimiento->result->getStatus()  != STATUS_OK) {
                        //print_r($sucursalEstablecimientoTipoEstablecimiento); die('error dos');
                        $this->result = $sucursalEstablecimientoTipoEstablecimiento->result;
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

    public function getAllRowsPrivate(){

    }

    public function getSucursalesEstablecimientoPorEstablecimiento($data, $format = null){
        $sql = 'select sc.idSucursalEstablecimiento as data, sucursalEstablecimiento as label
                from '.$this->getTableName().' as sc';
        if($data['idTipoEstablecimiento'])
            $sql .= ' inner join sucursalesEstablecimiento_tiposEstablecimiento as sctc on sctc.idSucursalEstablecimiento = sc.idSucursalEstablecimiento and sctc.idTipoEstablecimiento = '.$data['idTipoEstablecimiento'];
        $sql .= ' where true ';
        if($this->idEstablecimiento['valor'])
            $sql .= ' and sc.idEstablecimiento = '.$this->idEstablecimiento['valor'];
        if($this->habilitado['valor'])
            $sql .= ' and sc.habilitado';
        $sql .= ' order by sucursalEstablecimiento asc ';
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            //print_r($rs);
            $items = array();
            if($rs && count($rs) > 0) {
                if($format == 'json') {
                    foreach ($rs as $row) {
                        $items[] = array('id' => $row['data'], 'value' => $row['label']);
                    }
                    echo json_encode($items);
                    return;
                } else {
                    $this->result->setData($rs);
                }
            } else {
                if($format == 'json') { // aunque no traiga nada debo devolver un array
                    echo json_encode($items);
                }
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return ;
    }

	public function getSucursalesEstablecimiento(){
		$sql = 'select se.idSucursalEstablecimiento, se.sucursalEstablecimiento, p.idProvincia, p.provincia
				from sucursalesEstablecimiento as se
				inner join provincias as p using (idProvincia)
				where se.idEstablecimiento = '.$this->idEstablecimiento['valor'].'
				and se.habilitado = '.$this->habilitado['valor'].'
				order by se.sucursalEstablecimiento
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			//print_r($rs);
			$items = array();
			foreach ($rs as $row) {
				$items[] = array('idSucursalEstablecimiento' => $row['idSucursalEstablecimiento'],
					'sucursalEstablecimiento' => $row['sucursalEstablecimiento'],
					'idProvincia' => $row['idProvincia'],
					'provincia' => $row['provincia'],
				);
			}
			echo json_encode($items);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getSucursalesEstablecimientoPorEstablecimiento'){
    $aux = new SucursalEstablecimientoVO();
    $aux->habilitado['valor'] = $_GET['habilitado'];
    $aux->idEstablecimiento['valor'] = $_GET['idEstablecimiento'];
    $data['idTipoEstablecimiento'] = $_GET['idTipoEstablecimiento'];
    $aux->getSucursalesEstablecimientoPorEstablecimiento($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getSucursalesEstablecimiento'){
    $aux = new SucursalEstablecimientoVO();
    $aux->habilitado['valor'] = $_GET['habilitado'];
    $aux->idEstablecimiento['valor'] = $_GET['idEstablecimiento'];
    $aux->getSucursalesEstablecimiento();
}

if($_GET['debug'] == 'SucursalEstablecimiento' or false){
	echo "DEBUG<br>";
	$kk = new SucursalEstablecimientoVO();
	//print_r($kk->getAllRows());
	$kk->idCliente = 116;
	$kk->cliente = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>