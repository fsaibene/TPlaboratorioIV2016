<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class MovimientoEquipamientoEquipamientoVO extends Master2 {
    public $idMovimientoEquipamientoEquipamiento = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idMovimientoEquipamiento = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "movimiento equipamiento",
                       "referencia" => "",
                       ];
	public $idEquipamiento = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "equipamiento",
                       "referencia" => "",
                       ];

	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
        $this->setTableName('movimientosEquipamiento_equipamientos');
		$this->setFieldIdName('idMovimientoEquipamientoEquipamiento');
        $this->idMovimientoEquipamiento['referencia'] =  new MovimientoEquipamientoVO();
        $this->idEquipamiento['referencia'] =  new EquipamientoVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        /*if($this->descuentoPorcentaje['valor'] < 0 || $this->descuentoPorcentaje['valor'] > 100) {
            $resultMessage = 'El porcentaje de descuento debe ser un valor entre 0 y 100.';
        }*/
        return $resultMessage;
    }
}


if($_GET['debug'] == 'MovimientoEquipamientoEquipamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new MovimientoEquipamiento_EquipamientoVO();
	//print_r($kk->getAllRows());
	$kk->idMovimientoEquipamientoEquipamiento = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>