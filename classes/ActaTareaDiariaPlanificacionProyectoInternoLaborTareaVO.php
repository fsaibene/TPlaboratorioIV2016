<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaDiariaPlanificacionProyectoInternoLaborTareaVO extends Master2 {
    public $idActaTareaDiariaPlanificacionProyectoInternoLaborTarea = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idActaTareaDiariaPlanificacionProyectoInterno = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "planificación",
        "referencia" => "",
    ];
    public $idLaborTarea = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => " tarea",
        "referencia" => "",
    ];
    public $cantidad = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "cantidad",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
	public $idTipoUnidadMedidaLabor = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "unidad de medida",
		"referencia" => "",
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
   
    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('actaTareasDiariaPlanificacionProyectosInternos_laboresTarea');
        $this->setFieldIdName('idActaTareaDiariaPlanificacionProyectoInternoLaborTarea');
        $this->idActaTareaDiariaPlanificacionProyectoInterno['referencia'] =  new ActaTareaDiariaPlanificacionProyectoInternoVO();
        $this->idLaborTarea['referencia'] =  new LaborTareaVO();
	    $this->idTipoUnidadMedidaLabor['referencia'] =  new TipoUnidadMedidaLaborVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

}

// debug zone
if($_GET['debug'] == 'ActaTareaDiariaPlanificacionProyectoInternoLaborTareaVO' or false){
    echo "DEBUG<br>";
    $kk = new ActaTareaDiariaPlanificacionProyectoInternoLaborTareaVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
