<?php
//TIENEN DOS BLOQUES DE PHP EN EL MISMO PHP
session_start();
require_once '../lib/common.php';
$db = new bd($_SESSION['bd_nomina']);
$sql_cabecera = "SELECT numpre,cuotas from nomprestamos_cabecera order by numpre ";
$res_cabecera = $db->query($sql_cabecera);

echo "<table border=1><th>Préstamo</th><th>Estado</th>";
while($fila_cabecra = $res_cabecera->fetch_assoc())
{
	$numpre = $fila_cabecra['numpre'];
    $sql_prestamos = "SELECT count(fechaven) cant_cuotas from nomprestamos_detalles WHERE  numpre='".$numpre."' AND estadopre = 'Cancelada'";
    $res_prestamos = $db->query($sql_prestamos);
    $fila_pres = $res_prestamos->fetch_assoc();
    if(($fila_cabecra['cuotas']) == ($fila_pres['cant_cuotas']) )
    {
        $sql_cab = "UPDATE nomprestamos_cabecera SET estadopre = 'Cancelada' where numpre ='".$numpre."'";
        $db->query($sql_cab);
        echo "<tr><td>".$numpre."</td> <td>Cancelada</td><Tr>";

    }
}
echo "</table>";
?>


<?php
//ACA INICIA EL SEGUNDO BLOQUE
session_start();
require_once '../lib/common.php';
$db = new bd($_SESSION['bd_nomina']);
$sql_cabecera = "SELECT numpre,cuotas from nomprestamos_cabecera order by numpre ";
$res_cabecera = $db->query($sql_cabecera);
echo "<table border=1><th>Préstamo</th><th>Estado</th>";
while($fila_cabecra = $res_cabecera->fetch_assoc())
{
	$numpre = $fila_cabecra['numpre'];
    $sql_prestamos = "SELECT count(fechaven) cant_cuotas from nomprestamos_detalles WHERE  numpre='".$numpre."' AND estadopre = 'Cancelada'";
    $res_prestamos = $db->query($sql_prestamos);
    $fila_pres = $res_prestamos->fetch_assoc();
    if(($fila_cabecra['cuotas']) == ($fila_pres['cant_cuotas']) )
    {
        $sql_cab = "UPDATE nomprestamos_cabecera SET estadopre = 'Cancelada' where numpre ='".$numpre."'";
        $db->query($sql_cab);
        echo "<tr><td>".$numpre."</td> <td>Cancelada</td><Tr>";

    }
}
echo "</table>";
?>
        