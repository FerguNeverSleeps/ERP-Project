<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;

	$sql = "SELECT 	IdDepartamento,Descripcion FROM departamento ORDER BY Descripcion ASC";
	$res = $db->query($sql);
	$sql2   = "SELECT b.IdDepartamento,b.Descripcion
						FROM posicionempleado a, departamento b
						WHERE b.IdDepartamento=a.IdDepartamento AND a.Posicion='".$nomposicion_id."'";
	$res2   = $db->query($sql2);
    $fila2 = $res2->fetch_assoc();

	echo '<div class="form-group"><label class="col-md-3">Departamento</label><div class="com-md-8"><select id="departamento_estructura" name="departamento_estructura" class="form-control form-control-inline input-medium">';
	echo '<option value="'.$fila2['IdDepartamento'].'">'.$fila2['Descripcion'].'</option>'; 

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['IdDepartamento'].'">'.$fila['Descripcion'].'</option>'; 
	}	
    echo '</select></div></div>';

?>