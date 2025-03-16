<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	nomfuncion_id,descripcion_funcion FROM `nomfuncion` ORDER BY descripcion_funcion ASC";
	$res = $db->query($sql);


	echo '<div class="form-group"><label class="col-md-3">Función</label><div class="com-md-8"><select id="funcion_estructura" name="funcion_estructura" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['nomfuncion_id'].'">'.$fila['descripcion_funcion'].'</option>'; 
	}	
    echo '</select></div></div>';

?>