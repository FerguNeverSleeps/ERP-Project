<?php
/***********************************************/
/* Parámetro de conexión base de datos emisora */
/***********************************************/
$db_user1  = 'root';
$db_pass1  = '4m4x0n14';
$db_name1  = 'itesa_rrhh';
$db_host1  = 'localhost';

/**************************************/
/* Conexión con base de datos emisora */
/**************************************/
$conexion1 =  mysqli_connect($db_host1,$db_user1,$db_pass1,$db_name1) or die('Error de conexión con base de datos emisora');
mysqli_query($conexion, 'SET CHARACTER SET utf8');


/***************************************/
/* Se buscan los registros en la tabla */
/***************************************/
$select1     = "SELECT periodo_fin, codnom, codtip from nom_nominas_pago";
$resultados1 = mysqli_query( $conexion1, $select1 ) or die(mysqli_error($conexion1));

/***************************************************/
/* Contadores de registros actualizados/insertados */
/***************************************************/
$act = $ins = 0;

/***************************************************/
/*            Interfaz con Bootstrap3              */
/***************************************************/
?>

<?
/**************************************************************************************/
/* Se hace un bucle con los registros obtenidos en la tabla leída de la base de datos */
/**************************************************************************************/
while ($registros1  = mysqli_fetch_array($resultados1)) 
{	
    $fecha_pago=$registros1[periodo_fin];
    $cod_planilla=$registros1[codnom];
    $tipo_planilla=$registros1[codtip];
    
    $actualizar = "UPDATE salarios_acumulados
                SET fecha_pago='{$fecha_pago}'
                WHERE (cod_planilla = '".$cod_planilla."' AND tipo_planilla = '".$tipo_planilla."')";
    $actualizado = mysqli_query( $conexion1, $actualizar ) or die(mysqli_error($conexion1));
		

}

?>
