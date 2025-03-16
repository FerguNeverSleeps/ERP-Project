<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db  = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;

	$sql = "SELECT a.cod_car, a.des_car FROM nomcargos a, nomposicion b WHERE a.cod_car=b.cargo_id AND b.nomposicion_id=".$nomposicion_id;
	$res = $db->query($sql);
        $n_filas = 0;
        
//echo $sql,"<br>";
	$print ="<option value=''>Seleccione...</option>";
	while($fila = $res->fetch_assoc())
	{
                $n_filas=$n_filas+1;
		if($fila['cod_car'] != "")
			$print="<option value='".$fila['cod_car']."' selected>".$fila['des_car']."</option>";
	}
	echo $print;
        if($n_filas==0)
        {
            $sql1 = "SELECT a.cod_car, a.des_car FROM nomcargos a ";
            $res1 = $db->query($sql1);
            while($fila1 = $res1->fetch_assoc())
            {
                echo "<option value='".$fila1['cod_car']."'>".$fila1['des_car']."</option>";
            }
        }

?>