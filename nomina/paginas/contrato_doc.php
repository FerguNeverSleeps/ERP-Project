<?php
require_once 'phpword/PHPWord.php';

require_once('../lib/config.php'); // general.config.inc.php
require_once('../lib/common.php'); // funciones
include('../fpdf/numerosALetras.class.php');
//include ("../paginas/func_bd.php");
//include ("../paginas/funciones_nomina.php"); 

$registro_id= (int) $_GET['registro_id']; 
$tipnom= (int) $_GET['tipn'];
// $est=$_GET['est'];

$numeroTexto = new numerosALetras();
$conexion = conexion();

# Consultar datos del empleado
$sql='SELECT cedula, apenom, estado_civil, direccion, telefonos, email, ficha, fecing, codcargo, 
             fecharetiro, suesal, nombres, apellidos, nacionalidad, nominstruccion_id,
             DATE_FORMAT(fecing, "%d/%m/%Y") as fecha_ingreso  
      FROM nompersonal WHERE ficha='.$registro_id.' AND tipnom='.$tipnom;
$res=query($sql, $conexion);
$fila=fetch_array($res);
$cedula = $fila['cedula'];
$nombre = str_replace(',', '', $fila['apenom']); 
$estado_civil = $fila['estado_civil'];
$direccion_empleado = ucwords(mb_strtolower($fila['direccion']));
$codcargo = $fila['codcargo'];
$fecha_ingreso = $fila['fecha_ingreso'];
$sueldo = $fila['suesal']; // sueldo basico
$sueldoletras = strtoupper(trim($numeroTexto->convertir($sueldo, '2')));

// Datos del cargo del trabajador
$sql2  =  'SELECT des_car FROM nomcargos WHERE cod_car='.$codcargo;
$res2  =  query($sql2, $conexion);
$fila2 =  fetch_array($res2);
$cargo = mb_strtolower($fila2['des_car']);

// Datos de la empresa 
$sql3  = 'SELECT dir_emp, ciu_emp, edo_emp FROM nomempresa';
$res3  = query($sql3, $conexion);
$fila3 = fetch_array($res3);
$direccion_emp = ucwords(mb_strtolower($fila3['dir_emp']));
$ciudad_emp = ucwords(mb_strtolower($fila3['ciu_emp']));
$estado_emp = ucwords(mb_strtolower($fila3['edo_emp']));
$direccion_completa_emp = $direccion_emp.'. '.$ciudad_emp.". Estado ".$estado_emp;

$PHPWord = new PHPWord();

$document = $PHPWord->loadTemplate('phpword/TemplateContrato.docx');

$document->setValue('ciudadano', $nombre);
$document->setValue('cedula', number_format($cedula,0,',','.'));
$document->setValue('estado_civil', $estado_civil);
$document->setValue('cargo', $cargo);
$document->setValue('direccion', $direccion_emp);
$document->setValue('ciudad', $ciudad_emp);
$document->setValue('fechaingre', $fecha_ingreso);
$document->setValue('sueldoletras', $sueldoletras);
$document->setValue('sueldo',   number_format($sueldo, 2, ',', '.'));
$document->setValue('quincena', number_format(($sueldo/2), 2, ',', '.'));
$document->setValue('direccion_empresa',  $direccion_completa_emp);
$document->setValue('direccion_empleado', $direccion_empleado);
$document->setValue('ciudad', $ciudad_emp);

// Save File
$temp_file_uri = tempnam('', 'contrato.docx');
$document->save($temp_file_uri);

// Download the file:
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=ContratoTrabajador'.$cedula.'.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($temp_file_uri));
flush();
readfile($temp_file_uri);
unlink($temp_file_uri); // deletes the temporary file
exit;
?>