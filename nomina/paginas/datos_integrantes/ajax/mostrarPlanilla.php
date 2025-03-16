<?php
	$ruta  = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	
	$db    = new Database($_SESSION['bd']);
	if (($_REQUEST['nomposicion_id'])!=NULL) {
			
		$sql1  = "SELECT tipnom FROM nompersonal WHERE nomposicion_id=".$nomposicion_id;
		$res1  = $db->query($sql1);
		$fila1 = $res1->fetch_assoc();

		$sql2  = "SELECT codtip,descrip FROM nomtipos_nomina	WHERE codtip='".$fila1["tipnom"]."'";
		$res2  = $db->query($sql2);
		$fila2 = $res2->fetch_assoc();

		$sql3   = "SELECT codtip,descrip FROM nomtipos_nomina ORDER BY codtip ASC";
		$res3   = $db->query($sql3);

		echo '<div class="form-group"><label class="col-md-3">Planilla</label><div class="com-md-8"><select id="planilla_estructura" name="planilla_estructura" class="form-control form-control-inline input-medium"><option value='.$fila2["codtip"].'>'.$fila2["descrip"].'</option>' ;
	    while($fila3 = $res3->fetch_assoc())
	    {
	    	echo '<option value="'.$fila3["codtip"].'">'.$fila3["descrip"].'</option>';
	    }
	    echo '</select></div></div>';

		if($fila['ctacontab'])
		{
			echo '<input type="text" name="cuentacontable_estructura" id="cuentacontable_estructura" class="form-control form-control-inline input-medium" value="'. $fila['ctacontab'].'"></div>
				</div>';
			
		}else{
			//echo '<div class="alert alert-danger" role="alert">Posición No Disponible</div>';
		}
	}


?>