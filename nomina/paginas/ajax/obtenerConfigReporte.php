<?php
session_start();
ob_start();
//$nivel1      = ($_GET["nivel1"]!='') ? $_GET["nivel1"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     = "SELECT * FROM config_reportes_planilla";
$result    = $db->query($query);
$cant      =  $result->num_rows;
if($cant > 0)
{
	echo "<div class='form-group'>
	<select name='config_rpt_id' id='config_rpt_id'  class='form-control'>";
	while ($datos    = $result->fetch_assoc()) 
	{
		echo "<option value='$datos[id]'>$datos[descrip]</option>";
	}
	echo "</select>
	</div>";
}
else
{
	echo "<div class='form-group'>
        <div class='col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center'>
		<select name='config_rpt_id' id='config_rpt_id'  class='form-control'>";
	echo "	</select></div></div>";
}

?>