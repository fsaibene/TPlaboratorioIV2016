<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
include_once('UsuarioVO.php');
include_once('TipoSituacionFiscalVO.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class EstablecimientoVO extends Master2 {
    public $idEstablecimiento = ["valor" => "",
       "obligatorio" => FALSE,
       "tipo" => "integer",
       "nombre" => "id",
       "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => FALSE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $establecimiento = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "establecimiento / empresa",
    ];
    public $idTipoSituacionFiscal = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "tipo situación fiscal",
        "referencia" => "",
    ];
    public $cuit = ["valor" => "",
        "obligatorio" => true,
        "tipo" => "cuit",
        "nombre" => "cuit",
    ];
    public $web = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "página web",
    ];
    public $telefono = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "teléfono",
    ];
    public $contacto = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "contacto",
    ];
    public $email = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "email",
        "nombre" => "email",
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
    public $esCliente = ["valor" => FALSE,
        "obligatorio" => FALSE,
        "tipo" => "bool",
        "nombre" => "es cliente",
    ];
    public $codigoCliente = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "código de cliente",
    ];
    public $esProveedor = ["valor" => FALSE,
        "obligatorio" => FALSE,
        "tipo" => "bool",
        "nombre" => "es proveedor",
    ];
    public $habilitado = ["valor" => TRUE,
        "obligatorio" => TRUE,
        "tipo" => "bool",
        "nombre" => "habilitado",
    ];

    public $establecimientoTipoEstablecimientoArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('establecimientos');
		$this->setFieldIdName('idEstablecimiento');
		$this->idTipoSituacionFiscal['referencia'] = new TipoSituacionFiscalVO();
	}

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        if($this->esCliente['valor'] == '1'){
            $this->codigoCliente['obligatorio'] = TRUE;
        }else{
            $this->codigoCliente['obligatorio'] = FALSE;
            $this->codigoCliente['valor'] = null;
        }
        if($operacion != DELETE && !$this->establecimientoTipoEstablecimientoArray) {
            $resultMessage = 'Debe seleccionar al menos UN tipo de Establecimiento.';
        }
        return $resultMessage;
    }

    /*
     * por un tema de recursividad no puedo llamar a esta funcion getRowById
     */
    /*public function getRowById2() {
        /*$data['nombreCampoWhere'] = 'idUsuario';
        $data['valorCampoWhere'] = $_SESSION['usuarioLogueadoIdUsuario'];
        parent::getRowById($data);*/
        /*parent::getRowById();
        return $this;
    }*/

    public function getComboList($data = null){
	    $sql = 'select CONCAT(e.establecimiento, " [", e.cuit, "] ", te.tipoEstablecimiento) as label, e.idEstablecimiento as data
				from establecimientos as e
				INNER JOIN establecimientos_tiposEstablecimiento as ete using (idEstablecimiento)
				inner join tiposEstablecimiento as te using (idTipoEstablecimiento)
				where true ';
	    if($data)
		    $sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
	    $sql .= ' group by e.cuit ';
	    $sql .= ' order by e.establecimiento ';
	    //die($sql);
	    try{
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

	public function getComboListParaProyectos($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select e.idEstablecimiento as data, CONCAT(e.establecimiento, " [", e.cuit, "] ", te.tipoEstablecimiento) as label
                    from establecimientos as e
                    INNER JOIN establecimientos_tiposEstablecimiento as ete using (idEstablecimiento)
                    inner join tiposEstablecimiento as te using (idTipoEstablecimiento)';
			$sql .= ' where e.habilitado and esCliente';
			$sql .= ' group by e.cuit ';
			$sql .= ' order by e.establecimiento ';
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
     * sobreescribo el metodo porque hay que hacer una magia para poder insertar en la tabla de muchos a muchos.
     */
    public function insertData(){
        //print_r($this); die('dos');
        try{
            //echo $this->idEstablecimiento['valor']; die();
            $this->conn->beginTransaction();
            parent::insertData();
            if($this->result->getStatus() != STATUS_OK) {
                $this->conn->rollBack();
                return $this;
            }
            //echo $this->idEstablecimiento['valor']; die();
            //print_r($this); die('dos');
            if($this->establecimientoTipoEstablecimientoArray) {
                //print_r($this->establecimientoTipoEstablecimientoArray); die('tres');
                foreach ($this->establecimientoTipoEstablecimientoArray as $establecimientoTipoEstablecimiento){
                    //print_r($establecimientoTipoEstablecimiento); die();
                    $establecimientoTipoEstablecimiento->idEstablecimiento['valor'] = $this->idEstablecimiento['valor'];
                    $establecimientoTipoEstablecimiento->insertData();
                    if($establecimientoTipoEstablecimiento->result->getStatus()  != STATUS_OK) {
                        //print_r($establecimientoTipoEstablecimiento); die('error uno');
                        $this->result = $establecimientoTipoEstablecimiento->result;
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
            $ctc = new EstablecimientoTipoEstablecimientoVO();
            $data = array();
            $data['nombreCampoWhere'] = 'idEstablecimiento';
            $data['valorCampoWhere'] = $this->idEstablecimiento['valor'];
            $ctc->deleteData($data);
            if($ctc->result->getStatus() != STATUS_OK) {
                //print_r($pm); die('error uno');
                $this->result = $ctc->result;
                $this->conn->rollBack();
                return $this;
            }
            if($this->establecimientoTipoEstablecimientoArray) {
                //print_r($this->reciboArticulosArray); //die();
                foreach ($this->establecimientoTipoEstablecimientoArray as $establecimientoTipoEstablecimiento){
                    //print_r($establecimientoTipoEstablecimiento); die();
                    $establecimientoTipoEstablecimiento->idEstablecimiento['valor'] = $this->idEstablecimiento['valor'];
                    $establecimientoTipoEstablecimiento->insertData();
                    if($establecimientoTipoEstablecimiento->result->getStatus()  != STATUS_OK) {
                        //print_r($establecimientoTipoEstablecimiento); die('error dos');
                        $this->result = $establecimientoTipoEstablecimiento->result;
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

    public function getEstablecimientos($data, $format = null){
        $sql = 'select CONCAT(e.establecimiento, " [", e.cuit, "] ", te.tipoEstablecimiento) as label, e.idEstablecimiento as data
                from '.$this->getTableName().' as e
                inner join establecimientos_tiposEstablecimiento as ctc on ctc.idEstablecimiento = e.idEstablecimiento ';
        if($data['idTipoEstablecimiento'])
            $sql .= '  and ctc.idTipoEstablecimiento = '.$data['idTipoEstablecimiento'];
        $sql .= ' inner join tiposEstablecimiento as te using (idTipoEstablecimiento) ';
        $sql .= ' where true ';
        if($this->habilitado['valor'])
            $sql .= ' and e.habilitado';
        //$sql .= ' group by data ';
        $sql .= ' order by e.establecimiento ';
//        echo $sql;
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
	                echo json_encode(array_map('setHtmlEntityDecode', $items));
                    return;
                } else {
                    $this->result->setData($rs);
                }
            } else {
                if($format == 'json') { // aunque no traiga nada debo devolver un array
	                echo json_encode(array_map('setHtmlEntityDecode', $items));
                }
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }

    public function getEstablecimientosClientesProveedores($data = null, $format = null){
        $sql = 'select CONCAT(e.establecimiento, " [", e.cuit, "] ", te.tipoEstablecimiento) as label, e.idEstablecimiento as data
                from '.$this->getTableName().' as e
                inner join establecimientos_tiposEstablecimiento as ctc on ctc.idEstablecimiento = e.idEstablecimiento ';
        if($data['idTipoEstablecimiento'])
            $sql .= '  and ctc.idTipoEstablecimiento = '.$data['idTipoEstablecimiento'];
	    $sql .= ' inner join tiposEstablecimiento as te using (idTipoEstablecimiento)
                where true ';
        if($this->habilitado['valor'])
            $sql .= ' and e.habilitado';
        if($this->esCliente['valor'])
            $sql .= ' and e.esCliente';
        if($this->esProveedor['valor'])
            $sql .= ' and e.esProveedor';
        $sql .= ' order by e.establecimiento ';
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
	                echo json_encode(array_map('setHtmlEntityDecode', $items));
                    return;
                } else {
                    $this->result->setData($rs);
                }
            } else {
                if($format == 'json') { // aunque no traiga nada debo devolver un array
	                echo json_encode(array_map('setHtmlEntityDecode', $items));
                }
            }
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return $this;
    }

    public function getEstablecimientoPorCUIT(){
	    $sql = 'select e.idEstablecimiento, e.establecimiento, tsf.idTipoSituacionFiscal, tsf.tipoSituacionFiscal
				from establecimientos as e
				inner join tiposSituacionFiscal as tsf using (idTipoSituacionFiscal)
				where e.cuit = "'.$this->cuit['valor'].'"
				and e.habilitado = '.$this->habilitado['valor'].'
				order by e.establecimiento
                ';
	    //die($sql);
	    try {
		    $ro = $this->conn->prepare($sql);
		    $ro->execute();
		    $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
		    //print_r($rs);
		    $items = array();
		    foreach ($rs as $row) {
	            $items[] = array('idEstablecimiento' => $row['idEstablecimiento'],
		                        'establecimiento' => $row['establecimiento'],
		                        'idTipoSituacionFiscal' => $row['idTipoSituacionFiscal'],
		                        'tipoSituacionFiscal' => $row['tipoSituacionFiscal'],
	                        );
            }
		    echo json_encode(array_map('setHtmlEntityDecode', $items));
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
            myExceptionHandler($e);
        }
        return;
    }

    public function getDomicilioFiscalSucursal(){
        $sql = "select  concat(g.route, ' ', g.street_number, ' - ', g.locality, ', ', g.administrative_area_level_1) as domicilioFiscal
                from establecimientos e
                Inner join sucursalesEstablecimiento se using (idEstablecimiento)
                inner join gmaps g	using (idGmaps)
                where se.esDomicilioFiscal and e.idEstablecimiento  = ".$this->idEstablecimiento['valor'];
        //echo($sql);
        try{
            $ro = $this->conn->prepare($sql);
            if($ro->execute()){
                $rs = $ro->fetch(PDO::FETCH_ASSOC);
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
        return $rs['domicilioFiscal'];
    }

}
if($_GET['action'] == 'json' && $_GET['type'] == 'getEstablecimientos'){
    $aux = new EstablecimientoVO();
    $aux->habilitado['valor'] = $_GET['habilitado'];
    $data['idTipoEstablecimiento'] = $_GET['idTipoEstablecimiento'];
    $aux->getEstablecimientos($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getEstablecimientosPorTipoMovimientoEquipamiento'){
    $aux = new EstablecimientoVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	if($_GET['idTipoMovimientoEquipamiento'] == 1) { // COMISIONES
		$data['idTipoEstablecimiento'] = 13;
	} else if($_GET['idTipoMovimientoEquipamiento'] == 2) { // CALIBRACION
		$data['idTipoEstablecimiento'] = 9;
	} else if($_GET['idTipoMovimientoEquipamiento'] == 3) { // OTRO

	}
    $aux->getEstablecimientos($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getEstablecimientosClientesProveedores'){
    $aux = new EstablecimientoVO();
    $aux->habilitado['valor'] = $_GET['habilitado'];
    $aux->esProveedor['valor'] = $_GET['esProveedor'];
    $aux->esCliente['valor'] = $_GET['esCliente'];
	$data['idTipoEstablecimiento'] = $_GET['idTipoEstablecimiento'];
    $aux->getEstablecimientosClientesProveedores($data, 'json');
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getEstablecimientoPorCUIT'){
    $aux = new EstablecimientoVO();
	$aux->cuit['valor'] = $_GET['cuit'];
	$aux->habilitado['valor'] = $_GET['habilitado'];
    $aux->getEstablecimientoPorCUIT();
}

if($_GET['debug'] == 'EstablecimientoVO' or FALSE){
	echo "DEBUG<br>";
	$kk = new EstablecimientoVO();
	//print_r($kk);
	$kk->idEstablecimiento['valor'] = 1;
	//$kk->establecimiento = 'hhh2';
	//print_r($kk->getRowById());
	print_r($kk->getAllRows());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
?>