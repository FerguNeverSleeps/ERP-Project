<?php
session_start();  
$cedula = (isset($_GET['cedula'])) ? $_GET['cedula'] : '' ;
$tipo = (isset($_GET['tipo'])) ? $_GET['tipo'] : '' ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);

if ($tipo == 4) {
	echo '<input type="number" min="1" name="periodo" id="periodo" class="form-control" placeholder="Periodo">';
}
else {
	$query     ="SELECT periodo
	FROM nom_progvacaciones
	WHERE ceduda = '{$cedula}'";
	$result    = $db->query($query);

	echo '<select  name="periodo" id="periodo" class="form-control text-right">';
	while ($filas=$result->fetch_assoc())
	{
		echo '<option value="'.$filas['periodo'].'"> '.$filas['periodo'].' </option>';
	}
	echo '</select>';
}


?>