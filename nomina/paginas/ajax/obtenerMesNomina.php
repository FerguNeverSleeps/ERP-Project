<?php
session_start();
ob_start();
$anio      = ($_GET["anio"]!='') ? $_GET["anio"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     ="SELECT mes 
FROM nom_nominas_pago
WHERE anio = ".$anio."
        group by mes";
$result    = $db->query($query);
function meses( $mes ) 
{
    $meses  = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $mes_letras=$meses[$mes-1];
    return $mes_letras; 
} 
echo "
	<label class='col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-center'>Mes</label>
	<div class='col-xs-10 col-sm-10 col-md-10 col-lg-10'>
		<select name='mes' id='mes'  class='form-control'>";
	echo "	<option value=''>SELECCIONE UN MES</option>";
		while ($nomina    = $result->fetch_assoc()) 
		{
	echo "	<option value='".$nomina["mes"]."'>".meses($nomina["mes"])."</option>";

		}
echo "	</select>
	</div>
";
?>