<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR .'../../plugins/PHP_XLSXWriter-master/xlsxwriter.class.php');
/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class EmpleadoVO extends Master2 {
	public $idEmpleado = ["valor" => "",
	                   "obligatorio" => FALSE,
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
    public $codigo = ["valor" => "",
					    "obligatorio" => TRUE,
					    "tipo" => "integer",
					    "nombre" => "código",
					    "validador" => ["admiteMenorAcero" => FALSE,
						    "admiteCero" => FALSE,
						    "admiteMayorAcero" => TRUE,
					    ],
				    ];
	public $nombres = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "nombres",
					];
	public $apellido = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "string",
						"nombre" => "apellido",
					];
	public $idTipoDocumento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo documento",
						"referencia" => "",
					];
	public $numeroDocumento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "dni",
						"nombre" => "número documento",
					];
	public $cuil = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "cuit",
						"nombre" => "cuil",
					];
	public $idTipoSexo = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "sexo",
						"referencia" => "",
					];
	public $idTipoContratacion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "tipo de contratación",
						"referencia" => "",
					];
	public $fechaNacimiento = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "date",
						"nombre" => "fecha de nacimiento",
						"validador" => ["admiteMenorAhoy" => TRUE,
							"admiteHoy" => FALSE,
							"admiteMayorAhoy" => FALSE
						],
					];
	public $idPais = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "país de origen",
						"referencia" => "",
					];
	public $idEstadoCivil = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "estado civil",
						"referencia" => "",
					];
	public $idNivelFormacion = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "nivel de formación",
						"referencia" => "",
					];
	public $idGmaps = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "combo",
						"nombre" => "domicilio",
						"referencia" => "",
					];
	public $codigoPostal = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "código postal",
                       ];
	public $telefonoParticular = ["valor" => "",
                       "obligatorio" => TRUE,
                       "tipo" => "string",
                       "nombre" => "teléfono particular",
                       ];
	public $celularParticular = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "teléfono celular particular",
                       ];
	public $celularEmpresa = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "string",
                       "nombre" => "teléfono celular empresa",
                       ];
	public $emailParticular = ["valor" => "",
                       "obligatorio" => true,
                       "tipo" => "email",
                       "nombre" => "e-mail particular",
                       ];
	public $emailEmpresa = ["valor" => "",
                       "obligatorio" => FALSE,
                       "tipo" => "email",
                       "nombre" => "e-mail empresa",
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
	public $observaciones = ["valor" => "",
						"obligatorio" => FALSE,
						"tipo" => "string",
						"nombre" => "observaciones",
					    ];
	public $puedeComprar = ["valor" => FALSE,
		"obligatorio" => TRUE,
		"tipo" => "bool",
		"nombre" => "habilitado para comprar",
	];

    public $archivo = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "firma",
        "ruta" => "empleados/firmas/", // de files/ en adelante
        "tamaño" => 10485760, // 10 * 1048576 = 10 mb
    ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('empleados');
		$this->setFieldIdName('idEmpleado');
		$this->idGmaps['referencia'] = new GmapsVO();
		$this->idTipoDocumento['referencia'] = new TipoDocumentoVO();
		$this->idTipoSexo['referencia'] = new TipoSexoVO();
		$this->idNivelFormacion['referencia'] = new NivelFormacionVO();
		$this->idEstadoCivil['referencia'] = new EstadoCivilVO();
		$this->idPais['referencia'] = new GeoPaisVO();
		$this->idTipoContratacion['referencia'] = new TipoContratacionVO();
	}

	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
        //$this->idEmpleado['valor'] = $_SESSION['usuarioLogueadoIdEmpleado'];
		/*
        if($operacion != DELETE) {
            if ($operacion == INSERT) {
                $this->apellido['obligatorio'] = FALSE;
                $this->nombres['obligatorio'] = FALSE;
                $this->idSexo['obligatorio'] = FALSE;
                $this->fechaNacimiento['obligatorio'] = FALSE;
                $this->idPais['obligatorio'] = FALSE;
            }
            if (strlen($this->clave['valor']) < 6) {
                $resultMessage = 'La clave debe contener como mínimo 6 caracteres.';
            }
        }*/
        return $resultMessage;
 	}

    public function getNombreCompleto(){
    	if($this->apellido['valor']){
            return $this->apellido['valor'] . ', ' .$this->nombres['valor'];
        }
    }

    public function getIniciales(){
    	if($this->apellido['valor']){
            return strtoupper(substr($this->nombres['valor'],0,1).substr($this->apellido['valor'],0,1));
	    }
    }

    public function getAvatar(){
        if(file_exists(getFullPath().'/files/avatars/'.$this->idEmpleado['valor'].'.jpg')){
            return $this->idEmpleado['valor'].'.jpg';
        } else {
            return 'nobody.png';
        }
    }
    /*
     * funcion que retorna un array que permite armar el menu del sistema de manera dinamica
     */
    public function getMenu(){
        $sql = 'select p.idSeccion, seccion, s.icono as iconoSeccion, p.idPagina, pagina, p.icono as iconoPagina, p.path as pathPagina, poseeAyuda,
                    p.superAdmin, p.orden
                from paginas as p
                left join secciones as s using (idseccion)
                where p.visibleEnMenu
                order by s.orden asc, p.orden asc';
        //die($sql);
        try {
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

	public function insertData(){
		//print_r($this); die('dos');
		try{
			//echo $this->idEstablecimiento['valor']; die();
			//$gmaps = clone $this->idGmaps['referencia'];
			//print_r($gmaps); die();
			//print_r($this->idGmaps['referencia']); die();
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

			$u = new UsuarioVO();
			$u->usuario['valor'] = strtolower(str_replace(' ', '', substr($this->nombres['valor'], 0, 1).$this->apellido['valor']));
			$u->email['valor'] = ($this->emailEmpresa['valor'])? $this->emailEmpresa['valor'] : $this->emailParticular['valor'];
			$u->idEmpleado['valor'] = $this->idEmpleado['valor'];
			$u->superAdmin['valor'] = 0;
			$u->habilitado['valor'] = 0;
			$u->insertData();
			//print_r($u);
			if($u->result->getStatus() != STATUS_OK) {
				$this->result = $u->result;
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

			//die('fin');
			$this->conn->commit();
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}

		return $this;
	}

	public function getComboList($data = NULL){
		try{
			$sql = 'select idEmpleado as data, concat(apellido, ", ", nombres) as label
					from empleados
					left join empleadosRelacionLaboral as erl using (idEmpleado)';
			$sql .= ' where true';
			if($data['valorCampoWhere'] == 'all'){
				//$sql .= ' and '.$data['nombreCampoWhere'].' is '.$data['valorCampoWhere'];
			} else {
				$sql .= ' and erl.fechaEgreso is null';
			}
			$sql .= ' order by label';
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

	public function getComboList3($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select e.idEmpleado, concat(apellido, ", ", nombres) as empleado, ce.idCapacitacionEmpleado
                    from empleados as e
                    left join capacitaciones_empleados as ce on ce.idEmpleado = e.idEmpleado';
			if($data['idCapacitacion']) $sql .= ' and idCapacitacion = '.$data['idCapacitacion'];
			$sql .= ' left join empleadosRelacionLaboral as erl on erl.idEmpleado = e.idEmpleado
			          where true and erl.fechaEgreso is null';
			$sql .= ' order by empleado';
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

	public function getComboList4($data = NULL){
		//print_r($this);die();
		//print_r($data);die();
		try{
			$sql = 'select e.idEmpleado, concat(apellido, ", ", nombres) as empleado, cer.idCapacitacionEficaciaResponsable
                    from empleados as e
                    left join capacitaciones_eficaciaResponsables as cer on cer.idEficaciaResponsable = e.idEmpleado ';
			if($data['idCapacitacion']) $sql .= ' and idCapacitacion = '.$data['idCapacitacion'];
			$sql .= ' left join empleadosRelacionLaboral as erl using (idEmpleado) ';
			$sql .= ' where true and erl.fechaEgreso is null';
			$sql .= ' order by empleado';
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
	 * combo que devuelve los empleados que poseen tarjeta de debito
	 */
	public function getComboList5(){
		try{
			$sql = 'select a2.idEmpleadoTarjetaDebito as data, concat(apellido, ", ", nombres, " - ", a3.tipoMarcaTarjeta, " ", SUBSTRING(a2.nroTarjetaDebito, -4)) as label
					from empleados as a1
					inner join empleadosTarjetaDebito as a2 using (idEmpleado)
					inner join tiposMarcaTarjeta as a3 using (idTipoMarcaTarjeta)
					left join empleadosRelacionLaboral as erl using (idEmpleado)
					where a2.habilitado and erl.fechaEgreso is null
					order by label
					';
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
	 * retorna la cantidad de empleados que estan vigentes, es decir, que no tienen fecha de egreso
	 */
	public function getCantidadEmpleadosVigentes() {
		$sql = 'select count(1) as cantidad
				from empleados as e
				inner join empleadosRelacionLaboral as erl using (idEmpleado)
				where erl.fechaEgreso is null
				';
		$cantidad = 0;
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
				//print_r($rs); die();
				$cantidad = $rs['cantidad'];
			}
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
			myExceptionHandler($e);
		}
		//print_r($this); die();
		return $cantidad;
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

		$objectPropierties[] = array('nombre' => 'idEmpleado',                    'dbFieldName' => 'idEmpleado',                      'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => true,  'searchable' => true);   //bVIsible:Indica si la columna se muestra
		$objectPropierties[] = array('nombre' => 'código',                        'dbFieldName' => 'codigo',                          'visibleDT' => false,'visibleDTexport' => true ,   'className' => false,  'aDataSort' => false,                 'bSortable' => true,  'searchable' => true);   //aDataSort: Hay que indicar el nombre de la columna de la cual se desea ordenar al clickear sobre el campo actual
		$objectPropierties[] = array('nombre' => 'apellido',                      'dbFieldName' => 'apellido',                        'visibleDT' => false,'visibleDTexport' => false,  'className' => false,  'aDataSort' => false,                  'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'empleado',                      'dbFieldName' => 'empleado',                        'visibleDT' => true, 'visibleDTexport' => true ,  'className' => false,  'aDataSort' => 'apellido',             'bSortable' => true,  'searchable' => false);
		$objectPropierties[] = array('nombre' => 'nombres',                       'dbFieldName' => 'nombres',                         'visibleDT' => false,'visibleDTexport' => false,   'className' => false,  'aDataSort' => false,                 'bSortable' => true,  'searchable' => true);   //bSorteable: Indica si se podra ordenar por esa columna
		$objectPropierties[] = array('nombre' => 'tipo de documento',             'dbFieldName' => 'tipoDocumento',                   'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'número de documento',           'dbFieldName' => 'numeroDocumento',                 'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'cuil',                          'dbFieldName' => 'cuil',                            'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'sexo',                          'dbFieldName' => 'tipoSexo',                        'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'tipo de Contratacion',          'dbFieldName' => 'tipoContratacion',                'visibleDT' => true, 'visibleDTexport' => true , 'className' => false,  'aDataSort' => false,                   'bSortable' => true,  'searchable' => true);
		$objectPropierties[] = array('nombre' => 'fecha de NacimientoEN',         'dbFieldName' => 'fechaNacimiento',                 'visibleDT' => false,'visibleDTexport' => false,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'fecha de Nacimiento',           'dbFieldName' => 'fechaNacimientoES',               'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => 'false',                'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'pais de origen',                'dbFieldName' => 'pais',                            'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'estado Civil',                  'dbFieldName' => 'estadoCivil',                     'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'nivel de Formación',            'dbFieldName' => 'nivelFormacion',                  'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'código Postal',                 'dbFieldName' => 'codigoPostal',                    'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'teléfono Particular',           'dbFieldName' => 'telefonoParticular',              'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'celular Particular',            'dbFieldName' => 'celularParticular',               'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'celular Empresa',               'dbFieldName' => 'celularEmpresa',                  'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'email Particular',              'dbFieldName' => 'emailParticular',                 'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'email Empresa',                 'dbFieldName' => 'emailEmpresa',                    'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'piso',                          'dbFieldName' => 'piso',                            'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'depto',                         'dbFieldName' => 'depto',                           'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'observaciones',                 'dbFieldName' => 'observaciones',                   'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'puede Comprar',                 'dbFieldName' => 'puedeComprar',                    'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'archivo',                       'dbFieldName' => 'archivo',                         'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'calle',                         'dbFieldName' => 'street_address',                  'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'numero',                        'dbFieldName' => 'street_number',                   'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'route',                         'dbFieldName' => 'route',                           'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'barrio',                        'dbFieldName' => 'neighborhood',                    'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'localidad',                     'dbFieldName' => 'locality',                        'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'provincia',                     'dbFieldName' => 'administrative_area_level_1',     'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'pais',                          'dbFieldName' => 'country',                         'visibleDT' => false,'visibleDTexport' => true ,  'className' => false,  'aDataSort' => false,                  'bSortable' => false, 'searchable' => false);
		$objectPropierties[] = array('nombre' => 'acción',                        'dbFieldName' => 'accion',                          'visibleDT' => true, 'visibleDTexport' => false, 'className' => 'center','aDataSort' => false,                  'bSortable' => false, 'searchable' => false);

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
		$aaSorting = '"aaSorting": [ [2,"asc"] ],';
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
					$auxRow['idEmpleado'] = $row['idEmpleado'];
					$auxRow['codigo'] = $row['codigo'];
					$auxRow['apellido'] = $row['apellido'];
					$auxRow['empleado'] = $row['apellido'].', '.$row['nombres'];
					$auxRow['nombres'] = $row['nombres'];
					$auxRow['tipoDocumento'] = $row['tipoDocumento'];
					$auxRow['numeroDocumento'] = $row['numeroDocumento'];
					$auxRow['cuil'] = $row['cuil'];
					$auxRow['tipoSexo'] = $row['tipoSexo'];
					$auxRow['tipoContratacion'] = $row['tipoContratacion'];
					$auxRow['fechaNacimiento'] = $row['fechaNacimiento'];
					$auxRow['fechaNacimientoES'] = $row['fechaNacimientoES'];
					$auxRow['pais'] = $row['pais'];
					$auxRow['estadoCivil'] = $row['estadoCivil'];
					$auxRow['nivelFormacion'] = $row['nivelFormacion'];
					$auxRow['codigoPostal'] = $row['codigoPostal'];
					$auxRow['telefonoParticular'] = $row['telefonoParticular'];
					$auxRow['celularParticular'] = $row['celularParticular'];
					$auxRow['celularEmpresa'] = $row['celularEmpresa'];
					$auxRow['emailParticular'] = $row['emailParticular'];
					$auxRow['emailEmpresa'] = $row['emailEmpresa'];
					$auxRow['piso'] = $row['piso'];
					$auxRow['depto'] = $row['depto'];
					$auxRow['observaciones'] = limpiarCampoWysihtml5($row['observaciones']);
					$auxRow['puedeComprar'] = $row['puedeComprar'];
					$auxRow['archivo'] = $row['archivo'];
					$auxRow['street_address'] = $row['street_address'];
					$auxRow['street_number'] = $row['street_number'];
					$auxRow['route'] = $row['route'];
					$auxRow['neighborhood'] = $row['neighborhood'];
					$auxRow['locality'] = $row['locality'];
					$auxRow['administrative_area_level_1'] = $row['administrative_area_level_1'];
					$auxRow['country'] = $row['country'];
					$auxRow['postal_cod'] = $row['postal_cod'];
					$auxRow['accion'] = '';
					if($auxRow['archivo']){
						$auxRow['accion'] ='<a target="_blank" href="'.getPath().'/files/empleados/firmas/'.$row['archivo']. '" title="Descargar documento"><span class="fa fa-file-o fa-lg"></span></a>&nbsp;&nbsp';
					}
					$auxRow['accion'] .= '<a href="'.$postData['page'].'?' . codificarGets('id=' . $row['idEmpleado'] . '&action=edit') . '" title="Editar"><span class="fa fa-edit fa-lg"></span></a>&nbsp;&nbsp;
	                                      <a class="btn-compose-modal-confirm" href="#" data-href="'.$postData['page'].'?' . codificarGets('id=' . $row['idEmpleado'] . '&action=delete') . '" data-toggle="modal" data-target="#compose-modal-confirm" title="Eliminar"><span class="fa fa-trash-o fa-lg"></span></a>';
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
					e.idEmpleado,
					e.codigo,
					getEmpleado(e.idEmpleado) as empleado,
					e.nombres,
					e.apellido,
					td.tipoDocumento,
					e.numeroDocumento,
					e.cuil,
					ts.tipoSexo,
					tc.tipoContratacion,
					e.fechaNacimiento,
					DATE_FORMAT(e.fechaNacimiento, "%d/%m/%Y") as fechaNacimientoES,
					gp.pais,
					ec.estadoCivil,
					nf.nivelFormacion,
					e.idGmaps,
					e.codigoPostal,
					e.telefonoParticular,
					e.celularParticular,
					e.celularEmpresa,
					e.emailParticular,
					e.emailEmpresa,
					e.piso,
					e.depto,
					e.observaciones,
					e.puedeComprar,
					e.archivo,
					gm.street_address,
					gm.street_number,
					gm.route,
					gm.neighborhood,
					gm.locality,
					gm.administrative_area_level_1,
					gm.country
				FROM
					empleados AS e
				LEFT JOIN tiposDocumento AS td ON td.idTipoDocumento = e.idTipoDocumento
				LEFT JOIN tiposSexo AS ts ON ts.idTipoSexo = e.idTipoSexo
				LEFT JOIN tiposContratacion AS tc ON tc.idTipoContratacion = e.idTipoContratacion
				LEFT JOIN geoPaises AS gp ON gp.idPais = e.idPais
				LEFT JOIN estadosCiviles AS ec ON ec.idEstadoCivil = e.idEstadoCivil
				LEFT JOIN nivelesFormacion AS nf ON nf.idNivelFormacion = e.idNivelFormacion
				LEFT JOIN gmaps AS gm ON gm.idGmaps = e.idGmaps
				WHERE
					TRUE';

		return $sql;
	}

}
if($_POST['action'] == 'dtSQL' && ($_POST['page'] == 'ABMempleado.php' )){
	$aux = new EmpleadoVO();
	if (empty($_POST) ) {
		$aux->getDataTableProperties();
	} else {
		$aux->getDataTableData($_POST, null);
	}
}

// debug zone
if($_GET['debug'] == 'EmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new EmpleadoVO();
	//print_r($kk->getAllRows());
	$kk->idEmpleado = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
