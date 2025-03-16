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
$select1     = "SELECT ficha, id, valor from nomcampos_adic_personal "
             . "WHERE id = '2'";
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
    $ficha=$registros1[ficha];
    $id=$registros1[id];
    $valor=$registros1[valor];
    
    $capataz=0;
    if($valor=="Si")
    {
        $capataz=1;
    }
    
    $actualizar = "UPDATE reloj_detalle
                SET capataz='{$capataz}'
                WHERE (ficha = '".$ficha."' AND `fecha` >=  '2019-04-16' AND `fecha` <=  '2019-05-15')";
    $actualizado = mysqli_query( $conexion1, $actualizar ) or die(mysqli_error($conexion1));
		

}

?>
