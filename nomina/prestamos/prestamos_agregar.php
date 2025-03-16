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
    $ruta_destino     = $_REQUEST['ruta_destino'];
    $cuenta_destino   = $_REQUEST['cuenta_destino'];
    $producto_destino = $_REQUEST['producto_destino'];

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
                . "id_tipoprestamo='$_POST[tipos_prestamos]',"
                . "ruta_destino='$_POST[ruta_destino]',"
                . "cuenta_destino='$_POST[cuenta_destino]',"
                . "producto_destino='$_POST[producto_destino]',"
                . "frededu='$_POST[frededu]'";
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
	var len = document.form1.frededu
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
		document.form1.montocuota.value=resultado
		document.form1.mcuota.value=resultado
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
		document.form1.numcuota.value=b
		document.form1.mcuota.value=mto.toFixed(2)
	}
	else
		alert("Cuotas a monto fijo o el Número de cuotas debe ser cero(0) o en blanco")
}
/*
function Aceptar()
{
	var ficha=document.getElementById('ficha')
	var concepto=document.getElementById('concepto')
	var anio=document.getElementById('anio')
	var division=document.getElementById('acum')
	var contenido_division=abrirAjax()
	contenido_division.open("GET", "mostrar_acumulados.php?ficha="+ficha.value+"&opcion=1&concepto="+concepto.value+"&anio="+anio.value, true)
   	contenido_division.onreadystatechange=function() 
	{
		if (contenido_division.readyState==4)
		{
			division.parentNode.innerHTML = contenido_division.responseText;
		}
	}
	contenido_division.send(null);	
}*/
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
<form action="" method="post" name="form1" id="form1"  autocomplete="off">
<?php include("../../includes/dependencias.php"); ?>
<style>
	.mt-2{
		margin-top : 20px;
	}
</style>
<?php 
function colaborador(){
	$conexion=conexion();
	$sql = "SELECT apenom,cedula,ficha,nomposicion_id FROM nompersonal WHERE estado <> 'Egresado'";
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
function acreedor(){
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
		echo "<OPTION VALUE=".$codigopr[$j].">".$codigopr[$j]." - ".$descrip[$j]."</OPTION>";
	}
}
$consulta="SELECT MAX(numpre) as numpre FROM nomprestamos_cabecera";
$resultado=query($consulta,$conexion);
$fetch=fetch_array($resultado);
$numpre=$fetch['numpre']+1;
?>

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<img src="../imagenes/21.png" width="22" height="22" class="icon"> Agregar Prestamos
				</div>
				<div class="actions">
					<a class="btn btn-sm blue"  onclick="javascript: window.location='prestamos_list.php'">
						<!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
						<i class="fa fa-arrow-left"></i> Regresar
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<?php
					$consulta="SELECT MAX(numpre) as numpre FROM nomprestamos_cabecera";
					$resultado=query($consulta,$conexion);
					$fetch=fetch_array($resultado);
					$numpre=$fetch['numpre']+1;
				?>
				<div class="row">
					<div class="col-md-3">
						<div class="col-md-6">
							<label for="numpre">Numero de Prestamo:</label>
						</div>
						<div class="col-md-6">
							<? echo $numpre;?><input class="form-control" type="hidden" name="numpre" id="numpre" value="<? echo $numpre;?>">
							
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="ficha">N° de colaborador:  *</label>
								<!--<input class="form-control" type="text" name="ficha" align="right" id="ficha"  maxlength="6" size="8"/>-->
								<select class="sel form-control" name="ficha" id="ficha">
									<option selected disabled>Seleccione el colaborador</option>
									<?php colaborador(); ?>
								</select>

							</label>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tipo">Acreedor:  *</label>
								<!--<input class="form-control" type="text" name="tipo" id="tipo" align="right" maxlength="5"  size="8"/>-->
								<select class="sel form-control" name="tipo" id="tipo">
									<option selected disabled>Seleccione el acreedor</option>
									<?php acreedor(); ?>
								</select>
							</label>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="tipo">Tipos Prestamos:  *</label>
								<?php
									$consulta_prestamo="SELECT * from tipos_prestamos";
									$resultado_prestamo=query($consulta_prestamo,$conexion);
									;
								?>
								<select class="sel form-control" name="tipos_prestamos" id="tipos_prestamos">
									<option selected disabled>Seleccione el tipo de prestamo</option>
									<?php while ($fetch_prestamo=fetch_array($resultado_prestamo)) {
										echo "<option value='".$fetch_prestamo["id_tipos_prestamos"]."'>".$fetch_prestamo["nom_tipos_prestamos"]."</option>";
									} ?>
								</select>
							</label>
						</div>
					</div>
					<br><br>
					<div class="col-md-4">
						<label for="rg-from">Monto de Prestamo:  *</label>
  						<div class="form-group">
    						<input class="form-control" type="text" name="montopre" align="right" id="montopre" onchange="javascript:fixit(this);"/>
  						</div>
					</div>
					<div class="col-md-4">
						<label for="rg-from">Cuotas a monto fijo: </label>
  						<div class="form-group">
    						<input class="form-control" type="text" name="montocuota" align="right" id="montocuota" onchange="javascript:fixit(this);"/>
  						</div>
					</div>
					<div class="col-md-4">
						<label for="rg-from">Numero de cuotas: </label>
  						<div class="form-group">
    						<input class="form-control" type="text" name="numcuota" align="right" onblur="javascript:cuotas();" id="numcuota" maxlength="8" size="10" onkeypress="javascript:ValidaSoloNumeros();"/>
  						</div>
					</div>
					<br><br>
					<div class="col-md-8">
						<label for="rg-from">Descripcion del prestamo: </label>
  						<div class="form-group">
    						<input class="form-control" type="text" name="descrip" align="right" id="descrip" maxlength="300" size="50"/>
  						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="diciembre">¿Descuenta en diciembre?:</label>
							<select class="form-control" name="diciembre" id="diciembre">
								<option value="1">No</option>
								<option value="0">Si</option>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label for="gastos_admon">Gastos Admon. (%):</label>
							<input class="form-control" type="text" name="gastos_admon" align="right" id="gastos_admon" maxlength="10" size="10"/>
						</div>
					</div>
					<br><br>
					<div id="frecuencia" class="col-md-12">
						<div class="form-group">
							<label>Frecuencia:</label>
	  						<label class="radio-inline"><input type="radio" name="frededu" id="frededu1" value="1">Semanal</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu2" value="2" checked>Quincenal</label>
							<label class="radio-inline"><input type="radio" name="frededu" id="frededu3" value="3">Mensual</label>
                            <label class="radio-inline"><input type="radio" name="frededu" id="frededu4" value="4">Bisemanal</label>
                            <label class="radio-inline"><input type="radio" name="frededu" id="frededu5" value="5">Periodo</label>
						</div>
					</div>
					<div id="bisemanal" class="col-md-6">
						<div class="form-group">
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
					</div>
					<div id="periodo" class="col-md-6">
						<div class="form-group">
							<label>Periodo (días):</label>
								<input type="number" class="form-control" id="periodo_bi" name="periodo_bi">
						</div>
					</div>
					<br><br>
					<div class='col-sm-6'>
			            <div class="form-group">
			            	<label for="datetimepicker1">Fecha de aprobacion:</label>
			            	<div class="input-group">
			            	   	<input id="fechaap" data-provide="datepicker" data-date-format="dd/mm/yyyy" name="fechaap" value="<?php if ($registro_id!=0){ echo $periodo_ini; }  ?>" type="text" class="form-control" maxlenght="60" size="70" placeholder="dd/mm/aaaa">
                            	<div class="input-group-addon date">
                                	<span class="glyphicon glyphicon-calendar"></span>
                            	</div>
                        	</div>
			            </div>
			        </div>
			        <div class='col-sm-6'>
			            <div class="form-group">
			            	<label for="datetimepicker2">Fecha de vencimiento 1° cuota:</label>
			            	<div class="input-group">
                                            <input id="fecha1" data-provide="datepicker" data-date-format="dd/mm/yyyy" name="fecha1" value="<?php if ($registro_id!=0){ echo $periodo_ini; }  ?>" type="text" class="form-control" maxlenght="60" size="70" placeholder="dd/mm/aaaa">
                                            <div class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                            </div>
                                        </div>
			            </div>
			        </div>
			        <br><br>
			        <div class="col-md-6">
			        	<label for="rg-from">Monto de cuotas: </label>
  						<div class="form-group">
    						<input class="form-control" type="text" name="mcuota" id="mcuota" readonly="true" size="10" style="color : #000099; font-size : 14pt; text-align : right;" value="0.0" align="left" class="form-control">
  						</div>
			        </div>
			        <br><br>

					<div class="col-md-12 mt-2">
						<div class="form-group">
							<label class="control-label col-md-2">Ruta:</label>
							<div class="col-md-4">
								<select name="ruta_destino" id="ruta_destino" class="form-control">
									<option value="0">Seleccione...</option>
									<option value="000000013" <?php $ruta_destino=$fetch['ruta_destino']; if($ruta_destino=="000000013") echo "selected" ;?>>BANCO NACIONAL DE PANAMA</option>
									<option value="000001575" <?php if($ruta_destino=="000001575") echo "selected" ;?>>BANCO LAFISE PANAMÁ, S.A.</option>
									<option value="000001384" <?php if($ruta_destino=="000001384") echo "selected" ;?>>BAC INTERNATIONAL BANK</option>
									<option value="000001083" <?php if($ruta_destino=="000001083") echo "selected" ;?>>BANCO ALIADO</option>
									<option value="000001504" <?php if($ruta_destino=="000001504") echo "selected" ;?>>BANCO AZTECA</option>
									<option value="000001562" <?php if($ruta_destino=="000001562") echo "selected" ;?>>BANCO DELTA</option>
									<option value="000001724" <?php if($ruta_destino=="000001724") echo "selected" ;?>>BANCO FICOHSA PANAMA</option>
									<option value="000000071" <?php if($ruta_destino=="000000071") echo "selected" ;?>>BANCO GENERAL</option>
									<option value="000001517" <?php if($ruta_destino=="000001517") echo "selected" ;?>>BANCO PICHINCHA PANAMA</option>
									<option value="000001258" <?php if($ruta_destino=="000001258") echo "selected" ;?>>CANAL BANK</option>
									<option value="000001588" <?php if($ruta_destino=="000001588") echo "selected" ;?>>BANESCO</option>
									<option value="000001614" <?php if($ruta_destino=="000001614") echo "selected" ;?>>BANISI, S.A.</option>
									<option value="000001164" <?php if($ruta_destino=="000001164") echo "selected" ;?>>BANK OF CHINA</option>
									<option value="000001685" <?php if($ruta_destino=="000001685") echo "selected" ;?>>BALBOA BANK & TRUST</option>
									<option value="000001397" <?php if($ruta_destino=="000001397") echo "selected" ;?>>BCT BANK</option>
									<option value="000000518" <?php if($ruta_destino=="000000518") echo "selected" ;?>>BICSA</option>
									<option value="000002529" <?php if($ruta_destino=="000002529") echo "selected" ;?>>CACECHI</option>
									<option value="000000770" <?php if($ruta_destino=="000000770") echo "selected" ;?>>CAJA DE AHORROS</option>
									<option value="000001591" <?php if($ruta_destino=="000001591") echo "selected" ;?>>CAPITAL BANK</option>
									<option value="000000039" <?php if($ruta_destino=="000000039") echo "selected" ;?>>CITIBANK</option>
									<option value="000002532" <?php if($ruta_destino=="000002532") echo "selected" ;?>>COEDUCO</option>
									<option value="000002516" <?php if($ruta_destino=="000002516") echo "selected" ;?>>COOESAN</option>
									<option value="000002503" <?php if($ruta_destino=="000002503") echo "selected" ;?>>COOPEDUC</option>
									<option value="000005005" <?php if($ruta_destino=="000005005") echo "selected" ;?>>COOPERATIVA CRISTOBAL</option>
									<option value="000000712" <?php if($ruta_destino=="000000712") echo "selected" ;?>>COOPERATIVA DE PROFESIONALES</option>
									<option value="000002545" <?php if($ruta_destino=="000002545") echo "selected" ;?>>COOPEVE</option>
									<option value="000001106" <?php if($ruta_destino=="000001106") echo "selected" ;?>>CREDICORP BANK</option>
									<option value="000001151" <?php if($ruta_destino=="000001151") echo "selected" ;?>>GLOBAL BANK</option>
									<option value="000000026" <?php if($ruta_destino=="000000026") echo "selected" ;?>>BANISTMO S.A.</option>
									<option value="000001630" <?php if($ruta_destino=="000001630") echo "selected" ;?>>MERCANTIL BANK</option>
									<option value="000001067" <?php if($ruta_destino=="000001067") echo "selected" ;?>>METROBANK S.A.</option>
									<option value="000001478" <?php if($ruta_destino=="000001478") echo "selected" ;?>>MMG BANK</option>
									<option value="000000372" <?php if($ruta_destino=="000000372") echo "selected" ;?>>MULTIBANK</option>
									<option value="000001672" <?php if($ruta_destino=="000001672") echo "selected" ;?>>PRIVAL BANK</option>
									<option value="000000424" <?php if($ruta_destino=="000000424") echo "selected" ;?>>THE BANK OF NOVA SCOTIA</option>
									<option value="000001494" <?php if($ruta_destino=="000001494") echo "selected" ;?>>ST. GEORGES BANK</option>
									<option value="000000408" <?php if($ruta_destino=="000000408") echo "selected" ;?>>TOWERBANK</option>
									<option value="000001708" <?php if($ruta_destino=="000001708") echo "selected" ;?>>UNIBANK</option>
									<option value="000001740" <?php if($ruta_destino=="000001740") echo "selected" ;?>>ALLBANK</option>
									<option value="000001656" <?php if($ruta_destino=="000001656") echo "selected" ;?>>BBP BANK, S.A.</option>
									<option value="000005018" <?php if($ruta_destino=="000005018") echo "selected" ;?>>EDIOACC, R.L</option>
									<option value="000000916" <?php if($ruta_destino=="000000916") echo "selected" ;?>>BANCO DEL PACÍFICO (PANAMÁ), S.A.</option>
									<option value="000001805" <?php if($ruta_destino=="000001805") echo "selected" ;?>>ATLAS BANK</option>
									<option value="000005021" <?php if($ruta_destino=="000005021") echo "selected" ;?>>ECASESO</option>
									<option value="000005034" <?php if($ruta_destino=="000005034") echo "selected" ;?>>COOPRAC, R.L.</option>
									<option value="000000181" <?php if($ruta_destino=="000000181") echo "selected" ;?>>DAVIVIENDA</option>
								</select>
							</div>
						</div>

					</div>
			        	<br>
					<div class="col-md-12 mt-2">
						<div class="form-group">
							<label class="control-label col-md-2">Producto:</label>
							<div class="col-md-4">
								<select name="producto_destino" id="producto_destino" class="form-control">
									<option value="0" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="0") echo "selected" ;?>>Seleccione...</option>
									<option value="04" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="04") echo "selected" ;?>>CUENTA DE AHORROS</option>
									<option value="03-1" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="03-1") echo "selected" ;?>>CUENTA CORRIENTE</option>
									<option value="03-2" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="03-2") echo "selected" ;?>>TARJETAS DE CREDITO Y PREPAGADAS</option>
									<option value="07" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="07") echo "selected" ;?>>PRESTAMOS</option>
								</select>
							</div>
						</div>
					</div>
			        	<br>
					<div class="col-md-12 mt-2">
						<div class="form-group">
							<label class="control-label col-md-2">Cuenta:</label>
							<div class="col-md-4">
								<input class="form-control" name="cuenta_destino" type="text" id="cuenta_destino" value="<?php  echo $fetch['cuenta_destino'];  ?>">
							</div>	
						</div>
					</div>
			        <div class="col-md-4">
					</div>
			        <div class="col-md-6">
			        	<br>
			        	<input class="btn btn-primary" value="Generar Cuotas" onclick="javascript:generar_cuotas();" title="Debe generar las cuotas antes de enviar" required>
			        	<input class="btn btn-primary" value="Guardar Cuotas" onclick="javascript:document.form1.submit();">
			        </div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<!--
Numero de prestamo:   <?// echo $numpre;?><input type="hidden" name="numpre" id="numpre" value="<?// echo $numpre;?>">
No. Colaborador: <input type="text" name="ficha" align="right" id="ficha"  maxlength="6" size="8"/>

Acreedor: <input type="text" name="tipo" id="tipo" align="right" maxlength="5"  size="8"/>

<strong>Monto del prestamo:</strong> <input type="text" name="montopre" align="right" id="montopre"  maxlength="8" size="10"/><font color="Red">&nbsp;*</font>

<strong>Cuotas a monto fijo:</strong> <input type="text" name="montocuota" align="right" id="montocuota" maxlength="8" size="10"/><font color="Red">&nbsp;*</font>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<strong>Número de cuotas:</strong> <input type="text" name="numcuota" align="right" onblur="javascript:cuotas();" id="numcuota" maxlength="8" size="10"/><font color="Red">&nbsp;*</font>

<strong>Descripción del prestamo:</strong> <input type="text" name="descrip" align="right" id="descrip" maxlength="300" size="50"/>

<strong>Descuenta en Diciembre?:</strong> 
<select name="diciembre" id="diciembre">
<option value="1">No</option>
<option value="0">Si</option>

</select>

<strong>Frecuencia de deducción:</strong> &nbsp;&nbsp;Semanal <input type="radio" name="frededu" id="frededu" value="1"/>
&nbsp;&nbsp;&nbsp;&nbsp;Quincenal <input type="radio" name="frededu" id="frededu" value="2" checked="true"/>
&nbsp;&nbsp;&nbsp;&nbsp;Mensual <input type="radio" name="frededu" id="frededu" value="3"/>

<strong>Fecha de Aprobación:</strong> 

<input name="fechaap" type="text" id="fechaap" size="8" value="<?php// if ($registro_id!=0){ echo $periodo_ini; }  ?>" maxlength="60">

<strong>Fecha de vencimiento 1era cuota:</strong>

<input name="fecha1" type="text" id="fecha1" size="8" value="<?php// if ($registro_id!=0){ echo $periodo_ini; }  ?>" maxlength="60">

<strong>Monto de cuotas:</strong> &nbsp;&nbsp;&nbsp;<input type="text" name="mcuota" id="mcuota" readonly="true" size="10" style="color : #000099; font-size : 14pt; text-align : right;" value="0.0" align="left">
-->
<?php// btn('ok','generar_cuotas();',2,'Generar cuotas') ?>

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
<td width="2%"><?php// btn('ok','document.form1.submit();',2,'Enviar') ?></td>
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
</form>
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
