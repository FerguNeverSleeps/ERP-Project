<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	IdNivelEducativo,Descripcion FROM niveleducativo ORDER BY Descripcion ASC";
	$res = $db->query($sql);

	echo '<select id="nivel_educativo" name="nivel_educativo" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['IdNivelEducativo'].'">'.$fila['Descripcion'].'</option>'; 
	}	
    echo '</select>';

?>