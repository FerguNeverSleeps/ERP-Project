<?php
session_start();
ob_start();
//$nivel1      = ($_GET["nivel1"]!='') ? $_GET["nivel1"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);

$query       = "SELECT ficha, cedula, apenom
FROM nompersonal WHERE tipnom = '{$_SESSION[codigo_nomina]}'";

$result    = $db->query($query);
$cant =  $result->num_rows;
if($cant > 0)
{
	echo "<div class='form-group'>
	<select name='ficha' id='ficha'  class='form-control'>";
	while ($datos    = $result->fetch_assoc()) 
	{
		echo "<option value='$datos[ficha]'>$datos[cedula] - $datos[apenom]</option>";
	}
	echo "</select>
	</div>";
}
else
{
	echo "<div class='form-group'>
        <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center'>
		<select name='ficha' id='ficha'  class='form-control'>";
	echo "	</select></div></div>";
}

?>