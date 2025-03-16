<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
	include ("../header.php");
	include("../lib/common.php");
	include("func_bd.php");
?>

<?php
	if($_GET['codigo']!='')
	{
		$consulta="SELECT * FROM nomexpediente WHERE cedula='$_GET[cedula]' AND cod_expediente_det=$_GET[codigo]";
		$resultado555=sql_ejecutar($consulta);
		$fetch33=fetch_array($resultado555);
	}
	if($_GET['cedula']!='')
	{
		$consulta="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
		$resultado66=sql_ejecutar($consulta);
		$fetch66=fetch_array($resultado66);
	}
	$periodo = $_GET['periodo'];
	$frecuencia = $_GET['frecuencia'];
	$opcion=$_GET['opcion'];
	switch ($opcion)
	{
		case '1':
			$consulta = "SELECT periodos FROM nomfrecuencias WHERE codfre='".$frecuencia."'";
			$resultado = sql_ejecutar($consulta);
			$fetch_fre = fetch_array($resultado);
			if($fetch_fre['periodos']==1)
			{
				$consulta="SELECT codfre,nper,status FROM nomperiodos WHERE codfre=$frecuencia and status='Abierto'";
				$resultado_per=sql_ejecutar($consulta);
				?>
				<div id="periodo">
				<!--<SELECT name="sel_periodo" id="sel_periodo" onchange="javascript:cargar_fecha()">-->
				<SELECT name="sel_periodo" id="sel_periodo">
				<option value="0">Seleccione un periodo</option>
				<?php
				while($fetch_per=fetch_array($resultado_per))
				{
					$codigo_per = $fetch_per['codfre'];
					$periodo = $fetch_per['nper'];
					$status=$fetch_per['status'];
					echo "<option value=\"$periodo\">".$periodo." ".$status."</option>";
				}
			}
			?>
			</SELECT>
			</div>

			<?
			break;
		
		case '2':
			$consulta_fec = "SELECT finicio,ffin FROM nomperiodos WHERE codfre=".$frecuencia." and nper=".$periodo."";
			$resultado_fec = sql_ejecutar($consulta_fec);
			$fetch_fec=fetch_array($resultado_fec);
			?>
			 <tr id="fechas" bgcolor="#FFFFFF">
			<td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Fecha de Inicio: </td>
			<td width="141" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
			<font size="2" face="Arial, Helvetica, sans-serif">
			<input onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="txtfechainicio" type="text" id="txtfechainicio" style="width:100px" value="<?php echo $fetch_fec['finicio']?>" maxlength="60" >
			</font>
			<input name="image988" type="image" id="d_fechainicio" src="lib/jscalendar/cal.gif" />
			<script type="text/javascript">Calendar.setup({inputField:"txtfechainicio",ifFormat:"%d/%m/%Y",button:"d_fechainicio"});</script>
			</a></font></td>
			<td width="83" bgcolor="#FFFFFF" class="ewTableAltRow">Fecha Final: </td>
			<td width="137" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
			<input onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="txtfechafinal" type="text" id="txtfechafinal" style="width:100px" value="<?php echo $fetch_fec['ffin']  ?>" maxlength="60">
			<input name="image22" type="image" id="d_fechafinal" src="lib/jscalendar/cal.gif" />
			<script type="text/javascript">Calendar.setup({inputField:"txtfechafinal",ifFormat:"%d/%m/%Y",button:"d_fechafinal"});</script>
			</a></font></td>
			<td width="97" bgcolor="#FFFFFF" class="ewTableAltRow">Fecha de Pago: </td>
			<td width="151" bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
			<input name="txtfechapago" type="text" id="txtfechapago" style="width:100px" value="<?php echo $fetch_fec['ffin'] ?>" maxlength="60">
			<input name="image222" type="image" id="d_fechapago" src="lib/jscalendar/cal.gif" />
			<script type="text/javascript">Calendar.setup({inputField:"txtfechapago",ifFormat:"%d/%m/%Y",button:"d_fechapago"});</script>
			</a></font>
				
			</td>
			</tr>
			
			
			<?
		break;

		case 'Estudios Academicos':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<tr>
			<td>
			Tipo registro: <select name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Primaria" <? if ($fetch33['tipo_tiporegistro']=="Primaria") echo "selected='true'"?>>Primaria</option>
			<option value="Basico" <? if ($fetch33['tipo_tiporegistro']=="Basico") echo "selected='true'"?>>Basico</option>
			<option value="Diversificado" <? if ($fetch33['tipo_tiporegistro']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
			<option value="Tecnico-Universitario" <? if ($fetch33['tipo_tiporegistro']=="Tecnico-Universitario") echo "selected='true'"?>>Tecnico Universitario</option>
			<option value="Pre-Grado" <? if ($fetch33['tipo_tiporegistro']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
			<option value="Post-Grado" <? if ($fetch33['tipo_tiporegistro']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Pagado por la instituci&oacute;n?:
			<select name="pagado_por_emp" id="pagado_por_emp">
			<option value="">Seleccione</option>
			<option <?if($fetch33['pagado_por_emp']=="Si")echo "selected='true'"?> value="Si">Si</option>
			<option <?if($fetch33['pagado_por_emp']=="No")echo "selected='true'"?> value="No">No</option>
			</select>
			</TD>
			</tr>
			
			
			
			<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			<TR height="50">
			<TD colspan="2">Instituci&oacute;n: <input type="text" name="institucion" id="institucion" <? if (isset($fetch33['institucion'])) echo "value='$fetch33[institucion]'"?> size="30"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tipo Estudio: <select name="tipo_estudio" id="tipo_estudio">
			<option value="Primaria" <? if ($fetch33['tipo_estudio']=="Primaria") echo "selected='true'"?>>Primaria</option>
			<option value="Basico" <? if ($fetch33['tipo_estudio']=="Basico") echo "selected='true'"?>>Basico</option>
			<option value="Diversificado" <? if ($fetch33['tipo_estudio']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
			<option value="Tecnico-Universitario" <? if ($fetch33['tipo_tiporegistro']=="Tecnico-Universitario") echo "selected='true'"?>>Tecnico Universitario</option>
			<option value="Pre-Grado" <? if ($fetch33['tipo_estudio']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
			<option value="Post-Grado" <? if ($fetch33['tipo_estudio']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nivel curso actual: <select name="nivel_actual" id="nivel_actual">
			<option value="Primaria" <? if ($fetch33['nivel_actual']=="Primaria") echo "selected='true'"?>>Primaria</option>
			<option value="Basico" <? if ($fetch33['nivel_actual']=="Basico") echo "selected='true'"?>>Basico</option>
			<option value="Diversificado" <? if ($fetch33['nivel_actual']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
			<option value="Pre-Grado" <? if ($fetch33['nivel_actual']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
			<option value="Post-Grado" <? if ($fetch33['nivel_actual']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
			</select>
			</TD>
			</TR>
			
			<tr height="50">
			<TD colspan="2">Duraci&oacute;n: 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de inicio:
			<input type="text" name="fecha_salida" id="fecha_salida" size="10" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>" maxlength="10"/>(dd/mm/aaaa)
			
			<!--<input name="image3" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de culminaci&oacute;n:
			<input size="10" type="text" name="fecha_reintegro" id="fecha_reintegro" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>"/>(dd/mm/aaaa)
			
			<!--<input name="image33" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_reintegro",ifFormat:"%d/%m/%Y",button:"d_fechafin"})
			</script>-->
			</TD>
			</tr>
			</table>
			
			</div>
			<?
		break;
		
		case 'Estudios Extra Academicos':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<table width="100%" border="0">
			<tr>
			<td>
			Tipo registro: <select name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Curso" <? if ($fetch33['tipo_tiporegistro']=="Curso") echo "selected='true'"?>>Curso</option>
			<option value="Taller" <? if ($fetch33['tipo_tiporegistro']=="Taller") echo "selected='true'"?>>Taller</option>
			<option value="Seminario" <? if ($fetch33['tipo_tiporegistro']=="Seminario") echo "selected='true'"?>>Seminario</option>
			<option value="Charla" <? if ($fetch33['tipo_tiporegistro']=="Charla") echo "selected='true'"?>>Charla</option>
			<option value="Jornada" <? if ($fetch33['tipo_tiporegistro']=="Jornada") echo "selected='true'"?>>Jornada</option>
			</select>
			
			</td>
			<td>
			Pagado por la instituci&oacute;n?:
			<select name="pagado_por_emp" id="pagado_por_emp">
			<option value="">Seleccione</option>
			<option <?if($fetch33['pagado_por_emp']=="Si")echo "selected='true'"?> value="Si">Si</option>
			<option <?if($fetch33['pagado_por_emp']=="No")echo "selected='true'"?> value="No">No</option>
			</select>
			</TD>
			</tr>
			</table>
			
			<table width="100%" border="0">
			<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			<TR height="50">
			<TD colspan="2">Instituci&oacute;n: <input type="text" name="institucion" id="institucion" <? if (isset($fetch33['institucion'])) echo "value='$fetch33[institucion]'"?> size="30"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tipo Estudio: <select name="tipo_estudio" id="tipo_estudio">
			<option value="Primaria" <? if ($fetch33['tipo_estudio']=="Primaria") echo "selected='true'"?>>Primaria</option>
			<option value="Basico" <? if ($fetch33['tipo_estudio']=="Basico") echo "selected='true'"?>>Basico</option>
			<option value="Diversificado" <? if ($fetch33['tipo_estudio']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
			<option value="Pre-Grado" <? if ($fetch33['tipo_estudio']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
			<option value="Post-Grado" <? if ($fetch33['tipo_estudio']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nivel curso actual: <select name="nivel_actual" id="nivel_actual">
			<option value="Primaria" <? if ($fetch33['nivel_actual']=="Primaria") echo "selected='true'"?>>Primaria</option>
			<option value="Basico" <? if ($fetch33['nivel_actual']=="Basico") echo "selected='true'"?>>Basico</option>
			<option value="Diversificado" <? if ($fetch33['nivel_actual']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
			<option value="Pre-Grado" <? if ($fetch33['nivel_actual']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
			<option value="Post-Grado" <? if ($fetch33['nivel_actual']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
			</select>
			</TD>
			</TR>
			
			<tr height="50">
			<TD colspan="2">Duraci&oacute;n: 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de inicio:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image4" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de culminaci&oacute;n:
			<input size="10" type="text" name="fecha_reintegro" id="fecha_reintegro" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image44" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_reintegro",ifFormat:"%d/%m/%Y",button:"d_fechafin"})
			</script>-->
			</TD>
			</tr>

			<TR height="50">
			<TD colspan="2">Especialidad: <input type="text" name="nombre_especialista" id="nombre_especialista" <? if (isset($fetch33['nombre_especialista'])) echo "value='$fetch33[nombre_especialista]'"?> size="30"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Costo por persona: <input type="text" name="costo_persona" id="costo_persona" <? if (isset($fetch33['costo_persona'])) echo "value='$fetch33[costo_persona]'"?> size="10"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Cant. Personas: <input type="text" name="num_participantes" id="num_participantes" <? if (isset($fetch33['num_participantes'])) echo "value='$fetch33[num_participantes]'"?> size="10"/>
			</TD>
			</TR>
			</table>
			</table>
			</div>
			<?
		break;

		case 'Permisos':
			?>
			<div id="registro" style="align:center;">
			<table  border="0">
			<table  border="0" align="center" >
			
			<tr height="50">
			<td><?
			$consulta="SELECT fecing FROM nompersonal where cedula='$_GET[cedula]'";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);
			$fecing = $fetch[fecing];
			$fecing1 = fecha($fetch[fecing]);

			$anio = date('Y')."-01-01";

			if($fecing<=$anio)
				$inicio = $anio;
			elseif($fecing>$anio)
				$inicio = $fecing;

			$consulta="select timestampdiff(month,'$inicio',curdate()) as meses";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);
			$meses = $fetch[meses];

			$horas = $meses * 12;

			$consulta="SELECT sum(dias)as dias,sum(horas) as horas,sum(minutos) as minutos FROM nomexpediente where cedula='$_GET[cedula]' and fecha BETWEEN '".date('Y')."-01-01' and '".date('Y')."-12-31' and tipo_registro='Permisos o Ausencias' and tipo_tiporegistro in (1,2)";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);


			$horass = $horas - (($fetch['dias']*8)+$fetch['horas']+($fetch['minutos']/60));
			?>
			FECHA DE INGRESO: <span style="color:green;"><strong><?php echo $fecing1?></strong></span> &nbsp;&nbsp; | &nbsp;&nbsp; REGISTRADO EN <?php echo date('Y');?>: 
			<input size="10" type="text" name="tiempo" style='color:#FF0000;' readonly id="tiempo" value="<? echo $fetch['dias'].' / '.$fetch['horas'].' / '.$fetch['minutos'];?>">(Dias/Horas/Minutos)
			&nbsp;&nbsp; | &nbsp;&nbsp;
			DISPONIBLE HORAS: <span style="color:green;"><strong><?php echo $horass?></strong></span> &nbsp;&nbsp; DIAS: <span style="color:green;"><strong><?php echo $horass/8?></strong></span> 

			<input type="hidden" name="dispo" id="dispo" value="<?php echo $horass?>">
			</TD>
			</tr>
			
			
			<tr class='tb-fila'>
			<td><?
			$consulta="SELECT * FROM nomsuspenciones where ee=0";
			$resultado=sql_ejecutar($consulta);
			?>
			<div id="tipo_tipo">
			Tipo registro: <SELECT name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="">Seleccione</option>
			<?php
			while($fetch=fetch_array($resultado))
			{
			?>
				<option value="<? echo $fetch['codigo']?>" <? if ($fetch33['tipo_tiporegistro']==$fetch['codigo']) echo "selected='true'"?>><? echo utf8_encode($fetch['descrip'])?></option>";
			<?
			}
			?>
			</SELECT>
			</div>
			
			</TD>
			</tr>
			
			<!--<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>-->
			
			<TR height="50" >
			<TD colspan="2">Desde Hora(s): 
<input type="text" size="7" name="desde" id="desde" onclick="javascript:inicio();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/>(00:00 am/pm)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de inicio:
			<input size="10" type="text" name="fecha_salida" readonly="readonly" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			</TD>
			</TR>
			<TR height="50" class='tb-fila'>
			<TD colspan="2">Hasta Hora(s): 
<input type="text" size="7" name="hasta" id="hasta" onclick="javascript:inicio();"  <? if (isset($fetch33['hasta'])) echo "value='$fetch33[hasta]'"?>/>(00:00 am/pm)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de Fin:
			<input size="10" type="text" name="fecha_retorno" id="fecha_retorno" readonly="readonly" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)
			</TD>
			</TR>
			
			<tr height="50" >
			<TD colspan="2">Duraci&oacute;n (D&iacute;as): 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Duraci&oacute;n (Horas): 
			<input type="text" size="3" name="horas" id="horas" maxlength="2" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Duraci&oacute;n (Minutos): 
			<input type="text" size="3" name="minutos" id="minutos" maxlength="2" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
			</TD>
			</tr>
			<tr height="50" class='tb-fila'>
			<TD colspan="2">
			Numero de Resolucion:
			<input type="text" size="20" name="numero_resolucion" id="numero_resolucion" maxlength="20" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?>/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 
			Fecha:
			<input size="10" type="text" name="fecha" id="fecha" readonly="readonly" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa)
			
			</TD>
			<tr height="50" class='tb-fila'>
			<TD colspan="2">
			Fecha de Aprobado:
			<input size="10" type="text" name="aprobado" id="aprobado" readonly="readonly" value="<?if(isset($fetch33['aprobado'])) echo fecha($fetch33['aprobado']);?>">(dd/mm/aaaa)
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Enterado:
			<input size="10" type="text" name="enterado" id="enterado" readonly="readonly" value="<?if(isset($fetch33['enterado'])) echo fecha($fetch33['enterado']);?>">(dd/mm/aaaa)
			</TD>
			</tr>
			</tr>

			</table>
			</table>
			</div>
			<?
		break;

		case 'Licencias':
			?>
			<div id="registro" style="align:center;">
			<table  border="0">
			<table  border="0" align="center" >
			
			<tr height="50">
			<td><?
			$consulta="SELECT fecing FROM nompersonal where cedula='$_GET[cedula]'";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);
			$fecing = $fetch[fecing];
			$fecing1 = fecha($fetch[fecing]);

			$anio = date('Y')."-01-01";

			if($fecing<=$anio)
				$inicio = $anio;
			elseif($fecing>$anio)
				$inicio = $fecing;

			$consulta="select timestampdiff(month,'$inicio',curdate()) as meses";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);
			$meses = $fetch[meses];

			$horas = $meses * 12;

			$consulta="SELECT sum(dias)as dias,sum(horas) as horas,sum(minutos) as minutos FROM nomexpediente where cedula='$_GET[cedula]' and fecha BETWEEN '".date('Y')."-01-01' and '".date('Y')."-12-31' and tipo_registro='Permisos o Ausencias' and tipo_tiporegistro in (1,2)";
			$resultado=sql_ejecutar($consulta);
			$fetch=fetch_array($resultado);


			$horass = $horas - (($fetch['dias']*8)+$fetch['horas']+($fetch['minutos']/60));
			?>
			FECHA DE INGRESO: <span style="color:green;"><strong><?php echo $fecing1?></strong></span> &nbsp;&nbsp; | &nbsp;&nbsp; REGISTRADO EN <?php echo date('Y');?>: 
			<input size="10" type="text" name="tiempo" style='color:#FF0000;' readonly id="tiempo" value="<? echo $fetch['dias'].' / '.$fetch['horas'].' / '.$fetch['minutos'];?>">(Dias/Horas/Minutos)
			&nbsp;&nbsp; | &nbsp;&nbsp;
			DISPONIBLE HORAS: <span style="color:green;"><strong><?php echo $horass?></strong></span> &nbsp;&nbsp; DIAS: <span style="color:green;"><strong><?php echo $horass/8?></strong></span> 

			<input type="hidden" name="dispo" id="dispo" value="<?php echo $horass?>">
			</TD>
			</tr>
			
			
			<tr class='tb-fila'>
			<td><?
			$consulta="SELECT * FROM nomsuspenciones  where ee=1";
			$resultado=sql_ejecutar($consulta);
			?>
			<div id="tipo_tipo">
			Tipo registro: <SELECT name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="">Seleccione</option>
			<?php
			while($fetch=fetch_array($resultado))
			{
			?>
				<option value="<? echo $fetch['codigo']?>" <? if ($fetch33['tipo_tiporegistro']==$fetch['codigo']) echo "selected='true'"?>><? echo utf8_encode($fetch['descrip'])?></option>";
			<?
			}
			?>
			</SELECT>
			</div>
			
			</TD>
			</tr>
			
			<!--<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>-->
			
			<TR height="50" >
			<TD colspan="2">Desde Hora(s): 
<input type="text" size="7" name="desde" id="desde" onclick="javascript:inicio();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/>(00:00 am/pm)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de inicio:
			<input size="10" type="text" name="fecha_salida" readonly="readonly" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			</TD>
			</TR>
			<TR height="50" class='tb-fila'>
			<TD colspan="2">Hasta Hora(s): 
<input type="text" size="7" name="hasta" id="hasta" onclick="javascript:inicio();"  <? if (isset($fetch33['hasta'])) echo "value='$fetch33[hasta]'"?>/>(00:00 am/pm)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de Fin:
			<input size="10" type="text" name="fecha_retorno" id="fecha_retorno" readonly="readonly" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)
			</TD>
			</TR>
			
			<tr height="50" >
			<TD colspan="2">Duraci&oacute;n (D&iacute;as): 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Duraci&oacute;n (Horas): 
			<input type="text" size="3" name="horas" id="horas" maxlength="2" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Duraci&oacute;n (Minutos): 
			<input type="text" size="3" name="minutos" id="minutos" maxlength="2" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
			</TD>
			</tr>
			<tr height="50" class='tb-fila'>
			<TD colspan="2">
			Numero de Resolucion:
			<input type="text" size="20" name="numero_resolucion" id="numero_resolucion" maxlength="20" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?>/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			 
			Fecha:
			<input size="10" type="text" name="fecha" id="fecha" readonly="readonly" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa)
			
			</TD>
			<tr height="50" class='tb-fila'>
			<TD colspan="2">
			Fecha de Aprobado:
			<input size="10" type="text" name="aprobado" id="aprobado" readonly="readonly" value="<?if(isset($fetch33['aprobado'])) echo fecha($fetch33['aprobado']);?>">(dd/mm/aaaa)
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Enterado:
			<input size="10" type="text" name="enterado" id="enterado" readonly="readonly" value="<?if(isset($fetch33['enterado'])) echo fecha($fetch33['enterado']);?>">(dd/mm/aaaa)
			</TD>
			</tr>
			</tr>

			</table>
			</table>
			</div>
			<?
		break;


		case 'Logros':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<table width="100%" border="0">
			<tr>
			<td><?
			$consulta="SELECT * FROM nomaumentos";
			$resultado=sql_ejecutar($consulta);
			?>
			<div id="tipo_tipo">
			Tipo registro: <SELECT name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="">Seleccione</option>
			<?php
			while($fetch=fetch_array($resultado))
			{
			?>
				<option value="<? echo $fetch['codlogro']?>" <? if ($fetch33['tipo_tiporegistro']==$fetch['codlogro']) echo "selected='true'"?>><? echo $fetch['descrip']?></option>";
			<?
			}
			?>
			</SELECT>
			</div>
			
			</TD>
			</tr>
			</table>
			
			<table width="100%" border="0">
			<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			
			<tr height="50">
			<TD colspan="2">Cargo Anterior: 
			<select name="cod_cargo" id="cod_cargo">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT cod_car, des_car FROM nomcargos";
			$resultado2=sql_ejecutar($consulta);
			while($fetch2=fetch_array($resultado2))
			{
				if($_GET['codigo']!='')
				{
				?>
				<option <?if($fetch33['cod_cargo']==$fetch2['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch2['cod_car']?>"><?echo $fetch2['des_car']?></option>
				<?
				}
				else
				{
				?>
				<option <?if($fetch66['codcargo']==$fetch2['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch2['cod_car']?>"><?echo $fetch2['des_car']?></option>
				<?
				}
			}
			?>
			</select>
			</TD>
			</tr>
			<tr height="50">
			<TD colspan="2">
			Cargo Nuevo:
			<select name="cod_cargo_nuevo" id="cod_cargo_nuevo">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT cod_car, des_car FROM nomcargos";
			$resultado3=sql_ejecutar($consulta);
			while($fetch3=fetch_array($resultado3))
			{
				?>
				<option <?if($fetch33['cod_cargo_nuevo']==$fetch3['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch3['cod_car']?>"><?echo $fetch3['des_car']?></option>
				<?
			}
			?>
			
			</select>
			</td>
			</tr>
			
			<tr height="50">
			<TD colspan="2">
			Monto Anterior: <input type="text" name="monto" id="monto" value="<? if(isset($fetch33['monto'])) echo $fetch33['monto']; else echo "";?>" size="8"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Monto Nuevo: <input type="text" name="monto_nuevo" id="monto_nuevo" value="<? if(isset($fetch33['monto_nuevo'])) echo $fetch33['monto_nuevo']; else echo "";?>" size="8"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de aplicaci&oacute;n:
			<input name="fecha_salida" type="text" id="fecha_salida" value="<?php echo fecha($fetch33['fecha_salida'])?>" maxlength="10" >
			</font>(dd/mm/aaaa)
			<!--<input type="image" name="image66" id="d_fechainicio" src="../lib/jscalendar/cal.gif" />
			<script type="text/javascript"> Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"}); </script>-->
			</a></font></td>
			
            <tr height="50" class='tb-fila'>
					<TD colspan="2">
					Numero Decreto:
					<input type="text" size="20" name="numero_decreto" id="numero_decreto" maxlength="20" <? if (isset($fetch33['numero_decreto'])) echo "value='$fetch33[numero_decreto]'"?>/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 
					Fecha: Decreto
					<input size="10" type="text" name="fecha" id="fecha" readonly="readonly" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa)
					
								Fecha Ascenso:
					<input size="10" type="text" name="aprobado" id="aprobado" readonly="readonly" value="<?if(isset($fetch33['aprobado'])) echo fecha($fetch33['aprobado']);?>">(dd/mm/aaaa)
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 
					</tr>
					</tr>


			</TD>
			</tr>
			</table>
			</table>
			</div>
			<?
		break;

		case 'Penalizaciones':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<table width="100%" border="0">
			<tr>
			<td>
			Tipo registro: <select name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Amonestacion" <? if ($fetch33['tipo_tiporegistro']=="Amonestacion") echo "selected='true'"?>>Amonestacion</option>
			<option value="Suspension" <? if ($fetch33['tipo_tiporegistro']=="Suspencion") echo "selected='true'"?>>Suspension</option>
			</select>
			
			</td>
			</tr>
			</table>
			
			<table width="100%" border="0">
			<TR height="50">
			<TD colspan="2">Motivo: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			<tr height="50">
			<TD colspan="2">Duraci&oacute;n (D&iacute;as): 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de aplicacion:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image77" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			</TD>
			</tr>
			
			</table>
			</table>
			</div>
			<?
		break;
		
		case 'Movimiento de Personal':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<table width="100%" border="0">
			<tr>
			<td>
			Tipo registro: <select name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Traslado de cargo" <? if ($fetch33['tipo_tiporegistro']=="Traslado de cargo") echo "selected='true'"?>>Traslado Cargo</option>
			<option value="Traslado de nomina" <? if ($fetch33['tipo_tiporegistro']=="Traslado de nomina") echo "selected='true'"?>>Traslado Planilla</option>
			<option value="Traslado de gerencias" <? if ($fetch33['tipo_tiporegistro']=="Traslado de gerencias") echo "selected='true'"?>>Traslado Estructura</option>
			<option value="Comision de servicios" <? if ($fetch33['tipo_tiporegistro']=="Comision de servicios") echo "selected='true'"?>>Personal Otra Institucion</option>
			</select>
			
			</td>
			</tr>
			</table>
			
			<table width="100%" border="0">
			<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			
			<tr height="50">
			<TD colspan="2">Cargo Anterior: 
			<select name="cod_cargo" id="cod_cargo">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT cod_car, des_car FROM nomcargos";
			$resultado2=sql_ejecutar($consulta);
			while($fetch2=fetch_array($resultado2))
			{
				if($_GET['codigo']!='')
				{
				?>
				<option <?if($fetch33['cod_cargo']==$fetch2['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch2['cod_car']?>"><?echo $fetch2['des_car']?></option>
				<?
				}
				else
				{
				?>
				<option <?if($fetch66['codcargo']==$fetch2['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch2['cod_car']?>"><?echo $fetch2['des_car']?></option>
				<?
				}
			}
			?>
			</select>
			</TD>
			</tr>
			<tr height="50">
			<TD colspan="2">
			Cargo Nuevo:
			<select name="cod_cargo_nuevo" id="cod_cargo_nuevo">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT cod_car, des_car FROM nomcargos";
			$resultado3=sql_ejecutar($consulta);
			while($fetch3=fetch_array($resultado3))
			{
				?>
				<option <?if($fetch33['cod_cargo_nuevo']==$fetch3['cod_car']) echo "selected='true'" ?>  value="<?echo $fetch3['cod_car']?>"><?echo $fetch3['des_car']?></option>
				<?
			}
			?>
			
			</select>
			</td>
			</tr>
			
			<tr height="50">
			<TD colspan="2">
			Estructura Anterior:
			<select name="gerencia_anterior" id="gerencia_anterior">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT codorg, descrip FROM nomnivel1";
			$resultado4=sql_ejecutar($consulta);
			while($fetch4=fetch_array($resultado4))
			{
				if($_GET['codigo']!='')
				{
				?>
				<option <?if($fetch33['gerencia_anterior']==$fetch4['codorg']) echo "selected='true'" ?>  value="<?echo $fetch4['codorg']?>"><?echo $fetch4['descrip']?></option>
				<?
				}
				else
				{
				?>
				<option <?if($fetch66['codnivel4']==$fetch4['codorg']) echo "selected='true'" ?>  value="<?echo $fetch4['codorg']?>"><?echo $fetch4['descrip']?></option>
				<?
				}
			}
			?>
			
			</select>
			</td>
			</tr>

			<tr height="50">
			<TD colspan="2">
			Estructura Nueva:
			<select name="gerencia_nueva" id="gerencia_nueva">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT codorg, descrip FROM nomnivel1";
			$resultado5=sql_ejecutar($consulta);
			while($fetch5=fetch_array($resultado5))
			{
				?>
				<option <?if($fetch33['gerencia_nueva']==$fetch5['codorg']) echo "selected='true'" ?>  value="<?echo $fetch5['codorg']?>"><?echo $fetch5['descrip']?></option>
				<?
			}
			?>
			
			</select>
			</td>
			</tr>
		
			<tr height="50">
			<TD colspan="2">
			Planilla Anterior:
			<select name="nomina_anterior" id="nomina_anterior">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT codtip, descrip FROM nomtipos_nomina";
			$resultado6=sql_ejecutar($consulta);
			while($fetch6=fetch_array($resultado6))
			{
				if($_GET['codigo']!='')
				{
				?>
				<option <?if($fetch33['nomina_anterior']==$fetch6['codtip']) echo "selected='true'" ?>  value="<?echo $fetch6['codtip']?>"><?echo $fetch6['descrip']?></option>
				<?
				}
				else
				{
				?>
				<option <?if($fetch66['tipnom']==$fetch6['codtip']) echo "selected='true'" ?>  value="<?echo $fetch6['codtip']?>"><?echo $fetch6['descrip']?></option>
				<?
				}
			}
			?>
			
			</select>
			</td>
			</tr>	
	
			<tr height="50">
			<TD colspan="2">
			Planilla Nueva:
			<select name="nomina_nueva" id="nomina_nueva">
			<option value="">Ninguna</option>
			<?
			$consulta="SELECT codtip, descrip FROM nomtipos_nomina";
			$resultado7=sql_ejecutar($consulta);
			while($fetch7=fetch_array($resultado7))
			{
				?>
				<option <?if($fetch33['nomina_nueva']==$fetch7['codtip']) echo "selected='true'" ?>  value="<?echo $fetch7['codtip']?>"><?echo $fetch7['descrip']?></option>
				<?
			}
			?>
			
			</select>
			</td>
			</tr>	
		
			<tr height="50">
			<TD colspan="2">
			Fecha de incorporacion al cargo:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image8" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de culminaci&oacute;n:
			<input size="10" type="text" name="fecha_reintegro" id="fecha_reintegro" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image88" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_reintegro",ifFormat:"%d/%m/%Y",button:"d_fechafin"})
			</script>-->
			</a></font></td>
			
			</TD>
			</tr>
			</table>
			</table>
			</div>
			<?
		break;

		case 'Evaluacion de desempeño':
			?>
			<div id="registro">
			
			
			
			<table width="100%" border="0">
			<TR height="50">
			<TD>Puntaje: 
			<input type="text" size="3" name="puntaje" id="puntaje" maxlength="3" <? if (isset($fetch33['puntaje'])) echo "value='$fetch33[puntaje]'"?>/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Tipo registro: <select name="calificacion" id="calificacion">
			<option value="No satisfactorio" <? if ($fetch33['calificacion']=="No satisfactorio") echo "selected='true'"?>>No satisfactorio</option>
			<option value="Regular" <? if ($fetch33['calificacion']=="Regular") echo "selected='true'"?>>Regular</option>
			<option value="Bueno" <? if ($fetch33['calificacion']=="Bueno") echo "selected='true'"?>>Bueno</option>
			<option value="Excelente" <? if ($fetch33['calificacion']=="Excelente") echo "selected='true'"?>>Excelente</option>
			</select>
			</TD>
			<TR height="50">
			<TD colspan="2">Observacion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			<tr height="50">
			<TD colspan="2">
			Fecha de aplicacion:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image9" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			</TD>
			</tr>
			
			
			</table>
			</div>
			<?
		break;

		case 'Experiencia':
			?>
			<div id="registro">
			<table width="100%" border="0">
			<table width="100%" border="0">
			<tr>
			<td>
			Tipo registro: <select name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Trabajo realizado" <? if ($fetch33['tipo_tiporegistro']=="Trabajo realizado") echo "selected='true'"?>>Trabajo realizado</option>
			<option value="Labor realizada" <? if ($fetch33['tipo_tiporegistro']=="Labor realizada") echo "selected='true'"?>>Labor realizada</option>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Instituci&oacute;n: <input type="text" name="institucion" id="institucion" <? if (isset($fetch33['institucion'])) echo "value='$fetch33[institucion]'"?> size="30"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label>Instituci&oacute;n P&uacute;blica? <input type="checkbox" name="institucion_publica" id="institucion_publica" value="1" <? if (isset($fetch33['institucion_publica'])) echo "checked='true'"?>/></label>
			</TD>
			</tr>
			</table>
			
			<table width="100%" border="0">
			<TR height="50">
			<td>
			Labor realizada: <input type="text" name="labor" id="labor" <? if (isset($fetch33['labor'])) echo "value='$fetch33[labor]'"?> size="30"/> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Cargo desempeñado: <input type="text" name="cod_cargo" id="cod_cargo" <? if (isset($fetch33['cod_cargo'])) echo "value='$fetch33[cod_cargo]'"?> size="30"/>
			</TD>
			</tr>
			
			<tr height="50">
			<TD colspan="2">
			Fecha de ingreso:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image2" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de salida:
			<input size="10" type="text" name="fecha_reintegro" id="fecha_reintegro" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image1" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_reintegro",ifFormat:"%d/%m/%Y",button:"d_fechafin"})
			</script>-->
			</TD>
			</tr>
		
			<TR height="50">
			<TD colspan="2">Motivo de salida: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			</table>
			</table>
			</div>
			<?
		break;

		case 'Antic. prestaciones':
			?>
			<div id="registro">
			
			<table width="100%" border="0">
			<TR height="50">
			<TD colspan="2">Motivo del anticipo: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			
			<tr height="50">
			<TD colspan="2">
			Fecha del anticipo:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image11" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Monto: <input type="text" name="monto" id="monto" <? if (isset($fetch33['monto'])) echo "value='$fetch33[monto]'"?> size="10"/>
			</TD>
			</tr>
			</table>
			</div>
			<?
		break;
		
		case 'Entrega de Uniformes':
			?>
			<div id="registro">
			
			<table width="100%" border="0">
			<TR height="30">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>
			<TR height="30">
			<TD colspan="2">Talla camisa:
			<select name="tcamisa" id="tcamisa">
			<option value="">Ninguno</option>
			<option value="S">S</option>
			<option value="M">M</option>
			<option value="L">L</option>
			<option value="XL">XL</option>
			<option value="XLL">XLL</option>
			<option <? if($fetch33[tcamisa]!='') echo "selected='true'"?> value="<? echo $fetch33[tcamisa]?>"><? echo $fetch33[tcamisa]?></option>
			</select>
			</TD>
			</TR>
		
			<TR height="30">
			<TD colspan="2">Talla chaqueta:
			<select name="tchaqueta" id="tchaqueta">
			<option value="">Ninguno</option>
			<option value="S">S</option>
			<option value="M">M</option>
			<option value="L">L</option>
			<option value="XL">XL</option>
			<option value="XLL">XLL</option>
			<option <? if($fetch33[tchaqueta]!='') echo "selected='true'"?> value="<? echo $fetch33[tchaqueta]?>"><? echo $fetch33[tchaqueta]?></option>
			</select>
			</TD>
			</TR>
			
			<TR height="30">
			<TD colspan="2">Talla bata:
			<select name="tbata" id="tbata">
			<option value="">Ninguno</option>
			<option value="S">S</option>
			<option value="M">M</option>
			<option value="L">L</option>
			<option value="XL">XL</option>
			<option value="XLL">XLL</option>
			<option <? if($fetch33[tbata]!='') echo "selected='true'"?> value="<? echo $fetch33[tbata]?>"><? echo $fetch33[tbata]?></option>
			</select>
			</TD>
			</TR>
		
			<TR height="30">
			<TD colspan="2">Talla pantalon:
			<select name="tpantalon" id="tpantalon">
			<option value="">Ninguno</option>
			<option value="28">28</option>
			<option value="30">30</option>
			<option value="32">32</option>
			<option value="34">34</option>
			<option value="36">36</option>
			<option value="38">38</option>
			<option value="40">40</option>
			<option value="42">42</option>
			<option value="44">44</option>
			<option value="46">46</option>
			<option value="48">48</option>
			<option <? if($fetch33[tpantalon]!='') echo "selected='true'"?> value="<? echo $fetch33[tpantalon]?>"><? echo $fetch33[tpantalon]?></option>
			</select>
			</TD>
			</TR>

			<TR height="30">
			<TD colspan="2">Talla mono:
			<select name="tmono" id="tmono">
			<option value="">Ninguno</option>
			<option value="28">28</option>
			<option value="30">30</option>
			<option value="32">32</option>
			<option value="34">34</option>
			<option value="36">36</option>
			<option value="38">38</option>
			<option value="40">40</option>
			<option value="42">42</option>
			<option value="44">44</option>
			<option value="46">46</option>
			<option value="48">48</option>
			<option <? if($fetch33[tmono]!='') echo "selected='true'"?> value="<? echo $fetch33[tmono]?>"><? echo $fetch33[tmono]?></option>
			</select>
			</TD>
			</TR>

			<TR height="30">
			<TD colspan="2">Talla zapato:
			<select name="tzapato" id="tzapato">
			<option value="">Ninguno</option>
			<? for($i=30;$i<=48;$i++){?>
			<option value="<? echo $i?>"><? echo $i?></option>
			<?}?>
			<option <? if($fetch33[tzapato]!='') echo "selected='true'"?> value="<? echo $fetch33[tzapato]?>"><? echo $fetch33[tzapato]?></option>
			</select>
			</TD>
			</TR>
			
			<tr height="30">
			<TD colspan="2">
			Fecha de entrega:
			<input size="10" type="text" name="fecha_salida" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			<!--<a>
			<input name="image223" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif"/>
			<script type="text/javascript">
			Calendar.setup({inputField:"fecha_salida",ifFormat:"%d/%m/%Y",button:"d_fechainicio"})
			</script>-->
			
			</TD>
			</tr>
			</table>
			</div>
			<?
		break;

		case '3':
			$consulta = "SELECT grado FROM nomcargos WHERE cod_car='$_GET[cargo]'";
			$resultado_car = sql_ejecutar($consulta);
			$fetch_car = fetch_array($resultado_car);
			
			$consulta = "SELECT * FROM nomgradospasos WHERE grado='$fetch_car[grado]'";
			$resultado_gra = sql_ejecutar($consulta);
			$fetch_gra = fetch_array($resultado_gra);
			if($_GET['paso']==1)
				echo "<input align='left' name='txtmonto' type='text' id='txtmonto' style='width:100px' value='$fetch_gra[p1]' maxlength='20'>";
				//echo $fetch_gra['p1'];
			else
			{
				$paso="p".$_GET['paso'];
				echo "<input align='left' name='txtmonto' type='text' id='txtmonto' style='width:100px' value='".($fetch_gra['p1']+$fetch_gra[$paso])."' maxlength='20'>";
				//echo $fetch_gra['p1']+$fetch_gra[$paso];
			}
		break;
		case 'Vacaciones':
			?>
			<div id="registro" style="align:center;">
			<table width="0" border="0" align="center">
			<table width="0" border="0" align="center">
			<tr >
			<td>
			<div id="tipo_tipo">
			Tipo registro: <SELECT name="tipo_tiporegistro" id="tipo_tiporegistro">
			<option value="Vacaciones" <? if ($fetch33['tipo_tiporegistro']=="Vacaciones") echo "selected='true'"?>>Vacaciones</option>
			
			</SELECT>
			</div>
			
			</TD>
			</tr>
			</table>
			
			<table  border="0" align="center">
			<!--<TR height="50">
			<TD colspan="2">Descripcion: <input type="text" name="descripcion" id="descripcion" <? if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?> size="70"/>
			</TD>
			</TR>-->
			
			<TR height="50" class='tb-fila'>
			<TD colspan="2">Fecha solicitud:
			<input size="10" type="text" name="fecha" id="fecha" readonly="readonly" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa)

				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Duraci&oacute;n (D&iacute;as): 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/>

			</TD>
			</TR>
			<TR height="50">
			<TD colspan="2">Fecha de salida:
			<input size="10" type="text" name="fecha_salida" readonly="readonly" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fecha de Retorno:
			<input size="10" type="text" name="fecha_retorno" id="fecha_retorno" readonly="readonly" value="<?if(isset($fetch33['fecha_retorno'])) echo fecha($fetch33['fecha_retorno']);?>">(dd/mm/aaaa)

			</TD>
			</TR>
			<tr height="50" >
			<TD colspan="2">
				<table align="center" width="100%">
					<tr class='tb-fila'>
						<td> VACACIONES CORRESPONDIENTES </td>
					</tr>
					<tr>
					<td>
		(1): Resolucion
		<input type="text" size="10" name="resol1" id="resol1"   <? if (isset($fetch33['resol1'])) echo "value='$fetch33[resol1]'"?>/> Fecha:
			<input size="10" type="text" name="fecha1" readonly="readonly" id="fecha1" value="<?if(isset($fetch33['fecha1'])) echo fecha($fetch33['fecha1']);?>">(dd/mm/aaaa)
		<input type="text" size="5" name="dr1" id="dr1"   <? if (isset($fetch33['dr1'])) echo "value='$fetch33[dr1]'"?>/>(dias)</td>
						</tr>
						<tr >
							<td>
		(2): Resolucion
		<input type="text" size="10" name="resol2" id="resol2"   <? if (isset($fetch33['resol2'])) echo "value='$fetch33[resol2]'"?>/> Fecha:
			<input size="10" type="text" name="fecha2" readonly="readonly" id="fecha2" value="<?if(isset($fetch33['fecha2'])) echo fecha($fetch33['fecha2']);?>">(dd/mm/aaaa)
		<input type="text" size="5" name="dr2" id="dr2"   <? if (isset($fetch33['dr2'])) echo "value='$fetch33[dr2]'"?>/>(dias)</td>
		</tr>
						<tr >
							<td>
		(3): Resolucion
		<input type="text" size="10" name="resol3" id="resol3"  <? if (isset($fetch33['resol3'])) echo "value='$fetch33[resol3]'"?>/> Fecha:
			<input size="10" type="text" name="fecha3" readonly="readonly" id="fecha3" value="<?if(isset($fetch33['fecha3'])) echo fecha($fetch33['fecha3']);?>">(dd/mm/aaaa)
		<input type="text" size="5" name="dr3" id="dr3"   <? if (isset($fetch33['dr3'])) echo "value='$fetch33[dr3]'"?>/>(dias)</td>
						</tr>
				</table>
			</td>
			</tr>
			<tr height="50">
			<TD colspan="2">
			<table  width="100%">
				<tr class='tb-fila'>
					<td> VACACIONES RESTANTES </td>
				</tr>
				<tr>
					<td align="center">
(1): 
<input type="text" size="5" name="hasta" id="hasta" onclick="javascript:vacacion();"  <? if (isset($fetch33['hasta'])) echo "value='$fetch33[hasta]'"?>/>(dias/a&ntilde;o)</td>
				</tr>
				<tr>
					<td align="center">
(2): 
<input type="text" size="5" name="desde" id="desde" onclick="javascript:vacacion();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/>(dias/a&ntilde;o)</td>
				</tr>
				<tr>
					<td align="center">
(3): 
<input type="text" size="5" name="horas" id="horas"  <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>(dias/a&ntilde;o)</td>
				</tr>
				<tr class='tb-fila'>
				<td height='5'><td>
				</tr>
			</table>

<!--
			
			
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Duraci&oacute;n (Minutos): 
			<input type="text" size="3" name="minutos" id="minutos" maxlength="2" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
			</TD>
			</tr>
			<tr height="50">
			<TD colspan="2">Fecha:
			<input size="10" type="text" name="fecha" id="fecha" onclick="javascript:inicio();" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Aprobado:
			<input size="10" type="text" name="aprobado" id="aprobado" onclick="javascript:inicio();" value="<?if(isset($fetch33['aprobado'])) echo fecha($fetch33['aprobado']);?>">(dd/mm/aaaa)
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Enterado:
			<input size="10" type="text" name="enterado" id="enterado" onclick="javascript:inicio();" value="<?if(isset($fetch33['enterado'])) echo fecha($fetch33['enterado']);?>">(dd/mm/aaaa)
-->				

			</TD>
			</tr>
			</table>
			</table>
			</div>
			<?
		break;
		case 'Tiempo Compensatorio':
			?>
			<div id="registro" style="align:center;">
			<table width="0" border="0" align="center">
			<table width="0" border="0" align="center">
			<tr >
			<td align="center">
			<div id="tipo_tipo" >
			Tipo registro: <SELECT name="tipo_tiporegistro" id="tipo_tiporegistro" onchange="mostrar_campo(this.value);">
				<option value="Disminuye" <? if ($fetch33['tipo_tiporegistro']=="Disminuye") echo "selected='true'"?>>Disminuye</option>
			<option value="Aumenta" <? if ($fetch33['tipo_tiporegistro']=="Aumenta") echo "selected='true'"?>>Aumenta</option>
		
			
			</SELECT>
			</div>
			
			</TD>
			</tr>
			<tr height="50" class='tb-fila'>
				<td align="center">
					D&iacute;as: 
			<input type="text" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Horas: 
			<input type="text" size="3" name="horas" id="horas" maxlength="2" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Minutos: 
			<input type="text" size="3" name="minutos" id="minutos" maxlength="2" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
				</td>
			</tr>
			<TR height="50" >
			<TD colspan="2">
			<div id="efectividad" >
			<table width="100%" >
			<tr height="5"><td></td></tr>
			<tr class='tb-fila' ><td>SOLICITUD DE USO </td></tr>
			<tr><td>
			Fecha de Efectividad:
			<input size="10" type="text" name="fecha_salida" onclick="javascript:inicio();" id="fecha_salida" value="<?if(isset($fetch33['fecha_salida'])) echo fecha($fetch33['fecha_salida']);?>">(dd/mm/aaaa)
			
			</TD>
			</TR>
			<TR  >
			<TD colspan="2">Desde Hora(s): 
<input type="text" size="5" name="desde" id="desde" onclick="javascript:inicio();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/>(00:00 am/pm)
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
Hasta Hora(s): 
<input type="text" size="5" name="hasta" id="hasta" onclick="javascript:inicio();"  <? if (isset($fetch33['hasta'])) echo "value='$fetch33[hasta]'"?>/>(00:00 am/pm)
			</TD>
			</TR>
			</table>
			</div>
			
			<tr height="50" class='tb-fila'>
			<TD colspan="2">Fecha:
			<input size="10" type="text" name="fecha" id="fecha" onclick="javascript:inicio();" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">(dd/mm/aaaa) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Aprobado:
			<input size="10" type="text" name="aprobado" id="aprobado" onclick="javascript:inicio();" value="<?if(isset($fetch33['aprobado'])) echo fecha($fetch33['aprobado']);?>">(dd/mm/aaaa)
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Fecha de Enterado:
			<input size="10" type="text" name="enterado" id="enterado" onclick="javascript:inicio();" value="<?if(isset($fetch33['enterado'])) echo fecha($fetch33['enterado']);?>">(dd/mm/aaaa)
			</TD>
			</tr>
			</table>
			<?
		break;
		case 'Documentos':
			?>
			<div id="registro">
				<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
				<link href="../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
				<style>
					.row{
						margin-top: 15px;
					}
					.margin-top-30{
						margin-top: 30px; 
					}
				</style>
				<?php
					if(isset($_GET['codigo']) && $_GET['codigo']!='')
					{
						$conexion = conexion();

						$sql = "SELECT nombre_documento, descripcion, url_documento, fecha_registro, fecha_vencimiento 
								FROM   nomexpediente_documentos 
								WHERE  cod_expediente_det='{$_GET['codigo']}'";

						$res=query($sql,$conexion);

						$documento = $res->fetch_object();

						$fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $documento->fecha_vencimiento);
						$fecha_vencimiento = ($fecha_vencimiento !== false) ? $fecha_vencimiento->format('d/m/Y') : '';	
					}
				?>
				<div class="form-horizontal margin-top-30">
				  <div class="row">
				    <label for="nombre_documento" class="col-sm-2 control-label">Nombre del documento:</label>
				    <div class="col-sm-8">
				      <input type="text" class="form-control" id="nombre_documento" name="nombre_documento" placeholder="Nombre" value="<?php echo isset($fetch33['tipo_tiporegistro']) ? $fetch33['tipo_tiporegistro'] : ''; ?>">
				    </div>
				  </div>
				  <div class="row">
				    <label for="descripcion" class="col-sm-2 control-label">Descripci&oacute;n del documento:</label>
				    <div class="col-sm-8">
				    	<textarea id="descripcion" name="descripcion" class="form-control" rows="2" style="resize:vertical;"><?php echo isset($documento->descripcion) ? $documento->descripcion: ''; ?></textarea>
				    </div>
				  </div>
				  <div class="row">
				    <label for="descripcion" class="col-sm-2 control-label">Fecha de vencimiento:</label>
				    <div class="col-sm-3">
						<input type="text" class="form-control" name="fecha_vencimiento" id="fecha_vencimiento" placeholder="dd/mm/yyyy" value="<?php echo isset($fecha_vencimiento) ? $fecha_vencimiento : ''; ?>" >
				    </div>
				  </div>
				  <?php
				  if($_GET['codigo']=='')
				  {
				  	?>
						  <div class="row">
						  	<label for="archivo" class="col-sm-2 control-label">Cargar documento:</label>
						    <div class="col-sm-5">
						    	<input type="file" name="archivo" id="archivo" accept="image/*" style="border: none; outline:none;">
						    </div>
						  </div>
				  	<?php
				  }
				  else
				  { ?> <div class="row">
							<img src="<?php echo $documento->url_documento; ?>" class="img-responsive" alt="Documento" style="max-width: 250px; margin: auto; margin-bottom: 40px; margin-top: 30px">
					   </div>
					<?php
				  }
				  ?>
				  </div>
			</div>
			<?php
		break;
	}
?>
