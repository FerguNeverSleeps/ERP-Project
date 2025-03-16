<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
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
//--------------------------------------------------------------------------------------------------------------------------------------
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
//--------------------------------------------------------------------------------------------------------------------------------------
header('Content-type:application/vnd.ms-word');
header('Content-Disposition: attachment;Filename=planilla_siclar.doc');
header("Pragma: no‐cache");
header("Expires: 0");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 9">
<meta name=Originator content="Microsoft Word 9">
<title>Documento de Word</title>
<!-- <LINK href="common/style.css" type=text/css rel=stylesheet> -->
<style>
@page Section1 {size:595.45pt 841.7pt; margin:1.0in 1.25in 1.0in 1.25in;mso-header-margin:.5in;mso-footer-margin:.5in;mso-paper-source:0;}
div.Section1 {page:Section1;}
@page Section2 {size:841.7pt 595.45pt;mso-page-orientation:landscape;margin:1.25in 1.0in 1.25in 1.0in;mso-header-margin:.5in;mso-footer-margin:.5in;mso-paper-source:0;}
div.Section2 {page:Section2;}
</style>
</head>
<body>
<div class=Section2>
<?php
$sql1="SELECT * FROM nomempresa";
$res2 = $db->query($sql1);
$empresa = mysqli_fetch_array($res2);
$ruta_img = "../../nomina/imagenes/";
$izq = $empresa["imagen_izq"];
$der = $empresa["imagen_der"];

echo"
<table border='1' cellspacing='1' cellpadding='1' Width='100%' >
					<tr>
						<th><img src=".$ruta_img.$izq." height='100' weight='100'></th>
						<th><H1>Planilla Siclar</H1></th>
						<th><img src=".$ruta_img.$der." height='100' weight='100'></th>
					</tr>
</table>
<style>
table{
	font-size: 0.8em;
}
</style>
<table border='1' cellspacing='1' cellpadding='1' Width='100%' >
		              <thead>
		              <tr>
		                <th>Departamento</th>
		                <th>Planilla</th>
		                <th>Posicion</th>
		                <th>Apellidos</th>
		                <th>Nombres</th>
		                <th>Cedula</th>
		                <th>F. Nacimiento</th>
		                <th>Cargo</th>
		                <th>Funcion</th>
		                <th>F. Inicio</th>
		                <th>Salario</th>
		                <th>Estado</th>
		                <th>Resolucion</th>
		              </tr>
		              </thead>
		              <tbody>";
		              	$count=0;
		              	$departamento=NULL;
		                while($fila=fetch_array($result1))
		                {
		                	$date = date_create($fila["fecnac"]);
							$nuevofn = date_format($date, "d-m-Y");
        					$fecha = date_create($fila["fecing"]);
        					$nuevofi = date_format($fecha,"d-m-Y");
        					
echo"
		                  <tr class='odd gradeX'>
		                    <td>".departamentos($data['IdDepartamento'],$total_dep,$dep,$descripcion_d)."</td>
		                    <td>".$fila['tipnom']."</td>
		                    <td>".$fila['nomposicion_id']."</td>
		                    <td>".$fila['apellidos']."</td>
		                    <td>".$fila['nombres']."</td>
		                    <td>".$fila['cedula']."</td>
		                    <td>".$nuevofn."</td>
		                    <td>".codcargo($data['codcargo'],$codcargo,$total_car,$desc_car)."</td>
		                    <td>".nomfuncion($data['nomfuncion_id'],$total_fun,$nom_id,$desc_fun)."</td>
		                    <td>".$nuevofi."</td>
		                    <td>".$fila['suesal']."</td>
		                    <td>".$fila['estado']."</td>
		                    <td>".$fila['num_decreto']."</td>
		                  </tr>";         
		                }
		             ?>
		              </tbody>
		              </table>
</div>
</body>
</html> 
