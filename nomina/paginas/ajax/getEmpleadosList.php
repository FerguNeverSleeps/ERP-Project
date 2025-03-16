<?php
session_start();  

require_once('../../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
$query       = "SELECT personal_id, cedula, apenom
FROM nompersonal WHERE tipnom = '{$_SESSION[codigo_nomina]}'";
$filas       = $db->query($query)->fetch_assoc();
$result = $db->query($query);

$cant =  $result->num_rows;
if($cant > 0)
{
echo "
		<select name='id_empleado' id='id_empleado'  class='form-control' style='width: 700px'>";
	echo "	<option value='Todos'>SELECCIONE UN EMPLEADO</option>";
		while ($datos    = $result->fetch_assoc()) 
		{

			echo "	<option value='".$datos["personal_id"]."'>".$datos["cedula"]."-".$datos["apenom"]."</option>";

		}
echo "	</select>
";
}else
{
echo "
	<div class='col-sm-12'>
		<select name='id_empleado' id='id_empleado'  class='form-control'>";

	echo "	<option value='Todos'>SELECCIONE UN EMPLEADO***</option>";

echo "	</select></div>";
}

?>