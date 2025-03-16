<?php
session_start();
ob_start();
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     ="SELECT anio 
FROM nom_nominas_pago
WHERE status ='C'
        group by anio";
$result    = $db->query($query);
echo "
	<label class='col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-center'>Año</label>
	<div class='col-xs-10 col-sm-10 col-md-10 col-lg-10'>
		<select name='anio' id='anio'  class='form-control'>";
	echo "	<option value=''>SELECCIONE UN AÑO</option>";

		while ($nomina    = $result->fetch_assoc()) 
		{
	echo "	<option value='".$nomina["anio"]."'>".$nomina["anio"]."</option>";

		}
echo "	</select>
	</div>
";
?>