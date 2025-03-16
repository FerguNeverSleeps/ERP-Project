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
$modulo = "Movimientos Planilla - Editar";
$url = "movimientos_nomina_pago_editar.php";
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
	$consulta="update ".$tabla." set 
	 valor='".$_REQUEST['valor']."' where id='".$_REQUEST['concepto']."' 
	AND codnom='".$_REQUEST['nomina']."' AND ficha='".$_REQUEST['ficha']."'";
        
	$consulta2="update ".$tabla." set 
	 monto='".$_REQUEST['monto']."' where id='".$_REQUEST['concepto']."' 
	AND codnom='".$_REQUEST['nomina']."' AND ficha='".$_REQUEST['ficha']."'";
	//echo $consulta."<br>";

	$resultado=query($consulta,$conexion) or die("no se actualizo el Tipo de personal");
	$resultado2=query($consulta2,$conexion) or die("no se actualizo el Tipo de personal");
	//echo $resultado;
	//cerrar_conexion($conexion);	
//	$url='location:movimientos_nomina_pago.php?codigo_nomina='.$_REQUEST['nomina'].'&codt='.$_REQUEST['codt'].'&ficha='.$_REQUEST['ficha'].'&pagina='.$_REQUEST['pagina'].'&tipob=exacta';
//	header($url);

	/*?>
	<SCRIPT language="JavaScript" type="text/javascript">
	 parent.cont.location.href='movimientos_nomina_pago.php?codigo_nomina=<? echo $_REQUEST['nomina']?>&codt=<? echo $_REQUEST['codt'] ?>'
	 </SCRIPT>
	 <?*/
        
        $nomina=$_REQUEST['nomina'];
        $tipo_nomina=$_REQUEST['codt'];
        $concepto=$_REQUEST['concepto'];
        $ficha=$_REQUEST['ficha'];
        $cedula=$_REQUEST['cedula'];
        $nombre=$_REQUEST['nombre'];
        $monto_anterior=$_REQUEST['monto_anterior'];
        $monto=$_REQUEST['monto'];
        $accion=$_REQUEST['accion'];
        $descripcion = "Editar Movimiento - Colaborador ".$_POST[ficha]." - Nombre ".$nombre." - Cedula: ".$cedula.""
                     . " - Concepto: ".$concepto." - Monto Anterior: ".$monto_anterior." - Monto Nuevo: ".$monto;
        
        $sql_log = "INSERT INTO log_transacciones 
                    (cod_log, 
                    descripcion, 
                    fecha_hora, 
                    modulo, 
                    url, 
                    accion, 
                    valor, 
                    usuario,
                    host) 
                    VALUES 
                    (NULL, 
                    '".$descripcion."', "
                    . "now(), "
                . "'".$modulo."', "
                . "'".$url."', "
                . "'".$accion."',"
                . "'".$cod."',"
                . "'".$_SESSION['usuario'] ."',"
                . "'".$host_ip."')";
        
        $res_log = query($sql_log,$conexion) or die("no se actualizo el Tipo de personal");
        echo "<center style='padding-top: 50px;'>Recalculando... Por favor espere...</center>";
        echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	//window.opener.document.forms[0].buscar.value=$_POST[ficha]
	//window.opener.document.forms[0].submit();
	//window.close();

        if(confirm('Usted debe recalcular la planilla. ¿Desea hacerlo en este momento?')){

			var codnom = '".$_SESSION['codigo_nomina']."';
	        var nomina = '$nomina';
	        var ficha  = '$ficha';        


	        $.get( 'recalcular_movimientos_nomina.php', { codnom: codnom, nomina:nomina, ficha:ficha }, function(file) { 
	            window.opener.location.reload();
                    window.close();            
	        }); 
        }
        else{
        	window.opener.location.reload();
		window.close();    
        }
	</SCRIPT>";

	
}
if($accion=='modificar')
{
	$consulta_personal="SELECT p.apenom, p.cedula "
                . "FROM nompersonal as p "
                . "WHERE p.ficha='".$_GET['ficha']."'";
	//echo $consulta;
	$resultado_personal=query($consulta_personal,$conexion);
        $fila_personal = mysqli_fetch_array($resultado_personal);
        $nombre=       $fila_personal[apenom];
        $cedula=       $fila_personal[cedula];
        $consulta="SELECT nm.* "
                . "FROM nom_movimientos_nomina as nm	"
                . "WHERE id='".$_GET['concepto']."' 
                AND nm.ficha='".$_GET['ficha']."' AND codnom='".$_GET['nomina']."'";
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
                                                    <div class="caption">Editar Concepto / <?php echo $fila_personal['cedula']." - ".utf8_encode($fila_personal['apenom']); ?></div>
						</div>
						<div class="portlet-body">
							<FORM name="sampleform" method="POST" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>">
							<INPUT type="hidden" name="nomina" size="100" value="<?echo $_REQUEST['nomina']?>">
							<INPUT type="hidden" name="pagina" size="100" value="<?echo $_REQUEST['pagina']?>">
							<INPUT type="hidden" name="concepto" size="100" value="<?echo $_REQUEST['concepto']?>">
							<INPUT type="hidden" name="ficha" value="<?echo $_REQUEST['ficha']?>">
                                                        <INPUT type="hidden" name="cedula" value="<?echo $fila_personal['cedula']?>">
                                                        <INPUT type="hidden" name="nombre" value="<?echo utf8_encode($fila_personal['apenom'])?>">
							<INPUT type="hidden" name="codt" value="<?echo $_REQUEST['codt']?>">
							<INPUT type="hidden" name="accionMod" value="<?if($accion=='modificar') echo '1'; else echo "0";?>">
							<?
							while ($fila = mysqli_fetch_array($resultado_movimientos))
							{
							?>
								<INPUT type="hidden" name="monto_anterior" size="100" value="<?echo $fila[monto]?>">
                                                                <div class="row">
									<div class="col-md-2">Concepto:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control" disabled="disabled" size="80" value="<? echo $fila[codcon];?>"></div>
								</div>
                                                                <br>
								<div class="row">
									<div class="col-md-2">Descripción:</div>
									<div class="col-md-9"><INPUT type="text" name='descrip' class="form-control" disabled="disabled" size="80" value="<? echo $fila[descrip];?>"></div>
								</div>
                                                                <br>
								<div class="row">
									<div class="col-md-2">Referencia:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control"  size="80" value="<? echo $fila[valor];?>"></div>
								</div>
                                                                <br>
								<div class="row">
									<div class="col-md-2">Unidad:</div>
									<div class="col-md-9"><INPUT type="text" name='valor' class="form-control" disabled="disabled" size="80" value="<? echo $fila[unidad];?>"></div>
								</div>
								<br>
								<?
								if($fila[tipcon]=="A")
								{?>
									<div class="row">
										<div class="col-md-2">Asignaciones:</div>
										<div class="col-md-9"><INPUT type="text" name='monto' class="form-control" size="80" value="<? echo $fila[monto];?>"></div>
									</div>	
                                                                       <br>
								<?
								}
								else{
								?>
								<div class="row">
										<div class="col-md-2">Deducciones:</div>
										<div class="col-md-9"><td colspan="3"><INPUT type="text" name='monto' class="form-control" size="80" value="<? echo $fila[monto];?>"></div>
									</div>	
									<br>
								<? 
								}
                                                                
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