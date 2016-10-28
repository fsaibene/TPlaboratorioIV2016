<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/PHPMailer/PHPMailerAutoload.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ProyectoComisionPlanificacionAlojamientoVO extends Master2 {
	public $idProyectoComisionPlanificacionAlojamiento = ["valor" => "", 
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
	public $idDestino = ["valor" => "",
						"obligatorio" => TRUE,
						"tipo" => "combo",
						"nombre" => "destino",
						"referencia" => "",
	];
	public $fechaArribo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "date",
		"nombre" => "Check-In",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $fechaPartida = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "date",
		"nombre" => "Check-Out",
		"validador" => ["admiteMenorAhoy" => TRUE,
			"admiteHoy" => TRUE,
			"admiteMayorAhoy" => TRUE,
		],
	];
	public $huespedes = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "integer",
		"nombre" => "cantidad de huéspedes",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $costo = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "float",
		"nombre" => "monto de la compra",
		"validador" => ["admiteMenorAcero" => FALSE,
			"admiteCero" => FALSE,
			"admiteMayorAcero" => TRUE,
		],
	];
	public $observaciones = ["valor" => "",
		"obligatorio" => FALSE,
		"tipo" => "string",
		"nombre" => "observaciones",
	];

	public function __construct(){
 		parent::__construct();
		$this->result = new Result();
		$this->setTableName('proyectosComisiones_planificacionAlojamiento');
		$this->setFieldIdName('idProyectoComisionPlanificacionAlojamiento');
		$this->idProyectoComision['referencia'] =  new ProyectoComisionVO();
		$this->idDestino['referencia'] =  new DestinoVO();
    }
	
	/*
     * Funcion que valida cierta logica de negocios
     */
	public function validarLogicasNegocio($operacion){
		if (strtotime(convertDateEsToDb($this->fechaArribo['valor'])) > strtotime(convertDateEsToDb($this->fechaPartida['valor'])) ) {
			$resultMessage = 'La fecha de Partida no puede ser menor que la fecha de Arribo.';
		}
        return $resultMessage;
 	}

	public function deleteDataArray($idProyectoComision, $idProyectoComisionPlanificacionAlojamientoArray = null){
		$sql = 'delete from '.$this->getTableName().'
				where idProyectoComision = '.$idProyectoComision;
		if($idProyectoComisionPlanificacionAlojamientoArray){
			$sql .= ' and idProyectoComisionPlanificacionAlojamiento not in ('.implode(",", $idProyectoComisionPlanificacionAlojamientoArray).')';
		}
		//die($sql);
		try{
			$ro = $this->conn->prepare($sql);
			if(!$ro->execute()){
				$this->result->setStatus(STATUS_ERROR);
				$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			}
			$this->result->setMessage('Los datos fueron ACTUALIZADOS con éxito.');
		}catch(Exception $e){
			$this->result->setData($this);
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación.\nReintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return $this;
	}
	/*public function getComboList($data = null){
		$sql = 'select CONCAT(a.apellido, ", ", a.nombres) as label, a.idPlanificacionAlojamiento as data, b.idProyectoComisionPlanificacionAlojamiento as selected
				from empleados as a
				left JOIN proyectosComisiones_empleados as b on a.idPlanificacionAlojamiento = b.idPlanificacionAlojamiento ';
		if($data)
			$sql .= ' and '.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		$sql .= ' where true';
		$sql .= ' order by label';
		//die($sql);
		try{
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
	}*/
}

// debug zone
if($_GET['debug'] == 'ProyectoComisionPlanificacionAlojamientoVO' or false){
	echo "DEBUG<br>";
	$kk = new ProyectoComisionPlanificacionAlojamientoVO();
	//print_r($kk->getAllRows());
	$kk->idProyectoComisionPlanificacionAlojamiento = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	//echo $kk->getResultMessage();
}
