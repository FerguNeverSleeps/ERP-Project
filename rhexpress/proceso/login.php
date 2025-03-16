<?php
//-------------------------------------------------
$manual = true;
require_once "../config/rhexpress_config.php";
//-------------------------------------------------
if ( (!empty($_POST['txtUsuario']))&&(!empty($_POST['txtContrasena']))&&(!empty($_POST['empresaSeleccionada'])) )
{
	$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_POST['empresaSeleccionada'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
	mysqli_query($conexion, 'SET CHARACTER SET utf8');

	$sql = "SELECT a.*, b.descrip AS gerencia, c.descrip AS dpto, d.des_car AS cargo,
	e.Descripcion AS departamento,e.IdJefe AS jefe
	FROM nompersonal AS a
	LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
	LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
	LEFT JOIN nomcargos AS d ON a.codcargo = d.cod_car
	LEFT JOIN departamento AS e ON a.IdDepartamento = e.IdDepartamento
	WHERE usuario_workflow = '".$_POST['txtUsuario']."' AND usr_password = '".md5($_POST['txtContrasena'])."'";
	
	$res = $conexion->query($sql);
	
	if ($usuario = mysqli_fetch_array($res)) {
		session_start();
		$_SESSION['usuario_rhexpress']  = $usuario['usuario_workflow'];
		$_SESSION['nombre_rhexpress']   = $usuario['apenom'];
		$_SESSION['cedula_rhexpress']   = $usuario['cedula'];
		$_SESSION['ficha_rhexpress']    = $usuario['ficha'];
		$_SESSION['pos_rhexpress']      = $usuario['nomposicion_id'];
		$_SESSION['useruid_rhexpress']  = $usuario['useruid'];
		$_SESSION['gerencia_rhexpress'] = $usuario['gerencia'];
		$_SESSION['dpto_rhexpress']     = $usuario['dpto'];
		$_SESSION['_Departamento']      = $usuario['departamento'];
		$_SESSION['_Jefe']       			  = $usuario['jefe'];
		$_SESSION['cargo_rhexpress']    = $usuario['cargo'];
		$_SESSION['foto_rhexpress']     = $usuario['foto'];
		$_SESSION['bd']     = $_POST['empresaSeleccionada'];
		header("location:../rhexpress_menu.php");
	}else{
		header("location:../rhexpress_login.php?error=El Usuario o la Clave son invalidos, por favor verifique la informacion.");
	}
}
?>
