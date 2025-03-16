<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	$sql            = "SELECT sueldo_propuesto_80
						FROM nomposicion b 
						WHERE nomposicion_id='".$nomposicion_id."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();

echo $fila['sueldo_propuesto_80'];
		/*echo '<div class="form-group">
				<label class="col-md-3">Salario</label>
				<div class="com-md-8"><input type="text" name="salario_estructura" id="salario_estructura" class="form-control form-control-inline input-medium" value="'. $fila['sobresueldo_antiguedad_1'].'"></div>
			</div>';*/


?>