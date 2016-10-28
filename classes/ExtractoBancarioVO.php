<?php
/**
 * Created by PhpStorm.
 * User: German
 * Date: 28/08/2016
 * Time: 16:11
 */

require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../includes/Master2.php');
//require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/Excel/PHPExcel/Autoloader.php';
//require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/Excel/PHPExcel/PHPExcelAutoload.php');
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../tools/Excel/PHPExcel/IOFactory.php';

include_once('ExtractoBancarioItemVO.php');
include_once('TipoBancoVO.php');

class ExtractoBancarioVO extends Master2 {

    public $idExtractoBancario = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "ID",
    ];
    public $archivo = ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "string",
        "nombre" => "archivo",
        "ruta" => "extractos/0_zip/", // de files/ en adelante
        "tamaño" => 10485760, // 10 * 1048576 = 10 mb
    ];
    public $idTipoBanco = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "combo",
        "nombre" => "banco",
        "referencia" => "",
    ];
    public $cantidadRegistros= ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "cantidad registros",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $cantidadRegistrosError= ["valor" => "",
        "obligatorio" => TRUE,
        "tipo" => "integer",
        "nombre" => "cantidad registros error",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $cantidadRegistrosActualizados= ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "integer",
        "nombre" => "cantidad registros actualizados",
        "validador" => ["admiteMenorAcero" => FALSE,
            "admiteCero" => TRUE,
            "admiteMayorAcero" => TRUE
        ],
    ];
    public $mensaje = ["valor" => "",
        "obligatorio" => FALSE,
        "tipo" => "string",
        "nombre" => "mensaje",
    ];

    private $ruta;
    private $columnas;
    private $FILE;

    public $filaDesde;
    public $filaHasta;
    public $columnaDesde;
    public $columnaHasta;
    public $worksheet;
    public $resultado;

    public $extractoBancarioItemArray;

    public function __construct()
    {
	    parent::__construct();
	    $this->result = new Result();
	    $this->setHasNotification(true);
	    $this->setTableName('extractosBancarios');
	    $this->setFieldIdName('idExtractoBancario');
	    $this->idTipoBanco['referencia'] = new TipoBancoVO();
	    $this->cantidadRegistros['valor'] = 0;
	    $this->cantidadRegistrosError['valor'] = 0;
	    $this->cantidadRegistrosActualizados['valor'] = 0;
	    $this->mensaje['valor'] = "";
    }

    public function insertData(){
        //print_r($this); die('uno');
        try{
            //inserto el registro del extracto dado que va siempre porque lo usamos como log.
            parent::insertData();
            if($this->result->getStatus() != STATUS_OK) {
                //print_r($this); die('www');
                $this->moverArchivo("2_error");
                $this->mensaje['valor'] = $this->result->getMessage();
                return $this;
            }
            //print_r($this); die('dos');

            //si el mensaje está vacío es porque no hubo errores, entonces hago los inserts de los items.
            if(!$this->mensaje['valor']) {
                $this->conn->beginTransaction();
                if($this->extractoBancarioItemArray) {
                    foreach ($this->extractoBancarioItemArray as $aux){
                        //print_r($aux); die();
                        $aux->idExtractoBancario['valor'] = $this->idExtractoBancario['valor'];

                        //chequeo si los campos unique a insertar existen en la tabla.
                        //si existen los borro para que se inserten los nuevos.
                        if($aux->selectDataDuplicados()){
                            $aux->deleteDataDuplicados();
                            $this->cantidadRegistrosActualizados['valor']++;
                        }
                        $aux->insertData();
                        if($aux->result->getStatus()  != STATUS_OK) {
                            $this->mensaje['valor'] = $aux->result->getMessage();
                            $this->conn->rollBack();
                            $this->moverArchivo("2_error");
                            $this->updateData();
                            $this->result = $aux->result;
                            return $this;
                        }
                    }
                }
                //die('fin');
                $this->conn->commit();
                $this->moverArchivo("3_finalizado");
                //guardo el mensaje antes de hacer el update
                $bkresult = $this->result->getMessage();
                $this->updateData();
                //si el update dió correcto vuelvo a setear el msj anterior.
                if($this->result->getStatus() == STATUS_OK)
                    $this->result->setMessage($bkresult);
            }else{
                $this->result->setStatus(STATUS_ERROR);
                $this->result->setMessage($this->mensaje['valor']); //guardo en el message del result el mensaje que fuimos acumulando.
                $this->moverArchivo("2_error");
            }

        }catch(Exception $e) {
            $this->result->setStatus(STATUS_ERROR);
            $this->result->setMessage("ERROR, contacte al administrador. MSJ: " . $e->getMessage());
            myExceptionHandler($e);
        }

        return $this;
    }

    //este método toma los datos del archivo y configura los parámetros necesarios.
    public function importarArchivo(){
        //si es del banco SRIO llamo al XLS, para los demás son TXT.
        switch ($this->idTipoBanco['valor']){
            case 2:
                $this->FILE = PHPExcel_IOFactory::load($this->ruta);
                $this->worksheet = $this->FILE->getActiveSheet();
                break;
            default:
                $this->FILE = file($this->ruta);
                break;
        }
        $this->buscarFilaColumna();
    }

    //setea los parámetros de las filas y columnas a barrer
    public function buscarFilaColumna(){
        switch ($this->idTipoBanco['valor']){
            case 1:
                $this->filaDesde = 1;
                $this->filaHasta = count($this->FILE)-1;
                $this->columnaDesde = 0;
                $aux = explode(",", $this->FILE[0]);
                $this->columnaHasta = count($aux);
                $this->columnas = [0, 1, 2, 3, 4];
                break;
            case 2:
                $this->filaDesde = 8;
                $this->filaHasta = $this->worksheet->getHighestRow()-2; // e.g. 10
                $this->columnaDesde = 0;
                $highestColumn = $this->worksheet->getHighestColumn(); // e.g 'F'
                $this->columnaHasta = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $this->columnas = [0, 4, 5, 6,7];
                break;
            case 3:
                $this->filaDesde = 1;
                $this->filaHasta = count($this->FILE)-1;
                $this->columnaDesde = 0;
                $aux = explode(",", $this->FILE[0]);
                $this->columnaHasta = count($aux);
                $this->columnas = [0, 1, 3, 4, 5, 6];
                break;
            case 5:
                $this->filaDesde = 10;
                $this->filaHasta = count($this->FILE)-5;
                $this->columnaDesde = 0;
                $aux = explode(" ", $this->FILE[0]);
                $this->columnaHasta = count($aux);
                $this->columnas = [0, 2, 3, 5, 6]; //al parsear se agrega una columna 4 por los caracteres inválidos del archivo.
                break;
        }
    }

    public function recorrerArchivo(){
        //si es del banco SRIO llamo al XLS, para los demás son TXT.
        switch ($this->idTipoBanco['valor']){
            case 2:
                $this->recorrerArchivoXLS();
                break;
            case 5:
                $this->recorrerArchivoTXT();
                break;
            default:
                $this->recorrerArchivoCSV();
                break;
        }
    }

    public function recorrerArchivoXLS(){
        //recorro todas las filas que necesitamos.
        $array = [];
        for ($row = $this->filaDesde; $row <= $this->filaHasta; ++ $row) {
            $item = new ExtractoBancarioItemVO();
            for ($col = $this->columnaDesde; $col < $this->columnaHasta; ++ $col) {
                //si la columna leída nos interesa continúo.
                if(in_array($col, $this->columnas)){
                    $cell = $this->worksheet->getCellByColumnAndRow($col, $row);
                    $val = $cell->getValue();
                    if (PHPExcel_Shared_Date::isDateTime($cell)) {
                        $val = date($format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($val));
                    }
                    array_push($array, $val);
                }
            }

            $item->asignarValores($array, $this->idTipoBanco['valor']); //asigno los valores al objeto.
            $this->mensaje['valor'] .= $item->idTipoBancoConcepto['referencia']->result->getMessage();
            //si el status no es OK, incremento el contador de erróneos.
            if($item->idTipoBancoConcepto['referencia']->result->getStatus() != STATUS_OK)
                $this->cantidadRegistrosError['valor']++;
            $this->extractoBancarioItemArray[] = $item; //agrego el registro al array.
            $array = [];
            $this->cantidadRegistros['valor']++;
        }
    }

    private function sacarCaracter(&$valor){
        $valor = trim($valor, " ");
    }

    public function recorrerArchivoTXT(){
        //recorro todas las filas que necesitamos.
        $array = [];

        for ($row = $this->filaDesde; $row <= $this->filaHasta; ++ $row) {
            $item = new ExtractoBancarioItemVO();
            $registro = $this->FILE[$row];
            if($this->idTipoBanco['valor'] == 5){
                //pongo como separador la coma.
                $registro = preg_replace('/  +/', ",", $registro);
            }

            $registro = explode ("," , $registro);

            if($this->idTipoBanco['valor'] == 5){
                //la posición 4 del vector es un error al hacer el parseo, no hay que considerarlo.
                //arreglo los valores del array para sacar un caracter inválido que viene del archivo.
                $invalido = $registro[1][0].$registro[1][1]; //en esta posición encuentro el caracter inválido.
                foreach ($registro as &$valor){
                    $valor = str_replace($invalido, "", $valor);
                    $valor = trim ($valor);
                }
            }

            for ($col = $this->columnaDesde; $col < $this->columnaHasta; ++ $col) {
                //si la columna leída nos interesa continúo.
                if(in_array($col, $this->columnas)){
                    $val = $registro[$col];
                    array_push($array, trim($val));
                }
            }

            $item->asignarValores($array, $this->idTipoBanco['valor']); //asigno los valores al objeto.
            $this->mensaje['valor'] .= $item->idTipoBancoConcepto['referencia']->result->getMessage();
            //si el status no es OK, incremento el contador de erróneos.
            if($item->idTipoBancoConcepto['referencia']->result->getStatus() != STATUS_OK)
                $this->cantidadRegistrosError['valor']++;
            $this->extractoBancarioItemArray[] = $item; //agrego el registro al array.

            $array = [];
            $this->cantidadRegistros['valor']++;
        }
    }

    public function recorrerArchivoCSV(){
        //recorro todas las filas que necesitamos.
        $array = [];
        for ($row = $this->filaDesde; $row <= $this->filaHasta; ++ $row) {
            $item = new ExtractoBancarioItemVO();
            $registro = $this->FILE[$row];
            $registro = explode (",", $registro);

            //este IF es por si tiene "," en algún lugar del nro, pasa en MACRO.
            if ($this->idTipoBanco['valor'] == 3){
                $aux = [];
                //arreglamos el formato de la fecha.
                $date = new DateTime($registro[0]);
                $aux[0] = $date->format("Y-m-d");

                //asigno los valores que están bien en $aux.
                $aux[1] = $registro[1];
                $aux[2] = $registro[2];
                $aux[3] = $registro[3];

                //arreglamos el registro para que los nros queden en la posición del array que les corresponden.
                //analizo el campo Débito.
                $i=4;
                if(strlen($registro[$i]) > 0 && $registro[$i][strlen($registro[$i])-1]!='"' ){
                    $aux[4] = $registro[$i].$registro[$i+1];
                    $i++;
                }else{
                    $aux[4] = $registro[$i];
                }
                //sacamos las " de los nros.
                $aux[4] = str_replace('"', "", $aux[4]);

                //incremento el valor de $i.
                $i++;
                //analizo el campo Crédito.
                if(strlen($registro[$i]) > 0 && $registro[$i][strlen($registro[$i])-1]!='"' ){
                    $aux[5] = $registro[$i].$registro[$i+1];
                    $i++;
                }else{
                    $aux[5] = $registro[$i];
                }
                //sacamos las " de los nros.
                $aux[5] = str_replace('"', "", $aux[5]);

                //incremento el valor de $i.
                $i++;
                //analizo el campo Saldo.
                if(strlen($registro[$i]) > 0 && $registro[$i][strlen($registro[$i])-1]!='"' ){
                    $aux[6] = $registro[$i].$registro[$i+1];
                    $i++;
                }else{
                    $aux[6] = $registro[$i];
                }
                //sacamos las " de los nros.
                $aux[6] = str_replace('"', "", $aux[6]);

                $registro = $aux;
            }

            for ($col = $this->columnaDesde; $col < $this->columnaHasta; ++ $col) {
                //si la columna leída nos interesa continúo.
                if(in_array($col, $this->columnas)){
                    $val = $registro[$col];
                    array_push($array, trim($val));
                }
            }

            $item->asignarValores($array, $this->idTipoBanco['valor']); //asigno los valores al objeto.
            $this->mensaje['valor'] .= $item->idTipoBancoConcepto['referencia']->result->getMessage();
            //si el status no es OK, incremento el contador de erróneos.
            if($item->idTipoBancoConcepto['referencia']->result->getStatus() != STATUS_OK)
                $this->cantidadRegistrosError['valor']++;
            $this->extractoBancarioItemArray[] = $item; //agrego el registro al array.

            $array = [];
            $this->cantidadRegistros['valor']++;
        }
    }

    public function moverArchivo($dirDestino){
        //muevo el archivo de carpeta.
        $path_origen = $this->ruta;
        $path_destino = str_replace("1_nuevos", $dirDestino, $path_origen);
        //con rename cambio el directorio en el que se encuentra el archivo, por lo que lo mueve.
        rename ($path_origen, $path_destino);
    }

    public function procesar($archivo){
        $this->archivo['valor'] = $archivo;

        $this->ruta = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../files/extractos/1_nuevos/'.$archivo;
        //obtengo el banco desde el nombre del archivo.
        $aux = explode("_",$archivo);
        $banco = $aux[2];

        $this->idTipoBanco['referencia']->getRowById(array('nombreCampoWhere'=>'tipoBanco', 'valorCampoWhere'=>$banco));
        $this->idTipoBanco['valor'] = $this->idTipoBanco['referencia']->idTipoBanco['valor'];

        $this->importarArchivo();
        $this->recorrerArchivo();
        $this->insertData();
    }
}