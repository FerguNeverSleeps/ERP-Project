<?php
require_once('../lib/database.php');

if(isset($_POST['codnom1']))
{
	$codnom1 = $_POST['codnom1'];

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT mes, anio FROM nom_nominas_pago WHERE codnom={$codnom1}";
	$res = $db->query($sql);

	if($planilla1 = $res->fetch_object())
	{
		$sql = "SELECT codnom, descrip, codtip 
				FROM   nom_nominas_pago 
				WHERE  codtip = '{$_SESSION['codigo_nomina']}' AND frecuencia=3
				AND    mes={$planilla1->mes} AND anio={$planilla1->anio}";	
		$res = $db->query($sql);

		echo "<option value=''>Seleccione una Planilla</option>";
		while($planilla2 = $res->fetch_object())
		{
			echo "<option value='{$planilla2->codnom}'>{$planilla2->descrip}</option>"; 
		}
	}	
}