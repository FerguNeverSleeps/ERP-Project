<?php
$params = array(
    'dbname'   => "asamblea",
    'user'     => "root",
    'password' => "",
    'host'     => "localhost",
    'driver'   => 'pdo_mysql', 
    'charset'  => 'utf8',    
);

$conexion = new mysqli( $params['host'], $params['user'], $params['password'], $params['dbname'] );

$conexion->query("DELETE FROM `caa_registros` WHERE fecha_registro = '".date("Y-m-d")."'");
$conexion->query("ALTER TABLE `caa_registros` AUTO_INCREMENT =1");

$nombre_fichero= "20161204.log";
$ruta = "/media/reloj/" . $nombre_fichero ;
$gestor = fopen($ruta, "r");
while (($data = fgetcsv($gestor, 1000, "\n")) !== false) {
    
    foreach ($data as $value) {
        $linea = explode( "," , $value );

        $dispositivo = (int) $linea[0].(int) $linea[1];
        $date = new DateTime( $linea[5].":".$linea[6].":00" );
        $hora = $date->format('H:m:s');
        $date = new DateTime($linea[9]."-".$linea[7]."-".$linea[8]);
        $fecha = $date->format('Y-m-d');

        $sql = "INSERT INTO `caa_registros`(`fecha_registro`,`ficha`,`fecha`,`hora`,`turno_id`,`dispositivo`,`archivo_reloj`)VALUES('".date("Y-m-d")."','". (int) $linea[3]."','".$fecha."','".$hora."','1','".$dispositivo."','1')";
        echo $sql."<br>";
        $res = $conexion->query($sql);
        if ($res) {
            echo "SI guardo <br>";
        }else{
            echo "NO guardo <br>";
        }
    }
}
fclose($gestor);
?>