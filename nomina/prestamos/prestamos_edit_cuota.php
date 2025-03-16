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

$host_ip = $_SERVER['REMOTE_ADDR'];
$modulo = "Prestamos - Editar Cuota";
$url = "prestamos_edit_cuota.php";
//echo $conexion;

//$url="nom_movimientos_nomina";
//$modulo="Conceptos Planilla de Pago";
$tabla="nom_movimientos_nomina";
$titulos=array("Codigo","Descripcion","Tipo de pago");
$indices=array("1","2","3");

//echo $_REQUEST['nomina'];echo "<br>";
//echo $_REQUEST['pagina'];echo "<br>";
//echo $_REQUEST['concepto'];echo "<br>";
//echo $_REQUEST['ficha'];echo "<br>";
//echo $_REQUEST['codt'];echo "<br>";
                                                            
$tipo=$_GET['ficha'];
$concepto=$_GET['concepto'];
$accion=$_GET['accion'];
//echo $tipo."<- <br>";
//echo $url."<BR>";
if(isset($_POST['accionMod'])) 
{

	//echo "modifica";
	$consulta="UPDATE nomprestamos_detalles 
                   SET 
                   montocuo='".$_REQUEST['monto']."',"
                . "fechaven='".fecha_sql($_REQUEST['fecha_vencimiento'])."' "
                . "WHERE  numcuo='".$_REQUEST['cuota']."' 
	           AND numpre='".$_REQUEST['prestamo']."' AND ficha='".$_REQUEST['ficha']."'";
//	echo $consulta."<br>";

	$resultado=query($consulta,$conexion) or die("no se actualizo el Tipo de personal");
	//echo $resultado;
//	cerrar_conexion($conexion);	
	//$url='location:movimientos_nomina_pago.php?codigo_nomina='.$_REQUEST['nomina'].'&codt='.$_REQUEST['codt'].'&ficha='.$_REQUEST['ficha'].'&pagina='.$_REQUEST['pagina'].'&tipob=exacta';
//	header($url);
        echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	
        
        //window.opener.document.forms[0].numpre.value='$_REQUEST[prestamo]';
        window.opener.location.reload();
        window.close();       
	</SCRIPT>";
	
}
if($accion=='modificar')
{
	$consulta_personal="SELECT p.apenom, p.cedula,p.ficha "
                . "FROM nompersonal as p "
                . "WHERE p.ficha='".$_GET['ficha']."'";
	//echo $consulta;
	$resultado_personal=query($consulta_personal,$conexion);
        $fila_personal = mysqli_fetch_array($resultado_personal);
        $nombre=       $fila_personal[apenom];
        $cedula=       $fila_personal[cedula];
        
        $consulta_prestamo="SELECT p.* "
                . "FROM nomprestamos_cabecera as p "
                . "WHERE p.ficha='".$_GET['ficha']."' AND p.numpre='".$_GET['prestamo']."'";
	//echo $consulta;
	$resultado_prestamo=query($consulta_prestamo,$conexion);
        $fila_prestamo = mysqli_fetch_array($resultado_prestamo);
        $monto=       $fila_prestamo[monto];
        $prestamo=    $fila_prestamo[numpre];
        
        $consulta_cuota="SELECT pd.*,DATE_FORMAT(pd.fechaven, '%d/%m/%Y') as fechaven "
                . "FROM nomprestamos_detalles as pd	"
                . "WHERE pd.ficha='".$_GET['ficha']."' AND pd.numpre='".$_GET['prestamo']."' AND pd.numcuo='".$_GET['cuota']."'";
//	echo $consulta_cuota;
	$resultado_cuota=query($consulta_cuota,$conexion);
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
                                                    <div class="caption">Editar Cuota / Colaborador: <?php echo $fila_personal['cedula']." - ".utf8_encode($fila_personal['apenom']); ?> / Prestamo Nº: <?php echo $prestamo." - Monto: ".$monto; ?></div>
						</div>
						<div class="portlet-body">
							<FORM name="sampleform" method="POST" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							
							<INPUT type="hidden" name="prestamo" size="100" value="<?echo $prestamo?>">
							<INPUT type="hidden" name="ficha" value="<?echo $fila_personal['ficha']?>">
                                                        <INPUT type="hidden" name="numcuo" value="<?echo $fila['numcuo']?>">
							<INPUT type="hidden" name="accionMod" value="<?if($accion=='modificar') echo '1'; else echo "0";?>">
							<?
							while ($fila = mysqli_fetch_array($resultado_cuota))
							{
							?>
								<INPUT type="hidden" name="monto_anterior" size="100" value="<?echo $fila[montocuo]?>">
                                                                <INPUT type="hidden" name="fecha_anterior" size="100" value="<?echo $fila[fechaven]?>">
                                                                <div class="row">
									<div class="col-md-2">Cuota Nº:</div>
									<div class="col-md-9">
                                                                            <INPUT type="text" name='cuota' id='cuota' class="form-control" readonly size="80" value="<? echo $fila[numcuo];?>">
                                                                        </div>
								</div>
                                                                <br>
								
								<div class="row">
									<div class="col-md-2">Fecha Vencimiento:</div>
									<div class="col-md-9">
                                                                            
                                                                            <div class="input-group">
                                                                                <input name="fecha_vencimiento" data-provide="datepicker" data-date-format="dd/mm/yyyy" id="fecha_vencimiento" value="<?php echo $fila[fechaven];   ?>" type="text" class="form-control" maxlenght="60" size="70" placeholder="dd/mm/aaaa">
                                                                                <div class="input-group-addon">
                                                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
								</div>
                                                                <br>
								<div class="row">
									<div class="col-md-2">Monto:</div>
									<div class="col-md-9">
                                                                            <INPUT type="text" name='monto' id='monto' class="form-control"  size="80" value="<? echo $fila[montocuo];?>">
                                                                        </div>
								</div>
								<br>
                                                                <div class="row">
									<div class="col-md-2">Saldo Final:</div>
									<div class="col-md-9">
                                                                            <INPUT type="text" name='valor' class="form-control" disabled="disabled" size="80" value="<? echo $fila[salfinal];?>">
                                                                        </div>
								</div>
                                                                						
                                                         <?       
							 }
							 ?>
							 <div class="row">&nbsp;</div>
							 <div class="row">
							 	<div class="col-md-12 text-center">
							 		<INPUT type="submit" class="btn btn-primary" name="aceptar" value="Aceptar">&nbsp;
<!--									<INPUT type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript:cerrar()">-->
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