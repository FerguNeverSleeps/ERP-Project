<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

$q = empty( $_GET['q'] ) ? 0 : $_GET['q'];

$car = $conn->fetchAll("SELECT cod_car, des_car FROM nomcargos WHERE des_car LIKE '%$q%' ORDER BY des_car");
foreach ($car as $value){
    $data[] = array('id' => $value['cod_car'], 'text' => $value['des_car']);
}

echo json_encode($data);
