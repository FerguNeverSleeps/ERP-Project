<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	id,descripcion FROM instituciones ORDER BY descripcion ASC";
	$res = $db->query($sql);


	echo '<div class="col-md-8">Institución<select id="institucion_estructura" name="institucion_estructura" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['id'].'">'.$fila['descripcion'].'</option>'; 
	}	
    echo '</select></div>';

?>