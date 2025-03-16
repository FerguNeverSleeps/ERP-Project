<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
include ("../../nomina/paginas/funciones_nomina.php");


$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla15.xlsx");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

function fechas($fecha,$formato)
{
    $meses = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
    if (strlen($fecha)<2) {
        return "";
    }
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
         case 3:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia."-".$meses[$mes-1]. "-".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}

$ficha=$_GET['ficha'];
$cedula=$_GET['cedula'];

$conexion=conexion();
$var_sql="select * from nomempresa";
$rs = query($var_sql,$conexion);
$var_encabezado=$row_rs['nom_emp'];
$var_imagen_izq=$row_rs['imagen_izq'];
$date1=date("Y-m-d");
$date2=date("h:m:s");

$objPHPExcel->getActiveSheet()->SetCellValue('I3', fechas($date1,3));
$objPHPExcel->getActiveSheet()->SetCellValue('I4', $date2);

$conexion=conexion();
$tipo=$_SESSION['codigo_nomina'];
$consulta="select * from nompersonal where ficha='{$ficha}' and cedula='{$cedula}' ";
$resultado=query($consulta,$conexion);
$rc=fetch_array($resultado);


$objPHPExcel->getActiveSheet()->SetCellValue('A15', fechas($rc['fecing'],3));
$objPHPExcel->getActiveSheet()->SetCellValue('A13', $rc['apenom']);
$objPHPExcel->getActiveSheet()->SetCellValue('E13', $rc['cedula']);

$query="select * from nomcargos where cod_car = '".$rc['codcargo']."'";
$resultado1 = query($query,$conexion);
$cargo1 = fetch_array($resultado1);
$cargo=$cargo1['des_car'];


$objPHPExcel->getActiveSheet()->SetCellValue('C15', $cargo);

if($rc[nacionalidad]==0){
  $nacio='Extranjero';
}Else{
  $nacio='Panameño';
}

$query="select * from tiposangre where IdTipoSangre = '".$rc['IdTipoSangre']."'";
$resultado2 = query($query,$conexion);
$aux5 = fetch_array($resultado2);
$tiposangre=$aux5['Descripcion'];

$objPHPExcel->getActiveSheet()->SetCellValue('A21', $tiposangre);



$objPHPExcel->getActiveSheet()->SetCellValue('A17', $nacio);
$objPHPExcel->getActiveSheet()->SetCellValue('D17', $rc['sexo']);
$objPHPExcel->getActiveSheet()->SetCellValue('F17', $rc['estado_civil']);


$edad=antiguedad($rc['fecnac'],date('Y-m-d'),"A");

if ($edad!="") {
    $edad.=" años";
}
$objPHPExcel->getActiveSheet()->SetCellValue('A19', fechas($rc['fecnac'],3));
$objPHPExcel->getActiveSheet()->SetCellValue('D19', $edad);
$objPHPExcel->getActiveSheet()->SetCellValue('F19', $rc['lugarnac']);



$aux = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"A"));
if ($aux > 1) {
  $aux = $aux." años, ";
}
else
{
  $aux = $aux." año, ";
}

$aux2 = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"M"));

if ($aux2 > 1) {
  $aux2 = $aux2." meses y ";
}
else
{
  $aux2 = $aux2." mes y ";
}

$aux3 = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"D"));

$aux4 = $aux3 % 30;
if ($aux4 > 1) {
  $aux4 = $aux4." dias";
}
else
{
  $aux4 = $aux4." dia";
}


$objPHPExcel->getActiveSheet()->SetCellValue('D21',$aux.$aux2.$aux4);



$objPHPExcel->getActiveSheet()->SetCellValue('A23', $rc['telefonos']);
$objPHPExcel->getActiveSheet()->SetCellValue('D23', utf8_decode($rc['email']));

$objPHPExcel->getActiveSheet()->SetCellValue('A25', $rc['direccion']);
$objPHPExcel->getActiveSheet()->SetCellValue('E15', $ficha);





if($rc['foto']!='fotos/' && $rc['foto']!=''){
  if(file_exists("../../nomina/paginas/".$rc['foto'])){
    $foto = "../../nomina/paginas/".$rc['foto'];
  }else{
    $foto = "../../nomina/paginas/fotos/silueta.gif";
    //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
  }
}else{
    //$this->Image("../paginas/fotos/silueta.gif" ,160,30,43);
    $foto = '../../includes/assets/img/profile/profile_black.jpg';
    //$this->Image('../../includes/assets/img/profile/profile.png',160,30,43);
    //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
  }
// Provide path to your logo file
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');

$objDrawing->setPath($foto);  //setOffsetY has no effect
$objDrawing->setCoordinates('H6');
$objDrawing->setHeight(240); // logo height
$objDrawing->setWidth(140); // logo height
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());


header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="datos_personales.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');