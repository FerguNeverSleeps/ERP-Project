<?php
session_start();  
$cedula      = (isset($_GET['cedula'])) ? $_GET['cedula'] : '' ;
$periodo     = (isset($_GET['periodo'])) ? $_GET['periodo'] : '' ;
require_once('../../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
$query       = "SELECT saldo_dias_pdisfrutar
FROM nom_progvacaciones 
WHERE ceduda = '{$cedula}' AND periodo = '{$periodo}'";
$filas       = $db->query($query)->fetch_assoc();

echo $filas['saldo_dias_pdisfrutar'];


?>