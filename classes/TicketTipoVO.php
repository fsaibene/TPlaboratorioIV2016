<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TicketTipoVO extends Master2 {
    public $idTicketTipo = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $ticketTipo = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "tipo",
                       ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ticketTipos');
		$this->setFieldIdName('idTicketTipo');
	}
	
    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
    
	public function getComboList(){
		$result = new Result();
      	$data['data'] = 'idticketTipo';
      	$data['label'] = 'ticketTipo';
      	$data['orden'] = 'ticketTipo';
   		$result = parent::getComboList($data); 
   		return $result;
	}
}
if($_GET['debug'] == 'TicketTipoVO' or false){
	echo "DEBUG<br>";
	$kk = new TicketTipoVO();
	//print_r($kk->getAllRows());
	$kk->idTicket = 116;
	$kk->titulo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>