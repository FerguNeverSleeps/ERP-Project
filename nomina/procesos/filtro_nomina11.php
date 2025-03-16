<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";
$conexion=conexion();

$opcion=11;
?>

<script>
function direccionar_txt()
{
    var opcion=document.getElementById('opcion');

    document.getElementById("accion_direccionar").style.display="none";
    document.getElementById("tabla_seleccion").style.display="none";
    document.getElementById("cargando").style.display="";

    if(opcion.value==1)
        document.location.href="txt_ach.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==2)
        document.location.href="txt_LPH_mens.php?mes="+document.form1.mes.value+"&anio="+document.form1.anio.value   
    else if(opcion.value==3)
        document.location.href="txt_APP_mens.php?mes="+document.form1.mes.value+"&anio="+document.form1.anio.value+"&tipo="+document.form1.tipo.value
    else if(opcion.value==4)
        document.location.href="txt_mercantil.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==5)
        document.location.href="orden_nomina.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==6)
        document.location.href="contabilizar_nomina.php?codigo_nomina="+document.form1.cboTipoNomina.value+"&cod_banco="+document.form1.banco.value
    else if(opcion.value==7)
        document.location.href="txt_sipe.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==8)
        document.location.href="txt_BFC.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==9)
        document.location.href="txt_BOD.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==10)
        document.location.href="txt_CA.php?mes="+document.form1.mes.value+"&anio="+document.form1.anio.value+"&tipo="+document.form1.tipo.value
    else if(opcion.value==11)
        document.location.href="rpt_recibo_pago_correo_pdf.php?codigo_nomina="+document.form1.cboTipoNomina.value
	else if(opcion.value==12)
        document.location.href="txt_peachtree.php?codigo_nomina="+document.form1.cboTipoNomina.value
	else if(opcion.value==13)
        document.location.href="xls_banitsmo.php?codigo_nomina="+document.form1.cboTipoNomina.value
	else if(opcion.value==14){
		if(($('select#nom').val()==null)||( $('#mesano').val()=='undefined'))
		{
			alert("POR FAVOR NO DEJE CAMPOS SIN SELECCIONAR!!!");
			return false;
		}
		$('#nomina').val($('select#nom').val());
		$('#frecuencia').val($('select#frec').val());
		document.form1.action="xls_sipe.php";
		document.form1.submit();
	}
    else if(opcion.value==15)
        document.location.href="txt_especial_ach.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==16)
        document.location.href="txt_especial_gastos.php?codigo_nomina="+document.form1.cboTipoNomina.value+"&concepto="+document.form1.acum.value
    else if(opcion.value==17)
        document.location.href="txt_especial_regular.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==18)
        document.location.href="txt_cheque_empleado.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==19)
        document.location.href="txt_cheque_acreedor.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==20)
        document.location.href="txt_recibo_empleado.php?codigo_nomina="+document.form1.cboTipoNomina.value
    else if(opcion.value==21)
    {
        //console.log("codnom="+document.form1.cboTipoNomina.value+" codtip="+document.form1.codt.value);
        document.location.href='comprobante_pago_correo_xls.php?codnom='+document.form1.cboTipoNomina.value+'&codtip='+document.form1.codt.value; 
    }
        
}
</script>
<form id="form1" name="form1" method="post" action="">
<?titulo_mejorada("Parámetros","","","../paginas/home.php")?>

<?
if(($opcion==2)||($opcion==3))
{
?>
    <table width="100%" height="229" align="center" border="0">
    <tr>
    <td width="489" height="190" >
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <table width="520" align="right" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle">
    <div align="left">Mes: <font size="2" face="Arial, Helvetica, sans-serif">
    <input type="text" maxlength="4" size="4" name="mes" id="mes"> A&#241;o:
    <input type="text" maxlength="4" size="4" name="anio" id="anio">
    </font></div></td>
    </tr>
    <?
    if($opcion==3)
    {
    ?>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left">Tipo:<font size="2" face="Arial, Helvetica, sans-serif">
    <select name="tipo" id="tipo" style="width:200px">
    <option>Seleccione tipo</option>
    <option value="APORTE">APORTE</option>
    <option value="INGRESO">INGRESO</option>
    </select>
    </font></div></td>
    <?
    }
    ?>
    </table>
<?
}
elseif(($opcion==1)||($opcion==4)||($opcion==7)||($opcion==8)||($opcion==9)||($opcion==11)||($opcion==12)||($opcion==13)||($opcion==15)||($opcion==17)||($opcion==18)||($opcion==19)||($opcion==20))
{
?>
    <table width="100%" height="229" border="0" id="tabla_seleccion">
    <tr>
    <td width="489" height="190" ><table width="520" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><?echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <select name="cboTipoNomina" id="select2" style="width:400px">
    <option>Seleccione una <?echo $termino?></option>
    <?php
    $query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."' and status='C'";
    $result=query($query,$conexion);
    //ciclo para mostrar los datos
    while ($row = fetch_array($result))
    {       
    // Opcion de modificar, se selecciona la situacion del registro a modificar             
    ?>
    <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
    <?
    }//fin del ciclo while
    ?>      
    </select>
    <input type="hidden" name="codt" id="codt" value="<? echo $fila['codtip']; ?>" >
    </font></div></td>
    </tr>
    </table>
<?
}
elseif($opcion==21)
{
?>
    <table width="520" border="0">
        <tr>
            <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><?echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">
                <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
                <select name="cboTipoNomina" id="select2" style="width:400px">
                    <option>Seleccione una <?echo $termino?></option>
                    <?php
                    $query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."'";
                    $result=query($query,$conexion);
                    while ($row = fetch_array($result))
                    { ?>
                        <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
                    <?
                    }
                    ?>      
                </select>
                <input type="hidden" name="codt" id="codt" value="<? echo $_SESSION['codigo_nomina']; ?>" >
                </font></div>
            </td>
        </tr>
    </table>
<?    
}
elseif($opcion==5)
{
?>
    <table width="100%" height="229" border="0">
    <tr>
    <td width="489" height="190" ><table width="520" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><?echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <select name="cboTipoNomina" id="select2" style="width:400px">
    <option>Seleccione una <?echo $termino?></option>
    <?php
    $query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."' and status='C' AND comprometida<>1";
    $result=query($query,$conexion);
    //ciclo para mostrar los datos
    while ($row = fetch_array($result))
    {       
    // Opcion de modificar, se selecciona la situacion del registro a modificar             
    ?>
    <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
    <?
    }//fin del ciclo while
    ?>       
    </select>
    <input type="hidden" name="codt" id="codt" value="<? echo $fila['codtip']; ?>" >
    </font></div></td>
    </tr>
    </table>
<?
}

elseif($opcion==6)
{
?>
    <table width="100%" height="229" border="0">
    <tr>
    <td width="489" height="190" ><table width="520" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><?echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <select name="cboTipoNomina" id="select2" style="width:400px">
    <option>Seleccione una <?echo $termino?></option>
    <?php
    $query="SELECT codnom,descrip,codtip FROM nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."' AND status='C' AND comprometida=1 AND contabilizada<>1 ";
    $result=query($query,$conexion);
    //ciclo para mostrar los datos
    while ($row = fetch_array($result))
    {       
    // Opcion de modificar, se selecciona la situacion del registro a modificar             
    ?>
    <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
    <?
    }//fin del ciclo while
    ?>      
    </select>
    <input type="hidden" name="codt" id="codt" value="<? echo $fila['codtip']; ?>" >
    </font></div></td>
    </tr>
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left">Banco:<font size="2" face="Arial, Helvetica, sans-serif">
    <select name="banco" id="banco" style="width:200px">
    <option>Seleccione un banco</option>
    <?php
    $query="SELECT des_ban, ctacon FROM nombancos";
    $resultado33=query($query,$conexion);
    //ciclo para mostrar los datos
    while ($fetch = fetch_array($resultado33))
    {       
    // Opcion de modificar, se selecciona la situacion del registro a modificar             
    ?>
    <option value="<?php echo $fetch['ctacon'];?>"><?php echo $fetch['des_ban'];?></option>
    <?
    }//fin del ciclo while
    ?>      
    </select>
    </font></div></td>
    </tr>
    </table>
<?
}
elseif($opcion==10)
{
?>
    <table width="100%" height="229" align="center" border="0">
    <tr>
    <td width="489" height="190" >
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <table width="520" align="right" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle">
    <div align="left">Mes: <font size="2" face="Arial, Helvetica, sans-serif">
    <input type="text" maxlength="4" size="4" name="mes" id="mes"> A&#241;o:
    <input type="text" maxlength="4" size="4" name="anio" id="anio">
    </font></div></td>
    </tr>
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left">Tipo:<font size="2" face="Arial, Helvetica, sans-serif">
    <select name="tipo" id="tipo" style="width:200px">
    <option>Seleccione tipo</option>
    <option value="APORTE">APORTE</option>
    <option value="INGRESO">INGRESO</option>
    <option value="PRESTAMO">PRESTAMO</option>
    </select>
    </font></div></td>
    </tr>
    </table>
<?
}elseif ($opcion==14){
?>
		<tr>
		<td >
		<input name="nomina" type="hidden"  id="nomina"  value="" >
		<input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
		<input name="frecuencia" type="hidden"  id="frecuencia"  value="" >
		Frecuencia: 
		<select size="10" name="frec" multiple="multiple" id="frec">
		<?php
		$consulta="select * from nomfrecuencias order by orden";
		$resultado=query($consulta,$conexion);
		while($fila=fetch_array($resultado))
		{
			?>
			<option value="<?php echo $fila['codfre']?>"><?php echo $fila['descrip']?></option>
			<?php
		}
		?>
    		</select>
		</td>


      <td>
        Conceptos: 
        <select size="10" name="acum[]" multiple="multiple" id="acum">
        <?php
        $consulta="select * from nomconceptos ";
        $resultado=query($consulta,$conexion);
        while($fila=fetch_array($resultado))
        {
            ?>
            <option value="<?php echo $fila['codcon']?>"><?php echo $fila['descrip']?></option>
            <?php
        }
        ?>
            </select>
        </td>


		<td >Tipo de Nomina:&nbsp;&nbsp;
		<select size="10" multiple="multiple" name="nom" id="nom">
		<?php
		$consulta="select * from nomtipos_nomina";
		$resultado2=query($consulta,$conexion);
		while($fila=fetch_array($resultado2))
		{
			?>
			<option  value="<?php echo $fila['codtip'];?>"><?php echo $fila['descrip'];?></option>
			<?php
		}
		?>
		</select>
		</td>
		</tr>
		
		<tr>
		<td  align="center">
		<br><br>
		Mes y A&ntilde;o: 
		<input name="mesano" type="text"  id="mesano" style="width:100px" value="<?php echo date("m/Y") ?>" maxlength="60" >
		<input name="image2" type="image" id="mesano2" src="../lib/jscalendar/cal.gif" />
		<script type="text/javascript">Calendar.setup({inputField:"mesano",ifFormat:"%m/%Y",button:"mesano2"});</script>
		</td>
		
		</tr>
		<?
}
elseif ($opcion==16){
?>
        
        <tr>
        
        </tr>
        <tr>


      <td>
        Conceptos: 
        <select size="1" name="acum" id="acum">
        <?php
        $consulta="select * from nomconceptos ";
        $resultado=query($consulta,$conexion);
        while($fila=fetch_array($resultado))
        {
            ?>
            <option value="<?php echo $fila['codcon']?>"><?php echo $fila['descrip']?></option>
            <?php
        }
        ?>
            </select>
        </td>


        <td ><?echo $termino?>:&nbsp;&nbsp;
        <select name="cboTipoNomina" id="select2" style="width:400px">
        <option>Seleccione una <?echo $termino?></option>
        <?php
        $query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."' and status='C'";
        $result=query($query,$conexion);
        //ciclo para mostrar los datos
        while ($row = fetch_array($result))
        {       
        // Opcion de modificar, se selecciona la situacion del registro a modificar             
        ?>
        <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
        <?
        }//fin del ciclo while
        ?>      
        </select>
        </td>
        </tr>
        <?
}
?>
<p>&nbsp;</p>
<table width="467" border="0" id="accion_direccionar">
<tr>
<td width="466"><div align="right">
<?btn('ok','direccionar_txt();',2); ?>
</div></td>
</tr>
</table>
</td>
</tr>
</table>

<div id='cargando' style='text-align: center; padding-top: 80px; display: none;'>
    <svg width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling">
        <circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#555555" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(36 50 50)">
          <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
        </circle>
    </svg>
    <br>PROCESANDO<br><small>No cierre hasta que culmine el proceso.</small>
</div>
</form>

