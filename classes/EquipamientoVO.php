<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class EquipamientoVO extends Master2 {
	public $idEquipamiento = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					    ];
    public $codigo = ["valor" => "",
					    "obligatorio" => TRUE,
					    "tipo" => "string",
					    "nombre" => "código",
	                    "longitud" => "16"
				        ];
	public $modelo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "modelo",
						"longitud" => "64"
				    	];
	public $marca = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "marca",
						"longitud" => "32"
				    	];
	public $nroSerie = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "nro de Serie",
						"referencia" => "",
						"longitud" => "32"
				    	];
    public $idTipoEquipamiento = ["valor" => "",
                        "obligatorio" => TRUE,
                        "tipo" => "combo",
                        "nombre" => "tipo de equipamiento",
                        "referencia" => "",
                    ];
    public $idTipoOrigenBien = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "combo",
                        "nombre" => "tipo de origen del equipamiento",
                        "referencia" => "",
                        ];
    public $idSucursalEstablecimiento = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "combo",
                        "nombre" => "sucursal del establecimiento",
                        "referencia" => "",
                         ];
    public $origenAlquilerDesde = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "date",
                        "nombre" => "fecha alquiler desde",
                        "validador" => ["admiteMenorAhoy" => TRUE,
                            "admiteHoy" => TRUE,
                            "admiteMayorAhoy" => TRUE,
                            ],
                        ];
    public $origenAlquilerHasta = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "date",
                        "nombre" => "fecha alquiler hasta",
                        "validador" => ["admiteMenorAhoy" => FALSE,
                            "admiteHoy" => TRUE,
                            "admiteMayorAhoy" => TRUE,
                            ],
                        ];
    public $llevaCalibracion = ["valor" => FALSE,
                        "obligatorio" => FALSE,
                        "tipo" => "bool",
                        "nombre" => "lleva calibración",
                        ];
    public $cantidad = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "integer",
                        "nombre" => "cantidad",
                         ];
    public $habilitado = ["valor" => TRUE,
                        "obligatorio" => FALSE,
                        "tipo" => "bool",
                        "nombre" => "habilitado",
                         ];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "archivo",
		"ruta" => "equipamientos/", // de files/ en adelante
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
		$this->setTableName('equipamientos');
		$this->setFieldIdName('idEquipamiento');
		$this->idTipoOrigenBien['referencia'] = new TipoOrigenBienVO();
		$this->idSucursalEstablecimiento['referencia'] = new SucursalEstablecimientoVO();
		$this->idTipoEquipamiento['referencia'] = new TipoEquipamientoVO();

	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        if($this->idTipoEquipamiento['valor'] == '1' || $this->idTipoEquipamiento['valor'] == '2' ){
            $this->idTipoOrigenBien['obligatorio'] = TRUE;
            $this->llevaCalibracion['obligatorio'] = TRUE;
            $this->cantidad['obligatorio'] = FALSE;
            $this->cantidad['valor'] = NULL;
        } else {
            $this->cantidad['obligatorio'] = TRUE;
            $this->idTipoOrigenBien['obligatorio'] = FALSE;
            $this->idTipoOrigenBien['valor'] = NULL;
            $this->llevaCalibracion['obligatorio'] = FALSE;
            $this->llevaCalibracion['valor'] = NULL;
            $this->idSucursalEstablecimiento['obligatorio'] = FALSE;
            $this->idSucursalEstablecimiento['valor'] = NULL;
            $this->origenAlquilerDesde['obligatorio'] = FALSE;
            $this->origenAlquilerDesde['valor'] = NULL;
            $this->origenAlquilerHasta['obligatorio'] = FALSE;
            $this->origenAlquilerHasta['valor'] = NULL;
        }

        if($this->idTipoOrigenBien['valor'] == '2'){
            $this->idSucursalEstablecimiento['obligatorio'] = TRUE;
            $this->origenAlquilerDesde['obligatorio'] = TRUE;
            $this->origenAlquilerHasta['obligatorio'] = TRUE;
	        if (strtotime(convertDateEsToDb($this->origenAlquilerDesde['valor'])) > strtotime(convertDateEsToDb($this->origenAlquilerHasta['valor'])) ) {
		        $resultMessage = 'La fecha de alquiler HASTA no puede ser menor que la fecha de alquiler DESDE.';
	        }
        } else {
            $this->idSucursalEstablecimiento['obligatorio'] = FALSE;
            $this->idSucursalEstablecimiento['valor'] = NULL;
            $this->origenAlquilerDesde['obligatorio'] = FALSE;
            $this->origenAlquilerDesde['valor'] = NULL;
            $this->origenAlquilerHasta['obligatorio'] = FALSE;
            $this->origenAlquilerHasta['valor'] = NULL;
        }

        return $resultMessage;
 	}

	public function getNombreCompleto(){
		return $this->codigo['valor'] . '/' .$this->modelo['valor'] . '/' .$this->nroSerie['valor'];
	}

	public function getCantidadEquipamientosHabilitados() {
		$data = NULL;
		$data['nombreCampoWhere'] = 'habilitado';
		$data['valorCampoWhere'] = '1';
		$this->getAllRows($data);
		return count($this->result->getData());
	}

	public function getCantidadEquipamientos() {
		$this->getAllRows();
		return count($this->result->getData());
	}

	public function getCantidadEquipamientosEnComision() {
		$sql = 'select mee.idEquipamiento
				from movimientosEquipamiento as m
				inner join locaciones as c on c.idLocacion = m.idLocacionDestino
				inner join movimientosEquipamiento_equipamientos as mee using (idMovimientoEquipamiento)
				inner join (
					select max(fecha) as fecha, idEquipamiento
					from movimientosEquipamiento
					inner join movimientosEquipamiento_equipamientos as mee2 using (idMovimientoEquipamiento)
					group by idEquipamiento
				) as m2 ON m2.fecha = m.fecha and m2.idEquipamiento = mee.idEquipamiento
				where c.idLocacion not in (1,2) -- no tiene llegada a ninguna base
				group by idEquipamiento
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}

	public function getCantidadEquipamientosEnBaseBA() {
		$sql = 'select m.*
				from movimientos as m
				inner join (
					select max(fecha) as fecha, idEquipamiento
					from movimientos
					group by idEquipamiento
				) as m2 USING (fecha, idEquipamiento)
				where idTipoMovimiento = 19  -- llegada base BA
				group by idTipoMovimiento
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}

	public function getCantidadEquipamientosEnBaseSJ() {
		$sql = 'select m.*
				from movimientos as m
				inner join (
					select max(fecha) as fecha, idEquipamiento
					from movimientos
					group by idEquipamiento
				) as m2 USING (fecha, idEquipamiento)
				where idTipoMovimiento = 18  -- llegada base SJ
				group by idTipoMovimiento
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetchAll(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = count($rs);
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
	}

	public function getComboList(){
		$data['data'] = 'idEquipamiento';
		$data['label'] = 'concat_ws("/", codigo, marca, modelo, nroSerie)';
		$data['orden'] = 'concat_ws("/", codigo, marca, modelo, nroSerie)';
		$data['nombreCampoWhere'] = 'llevaCalibracion';
		$data['valorCampoWhere'] = '1';

		parent::getComboList($data);
		return $this;
	}

	public function getComboList3($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			/*
			 * sería ideal que esta query traiga solo los equipos que estan en la locacion X en la fecha Y.
			 * Dichos datos vienen en el DATA desde el formulario aunque por ahora no se usa.
			 */
			$sql = 'select e.idEquipamiento, CONCAT(e.marca, "/", e.modelo) as equipamiento, mee.idMovimientoEquipamientoEquipamiento
                    from equipamientos as e
                    left join movimientosEquipamiento_equipamientos as mee on mee.idEquipamiento = e.idEquipamiento';
			if($data['idMovimientoEquipamiento'])
				$sql .= ' and idMovimientoEquipamiento = '.$data['idMovimientoEquipamiento'];
			$sql .= ' where e.habilitado and (idTipoEquipamiento = 1 || idTipoEquipamiento = 2)';
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

		$objectPropierties[] = array('nombre' => 'idEquipamiento',   'dbFieldName' => 'idEquipamiento',    'visibleDT' => false,'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'tipo',             'dbFieldName' => 'tipoEquipamiento',  'visibleDT' => true, 'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //aDataSort: Hay que indicar el nombre de la columna de la cual se desea ordenar al clickear sobre el campo actual
		$objectPropierties[] = array('nombre' => 'marca',            'dbFieldName' => 'marca',             'visibleDT' => true, 'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'modelo',           'dbFieldName' => 'modelo',            'visibleDT' => true, 'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'código',           'dbFieldName' => 'codigo',            'visibleDT' => true, 'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //aDataSort: Hay que indicar el nombre de la columna de la cual se desea ordenar al clickear sobre el campo actual
		$objectPropierties[] = array('nombre' => 'nro Serie',        'dbFieldName' => 'nroSerie' ,         'visibleDT' => true, 'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'tipoOrigenBien',   'dbFieldName' => 'tipoOrigenBien',    'visibleDT' => false,'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'llevaCalibracion', 'dbFieldName' => 'llevaCalibracion',  'visibleDT' => false,'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'cantidad',         'dbFieldName' => 'cantidad',          'visibleDT' => false,'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'habilitado',       'dbFieldName' => 'habilitado',        'visibleDT' => false,'visibleDTexport' => true,    'className' => false,  'aDataSort' => false,       'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'observaciones',    'dbFieldName' => 'observaciones',     'visibleDT' => false,'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,       'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'accion',           'dbFieldName' => 'accion',            'visibleDT' => true, 'visibleDTexport' => false,   'className' => 'center','aDataSort' => false,      'bSortable' => false, 'searchable' => false);

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
		$aaSorting = '"aaSorting": [ [4,"asc"] ],';
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
					$auxRow['idEquipamiento'] = $row['idEquipamiento'];
					$auxRow['tipoEquipamiento'] = $row['tipoEquipamiento'];
					$auxRow['codigo'] = $row['codigo'];
					$auxRow['modelo'] = $row['modelo'];
					$auxRow['marca'] = $row['marca'];
					$auxRow['nroSerie'] = $row['nroSerie'];
					$auxRow['tipoOrigenBien'] = $row['tipoOrigenBien'];
					$auxRow['llevaCalibracion'] = $row['llevaCalibracion'] ? 'SI' : 'NO';
					$auxRow['cantidad'] = $row['cantidad'];
					$auxRow['habilitado'] = $row['habilitado'] ? 'SI' : 'NO ';
					$auxRow['observaciones'] = $row['observaciones'];
					$auxRow['accion'] = '';
					if($auxRow['archivo']){
						$auxRow['accion'] ='<a class = "text-black" target="_blank" href="'.getPath().'/files/equipamientos/'.$row['archivo']. '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp';
					}
					$auxRow['accion'] .= '<a class = "text-black" href="'.$postData['page'].'?' . codificarGets('id=' . $row['idEquipamiento'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
	                                      <a class="text-black btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idEquipamiento'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
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
		//fc_print($data, true);
		$sql = 'SELECT
					e.idEquipamiento,
					te.tipoEquipamiento,
					e.codigo,
					e.modelo,
					e.marca,
					e.nroSerie,
					e.idTipoEquipamiento,
					tob.tipoOrigenBien,
					e.idSucursalEstablecimiento,
					e.origenAlquilerDesde,
					e.origenAlquilerHasta,
					e.llevaCalibracion,
					e.cantidad,
					e.habilitado,
					e.archivo,
					e.observaciones
				FROM
					equipamientos AS e
				LEFT JOIN tiposEquipamiento AS te ON te.idTipoEquipamiento = e.idTipoEquipamiento
				LEFT JOIN tiposOrigenBien AS tob ON tob.idTipoOrigenBien = e.idTipoOrigenBien
				LEFT JOIN sucursalesEstablecimiento AS se ON se.idSucursalEstablecimiento = e.idSucursalEstablecimiento
				WHERE
				TRUE';

		return $sql;
	}
}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMequipamientos.php' )){
	$aux = new EquipamientoVO();
	if (empty($_POST) ) {
		$aux->getDataTableProperties();
	} else {
		$aux->getDataTableData($_POST, null);
	}
}
// debug zone
if($_GET['debug'] == 'EquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new EquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idEquipamiento = 116;
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
