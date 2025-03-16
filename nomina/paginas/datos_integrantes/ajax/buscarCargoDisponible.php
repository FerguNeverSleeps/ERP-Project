<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$cod_cargo = isset($_REQUEST['cod_cargo']) ? $_REQUEST['cod_cargo']:NULL;
	/*$sql            = "SELECT nomposicion_id
						FROM nompersonal b 
						WHERE nomposicion_id='".$nomposicion_id."'";´*/
$sql            = "SELECT b.cod_cargo, b.sueldo_propuesto, b.sueldo_2,b.sueldo_3,b.sueldo4,b.mes_1,b.mes_2,b.mes_3,b.mes_4
					FROM nomcargos b, nompersonal a
					WHERE a.codcargo=b.cod_cargo AND b.cod_cargo='".$cod_cargo."'";

	$res            = $db->query($sql);

    $fila = $res->fetch_assoc();
	//echo $fila['nomposicion_id'];

/*
	if($fila['cod_cargo'])
	{
		//echo'<div class="alert alert-success" role="alert">Posición Disponible</div>'; 
		echo '
<div class="control-label form-group">
  <div class="col-md-3">Partida:</div>
  <div class="col-md-8"> '.$fila["partida"].'</div>
  	</div></br>
<div class="control-label form-group">
  <div class="col-md-3">&nbsp;</div>
  <div class="col-md-8">
    <table class="table table-striped table-bordered table-hover">
    <thead><tr><th>Sueldo 1</th><th>Sueldo 2</th><th>Sueldo 3</th><th>Sueldo 4</th></tr></thead>
    <tbody>
    <tr>
    	<td>'.$fila["sueldo_propuesto"].'</td>
    	<td>'.$fila["sueldo_2"].'</td>
    	<td>'.$fila["sueldo_3"].'</td>
    	<td>'.$fila["sueldo4"].'</td>
    </tr>
    <tr><td>Antigüedad 011</td></tr>
	<tr>
    	<td>Zona Apartada (012)</td></tr>
	<tr>
    	<td>Jefaturas (013)</td></tr>
	<tr>
    	<td>Especialidad o Exc. (019)</td></tr>
	<tr>
    	<td>Otros (080)</td></tr>
	<tr>
    	<td>Gastos de Representacion (030)</td>
    </tr>
    <tr><td><input type="text" name="antiguedad" id="antiguedad" class="form-control"></td></tr>
	<tr>
    	<td><input type="text" name="zona_apartada" id="zona_apartada" class="form-control"></td></tr>
	<tr>
    	<td><input type="text" name="jefaturas" id="jefaturas" class="form-control"></td></tr>
	<tr>
    	<td><input type="text" name="jefaturas" id="jefaturas" class="form-control"></td></tr>
	<tr>
    	<td><input type="text" name="otros" id="otros" class="form-control"></td></tr>
	<tr>
    	<td><input type="text" name="gastos_representacion" id="gastos_representacion" class="form-control"></td>
    </tr>
    </tbody></table>
  </div>
</div>';

	}*/


?>