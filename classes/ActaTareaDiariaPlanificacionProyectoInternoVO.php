<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ActaTareaDiariaPlanificacionProyectoInternoVO extends Master2 {
    public $idActaTareaDiariaPlanificacionProyectoInterno = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idActaTareaDiaria = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "planificación",
        "referencia" => "",
    ];
	public $idPlanificacionProyectoInterno = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "planificación proyecto interno",
		"referencia" => "",
	];
    public $observaciones = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "observaciones",
    ];
	public $planificacionProyectoInternoLaborTareaArray = [
		'tipo' => 'itemDinamico',
		'objectVOArray' => null, // es el array de objetos de la clase
		'className' => 'ActaTareaDiariaPlanificacionProyectoInternoLaborTareaVO', // es el nombre de la clase a la que hace referencia el array
		'varPrefix' => 'ppi-ppii', // es el prefijo con el que se identificará en el abm a los inputs de esta clase
		'filterKeyArray' => null, // es el array con los valores del filtro... Ver funcion deleteDataArray
		'filterKeyName' => 'idActaTareaDiariaPlanificacionProyectoInternoLaborTarea', // es el atributo de la clase que me servirá de clave en el abm para identificar un item (conjunto de inputs)
		'filterGroupKeyName' => 'idActaTareaDiariaPlanificacionProyectoInterno'  // es el campo por el que se agrupa... Ver funcion deleteDataArray
	];
   
    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('actaTareasDiaria_planificacionProyectosInternos');
        $this->setFieldIdName('idActaTareaDiariaPlanificacionProyectoInterno');
        $this->idActaTareaDiaria['referencia'] =  new ActaTareaDiariaVO();
        $this->idPlanificacionProyectoInterno['referencia'] =  new PlanificacionProyectoInternoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

}

// debug zone
if($_GET['debug'] == 'ActaTareaDiariaVO' or false){
    echo "DEBUG<br>";
    $kk = new ActaTareaDiariaVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
