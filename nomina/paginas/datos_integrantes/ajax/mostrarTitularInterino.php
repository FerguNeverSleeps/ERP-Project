<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

$db           = new Database($_SESSION['bd']);

$ficha_actual = (isset($_GET['ficha'])) ? $_GET['ficha'] : '';

$sql = "SELECT * FROM nompersonal WHERE ficha='".$ficha_actual."'";
	$res = $db->query($sql);

$integrante = $res->fetch_object();
echo '

		<div class="form-group">';
				$titular = 'checked';
				$interino = '';

				if(isset($integrante))
				{
					$titular = ($integrante->tipo_empleado=='Titular') ? 'checked' : '';
					$interino = ($integrante->tipo_empleado=='Interino')  ? 'checked' : '';
				} 
			
		echo '	<label class="control-label col-md-3">Titular/Interino</label>
			<div class="col-md-9">
				<div class="radio-list">
					<label class="radio-inline">
					<input type="radio" name="tipo_empleado" id="titular" value="Titular" '. $titular.'> Titular</label>
					<label class="radio-inline">
					<input type="radio" name="tipo_empleado" id="interino" value="Interino" '.$interino.'> Interino</label>
				</div>
			</div>
		</div>';