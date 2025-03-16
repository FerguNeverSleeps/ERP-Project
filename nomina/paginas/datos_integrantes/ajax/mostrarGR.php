<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$sql            = "SELECT gastos_representacion FROM nomposicion a, nompersonal b WHERE a.nomposicion_id=b.nomposicion_id AND b.personal_id='".$ficha."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();



		echo '<div class="form-group">
			<label class="col-md-3">Gastos Representacion</label>
			<div class="com-md-8"><input type="text" name="gr_estructura" id="gr_estructura" class="form-control form-control-inline input-medium" value="'. $fila['gastos_representacion'].'"></div>
			</div>';
		


?>