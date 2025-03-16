<?php
session_start();
ob_start();
$anio      = ($_GET["anio"]!='') ? $_GET["anio"] : NULL ;
$mes      = ($_GET["mes"]!='') ? $_GET["mes"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$query     ="SELECT codnom, codtip, descrip 
FROM nom_nominas_pago
WHERE anio_sipe = ".$anio." AND mes_sipe = ".$mes."
        group by codnom, codtip";
$result    = $db->query($query);
function meses( $mes ) 
{
    $meses  = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $mes_letras=$meses[$mes-1];
    return $mes_letras; 
} 
echo "
	<label class='col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-center'>Planilla</label>
	<div class='col-xs-10 col-sm-10 col-md-10 col-lg-10'>
		<select name='tipos' id='tipos'  class='form-control'>";
	echo "	<option value=''>SELECCIONE UNA PLANILLA</option>";
		while ($nomina    = $result->fetch_assoc()) 
		{
			$tipos = $nomina[codnom]."_".$nomina[codtip];
	echo "	<option value='".$tipos."'>".$nomina[descrip]."</option>";

		}
echo "	</select>
	</div>
";
?>