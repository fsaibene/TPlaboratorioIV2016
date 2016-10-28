<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class VersionPaginaVO extends Master2 {
    public $idVersionPagina = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idVersionSIGI = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "ID PADRE VP",
    ];
    public $idPagina = ["valor" => "",
        "obligatorio" => false,
        "tipo" => "combo",
        "nombre" => "Página",
        "referencia" => "",
    ];
	public $idTipoVersion = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "Tipo de Versión",
		"referencia" => "",
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
	public $versionPaginaItemArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'VersionPaginaItemVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'vp-vpi', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idVersionPaginaItem', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idVersionPagina'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];

    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('versionesPaginas');
        $this->setFieldIdName('idVersionPagina');
	    $this->idTipoVersion['referencia'] = new TipoVersionVO();
        $this->idPagina['referencia'] =  new PaginaVO();
        $this->idVersionSIGI['referencia'] =  new VersionSIGIVO();
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
                        $this->bug['valor'] = 0;
                        break;
                    case 3:
                        $this->mayor['valor']++;
                        $this->minor['valor'] = 0;
                        $this->bug['valor'] = 0;
                        break;
                }
            }else{
                $resultMessage = 'No se pudo Generar el Número de Version';
            }
        }
        return $resultMessage;
    }

    public function getCodigoUltimaVersionPagina() {
        $this->getUltimaVersion();
        if ($this->result->getStatus() == STATUS_OK) {
            $row = $this->result->getData()[0];
            $versionActual = 'v' . $row['mayor'] . '.' . $row['minor'] . '.' . $row['bug'];
            return ($versionActual);
        }
    }

    public function getUltimaVersion(){
        $sql = 'SELECT COALESCE(vp.mayor,1) mayor, COALESCE(vp.minor,0) minor, COALESCE(MAX(vp.bug),0) as bug, max(vs.fechaVersion) as fechaVersion
				FROM versionesPaginas as vp
				INNER JOIN versionesSIGI as vs using (idVersionSIGI)
				where (vp.mayor,vp.minor) = (
                	SELECT mayor, MAX(minor) 
                	FROM versionesPaginas
                	WHERE mayor = (
                		SELECT MAX(mayor) 
                		FROM versionesPaginas 
                		where idPagina = '.$this->idPagina['valor'].'
                	) and idPagina = '.$this->idPagina['valor'].'
                ) and idPagina = '.$this->idPagina['valor'];
        //die ($sql);
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

    public function getItemsVersiones(){
        $sql = " SELECT idVersionPagina, idpagina,fechaVersion, versionesPaginas.mayor, versionesPaginas.minor, versionesPaginas.bug, versionesPaginas.idTipoVersion, 
 					tipoVersion, item, idTicket , versionesPaginas.observaciones
				FROM versionesPaginas
				LEFT join versionesPaginasItems using (idVersionPagina)
				inner JOIN tiposVersion using(idTipoVersion)
				inner join versionesSIGI using (idVersionSIGI)
				where versionesPaginas.idPagina = ".$this->idPagina['valor']."
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
}

// debug zone
if($_GET['debug'] == 'VersionPaginaVO' or false){
    echo "DEBUG<br>";
    $kk = new VersionPaginaVO();
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
