<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql = "SELECT Descripcion,IdDepartamento FROM departamento as a GROUP BY Descripcion";

$res = $db->query($sql);

while($fila=fetch_array($res))
{
	$dep=$fila['Descripcion'];
	$departamento[$dep]=0;
}

$sql2 = "SELECT a.Descripcion as Descripcion,count(B.IdDepartamento) as total FROM departamento as a,nompersonal as B WHERE a.IdDepartamento=b.IdDepartamento GROUP BY Descripcion";

$res2 = $db->query($sql2);

while($fila=fetch_array($res2))
{
	$dep=$fila['Descripcion'];
	$departamento[$dep]=$fila['total'];
}
//--------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------------
header('Content-type:application/vnd.ms-word');
header('Content-Disposition: attachment;Filename=tiempo_departamento.doc');
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
$sql3="SELECT * FROM nomempresa";
$res3 = $db->query($sql3);
$empresa = mysqli_fetch_array($res3);
$ruta_img = "../../nomina/imagenes/";
$izq = $empresa["imagen_izq"];
$der = $empresa["imagen_der"];
echo"
<table border='1' cellspacing='1' cellpadding='1' Width='100%' >
					<tr>
						<th><img src=".$ruta_img.$izq." height='100' weight='100'></th>
						<th><H1>Tiempo por Departamento</H1></th>
						<th><img src=".$ruta_img.$der." height='100' weight='100'></th>
					</tr>
</table>
<table border='1' cellspacing='1' cellpadding='1' Width='100%' >
		              <thead>
		              <tr>
						<th>Departamento</th>
						<th>Total de Empleados</th>
					  </tr>
		              </thead>
		              <tbody>";
		                $res = $db->query($sql);
						while($fila=fetch_array($res))
						{ 
							$dep=$fila['Descripcion'];
echo"
		                <tr class='odd gradeX'>
							<td>".$fila['Descripcion']."</td>
							<td>".$departamento[$dep]."</td>							
						</tr>";
		                 // <?php                 
		                }
		              ?>
		              </tbody>
		              </table>
</div>
</body>
</html> 
