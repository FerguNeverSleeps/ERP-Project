<?php

session_start();  

if(isset($_POST['agregarObservacion']) AND $_POST['agregarObservacion'] == "yes")
{

	$observacion  = (isset($_POST['observacion'])) ? $_POST['observacion'] : '' ;
	$codnom  = (isset($_POST['codnom'])) ? $_POST['codnom'] : '' ;
	$tipnom  = (isset($_POST['tipnom'])) ? $_POST['tipnom'] : '' ;
	$ficha  = (isset($_POST['ficha'])) ? $_POST['ficha'] : '' ;
	require_once('../../../nomina/lib/database.php');
	$db          = new Database($_SESSION['bd']);
	$query       = "UPDATE nom_movimientos_nomina 
					SET observacion = '{$observacion}' 
					WHERE codcon = '114' AND ficha = {$ficha} AND codnom= {$codnom} AND tipnom={$tipnom} ;";
	$resultado       = $db->query($query);
	if($resultado)
	{
		$array = array('data' => 1, "mensaje" => "Se actualizó el observacion del usuario" );
	}
	else{

		$array = array('data' => 0, "mensaje" => "Error al actualizar el observacion del usuario" );
	}
	echo json_encode($array);exit;
}