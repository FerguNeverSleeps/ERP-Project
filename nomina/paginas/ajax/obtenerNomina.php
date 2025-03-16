<?php
session_start();
ob_start();
//$nivel1      = ($_GET["nivel1"]!='') ? $_GET["nivel1"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     ="SELECT codnom, descrip, codtip FROM   nom_nominas_pago "
        . "WHERE codtip='".$_SESSION['codigo_nomina']."' "
        . "ORDER BY codnom DESC";
$result    = $db->query($query);
$cant =  $result->num_rows;
if($cant > 0)
{
	echo "<div class='form-group'>
	<select name='codnom' id='codnom'  class='form-control'>";
	while ($datos    = $result->fetch_assoc()) 
	{
		echo "<option value='$datos[codnom]'>$datos[codnom] - $datos[descrip]</option>";
	}
	echo "</select>
	</div>";
}
else
{
	echo "<div class='form-group'>
        <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center'>
		<select name='nomina' id='nomina'  class='form-control'>";
	echo "	</select></div></div>";
}

?>