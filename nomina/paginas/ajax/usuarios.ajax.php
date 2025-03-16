<?php

session_start();  

if(isset($_POST['usuarioActivarDesactivar']) AND $_POST['usuarioActivarDesactivar'] == "yes")
{
	$coduser = (isset($_POST['coduser'])) ? $_POST['coduser'] : '' ;
	$estado  = (isset($_POST['estado'])) ? $_POST['estado'] : '' ;
	require_once('../../../nomina/lib/database.php');
	$db          = new Database($_SESSION['bd']);
	$query       = "UPDATE " . SELECTRA_CONF_PYME . ".nomusuarios SET estado = '{$estado}' WHERE coduser = '{$coduser}';";
	$resultado       = $db->query($query);
	if($resultado)
	{
		$array = array('data' => 1, "mensaje" => "Se actualizó el estado del usuario" );
	}
	else{

		$array = array('data' => 0, "mensaje" => "Error al actualizar el estado del usuario" );
	}
	echo json_encode($array);exit;
}