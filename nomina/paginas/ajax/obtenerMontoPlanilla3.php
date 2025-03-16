<?php
session_start();
ob_start();
$codnom      = ($_GET["codnom"]!='') ? $_GET["codnom"] : NULL ;
$codtip      = ($_GET["tipnom"]!='') ? $_GET["tipnom"] : NULL ;
$ficha       = ($_GET["ficha"]!='') ? $_GET["ficha"] : NULL ;
 
require_once('../../../nomina/lib/database.php');
$db        = new Database($_SESSION['bd']);
$bd_asig=new Database($_SESSION['bd']);
    $sql_asig = "SELECT SUM(nmn.monto) as monto
    FROM nom_movimientos_nomina nmn 
    INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
    WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$ficha}' AND nmn.tipcon='A'
    AND (nmn.codcon >= '300' AND nmn.codcon <='399')
    ORDER BY nmn.ficha,nmn.codcon";

    $res_asig=$bd_asig->query($sql_asig);
    $monto_asig = $res_asig->fetch_assoc();

    /** DEDUCCIONES **/
    $bd_deduc=new Database($_SESSION['bd']);
    $sql_deduc = "SELECT SUM(nmn.monto) as monto
    FROM nom_movimientos_nomina nmn 
    INNER JOIN nompersonal np ON (np.ficha=nmn.ficha) 
    WHERE nmn.codnom='".$codnom."' AND nmn.tipnom='".$codtip."' AND nmn.ficha='{$ficha}' AND nmn.tipcon='D'
    AND (nmn.codcon >= '300' AND nmn.codcon <='399')
    ORDER BY nmn.ficha,nmn.codcon";
    
    /** Cálculo del neto**/
    $res_deduc=$bd_deduc->query($sql_deduc);
    $monto_deduc = $res_deduc->fetch_assoc();
    $neto = $monto_asig[monto] - $monto_deduc[monto];
    $neto_empleado=number_format($neto,2,'.','');
    $data = array('neto' => $neto_empleado);
    echo json_encode($data);


?>