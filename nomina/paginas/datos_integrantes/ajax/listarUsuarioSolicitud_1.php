<?php
/*$db_host      = "186.74.196.171";
$db_name      = "rb_minsa_rh";
$db_user      = "ginteven";
$db_pass      = "g1nt3v3n";

    $conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
        die( 'Could not open connection to server' );    */
    $uid_user_aprueba = $_GET['uid_user_aprueba'];
    $ruta = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($ruta.'/lib/database.php');
    $db             = new Database($_SESSION['bd']);
   //$res= mysqli_query($conexion, 'SET CHARACTER SET utf8');
   
    /*$sql = "SELECT B.USR_UID, B.USR_FIRSTNAME, B.USR_LASTNAME
				FROM ROLES A, USERS B, USERS_ROLES C
				WHERE C.ROL_UID = A.ROL_UID
				AND B.USR_UID = C.USR_UID
				AND A.ROL_UID =3";*/
    $sql="SELECT apenom, useruid FROM  nompersonal WHERE useruid <> '' ";
	$res            = $db->query($sql);

	echo '<div class="control-label col-md-8">Usuario que aprueba<select id="usuario_aprueba" name="usuario_aprueba" class="form-control form-control-inline input-medium"><option value="">Seleccione ...</option>';
	//$fila = mysqli_fetch_array($res);
	//print_r($fila);
	while($fila = mysqli_fetch_array($res))
	{
            $selected='';
            if($uid_user_aprueba==$fila['useruid'])
                $selected='selected="selected"';
            
		echo '<option '.$selected.'value="'.$fila['useruid'].'">'.$fila['apenom'].'</option>'; 
	}	
    echo '</select> </div>';


?>