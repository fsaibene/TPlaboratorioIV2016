<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 01-oct-2014 04:40:11 p.m.
 */
class VersionPaginaItemVO extends Master2 {
    public $idVersionPaginaItem = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idVersionPagina = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "ID PADRE VPI",
    ];

    public $item = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => " ítem",
    ];
    public $idTicket = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "Ticket",
        "referencia" => "",
    ];
   
    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('versionesPaginasItems');
        $this->setFieldIdName('idVersionPaginaItem');
        $this->idVersionPagina['referencia'] =  new VersionPaginaVO();
        $this->idTicket['referencia'] = new TicketVO();
    }

    /*
     * Funcion que valida cierta logica de negocios
     */
    public function validarLogicasNegocio($operacion){
        return $resultMessage;
    }

}

// debug zone
if($_GET['debug'] == 'VersionPaginaItemVO' or false){
    echo "DEBUG<br>";
    $kk = new VersionSIGIVO();
    //print_r($kk->getAllRows());
    $kk->idVersionSIGI = 116;
    $kk->usuario = 'hhh2';
    //print_r($kk->getRowById());
    //print_r($kk->insertData());
    //print_r($kk->updateData());
    //print_r($kk->deleteData());
    //echo $kk->getResultMessage();
}
