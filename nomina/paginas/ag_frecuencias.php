<?php 
session_start();
ob_start();
include("func_bd.php");	
	include ("../header.php");
	include("../lib/common.php");
?>
<html>
<head>
<script>

function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 

		document.frmPrincipal.op_tp.value=1}
	else{ 	

		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.txtdescripcion.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripcion valida. Verifique...");}
}

</script>


<?php 
	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];
	$validacion=0;
	
	if ($registro_id==0) {// Si el registro_id es 0 se va a agregar un registro nuevo
				
		if ($op_tp==1){
		
		if (isset($_POST[chkPeriodos])){$periodos=1;}else{$periodos=2;}
		
		$codigo_nuevo=AgregarCodigo("nomfrecuencias","codfre");
				
		$query="insert into nomfrecuencias 
		(codfre,descrip,diasperiodo,periodos,orden)
		values ('$codigo_nuevo','$_POST[txtdescripcion]','$_POST[txtdias]',
		'$periodos','$_POST[orden]')";
		
		$result=sql_ejecutar($query);	
		activar_pagina("frecuencias.php");				
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
		$query="select * from nomfrecuencias where codfre=$registro_id";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$codigo=$row[codfre];	
		$nombre=$row[descrip];
		$dias=$row[diasperiodo];	
		$periodos=$row[periodos];	
		$orden = $row[orden];

	}	
		
	if ($op_tp==2){					
		
		if (isset($_POST[chkPeriodos])){$periodos=1;}else{$periodos=2;}
		
		$query="UPDATE nomfrecuencias set codfre=$registro_id,
		descrip='$_POST[txtdescripcion]',
		diasperiodo='$_POST[txtdias]',
		periodos='$periodos' , orden ='$_POST[orden]' 		
		where codfre='$registro_id'";	
		
		$result=sql_ejecutar($query);				
		activar_pagina("frecuencias.php");										
		{			
	}
}	

?>
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
  <p>
  <input name="op_tp" type="Hidden" id="op_tp" value="-1">
  <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
  </p>
  <table width="780" height="125" border="0" class="row-br">
    <tr>
      <td height="31" class="row-br"><font color="#000066"><strong>&nbsp;<font color="#000066">
        <?php
		if ($registro_id==0)
		{
		echo "Agregar Tipo de Frecuencia de Pago";
		}
		else
		{
		echo "Modificar Tipo de Frecuencia de Pago";
		}
		?>
      </font></strong></font></td>
    </tr>
    <tr>
      <td width="489" height="86" class="ewTableAltRow"><table width="790" border="0" bordercolor="#0066FF">
        <tr bgcolor="#FFFFFF">
          <td width="207" height="23" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">Codigo:</font></td>
          <td width="573" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" style="width:100px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>"  maxlength="10">
          </font></td>
          </tr>
        
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Descripci&oacute;n:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtdescripcion" type="text" id="txtdescripcion" style="width:300px" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>">
            <label></label>
            <label></label>
          </font></td>
          </tr>
        
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Dias entre periodos:</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtdias" type="text" id="txtdias" style="width:100px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias; }  ?>" maxlength="10">
          </font></td>
        </tr>
     



        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Orden:</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="orden" type="text" id="orden" value="<?php if ($registro_id!=0){ echo $orden; }  ?>" style="width:100px"  maxlength="2">
          </font></td>
        </tr>




        <tr bgcolor="#FFFFFF">
          <td height="26" bgcolor="#FFFFFF" class="ewTableAltRow">&iquest; Maneja Periodos ?: </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><label>
            <input name="chkPeriodos" type="checkbox" id="chkPeriodos" value="checkbox"
			<?php 
			if ($periodos==1)
			{ ?> checked="checked" <?php }
			?>
			>
          </label></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="26" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><div align="right">
            <table width="85" border="0">
              <tr>
                <td width="39"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                    <?php btn('cancel','history.back();',2) ?>
                </font></div></td>
                <td width="36"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                    <?php btn('ok','Enviar(); document.frmPrincipal.submit();',2) ?>
                </font></div></td>
              </tr>
            </table>
          </div></td>
        </tr>
      </table>      </td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>  
  <p>&nbsp;</p>
</form>
</body>
</html>

