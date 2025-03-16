<?php
require_once "../../config/db.php";
require_once "../../utils/funciones_procesar.php";
//------------------------------------------------------------
$fec_ent = date ( 'Y-m-j' , strtotime ( $_POST['fec_ent'] ) );
$fec_sal = date ( 'Y-m-j' , strtotime ( $_POST['fec_sal'] ) );
$entrada = $_POST['entrada'];
$salida  = $_POST['salida'];
//------------------------------------------------------------
if ((strtotime($entrada)>strtotime($salida))&&(strtotime($fec_ent)==strtotime($fec_sal))&&(strtotime($salida)!=strtotime("00:00:00")))
{
	$datos = array(
		'dato' => 1,
		'msj'  => 'La hora de entrada no debe ser mayor a la de salida, Verifique por favor..!!'
	 );
}elseif (strtotime($entrada) == strtotime("00:00:00")){
	$datos = array(
		'dato' => 2,
		'msj'  => 'El valor de entrada es igual 00:00:00, Verifique por favor..!!'
	 );
}elseif (strtotime($salida) == strtotime("00:00:00")){
	$datos = array(
		'dato' => 3,
		'msj'  => 'El valor de salida es igual 00:00:00, Verifique por favor..!!'
	 );
}else{
	$datos = array(
		'dato' => 0
	 );	
}
//------------------------------------------------------------
echo json_encode($datos);
?>