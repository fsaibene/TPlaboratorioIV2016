<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class VersionSIGIVO extends Master2 {
    public $idVersionSIGI = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
	public $idTipoVersion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Tipo de versión",
		"referencia" => "",
	];
    public $fechaVersion = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "date",
        "nombre" => "fecha de la versión",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
    public $mayor = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "mayor",
	    "validador" => ["admiteMenorAcero" => FALSE,
		    "admiteCero" => TRUE,
		    "admiteMayorAcero" => TRUE
	    ],
    ];
    public $minor = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "minor",
	    "validador" => ["admiteMenorAcero" => FALSE,
		    "admiteCero" => TRUE,
		    "admiteMayorAcero" => TRUE
	    ],
    ];
    public $bug = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "bug",
	    "validador" => ["admiteMenorAcero" => FALSE,
		    "admiteCero" => TRUE,
		    "admiteMayorAcero" => TRUE
	    ],
    ];
	public $versionSIGIitemArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'VersionSIGIitemVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'vsi', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idVersionSIGIitem', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idVersionSIGI'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
	public $versionPaginaArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'VersionPaginaVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'vp', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idVersionPagina', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idVersionSIGI'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];


    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('versionesSIGI');
	    $this->setFieldIdName('idVersionSIGI');
	    $this->idTipoVersion['referencia'] = new TipoVersionVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        if($operacion == 'INSERT'){
            $this->getUltimaVersion();
            if($this->result->getStatus() == STATUS_OK) {
                $row = $this->result->getData()[0];
                $this->bug['valor'] = $row['bug'];
                $this->minor['valor'] = $row['minor'];
                $this->mayor['valor'] = $row['mayor'];

                switch ($this->idTipoVersion['valor']) {
                    case 1:
                        $this->bug['valor']++;
                        break;
                    case 2:
                        $this->minor['valor']++;
                        $this->bug['valor']=0;
                        break;
                    case 3:
                        $this->mayor['valor']++;
                        $this->minor['valor']=0;
                        $this->bug['valor']=0;
                        break;
                }
            }else{
                $resultMessage = 'No se pudo Generar el Número de Version';
            }
        }
        return $resultMessage;
    }


    public function getUltimaVersion(){
        $sql = "SELECT COALESCE(mayor,1) mayor , COALESCE(minor,0) minor , COALESCE(MAX(bug),0) as bug  
                FROM versionesSIGI 
                where (mayor,minor) = (
                	SELECT mayor, MAX(minor) 
                    FROM versionesSIGI
                	WHERE mayor = (
                		SELECT MAX(mayor) 
                		FROM versionesSIGI
                    )
                )";
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
            $this->result->setStatus(STATUS_OK);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        $this->result;
    }

    public function getCodigoUltimaVersionSIGI() {
        $this->getUltimaVersion();
        if ($this->result->getStatus() == STATUS_OK) {
            $row = $this->result->getData()[0];
            $versionActual = 'v' . $row['mayor'] . '.' . $row['minor'] . '.' . $row['bug'];
            return ($versionActual);
        }
    }

    public function getItemsVersiones(){
        $sql = "SELECT idVersionSIGI, mayor, minor, bug, fechaVersion,tipoVersion as tipoVersion, item, idTicket, observaciones 
 				from versionesSIGI
				left join versionesSIGIitems using (idVersionSIGI)
				inner JOIN tiposVersion using(idTipoVersion)
				order by mayor desc, minor desc, bug desc ";
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
            $this->result->setStatus(STATUS_OK);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        $this->result;
    }

    public function getPaginasItemsVersion($idVersionSIGI){
        $sql = "SELECT idVersionPagina, idpagina, getPagina(idPagina) as pagina, versionesPaginas.observaciones, 
					versionesPaginas.mayor, versionesPaginas.minor, versionesPaginas.bug, versionesPaginas.idTipoVersion, item, idTicket
                from versionesSIGI
				inner join versionesPaginas using (idVersionSIGI)
				left join versionesPaginasItems using (idVersionPagina)
				where versionesSIGI.idVersionSIGI = $idVersionSIGI ";
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetchAll(PDO::FETCH_ASSOC);
            $this->result->setData($rs);
            $this->result->setStatus(STATUS_OK);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        $this->result;
    }
}

// debug zone
if($_GET['debug'] == 'VersionSIGIVO' or false){
    echo "DEBUG<br>";
    $kk = new VersionSIGIVO();
	//fc_print($kk->getAtributosPermitidos());
    //fc_print($kk->getAllRows());
    //$kk->idProyectoUnidadEconomica = 116;
    //$kk->usuario = 'hhh2';
    //fc_print($kk->getRowById());
    //fc_print($kk->insertData());
    //fc_print($kk->updateData());
    //fc_print($kk->deleteData());
    //echo $kk->getResultMessage();
}
