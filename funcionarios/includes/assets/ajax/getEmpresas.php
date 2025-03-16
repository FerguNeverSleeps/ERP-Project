<?php
  include_once('../../../nomina/lib/common_excel.php');

  if(isset($_POST['usuario']))
  {
  		$usuario = $_POST['usuario'];

		try{
			$conexion=conexion();

            $sql = "select e.* 
                    from ".SELECTRA_CONF_PYME.".nomempresa e 
            		inner join ".SELECTRA_CONF_PYME.".nomusuario_empresa ne on ne.id_empresa = e.codigo and ne.acceso=1 
            		inner join ".SELECTRA_CONF_PYME.".nomusuarios u on u.coduser = ne.id_usuario 
             		where u.login_usuario='". $usuario ."' ";
            // where e.bd_nomina != '".$request['db_connect'] ."' and u.login_usuario='". $usuario ."'

			$res=query($sql, $conexion);

			echo "<option value=''>Seleccione una organizaci&oacute;n</option>";

			while($fila=fetch_array($res))
			{
	        	echo "<option value='" . $fila['bd_nomina'] . "'>" . $fila['nombre'] . "</option>";
			}

		}catch(Exception $e) {
			echo 'Error No: ' . $e->getCode() . 'Error Message:' . $e->getMessage();
		}		
  }
?>