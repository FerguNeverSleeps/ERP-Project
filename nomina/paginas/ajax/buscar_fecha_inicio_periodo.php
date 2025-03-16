<?php
session_start();  

$id_periodo     = (isset($_GET['id_periodo'])) ? $_GET['id_periodo'] : '' ;
require_once('../../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
$query       = "SELECT numBisemana,fechaInicio
                FROM bisemanas 
                WHERE numBisemana = '{$id_periodo}'";
$filas       = $db->query($query)->fetch_assoc();

echo date("d/m/Y", strtotime($filas['fechaInicio']));


?>
