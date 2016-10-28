<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR .'../../plugins/PHP_XLSXWriter-master/xlsxwriter.class.php');
/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class EmpleadoElementoProteccionPersonalVO extends Master2 implements iListadoExportable  {
	public $idEmpleadoElementoProteccionPersonal = ["valor" => "", 
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idEmpleado = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "empleado",
						"referencia" => "",
	];
	public $idElementoProteccionPersonal = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "Elemento de Protección Personal",
						"referencia" => "",
	];
	public $fechaEntrega = ["valor" => "",
						"obligatorio" => true,
						"tipo" => "date",
						"nombre" => "fecha de entrega",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => TRUE,
							"admiteMayorAhoy" => FALSE
						],
	];
	public $archivo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "archivo",
		"ruta" => "empleados/empleadosElementosProteccionPersonal/", // de files/ en adelante
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
		$this->setTableName('empleadosElementosProteccionPersonal');
		$this->setFieldIdName('idEmpleadoElementoProteccionPersonal');
		$this->idEmpleado['referencia'] = new EmpleadoVO();
		$this->idElementoProteccionPersonal['referencia'] = new ElementoProteccionPersonalVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        return $resultMessage;
 	}

	public function getExcelFile($fileName, $data = null){
		$tablePropierties = $this->getObjectPropierties();
		foreach ($tablePropierties as $campo) {
			if($campo['visibleDTexport']){
//				$campos[] = ucfirst($campo['nombre']);//Se carga un array con los campos
				//$campos[] = ucfirst($campo['nombre']);//Se carga un array con los nombres de los campos para mostrar al usuario
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

	public function getDataTableData($postData, $data = null){
		try{
			$sql = $this->getSqlForTableExport($data);
			$dataSql = getDataTableSqlDataFilter($postData, $sql);
			if ($dataSql['data']) {
				foreach ($dataSql['data'] as $row) {
					$auxRow['idEmpleadoElementoProteccionPersonal'] = $row['idEmpleadoElementoProteccionPersonal'];
					$auxRow['empleado'] = $row['apellido'].', '.$row['nombres'];
					$auxRow['apellido'] = $row['apellido'];
					$auxRow['nombres'] = $row['nombres'];
					$auxRow['elementoProteccionPersonal'] = $row['elementoProteccionPersonal'];
					$auxRow['fechaEntrega'] = $row['fechaEntrega'];
					$auxRow['fechaEntregaES'] = $row['fechaEntregaES'];
					$auxRow['archivo'] = $row['archivo'];
					$auxRow['observaciones'] = limpiarCampoWysihtml5($row['observaciones']);
					$auxRow['accion'] = '';
					if($auxRow['archivo']){
						$auxRow['accion'] ='<a target="_blank" href="'.getPath().'/files/empleados/empleadosElementosProteccionPersonal/'.$row['archivo']. '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp';
					}
					$auxRow['accion'] .= '<a class="btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idEmpleadoElementoProteccionPersonal'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
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
	public function getObjectPropierties(){

		$objectPropierties = null;   //Nombre: Indica nombre de los campos que se mostraran en el excel y el Datatable
		$objectPropierties[] = array('nombre' => 'idEmpleadoElementoProteccionPersonal', 'dbFieldName' => 'idEmpleadoElementoProteccionPersonal','visibleDT' => false,  'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false);   //className: Indica de que lado estara ordenado el elmento de esa columna
		$objectPropierties[] = array('nombre' => 'apellido',                             'dbFieldName' => 'apellido',                            'visibleDT' => false,  'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);
		$objectPropierties[] = array('nombre' => 'empleado',                             'dbFieldName' => 'empleado',                            'visibleDT' => true,   'visibleDTexport' => true ,   'className' => false,       'aDataSort' => 'apellido',    'bSortable' => true,     'searchable' => false);
		$objectPropierties[] = array('nombre' => 'nombres',                              'dbFieldName' => 'nombres',                             'visibleDT' => false,  'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'elemento de protección personal',      'dbFieldName' => 'elementoProteccionPersonal',          'visibleDT' => true,   'visibleDTexport' => true ,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'fechaEntregaEN',                       'dbFieldName' => 'fechaEntrega',                        'visibleDT' => false,  'visibleDTexport' => false,   'className' => false,       'aDataSort' => false,         'bSortable' => true,     'searchable' => false);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'fecha de entrega',                     'dbFieldName' => 'fechaEntregaES',                      'visibleDT' => true,   'visibleDTexport' => true ,   'className' => false,       'aDataSort' => 'fechaEntregaEN','bSortable' => true,   'searchable' => true);  //sercheable: indica si se podra buscar mediante el textbox de busqueda este campo
		$objectPropierties[] = array('nombre' => 'archivo',                              'dbFieldName' => 'archivo',                             'visibleDT' => false,  'visibleDTexport' => false,   'className' => 'center',    'aDataSort' => false,         'bSortable' => false,    'searchable' => false);
		$objectPropierties[] = array('nombre' => 'observaciones',                        'dbFieldName' => 'observaciones',                       'visibleDT' => false,  'visibleDTexport' => true ,   'className' => 'center',    'aDataSort' => false,         'bSortable' => false,    'searchable' => false);
		$objectPropierties[] = array('nombre' => 'acción',                               'dbFieldName' => 'accion',                              'visibleDT' => true,   'visibleDTexport' => false,   'className' => 'center',    'aDataSort' => false,         'bSortable' => false,    'searchable' => false);

		return $objectPropierties;
	}

	public function getDataTableProperties()
	{
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

		$parametros['aDataSorts'] = $aDataSorts;
		$parametros['bVisible'] = '{"bVisible": false, "aTargets":[' . $bVisible . ']},';
		$parametros['bSortable'] = '{"bSortable": false, "aTargets":[' . $bSortable . ']},';
		$parametros['searchable'] = '{"searchable" : false, "aTargets":[' . $searchable . ']},';
		$parametros['lefts'] = '{"className": "dt-left", "aTargets":[' . $lefts . ']},';
		$parametros['centers'] = '{"className": "dt-center", "aTargets":[' . $centers . ']},';
		$parametros['rights'] = '{"className": "dt-right", "aTargets":[' . $rights . ']},';

		$listadoDatosCargados['exportables'] = '[' . $exportables . ']';
		//COLUMNDEFS
		$aoColumnDefs = '"aoColumnDefs": [';

		(isset($parametros['bVisible'])) ? $aoColumnDefs .= $parametros['bVisible'] : '';
		(isset($parametros['bSortable'])) ? $aoColumnDefs .= $parametros['bSortable'] : '';
		(isset($parametros['searchable'])) ? $aoColumnDefs .= $parametros['searchable'] : '';
		(isset($parametros['lefts'])) ? $aoColumnDefs .= $parametros['lefts'] : '';
		(isset($parametros['centers'])) ? $aoColumnDefs .= $parametros['centers'] : '';
		(isset($parametros['rights'])) ? $aoColumnDefs .= $parametros['rights'] : '';
		(is_array($parametros['aDataSorts'])) ? $aoColumnDefs .= implode(',', $parametros['aDataSorts']) : '';//Importante que los DataSort esten al final
		$aoColumnDefs .= '],';
		//COLUMNDEFS

		//AASORTING
		$aaSorting = '"aaSorting": [[1,"asc"], [4,"asc"], [6,"desc"]],';
		//AASORTING

		//COLUMN NAMES
		$columns = '"columns": [';
		foreach ($dbFieldNames as $name) {
			$columns .= '{"data": "' . $name . '"},';
		}
		$columns = rtrim($columns, ',');
		$columns .= '],';
		//COLUMN NAMES

		$listadoDatosCargados['columns'] = $columns;
		$listadoDatosCargados['aaSorting'] = $aaSorting;
		$listadoDatosCargados['aoColumnDefs'] = $aoColumnDefs;

		return $listadoDatosCargados;// Retorna campos,columns, aoColumnDefs y aaSorting
	}


	public function getSqlForTableExport($data = null){
		$sql = "SELECT
						eepp.idEmpleadoElementoProteccionPersonal,
						eepp.idEmpleado,
						getEmpleado(e.idEmpleado) as empleado,
						e.nombres,
						e.apellido,
						epp.elementoProteccionPersonal,
						eepp.fechaEntrega,
						DATE_FORMAT(eepp.fechaEntrega,\"%d/%m/%Y\") as fechaEntregaES,
						eepp.archivo,
						eepp.observaciones
					FROM
						empleadosElementosProteccionPersonal AS eepp
					LEFT JOIN empleados AS e ON e.idEmpleado = eepp.idEmpleado
					LEFT JOIN elementosProteccionPersonal AS epp ON epp.idElementoProteccionPersonal = eepp.idElementoProteccionPersonal
					WHERE
						TRUE";
		if ($data['nombreCampoWhere'] && $data['valorCampoWhere']) {
			$sql .= " and e." . $data['nombreCampoWhere'] . " = " . $data['valorCampoWhere'];
		}
		return $sql;
	}

}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMempleadoElementoProteccionPersonal.php' || $_POST['page'] == 'ABMempleadoElementoProteccionPersonal2.php')){
	$aux = new EmpleadoElementoProteccionPersonalVO();
	if($_SESSION['selectorEmpleadoIdEmpleado']) {
		$data = array();
		$data['nombreCampoWhere'] = 'idEmpleado';
		$data['valorCampoWhere'] = $_SESSION['selectorEmpleadoIdEmpleado'];
	}
	if (empty($_POST) ) {
		$aux->getDataTableProperties();
	} else {
		$aux->getDataTableData($_POST, $data);
	}
}

// debug zone
if($_GET['debug'] == 'EmpleadoElementoProteccionPersonalVO' or false){
	echo "DEBUG<br>";
	$kk = new EmpleadoElementoProteccionPersonalVO();
	//print_r($kk->getAllRows());
	$kk->idEmpleadoElementoProteccionPersonal = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
