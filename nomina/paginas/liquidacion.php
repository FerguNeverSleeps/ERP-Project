<?php 
session_start();
ob_start();
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>


<script type="text/javascript">


function Aceptar(variable)
{
	//opener.document.forms[0].textfield.value=variable;
	//window.opener.SumarCampoFormula();
	opener.document.location.href="movimientos_nomina_liquidaciones.php?codigo_nomina="+document.frmPrincipal.codnomm.value+"&codt="+document.frmPrincipal.codtt.value+"&pag="+document.frmPrincipal.numpagg.value+"&ficha="+variable
	CerrarVentana();
}

function MarcarTodos()
{
	if (document.frmPrincipal.marcar_todos.value==1)
		{Opcion=true;document.frmPrincipal.marcar_todos.value=0;}
	else
		{Opcion=false;document.frmPrincipal.marcar_todos.value=1;}

	for(i=0; ele=document.frmPrincipal.elements[i]; i++){  		
		if (ele.name=='chkCodigo[]')
			{ele.checked =Opcion;}			
	}	
}

function CerrarVentana(){
	javascript:window.close();
}

function enviar(op)
{
	document.frmPrincipal.enviarr.value=op;
	document.frmPrincipal.submit();
}
</script>


<?
include ("../header.php");
include("../lib/common.php");
include ("func_bd.php");
?>
<p>

<?php 
$conexion=conexion();

if($_POST['enviarr']==1)
{
	$consulta = "UPDATE nompersonal 
                SET cod_tli_tmp='".$_POST['tipoliq']."', 
                fecharetiro_tmp='".fecha_sql($_POST['fechaD'])."',
                motivo_liq_tmp='".$_POST['motivoliq']."', 
                preaviso_tmp='".$_POST['preaviso']."',
                articulo_122_tmp='".$_POST['articulo_122']."' ,
                periodo_vacac_pagado='".$_POST['periodo_vacac_pagado']."' ,
                indemnizacion_especial='".$_POST['indemnizacion_especial']."' 
                WHERE ficha = '".$_POST['ficha']."' AND tipnom = '".$_POST['tiponom']."' ";
	$resulta2 = query($consulta,$conexion);
	?>
	<script>
	alert ("DATOS AGREGADOS CON EXITO!!")
	window.close();
	</script>
	<?php
}

$tiponom = $_GET['tipo_nomina'];
$ficha = $_GET['ficha'];

$conexion=conexion();

$consulta_personal= "SELECT pe.fecharetiro_tmp, pe.cod_tli_tmp, pe.motivo_liq_tmp, pe.preaviso_tmp, pe.articulo_122_tmp, periodo_vacac_pagado,indemnizacion_especial"
        . " FROM  nompersonal as pe "
        . " WHERE pe.tipnom='".$tiponom."' AND pe.ficha='".$ficha."' ";
$resultado_personal = query($consulta_personal,$conexion);
$fetch_personal = fetch_array($resultado_personal);
$fecharetiro_tmp=$fetch_personal['fecharetiro_tmp'];
$cod_tli_tmp=$fetch_personal['cod_tli_tmp'];
$motivo_liq_tmp=$fetch_personal['motivo_liq_tmp'];
$preaviso_tmp=$fetch_personal['preaviso_tmp'];
$articulo_122_tmp=$fetch_personal['articulo_122_tmp'];
$periodo_vacac_pagado=$fetch_personal['periodo_vacac_pagado'];
$indemnizacion_especial=$fetch_personal['indemnizacion_especial'];

if($fecharetiro_tmp != "")
{
    $fecha = date("d/m/Y",strtotime($fecharetiro_tmp));
}
else{
    $fecha = date("d/m/Y");
}
if($preaviso_tmp == "Si")
{
    $preaviso_si = "selected";
    $preaviso_no = "";
}
else{
    $preaviso_si = "";
    $preaviso_no = "selected";
}


if($articulo_122_tmp == "Si")
{
    $articulo_122_si = "selected";
    $articulo_122_no = "";
}
else{
    $articulo_122_si = "";
    $articulo_122_no = "selected";
}
if($periodo_vacac_pagado == 1)
{
    $periodo_vacac_pagado_si = "selected";
    $periodo_vacac_pagado_no = "";
}
else{
    $periodo_vacac_pagado_si = "";
    $periodo_vacac_pagado_no = "selected";
}
if($indemnizacion_especial == 1)
{
    $indemnizacion_especial_si = "selected";
    $indemnizacion_especial_no = "";
}
else{
    $indemnizacion_especial_si = "";
    $indemnizacion_especial_no = "selected";
}
?>
</p>
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
    <table width="100%" class="tb-tit">
        <tr>
            <td class="row-br">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="100%">
                            <strong><font color="#000066">
                                     Datos de liquidaci&oacute;n </strong></td>
                        <td width="2%" align="right">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                  <td>
                                                    <div align="right">
                                                      <?php btn('cancel','CerrarVentana();',2,'Salir') ?>
                                                    </div>
                                  </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <TABLE width="100%" class="table table-striped"  cellspacing="0" cellpadding="0">

        <tr> 
            <div class="form-group">
                <td width="35%" height="21" align="right"> 
                <div align="left">Fecha de liquidaci&oacute;n</div>	  </td>
                <TD  width="65%"><INPUT type="text" name="fechaD" id="fechaD" size="15" maxlength="12" value="<? echo $fecha; ?>">&nbsp;<input name="d_fecha" type="image" id="d_fecha" src="lib/jscalendar/cal.gif">
                <script type="text/javascript">Calendar.setup({inputField:"fechaD",ifFormat:"%d/%m/%Y",button:"d_fecha"});</script></TD>
            </div>
        </tr>	
        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                Tipo de liquidaci&oacute;n
                </div>
            </td>
            <TD width="65%">
                <select name="tipoliq" id="tipoliq" class="form-control">
                <?php 
                $consulta = "SELECT * FROM nomliquidaciones";
                $resultado = query($consulta,$conexion);
                while($fetch = fetch_array($resultado))
                {
                    $tli_selected = $fetch['cod_tli'] == $cod_tli_tmp? "selected": "";

                ?>
                <OPTION value="<?php echo $fetch['cod_tli'];?>" <?= $tli_selected ?>><?php echo $fetch['des_tli'];?></OPTION>
                <?php
                }
                ?>
                </select>
            </TD>
        </tr>	

        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                Motivo de liquidaci&oacute;n
                </div>
            </td>
            <TD width="65%">
                <select name="motivoliq" id="motivoliq" class="form-control" >
                    <?php 
                    $consulta = "SELECT * FROM nom_motivos_retiros";
                    $resultado = query($consulta,$conexion);
                    while($fetch = fetch_array($resultado))
                    {
                        $liq_selected = $fetch['codigo'] == $motivo_liq_tmp? "selected": "";
                    ?>
                    <OPTION value="<?php echo $fetch['codigo'];?>" <?= $liq_selected ?> ><?php echo $fetch['descripcion'];?></OPTION>
                    <?php
                    }
                    ?>
                </select>
            </TD>
        </tr>	

        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                ¿Pagar Preaviso?
                </div>
            </td>
            <TD width="65%">
                <select name="preaviso" id="preaviso" class="form-control" >
                    <option>Seleccione</option>
                    <OPTION value="Si" <?php echo $preaviso_si; ?>>Si</OPTION>
                    <OPTION value="No" <?php echo $preaviso_no; ?>>No</OPTION>
                </select>
            </TD>
        </tr>
        
        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                Art. 222
                </div>
            </td>
            <TD width="65%">
                <select name="articulo_122" id="articulo_122" class="form-control" >
                    <option>Seleccione</option>
                    <OPTION value="Si" <?php echo $articulo_122_si; ?>>Si</OPTION>
                    <OPTION value="No" <?php echo $articulo_122_no; ?>>No</OPTION>
                </select>
            </TD>
        </tr>

        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                ¿&Uacute;ltimo Periodo Vacaciones Pagado?
                </div>
            </td>
            <TD width="65%">
                <select name="periodo_vacac_pagado" id="periodo_vacac_pagado" class="form-control" >
                    <option>Seleccione</option>
                    <OPTION value="1" <?php echo $periodo_vacac_pagado_si; ?>>Si</OPTION>
                    <OPTION value="0" <?php echo $periodo_vacac_pagado_no; ?>>No</OPTION>
                </select>
            </TD>
        </tr>

        <tr> 
            <td width="35%" height="21" align="right"> 
                <div align="left">
                ¿Indemnización Especial?
                </div>
            </td>
            <TD width="65%">
                <select name="indemnizacion_especial" id="indemnizacion_especial" class="form-control" >
                    <option>Seleccione</option>
                    <OPTION value="1" <?php echo $indemnizacion_especial_si; ?>>Si</OPTION>
                    <OPTION value="0" <?php echo $indemnizacion_especial_no; ?>>No</OPTION>
                </select>
            </TD>
        </tr>

        <tr border="0"> 
        <td colspan="2" align="center" width="35%" height="21" align="right" >
        <input type="hidden" name="enviarr">
        <input type="hidden" name="tiponom" value="<?php echo $tiponom;?>">
        <input type="hidden" name="ficha" value="<?php echo $ficha;?>">
        <?php btn('ok','enviar(1);',2); ?>
        </td>
        </tr>	
    </table>
</form>
</body>
</html>
