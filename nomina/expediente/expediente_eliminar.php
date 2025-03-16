<?php 
session_start();
	
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
$conexion=conexion();

//echo "CODIGO: "; echo $_REQUEST['cod_eliminar'];
if(isset($_REQUEST['cod_eliminar']))
    $codigo=$_REQUEST['cod_eliminar'];

$consulta="SELECT * FROM expediente WHERE cod_expediente_det='$codigo'";

//echo "CONSULTA: "; echo $consulta;
$resultado=query($consulta,$conexion);
$fetch=fetch_array($resultado,$conexion);
$tipo=$fetch['tipo'];
$subtipo=$fetch['subtipo'];
$cedula=$fetch['cedula'];
$numpre_tmp=$fetch['numero_accion'];

$consulta_adjunto="SELECT * FROM expediente_adjunto WHERE cod_expediente_det='$codigo'";

$resultado_adjunto=query($consulta_adjunto,$conexion);
$fetch_adjunto=fetch_array($resultado_adjunto,$conexion);

$filas_adjunto  = count($fetch_adjunto);

if ($filas_adjunto)
{
    $consulta="DELETE FROM expediente_adjunto WHERE cod_expediente_det='$codigo'";
    $resultado=query($consulta,$conexion);   
}

$consulta_documento="SELECT * FROM expediente_documento WHERE cod_expediente_det='$codigo'";

$resultado_documento=query($consulta_documento,$conexion);
$fetch_documento=fetch_array($resultado_documento,$conexion);

$filas_documento  = count($fetch_documento);

if ($filas_documento)
{
    $consulta="DELETE FROM expediente_documento WHERE cod_expediente_det='$codigo'";
    $resultado=query($consulta,$conexion);   
}

if ($tipo=="68")
{
    
    //EXPEDIENTE - ELIMINAR
    $delete_implemento="DELETE FROM expediente_implemento "
                    . "WHERE cod_expediente_det='{$codigo}'";
    $resultado_delete=query($delete_implemento,$conexion);
    
}

/*
if ($tipo=="81")
{    
    //PRESTAMO TMP - ELIMINAR
    $delete_exp="DELETE FROM nomprestamos_cabecera_tmp "
                    . "WHERE numpre='{$numpre_tmp}'";
    $resultado_delete=query($delete_exp,$conexion);
    //PRESTAMO DETALLE TMP - ELIMINAR
    $delete_exp="DELETE FROM nomprestamos_detalles_tmp "
                    . "WHERE numpre='{$numpre_tmp}'";
    $resultado_delete=query($delete_exp,$conexion);
    
}*/

//EXPEDIENTE - ELIMINAR
$consulta="DELETE FROM expediente WHERE cod_expediente_det='$codigo'";
$resultado=query($consulta,$conexion);

?>

<script type="text/javascript">
	 
             alert("Movimiento eliminado con éxito");
               
</script>
<?

activar_pagina("expediente_list.php?cedula=$cedula");

?>


