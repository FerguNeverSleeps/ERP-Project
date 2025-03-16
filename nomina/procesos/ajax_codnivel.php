<?php
require_once('../lib/database.php');

if(isset($_POST['codnivel1']))
{
	$codnivel1 = $_POST['codnivel1'];

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT COUNT(*) as total FROM nompersonal WHERE codnivel1='{$codnivel1}'";
	$res = $db->query($sql);

	$procesar = 2000;

	if($obj = $res->fetch_object())
	{
		if( $obj->total >= 500 && $obj->total < 1000)
		{
			$procesar = 5000;
		}
		else if($obj->total >= 1000 &&  $obj->total < 1500)
		{
			$procesar = 7000;
		}
		elseif ($obj->total >1500) 
		{
			$procesar = 9000;
		}
	}

	echo $procesar;
}