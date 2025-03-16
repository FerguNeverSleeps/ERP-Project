<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db              = new Database($_SESSION['bd']);
	$nomposicion_id  = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	$sql             = "SELECT a.cod_car,a.des_car
						FROM nomposicion b, nomcargos a
						WHERE b.cargo_id =a.cod_cargo AND b.nomposicion_id='".$nomposicion_id."'";
	$sql1            = "SELECT cod_car,des_car
						FROM nomcargos";
	
	$res             = $db->query($sql);
	$res1            = $db->query($sql1);
	$fila = $res->fetch_assoc();
	echo '<div class="col-md-9"><select id="codcargo" name="codcargo" class="form-control"><option value="'.$fila['cod_car'].'">'.$fila['des_car'].'</option>';

	while($fila1 = $res1->fetch_assoc())
	{
		echo '<option value="'.$fila1['cod_car'].'">'.$fila1['des_car'].'</option>'; 
	}
		
    echo '</select>';


	//echo $fila["des_car"]


?>