<?php 
session_start();
ob_start();
include ("../header3.php");
include("../lib/common.php") ;
include("func_bd.php");	
?>
<style>

.ayuda{
    background: none repeat scroll 0 0 #C0CCCF;
    border: 1px solid #C1C1C1;
    color: black;
    font-size: 10px;
    font-weight: bold;
    margin: 0;
    padding: 2px;
    text-align: center;
    width: 600px;
}
 
.content-horario {
  height: 170px;
    width: 200px;
}

</style>

<script>

function Enviar(){					
	if (document.frmAgregarCargo.registro_id.value==0){ 
		document.frmAgregarCargo.op_tp.value=1
	}
	else{ 	
		document.frmAgregarCargo.op_tp.value=2
	}		
	
	if (document.frmAgregarCargo.txtdescripcion.value==0){
		document.frmAgregarCargo.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");			
	}				
	

	if(!$("#horas_reales").val()){
		alert("Debe ingresar un valor válido para las horas reales.");
		return false;
	}

	if(!$("#horas_teoricas").val() || $("#horas_teoricas").val()=="00:00"){
		alert("Debe ingresar un valor válido para las horas teoricas.");
		return false;
	}


	document.frmAgregarCargo.submit();
}


</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">
//Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
//Script featured on JavaScript Kit (http://www.javascriptkit.com)
//For this script, visit http://www.javascriptkit.com 

</script>


<?php 
	
	
	
	$registro_id=$_POST[registro_id];
	
	
	$op_tp=$_POST[op_tp];
	$validacion=0;
	
	if ($registro_id==0) // Si el registro_id es 0 se va a agregar un registro nuevo
	{			
		
		if ($op_tp==1)
		{
			$query="select * from nomturnos where turno_id='".$_POST['txtcodigo']."'";
			$result=sql_ejecutar($query);
			$cantidad= num_rows($result);
			if ($cantidad!=0){ // el codigo esta repetido ?>
				<script language="javascript" type="text/javascript" >
				alert('Este código ya existe!!')	
				</script>
				<?php
				activar_pagina("ag_turnos.php");
				
			}
			else{

				$codigo = $_POST["txtcodigo"];
				$descripcion = $_POST["txtdescripcion"];
				$entrada = "TIME(STR_TO_DATE('".$_POST["entrada"]."','%h:%i:%s %p'))";
				$tolerancia_entrada = "TIME(STR_TO_DATE('".$_POST["tolerancia_entrada"]."','%h:%i:%s %p'))";
				$inicio_descanso = "TIME(STR_TO_DATE('".$_POST["inicio_descanso"]."','%h:%i:%s %p'))";
				$salida_descanso = "TIME(STR_TO_DATE('".$_POST["salida_descanso"]."','%h:%i:%s %p'))"; 
				$tolerancia_descanso = "TIME(STR_TO_DATE('".$_POST["tolerancia_descanso"]."','%h:%i:%s %p'))";
				$salida = "TIME(STR_TO_DATE('".$_POST["salida"]."','%h:%i:%s %p'))";
				$tolerancia_salida = "TIME(STR_TO_DATE('".$_POST["tolerancia_salida"]."','%h:%i:%s %p'))";
				$tolerancia_llegada = "TIME(STR_TO_DATE('".$_POST["tolerancia_llegada"]."','%h:%i:%s %p'))";
				$libre = $_POST['libre'];
                                $tipo = $_POST['tipo'];
                                $descpago = $_POST[descpago];
                                $descanso_contrato = $_POST[descanso_contrato];
                                $descanso_estricto = $_POST[descanso_estricto];
                                $horas_teoricas = $_POST[horas_teoricas];
                                $horas_reales = $_POST[horas_reales];
				$query="INSERT INTO `nomturnos` (turno_id, descripcion, entrada, tolerancia_entrada, 
                                    inicio_descanso, salida_descanso, tolerancia_descanso, salida, tolerancia_salida,
                                    tolerancia_llegada, libre,nocturno,tipo, descpago,descanso_contrato, descanso_estricto, horas_teoricas, horas_reales) 
                                    VALUES
					(".$codigo.", '".$descripcion."', ".$entrada.", ".$tolerancia_entrada.", ".$inicio_descanso.","
                                        . " ".$salida_descanso.", ".$tolerancia_descanso.", ".$salida.", ".$tolerancia_salida.","
                                        . " ".$tolerancia_llegada.", '$libre', '$nocturno', '$tipo','$descpago','$descanso_contrato','$descanso_estricto','$horas_teoricas','$horas_reales');";

//insert into nomturnos values ('".$_POST['txtcodigo']."','".$_POST[txtdescripcion]."','','',0,0)";
					
				$result=sql_ejecutar($query);	?>
				<script language="javascript" type="text/javascript" >
				alert('Su nuevo turno fue agregado exitosamente!!')	
				</script>
				<?php
				activar_pagina("turnos.php");				
			}
		}
	}
	else // Si el registro_id es mayor a 0 se va a editar el registro actual
	{	
	$query="select 
		turno_id,
		descripcion,
		date_format(entrada,'%h:%i:%s %p') as entrada,
		date_format(tolerancia_entrada,'%h:%i:%s %p') as tolerancia_entrada,
		date_format(inicio_descanso,'%h:%i:%s %p') as inicio_descanso,
		date_format(salida_descanso,'%h:%i:%s %p') as salida_descanso,
		date_format(tolerancia_descanso,'%h:%i:%s %p') as tolerancia_descanso,
		date_format(salida,'%h:%i:%s %p') as salida,
		date_format(tolerancia_salida,'%h:%i:%s %p') as tolerancia_salida,
		date_format(tolerancia_llegada,'%h:%i:%s %p') as tolerancia_llegada,
		libre,
		nocturno,
		tipo,
		descpago,
                descanso_contrato,
                descanso_estricto,
                horas_teoricas,
                horas_reales
		from nomturnos where turno_id='$registro_id'";	
		$result=sql_ejecutar($query);	
		$row = fetch_array ($result);	
		$codigo=$row[turno_id];	
		$descripcion=$row[descripcion];
		$entrada=$row[entrada];
		$tolerancia_entrada=$row[tolerancia_entrada];
		$inicio_descanso=$row[inicio_descanso];
		$salida_descanso=$row[salida_descanso];
		$tolerancia_descanso=$row[tolerancia_descanso];
		$salida=$row[salida];
		$tolerancia_salida=$row[tolerancia_salida];
		$tolerancia_llegada=$row[tolerancia_llegada];
		$libre = $row[libre];
		$nocturno = $row[nocturno];
		$tipo = $row[tipo];
		$descpago = $row[descpago];
                $descanso_contrato = $row[descanso_contrato];
                $descanso_estricto = $row[descanso_estricto];
                $horas_teoricas = $row[horas_teoricas];
                $horas_reales = $row[horas_reales];
//		$grado=$row[grado];
	}	
		
		
		if ($op_tp==2)
		{							
			$codigo = $_POST["txtcodigo"];
			$descripcion = $_POST["txtdescripcion"];
			$entrada = "TIME(STR_TO_DATE('".$_POST["entrada"]."','%h:%i:%s %p'))";
			$tolerancia_entrada = "TIME(STR_TO_DATE('".$_POST["tolerancia_entrada"]."','%h:%i:%s %p'))";
			$inicio_descanso = "TIME(STR_TO_DATE('".$_POST["inicio_descanso"]."','%h:%i:%s %p'))";
			$salida_descanso = "TIME(STR_TO_DATE('".$_POST["salida_descanso"]."','%h:%i:%s %p'))"; 
			$tolerancia_descanso = "TIME(STR_TO_DATE('".$_POST["tolerancia_descanso"]."','%h:%i:%s %p'))";
			$salida = "TIME(STR_TO_DATE('".$_POST["salida"]."','%h:%i:%s %p'))";
			$tolerancia_salida = "TIME(STR_TO_DATE('".$_POST["tolerancia_salida"]."','%h:%i:%s %p'))";
			$tolerancia_llegada = "TIME(STR_TO_DATE('".$_POST["tolerancia_llegada"]."','%h:%i:%s %p'))";
			$libre = $_POST[libre];
			$nocturno = $_POST[nocturno];
			$tipo = $_POST[tipo];
			$descpago = $_POST[descpago];
                        $descanso_contrato = $_POST[descanso_contrato];
                        $descanso_estricto = $_POST[descanso_estricto];
                        $horas_teoricas = $_POST[horas_teoricas];
                        $horas_reales = $_POST[horas_reales];
			$query="UPDATE `nomturnos` SET `descripcion` = '".$descripcion."',
					`entrada` = ".$entrada.",
					`tolerancia_entrada` = ".$tolerancia_entrada.",
					`inicio_descanso` = ".$inicio_descanso.",
					`salida_descanso` = ".$salida_descanso.",
					`tolerancia_descanso` = ".$tolerancia_descanso.",
					`salida` = $salida,
					`tolerancia_salida` = ".$tolerancia_salida.",
					`tolerancia_llegada` = ".$tolerancia_llegada.",
					`nocturno` = ".$nocturno.",
					libre='$libre',
					`tipo` = ".$tipo.",
					descpago = '".$descpago."',
                                        descanso_contrato = '".$descanso_contrato."',
                                        descanso_estricto = '".$descanso_estricto."',
                                        horas_teoricas = '".$horas_teoricas."',
                                        horas_reales = '".$horas_reales."'
					WHERE `nomturnos`.`turno_id` = ".$registro_id;

			$result=sql_ejecutar($query);	
			activar_pagina("turnos.php");
		{			
	}
}		

?>
<form action="" method="post" name="frmAgregarCargo" id="frmAgregarCargo" onsubmit="return validar_guardar()" enctype="multipart/form-data">
  <p>
  <input name="op_tp" type="Hidden" id="op_tp" value="-1">
  <input name="registro_id" type="Hidden" id="registro_id" value="<?php if (isset($_POST[registro_id])){echo $_POST[registro_id];} else {echo 0;} ?>">
  </p>
  <table width="780" height="140" border="0" class="row-br">
    <tr>
      <td height="40" class="row-br"><font color="#000066"><strong>&nbsp;<font color="#000066">
        <?php
		
		if ($registro_id==0)
		{
		echo "Agregar Turno";
		}
		else
		{
		echo "Modificar Turno";
		}
		?>
      </font></strong></font></td>
    </tr>
    <tr>
      <td width="489" height="96" class="ewTableAltRow"><table width="790" border="0" bordercolor="#0066FF">
        <tr bgcolor="#FFFFFF">
          <td width="217" height="30" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">C&oacute;digo:</font></td>
          <td width="390"  class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtcodigo" type="text" id="txtcodigo"  style="background-color:#FFFFFF;" style="width:100px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>" maxlength="10">
          </font></td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>
        
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="30" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Descripci&oacute;n:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
 	 <input name="txtdescripcion" type="text" id="txtdescripcion" style="width:300px" value="<?php if ($registro_id!=0){ echo $descripcion; }  ?>" maxlength="100">
          </font></td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>


        <tr valign="middle">
          <td height="30" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Tipo de Turno:</font></td>
          <td>
        <!--
		<select name="tipo" id="tipo" />
			<option <?php //if($tipo==1) echo "selected='selected'"?> value="1">Diurno</option>
			<option <?php //if($tipo==2) echo "selected='selected'"?> value="2">Norturno</option>
			<option <?php //if($tipo==3) echo "selected='selected'"?> value="3">Mixto-Diurno-Nocturno</option>
			<option <?php //if($tipo==4) echo "selected='selected'"?> value="4">Mixto-Nocturno-Diurno</option>
			<option <?php //if($tipo==5) echo "selected='selected'"?> value="5">Diurno-Corrido</option>
			<option <?php //if($tipo==6) echo "selected='selected'"?> value="6">Libre</option>
		</select>
		-->
		<select name="tipo" id="tipo">
			<?php 
				$sql = "SELECT turnotipo_id, descripcion FROM nomturnos_tipo";	
				$res = sql_ejecutar_utf8($sql);	
				while($fila = fetch_array ($res))
				{
					$selected = ($fila['turnotipo_id']==$tipo) ? 'selected' : '';
					?>
						<option value="<?php echo $fila['turnotipo_id']; ?>" <?php echo $selected; ?> ><?php echo $fila['descripcion']; ?></option>
					<?php
				}	
			?>
		</select>
		</td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>


        <tr valign="middle">
          <td height="30" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Nocturno?:</font></td>
          <td>
		<select name="nocturno" id="nocturno" />
			<option <?php if($nocturno==1) echo "selected='selected'"?> value="1">Si</option>
			<option <?php if($nocturno==0) echo "selected='selected'"?> value="0">No</option>
		</select>
		</td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>

        <tr valign="top" bgcolor="#FFFFFF">
          <td height="30" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Horarios:</font></td>
          <td height="40" valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" colspan=2 ><font size="2" face="Arial, Helvetica, sans-serif"></font>
	<?php
		$var_ejemplo = '<div class="ayuda"><b>Ej.</b>: 07:20:00 AM, 01:05:00 PM</div>';
	?>	


<table>
	<tr  valign="top">
	   <td>
		<fieldset class="content-horario">
		<legend>Entrada</legend>
		<table style="margin-bottom:9px;">
                    <tr>
                    <td>Tolerancia de llegada</td><td><input style="width:80px" type="text" onblur="valida(this.value);" value="<?= $tolerancia_llegada ?>" name="tolerancia_llegada" id="tolerancia_llegada" /></td>
                    </tr>
                    <tr>
                    <td>Hora</td><td><input style="width:80px" type="text" onblur="valida(this.value);" onchange="calcular_horas_reales()" value="<?= $entrada ?>" name="entrada" id="entrada" /></td>
                    </tr>
                    <tr>
                    <td>Tolerancia</td><td><input style="width:80px" type="text" onblur="valida(this.value);" value="<?= $tolerancia_entrada ?>" name="tolerancia_entrada" id="tolerancia_entrada" /></td>
                    </tr>
		</table>
		</fieldset>
           </td>
	   <td>
		<fieldset class="content-horario">
		<legend>Descanso (Ley)</legend>
		<table style="margin-bottom:9px;">
                    <tr>
                    <td>Inicio</td><td><input style="width:80px" type="text" onblur="valida(this.value);" onchange="calcular_horas_reales()" value="<?= $inicio_descanso ?>" name="inicio_descanso" id="inicio_descanso" /></td>
                    </tr>
                    <tr>
                    <td>Fin</td><td><input style="width:80px" type="text" onblur="valida(this.value);" onchange="calcular_horas_reales()" value="<?= $salida_descanso ?>" name="salida_descanso" id="salida_descanso" /></td>
                    </tr>
                    <tr>
                    <td>Tolerancia de descanso</td><td><input style="width:80px" type="text" onblur="valida(this.value);" value="<?= $tolerancia_descanso ?>" name="tolerancia_descanso" id="tolerancia_descanso" /></td>
                    </tr>


                    <tr>
                    <td>Descanso Libre?</td>
                    <td>
                    <select name="libre" id="libre" />
                            <option <?php if($libre==1) echo "selected='selected'"?> value="1">Si</option>
                            <option <?php if($libre==0) echo "selected='selected'"?> value="0">No</option>
                    </select>
                    </td>
                    </tr>

                    <tr>
                    <td>Descanso pago?</td>
                    <td>
                    <select name="descpago" id="descpago" />
                            <option <?php if($descpago==1) echo "selected='selected'"?> value="1">Si</option>
                            <option <?php if($descpago==0) echo "selected='selected'"?> value="0">No</option>
                    </select>
                    </td>
                    </tr>

                    <tr>
                    <td>Cumplimiento Estricto?</td>
                    <td>
                    <select name="descanso_estricto" id="descanso_estricto" />
                            <option <?php if($descanso_estricto==1) echo "selected='selected'"?> value="1">Si</option>
                            <option <?php if($descanso_estricto==0) echo "selected='selected'"?> value="0">No</option>
                    </select>
                    </td>
                    </tr>

		</table>
		</fieldset>
	   </td>
           <td>
		<fieldset class="content-horario">
		<legend>Salida</legend>
		<table style="margin-bottom:9px;">
                    <tr>
                    <td>Hora</td><td><input style="width:80px" type="text" onblur="valida(this.value);" onchange="calcular_horas_reales()" value="<?= $salida ?>" name="salida" id="salida" /></td>
                    </tr>
                    <tr>
                    <td>Tolerancia</td><td><input style="width:80px" type="text" onblur="valida(this.value);" value="<?= $tolerancia_salida ?>" name="tolerancia_salida" id="tolerancia_salida" /></td>
                    </tr>
		</table>
		</fieldset>
	   </td>
	</tr>
        <tr  valign="top">
	   <td>
		<fieldset class="content-horario">
		<legend>Horas</legend>
		<table style="margin-bottom:2px;">
                    <tr>
                    <td>Teoricas (Horas:Minutos)</td><td><input style="width:80px" type="text" value="<?= $horas_teoricas ?>" name="horas_teoricas" id="horas_teoricas" autocomplete="off" /></td>
                    </tr>
                    <tr>
                    <td>Reales (Horas:Minutos)</td><td>
                    	<div style="display: flex; align-items: center; justify-content: center;">
                    	<input style="width:60px;background: #e0e0e0; color: #565656;" type="text" value="<?= $horas_reales ?>" name="horas_reales" id="horas_reales" readonly /><button type="button" style="width: 20px;height: 18px; padding: 0; cursor: pointer;" onclick="calcular_horas_reales()" title="Recalcular"><img src='../imagenes/generar.png' width="16" height="16"></button>
                    	</div>
                    </td>
                    </tr>

		</table>
		</fieldset>
	   </td>
	   <td>
		<fieldset class="content-horario">
		<legend>Descanso (Contrato Colectivo)</legend>
		<table style="margin-bottom:2px;">
                    <tr>
                    <td>Tiempo (Horas:Minutos)</td><td><input style="width:80px" type="text" value="<?= $descanso_contrato ?>" name="descanso_contrato" id="descanso_contrato" /></td>
                    </tr>
                    

		</table>
		</fieldset>
	   </td>
           <td>
		
	   </td>
	</tr>
</table>
<table>
<?= $var_ejemplo ?>
</td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>


	

        
<!--	<tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Grado del Cargo:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
        
	<?
	$consulta="SELECT grado FROM nomgradospasos";
	$resultado=sql_ejecutar($consulta);
	?>
	<select name="grado" id="grado">
	<option value="">Seleccione</option>
	<?php
	while($fetch=fetch_array($resultado))
	{
	?>
		<option value="<? echo $fetch['grado']?>" <? if ($grado==$fetch['grado']) echo "selected='true'"?>><? echo $fetch['grado']?></option>";
	<?
	}
	?>
	</SELECT>
          </font></td>
          <td width="169" colspan="-1" bgcolor="#FFFFFF" class="ewTableAltRow" >&nbsp;</td>
        </tr>-->
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;</td>
          <td colspan="2" bgcolor="#FFFFFF" class="ewTableAltRow"><div align="right"><font size="2" face="Arial, Helvetica, sans-serif"></font>
                  <table width="85" border="0">
                    <tr>
                      <td width="39"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                          <?php btn('cancel','history.back();',2) ?>
                      </font></div></td>
                      <td width="36"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                          <?php btn('ok','Enviar();',2) ?>
					
                      </font></div></td>
                    </tr>
                  </table>
            <font size="2" face="Arial, Helvetica, sans-serif"></font></div></td>
        </tr>
      </table>      </td>
    </tr>
  </table>
  <p>&nbsp;</p>
</form>
<script type="text/javascript">
   jQuery(function($)
    {
       $.mask.definitions['H']='[01]';
	   $.mask.definitions['N']='[012345]';
	   $.mask.definitions['n']='[0123456789]';
	   $.mask.definitions['m']='[apAP]';
	   $.mask.definitions['M']='[mM]';

	   $("#entrada, #tolerancia_entrada, #inicio_descanso, #salida_descanso, #tolerancia_descanso, #salida, #tolerancia_salida").mask("Hn:Nn:Nn mM");
	   $("#horas_teoricas").mask("nn:Nn");
	   
	   if(!$("#horas_reales").val()){
	   	 //calcular_horas_reales();
	   }
	});
	
	function valida(valor) 
	{
	   //que no existan elementos sin escribir
	   if(valor.indexOf("_") == -1)
	   {		      
	      var hora = valor.split(":")[0];
	      if(parseInt(hora) > 23 )
	      {
	           $("#entrada, #tolerancia_entrada, #inicio_descanso, #salida_descanso, #tolerancia_descanso, #salida, #tolerancia_salida").val("");		      
	      } 
	   }

	   //calcular_horas_reales();
	}


	function calcular_horas_reales(){
		var entrada         = $("#entrada").val();
		var inicio_descanso = $("#inicio_descanso").val();
		var salida_descanso = $("#salida_descanso").val();
		var salida          = $("#salida").val();

		entrada             = aminutos(entrada);
		inicio_descanso     = aminutos(inicio_descanso);
		salida_descanso     = aminutos(salida_descanso);
		salida              = aminutos(salida);

		//var resultado       = salida - entrada - (salida_descanso - inicio_descanso);
		var horas           = salida - entrada;
		//si la salida < entrada (turno que sale al dia siguiente, incrementarle a la salida 24horas)
		if(salida<entrada){
			horas           = salida+(60*24) - entrada;
		}
		var descanso        = Math.abs(salida_descanso - inicio_descanso);

		console.log(horas);
		console.log(descanso);

		var horas_reales    = ahoras(horas-descanso);

		$("#horas_reales").val(horas_reales);
		var horas_teoricas_actual=$("#horas_teoricas").val();
		if(!horas_teoricas_actual || horas_teoricas_actual=="00:00" || horas_teoricas_actual=="00:00:00"){
			$("#horas_teoricas").val(horas_reales);
		}
	}

	function aminutos(cad){
		var  tmp= String(cad).split(" ");
		if(String(tmp[1]).toUpperCase()=="PM"){
			var temp = String(tmp[0]).split(":");
			if(temp[0]*1==12)
	        	var min = ((temp[0]*1)*60)+(temp[1]*1);
	        else
	        	var min = ((temp[0]*1+12)*60)+(temp[1]*1);
	        return min;	
		}
		else{
			var temp = String(tmp[0]).split(":");
			if(temp[0]*1==12)
	        	var min = (temp[1]*1);
	        else
	        	var min = (temp[0]*60)+(temp[1]*1);

	        return min;			
		}
	}

	function ahoras(cad){
		var temp=cad*1;
		if(temp>59){
			temp=temp/60;
			temp=temp.toFixed(2);
			temp=temp.split(".");
			var parte1=temp[0]*1;
			var parte2=temp[1]*1;
			if(parte1<=9) parte1="0"+parte1;
			parte2=Math.round(parte2*60/100);
			if(parte2<=9) parte2="0"+parte2;
			return parte1+":"+parte2;
		}
		else if(temp<=0){
			return "00:00";
		}
		else{
			if(temp<=9)
				return "00:0"+temp;
			if(!temp) return "00:00";
			return "00:"+temp;
		}
	}

    $("#txtdescripcion").focus();
</script>

</body>
</html>

