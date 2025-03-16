<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

$q = empty( $_GET['q'] ) ? 0 : $_GET['q'];

$pos = $conn->fetchAll("SELECT nomposicion_id, descripcion_posicion FROM nomposicion WHERE nomposicion_id LIKE '%$q%'");
foreach ($pos as $value){
    $data[] = array('id' => $value['nomposicion_id'], 'text' => $value['nomposicion_id']);
    //$posiciones[$value['nomposicion_id']] = $value['descripcion_posicion'];
    // $data[] = array('id' => '0', 'text' => 'No Products Found');
}
echo json_encode($data);
