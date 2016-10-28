<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class ContratoItemServicioTareaVO extends Master2 {
    public $idContratoItemServicioTarea = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idContratoItem = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => " Ítem de contrato",
        "referencia" => "",
    ];
    public $idServicioTarea = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => " Área de aplicación \ Actividad \ Tarea (Sinec)",
        "referencia" => "",
    ];

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('contratosItems_serviciosTareas');
	    $this->setFieldIdName('idContratoItemServicioTarea');
	    $this->idContratoItem['referencia'] = new ContratoItemVO();
	    $this->idServicioTarea['referencia'] = new ServicioTareaVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

	public function getComboList($data = null){
		$sql = 'SELECT CONCAT_ws(" \\\ ", a3.servicioAreaDeAplicacion, a2.servicioActividad, a1.servicioTarea) AS label,
					a1.idServicioTarea AS data,
					b1.idContratoItemServicioTarea AS selected
			FROM serviciosTarea AS a1 
			INNER JOIN serviciosActividad AS a2 USING (idServicioActividad)
			INNER JOIN serviciosAreaDeAplicacion AS a3 USING (idServicioAreaDeAplicacion)
			LEFT JOIN contratosItems_serviciosTareas AS b1 ON a1.idServicioTarea = b1.idServicioTarea ';
		if($data['valorCampoWhere'])
			$sql .= ' and b1.'.$data['nombreCampoWhere'].' = '.$data['valorCampoWhere'];
		else
			$sql .= ' and b1.'.$data['nombreCampoWhere'].' is null ';
		$sql .= ' where true 
		         order by label';
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
	}

	

	/*public function getContratoItemServicioTarea(){
		$sql = 'select a.idServicioTarea as value, CONCAT_WS(", ", apellido, nombres) as ServicioTarea, idContratoItem
				from ServicioTareas as a
				left join ContratoItemes_ServicioTareas as b on a.idServicioTarea = b.idServicioTarea ';
		if($this->idContratoItem['valor']){
			$sql .= ' and b.idContratoItem = '.$this->idContratoItem['valor'];
		}
		$sql .= ' where true
				  group by a.idServicioTarea ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			echo json_encode(array_map('setHtmlEntityDecode', $ro->fetchAll(PDO::FETCH_ASSOC)));
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}*/
}
/*if($_GET['action'] == 'json' && $_GET['type'] == 'getContratoItemServicioTarea'){
	$aux = new ContratoItemServicioTareaVO();
	//$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->idContratoItem['valor'] = $_GET['idContratoItem'];
	$aux->getContratoItemServicioTarea();
}*/

// debug zone
if($_GET['debug'] == 'ContratoItemServicioTareaVO' or false){
    echo "DEBUG<br>";
    $kk = new ContratoItemVO();
    //print_r($kk->getAllRows());
    $kk->idProyectoUnidadEconomica = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
