<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	
	$sql            = "SELECT ctacontab
						FROM nompersonal 
						WHERE personal_id='".$ficha."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();


	echo '<div class="form-group">
			<label class="col-md-3">Cuenta Contable</label>
			<div class="com-md-8"><input type="text" name="cuentacontable_estructura" id="cuentacontable_estructura" class="form-control form-control-inline input-medium" value="'. $fila['ctacontab'].'"></div>
		</div>';


?>