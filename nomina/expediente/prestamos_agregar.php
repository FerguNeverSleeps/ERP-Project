<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?php
include ("../header.php");
include("../lib/common.php");
include ("../paginas/func_bd.php");
$conexion=conexion();

if(isset($_POST['numcuo1']))
{
	$consulta="INSERT INTO nomprestamos_cabecera "
                . "SET numpre=$_POST[numpre], "
                . "ficha=$_POST[ficha], "
                . "fechaapro='".fecha_sql($_POST[fechaap])."', "
                . "fecpricup='".fecha_sql($_POST[fecha1])."', "
                . "monto=$_POST[montopre], "
                . "estadopre='Pendiente', "
                . "detalle='$_POST[descrip]', "
                . "codigopr='$_POST[tipo]', "
                . "codnom=$_SESSION[codigo_nomina], "
                . "totpres=$_POST[montopre], "
                . "cuotas=$_POST[numcuota], "
                . "mtocuota=$_POST[montocuota], "
                . "diciembre='$_POST[diciembre]',"
                . "gastos_admon='$_POST[gastos_admon]',"
                . "id_tipoprestamo=$_POST[tipos_prestamos],"
                . "frededu=$_POST[frededu]";
	//echo $consulta;exit;
	if($resultado=query($consulta,$conexion))
	{
	$i=1;
	while($i<=$_POST['numcuota'])
	{
		$numcuo="numcuo".$i;
		$vence="vence".$i;
		$cad=explode("/",$_POST[$vence]);
		$salini="salini".$i;
		$mtocuo="mtocuo".$i;
		$salfin="salfin".$i;
		$consulta="INSERT INTO nomprestamos_detalles SET numpre=$_POST[numpre], ficha=$_POST[ficha], numcuo='".$_POST[$numcuo]."', fechaven='".fecha_sql($_POST[$vence])."', anioven=$cad[2], mesven=$cad[1], salinicial=".$_POST[$salini].", montocuo=".$_POST[$mtocuo].", salfinal=".$_POST[$salfin].", estadopre='Pendiente', codnom=$_SESSION[codigo_nomina]";
		$resultado2=query($consulta,$conexion);
		$i+=1;
	}
	if($resultado2)
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO GUARDADO EXITOSAMENTE!!!")
		parent.cont.location.href="prestamos_list.php"
 		</script>
		<?php	
	}
	else
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO NO GUARDADO !!!")
 		</script>
		<?php	
	}
	}
	else
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO NO GUARDADO !!!")
 		</script>
		<?php	
	}
}
?>
<script>
function generar_cuotas()
{
	var numpre=document.getElementById('numpre')
	var ficha=document.getElementById('ficha')
	var monto=document.getElementById('montopre')
	var cuota=document.getElementById('montocuota')
	var numcuota=document.getElementById('numcuota')
	var fecha1=document.getElementById('fecha1')
	var division=document.getElementById('divcuo')
	var diciembre=document.getElementById('diciembre')
	var periodo=document.getElementById('periodo_')
	var periodo_bi=document.getElementById('periodo_bi')
	var len = document.formulario1.frededu
	var frededux
	var i

	for (i = 0; i < len.length; i++)
	{
		if ( len[i].checked )
		{
			frededux = len[i].value;
			break;
		}
	}

	if((monto.value=='')||(monto.value==0)||(cuota.value=='')||(cuota.value==0)||(numcuota.value=='')||(numcuota.value==0)||(fecha1.value=='')||(ficha.value=='')||(tipo.value=='')||(frededux==''))
	{
		alert("Complete los campos marcados con asterisco (*)")
		return
	}

	var contenido_division=abrirAjax()
	contenido_division.open("GET", "prestamos_calc_cuotas.php?numpre="+numpre.value+"&ficha="+ficha.value+"&montopre="+monto.value+"&montocuota="+cuota.value+"&numcuota="+numcuota.value+"&fecha1="+fecha1.value+"&frededu="+frededux+"&diciembre="+diciembre.value+"&periodo="+periodo.value+"&periodo_bi="+periodo_bi.value, true)
   	contenido_division.onreadystatechange=function() 
	{
		if (contenido_division.readyState==4)
		{
			division.parentNode.innerHTML = contenido_division.responseText;
		}
	}
	contenido_division.send(null);
	
}
function buscar_empleado()
{
	AbrirVentana('buscar_empleado.php',660,700,0);
}
function buscar_concepto()
{
	AbrirVentana('buscar_tipo_prestamo.php',660,700,0);
}
function CerrarVentana()
{
	javascript:window.close();
}
function cuotas()
{
	var monto=document.getElementById('montopre')
	var cuota=document.getElementById('montocuota')
	var numcuota=document.getElementById('numcuota')
	var resultado=0
	var mto=0
	var a=b=0
	if((cuota.value==0)||(cuota.value==''))
	{
		resultado=monto.value/numcuota.value
		resultado=resultado.toFixed(2)
		//alert(resultado)
		document.formulario1.montocuota.value=resultado
		document.formulario1.mcuota.value=resultado
	}
	else if((numcuota.value==0)||(numcuota.value==''))
	{
		resultado=monto.value/cuota.value
		mto=cuota.value/1
		//mto=mto.toFixed(2)
		//alert(resultado)
		a=resultado.toFixed(2)
		b=resultado.toFixed(0)
		
		b=parseInt(b)
		if(a==b)
		{
			b=parseInt(b)
		}
		else if(a>b)
		{
			b=parseInt(b)+1
		}	
		document.formulario1.numcuota.value=b
		document.formulario1.mcuota.value=mto.toFixed(2)
	}
	else
		alert("Cuotas a monto fijo o el Número de cuotas debe ser cero(0) o en blanco")
}
$(document).ready(function() {
  $(".sel").select2();
});

function fixit( obj ) {
    obj.value = parseFloat( obj.value ).toFixed( 2 )
  }
  //Función que permite solo Números
function ValidaSoloNumeros() {
 if ((event.keyCode < 48) || (event.keyCode > 57)) 
  event.returnValue = false;
}
</script>

<script type="text/javascript" src="../tabber.js"></script>
<link href="../estilos.css" rel="stylesheet" type="text/css" />
<?php include("../../includes/dependencias.php"); ?>
<?php 
function colaborador(){
	$conexion=conexion();
	$sql = "SELECT apenom,cedula,ficha,nomposicion_id FROM nompersonal";
	$res =  query($sql,$conexion);
	$apenom = array();$cedula=array();$nomposicion=array();$ficha=array();$colab=0;
	while ($row = fetch_array($res)){
		array_push($apenom,$row['apenom']);
		array_push($cedula,$row['cedula']);
		array_push($ficha,$row['ficha']);
		array_push($nomposicion,$row['nomposicion_id']);
		$colab++;
	}
	for($i=0;$i<$colab;$i++){
		echo "<OPTION VALUE=".$ficha[$i].">".$ficha[$i]." - ".$cedula[$i]." - ".$nomposicion[$i]." - ".$apenom[$i]."</OPTION>";
	}
}
function acreedor($acreedor=null){
	$conexion=conexion();
	$sql = "SELECT codigopr,descrip FROM nomprestamos";
	$res =  query($sql,$conexion);
	$codigopr=array();$cedula=array();$descrip=array();$acre=0;
	while ($row = fetch_array($res)){
		array_push($codigopr,$row['codigopr']);
		array_push($cedula,$row['cedula']);
		array_push($descrip,$row['descrip']);
		$acre++;
	}
	for($j=0;$j<$acre;$j++){
        if($acreedor==$codigopr[$j])
            echo "<OPTION VALUE=".$codigopr[$j]." SELECTED>".$codigopr[$j]." - ".$descrip[$j]."</OPTION>";
        else
            echo "<OPTION VALUE=".$codigopr[$j].">".$codigopr[$j]." - ".$descrip[$j]."</OPTION>";
	}
}
$consulta="SELECT MAX(numpre) as numpre FROM nomprestamos_cabecera_tmp;";
$resultado=query($consulta,$conexion);
$fetch=fetch_array($resultado);
if($fetch['numpre'] != "")
	$numpre=$fetch['numpre']+1;
else{
	$consulta_2="SELECT MAX(numpre) as numpre FROM nomprestamos_cabecera;";
	$resultado_2=query($consulta_2,$conexion);
	$fetch_2=fetch_array($resultado_2);
	$numpre=$fetch_2['numpre']+1;

}
?>

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<img src="../imagenes/21.png" width="22" height="22" class="icon"> Agregar Datos del Prestamo
				</div>
			</div>
			<div class="portlet-body">
				
				<div class="row">
					<div class="col-md-12">
                    
						<input type="hidden" name="ficha" id="ficha" value="<? echo $fetch_emp['ficha'];?>" />
						<input type="hidden" name="acreedor" id="acreedor" value="<? echo $fetch_subtipo['acreedor'];?>" />
						<div class="row">
							<div class="col-md-3">
								<label for="rg-from">Numero de Prestamo:</label>
								<input class="form-control" type="text" name="numpre" align="right" id="numpre" value="<? echo $numpre;?>" readonly>
							</div>

							<div class="col-md-3">
								<label for="tipo">Acreedor:  *</label>
									<select class="sel form-control" name="tipo" id="tipo">
										<?php acreedor($fetch_subtipo['acreedor']); ?>
									</select>
								</label>
							</div>

							<div class="col-md-3">
								<label for="tipo">Tipos Prestamos:  *</label>
									<?php
										$consulta_prestamo="SELECT * from tipos_prestamos";
										$resultado_prestamo=query($consulta_prestamo,$conexion);
										;
									?>
									<select class="sel form-control" name="tipos_prestamos" id="tipos_prestamos">
										<option selected disabled>Seleccione el tipo de prestamo</option>
										<?php while ($fetch_prestamo=fetch_array($resultado_prestamo)) {
											if($fetch_subtipo['tip_prestamo'] == $fetch_prestamo["id_tipos_prestamos"])
												echo "<option value='".$fetch_prestamo["id_tipos_prestamos"]."' selected>".$fetch_prestamo["nom_tipos_prestamos"]."</option>";
											else
												echo "<option value='".$fetch_prestamo["id_tipos_prestamos"]."'>".$fetch_prestamo["nom_tipos_prestamos"]."</option>";
										} ?>
									</select>
								</label>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<label for="rg-from">Monto de Prestamo:  *</label>
								<input class="form-control" type="text" name="montopre" align="right" id="montopre" onchange="javascript:fixit(this);"/>
							</div>
							<div class="col-md-4">
								<label for="rg-from">Cuotas a monto fijo: </label>
								<input class="form-control" type="text" name="montocuota" align="right" id="montocuota" onchange="javascript:fixit(this);"/>
							</div>
							<div class="col-md-4">
								<label for="rg-from">Numero de cuotas: </label>
								<input class="form-control" type="text" name="numcuota" align="right" onblur="javascript:cuotas();" id="numcuota" maxlength="8" size="10" onkeypress="javascript:ValidaSoloNumeros();"/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								<label for="rg-from">Descripcion del prestamo: </label>
								<input class="form-control" type="text" name="descrip" align="right" id="descrip" maxlength="300" size="50"/>
							</div>
							<div class="col-md-2">
								<label for="diciembre">¿Descuenta en diciembre?:</label>
								<select class="form-control" name="diciembre" id="diciembre">
									<option value="1">No</option>
									<option value="0">Si</option>
								</select>
							</div>
						<div class="col-md-2">
							<label for="gastos_admon">Gastos Admon. (%):</label>
							<input class="form-control" type="text" name="gastos_admon" align="right" id="gastos_admon" maxlength="10" size="10"/>
						</div>
						<div class="col-md-12">&nbsp;
						</div>
						<div id="frecuencia" class="col-md-12">
							<label>Frecuencia:</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu1" value="1">Semanal</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu2" value="2" checked>Quincenal</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu3" value="3">Mensual</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu4" value="4">Bisemanal</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu5" value="5">Periodo</label>
						</div>
						<div class="col-md-12">&nbsp;
						</div>
						<div id="bisemanal" class="col-md-6">
							<label>Periodo:</label>
								<?php
									$consulta_bisemanas="SELECT * from bisemanas";
									$resultado_bisemanas=query($consulta_bisemanas,$conexion);
									;
								?>
							<select id="periodo_" name="periodo_" class="sel form-control">
							<option selected value="">Seleccione el Periodo</option>
							<?php while ($fetch=fetch_array($resultado_bisemanas)) {
								echo "<option value='".$fetch["numBisemana"]."'>".$fetch["numBisemana"]." - ".date('d-m-Y',strtotime($fetch["fechaInicio"]))." - ".date('d-m-Y',strtotime($fetch["fechaFin"]))."</option>";
							} ?>
							</select>
						</div>
						<div id="periodo" class="col-md-6">
								<label>Periodo (días):</label>
									<input type="number" class="form-control" id="periodo_bi" name="periodo_bi">
						</div>
						<br><br>
						<div class="col-md-12">&nbsp;
						</div>
						<div class='col-sm-6'>
							<label for="datetimepicker1">Fecha de aprobacion: (*)</label>
							<div class="input-group">
								<input id="fechaap" data-provide="datepicker" autocomplete="off" data-date-format="dd/mm/yyyy" name="fechaap" value="<?php if ($registro_id!=0){ echo $periodo_ini; }  ?>" type="text" class="form-control" maxlenght="60" size="70" placeholder="dd/mm/aaaa">
								<div class="input-group-addon date">
									<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
						<div class='col-sm-6'>
							<label for="datetimepicker2">Fecha de vencimiento 1° cuota: (*)</label>
							<div class="input-group">
								<input id="fecha1" data-provide="datepicker"  autocomplete="off" data-date-format="dd/mm/yyyy" name="fecha1" value="<?php if ($registro_id!=0){ echo $periodo_ini; }  ?>" type="text" class="form-control" maxlenght="60" size="70" placeholder="dd/mm/aaaa">
								<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
								</div>
							</div>
						</div>
						<br><br>
						<div class="col-md-12">&nbsp;
						</div>
						<br><br>
						<div class="col-md-6">
							<label for="rg-from">Monto de cuotas: </label>
								<input class="form-control" type="text" name="mcuota" id="mcuota" readonly="true" size="10" style="color : #000099; font-size : 14pt; text-align : right;" value="0.0" align="left" class="form-control">
						</div>
						<div class="col-md-6">
							<br>
							<input class="btn btn-primary" value="Generar Cuotas" onclick="javascript:generar_cuotas();" title="Debe generar las cuotas antes de enviar" required>
							<!--<input class="btn btn-primary" value="Guardar Cuotas" onclick="javascript:document.formulario1.submit();">-->
						</div>
						<div class="col-md-12">&nbsp;
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>

<table width="100%" border="0">
<TR>
<TD>
<div id="divcuo"></div>
</TD>
</TR>
</table>

<table width="100%"  class="tb-head">
<tr>
<td width="7%"><label></label></td>
<td width="10%">&nbsp;</td>
<td width="9%">&nbsp;</td>
<td>&nbsp;</td>
<td width="2%"></td>
<td width="2%"></td>
<td width="2%"><?php //btn('ok','document.formulario1.submit();',2,'Enviar'); ?></td>
</tr>
</table>

<table align="center" border="0">
<TR>
<TD>
<div id="acum">
</div>
</tr>
</TR>
</table>
<p>&nbsp;</p>
<p>
</p>
<p>&nbsp; </p>
<script type="text/javascript">
	 $(document).ready(function() { 
		$('#fechaap,#fecha1').datepicker({
		todayHighlight: true,
		autoclose:true,
		orientation: "bottom left",
		dateFormat: 'dd/mm/yy',
		templates: {
			leftArrow: '<i class="la la-angle-left"></i>',
			rightArrow: '<i class="la la-angle-right"></i>'
		}
		});
		$("#periodo").hide();
		$("#bisemanal").hide();
		$("#frededu4").on("change",function(){
			
			$("#frecuencia").removeClass("col-md-12");
			$("#frecuencia").addClass("col-md-6");
			$("#bisemanal").show();
			$("#periodo").hide();
		});
		$("#frededu5").on("change",function(){
			$("#frecuencia").removeClass("col-md-12");
			$("#frecuencia").addClass("col-md-6");
			$("#periodo").show();
			$("#bisemanal").hide();
		});
		$("#frededu1,#frededu2,#frededu3").on("change",function(){
			$("#frecuencia").removeClass("col-md-6");
			$("#frecuencia").addClass("col-md-12");
			$("#periodo").hide();
			$("#bisemanal").hide();
		});
	 });
</script>
</body>
</html>