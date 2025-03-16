<?php

require_once '../../generalp.config.inc.php';
session_start();
ob_start();

include("../lib/common.php") ;
$codigo_banco = @$_GET['codigo'];
$conexion = conexion();

$sql = "DELETE FROM `nombancos` WHERE `cod_ban` = '$codigo_banco'";
$res = query($sql, $conexion);
if($res)
{
	?>
	<script type="text/javascript">
		alert("Se Ha Eliminado Satisfactoriamente");
		window.location.href = "bancos.php?pagina=1";
	</script>
	<?php
}
else
{
	?>
	<script type="text/javascript">
		alert("Hubo Un Error Al Eliminar");
		window.location.href = "bancos.php?pagina=1";
	</script>
	<?php
}