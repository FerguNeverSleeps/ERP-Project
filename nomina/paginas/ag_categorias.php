<?php 
session_start();
ob_start();
include ("../header.php");
?>

<script>

function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 
		document.frmPrincipal.op_tp.value=1}
	else{ 	
		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.txtdescripcion.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar un salario valido. Verifique...");}
}

</script>


<?php 
	include("../lib/common.php") ;
	include("func_bd.php");	

	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];
	$validacion=0;
	
	if ($registro_id==0) {// Si el registro_id es 0 se va a agregar un registro nuevo
				
		if ($op_tp==1){
		
		$codigo_nuevo=AgregarCodigo("nomcategorias","codorg");
		
		$query="insert into nomcategorias 
		(codorg,descrip,gr,ee,ocupacion)
		values ($codigo_nuevo,'$_POST[txtdescripcion]','$_POST[cboGrupo]',
		0,'$_POST[optOcupacion]')";
		
		$result=sql_ejecutar($query);	
		activar_pagina("categorias.php");				
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
	
		$query="select * from nomcategorias where codorg=$registro_id";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$codigo=$row[codorg];	
		$nombre=$row[descrip];
		$grupo=$row[gr];	
		$ocupacion=$row[ocupacion];
		
	}	
		
	if ($op_tp==2){					
					
		$query="UPDATE nomcategorias set codorg=$registro_id,
		descrip='$_POST[txtdescripcion]',
		gr='$_POST[cboGrupo]',
		ee=0,
		ocupacion='$_POST[optOcupacion]'			
		where codorg=$registro_id";	
					
		$result=sql_ejecutar($query);				
		activar_pagina("categorias.php");										
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
		echo "Agregar Categoria";
		}
		else
		{
		echo "Modificar Categoria";
		}
		?>
      </font></strong></font></td>
    </tr>
    <tr>
      <td width="489" height="86" class="ewTableAltRow"><table width="790" border="0" bordercolor="#0066FF">
        <tr bgcolor="#FFFFFF">
          <td width="207" height="23" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">C&oacute;digo:</font></td>
          <td width="573" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" style="width:100px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>" maxlength="10">
          </font></td>
          </tr>
        
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Descripci&oacute;n:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtdescripcion" type="text" id="txtdescripcion" style="width:200px" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>">
          </font></td>
          </tr>
        
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Grupo (Tabulador):</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <select name="cboGrupo" id="cboGrupo" style="width:200px" >
              <?php
		  
	 	$query="select gr,salario from nomgrupos_categorias";
		$result=sql_ejecutar($query);
		
	 	  //ciclo para mostrar los datos
  		while ($row = mysqli_fetch_array($result))
  		{ 		
		// Opcion de modificar, se selecciona la situacion del registro a modificar		
  		if ($row[gr]==$grupo){ ?>
              <option value="<?php echo $row[gr];?>" selected > <?php echo $row[gr]." - ".$row[salario];?> </option>
              <?php 
		}
		else // opcion de agregar
		{ 
		   ?>
              <option value="<?php echo $row[gr];?>"><?php echo $row[gr]." - ".$row[salario];?></option>
              <?php 
		} 
		}//fin del ciclo while
		?>
            </select>
          </font></td>
        </tr>
     
        <tr bgcolor="#FFFFFF">
          <td height="26" colspan="2" bgcolor="#FFFFFF" class="ewTableAltRow"><fieldset><legend>Ocupaci&oacute;n</legend>
              <table width="546" border="0">
                <tr>
                  <td width="165"><label>
                    <input name="optOcupacion" type="radio" value="P" 
					<?php if ($ocupacion=='P'){?> checked="checked"<?php }?>>
                  </label>
                  Propietario</td>
                  <td width="167"><label>
                    <input name="optOcupacion" type="radio" value="S"
					<?php if ($ocupacion=='S'){?> checked="checked"<?php }?>>
                  </label> 
                    Socio
</td>
                  <td width="200"><label>
                    <input name="optOcupacion" type="radio" value="F"
					<?php if ($ocupacion=='F'){?> checked="checked"<?php }?>>
                  </label> 
                    Familiar
</td>
                </tr>
                <tr>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="G"
					<?php if ($ocupacion=='G'){?> checked="checked"<?php }?>>
                  </label> 
                    Gerente
</td>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="D"
					<?php if ($ocupacion=='D'){?> checked="checked"<?php }?>>
                  </label> 
                    Director
</td>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="E"
					<?php if ($ocupacion=='E'){?> checked="checked"<?php }?>>
                  </label> 
                    Empleado
</td>
                </tr>
                <tr>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="O"
					<?php if ($ocupacion=='O'){?> checked="checked"<?php }?>>
                  </label> 
                    Obrero
</td>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="A"
					<?php if ($ocupacion=='A'){?> checked="checked"<?php }?>>
                  </label> 
                    Aprendiz
</td>
                  <td><label>
                    <input name="optOcupacion" type="radio" value="J"
					<?php if ($ocupacion=='J'){?> checked="checked"<?php }?>>
                  </label> 
                    Menos - Jornada
</td>
                </tr>
              </table>
              </fieldset></td>
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

