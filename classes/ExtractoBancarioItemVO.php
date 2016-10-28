<?php
/**
 * Created by PhpStorm.
 * User: German
 * Date: 28/08/2016
 * Time: 16:11
 */

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
include_once('ExtractoBancarioVO.php');
include_once('TipoBancoConceptoVO.php');


class ExtractoBancarioItemVO extends Master2 {

    public $idExtractoBancarioItem = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $idExtractoBancario = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "extracto bancario",
        "referencia" => "",
    ];
    public $fecha = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "date",
        "nombre" => "fecha",
        "validador" => ["admiteMenorAhoy" => TRUE,
            "admiteHoy" => TRUE,
            "admiteMayorAhoy" => TRUE
        ],
    ];
    public $referencia = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "referencia",
    ];
    public $idTipoBancoConcepto = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "combo",
        "nombre" => "concepto",
        "referencia" => "",
    ];
    public $importe = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "importe",
        "validador" => ["admiteMenorAcero" => TRUE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];
    public $saldo = ["valor" => "0.00",
        "obligatorio" => TRUE,
        "tipo" => "float",
        "nombre" => "saldo",
        "validador" => ["admiteMenorAcero" => TRUE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE,
        ],
    ];

    public function __construct(){
        parent::__construct();
        $this->result = new Result();
        $this->setTableName('extractosBancarios_items');
        $this->setFieldIdName('idExtractoBancarioItem');
        $this->idExtractoBancario['referencia'] = new ExtractoBancarioVO();
        $this->idTipoBancoConcepto['referencia'] = new TipoBancoConceptoVO();
    }

    public function asignarValores ($array, $idTipoBanco){

        //con este switch saco la complejidad del orden de los campos en los distintos extractos.
        //así despues asigno estas variables a los atributos del objeto.
        switch ($idTipoBanco){
            case 1:
                $fecha = $array[0];
                $referencia = $array[1];
                if ($array[2])
                    $importe = $array[2]*(-1);
                else
                    $importe = $array[3];
                $concepto = $array[4];
                $saldo = 0; //TODO lo pongo en 0 porque no aparece en el extracto...
                break;
            case 2:
                $fecha = explode("-", $array[0]);
                $fecha[2]++; //BUG: incremento el día porque cuando lo toma desde el excel le resta uno...
                $fecha = $fecha[0]."-".$fecha[1]."-".$fecha[2];
                $referencia = $array[1];
                $concepto = $array[2];
                $importe = $array[3];
                $saldo = $array[4];
                break;
            case 3:
                $fecha = $array[0];
                $referencia = $array[1];
                $concepto = $array[2];
                if ($array[3]){
                    $importe = str_replace("\"", "",$array[3]); //saco las comillas.
                    $importe = str_replace(",", "", $importe); //saco la ,
                    $importe = trim ($importe);
                    $importe = $importe *(-1);

                }else{
                    $importe = str_replace("\"", "",$array[4]); //saco las comillas.
                    $importe = str_replace(",", "", $importe); //saco la ,
                    $importe = trim ($importe);
                }
                $saldo = str_replace("\"", "",$array[5]); //saco las comillas.
                $saldo = str_replace(",", "", $saldo); //saco la ,
                $saldo = trim ($saldo);
                break;
            case 5:
                $fecha = $array[0];
                $referencia = $array[2];
                $concepto = $array[1];
                $importe = $array[3];
                $saldo = $array[4];
                break;
        }

        $this->fecha['valor'] = $fecha;
        $this->referencia['valor'] = $referencia;

        $resultado = $this->idTipoBancoConcepto['referencia']->getIdTipoBancoConcepto($idTipoBanco, $concepto);
        if($this->idTipoBancoConcepto['referencia']->result->getStatus() == STATUS_OK){
            //si encontró el concepto para ese banco lo asigno al objeto.
            $this->idTipoBancoConcepto['valor'] = $resultado['idTipoBancoConcepto'];
        }else if($this->idTipoBancoConcepto['referencia']->result->getStatus() == STATUS_ERROR){
            $resultbk =  $this->idTipoBancoConcepto['referencia']->result;
            $this->idTipoBancoConcepto['referencia']->insertTipoBancoConcepto($idTipoBanco, $concepto);
            $this->idTipoBancoConcepto['referencia']->result = $resultbk; //dejo el msj que corresponde al concepto.
        }

        $this->importe['valor'] = $importe;
        $this->saldo['valor'] = $saldo;
    }

    public function selectDataDuplicados(){
        $sql = 'SELECT 1 as duplicado FROM extractosBancarios_items
              WHERE fecha = '."'".$this->fecha['valor']."'".' AND referencia = '."'".$this->referencia['valor']."'".
            ' AND idTipoBancoConcepto = '.$this->idTipoBancoConcepto['valor'];
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
            $rs = $ro->fetch(PDO::FETCH_ASSOC);
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        //print_r($this); die();
        return $rs['duplicado'];
    }

    public function deleteDataDuplicados(){
        $sql = 'DELETE FROM extractosBancarios_items
              WHERE fecha = '."'".$this->fecha['valor']."'".' AND referencia = '."'".$this->referencia['valor']."'".
            ' AND idTipoBancoConcepto = '.$this->idTipoBancoConcepto['valor'];
        //die($sql);
        try {
            $ro = $this->conn->prepare($sql);
            $ro->execute();
        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }
        return $this;
    }
}
