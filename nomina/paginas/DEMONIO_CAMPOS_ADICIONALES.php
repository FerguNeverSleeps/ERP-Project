<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];

include("../lib/common.php");
include ("func_bd.php") ;
?>


<?php 
$host_ip = $_SERVER['REMOTE_ADDR'];

$ficha = $_GET['ficha'];



$consulta_campos_adicionales="SELECT * FROM nomcampos_adicionales 
                              WHERE id NOT IN
                                (SELECT id FROM nomcampos_adic_personal 
                                WHERE 
                                ficha='$ficha' AND tiponom='$_SESSION[codigo_nomina]')";
$resultado_campos_adicionales=sql_ejecutar($consulta_campos_adicionales);

if(num_rows($resultado_campos_adicionales)==0)
{
	
	echo "<SCRIPT type='text/javascript'>
	alert('No tiene Campos Adicionales por Generar');
	</SCRIPT>";
}	
else
{
     while($fila_campos_adicionales = fetch_array($resultado_campos_adicionales))
    {       
   

            $consulta_adicional_personal="INSERT INTO nomcampos_adic_personal
                                        (ficha, 
                                        id, 
                                        valor, 
                                        mascara, 
                                        tipo, 
                                        codorgd,
                                        codorgh, 
                                        ee, 
                                        tiponom)
                                        VALUES  
                                        ('{$ficha}',"
                                        . "'{$fila_campos_adicionales[id]}',"
                                        . "'{$fila_campos_adicionales[valdefecto1]}',"
                                        . "NULL, "
                                        . "'{$fila_campos_adicionales[tipo]}',"
                                        . "NULL,"
                                        . "NULL,"
                                        . "NULL,"
                                        . "'{$_SESSION[codigo_nomina]}')";
            $rs1 = sql_ejecutar($consulta_adicional_personal);

    }
    
    
    
    
    $consulta="INSERT INTO log_transacciones VALUES ('', 'generacion de campos adicionales', '".date("Y-m-d H:i:s")."', 'Personal-campos adicionales', 'DEMONIO_CAMPOS_ADICIONALES.php', 'generar', '$ficha', '$_SESSION[nombre]','$host_ip')";
    $result2=sql_ejecutar($consulta);
    
    echo "<SCRIPT type='text/javascript'>
	alert('Campos Adicionales Generados Con Exito');
	</SCRIPT>";
    
}
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	document.location.href=\"otrosdatos_integrantes.php?txtficha=".$ficha."\"
	</SCRIPT>";


?>
