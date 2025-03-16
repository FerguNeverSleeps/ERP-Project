<?php

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
        $sql="SELECT personal_id, ficha, apenom, cedula FROM  nompersonal";
	$res            = $db->query($sql);

	echo '<div class="control-label col-md-2">Usuario Aprueba</div>'
        . '<div class="col-md-8">'        
        . '<select id="usuario_aprueba" name="usuario_aprueba" class="form-control select2me"><option value="">Seleccione ...</option>';
	//$fila = mysqli_fetch_array($res);
	//print_r($fila);
	while($fila = mysqli_fetch_array($res))
	{
            $selected='';
            if($uid_user_aprueba==$fila['useruid'])
                $selected='selected="selected"';
            
		echo '<option '.$selected.'value="'.$fila['useruid'].'">'.$fila['cedula']." - ".$fila['apenom'].'</option>'; 
	}	
        echo '</select> '
        . '</div>'
        . '<div class="col-md-2" style="text-align: right;" >'
        . '   <button type="button" class="btn green" onclick="javascript:actualizar_departamento();">Actualizar</button>'
        . '</div>';        

?>