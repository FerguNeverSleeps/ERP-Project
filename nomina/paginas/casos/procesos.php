<?php
session_start();
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

$sql = "SELECT *
        FROM   procesos";
        
$res = $db->query($sql);
$data="";

while ($fila = $res->fetch_array()) 
{
	$data.= "<div class='dd' id='nestable_list_".$fila[0]."'>";
	$data.= '<ol class="dd-list">';
	$data.= '<li>';
	
	$data.='<div class="dd-handle">'.$fila[1].'</div>';
	$data.='</li>';
	$sql2="SELECT *
			FROM subprocesos
			WHERE id_procesos=".$fila[0];
	$res2= $db ->query($sql2);
	$data.= '<ol class="dd-list">';
										
	while ($fila2 = $res2->fetch_array()) 
	{
			$data.='<li class="dd-item" data-id="'.$fila2[0].'">';
			$data.='<div class="dd-handle">';
			$data.= $fila2[2];
			$data.='</div>';
			$data.='</li>';
	}
	$data.= "</ol>";
	$data.= "</div>";

}
echo $data;
?>