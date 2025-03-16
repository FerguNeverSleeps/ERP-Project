<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	login,name FROM sec_users ORDER BY name ASC";
	$res = $db->query($sql);


	echo '<label class="col-md-3">Institución</label><div class="com-md-3"><select id="usuario_estructura" name="usuario_estructura" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['login'].'">'.$fila['name'].'</option>'; 
	}	
    echo '</select></div>';

?>