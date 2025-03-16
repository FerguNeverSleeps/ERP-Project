<?php
include_once "../../nomina/lib/common_excel.php";

  	if( isset($_POST['usuario']) || isset($_GET['usuario']) )
  	{
  		$usuario = (isset($_POST['usuario'])) ? $_POST['usuario'] : $_GET['usuario'];

		try{
			$config_conex=conexion();
			
			$sqldb = "select e.* 
                    from ".SELECTRA_CONF_PYME.".nomempresa e 
					where e.nomina_activo='1'";
			$res=query($sqldb, $config_conex);
				
			echo "<option value=''>Seleccione una organizaci&oacute;n</option>";
			
			while($fila=fetch_array($res))
			{
				$sqlemp = "select e.usuario_workflow from ".$fila['bd_nomina'].".nompersonal e 
					where e.usuario_workflow='".$usuario."';";
				
				$res2 = query2($sqlemp, $config_conex);
				$fila2=fetch_array($res2);

				if($fila2["usuario_workflow"]){
					echo "<option value='" . $fila['bd_nomina'] . "'>" . $fila['nombre'] . "</option>";
					//echo $sqlemp;exit;
				}
			}exit;

		}catch(Exception $e) {
			echo 'Error No: ' . $e->getCode() . 'Error Message:' . $e->getMessage();
		}		
  	}
?>