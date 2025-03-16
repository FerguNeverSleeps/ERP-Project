<?php
require_once('../lib/common.php');
require_once('../header.php');
$opcion=@$_GET['opcion'];
$banco=@$_GET['banco'];
$chequera=@$_GET['chequera'];
$cheque = @$_GET['cheque'];
$odp = @$_GET['odp'];
/*
echo "Opc: ".$opcion."<br>";
echo "banco: ".$banco."<br>";
echo "chequera: ".$chequera."<br>";
echo "cheque: ".$cheque."<br>";
echo "odp: ".$odp."<br>";
exit(0);*/
$conexion=conexion();

    
switch($opcion){

case 0:
	$inicio=@$_GET['inicio'];
	$fin=@$_GET['fin'];
	$status=@$_GET['status'];
	
	$consulta="SELECT * FROM nomcheques where chequera='".$chequera."'"; 

	$resultado=mysqli_query($conexion,$consulta);
	$num = mysqli_num_rows($resultado);
	if($num>0){
	/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Los cheques de esta chequera ya han sido generados\")
	</script>";*/
	?>
	<script type="text/javascript">
		window.location.href = "chequeras.php?pagina=1&codigo="+<?php echo $banco;?>;
	</script>
	<?php
	break;
	}
	for($i=$inicio; $i<=$fin; $i++){
		$consulta="insert into nomcheques (cheque_id,status, cheque, chequera) values ('','".$status."','".$i."','".$chequera."')";
		$resultado=mysqli_query($conexion,$consulta) or die("No se puede generar los cheques");
	}
	cerrar_conexion($conexion);
	/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Se han creado los cheques satisfactoriamente\")
	</script>";*/
	?>
	<script type="text/javascript">
		window.location.href = "chequeras.php?pagina=1&codigo="+<?php echo $banco;?>;
	</script>
	<?php
	break;

case 1:
	$valor=@$_GET['valor'];
	switch($valor){
	case 'A':
		$cadena="Activada";
		break;
	case 'C':
		$cadena="Consumida";
		break;
	case 'D':
		$cadena="Deposito";
		break;
	}
	$consulta="UPDATE nomchequera SET situacion='".$valor."' WHERE chequera_id='".$chequera."' ";
	$resultado=mysqli_query($conexion,$consulta) or die("No se puede generar los cheques");
	if($valor!="C"){
	$consulta="UPDATE nomcheques SET status='".$valor."' WHERE chequera='".$chequera."' ";
	$resultado=mysqli_query($conexion,$consulta) or die("No se puede generar los cheques");
	?>
	<script type="text/javascript">
		window.location.href = "chequeras.php?pagina=1&codigo="+<?php echo $banco;?>;
	</script>
	<?php
	}
	echo "<script language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Estatus de la chequera: ".$cadena."\")
	</script>";
	?>
	<script type="text/javascript">
		window.location.href = "chequeras.php?pagina=1&codigo="+<?php echo $banco;?>;
	</script>
	<?php
	break;

case 2:
	$valor=@$_GET['valor'];
	$numero=@$_GET['cheque'];
	$status=@$_GET['status'];

	switch($valor){
	case 'A':
		$cadena="Activada";
		break;
	case 'C':
		$cadena="Consumida";
		break;
	case 'D':
		$cadena="Deposito";
		break;
	}
	if($status=="A" || $status=="D"){
		$consulta="UPDATE nomcheques SET status='".$valor."' WHERE cheque= '".$numero."' and chequera='".$chequera."'";
		$resultado=query($consulta,$conexion);
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
	}
	else
	{
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
	}
	break;

case 3:
	$monto = $_GET['monto'];
	$fecha =$_GET['fecha'];
	
	// Verifica si el cheque ha sido dañado anteriormente
	$con = "SELECT * FROM nomcheques WHERE chequera='".$chequera."' AND cheque='".$cheque."' AND status='Da'";
	$res = query($con, $conexion);
	$registros = num_rows($res);
	if($registros > 0){
		/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
		alert(\"Este cheque ha sido dañado anteriormente\")
		</script>";*/
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
		exit(0);
	}
	else
	{
		// Cambia el estado del cheque a dañado (Da)
		$fech_anul=fecha_sql($fecha);
		$conCheque = "UPDATE nomcheques SET status='Da', fecha_anulacion='".$fech_anul."' WHERE chequera='".$chequera."' AND cheque='".$cheque."'";
		//echo $conCheque;
		$resCheque = query($conCheque, $conexion);

		/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
		alert(\"El cheque ha sido dañado exitosamente\")
		</script>";*/
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
	}
	break;
	
case 4:
	$monto = $_GET['monto'];
	$obs=$_GET['obs'];
	// Verifica si el cheque ha sido anulado anteriormente
	$con = "SELECT * FROM nomcheques WHERE chequera='".$chequera."' AND cheque='".$cheque."' AND status='An'";
	$res = query($con, $conexion);
	$registros = num_rows($res);
	if($registros > 0){
		/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
		alert(\"Este cheque ha sido anulado anteriormente\")
		</script>";*/
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
		exit(0);
	}
	else
	{
		// Cambia el estado del cheque a anulado (An), y la fecha de anulacion
		$fec_anu = fecha_sql($_GET['fecha']);
		$conCheque = "UPDATE nomcheques SET status='An', fecha_anulacion='".$fec_anu."',justificacion='$obs' WHERE chequera='".$chequera."' AND cheque='".$cheque."'";
		$resCheque = query($conCheque, $conexion);
		
		/*echo "<script language=\"JavaScript\" type=\"text/javascript\">
		alert(\"El cheque ha sido anulado exitosamente\")
		</script>";*/
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
	}
	break;

case 5:
	$valor=@$_GET['valor'];
	$numero=@$_GET['cheque'];
	$consulta="UPDATE nomcheques SET status='Ac' WHERE cheque= '".$numero."' and chequera='".$chequera."'";
	$resultado=query($consulta,$conexion);
	?>
	<script type="text/javascript">
		window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
	</script>
	<?php
	break;

case 6:
	$valor=@$_GET['valor'];
	$numero=@$_GET['cheque'];
	$status=$_GET['status'];
	if($status!="Ac" && $status!="Da"){
	$consulta="UPDATE nomcheques SET status='D' WHERE cheque= '".$numero."'and chequera='".$chequera."'";
	$resultado=query($consulta,$conexion);
	?>
	<script type="text/javascript">
		window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
	</script>
	<?php
	}
	else
	{
		?>
		<script type="text/javascript">
			window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
		</script>
		<?php
	}
	break;
	
case 7:
	$valor=@$_GET['valor'];
	$numero=@$_GET['cheque'];
	
	
	//$conMovBan = "delete from  movimientos_bancarios  WHERE codigo='".$banco."' AND numero='".$numero."'";
	//$resultados=query($conMovBan,$conexion);
	
	$consulta="UPDATE nomcheques SET status='A', beneficiario='', monto='', cedula_rif='',fecha='', fecha_anulacion='', concepto='', log_usr='' WHERE cheque= '".$numero."' and chequera='".$chequera."'";
	$resultado=query($consulta,$conexion);
	?>
	<script type="text/javascript">
		window.location.href = "cheques.php?pagina=1&banco="+<?php echo $banco;?>+"&chequera="+<?php echo $chequera;?>;
	</script>
	<?php
	break;
}
echo "<script language=\"JavaScript\" type=\"text/javascript\">
parent.cont.location.href=\"cheques.php?pagina=1&chequera=".$chequera."\"
</script>";