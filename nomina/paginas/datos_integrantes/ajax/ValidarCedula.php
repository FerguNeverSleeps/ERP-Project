<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	
	$cedula = isset($_REQUEST['cedula']) ? $_REQUEST['cedula']:NULL;
	
	$sql            = "SELECT estado
                            FROM nompersonal 
                            WHERE cedula='".$cedula."'";
	$res            = $db->query($sql);
        $fila = $res->fetch_assoc();
        if(count($fila)!=0)
        {
            if($fila["estado"]=="De Baja" || $fila["estado"]=="Egresado")
                $estado=1;
            else
                $estado=2;
        }
        else
        {
            $estado=0;
        }
            

echo $estado;

?>