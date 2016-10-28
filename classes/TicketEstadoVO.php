<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TicketEstadoVO extends Master2 {
    public $idTicketEstado = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $ticketEstado = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "estado",
                       ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ticketEstados');
		$this->setFieldIdName('idTicketEstado');
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
    public function getComboList(){
      	$data['data'] = 'idTicketEstado';
      	$data['label'] = 'ticketEstado';
      	$data['orden'] = 'ticketEstado';
		parent::getComboList($data); 
   		return $this;
	}
}
if($_GET['debug'] == 'TicketEstadoVO' or false){
	echo "DEBUG<br>";
	$kk = new TicketEstadoVO();
	//print_r($kk->getAllRows());
	$kk->idTicketEstado = 116;
	$kk->ticketEstado = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>