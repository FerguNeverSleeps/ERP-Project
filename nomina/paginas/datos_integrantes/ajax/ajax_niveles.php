<?php
$ruta = dirname(dirname(dirname(dirname(__FILE__))));
require_once($ruta.'/lib/database.php');

if(isset($_POST['nivel']) && isset($_POST['codnivel']))
{
	$nivel    = $_POST['nivel'];
	$codnivel = $_POST['codnivel'];

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT * FROM nomempresa";
	$res = $db->query($sql, "utf8");
	$empresa = $res->fetch_assoc();

	$nomnivel = $empresa['nomniv'.$nivel];

	$sql = "SELECT codorg, CONCAT_WS(' ', codorg, descrip, markar) as descrip 
			FROM   nomnivel{$nivel} 
			WHERE  gerencia='{$codnivel}'";

	$res = $db->query($sql);	

	echo "<option value=''>Seleccione ".$nomnivel."</option>";

	while($fila = $res->fetch_assoc())
	{
		echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>"; 
	}	
}