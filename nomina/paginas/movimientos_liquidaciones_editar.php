<?php
session_start();
ob_start();
/*echo " Nomina";
echo $_REQUEST['nomina']."<br>";
echo "Concepto";
echo $_REQUEST['concepto']."<br>";
echo "Ficha";
echo$_REQUEST['ficha']."<br>";*/

include("../header4.php");
include("../lib/common.php");
include("func_bd.php");
$conexion = conexion();

//echo $conexion;

$url="nom_movimientos_nomina";
$modulo="Conceptos Planilla de Pago";
$tabla="nom_movimientos_nomina";
$titulos=array("Codigo","Descripcion","Tipo de pago");
$indices=array("1","2","3");
$tipo=$_GET['ficha'];
$concepto=$_GET['concepto'];
$accion=$_GET['accion'];
//echo $tipo."<- <br>";
//echo $url."<BR>";
if(isset($_POST['accionMod'])) 
{

	//echo "modifica";
	$consulta="update ".$tabla." set 
	 valor='".$_REQUEST['valor']."' where codcon='".$_REQUEST['concepto']."' 
	AND codnom='".$_REQUEST['nomina']."' AND ficha='".$_REQUEST['ficha']."'";
        
	$consulta2="update ".$tabla." set 
	 monto='".$_REQUEST['monto']."' where codcon='".$_REQUEST['concepto']."' 
	AND codnom='".$_REQUEST['nomina']."' AND ficha='".$_REQUEST['ficha']."'";
	//echo $consulta."<br>";

	$resultado=query($consulta,$conexion) or die("no se actualizo el Tipo de personal");
	$resultado2=query($consulta2,$conexion) or die("no se actualizo el Tipo de personal");
	//echo $resultado;
	//cerrar_conexion($conexion);	
	$url='location:movimientos_nomina_liquidaciones.php?codigo_nomina='.$_REQUEST['nomina'].'&codt='.$_REQUEST['codt'].'&ficha='.$_REQUEST['ficha'].'&pagina='.$_REQUEST['pagina'].'&tipob=exacta';
	header($url);

	/*?>
	<SCRIPT language="JavaScript" type="text/javascript">
	 parent.cont.location.href='movimientos_nomina_pago.php?codigo_nomina=<? echo $_REQUEST['nomina']?>&codt=<? echo $_REQUEST['codt'] ?>'
	 </SCRIPT>
	 <?*/

	
}
if($accion=='modificar')
{
	$consulta="SELECT * FROM nom_movimientos_nomina	WHERE codcon='".$_GET['concepto']."' 
	AND ficha='".$_GET['ficha']."' AND codnom='".$_GET['nomina']."'";
	//echo $consulta;
	$resultado_movimientos=query($consulta,$conexion);
/*
	while($lista=mysqli_fetch_assoc($resultado_movimientos))
	{
		echo $lista["cedula"]."mes <br>";
	}
*/



?>
<html class="fondo">
<head>
<title></title>
</SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript">

function cerrar(){
	window.history.back();
	// Ésta página de editar se utiliza también para movimientos_nomina_vacaciones.php
	// por lo cual redirecciona incorrectamente si se coloca únicamente la url de movimientos_nomina_pago.php
	// document.location.href="movimientos_nomina_pago.php?codigo_nomina="+document.sampleform.nomina.value+"&codt="+document.sampleform.codt.value+"&pagina="+document.sampleform.pagina.value+"&tipob=exacta"+"&ficha="+document.sampleform.ficha.value;
}
</SCRIPT>
</head>
<body>
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">Editar Registro de <?echo $modulo?></div>
						</div>
						<div class="portlet-body">
							<FORM name="sampleform" method="POST" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<INPUT type="hidden" name="nomina" size="100" value="<?echo $_REQUEST['nomina']?>">
							<INPUT type="hidden" name="pagina" size="100" value="<?echo $_REQUEST['pagina']?>">
							<INPUT type="hidden" name="concepto" size="100" value="<?echo $_REQUEST['concepto']?>">
							<INPUT type="hidden" name="ficha" value="<?echo $_REQUEST['ficha']?>">
							<INPUT type="hidden" name="codt" value="<?echo $_REQUEST['codt']?>">
							<INPUT type="hidden" name="accionMod" value="<?if($accion=='modificar') echo '1'; else echo "0";?>">
							<?
							while ($fila = mysqli_fetch_array($resultado_movimientos))
							{
							?>
								<div class="row">
									<div class="col-md-2">Concepto:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control" disabled="disabled" size="80" value="<? echo $fila[codcon];?>"></div>
								</div>
								<div class="row">
									<div class="col-md-2">Descripción:</div>
									<div class="col-md-9"><INPUT type="text" name='descrip' class="form-control" disabled="disabled" size="80" value="<? echo $fila[descrip];?>"></div>
								</div>
								<div class="row">
									<div class="col-md-2">Referencia:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control"  size="80" value="<? echo $fila[valor];?>"></div>
								</div>
								<div class="row">
									<div class="col-md-2">Unidad:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control" disabled="disabled" size="80" value="<? echo $fila[unidad];?>"></div>
								</div>
								
								<?
								if($fila[tipcon]=="A")
								{?>
									<div class="row">
										<div class="col-md-2">Asignaciones:</div>
										<div class="col-md-9"><INPUT type="text" name='monto' class="form-control" size="80" value="<? echo $fila[monto];?>"></div>
									</div>									
								<?
								}
								else{
								?>
								<div class="row">
										<div class="col-md-2">Deducciones:</div>
										<div class="col-md-9"><td colspan="3"><INPUT type="text" name='monto' class="form-control" size="80" value="<? echo $fila[monto];?>"></div>
									</div>	
									
								<? 
								}
							 }
							 ?>
							 <div class="row">&nbsp;</div>
							 <div class="row">
							 	<div class="col-md-12 text-center">
							 		<INPUT type="submit" class="btn btn-primary" name="aceptar" value="Aceptar">&nbsp;
									<INPUT type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript:cerrar()">
							 	</div>
							 </div>
							</FORM>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</body>

</html>
<?
include("../footer4.php");

cerrar_conexion($conexion);
}
?>