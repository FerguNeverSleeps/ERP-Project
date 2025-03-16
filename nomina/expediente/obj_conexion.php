<?php        
        include('../../generalp.config.inc.php');
	$db = new Database(DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd']);               
	$db->connect();	
	//echo DB_HOST.' '.DB_USUARIO.' '.DB_CLAVE.' '.$_SESSION['bd'];	
	$error=$db->error;
?>
