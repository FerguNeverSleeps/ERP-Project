<?php
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
include ("../header4.php");
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
$usuario_creacion = $_SESSION['usuario'];

//Obtiene la IP del cliente
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
//-----------------------------------------------------
if(isset($_POST['aceptar'])){

    $a1 = $db->query("DELETE FROM nomusuarios where coduser='".$_POST["codigo"]."'");
    $a2 = $db->query("DELETE FROM nomusuario_empresa where coduser='".$_POST["codigo"]."'");
    $a3 = $db->query("DELETE FROM nomusuario_nomina where coduser='".$_POST["codigo"]."'");
    $a4 = $db->query("DELETE FROM nom_modulos_usuario where coduser='".$_POST["codigo"]."'");
    $a5 = $db->query("DELETE FROM nom_paginas_usuario where coduser='".$_POST["codigo"]."'");
    $a6 = $db->query("DELETE FROM usuario_permisos where id_usuario='".$_POST["codigo"]."'");
    //LOG TRANSACCIONES - ELIMINAR USUARIO              
    $descripcion_transaccion = 'ELIMINAR USUARIO: ' . $_POST['login_usuario'] . ', ' . $_POST['descrip']  . ', Cod Usuario: '. $codigo .', Correo: '. $_POST['correo'].', Region: '. $_POST['region'].', Departamento: '. $_POST['departamento'].', Acceso: '. $_POST['acceso_dir'].' - '. $_POST['acceso_dep'].',  Rol: '. $_POST['id_rol'];
    $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
    VALUES ('', '".$descripcion_transaccion."', now(), 'Eliminar Usuario', 'usuarios_delete.php', 'Eliminar','".$_POST['login_usuario']."','".$usuario_creacion."','".get_client_ip()."')";
    $res_transaccion = $db1->query($sql_transaccion);
    $data = "&data=Usuario Eliminado con exito..!!";
    header("location:usuarios_list.php?accion=Eliminar&$id_msj$msj$data");

}
$codigo = $_GET['codigo'];
$consulta="select * from nomusuarios where coduser='".$codigo."'";
$resultado=$db->query($consulta,$conexion);
$fila=mysqli_fetch_array($resultado);
?>
<SCRIPT language="JavaScript" type="text/javascript">

function cerrar(retorno){
	document.location.href=retorno+".php?pagina=1"
}

</SCRIPT>

<form name="sampleform" method="POST" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="page-container">
        <!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
<div class="page-content">
	<div class="row">
        <div class="col-md-12">
        	<div class="portlet box blue">
        		<div class="portlet-title">
                    <h4>Usuarios</h4>
                </div>
                <div class="portlet-body">
                	<div class="row">
                        <div class="col-md-4">
                        	<label>Eliminar un registro de usuarios</label>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
							<label>Nombre</label>
                        </div>
                        <div class="col-md-4"> 
                            <input type="hidden" name="codigo" value="<?php echo $codigo; ?>">
                            <input type="text" name="descrip" class="form-control" readonly="readonly" value="<?php echo $fila["descrip"]; ?>">
                        </div>
                    </div> 
                    <br>
                    <div class="row">
                    	<div class="col-md-3">
							<label>Nombre de Usuario</label>
                        </div>
                        <div class="col-md-4">
                        	<input type="text" name="login_usuario" class="form-control" readonly="readonly" value='<?php echo $fila["login_usuario"]; ?>'>
                        </div>
                    </div> 
                    <br>
                </div>
                <div align="right">
                		<?php if($_SESSION["coduser"]!=$_GET["codigo"]){ ?>
							 <input type="submit" name="aceptar" value="Aceptar">&nbsp;
						<?php }?>
							<input type="button" name="cancelar" value="Cancelar" onclick="javascript:cerrar('usuarios_list');">
                </div>
        	</div>
        </div>
    </div>
</div>
</div>
</div>
</form>
</body>
</html>
