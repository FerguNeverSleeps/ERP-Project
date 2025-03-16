<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla13.xlsx");

$sql = "SELECT nomposicion_id,IdDepartamento,apellidos,nombres,cedula,fecnac,fecing,suesal,estado,num_decreto,tipnom,codcargo,nomfuncion_id FROM nompersonal";
$result1 = $db->query($sql, $conexion);

//------------------------------------------------------------
$sql3 = "SELECT * FROM departamento";
$result3 = $db->query($sql3, $conexion);
$i=0;
while($departamentos=mysqli_fetch_array($result3))
{
    $dep[$i]=$departamentos['IdDepartamento'];
    $descripcion_d[$i]=$departamentos['Descripcion'];
      $i++;
}
$total_dep=$i;
//------------------------------------------------------------
$sql4 = "SELECT * FROM nomcargos";
$result4 = $db->query($sql4, $conexion);
$i=0;
while($cargos=mysqli_fetch_array($result4))
{
    $codcargo[$i]=$cargos['cod_car'];
    $desc_car[$i]=$cargos['des_car'];
      $i++;
}
$total_car=$i;
$j=0;
while($j<$total_car)
{
     $codcargo[$j];
     $desc_car[$j];
       $j++;
}
//------------------------------------------------------------
$sql2 = "SELECT * FROM nomfuncion";
$result2 = $db->query($sql2, $conexion);
$i=0;
while($funcion=mysqli_fetch_array($result2))
{
    $nom_id[$i]=$funcion['nomfuncion_id'];
      $desc_fun[$i]=$funcion['descripcion_funcion'];
      $i++;
}
$total_fun=$i;
$j=0;
while($j<$total_fun)
{
     $nom_id[$j];
     $desc_fun[$j];
       $j++;
}
//--------------------------------------------------------------
function departamentos($id,$total_dep,$dep,$descripcion_d){
      $exist=0;
      $per_dep=$id;
      // esto es para saber si el departamento existe
      $j=0;
      while($j< $total_dep)
      {
          if($per_dep==$dep[$j])
          {
            $exist=1;
            $desc=$descripcion_d[$j];
          } 
            $j++;
      }
      if($exist==0)
      {
            return 'SIN ASIGNAR';
      }
      if($exist==1)
      {
            return $desc;
      }
}
function codcargo($id,$codcargo,$total_car,$desc_car)
{
      $exist_car=0;
      $per_car=$id;
      $j=0;
      while($j<$total_car)
      {
            if($per_car==$codcargo[$j])
            {
            $exist_car=1;
            $cargo_persona=$desc_car[$j];
          }
            $j++;
      }
      if($exist_car==0)
      {
            return "SIN ASIGNAR";
      }
      if($exist_car==1)
      {
            return $cargo_persona;
      }
}
function nomfuncion($id,$total_fun,$nom_id,$desc_fun)
{
      $exist_fun=0;
      $per_fun=$id;
      $j=0;
      while($j<$total_fun)
      {
            if($per_fun==$nom_id[$j])
            {
            $exist_fun=1;
            $funcion_persona=$desc_fun[$j];
          }
            $j++;
      }
      if($exist_fun==0)
      {
            return "SIN ASIGNAR";
      }
      if($exist_fun==1)
      {
            return $funcion_persona;
      }
}
//---------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Planilla Siclar");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('M1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$borders = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        )
      ),
      'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 12,
        'name'  => 'Verdana'
    )
    );
$objPHPExcel->getActiveSheet()->getStyle('B2:N2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Departamento");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Planilla");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Posicion");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Apellidos");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Nombres");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Cedula");
$objPHPExcel->getActiveSheet()->SetCellValue('H2', "F. Nacimiento");
$objPHPExcel->getActiveSheet()->SetCellValue('I2', "Cargo");
$objPHPExcel->getActiveSheet()->SetCellValue('J2', "Función");
$objPHPExcel->getActiveSheet()->SetCellValue('K2', "F. Inicio");
$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Salario");
$objPHPExcel->getActiveSheet()->SetCellValue('M2', "Estado");
$objPHPExcel->getActiveSheet()->SetCellValue('N2', "Resolución");
//Fin encabezado tabla
$rowCount = 3;
$count=0;
$departamento=NULL;

while($row = fetch_array($result1)){
      
      $date = date_create($row['fecnac']);
      $nuevofn = date_format($date,'d-m-Y');
      $fecha = date_create($row['fecing']);
      $nuevofi = date_format($fecha,'d-m-Y');
      //----------------------------------------
      
      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, departamentos($data['IdDepartamento'],$total_dep,$dep,$descripcion_d));
      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['tipnom']);
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['nomposicion_id']);
      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['apellidos']);
      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['nombres']);
      $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['cedula']);
      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $nuevofn);
      $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, codcargo($data['codcargo'],$codcargo,$total_car,$desc_car));
      $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, nomfuncion($data['nomfuncion_id'],$total_fun,$nom_id,$desc_fun));
      $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $nuevofi);
      $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row['suesal']);
      $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row['estado']);
      $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row['num_decreto']);
      $borders = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        )
      ),
    );
      $desde = "B".$rowCount;
      $hasta = "N".$rowCount;
      $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
      $rowCount++;
      $count++;
}

$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, '=CONTAR(N3:N'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Planilla Siclar');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="planilla_siclar.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>