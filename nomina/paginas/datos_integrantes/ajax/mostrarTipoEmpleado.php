<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);

	$sql = "SELECT 	IdTipoEmpleado,Descripcion FROM tipoempleado ORDER BY Descripcion ASC";
	$res = $db->query($sql);


	echo '<div class="form-group"><label class="col-md-3">Tipo Empleado</label><div class="com-md-8"><select id="tipoempleado_estructura" name="tipoempleado_estructura" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['IdTipoEmpleado'].'">'.$fila['Descripcion'].'</option>'; 
	}	
    echo '</select></div></div>';

?>