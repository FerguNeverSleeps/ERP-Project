<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');
$delimitador = ";";
if (PHP_SAPI == 'cli')
    die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
//include('lib/php_excel.php');
require_once '../paginas/phpexcel/Classes/PHPExcel.php';
include('../paginas/lib/php_excel.php');
require_once './netsuite_funciones_comunes.php';

if (!isset($_GET['mes']) or !isset($_GET['anio']) or !isset($_REQUEST["fecha_inicio"]) or !isset($_REQUEST["fecha_fin"])) {
    exit;
}

if (!extension_loaded('zip')) {
    print "Se requiere de la extension php-zip.";
    exit;
}

$anio = intval($_GET['anio']);
$mes = intval($_GET['mes']);
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];
$ffecha_fin = $fecha_fin;
$ffecha_inicio = $fecha_inicio;
$fecha_fin = explode("/", $fecha_fin);
$fecha_fin = $fecha_fin[2] . "-" . $fecha_fin[1] . "-" . $fecha_fin[0];
$fecha_inicio = explode("/", $fecha_inicio);
$fecha_inicio = $fecha_inicio[2] . "-" . $fecha_inicio[1] . "-" . $fecha_inicio[0];
$conexion = new bd($_SESSION['bd']);
$objPHPExcel = new PHPExcel();
$objPHPExcel->getProperties()->setCreator("AMAXONIA")
    ->setLastModifiedBy("AMAXONIA")
    ->setTitle("Comprobante Salarios Administrativo");

$sheet = $objPHPExcel->getActiveSheet();

// PINTAR COLUMNAS
$columnas = [
    "frecuencia", "id_externo", "moneda", "tipo_cambio", "fecha", "fecha_contable", "nota", "subsidiaria",
    "nota_linea", "id_cuenta_contable", "numero_cuenta_contable", "debito", "credito", "external_id_tercero",
    "nombre_tercero", "clase", "departamento", "ubicacion_centro_costo",
];

$ln = 1;
$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$ln", join($delimitador, $columnas), PHPExcel_Cell_DataType::TYPE_STRING);
$ln++;

$total_creditos = 0;
$total_debitos = 0;
$otrosNivelesLista = obtenerNiveles($conexion, $fecha_inicio, $fecha_fin, 1);
$ultima_fila = [];
foreach ([[1], $otrosNivelesLista] as $key => $niveles) {
    // PINTAR CONCEPTOS EXCEPTO 180, 185, 187,190, 195,196,198,199
    $sql_ejecutar = obtenerSqlDeConceptosComunes($fecha_inicio, $fecha_fin, $niveles);

    $res = $conexion->query($sql_ejecutar);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_array()) {
            if ($row['codcon'] == 100) {
                // -185,-187,-190,-195,196,-198,-199 al 100
                $restaDebito = obtenerSumatoriaPorConceptos($conexion, [184,185, 187, 190, 195, 196, 198, 199], $fecha_inicio, $fecha_fin, $niveles);
                $sumaDebito = obtenerSumatoriaPorConceptos($conexion, [196], $fecha_inicio, $fecha_fin, $niveles);
                $row['debito'] = $row['debito'] - $restaDebito + $sumaDebito;
            }

            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$ln", construirAsiento($row, $delimitador), PHPExcel_Cell_DataType::TYPE_STRING);
            $ln++;

            $total_debitos += $row['debito'];
            $total_creditos += $row['credito'];
            $ultima_fila = $row;
        }
    }

    // PINTAR PATRONALES
    $sql_ejecutar = obtenerSqlDeConceptosPatronales($fecha_inicio, $fecha_fin, $niveles);
    $res = $conexion->query($sql_ejecutar);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_array()) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$ln", construirAsiento($row, $delimitador), PHPExcel_Cell_DataType::TYPE_STRING);
            $ln++;
            $total_debitos += $row['debito'];
            $total_creditos += $row['credito'];
            $ultima_fila = $row;
        }
    }

    // PINTAR CONCEPTO 180
    $sql_ejecutar = obtenerSqlDeConceptos180($fecha_inicio, $fecha_fin, $niveles);
    $res = $conexion->query($sql_ejecutar);
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_array()) {
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$ln", construirAsiento($row, $delimitador), PHPExcel_Cell_DataType::TYPE_STRING);
            $ln++;
            $total_debitos += $row['debito'];
            $total_creditos += $row['credito'];
            $ultima_fila = $row;
        }
    }
}

if (count($ultima_fila) > 0) {
    // Insertar renglon de totalizar
    $total_salarios_por_pagar = $total_debitos - $total_creditos;
    $resultado = array_pad(array(), 18, null); // crear un array vacio con 18 posiciones con valor = null para luego asignar descripcion y total la posicion correspondiente.
    $INDEX_FRECUENCIA = 0;
    $INDEX_EXTERNO = 1;
    $INDEX_MONEDA = 2;
    $INDEX_TIPO_CAMPO = 3;
    $INDEX_FECHA = 4;
    $INDEX_FECHA_CONTABLE = 5;
    $INDEX_NOTA = 6;
    $INDEX_SUBSIDIARIA = 7;
    $INDEX_NOTA_LINEA = 8;
    $INDEX_ID_CUENTA_CONTABLE = 9;
    $INDEX_NUMERO_CUENTA_CONTABLE = 10;
    $INDEX_DEBITO = 11;
    $INDEX_CREDITO = 12;

    foreach ([$INDEX_FRECUENCIA, $INDEX_EXTERNO, $INDEX_MONEDA, $INDEX_TIPO_CAMPO, $INDEX_FECHA, $INDEX_FECHA_CONTABLE, $INDEX_NOTA, $INDEX_SUBSIDIARIA] as $index) {
        if ($index == $INDEX_FECHA_CONTABLE) {
            $resultado[$index] = preparar_fecha_contable($ultima_fila['fecha_contable']);
        } else {
            $resultado[$index] = $ultima_fila[$index];
        }
    }

    $resultado[$INDEX_ID_CUENTA_CONTABLE] = "102";
    $resultado[$INDEX_NUMERO_CUENTA_CONTABLE] = "112101";
    $resultado[$INDEX_NOTA_LINEA] = "Total Salarios Por Pagar";
    $resultado[$INDEX_DEBITO] = "0";
    $resultado[$INDEX_CREDITO] = $total_salarios_por_pagar;

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$ln", join($delimitador, $resultado), PHPExcel_Cell_DataType::TYPE_STRING);
    $ln++;
}


// Terminar de preparar archivo .csv
$uid = uniqid();
$ruta = "excel/tmp/{$uid}";
mkdir($ruta, 0777, TRUE);

$filename = "netsuite-csv_salarios_administrativo_" . $anio . "_" . $mes . "_" . time();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
$writer->setSheetIndex(0);   // Select which sheet.
$writer->setDelimiter($delimitador);
$writer->setEnclosure('');
$writer->setLineEnding("\r\n");
ob_clean();
$path_csv = "{$ruta}/{$filename}.csv";
$writer->save($path_csv);

class Zip extends ZipArchive
{

    public function addDirectory($dir, $localname = "", $dir_original = "")
    {
        if (!$dir_original)
            $dir_original = $dir;
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file))
                $this->addDirectory($file, $localname, $dir_original);
            else
                $this->addFile($file, str_replace($dir_original, $localname, $file));
        }
    }
}

$zip = new Zip();
if (!$zip->open("{$ruta}.zip", ZIPARCHIVE::CREATE)) {
    print "Error al crear zip<br>Verifique los permisos sobre la carpeta nomina/paginas/excel/tmp/";
    exit;
}

$zip->addDirectory("{$ruta}", "$filename");
$zip->close();

ob_clean();
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=" . $filename . ".zip");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize("{$ruta}.zip"));
readfile("{$ruta}.zip");

?>
