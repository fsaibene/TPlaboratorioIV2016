<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ViajeEmpleadoDetalleVO extends Master2 {
	public $idViajeEmpleadoDetalle = ["valor" => "",
	                   "obligatorio" => FALSE, 
	                   "tipo" => "integer",
	                   "nombre" => "ID",
					];
	public $idViaje = ["valor" => "",
		"obligatorio" => TRUE,
		"tipo" => "combo",
		"nombre" => "comisión",
		"referencia" => "",
	];
	public $esEmpleadoTemporario = ["valor" => FALSE,
		"obligatorio" => TRUE,
		"tipo" => "bool",
		"nombre" => "empleado de Temporario",
	];
	public $empleadoTemporario = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "empleado de Temporario",
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
		$this->setTableName('viajes_empleadosDetalles');
		$this->setFieldIdName('idViajeEmpleadoDetalle');
		$this->idViaje['referencia'] =  new ViajeVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		//print_r($this);die();
		if($this->esEmpleadoTemporario['valor'] == '1'){
			$this->empleadoTemporario['obligatorio'] = TRUE;
		}else{
			$this->empleadoTemporario['obligatorio'] = FALSE;
			$this->empleadoTemporario['valor'] = null;
		}
        return $resultMessage;
 	}

}

// debug zone
if($_GET['debug'] == 'ViajeEmpleadoVO' or false){
	echo "DEBUG<br>";
	$kk = new ViajeEmpleadoVO();
	//print_r($kk->getAllRows());
	$kk->idViajeEmpleado = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
