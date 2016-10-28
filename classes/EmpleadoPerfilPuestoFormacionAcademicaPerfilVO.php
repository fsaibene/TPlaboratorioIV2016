<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class EmpleadoPerfilPuestoFormacionAcademicaPerfilVO extends Master2 {
    public $idEmpleadoPerfilPuestoFormacionAcademicaPerfil = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idEmpleadoPerfilPuesto = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "empleado perfil puesto",
                       "referencia" => "",
                       ];
	public $idFormacionAcademicaPerfil = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "formación académica",
                       "referencia" => "",
                       ];
	public $excluyente = ["valor" => TRUE,
						"obligatorio" => FALSE,
						"tipo" => "bool",
						"nombre" => "excluyente",
	];

	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
        $this->setTableName('empleadosPerfilPuesto_formacionesAcademicasPerfil');
		$this->setFieldIdName('idEmpleadoPerfilPuestoFormacionAcademicaPerfil');
        $this->idEmpleadoPerfilPuesto['referencia'] =  new EmpleadoPerfilPuestoVO();
        $this->idFormacionAcademicaPerfil['referencia'] =  new FormacionAcademicaPerfilVO();
    }

    /*
     * FormacionAcademica que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        /*if($this->descuentoPorcentaje['valor'] < 0 || $this->descuentoPorcentaje['valor'] > 100) {
            $resultMessage = 'El porcentaje de descuento debe ser un valor entre 0 y 100.';
        }*/
        return $resultMessage;
    }
}


if($_GET['debug'] == 'EmpleadoPerfilPuestoFormacionAcademicaPerfilVO' or false){
	echo "DEBUG<br>";
	$kk = new Establecimiento_TipoEstablecimientoVO();
	//print_r($kk->getAllRows());
	$kk->idEmpleadoPerfilPuestoFormacionAcademicaPerfil = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>