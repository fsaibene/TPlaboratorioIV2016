<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class EmpleadoPerfilPuestoCompetenciaPerfilVO extends Master2 {
    public $idEmpleadoPerfilPuestoCompetenciaPerfil = ["valor" => "",
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
	public $idCompetenciaPerfil = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "competencia perfil",
                       "referencia" => "",
                       ];

	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
        $this->setTableName('empleadosPerfilPuesto_competenciasPerfil');
		$this->setFieldIdName('idEmpleadoPerfilPuestoCompetenciaPerfil');
        $this->idEmpleadoPerfilPuesto['referencia'] =  new EmpleadoPerfilPuestoVO();
        $this->idCompetenciaPerfil['referencia'] =  new CompetenciaPerfilVO();
    }

    /*
     * Competencia que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        /*if($this->descuentoPorcentaje['valor'] < 0 || $this->descuentoPorcentaje['valor'] > 100) {
            $resultMessage = 'El porcentaje de descuento debe ser un valor entre 0 y 100.';
        }*/
        return $resultMessage;
    }
}


if($_GET['debug'] == 'EmpleadoPerfilPuestoCompetenciaPerfilVO' or false){
	echo "DEBUG<br>";
	$kk = new Establecimiento_TipoEstablecimientoVO();
	//print_r($kk->getAllRows());
	$kk->idEmpleadoPerfilPuestoCompetenciaPerfil = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>