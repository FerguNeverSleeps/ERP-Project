<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
include "../lib/common.php";
?>
<html class="fondo">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link href="../estilos.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <?php//titulo_mejorada("Procesos","","","");?>
        <form id="form1" name="form1" method="post" action="">
            <table width="100%" border="0">  
                <tr class="tb-tit">
                    <td height="10" class=""><font color="#000066"><strong>&nbsp;Procesos</strong></font></td>
                </tr>
                <tr>
                    <td width="10" height="10" valign="middle" class="icon"><table width="10" border="0">
                            <tr>
                               
                                <td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=15" target="_self" ><img src="../imagenes/txt-icon.png" title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=16" target="_self" ><img src="../imagenes/txt-icon.png" title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10"align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=17" target="_self" ><img src="../imagenes/txt-icon.png"  title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10"align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=18" target="_self" ><img src="../imagenes/txt-icon.png"  title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10"align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=19" target="_self" ><img src="../imagenes/txt-icon.png"  title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10"align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=20" target="_self" ><img src="../imagenes/txt-icon.png"  title="" border="0" align="absmiddle" class="icon" /></a></div></td>
                            </tr>
                            <tr>
                                <td height="32"><div align="center">TXT PLANILLA</div></td>
                                <td height="32"><div align="center">ACH GASTOS</div></td>
                                <td height="32"><div align="center">ACH REGULAR</div></td>
                                <td height="32"><div align="center">TXT CHEQUE EMPLEADO</div></td>
                                <td height="32"><div align="center">TXT CHEQUE ACREEDOR</div></td>
                                <td height="32"><div align="center">TXT RECIBO EMPLEADO</div></td>
                            </tr>
<tr>

</table>
</td>
</tr>
</table>
<p>&nbsp;</p>
</form>
</body>
</html>
