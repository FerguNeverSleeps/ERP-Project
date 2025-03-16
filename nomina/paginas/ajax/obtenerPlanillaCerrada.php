<?php
session_start();
ob_start();
$anio      = ($_GET["anio"]!='') ? $_GET["anio"] : NULL ;
$mes      = ($_GET["mes"]!='') ? $_GET["mes"] : NULL ;
$tipo      = ($_GET["tipo"]!='') ? $_GET["tipo"] : NULL ;
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);

$query     ="
SELECT codnom, codtip, descrip 
FROM nom_nominas_pago 
WHERE status = 'C' ";
if($tipo != NULL){
	$sql_WHERE =  " AND codtip = '{$tipo}'";
	$query .=$sql_WHERE;
}
$result    = $db->query($query);

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