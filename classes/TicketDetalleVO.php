<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TicketDetalleVO extends Master2 {
    public $idTicketDetalle = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $idTicket = ["valor" => "", 
                               "obligatorio" => TRUE, 
                               "tipo" => "combo",
                               "nombre" => "ticket",
                                "referencia" => "",
                               ];
    public $detalle = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "detalle",
                       ];
	
	public $ticketArchivosArray;

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ticketDetalles');
		$this->setFieldIdName('idTicketDetalle');
		$this->idTicket['referencia'] = new TicketVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }
	
    /*
     * originalmente se pensó para cargar mas de un archivo por detalle pero luego se decidió que se sube uno solo.
     */ 
	public function insertData(){
		//print_r($_SESSION); die();
	   	try{
	   		//$this->conn->beginTransaction();
			parent::insertData();
			if($this->result->getStatus() != STATUS_OK) {
				//$this->conn->rollBack();
			 	return $this;
				//print_r($this); die();
			} else {
				//print_r($this); die();
				if ($this->ticketArchivosArray) {
					foreach ($this->ticketArchivosArray as $ticketArchivo) {
						//print_r($this); die();
						$ticketArchivo->idTicketDetalle['valor'] = $this->idTicketDetalle['valor'];
						$ticketArchivo->insertData();
						//print_r($ticketArchivo); die();
						if($ticketArchivo->result->getStatus() != STATUS_OK) {
							//$this->conn->rollBack();
							$this->result = $ticketArchivo->result;
						 	return $this;
						} else {
							//$this->conn->commit();
						}
					}
				} else {
					//$this->conn->commit();
				}
			}
			//die('asd');
		}catch(Exception $e){
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $this->getPDOMessage($e));
            myExceptionHandler($e);
		}
		return $this;
	}

	/*
	 * Devuelve el ultimo detalle de un ticket particular
	 */
	public function getUltimoTicketDetallePorTicket(){
		//print_r($this); die();
		$result = new Result();
		$sql = "select idticketdetalle as 'idTicketDetalle', idticket as 'idTicket', detalle
				from ".$this->getTableName()."
				where idticket = ".$this->idTicket['valor']."
				order by fechalog desc limit 1
				";
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
            if($rs = $ro->fetch(PDO::FETCH_ASSOC)){
                $this->mapData($rs);
            } else {
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage("ERROR, contacte al administrador.");
            }
		}catch(Exception $e){
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $this->getPDOMessage($e));
            myExceptionHandler($e);
        }
		//print_r($result); die();
   		return $result;
	}
    
}
if($_GET['debug'] == 'TicketDetalleVO' or false){
	echo "DEBUG<br>";
	$kk = new TicketDetalleVO();
	//print_r($kk->getAllRows());
	$kk->idTicketDetalle = 116;
	$kk->ticketDetalle = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>