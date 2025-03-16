<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$sql            = "SELECT sueldopro
						FROM nompersonal b 
						WHERE personal_id='".$ficha."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();


		echo '<div class="form-group">
				<label class="col-md-3">Salario</label>
				<div class="com-md-8"><input type="text" name="salario_estructura" id="salario_estructura" class="form-control form-control-inline input-medium" value="'. $fila['sueldopro'].'"></div>
			</div>';


?>