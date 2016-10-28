<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajeVO extends Master2 {
	public $idViaje = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $nroViaje = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "integer",
		"nombre" => "Nro. de viaje",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $idDestino = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "provincia \ destino",
		"referencia" => "",
	];
	public $idLaborActividad = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => " Área de aplicación \ Actividad",
						"referencia" => "",
	];
	public $fechaInicio = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha inicio",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $fechaFin = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "date",
		"nombre" => "fecha fin",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "observaciones",
					];

	public $idViajeEmpleado = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeEmpleado",
		"referencia" => "",
	];
	public $idViajeVehiculo = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeVehiculo",
		"referencia" => "",
	];
	public $idViajeEquipamiento = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeEquipamiento",
		"referencia" => "",
	];
	public $idViajeEmpleadoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeEmpleadoDetalle",
		"referencia" => "",
	];
	public $idViajeVehiculoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeVehiculoDetalle",
		"referencia" => "",
	];
	public $idViajeEquipamientoDetalle = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "idViajeEquipamientoDetalle",
		"referencia" => "",
	];
	public $viajeEmpleadoArray;
	public $viajeVehiculoArray;
	public $viajeEquipamientoArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('viajes');
		$this->setFieldIdName('idViaje');
		$this->idDestino['referencia'] = new DestinoVO();
		$this->idLaborActividad['referencia'] = new LaborActividadVO();
		$this->excluirAtributo('idViajeEmpleado');
		$this->excluirAtributo('idViajeVehiculo');
		$this->excluirAtributo('idViajeEquipamiento');
		$this->excluirAtributo('idViajeEmpleadoDetalle');
		$this->excluirAtributo('idViajeVehiculoDetalle');
		$this->excluirAtributo('idViajeEquipamientoDetalle');
		$this->getNroViaje();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if (strtotime(convertDateEsToDb($this->fechaInicio['valor'])) > strtotime(convertDateEsToDb($this->fechaFin['valor'])) ) {
			$resultMessage = 'La fecha Fin no puede ser menor que la fecha Inicio.';
		}
		if ($this->nroViaje['valor'] > 9999 ) {
			$resultMessage = 'El Nro. de guía de ruta no puede ser mayor que 9999.';
		}
        return $resultMessage;
 	}

	public function getNroViaje(){
		$sql = "select max(nroViaje) as nroViaje from ".$this->getTableName();
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			if(count($rs) == 1){
				$this->nroViaje['valor'] = $rs[0]['nroViaje'] + 1;
			} else {
				$this->nroViaje['valor'] = 1;
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	public function getCodigoViaje(){
		//$aux = explode('/', convertDateDbToEs($this->fecha['valor']));
		$codigo = 'VPI';
		$codigo .= '-'.str_pad($this->nroViaje['valor'], 4, '0', STR_PAD_LEFT);
		return $codigo;
	}

	public function getAllRows2($data = null){
		$sql = 'select a.*, saa.laborAreaDeAplicacion, sa.laborActividad, l.destino, p.provincia, getCodigoViaje(a.idViaje) as codigoViaje
				from viajes as a
				inner join destinos as l using (idDestino)
				inner join provincias as p using (idProvincia)
				inner join laboresActividad as sa using (idLaborActividad)
				inner join laboresAreaDeAplicacion as saa using (idLaborAreaDeAplicacion)
				where true ';
		if($data['valorCampoWhere']) {
			$sql .= ' and c.'.$data['nombreCampoWhere'].' = ' . $data['valorCampoWhere'];
		}
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$this->result->setData($rs);
				//print_r($this); die();
			} else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("ERROR, contacte al administrador.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	/*
	 * se armó esta función para encapsular el insert y el update que viene desde el form de ABMviajes.php
	 * se quería devolver un solo result para simpleificar el manejo.
	 */
	public function insertDataRecursos($post){
		try{
			$this->conn->beginTransaction();

			// EMPLEADOS
			$this->idViajeEmpleado['referencia']->deleteDataArray($post['idViaje'], $post['viajeEmpleadoArray']);
			$this->result = $this->idViajeEmpleado['referencia']->result;
			if($this->idViajeEmpleado['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['viajeEmpleadoArray']){
				$this->idViajeEmpleado['referencia']->insertDataArray($post['idViaje'], $post['viajeEmpleadoArray']);
				$this->result = $this->idViajeEmpleado['referencia']->result;
				if($this->idViajeEmpleado['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pcemd';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idViajeEmpleadoDetalle['referencia']->{$postName})) {
						if ($this->idViajeEmpleadoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idViajeEmpleadoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idViajeEmpleadoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idViajeEmpleadoDetalle['referencia']->idViaje['valor'] = $_POST['idViaje'];
			//print_r($this->idViajeEmpleadoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idViajeEmpleadoDetalle['referencia'];
			if ($this->idViajeEmpleadoDetalle['referencia']->{$this->idViajeEmpleadoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idViajeEmpleadoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idViajeEmpleadoDetalle['referencia']->insertData();
			}
			$this->result = $this->idViajeEmpleadoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idViajeEmpleadoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idViajeEmpleadoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			// VEHICULOS
			$this->idViajeVehiculo['referencia']->deleteDataArray($post['idViaje'], $post['viajeVehiculoArray']);
			$this->result = $this->idViajeVehiculo['referencia']->result;
			if($this->idViajeVehiculo['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['viajeVehiculoArray']){
				$this->idViajeVehiculo['referencia']->insertDataArray($post['idViaje'], $post['viajeVehiculoArray']);
				$this->result = $this->idViajeVehiculo['referencia']->result;
				if($this->idViajeVehiculo['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pcved';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idViajeVehiculoDetalle['referencia']->{$postName})) {
						if ($this->idViajeVehiculoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idViajeVehiculoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idViajeVehiculoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idViajeVehiculoDetalle['referencia']->idViaje['valor'] = $_POST['idViaje'];
			//print_r($this->idViajeVehiculoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idViajeVehiculoDetalle['referencia'];
			if ($this->idViajeVehiculoDetalle['referencia']->{$this->idViajeVehiculoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idViajeVehiculoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idViajeVehiculoDetalle['referencia']->insertData();
			}
			$this->result = $this->idViajeVehiculoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idViajeVehiculoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idViajeVehiculoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			// EQUIPAMIENTOS
			$this->idViajeEquipamiento['referencia']->deleteDataArray($post['idViaje'], $post['viajeEquipamientoArray']);
			$this->result = $this->idViajeEquipamiento['referencia']->result;
			if($this->idViajeEquipamiento['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->conn->rollBack();
				return $this;
			}
			if($post['viajeEquipamientoArray']){
				$this->idViajeEquipamiento['referencia']->insertDataArray($post['idViaje'], $post['viajeEquipamientoArray']);
				$this->result = $this->idViajeEquipamiento['referencia']->result;
				if($this->idViajeEquipamiento['referencia']->result->getStatus() != STATUS_OK) {
					//print_r($aux); die('error uno');
					$this->conn->rollBack();
					return $this;
				}
			}
			$key = 'pceqd';
			foreach ($post as $postName => $postValue) {
				if(substr($postName, 0, strlen($key)) == $key){
					$postName = substr($postName, strlen($key)+1);
					if (isset($this->idViajeEquipamientoDetalle['referencia']->{$postName})) {
						if ($this->idViajeEquipamientoDetalle['referencia']->{$postName}['tipo'] == 'combo' && $postValue == '__jc__') { // ESTO ES POR UN BUG DEL SCRIPT DE COMBOS ANIDADOS
							$this->idViajeEquipamientoDetalle['referencia']->{$postName}['valor'] = NULL;
						} else {
							$this->idViajeEquipamientoDetalle['referencia']->{$postName}['valor'] = $postValue;
						}
					}
				}
			}
			$this->idViajeEquipamientoDetalle['referencia']->idViaje['valor'] = $_POST['idViaje'];
			//print_r($this->idViajeEquipamientoDetalle['referencia']); die();
			$backupObjectVO = clone $this->idViajeEquipamientoDetalle['referencia'];
			if ($this->idViajeEquipamientoDetalle['referencia']->{$this->idViajeEquipamientoDetalle['referencia']->getFieldIdName()}['valor']) {  // UPDATE
				$this->idViajeEquipamientoDetalle['referencia']->updateData();
			} else {                                                                                                                                            // INSERT
				$this->idViajeEquipamientoDetalle['referencia']->insertData();
			}
			$this->result = $this->idViajeEquipamientoDetalle['referencia']->result;
			//print_r($result); die();
			if ($this->idViajeEquipamientoDetalle['referencia']->result->getStatus() != STATUS_OK) {
				//print_r($aux); die('error uno');
				$this->idViajeEquipamientoDetalle['referencia'] = $backupObjectVO;
				$this->conn->rollBack();
				return $this;
			}

			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

	public function getComboList2($data){
		try{
			$sql = 'select CONCAT("VPI-", pc.nroViaje, " F.inicio:", pc.fechaInicio, " F.fin: ", pc.fechaFin) as label, pce.idViajeEmpleado as data
					from viajes as pc
					inner join viajes_empleados as pce using (idViaje)
					where pce.idEmpleado = '.$data['idEmpleado'].'
					and pce.idViajeEmpleado not in (
						select idViajeEmpleado
						from rendicionesViatico
						where idEmpleado = '.$data['idEmpleado'].' and idViajeEmpleado is not null';
			if($data['idViajeEmpleado']) {
				$sql .= ' and idViajeEmpleado != ' . $data['idViajeEmpleado'];
			}
			$sql .= ' )
					group by idViaje
					order by pc.fechaInicio';
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
	 * tengo que cambiar el nombre de la duncion por un problema de recursividad
	 */
	public function getRowById2($data = null){
		parent::getRowById();
		if($this->result->getStatus() == STATUS_OK) {
			// EMPLEADOS
			$this->idViajeEmpleado['referencia'] = new ViajeEmpleadoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeEmpleado['referencia']->getAllRows($data);
			//$result = $viajeEmpleado->result;
			if($this->idViajeEmpleado['referencia']->result->getStatus() == STATUS_OK) {
				//$this->viajeEmpleadoArray = $this->idViajeEmpleado['referencia']->result->getData();
			}
			//print_r($this->viajeEmpleadoArray);die();
			$this->idViajeEmpleadoDetalle['referencia'] = new ViajeEmpleadoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeEmpleadoDetalle['referencia']->getRowById($data);
			//$this->idViajeEmpleadoDetalle['referencia'] = $viajeEmpleadoDetalle;
			$this->idViajeEmpleadoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idViajeEmpleadoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...

			// VEHICULOS
			$this->idViajeVehiculo['referencia'] = new ViajeVehiculoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeVehiculo['referencia']->getAllRows($data);
			//$result = $viajeVehiculo->result;
			if($this->idViajeVehiculo['referencia']->result->getStatus() == STATUS_OK) {
				//$this->viajeVehiculoArray = $viajeVehiculo->result->getData();
			}
			//print_r($this->viajeVehiculoArray);die();
			$this->idViajeVehiculoDetalle['referencia'] = new ViajeVehiculoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeVehiculoDetalle['referencia']->getRowById($data);
			//$this->idViajeVehiculoDetalle['referencia'] = $viajeVehiculoDetalle;
			$this->idViajeVehiculoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idViajeVehiculoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...

			// EQUIPAMIENTOS
			$this->idViajeEquipamiento['referencia'] = new ViajeEquipamientoVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeEquipamiento['referencia']->getAllRows($data);
			//$result = $viajeEquipamiento->result;
			if($this->idViajeEquipamiento['referencia']->result->getStatus() == STATUS_OK) {
				//$this->viajeEquipamientoArray = $viajeEquipamiento->result->getData();
			}
			//print_r($this->viajeEquipamientoArray);die();
			$this->idViajeEquipamientoDetalle['referencia'] = new ViajeEquipamientoDetalleVO();
			$data[] = null;
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$this->idViajeEquipamientoDetalle['referencia']->getRowById($data);
			//$this->idViajeEquipamientoDetalle['referencia'] = $viajeEquipamientoDetalle;
			$this->idViajeEquipamientoDetalle['referencia']->result->setStatus(STATUS_OK); // debo hacer esto porque sino pincha el insert luego...
			$this->idViajeEquipamientoDetalle['referencia']->result->setMessage(''); // debo hacer esto porque sino pincha el insert luego...
		}
		return $this;
	}

	/*
	 * devuelve un array con la info para ser mostrada luego en un calendario
	 */
	public function getViajesParaCalendario($data){
		//fc_print($data);
		$sql = 'SELECT
					concat("viaje-", idViaje) as id
					, "#337AB7" as color
					, cgl.destino as title
					, pc.fechaInicio as start
					, DATE_ADD(pc.fechaFin, interval 1 day) as end
					, true as allDay
					, concat_ws("<br>"
						, getCodigoViaje(idViaje)
						, concat("Destino: ", cgl.destino, " (", p.provincia, ")")
						, concat("Actividad: ", sada.laborAreaDeAplicacion, " - ", sa.laborActividad)
						, concat("Integrantes: ", pce2.integrantes)
						, concat("Equipamientos: ", pceq2.equipamientos)
						, concat("Vehículos: ", pcv2.vehiculos)
						, if(pc.observaciones is null, "", concat("Obs: ", pc.observaciones))
						) as description ';
		if(!$data['print']){ // si es para imprimir no puedo mandar el campo url porque pincha...
			$sql .= ' , "ViajePDF.php?" as urlAddress, concat("id=", idViaje) as urlParamaters, null as url ';
		}
		$sql .= ' FROM viajes as pc
					left join viajes_empleados as pce using (idViaje)
					left join viajes_equipamientos as pceq using (idViaje)
					left join viajes_vehiculos as pcv using (idViaje)
					inner join destinos as cgl using (idDestino)
					inner join provincias as p using (idProvincia)
					inner join laboresActividad as sa using (idLaborActividad)
					inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
					left join (
						select idViaje, GROUP_CONCAT(getEmpleado(idEmpleado) SEPARATOR " - ") as integrantes
						from viajes_empleados as pce
						inner join empleados as e using (idEmpleado)
						group by idViaje
					) as pce2 using (idViaje)
					left join (
						select idViaje, GROUP_CONCAT(getEquipamiento(idEquipamiento) SEPARATOR " - ") as equipamientos
						from viajes_equipamientos as pceq
						inner join equipamientos as e using (idEquipamiento)
						group by idViaje
					) as pceq2 using (idViaje)
					left join (
						select idViaje, GROUP_CONCAT(getVehiculo(idVehiculo) SEPARATOR " - ") as vehiculos
						from viajes_vehiculos as pcv
						inner join vehiculos as v using (idVehiculo)
						group by idViaje
					) as pcv2 using (idViaje)
					where true ';
		if($data['idLaborActividad']){
			$sql .= ' and sa.idLaborActividad = '.$data['idLaborActividad'];
		}
		if($data['idLaborAreaDeAplicacion']){
			$sql .= ' and sada.idLaborAreaDeAplicacion = '.$data['idLaborAreaDeAplicacion'];
		}
		if($data['idEmpleadoArray'][0]){
			$sql .= ' and pce.idEmpleado in ('.implode(",", $data['idEmpleadoArray']).')';
		}
		if($data['idEquipamientoArray'][0]){
			$sql .= ' and pceq.idEquipamiento in ('.implode(",", $data['idEquipamientoArray']).')';
		}
		if($data['idVehiculoArray'][0]){
			$sql .= ' and pcv.idVehiculo in ('.implode(",", $data['idVehiculoArray']).')';
		}
		$sql .= ' and ((pc.fechaInicio BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'") 
					or (pc.fechaFin BETWEEN "'.convertDateEsToDb($data['start']).'" and "'.convertDateEsToDb($data['end']).'")
					or (pc.fechaInicio < "'.convertDateEsToDb($data['start']).'" and pc.fechaFin > "'.convertDateEsToDb($data['end']).'")) ';
		$sql .= ' GROUP BY id';
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC));
			foreach ($rs as &$aux){
				if($aux['urlAddress']){
					$aux['url'] = $aux['urlAddress'] . codificarGets($aux['urlParamaters']);
					$aux['urlParamaters'] = null;
				}
			}
			$this->result->setData($rs);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $this;
	}

	/*
	 * devuelve datos de la comision
	 */
	public function getViajePorIdViajeEmpleado($data){
		$sql = 'select idViaje, nroViaje, DATE_FORMAT(pc.fechaInicio,"%d/%m/%Y") as fechaInicio, DATE_FORMAT(pc.fechaFin,"%d/%m/%Y") as fechaFin,
				idViajeEmpleado, 
				getCodigoViaje(idViaje) as codigoViaje, 
				CONCAT_WS("\\\", laborAreaDeAplicacion, laborActividad) as laborActividad,
				CONCAT_WS("\\\", provincia, destino) as destino
				from viajes as pc
				inner join laboresActividad as sa using (idLaborActividad)
				inner join laboresAreaDeAplicacion as sada using (idLaborAreaDeAplicacion)
				inner join destinos as d using (idDestino)
				inner join provincias as p using (idProvincia)
				inner join viajes_empleados as pce using (idViaje)
				where idViajeEmpleado = '.$data['idViajeEmpleado'].'
				group by nroViaje
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

	function getViajePDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();
			$pageBodyBody = $this->getPDF();
			//print_r($pageBodyBody); die();
			//echo $result->getData();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return $pageBodyBody;
	}

	public function getPDF(){
		try {
			$this->getRowById();
			//fc_print($this); die();

			$pces = new ViajeEmpleadoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pces->getAllRows($data);

			$pcemd = new ViajeEmpleadoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcemd->getRowById($data);

			$pcvs = new ViajeVehiculoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcvs->getAllRows($data);

			$pcved = new ViajeVehiculoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcved->getRowById($data);

			$pceqs = new ViajeEquipamientoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pceqs->getAllRows($data);

			$pceqd = new ViajeEquipamientoDetalleVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pceqd->getRowById($data);

			$pcptbs = new ViajePlanificacionTrasladoBaseVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcptbs->getAllRows($data);
			//print_r($pcptbs); die();

			$pcptos = new ViajePlanificacionTrasladoOperativoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcptos->getAllRows($data);
			//print_r($pcptos); die();

			$pcpalos = new ViajePlanificacionAlojamientoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcpalos->getAllRows($data);
			//print_r($pcpalos); die();

			$pcpalis = new ViajePlanificacionAlimentoVO();
			$data = array();
			$data['nombreCampoWhere'] = 'idViaje';
			$data['valorCampoWhere'] = $this->idViaje['valor'];
			$pcpalis->getAllRows($data);
			//print_r($pcpalis); die();

			// documentacion de html2pdf aca: http://wiki.spipu.net/doku.php?id=html2pdf:es:v3:Accueil
			// para armar el pdf solo usar direcciones absolutas. las relativas no andan en el pdf
			//$result->message = $logodenotas; $result->status = STATUS_ERROR; return $result;
			$pages = '';
			$page = '';
			$css = '<style>
						.font8 {
							font-size: 8pt;
						}
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

			$pageHeader  = $css;
			$pageHeader .= '<page_header>';
			$pageHeader .= '	<table cellspacing="0" style="font-size: 8pt; margin: 10px 40px;">
										<tr>
											<td style="width: 70%; font-size: 18pt;">Código de viaje<br>'.$this->getCodigoViaje().'</td>
											<td style="width: 20%; text-align: right;"><img src="'.getFullPath().'/img/logo-sinec-nuevo-295x217.jpg" alt="" width="120" style="margin: -10px -16px 0 0;" alt="" /></td>
										</tr>
									</table>';
			$pageHeader .= '</page_header>';

			$pageFooter = '<page_footer>';
			$pageFooter .= '	<p align="center">P&aacute;gina [[page_cu]]/[[page_nb]]</p>';
			$pageFooter .= '</page_footer>';

			$pageBegin = '<page backtop="30mm" backbottom="10mm" backleft="10mm" backright="10mm" format="A4" orientation="P">';
			$pageEnd = '</page>';

			$pageBody = '	<table style="" class="borderYes">
							<tr>
								<td style="width: 20%; background-color: #ddd;">Destino</td>
								<td style="width: 80%;">'.$this->idDestino['referencia']->idProvincia['referencia']->provincia['valor'] . '\\'.$this->idDestino['referencia']->destino['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Actividad</td>
								<td style="width: 80%;">'.$this->idLaborActividad['referencia']->idLaborAreaDeAplicacion['referencia']->laborAreaDeAplicacion['valor'].'\\'.$this->idLaborActividad['referencia']->laborActividad['valor'].'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">F. Inicio</td>
								<td style="width: 80%;">'.convertDateDbToEs($this->fechaInicio['valor']).'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">F. Fin</td>
								<td style="width: 80%;">'.convertDateDbToEs($this->fechaFin['valor']).'</td>
							</tr>
							<tr>
								<td style="width: 20%; background-color: #ddd;">Obs:</td>
								<td style="width: 80%;">'.$this->observaciones['valor'].'</td>
							</tr>
						</table>';

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;

			$pageBody = '<div style="border: 1px; background-color: #ddd; text-align: center;">Recursos del Viaje</div>';
			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Empleados</div>';
			if($pces->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pces->result->getData() as $pce) {
					//print_r($pce);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Empleado # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pce->idEmpleado['referencia']->getNombreCompleto() . '<br>
										Celular SINEC: ' . $pce->idEmpleado['referencia']->celularEmpresa['valor'] . '<br>
										Celular personal: ' . $pce->idEmpleado['referencia']->celularParticular['valor'] . '<br>
										Email: ' . $pce->idEmpleado['referencia']->emailEmpresa['valor'] . '
									</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pcemd->esEmpleadoTemporario['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Empleado temporario</td>
									<td style="width: 80%; ">' . $pcemd->empleadoTemporario['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';
			}
			if($pcemd->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pcemd->observaciones['valor'].'</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Vehículos</div>';
			if($pcvs->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pcvs->result->getData() as $pcv) {
					//print_r($pcv);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Vehículo propio # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pcv->idVehiculo['referencia']->getNombreCompleto() . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pcved->esVehiculoDeAlquiler['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Vehículo de alquiler</td>
									<td style="width: 80%; ">' . $pcved->vehiculoDeAlquiler['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';

			}
			if($pcved->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pcved->observaciones['valor'].'</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Equipamientos</div>';
			if($pceqs->result->getData()) {
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pceqs->result->getData() as $pceq) {
					//print_r($pcv);die();
					$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Equipamiento # '. $count++ .'</td>
									<td style="width: 80%; ">' . $pceq->idEquipamiento['referencia']->getNombreCompleto() . '</td>
								</tr>';
				}
				$pageBody .= '	</table>';
			} else{
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}
			if($pceqd->esEquipamientoDeAlquiler['valor']){
				$pageBody .= '<table style="" class="borderYes">';
				$pageBody .= '	<tr>
									<td style="width: 20%; background-color: #ddd;">Equipamiento de alquiler</td>
									<td style="width: 80%; ">' . $pceqd->equipamientoDeAlquiler['valor'] . '</td>
								</tr>';
				$pageBody .= '</table>';

			}
			if($pceqd->observaciones['valor']) {
				$pageBody .= '<div style="border: 1px;">Observaciones: '.$pceqd->observaciones['valor'].'</div>';
			}

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 1
			$pages .= $page;

			$pageBody = '	<div style="border: 1px; background-color: #ddd; text-align: center;">Planificación de la Viaje</div>';
			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Traslados base</div>';
			if($pcptbs->result->getData()) {
				//print_r($pcptbs->result->getData());die();
				$pageBody .= '<table style="" class="borderYes">';
				$count = 1;
				foreach ($pcptbs->result->getData() as $pcptb) {

					$pcptbpces = new ViajePlanificacionTrasladoBaseViajeEmpleadoVO();
					$data = array();
					$data['nombreCampoWhere'] = 'idViajePlanificacionTrasladoBase';
					$data['valorCampoWhere'] = $pcptb->idViajePlanificacionTrasladoBase['valor'];
					$pcptbpces->getAllRows($data);
					$empleados = '';
					foreach ($pcptbpces->result->getData() as $pcptbpce) {
						//print_r($pcptbpce); die();
						$empleados .= 'Empleado: '.$pcptbpce->idViajeEmpleado['referencia']->idEmpleado['referencia']->getNombreCompleto().'<br>';
					}
					//print_r($pcptbs); die();

					//print_r($pcptb);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Traslado Base # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= $empleados;
					$pageBody .= '  		Tipo de Traslado: ' . $pcptb->idTipoTraslado['referencia']->tipoTraslado['valor'];
					$pageBody .= '  		<br>Tipo de Transporte: ' . $pcptb->idTipoTransporte['referencia']->tipoTransporte['valor'];
					if($pcptb->idTipoTransporte['referencia']->idTipoTransporte['valor'] == 6){
						$pageBody .= '		<br>Vehículo: ' . $pcptb->idViajeVehiculo['referencia']->idVehiculo['referencia']->getNombreCompleto();
						$pageBody .= '		<br>Km a recorrer: ' . $pcptb->km['valor'];
					}
					if($pcptb->idTipoTransporte['referencia']->idTipoTransporte['valor'] == 7){
						$pageBody .= '		<br>Vehículo: ' . $pcptb->idViajeVehiculoDetalle['referencia']->vehiculoDeAlquiler['valor'];
						$pageBody .= '		<br>Km a recorrer: ' . $pcptb->km['valor'];
					}
					$pageBody .= '	    	<br>Fecha de Arribo: ' . convertDateDbToEs($pcptb->fechaArribo['valor']);
					$pageBody .= '	    	<br>Destino de Salida: ' . $pcptb->idDestinoSalida['referencia']->destino['valor'];
					$pageBody .= '  		<br>Destino de Llegada: ' . $pcptb->idDestinoLlegada['referencia']->destino['valor'];
					if($pcptb->idTipoTraslado['referencia']->idTipoTraslado['valor'] == 5){
						$pageBody .= '  	<br>Fecha de Partida: ' . convertDateDbToEs($pcptb->fechaPartida['valor']);
					}
					$pageBody .= '  		<br>Observaciones: ' . $pcptb->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Traslados operativos</div>';
			if($pcptos->result->getData()) {
				//print_r($pcptos->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcptos->result->getData() as $pcpto) {
					//print_r($pcptb);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Traslado Operativo # '. $count++ .'</td>
										<td style="width: 78%; ">';
					if($pcpto->idTipoBien['referencia']->idTipoBien['valor'] == 1){
						$pageBody .= '  		Vehículo propio: '.$pcpto->idViajeVehiculo['referencia']->idVehiculo['referencia']->getNombreCompleto();
					}
					if($pcpto->idTipoBien['referencia']->idTipoBien['valor'] == 2){
						$pageBody .= '  		Vehículo de alquiler: '.$pcpto->idViajeVehiculoDetalle['referencia']->vehiculoDeAlquiler['valor'];
					}
					$pageBody .= '  		<br>Km diarios: ' . $pcpto->kmDiarios['valor'];
					$pageBody .= '  		<br>Cantidad de días: ' . $pcpto->cantidadDias['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpto->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Alojamientos</div>';
			if($pcpalos->result->getData()) {
				//print_r($pcpalos->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcpalos->result->getData() as $pcpalo) {
					//print_r($pcpalo);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Alojamiento # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= '  		Alojamiento: '.$pcpalo->idDestino['referencia']->destino['valor'];
					$pageBody .= '  		<br>Fecha de arribo: ' . convertDateDbToEs($pcpalo->fechaArribo['valor']);
					$pageBody .= '  		<br>Fecha de partida: ' . convertDateDbToEs($pcpalo->fechaPartida['valor']);
					$pageBody .= '  		<br>Cantidad de huéspedes: ' . $pcpalo->huespedes['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpalo->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$pageBody .= '<br><div style="border: 1px; background-color: #ddd; text-align: center;">Comidas</div>';
			if($pcpalis->result->getData()) {
				//print_r($pcpalis->result->getData());die();
				$pageBody .= '	<table style="width: 100%;" class="borderYes">';
				$count = 1;
				foreach ($pcpalis->result->getData() as $pcpali) {
					//print_r($pcpali);die();
					$pageBody .= '	<tr>
										<td style="width: 22%; background-color: #ddd;">Alimentos # '. $count++ .'</td>
										<td style="width: 78%; ">';
					$pageBody .= '  		Alimento: '.$pcpali->idDestino['referencia']->destino['valor'];
					$pageBody .= '  		<br>Cantidad de días: ' . $pcpali->cantidadDias['valor'];
					$pageBody .= '  		<br>Cantidad de empleados: ' . $pcpali->cantidadEmpleados['valor'];
					$pageBody .= '  		<br>Observaciones: ' . $pcpali->observaciones['valor'];
					$pageBody .= '		</td>
									</tr>';
				}
				$pageBody .= '	</table>';
			} else {
				$pageBody .= '<div style="border: 1px; text-align: center; height: 30px; vertical-align: middle;">No se registran datos.</div>';
			}

			$page = $pageBegin.$pageHeader.$pageBody.$pageFooter.$pageEnd; // fin de pagina 2
			$pages .= $page;
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		return html_entity_decode($pages, ENT_QUOTES | ENT_IGNORE, "UTF-8");
	}

	/*
	* devuelve datos de de una comision para una fecha y empleado
	*/
	public function getViajeParaATD($data){
		try {
			$sql = 'SELECT pc.idViaje, 
						getCodigoViaje(pc.idViaje) as codigoViaje, 
						idLaborActividad, 
						CONCAT_ws(" \\\ ", sada.laborAreaDeAplicacion, sa.laborActividad) as laborActividad, 
						idDestino,
						CONCAT_ws(" \\\ ", p.provincia, d.destino) as destino, 
						pc.observaciones as observacionesPlanificacion
					from viajes as pc
					inner JOIN viajes_empleados as pce using (idViaje)
					inner join laboresActividad as sa using(idLaborActividad)
					inner join laboresAreaDeAplicacion as sada using(idLaborAreaDeAplicacion)
					inner join destinos as d using (idDestino)
					inner join provincias as p using (idProvincia)
                    where true and idEmpleado = '.$data['idEmpleado'].' and "'.$data['fecha'].'" BETWEEN pc.fechaInicio and pc.fechaFin';
			//die($sql);
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

	public function getExcelFile($fileName, $data = null){
		$tablePropierties = $this->getObjectPropierties();
		foreach ($tablePropierties as $campo) {
			if($campo['visibleDTexport']){
				$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
				$header[ucfirst($campo['nombre'])] = 'string';// indica cómo será tomado el campo por el Excel
			}
		}

		$camposAux = implode(',', $dbFieldNames);
		$sql2 = $this->getSqlForTableExport($data);
		$sql = 'select '.$camposAux . ' from ('.$sql2.') as subConsulta';
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				$arrayRows = setHtmlEntityDecode($rs);
				$sheet_name = 'Hoja1';
				$writer = new XLSXWriter();
				$writer->writeSheetHeader($sheet_name, $header);
				foreach ($arrayRows as $row) {
					$writer->writeSheetRow($sheet_name, $row);
				}
				$writer->setAuthor('SIGIweb');
				$fileName = html_entity_decode($fileName, ENT_QUOTES | ENT_IGNORE, "UTF-8")."-".date('Ymd-His')."-all.xlsx";
				header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($fileName).'"');
				header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				$writer->writeToStdOut();
				exit(0);
			}else {
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage("La consulta no retornó registros.");
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

	}

	public function getObjectPropierties(){

		$objectPropierties[] = array('nombre' => 'idViaje',                         'dbFieldName' =>  'idViaje',               'visibleDT' => false, 'visibleDTexport' => false , 'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'código de Viaje',                 'dbFieldName' =>  'codigoViaje',           'visibleDT' => true,  'visibleDTexport' => true ,  'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'nro Viaje',                       'dbFieldName' =>  'nroViaje',              'visibleDT' => false, 'visibleDTexport' => false , 'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'Área de Aplicación / Actividad',  'dbFieldName' =>  'areaActividad',         'visibleDT' => true,  'visibleDTexport' => false , 'className' => false,   'aDataSort' => 'Área De Aplicación', 'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'Área De Aplicación',              'dbFieldName' =>  'laborAreaDeAplicacion', 'visibleDT' => false, 'visibleDTexport' => true ,  'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'Actividad',                       'dbFieldName' =>  'laborActividad',        'visibleDT' => false, 'visibleDTexport' => true ,  'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'Provincia / Destino',             'dbFieldName' =>  'provinciaDestino',      'visibleDT' => true,  'visibleDTexport' => false , 'className' => false,   'aDataSort' => 'Provincia',          'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'Destino',                         'dbFieldName' =>  'destino',               'visibleDT' => false, 'visibleDTexport' => true ,  'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'Provincia',                       'dbFieldName' =>  'provincia',             'visibleDT' => false, 'visibleDTexport' => true ,  'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'fechaInicioEN',                   'dbFieldName' =>  'fechaInicio',           'visibleDT' => false, 'visibleDTexport' => false , 'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'fecha Inicio',                    'dbFieldName' =>  'fechaInicioES',         'visibleDT' => true,  'visibleDTexport' => true ,  'className' => false,   'aDataSort' => 'fechaInicio',        'bSortable' => true,  'searchable' => true);   //aDataSort: Hay que indicar el nombre de la columna de la cual se desea ordenar al clickear sobre el campo actual
		$objectPropierties[] = array('nombre' => 'fechaFinEN',                      'dbFieldName' =>  'fechaFin',              'visibleDT' => false, 'visibleDTexport' => false,  'className' => false,    'aDataSort' => false,               'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'fecha Fin',                       'dbFieldName' =>  'fechaFinES',            'visibleDT' => true,  'visibleDTexport' => true ,  'className' => false,   'aDataSort' => 'fechaFin',           'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'observaciones',                   'dbFieldName' =>  'observaciones',         'visibleDT' => false, 'visibleDTexport' => true,   'className' => false,   'aDataSort' => false,                'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'opciones',                        'dbFieldName' =>  'accion',                'visibleDT' => true,  'visibleDTexport' => false,  'className' => 'center','aDataSort' => false,                'bSortable' => false, 'searchable' => false);

		return $objectPropierties;
	}

	public function getDataTableProperties(){
		$listadoDatosCargados['columnas'] = $this->getObjectPropierties();
		foreach ($listadoDatosCargados['columnas'] as $campo) {
			$campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
			$dbFieldNames[] = $campo['dbFieldName'];//Se carga un array con los nombres de los campos en la DB
		}
		for ($i = 0; $i < count($listadoDatosCargados['columnas']); $i++) {
			if ($listadoDatosCargados['columnas'][$i]['visibleDT'] == false) {//Se carga un array con los campos que nos seran visibles
				$bVisible[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['bSortable'] == false) {//Se carga un array con los campos que nos seran ordebables
				$bSortable[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['searchable'] == false) {//Se carga un array con los campos que no seran searcheables
				$searchable[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'left') {//Se carga un array con los campos que se ordenan a la izquierda
				$lefts[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'center') {//Se carga un array con los campos que se ordenan al centro
				$centers[] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['className'] == 'right') {//Se carga un array con los campos que se ordenan a la derecha
				$rights[] = $i;
			}
			if (is_array($listadoDatosCargados['columnas'][$i]['aaSorting'])) {
				$orden = $listadoDatosCargados['columnas'][$i]['aaSorting'][0];
				$criterio[$orden] = $listadoDatosCargados['columnas'][$i]['aaSorting'][1];
				$col[$orden] = $i;
			}
			if ($listadoDatosCargados['columnas'][$i]['visibleDTexport'] == true) {
				$exportables[] = $i;
			}

			if ($listadoDatosCargados['columnas'][$i]['aDataSort'] != false) {
				foreach ($campos as $campo) {
					if (strtolower($listadoDatosCargados['columnas'][$i]['aDataSort']) == strtolower($campo)) {
						$aDataSorts[] = '{"aDataSort":[' . array_search($campo, $campos) . '], "aTargets": [' . $i . ']}';// en cada elemento del array aDataSorts hay un string con la estructura del aDataSort para agregar a las propiedades del datatable
					}
				}
			}
		}

		if (is_array($bVisible)) {
			$bVisible = implode(', ', $bVisible);
		}
		if (is_array($bSortable)) {
			$bSortable = implode(', ', $bSortable);
		}
		if (is_array($searchable)) {
			$searchable = implode(', ', $searchable);
		}
		if (is_array($lefts)) {
			$lefts = implode(', ', $lefts);
		}
		if (is_array($centers)) {
			$centers = implode(', ', $centers);
		}
		if (is_array($rights)) {
			$rights = implode(', ', $rights);
		}
		if (is_array($exportables)) {
			$exportables = implode(', ', $exportables);
		}
		//Se pasan todos los arrays creados a la variable de retorno
		$listadoDatosCargados['campos'] = $campos;//Estos seran los nombres de las columnas

		$parametros['aDataSorts']  = $aDataSorts;
		$parametros['bVisible']    = '{"bVisible": false, "aTargets":[' . $bVisible . ']},';
		$parametros['bSortable']   = '{"bSortable": false, "aTargets":[' . $bSortable . ']},';
		$parametros['searchable']  = '{"searchable" : false, "aTargets":[' . $searchable . ']},';
		$parametros['lefts']       = '{"className": "dt-left", "aTargets":[' . $lefts . ']},';
		$parametros['centers']     = '{"className": "dt-center", "aTargets":[' . $centers . ']},';
		$parametros['rights']      = '{"className": "dt-right", "aTargets":[' . $rights . ']},';

		$listadoDatosCargados['exportables'] = '[' . $exportables . ']';

		//COLUMNDEFS
		$aoColumnDefs = '"aoColumnDefs": [';
		if(isset($parametros['bVisible'])) $aoColumnDefs .= $parametros['bVisible'];
		if(isset($parametros['bSortable'])) $aoColumnDefs .= $parametros['bSortable'];
		if(isset($parametros['searchable'])) $aoColumnDefs .= $parametros['searchable'];
		if(isset($parametros['lefts'])) $aoColumnDefs .= $parametros['lefts'];
		if(isset($parametros['centers'])) $aoColumnDefs .= $parametros['centers'];
		if(isset($parametros['rights'])) $aoColumnDefs .= $parametros['rights'];
		if(is_array($parametros['aDataSorts'])) $aoColumnDefs .= implode(',', $parametros['aDataSorts']);   //Importante que los DataSort esten al final
		$aoColumnDefs .= '],';
		//COLUMNDEFS

		//AASORTING
		$aaSorting = '"aaSorting": [ [1,"desc"] ],';
		//AASORTING

		//COLUMN NAMES
		$columns = '"columns": [';
		foreach ($dbFieldNames as $name) {
			$columns .= '{"data": "'.$name.'"},';
		}
		$columns = rtrim($columns,',');
		$columns .= '],';
		//COLUMN NAMES
		$listadoDatosCargados['columns'] = $columns;
		$listadoDatosCargados['aaSorting'] = $aaSorting;
		$listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

		return $listadoDatosCargados;   // Retorna campos,columns, aoColumnDefs y aaSorting
	}

	public function getDataTableData($postData, $data = null){
		try{
			$sql = $this->getSqlForTableExport($data);
			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				foreach ($dataSql['data'] as $row) {
					$auxRow['idViaje'] = $row['idViaje'];
					$auxRow['nroViaje'] = $row['nroViaje'];
					$auxRow['fechaInicio'] = $row['fechaInicio'];
					$auxRow['fechaInicioES'] = $row['fechaInicioES'];
					$auxRow['fechaFin'] = $row['fechaFin'];
					$auxRow['fechaFinES'] = $row['fechaFinES'];
					$auxRow['observaciones'] = limpiarCampoWysihtml5($row['observaciones']);
					$auxRow['areaActividad'] = $row['laborAreaDeAplicacion']. '/' . $row['laborActividad'];
					$auxRow['laborAreaDeAplicacion'] = $row['laborAreaDeAplicacion'];
					$auxRow['laborActividad'] = $row['laborActividad'];
					$auxRow['provinciaDestino'] = $row['provincia'] . '/' .$row['destino'];
					$auxRow['destino'] = $row['destino'];
					$auxRow['provincia'] = $row['provincia'];
					$auxRow['codigoViaje'] = $row['codigoViaje'];
					$auxRow['accion'] = '<a class="text-black" href="../pdfs/ViajePDF.php?'.codificarGets('id='.$row['idViaje'].'&action=pdf').'" target = "_blank" title="Guía de Ruta en PDF"><span class="fa fa-file-pdf-o fa-lg"></span></a>&nbsp;&nbsp;
                                                        <a class="text-black" href="ABMviajes.php?'.codificarGets('id='.$row['idViaje'].'&action=edit').' "title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
                                                        <a class="text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?'.codificarGets('id='.$row['idViaje'].'&action=delete').'" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
					$auxData[] = $auxRow;
				}
				$dataSql['data'] = $auxData;
			}
			echo json_encode($dataSql);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
	}

	public function getSqlForTableExport($data = null){
		$sql = 'SELECT
			a.idViaje,
			a.nroViaje,
			a.fechaInicio,
			DATE_FORMAT(a.fechaInicio, "%d/%m/%Y") as fechaInicioES,
			a.fechaFin,
			DATE_FORMAT(a.fechaFin, "%d/%m/%Y") as fechaFinES,
			a.observaciones,
		  	saa.laborAreaDeAplicacion,
			sa.laborActividad,
			l.destino,
			p.provincia,
			getCodigoViaje (a.idViaje) AS codigoViaje
		FROM
			viajes AS a
		INNER JOIN destinos AS l USING (idDestino)
		INNER JOIN provincias AS p USING (idProvincia)
		INNER JOIN laboresActividad AS sa USING (idLaborActividad)
		INNER JOIN laboresAreaDeAplicacion AS saa USING (idLaborAreaDeAplicacion)
		WHERE
			TRUE ';
		return $sql;
	}

}
if($_GET['action'] == 'json' && $_GET['type'] == 'getViajePorIdViajeEmpleado'){
	$aux = new ViajeVO();
	$data = array();
	$data['idViajeEmpleado'] = $_GET['idViajeEmpleado'];
	$aux->getViajePorIdViajeEmpleado($data);
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getViajeParaATD'){
	$aux = new ViajeVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		$data['fecha'] = convertDateEsToDb($_GET['fecha']);
		$data['idEmpleado'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		$aux->{$_GET['type']}($data);
	}
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ListadoViajes.php' )){
	$aux = new ViajeVO();
	if($_SESSION['usuarioLogueadoIdEmpleado']) {
		if (empty($_POST) ) {
			$aux->getDataTableProperties();
		} else {
			$data = array();
			$aux->getDataTableData($_POST, $data);
		}
	}
}
// debug zone
if($_GET['debug'] == 'ViajeVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajeVO();
	//print_r($kk->getAllRows());
	$kk->idViaje = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
