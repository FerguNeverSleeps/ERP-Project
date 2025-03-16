<?php
session_start();
ob_start();

$fecha_reg=date("Y-m-d");
$fecha_inicio= $_REQUEST['txtFechaInicio'];
$fecha_fin= $_REQUEST['txtFechaFin'];
//echo $fecha_inicio," ",$fecha_fin," ",$fecha_reg,"<br>";

require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");
$conexion  =conexion();
//echo 'Generando Conceptos ';
//$reg     =$_GET[reg];
$conceptos ="SELECT * FROM caa_conceptos";
$resul     = query($conceptos,$conexion);
while($fila_con=fetch_array($resul))
{
	//$var=&'$con_'.$fila_con[variable];
	//echo $var;
	$hora_{$fila_con[variable]}=$fila_con[variable];
	$con_{$fila_con[variable]}=$fila_con[concepto];
	if($fila_con[variable]=='regular')
		$regular=$fila_con[concepto];
	if($fila_con[variable]=='extra')
		$extra=$fila_con[concepto];
	if($fila_con[variable]=='domingo')
		$domingo=$fila_con[concepto];
	if($fila_con[variable]=='extradom')
		$extradom=$fila_con[concepto];
	if($fila_con[variable]=='nacional')
		$nacional=$fila_con[concepto];
	if($fila_con[variable]=='extranac')
		$extranac=$fila_con[concepto];
	if($fila_con[variable]=='ausencia')
		$ausencia=$fila_con[concepto];
	if($fila_con[variable]=='tardanza')
		$tardanza=$fila_con[concepto];

}

$sql ="INSERT INTO `caa_encabezado_procesar`( `fecha_reg`, `fecha_ini`, `fecha_fin`) 
VALUES ('".$fecha_reg."','".$fecha_inicio."','".$fecha_fin."')";
 query($sql, $conexion);
 $sql2="select max(id_caa_encabezado) as id_encabezado from caa_encabezado_procesar";
 $resultado=query($sql2,$conexion);
 $registro=fetch_array($resultado);
$id_encabezado=$registro["id_encabezado"];
//echo $id_encabezado," ";

$datos="SELECT
  vig_empleados.cedula,
  vig_empleados.codigo,
  vig_empleados.nombre,
  vig_empleados.estado,
  nompersonal.ficha,
  vig_asistencia.fecha,
  nomcalendarios_tiposnomina.dia_fiesta,
  vig_asistencia.id_empleado,
  vig_asistencia.inasistencia,
  vig_asistencia.tardanza,
  vig_asistencia.horas_extras_num,
  vig_asistencia.horas_trabajadas_num,
  vig_asistencia.id_accion
FROM
  vig_asistencia
  LEFT OUTER JOIN vig_empleados ON (vig_asistencia.id_empleado = vig_empleados.id_empleado)
  LEFT OUTER JOIN nomcalendarios_tiposnomina ON (vig_asistencia.fecha = nomcalendarios_tiposnomina.fecha)
  INNER JOIN nompersonal ON (vig_empleados.id_empleado = nompersonal.personal_id)
WHERE
	vig_asistencia.fecha between '".$fecha_inicio."' AND '".$fecha_fin."'
  ";
  //echo $datos,"<br>";
$rs                    = query($datos,$conexion);
//if((num_rows($rs)%2) ==0)
//{
$i                     =1;
$fichaaux              ='';
$color                 =0;
$conv                  =0 ;

//echo 'Insertar Asistencia ';

$insert ="insert into caa_procesar_asistencia (id_encabezado,personal_id,ficha,fecha,referencia,concepto) values " ;
while($fila=fetch_array($rs)) {
	//echo $fila[fecha]," ";
	if ($fila[id_accion] == 1){ //Asistio
		if ($fila[dia_fiesta] == 1) {//Domingo Trabajado
			$insertar = $insert."('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[horas_trabajadas_num] . ",".$domingo.")";
			//echo $insertar;
			$guardar = query($insertar, $conexion);
			if($fila[horas_extras_num]>0) {
				$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[horas_extras_num]  .",".$extradom.")";
				//echo $insertar," ";
				$guardar = query($insertar, $conexion);
			}

		}
		if ($fila[dia_fiesta] == 3) {//Nacional (Dia de Fiesta) Trabajado
			$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[horas_trabajadas_num] .",".$nacional.")";
			//echo $insertar;
			$guardar = query($insertar, $conexion);
			if($fila[horas_extras_num]>0) {
				$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[horas_extras_num]  .",".$extranac.")";
				//echo $insertar," ";
				$guardar = query($insertar, $conexion);
			}

		}
		if ($fila[dia_fiesta] == 0) {// Ordinario (regular) Trabajado
			$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "','" . $fila[horas_trabajadas_num] ."',".$regular.")";
			//echo $insertar," <br>";
			$guardar = query($insertar, $conexion);
			if($fila[horas_extras_num]>0) {
				$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[horas_extras_num]  .",".$extra.")";
				//echo $insertar," ";
				$guardar = query($insertar, $conexion);
			}
		}
		if ($fila[tardanza] >0) {   //Tardanzas
			$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[tardanza] .",".$tardanza.")";
			//echo $insertar;
			$guardar = query($insertar, $conexion);

		}



	}
	if ($fila[id_accion] > 1 and $fila[id_accion] < 5){ //No Asistio
		$insertar = $insert. "('" . $id_encabezado . "','" . $fila[id_empleado] . "','" . $fila[ficha] . "','" . $fila[fecha] . "'," . $fila[inasistencia] .",".$ausencia.")";
		//echo $insertar;
		$guardar = query($insertar, $conexion);

	}
	//echo $guardar,"<br>";

}

header("Location:importar_asistencia.php");

echo 'Termino Generando Conceptos';
?>