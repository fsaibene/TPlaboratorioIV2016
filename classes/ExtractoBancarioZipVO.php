<?php
/**
 * Created by PhpStorm.
 * User: German
 * Date: 17/09/2016
 * Time: 21:37
 *
 * Esta clase nos sirve para abrir el archivo ZIP y procesar cada archivo que contiene.
 */

include_once('../config/classes/ExtractoBancarioVO.php');

class ExtractoBancarioZipVO extends Master2 {

    public function __construct(){
        parent::__construct();
        $this->result = new Result();
    }

    public function procesarZIP($ruta, $archivo, $date_hour){
        $zip = new ZipArchive;
        //recorro el contenido del archivo ZIP y renombro todos con el DATE HOUR del archivo ZIP.
        if ($zip->open("../files/" . $ruta . $archivo) === TRUE) {
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $zip->renameName($filename,$date_hour."_".strtoupper ($filename));
            }
            $zip->close();
            //die();
        }
        //Descomprimo el contenido del ZIP en la carpeta de nuevos.
        if ($zip->open("../files/" . $ruta . $archivo) === TRUE) {
            $zip->extractTo("../files/extractos/1_nuevos/");
            $zip->close();
        }
        //leo el directorio que tiene los archivos nuevos a procesar.
        $archivos = scandir("../files/extractos/1_nuevos/");
        $resultado = "";
        $estado = STATUS_OK;
        for($i=2; $i < count($archivos); $i++){
            //verificación de que esté bien indicado el Banco en el nombre del archivo
            $banco = explode("_", $archivos[$i])[2];
            $tipoBanco = new TipoBancoVO();
            $lista = $tipoBanco->getComboList()->getData();
            $valores=[];
            foreach ($lista as $row)
                array_push($valores, $row['label']);

            if (in_array($banco, $valores)){
                $extracto = new ExtractoBancarioVO();
                $extracto->procesar($archivos[$i]);
                $resultado .= "<strong>Extracto Banco ".$extracto->idTipoBanco['referencia']->tipoBanco['valor']."</strong>: <br>";
                $resultado .= $extracto->result->getMessage()."<br>";
                //si el estado es OK y el del archivo dió error, cambio el estado y queda ese.
                if($estado == STATUS_OK AND $extracto->result->getStatus() == STATUS_ERROR)
                    $estado = STATUS_ERROR;
            }else{
                $resultado .= "<strong>Error, el archivo ".'"'.str_replace($date_hour."_", "", $archivos[$i]).'"'." no comienza con el nombre del Banco al que pertenece.</strong><br>";
                $estado = STATUS_ERROR;
                //muevo el archivo de carpeta.
                rename ("../files/extractos/1_nuevos/".$archivos[$i], "../files/extractos/4_invalido/".$archivos[$i]);
            }
        }

        if($resultado){
            $this->result->setStatus($estado);
            $this->result->setMessage($resultado); //guardo en el message del result el mensaje que fuimos acumulando.
        }
    }
}