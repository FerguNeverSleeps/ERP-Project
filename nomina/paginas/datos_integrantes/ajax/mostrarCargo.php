<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;

	$sql = "SELECT 	IdTituloInstitucional,Descripcion FROM tituloinstitucional ORDER BY Descripcion ASC";
	$res = $db->query($sql);
	$sql2   = "SELECT b.IdTituloInstitucional,b.Descripcion
						FROM posicionempleado a, tituloinstitucional b
						WHERE b.IdTituloInstitucional=a.IdTituloInstitucional AND a.Posicion='".$nomposicion_id."'";
	$res2   = $db->query($sql2);
    $fila2 = $res2->fetch_assoc();

	echo '<div class="form-group"><label class="col-md-3">Cargo</label><div class="com-md-8"><select id="cargo_estructura" name="cargo_estructura" class="form-control form-control-inline input-medium">';

	echo '<option value="'.$fila2['IdTituloInstitucional'].'">'.$fila2['Descripcion'].'</option>'; 

	while($fila = $res->fetch_assoc())
	{
		echo '<option value="'.$fila['IdTituloInstitucional'].'">'.$fila['Descripcion'].'</option>'; 
	}	
    echo '</select></div></div>';

?>