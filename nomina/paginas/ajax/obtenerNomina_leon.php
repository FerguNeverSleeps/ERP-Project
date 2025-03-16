<?php
session_start();
ob_start();
//$nivel1      = ($_GET["nivel1"]!='') ? $_GET["nivel1"] : NULL ;
require_once('../../../nomina/lib/database.php');
$_SESSION['bd'];
$ficha_individual=$_SESSION['ficha_rhexpress'];
$db        = new Database($_SESSION['bd']);
$query     ="SELECT DISTINCT(a.descrip) ,a.codnom, a.codtip,b.ficha FROM
nom_nominas_pago as a
INNER JOIN nom_movimientos_nomina as b ON  b.codnom=a.codnom AND a.tipnom=b.tipnom
WHERE codtip=1 and b.ficha='$ficha_individual' AND a.frecuencia in (2,3,7,8,15)";
$result    = $db->query($query);
$cant =  $result->num_rows;
if($cant > 0)
{
	echo "<div class='form-group'>
	<select name='codnom' id='codnom'  class='form-control'>";
	while ($datos    = $result->fetch_assoc()) 
	{
		echo "<option value='$datos[codnom]-$datos[codtip]'>$datos[descrip]</option>";
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