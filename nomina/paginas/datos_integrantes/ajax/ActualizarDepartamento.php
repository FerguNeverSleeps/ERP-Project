<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	
	$IdDepartamento = isset($_REQUEST['IdDepartamento']) ? $_REQUEST['IdDepartamento']:NULL;
        $IdJefe = isset($_REQUEST['IdJefe']) ? $_REQUEST['IdJefe']:NULL;
        $uid_jefe = isset($_REQUEST['uid_jefe']) ? $_REQUEST['uid_jefe']:NULL;
	
	$sql            = "UPDATE departamento
                           SET IdJefe='".$IdJefe."', uid_jefe='".$uid_jefe."'
                           WHERE IdDepartamento='".$IdDepartamento."'";
	$res            = $db->query($sql);
        if($res)
        {
           
          $estado=1;
          
        }
        else
        {
            $estado=0;
        }
            

echo $estado;

?>