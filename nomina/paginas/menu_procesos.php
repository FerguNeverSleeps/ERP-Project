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
                               
                                <td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=7" target="_self" ><img src="../imagenes/sipe.jpg" title="Generar TXT de <? echo $termino ?> para el SIPE" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=1" target="_self" ><img src="../imagenes/bancogeneral.jpg" title="Generar ACH de <? echo $termino ?> para el General" border="0" align="absmiddle" class="icon" /></a></div></td>
                                <td width="10" height="10"align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=12" target="_self" ><img src="../imagenes/PEACHTREE.jpg"  title="Generar TXT de <? echo $termino ?> para PEACHTREE " border="0" align="absmiddle" class="icon" /></a></div></td>

    <td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=13" target="_self" ><img src="../imagenes/banismo.gif"  title="Generar Excel de <? echo $termino ?> para BANISTMO " border="0" align="absmiddle" class="icon" /></a></div></td>
                         
                            </tr>
                            <tr>
                                <td height="32"><div align="center">SIPE</div></td>
                                <td height="32"><div align="center">ACH</div></td>
                                <td height="32"><div align="center">PEACHTREE</div></td>
<td height="32"><div align="center">EXCEL</div></td>
</tr>
<tr>
<tr>                                
<td width="0" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina4.php" target="_self" ><img src="../imagenes/contab.jpg" title="Contabilizar Planillas" border="0" align="absmiddle" class="icon" /></a></div></td>
                                 
                            
<td width="0" align="center" valign="middle" class="icon"> <div align="center"><a href="../procesos/filtro_nomina.php?opcion=11" target="_self" > <img src="../imagenes/email.jpg"  title="Generar Correos <? echo $termino ?>" border="0" align="absmiddle" class="icon" /></a></div></td>
<td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=14" target="_self" ><img src="../imagenes/sipe.jpg" title="Generar TXT de <? echo $termino ?> para el SIPE" border="0" align="absmiddle" class="icon" /></a></div></td>
<td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=15" target="_self" ><img src="../imagenes/mef.png" title="Generar TXT de <? echo $termino ?> para MEF" border="0" align="absmiddle" class="icon" /></a></div></td>
   
</tr>
<tr>                                
<td height="32"><div align="center">Contabilizaci&oacute;n  Planilla</div></td>
<td height="32"><div align="center">Recibos de pago a correo</div></td>
<td height="32"><div align="center">EXCEL</div></td>
<td height="32"><div align="center">MEF</div></td>
</tr>

<tr>                                
<td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=16" target="_self" ><img src="../imagenes/35.png"  title="Generar Excel de <? echo $termino ?> para BANISTMO " border="0" align="absmiddle" class="icon" /></a></div></td>
<td width="10" height="10" align="center" valign="middle" class="icon"><div align="center"><a href="../procesos/filtro_nomina.php?opcion=17" target="_self" ><img src="../imagenes/35.png"  title="Generar Excel de <? echo $termino ?> para BANISTMO " border="0" align="absmiddle" class="icon" /></a></div></td>
</tr>
<tr>                                
<td height="32"><div align="center">EXCEL PAGO POR SOBRE </div></td>
<td height="32"><div align="center">EXCEL PAGO POR SOBRE SIN CTA</div></td>
</tr>
</table>
</td>
</tr>
</table>
<p>&nbsp;</p>
</form>
</body>
</html>
