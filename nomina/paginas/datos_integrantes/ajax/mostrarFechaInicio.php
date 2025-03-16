<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$sql            = "SELECT fecing FROM nompersonal  WHERE personal_id='".$ficha."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();


$fecha=date('d-m-Y', $fila['fecing']);
		echo '
<div class="form-group">
	<label class="control-label col-md-3">Fecha Inicio</label>
	<div class="col-md-4">
		<div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd-mm-yyyy" data-date-viewmode="years">
			<input type="text" class="form-control form-control-inline input-medium" name="fecha_inicio" id="fecha_inicio" value="'.$fecha.'">
			<span class="input-group-btn">
				<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
			</span>
		</div>
	</div>
</div>				';
		


?>