<?php

	$ruta    = dirname(dirname(dirname(dirname(__FILE__))));
	$ruta    = str_replace('\\', '/', $ruta);
	require_once $ruta.'/generalp.config.inc.php';
	$db_user = DB_USUARIO;
	$db_pass = DB_CLAVE;
	$db_name = $_SESSION['bd'];
	$db_host = DB_HOST;
	
	$cedula  = isset($_GET["cedula"]) ? $_GET["cedula"] : '' ;
	$ficha   = isset($_GET["ficha"]) ? $_GET["ficha"] : '' ;

    //echo $_GET['cedula']," ",$_GET["ficha"];

    $conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
        die( 'Could not open connection to server' );
    $id  = $_POST['id'];
    $sql = "SELECT periodo, ddisfrute, dias_solic_ppagar, monto_vac, ficha, ceduda FROM nom_progvacaciones WHERE ceduda='{$cedula}' AND ficha='{$ficha}' AND estado = 'Pendiente' AND fechavac != '0000-00-00' AND fechareivac != '0000-00-00' and marca = 1";
    $res = $conexion->query($sql) or die(mysqli_error($conexion));
    $tabla .= "<table class='table table-responsive'>
    <tr><th>Periodo</th><th>Dias</th><th>Saldo Dias</th><th>Accion</th></tr>";
    while($filas = $res->fetch_assoc())
    {
    	$tabla .= "<tr class='text-center' periodo='".$filas['periodo']."' id='vac_nom'>";
    	$tabla .= "<td>".$filas['periodo']."</td>";
    	$tabla .= "<td>".$filas['ddisfrute']."</td>";
    	$tabla .= "<td>".$filas['dias_solic_ppagar']."</td>";
    	$tabla .= "<td><input type='checkbox' name='sele' value='{$filas['periodo']}'></td>";
    	$tabla .= "</tr>";

    }
    $tabla .= "</table>";

echo $tabla;

?>