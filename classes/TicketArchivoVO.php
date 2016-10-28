<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TicketArchivoVO extends Master2 {
    public $idTicketArchivo = ["valor" => "", 
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE,
                                        ],
                       ];
    public $idTicketDetalle = ["valor" => "",
                               "obligatorio" => TRUE, 
                               "tipo" => "combo",
                               "nombre" => "ticket detalle",
                                "referencia" => "",
                               ];
    public $archivo = ["valor" => "", 
                       "obligatorio" => TRUE, 
                       "tipo" => "string",
                       "nombre" => "archivo",
	                    "ruta" => "tickets/", // de files/ en adelante
						"tamaño" => 10485760, // 10 * 1048576 = 10 mb
                       ];

	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('ticketArchivos');
		$this->setFieldIdName('idTicketArchivo');
		$this->idTicketDetalle['referencia'] = new TicketDetalleVO();
	}
	
	/*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return;
    }

    /*
     * esta funcion recupera de la db un ticketarchivo segun el idticketdetalle
     */
    public function getTicketArchivoByIdTicketDetalle(){
        $sql = "select * from ".$this->getTableName()." where idTicketDetalle = '".$this->idTicketDetalle['valor']."'";
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
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
}
if($_GET['debug'] == 'TicketArchivoVO' or false){
	echo "DEBUG<br>";
	$kk = new TicketArchivoVO();
	//print_r($kk->getAllRows());
	$kk->idTicketArchivo = 116;
	$kk->ticketArchivo = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>