<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<?php    
    $registro_id=$_POST['registro_id'];
    
    $sql="DELETE FROM `solicitudes_tipos` WHERE `id_solicitudes_tipos` = '$registro_id'";

    $insertado = sql_ejecutar($sql);
    if($insertado) 
    {           
        echo "<script>alert('SOLICITUD ELIMINADA EXITOSAMENTE');location.href='config_solicitudes.php';</script>";
    }
    else
    {
        echo "<script>alert('HUBO UN ERROR AL ELIMINAR SU SOLICITUD');location.href='config_solicitudes.php';</script>";     
    }

?>