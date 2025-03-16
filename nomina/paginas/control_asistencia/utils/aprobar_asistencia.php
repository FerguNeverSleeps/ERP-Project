<?php
require_once "../config/db.php";
require_once "funciones_procesar.php";
//----------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//----------------------------------------------------------------
$tipo = $_GET['tipo'];
if ($tipo != 3)
{
	$fecha = $_GET['fecha'];
	$ficha = $_GET['ficha'];
}
//----------------------------------------------------------------

//----------------------------------------------------------------
switch ($tipo) {
	case 1:
		$conexion->query("SET AUTOCOMMIT=0");
		$conexion->query("START TRANSACTION");

		$a1 = $conexion->query("UPDATE caa_resumen SET estado = 1 WHERE ficha = '$ficha' AND fecha = '$fecha' AND estado = 0");
		$a2 = $conexion->query("UPDATE caa_periodos SET estado = 1 WHERE ficha = '$ficha' AND fec_ent = '$fecha' AND estado = 0");
		$a3 = $conexion->query("UPDATE caa_registros SET estado = 1 WHERE ficha = '$ficha' AND fecha = '$fecha' AND estado = 0");

		if ( $a1 and $a2 and $a3 ) {
		    $conexion->query("COMMIT");
		    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1");
		} else {        
		    $conexion->query("ROLLBACK");
		    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2");
		}
		break;
	case 2:
		$conexion->query("SET AUTOCOMMIT=0");
		$conexion->query("START TRANSACTION");

		$a1 = $conexion->query("UPDATE caa_resumen SET estado = 1 WHERE ficha = '$ficha' AND estado = 0");
		$a2 = $conexion->query("UPDATE caa_periodos SET estado = 1 WHERE ficha = '$ficha' AND estado = 0");
		$a3 = $conexion->query("UPDATE caa_registros SET estado = 1 WHERE ficha = '$ficha' AND estado = 0");

		if ($a1 and $a2 and $a3) {
		    $conexion->query("COMMIT");
		    header("location:../resumen_asistencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1");
		} else {        
		    $conexion->query("ROLLBACK");
		    header("location:../resumen_asistencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2");
		}
		break;
	case 3:
		$conexion->query("SET AUTOCOMMIT=0");
		$conexion->query("START TRANSACTION");

		$a1 = $conexion->query("UPDATE caa_resumen AS a,nompersonal As b 
			SET a.estado = 1 
			WHERE a.estado = 0 AND a.ficha = b.ficha AND b.codnivel1 = '".$_SESSION['region']."'");
		$a2 = $conexion->query("UPDATE caa_periodos AS a,nompersonal As b 
			SET a.estado = 1 
			WHERE a.estado = 0 AND a.ficha = b.ficha AND b.codnivel1 = '".$_SESSION['region']."'");
		$a3 = $conexion->query("UPDATE caa_registros AS a,nompersonal As b 
			SET a.estado = 1 
			WHERE a.estado = 0 AND a.ficha = b.ficha AND b.codnivel1 = '".$_SESSION['region']."'");

		if ($a1 and $a2 and $a3) {
		    $conexion->query("COMMIT");
		    header("location:../procesar_registros.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1");
		} else {        
		    $conexion->query("ROLLBACK");
		    header("location:../procesar_registros.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2");
		}
		break;
	default:
		# code...
		break;
}
//----------------------------------------------------------------
?>