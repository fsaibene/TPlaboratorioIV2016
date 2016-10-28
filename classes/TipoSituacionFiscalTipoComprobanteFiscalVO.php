<?php
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');

/**
 * @author fcsistemas - www.fcsistemas.com.ar
 * @version 2.1
 * @created 23-ene-2015
 */
class TipoSituacionFiscalTipoComprobanteFiscalVO extends Master2 {
    public $idTipoSituacionFiscalTipoComprobanteFiscal = ["valor" => "",
                       "obligatorio" => FALSE, 
                       "tipo" => "integer",
                       "nombre" => "id",
                       "validador" => ["admiteMenorAcero" => FALSE, 
                                        "admiteCero" => FALSE, 
                                        "admiteMayorAcero" => TRUE
                                        ],
                       ];
	public $idTipoSituacionFiscal = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "",
                       "referencia" => "",
                       ];
	public $idTipoEstablecimiento = ["valor" => "",
                       "obligatorio" => TRUE, 
                       "tipo" => "combo",
                       "nombre" => "",
                       "referencia" => "",
                       ];
    public $habilitado = ["valor" => TRUE,
                        "obligatorio" => FALSE,
                        "tipo" => "bool",
                        "nombre" => "habilitado",
                          ];
    public $observaciones = ["valor" => "",
                        "obligatorio" => FALSE,
                        "tipo" => "string",
                        "nombre" => "observaciones",
                        ];

	public $idUsuarioLog;
	public $fechaLog;
	
	public function __construct()
	{
		parent::__construct();
		$this->result = new Result();
		$this->setHasNotification(true);
		$this->setTableName('tipoSituacionFiscalTipoComprobanteFiscal');
		$this->setFieldIdName('idTipoSituacionFiscalTipoComprobanteFiscal');
		$this->idTipoSituacionFiscal['referencia'] = new TipoSituacionFiscalVO();
		$this->idTipoComprobanteFiscal['referencia'] = new TipoComprobanteFiscalVO();
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

	public function getComprobantesFiscalesPorSituacionFiscal(){
		$sql = 'select tcf.idTipoComprobanteFiscal, tcf.tipoComprobanteFiscal
				from tiposComprobanteFiscal as tcf
				inner join tiposSituacionFiscal_tiposComprobanteFiscal as tsftcf using (idTipoComprobanteFiscal)
				where tsftcf.idTipoSituacionFiscal = '.$this->idTipoSituacionFiscal['valor'].'
				and tcf.habilitado = '.$this->habilitado['valor'].'
				order by tcf.orden, tcf.tipoComprobanteFiscal
                ';
		//die($sql);
		try {
			$ro = $this->conn->prepare($sql);
			$ro->execute();
			$rs = $ro->fetchAll(PDO::FETCH_ASSOC);
			//print_r($rs);
			$items = array();
			foreach ($rs as $row) {
				$items[] = array('idTipoComprobanteFiscal' => $row['idTipoComprobanteFiscal'],
					'tipoComprobanteFiscal' => $row['tipoComprobanteFiscal'],
				);
			}
			echo json_encode($items);
		}catch(Exception $e) {
			$this->result->setStatus(STATUS_ERROR);
			$this->result->setMessage('ERROR: Ocurrió un error al realizar la operación. Reintente o contactese con el Administrador.');
			myExceptionHandler($e);
		}
		return;
	}
}
if($_GET['action'] == 'json' && $_GET['type'] == 'getComprobantesFiscalesPorSituacionFiscal'){
	$aux = new TipoSituacionFiscalTipoComprobanteFiscalVO();
	$aux->habilitado['valor'] = $_GET['habilitado'];
	$aux->idTipoSituacionFiscal['valor'] = $_GET['idTipoSituacionFiscal'];
	$aux->getComprobantesFiscalesPorSituacionFiscal();
}

if($_GET['debug'] == 'TipoSituacionFiscalTipoComprobanteFiscalVO' or false){
	echo "DEBUG<br>";
	$kk = new SucursalEstablecimiento_TipoEstablecimientoVO();
	//print_r($kk->getAllRows());
	$kk->idTipoSituacionFiscalTipoComprobanteFiscal = 116;
	$kk->usuario = 'hhh2';
	//print_r($kk->getRowById());
	//print_r($kk->insertData());
	//print_r($kk->updateData());
	//print_r($kk->deleteData());
	echo $kk->getResultMessage();
}
?>