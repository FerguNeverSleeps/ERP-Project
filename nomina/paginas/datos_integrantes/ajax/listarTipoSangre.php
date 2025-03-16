<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	IdTipoSangre,Descripcion FROM tiposangre ORDER BY Descripcion ASC";
	$res = $db->query($sql);

	echo '<select id="tipo_sangre" name="tipo_sangre" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['IdTipoSangre'].'">'.$fila['Descripcion'].'</option>'; 
	}	
    echo '</select>';

?>