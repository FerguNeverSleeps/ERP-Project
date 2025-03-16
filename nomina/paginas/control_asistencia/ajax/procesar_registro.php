<?php
require_once "../config/db.php";
require_once "../utils/funciones_procesar.php";
//------------------------------------------------------------
$ficha    = $_POST['ficha'];
$fec_ent  = $_POST['fec_ent'];
$fec_sal  = $_POST['fec_sal'];
$entrada  = $_POST['entrada'];
$salida   = $_POST['salida'];
$turno_id = $_POST['turno_id'];
$update   = $_POST['update'];
//------------------------------------------------------------
//se cargan todos los turnos registrados
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$res = $conexion->query("SELECT * FROM nomturnos WHERE turno_id = '$turno_id'") or die(mysqli_error($conexion));
$turno = mysqli_fetch_array($res);
//------------------------------------------------------------
$tardanza = tardanza($entrada, $turno);
$extra    = horas_extras($entrada, $salida, $turno);
$tiempo   = diff_fechora($fec_ent." ".$entrada, $fec_sal." ".$salida);
//------------------------------------------------------------
$datos = array(
    'ficha'      => $ficha,
    'fec_ent'    => $fec_ent,
    'fec_sal'    => $fec_sal,
    'entrada'    => $entrada,
    'salida'     => $salida,
    'tiempo'     => $tiempo,
    'tardanza'   => $tardanza,
    'h_extra'    => $extra[0],
    'recargo_25' => $extra[1],
    'recargo_50' => $extra[2],
    'jornada'    => $extra[3],
    'turno_id'   => $turno['turno_id']
);
if ($update == 1) {
$conexion->query("SET AUTOCOMMIT=0");
$conexion->query("START TRANSACTION");

    $sql="UPDATE caa_resumen SET ficha = '".$datos['ficha']."',  fecha = '".$datos['fec_ent']."',  fec_sal = '".$datos['fec_sal']."', entrada = '".$datos['entrada']."',  salida = '".$datos['salida']."',  tiempo = '".$datos['tiempo']."',  tardanza = '".$datos['tardanza']."',  h_extra = '".$datos['h_extra']."',  recargo_25 = '".$datos['recargo_25']."', recargo_50 = '".$datos['recargo_50']."', jornada = '".$datos['jornada']."',  turno_id = '".$datos['turno_id']."' WHERE ficha = '$ficha' AND fecha = '$fec_ent'";
    $a1 = $conexion->query($sql1);

    if ( $a1 ) {
        $conexion->query("COMMIT");
    } else {        
        $conexion->query("ROLLBACK");
    }
}
echo json_encode($datos);
?>