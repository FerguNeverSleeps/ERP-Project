<?php
session_start();
/*
 * Pagina Principal del sistema de facturacion.
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("../../../config.ini.php");

# La siguiente linea comentada sobra
#require_once(RAIZ_PROYECTO . "/libs/php/clases/login.php");

$default_version_jquery="jquery-1.3.2.min.js";


$login = new Login();

// Forzar conexion
// Esta rutina debe ser refactorizada y debe ser removida de este lugar.
if(array_key_exists("linked",$_GET)){

    require_once("../../../menu_sistemas/lib/common.php");
    $conexion = new bd(SELECTRA_CONF_PYME);
    $empresas = new bd(SELECTRA_CONF_PYME);

    //resolviendo bug
    if(isset($_SESSION["nombre_empresa_nomina"])){
        $empresa_nombre = $_SESSION["nombre_empresa_nomina"];
    }else{
        $empresa_nombre = $_SESSION["empresa"];
    }

    $query = "SELECT * FROM nomempresa WHERE nombre = '".$empresa_nombre."';";
    $resultado_empresas = $empresas->query($query);
    $empresas_lista =  $resultado_empresas->fetch_assoc();


    $_SESSION['EmpresaFacturacion'] = $empresas_lista["bd"]; #Base de datos
}

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
if (isset($_GET["logout"])) {

    require_once("../../../lib/common.php");
    //LOG TRANSACCIONES - REGISTRAR LOGOUT
    $db1 = new bd($_SESSION['bd_nomina']);
    $descripcion_transaccion = 'LOGOUT USUARIO: ' . $_SESSION['usuario'] . ', Empresa: '. $_SESSION['nombre_empresa'].", Desconexión";
    $descripcion_transaccion = utf8_decode($descripcion_transaccion);
    $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
    VALUES ('', '".$descripcion_transaccion."', now(), 'Login Usuario', 'logout.php', 'Logout','". $_SESSION['usuario']."','". $_SESSION['usuario']."','".get_client_ip()."')";
    $res_transaccion = $db1->query($sql_transaccion);
    $javascript1 = "<script> location = '../../../'; </script>";
    $smarty->assign("javascript1", $javascript1);
    $login->logout();
    header("Location: ../sesion/");
    //$smarty->display("salida.tpl");
    exit();
}
if ($login->validarLoginON() != 1) {
    header("Location: ../sesion/");
}



$menu = new Menu();
$parametrosgenerales = new ParametrosGenerales();




if (isset($_GET["opt_menu"])) {
    if (!is_numeric($_GET["opt_menu"])) {
        header("Location: ?opt_menu=54");
    }
} else {
    header("Location: ?opt_menu=54");
}
$sql = "select evento,hora_inicio "
                . " from selectra_conf_pyme.evento_usuario "
                . " where curdate() between  fecha_inicio and fecha_fin "
                . " order by hora_inicio asc ";
$datanotificaciones = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);
$smarty->assign("data_notificaciones_count",$parametrosgenerales->getFilas());
$smarty->assign("data_notificaciones",$datanotificaciones);

$sql = "select usuario,nombreyapellido,foto foto_usuario "
                . " from selectra_conf_pyme.usuarios "
                . " where usuario != '".$_SESSION['usuario']."' "
                . " order by nombreyapellido asc ";
$datausuarios = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);
$smarty->assign("data_usuarios_count",$parametrosgenerales->getFilas());
$smarty->assign("data_usuarios",$datausuarios);


$operacion = $parametrosgenerales->ObtenerFilasBySqlSelect("SELECT * from selectra_conf_pyme.operacion_nivel where operacion_id=42; ");
$smarty->assign("nivel_operacion_ver_dash_ventas", $operacion[0]["nivel_id"]);
$operacion = $parametrosgenerales->ObtenerFilasBySqlSelect("SELECT * from selectra_conf_pyme.operacion_nivel where operacion_id=43; ");
$smarty->assign("nivel_operacion_ver_dash_compras", $operacion[0]["nivel_id"]);
$smarty->assign("nivel_usuario", $_SESSION['nivel_usuario']);


$paramtros_generales_sistema = $parametrosgenerales->ObtenerFilasBySqlSelect("select * from parametros_generales");
$smarty->assign("multi_moneda", $paramtros_generales_sistema[0]['multi_moneda']);



if (isset($_GET["opt_menu"]) && $_GET["opt_menu"] == 54) {

    $cabeceraSeccionesByOptMenu = $menu->getCabeceraSeccionesByOptMenu($_GET["opt_menu"]); //Devuelve el nombre de option de menu principal que
    $archivophp = trim($cabeceraSeccionesByOptMenu[0]["archivo_php"]);
    $archivotpl = trim($cabeceraSeccionesByOptMenu[0]["archivo_tpl"]);
    $archivotpl = "pagina_inicio_soft.tpl";
    $sql = "select i.cod_item,sum(fd._item_preciosiniva) precio,sum(fd._item_cantidad) cantidad,i.descripcion1 "
        . " from factura_detalle fd "
        . " inner join item i on i.id_item = fd.id_item "
        . " where 1 "
        . " group by fd.id_item "
        . " order by cantidad desc "
        . " limit 10 ";
        $dataproductos = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);

        $smarty->assign("data_productos_count",10);//$parametrosgenerales->getFilas()
        $smarty->assign("data_productos",$dataproductos);

        $sql = "select cl.cod_cliente,count(f.id_factura) cantidad,cl.nombre "
                . " from factura f "
                . " inner join clientes cl on cl.id_cliente = f.id_cliente "
                . " where 1 "
                . " group by f.id_cliente "
                . " order by cantidad desc "
                . " limit 10 ";
        $dataclientes = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);

        $smarty->assign("data_clientes_count",10);//$parametrosgenerales->getFilas()
        $smarty->assign("data_clientes",$dataclientes);

        $sql = "select cl.cod_proveedor,count(f.id_compra) cantidad,cl.descripcion "
                . " from compra f "
                . " inner join proveedores cl on cl.id_proveedor = f.id_proveedor "
                . " where 1 "
                . " group by f.id_proveedor "
                . " order by cantidad desc "
                . " limit 10 ";
        $dataproveedores = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);
        $smarty->assign("data_proveedores_count",10);//$parametrosgenerales->getFilas()
        $smarty->assign("data_proveedores",$dataproveedores);

        $sql = "select evento,hora_inicio "
                . " from selectra_conf_pyme.evento_usuario "
                . " where curdate() between  fecha_inicio and fecha_fin "
                . " order by hora_inicio asc ";
        $datanotificaciones = $parametrosgenerales->ObtenerFilasBySqlSelect($sql);
        $smarty->assign("data_notificaciones_count",$parametrosgenerales->getFilas());
        $smarty->assign("data_notificaciones",$datanotificaciones);
        

        $datosempresa = $parametrosgenerales->ObtenerFilasBySqlSelect("select * from parametros_generales");
        $smarty->assign("empresa",$datosempresa);
        $smarty->assign("ruta", "?iframe&opt_menu=".$_GET["opt_menu"]);
}
else if (isset($_GET["opt_menu"]) && !isset($_GET["opt_seccion"]) && !isset($_GET["opt_subseccion"])) {
    // Entra aqui si o solo si el usuario ha seleccionado
    // una de las opciones Principales del Menu para cargar las secciones.
    $cabeceraSeccionesByOptMenu = $menu->getCabeceraSeccionesByOptMenu($_GET["opt_menu"]); //Devuelve el nombre de option de menu principal que
    //seleccionamos asi como la imagen que debera cargar de lado izquierdo.
    $seccionesByOptMenu = $menu->getSeccionesByOptMenu($_GET["opt_menu"]); //Devuelve todas las secciones de acuerdo a la
    //Opcion pedida del menu principal.
    $archivotpl = "secciones_by_opt-menu.tpl"; // este es el contenedor que carga las secciones
    $archivotpl = "index_iframe.tpl"; // tpl sin menu para solucionar bug
    // de acuerdo a la opcion de menu principal seleccionada.
    $smarty->assign("cabeceraSeccionesByOptMenu", $cabeceraSeccionesByOptMenu);
    $smarty->assign("seccionesByOptMenu", $seccionesByOptMenu);
} elseif (isset($_GET["opt_menu"]) && isset($_GET["opt_seccion"]) && !isset($_GET["opt_subseccion"])) {
    // Entra aqui si o solo si el usuario
    // solo ha seleccionado una de las opciones de una seccion (opt_seccion)
    $smarty->assign("ruta", "?opt_menu=".$_GET["opt_menu"]."&opt_seccion=".$_GET["opt_seccion"]);    
    $archivo = $menu->getArchivosPHPTPL($_GET["opt_menu"], $_GET["opt_seccion"]);
    $smarty->assign("nombre_menu", $archivo[0]["descripcion_optseccion"]);
    $smarty->assign("nombre_menu_padre", $archivo[0]["descripcion_optmenu"]);

    if ($archivo != "") {
        $archivophp = trim($archivo[0]["archivo_php"]);
        $archivotpl = $archivo[0]["archivo_tpl"];
        if (file_exists($archivo[0]["archivo_php"])) {
            include($archivophp);
            #echo "<script> alert ('$archivophp');</script>";
        }#Opcion pedida del menu principal.
        $smarty->assign("subseccion", $archivo);
    }
} else {
    #if (isset($_GET["opt_menu"]) && isset($_GET["opt_seccion"]) && isset($_GET["opt_subseccion"])) {
    // Opcion pedida en donde se especifica que tipo de mantenimiento se desea hacer sobre un submodulo: add, edit, delete
    $instruccion = "
            SELECT
                opt_menu.cod_modulo as optmenu,
                subseccion.*,
                opt_seccion.img_ruta
            FROM modulos opt_menu
            INNER JOIN
                modulos opt_seccion ON opt_menu.cod_modulo = opt_seccion.cod_modulo_padre
            INNER JOIN
                subseccion ON subseccion.cod_seccion  = opt_seccion.cod_modulo
            WHERE
                opt_menu.cod_modulo = {$_GET["opt_menu"]} AND
                subseccion.cod_seccion = {$_GET["opt_seccion"]} AND
                subseccion.opt_subseccion = '{$_GET["opt_subseccion"]}'
            ORDER BY opt_menu.orden";
    $archivo = $menu->ObtenerFilasBySqlSelect($instruccion);
    if ($archivo != "") {
        $archivophp = trim($archivo[0]["archivo_php"]);
        $archivotpl = trim($archivo[0]["archivo_tpl"]);
        if (file_exists($archivophp)) {
            #include $archivophp?file_exists($archivophp):"error.php";
            include $archivophp;
        }
        $smarty->assign("subseccion", $archivo);
        $smarty->assign("ruta", "?opt_menu=".$_GET["opt_menu"]."&opt_seccion=".$_GET["opt_seccion"]."&opt_subseccion=".$_GET["opt_subseccion"]);
    }//if($archivo!=-1){
    #}//if(isset($_GET["opt_menu"])==true&&isset($_GET["opt_seccion"])==true&&isset($_GET["opt_subseccion"])==true){ // Opcion pedida en donde se especifica que tipo de mantenimiento se desea hacer sobre un submodulo: add, edit, delete
}

// Reescribir version de jquery si es necesario
// para determinados templates tpl
if(!empty($archivotpl)){
    switch ($archivotpl) {
        case 'otros_ingresos.tpl':
        case 'otros_egresos.tpl':
        case 'factura_rapida_nueva.tpl':
        case 'pedido_rapido_nuevo.tpl':
        case 'pedido_rapido_nuevo_opcional.tpl':
        case 'ingreso_cambio_cheques.tpl':
        case 'egresos_pago_proveedores.tpl':
        case 'factura_modelo_rapida.tpl':
        case 'factura_modelo_detal.tpl':
        case 'factura_modelo_directa.tpl':
        case 'factura_modelo_concepto.tpl':
        case 'presupuesto_nuevo.tpl':
       // case 'entrada_almacen_nuevo.tpl':
        case 'ingreso_cobranza_clientes.tpl':
            $default_version_jquery="jquery-1.10.2.min.js";
            break;
        case 'herramientas_migrador.tpl':
        case 'actualizador.tpl':
        case 'novedades.tpl':
            $default_version_jquery="jquery-1.4.2.min.js";
            break;
    }

}

$campos = $parametrosgenerales->ObtenerFilasBySqlSelect("SELECT * FROM parametros_generales");
$smarty->assign("DatosGenerales", $campos);

$msgAUsuario2 = Msg::getMessage();
Msg::setMessage("");
$smarty->assign("idsesion", $login->getIdSessionActual());
$lista_menu = $menu->getMenu($_SESSION["cod_usuario"]);

$custom_menu = array();
foreach ($lista_menu as $key_parent_menu => $parent_menu) {
    $custom_menu []= $parent_menu;
    if($parent_menu["cod_modulo_padre"]==0){
        $children_menu = $menu->getSeccionesByOptMenu($parent_menu["cod_modulo"]);
        $custom_menu[$key_parent_menu]["children_menu"] = $children_menu;
    }
}

$smarty->assign("msgAUsuario", $msgAUsuario2);
$smarty->assign("DB_HOST", DB_HOST);
$smarty->assign("db_connect", $_SESSION['bd_administrativo']);
$smarty->assign("DB_SELECTRA_FAC", DB_SELECTRA_FAC);
$smarty->assign("archivotpl", $archivotpl);
$smarty->assign("archivophp", $archivophp);
$smarty->assign("codusuario", $_SESSION["cod_usuario"]);
$smarty->assign("stringLoginUsuario", $_SESSION["usuario"]);
$smarty->assign("nombre", ucwords(strtolower($_SESSION['nombreyapellido'])));

$path_foto_usuario = ($_SESSION['foto']=="")?"../../../includes/assets/img/User-red-icon.png":"http://localhost/pyme_usuario/web/uploads/pictures/users/".$_SESSION['foto'];
$existe_foto = @file_get_contents($path_foto_usuario);
if(!$existe_foto){
    $path_foto_usuario = "../../../includes/assets/img/User-red-icon.png";
}
$smarty->assign("foto_usuario", $path_foto_usuario);
//$smarty->assign("foto_usuario", "../../../includes/assets/img/User-red-icon.png");
//"../../../nomina/paginas/fotos/josemorales.jpg"
$smarty->assign("opt_menu", $_GET['opt_menu']);
$smarty->assign("opt_seccion", $_GET['opt_seccion']);
$smarty->assign("opt_subseccion", $_GET['opt_subseccion']);
$t = new DateTime($_SESSION["ultimo_login"]);
$smarty->assign("stringUltimaSesionFecha", $t->format("d-m-Y"));
$smarty->assign("stringUltimaSesionHora", $t->format("h:i:s"));
$smarty->assign("itemMenuPrincipal", $custom_menu);
$fecha = date('d/m/Y');

$campos = $parametrosgenerales->ObtenerFilasBySqlSelect("select id_divisa, Nombre, Abreviatura from parametros_generales,divisas where id_divisa = moneda_base ");
$campos2 = $parametrosgenerales->ObtenerFilasBySqlSelect("select * from moneda,divisas where id_divisa = moneda_actual ");
$campos3 = $parametrosgenerales->ObtenerFilasBySqlSelect("select cambio_unico from moneda");
$moneda_base = $parametrosgenerales->ObtenerFilasBySqlSelect("select moneda_base from parametros_generales");
$iddivisa2 = $campos2[0]['id_divisa'];
$cadena3 = "select id from tasas_cambio where date_format(fecha,'%d/%m/%Y')='$fecha' and divisa = $iddivisa2  and monedabase = '" . $moneda_base[0]['moneda_base'] . "'";
$campos4 = $parametrosgenerales->ObtenerFilasBySqlSelect($cadena3);

$abrev1 = $campos[0]['Abreviatura'];
$abrev2 = $campos2[0]['Abreviatura'];
$moneda1 = $campos[0]['Nombre'];
$moneda2 = $campos2[0]['Nombre'];

// if ($campos3[0]['cambio_unico'] == 0 && $campos4[0]['id'] == "" && $_GET['stop'] != 'true') {
//     echo '<script> location = "?opt_menu=1&opt_seccion=105&opt_subseccion=add&stop=true&mensaje=true";  </script>';
//     exit();
// }

$smarty->assign("cambio", $javascript1);

?>
<script type="text/javascript">
    var HOST_REPORTES = "http://<?php echo $_SESSION['conf_reportes_host'];?>";
    var PUERTO_REPORTES = "<?php echo $_SESSION['conf_reportes_puerto']; ?>";
    var APP_REPORTES = "<?php echo $_SESSION['conf_reportes_app']; ?>";
</script>
<?php
$layout = "index.tpl";

if (isset($_GET["layout"])) {
    if($_GET["layout"]==2){
        $layout = "index_modal.tpl";
    }
}

switch ($archivotpl) {
        case 'calculo_costos.tpl':
        case 'impresion_etiquetas.tpl':
        case 'analitico_caja.tpl':
        case 'distribucion.tpl':
        case 'estadisticas_venta.tpl':
        case 'estadisticas_anuales.tpl':
        case 'certificado_regalo.tpl':
        case 'comprobantes_pedido_pendiente.tpl':
        case 'cuentas_por_cobrar_consulta.tpl':
        case 'cuentas_por_cobrar_antiguedad.tpl':
        case 'reimpresion_comprobantes.tpl':
        case 'banco_linea_operacion.tpl':
        case 'caja_listado_recibos.tpl':
        case 'listado_transacciones.tpl':
        case 'caja_listado_transacciones.tpl':
        case 'herramientas_migrador.tpl':
        case 'seguimiento_cobranza.tpl':
        case 'tesoreria_concilar.tpl':
        case 'ingreso_distribucion_nuevo.tpl':
        case 'listado_toma_inventario.tpl':
        case 'actualizador.tpl':
        case 'reportes_stock.tpl':
        case 'novedades.tpl':
        case 'listado_fichas_kardex.tpl':
        case 'producto_nuevo.tpl':
        case 'producto_editar.tpl':
        case 'ingreso_cobranza_clientes_nueva.tpl':
        case 'ingreso_cobranza_clientes.tpl':
        case 'egresos_pago_proveedores.tpl':
        case 'calculo_costos_tekdata.tpl':
        case 'listado_toma_inventario.tpl':
        case 'carga_inventario.tpl':
        case 'impresion_carga_inventario.tpl':
        case 'actualizacion_inventario.tpl':
        case 'reimpresion_carga_inventario.tpl':
        case 'ajuste_inventario.tpl':
        case 'analitico_vendedores.tpl':
            $smarty->assign("default_version_extjs", "ext-3.4.0");
            break;
        default:
            $smarty->assign("default_version_extjs", "ext-3.1.1");
            break;
}

$smarty->assign("default_version_jquery", $default_version_jquery);

$smarty->debugging = false;
$smarty->assign("cerrar_ventana",(isset($_GET["cerrar_ventana"]))?"si":"no");
$smarty->assign("nombre_usuario",$_SESSION['admis_activo']);
$smarty->assign("titulo_pagina", $_SESSION['nombre_sistema']." :: ".$_SESSION['nombre_empresa']);


if(file_exists("../../../includes/imagenes/".$_SESSION['logo_empresa'])){
    $smarty->assign("logo_empresa", $_SESSION['logo_empresa']);
}
else{
    $smarty->assign("logo_empresa", "no_logo.png");
}
if (isset($_REQUEST["iframe"])) {
    $layout = "index_iframe.tpl";
}
$smarty->display($layout);
?>
