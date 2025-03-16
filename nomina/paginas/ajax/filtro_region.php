<?php 
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
    $ruta = str_replace('\\', '/', $ruta);
    require_once $ruta.'/generalp.config.inc.php';
    $db_user  = DB_USUARIO;
    $db_pass  = DB_CLAVE;
    $db_name  = $_SESSION['bd'];
    $db_host  = DB_HOST;
    $conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
        die( 'Could not open connection to server' );
    $id  = $_POST['id'];
    $sql = "SELECT codorg,descrip FROM nomnivel2 WHERE gerencia='$id'";
    $res = $conexion->query($sql) or die(mysqli_error($conexion));
    $i=0;
	while ( $dpto = mysqli_fetch_array($res) )
	{
		$d[$i] = array( "codorg" => $dpto['codorg'], "descrip" => utf8_encode($dpto['descrip']) );
		$i++;
	}
    $json['region']    = $id;
    $json['respuesta'] = $d;
    $json['consulta']  = $sql;
	echo json_encode( $json );
?>