<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProyectoComisionEquipamientoDetalleVO extends Master2 {
	public $idProyectoComisionEquipamientoDetalle = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idProyectoComision = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "comisión",
		"referencia" => "",
	];
	public $esEquipamientoDeAlquiler = ["valor" => FALSE,
		"obligatorio" => TRUE,
		"tipo" => "bool",
		"nombre" => "equipamiento de alquiler",
	];
	public $equipamientoDeAlquiler = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "equipamiento de alquiler",
		"longitud" => "64"
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('proyectosComisiones_equipamientosDetalles');
		$this->setFieldIdName('idProyectoComisionEquipamientoDetalle');
		$this->idProyectoComision['referencia'] =  new ProyectoComisionVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		//print_r($this);die();
		if($this->esEquipamientoDeAlquiler['valor'] == '1'){
			$this->equipamientoDeAlquiler['obligatorio'] = TRUE;
		}else{
			$this->equipamientoDeAlquiler['obligatorio'] = FALSE;
			$this->equipamientoDeAlquiler['valor'] = null;
		}
        return $resultMessage;
 	}

}

// debug zone
if($_GET['debug'] == 'ProyectoComisionEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new ProyectoComisionEquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoComisionEquipamiento = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
