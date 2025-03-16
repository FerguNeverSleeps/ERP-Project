<?php
error_reporting(E_ALL);
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
$usuario_creacion = $_SESSION['usuario'];

$a1 = $db->query("INSERT INTO nomusuarios (coduser,descrip,nivel,fecha,clave,
correo,acce_usuarios,acce_configuracion,acce_elegibles,acce_personal,
acce_prestamos,acce_consultas,acce_transacciones,acce_procesos,acce_reportes,
acce_estuaca,acce_xestuaca,acce_permisos,acce_logros,acce_penalizacion,
acce_movpe,acce_evalde,acce_experiencia,acce_antic,acce_uniforme,
contadorvence,fecclave,encript,pregunta,respuesta,
acctwind,borraper,dfecha,dfecclave,login_usuario,
acce_autorizar_nom,acce_enviar_nom,acce_generarordennomina,acce_validar_constancias,acce_editar_constancias,
img,acceso_sueldo,acceso_contraloria,acceso_s_efecto,acceso_editar,
acceso_imprimir,acceso_c_familiares,acceso_expedientes,region,departamento,
nivel3,nivel4,nivel5,acceso_dir,acceso_dep,id_rol) 
SELECT null,CONCAT(descrip,'COPY'),nivel,fecha,clave,
CONCAT(correo,'COPY'),acce_usuarios,acce_configuracion,acce_elegibles,acce_personal,
acce_prestamos,acce_consultas,acce_transacciones,acce_procesos,acce_reportes,
acce_estuaca,acce_xestuaca,acce_permisos,acce_logros,acce_penalizacion,
acce_movpe,acce_evalde,acce_experiencia,acce_antic,acce_uniforme,
contadorvence,fecclave,encript,pregunta,respuesta,
acctwind,borraper,dfecha,dfecclave,CONCAT(login_usuario,'COPY'),
acce_autorizar_nom,acce_enviar_nom,acce_generarordennomina,acce_validar_constancias,acce_editar_constancias,
img,acceso_sueldo,acceso_contraloria,acceso_s_efecto,acceso_editar,
acceso_imprimir,acceso_c_familiares,acceso_expedientes,region,departamento,
nivel3,nivel4,nivel5,acceso_dir,acceso_dep,id_rol
FROM nomusuarios WHERE coduser='".$_GET["codigo"]."'");

if($a1 === true){
    $last_id = $db->insert_id;

    $a2 = $db->query("INSERT INTO nom_modulos_usuario (id,coduser,cod_modulo) 
    select null,'".$last_id."',cod_modulo from nom_modulos_usuario where coduser='".$_GET["codigo"]."'");

    $a3 = $db->query("INSERT INTO nom_paginas_usuario (coduser,id_pagina) select '".$last_id."',id_pagina 
    from nom_paginas_usuario where coduser='".$_GET["codigo"]."'");

    $a4 = $db->query("INSERT INTO usuario_permisos (id_usuario,id_permiso) select '".$last_id."',id_permiso 
    from usuario_permisos where id_usuario='".$_GET["codigo"]."'");
}
//LOG TRANSACCIONES - CLONAR USUARIO            
$descripcion_transaccion = 'CLONAR USUARIO #' . $_GET['codigo'];
$client_ip = "";

$sql_transaccion = "INSERT INTO log_transacciones (`descripcion`, `fecha_hora`, `modulo`, `url`, `accion`, `valor`, `usuario`,`host`) 
VALUES ('".$descripcion_transaccion."', now(), 'Clonar datos de Usuario', 'usuarios_clonar.php', 'Clonar','".$_GET["codigo"]."','".$usuario_creacion."','".$client_ip."')";

$res_transaccion = $db1->query($sql_transaccion);

$data = "&data=Usuario Clonado con exito..!!";
header("location:usuarios_list.php?accion=Clonar&".$data);
