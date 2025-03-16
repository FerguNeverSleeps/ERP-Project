<?php session_start();

// error_reporting(E_ALL);
// ini_set("display_errors", 1);

require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect(DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd']) or die('No Hay Conexión con el Servidor de Mysql-');
//         mysqli_query($conexion, 'SET CHARACTER SET utf8');
if ((!isset($_SESSION['usuario_rhexpress'])) && (basename($_SERVER['PHP_SELF']) != basename("rhexpress_login.php"))) {
    header("location:rhexpress_login.php");
}
//--------------------------------------------
//--------------------------------------------
//Cargar foto de funcionario
$url_base = "../nomina/paginas/";
if (file_exists($url_base . $_SESSION['foto_rhexpress'])) {
    $foto = $url_base . $_SESSION['foto_rhexpress'];
} else {
    $foto = "assets/pages/img/login/silueta.gif";
}
$cedula      = $_SESSION['cedula_rhexpress'];
$sql_jefe    = "SELECT IdJefe,uid_subjefe,IdDepartamento FROM departamento where IdJefe = '{$cedula}' OR uid_subjefe = '{$cedula}'";
$esjefe      = $conexion->query($sql_jefe)->num_rows;

// Buscamos la persona logeada por cedula
$sql_colaborador = "SELECT * FROM nompersonal WHERE cedula='" . $cedula . "'";
$res_colaborador = $conexion->query($sql_colaborador);
$colaborador = $res_colaborador->fetch_object();

// Datos de la empresa
$sql_empresa = "SELECT e.nivel1, e.nomniv1, e.nivel2, e.nomniv2, e.nivel3, e.nomniv3, e.nivel4, e.nomniv4, e.nivel5, e.nomniv5, e.nivel6, e.nomniv6, e.nivel7, e.nomniv7, e.tipo_empresa, e.pais
		FROM nomempresa e";
$res_empresa = $conexion->query($sql_empresa);
$empresa = $res_empresa->fetch_object();
$pais = $empresa->pais;

// En caso de que tenga nacionalidad, aplicar el gentilicio
$sql_pais = "SELECT *
        FROM pais
        WHERE id='" . $pais . "'";
$res_pais = $conexion->query($sql_pais);
$pais = $res_pais->fetch_object();
$gentilicio = $pais->gentilicio;
// Seteamos algunos datos, en este caso fecha de cumpleaños
$fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $colaborador->fecnac);

// Fecha actual
$fecha_actual = date("Y-m-d");

// Calcular la edad
$edad = $fecha_nacimiento->diff(new DateTime($fecha_actual))->y;

// Seteamos algunos datos, en este caso fecha de cumpleaños
$fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $colaborador->fecnac);
$fecha_nacimiento = ($fecha_nacimiento !== false) ? $fecha_nacimiento->format('d-m-Y') : '';

// Antiguedad del empleador
// Seteamos algunos datos, en este caso fecha de cumpleaños
$fecha_ingreso = DateTime::createFromFormat('Y-m-d', $colaborador->fecing);

// Fecha actual
$fecha_actual = new DateTime();

// Calcular la diferencia
$interval = $fecha_actual->diff($fecha_ingreso);

// Formatear la diferencia
$antiguedad = "";
if ($interval->y > 0) {
    $antiguedad .= $interval->y . " años";
}
if ($interval->m > 0) {
    if ($antiguedad != "") {
        $antiguedad .= " ";
    }
    $antiguedad .= $interval->m . " meses";
}
if ($interval->d > 0) {
    if ($antiguedad != "") {
        $antiguedad .= " ";
    }
    $antiguedad .= $interval->d . " días";
}

// Fecha de ingreso
$fecha_ingreso = DateTime::createFromFormat('Y-m-d', $colaborador->fecing);
$fecha_ingreso = ($fecha_ingreso !== false) ? $fecha_ingreso->format('d-m-Y') : '';

// Mostramos la nacionalidad del individuo
switch ($colaborador->nacionalidad) {
    case 1:
        $nacionalidad = utf8_encode($gentilicio);
        break;
    case 2:
        $nacionalidad = 'Extranjero';
        break;
    case 3:
        $nacionalidad = 'Nacionalizado';
        break;

    default:
        $nacionalidad = 'N/A';
        break;
}

$banco_colaborador = '';

if ($colaborador->codbancob) {
    // Datos del banco
    $sql_bancos = "SELECT * FROM nombancos WHERE `cod_ban` = $colaborador->codbancob";
    $res_bancos = $conexion->query($sql_bancos)->fetch_assoc();
    $banco_colaborador = $res_bancos['des_ban'];
}

?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->

<head>
    <?php include("dep_css.php"); ?>
    <style type="text/css" media="screen">
        .page-header.navbar .page-logo .logo-default {
            margin: 14px 10px 0 !important;
        }
    </style>
</head>
<!-- END HEAD -->

<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-md">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="index.php">
                    <img src="logo_cliente.jpg" alt="logo" class="logo-default" width="180" /> </a>
                <div class="menu-toggler sidebar-toggler">
                    <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
                </div>
            </div>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN PAGE TOP -->
            <div class="page-top">
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-user dropdown-dark">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <span class="username username-hide-on-mobile"><?php echo (isset($_SESSION['nombre_rhexpress'])) ? $_SESSION['nombre_rhexpress'] : "Usuario"; ?></span>
                                <!-- DOC: Do not remove below empty space(&nbsp;) as its purposely used -->
                                <img alt="" class="img-circle" src="<?php echo $foto; ?>" /> </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="#" onClick="javascript:Abrir_Ventana3()">
                                        <i class="icon-user"></i>Cambiar clave </a>
                                </li>
                                <li>
                                    <a href="proceso/logout.php">
                                        <i class="icon-key"></i> Salir </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- BEGIN SIDEBAR -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
            <div class="page-sidebar navbar-collapse collapse">
                <!-- BEGIN SIDEBAR MENU -->
                <ul class="page-sidebar-menu   " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
                    <li class="nav-item start ">
                        <a href="rhexpress_menu.php" class="nav-link nav-toggle">
                            <i class="icon-home"></i>
                            <span class="title">Inicio</span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a href="#" class="nav-link nav-toggle">
                            <i class="fa fa-plane" aria-hidden="true"></i>
                            <span class="title">Casos</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="#" class="nav-link ">
                                    <span class="title">Nuevo Caso</span>
                                    <span class="arrow"></span>

                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item">
                                        <!--<a href="http://iform.bprosys.net/view.php?id=14056" target="NEW" class="nav-link ">
                                                <span class="title">Solicitud Vacaciones</span>
                                            </a>-->
                                        <a href="solicitud_vacaciones.php" class="nav-link ">
                                            <span class="title">Solicitud Vacaciones</span>
                                        </a>
                                    </li>
                                    <!--   <li class="nav-item">
                                          <a href="solicitud_tiempo_compensatorio.php" class="nav-link ">
                                                <span class="title">Solicitud Tiempo Compensatorio</span>
                                            </a>
                                        </li>-->
                                    <!-- <li class="nav-item">
                                            <a href="solicitud_horas_extraordinarias.php" class="nav-link ">
                                                <span class="title">Solicitud Horas Extraordinarias</span>
                                            </a>
                                        </li> -->
                                    <li class="nav-item">
                                        <a href="solicitud_permisos.php" class="nav-link ">
                                            <span class="title">Solicitud Permisos</span>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="solicitud_actualizacion_anual_bienes.php?tipo=7" class="nav-link ">
                                            <span class="title">Solicitud actualizacion anual-bienes</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="solicitud_prestamos_compras_creditos.php?tipo=6" class="nav-link ">
                                            <span class="title">Solicitud de prestamos, compras y creditos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="solicitud_reclamo_pago.php?tipo=5" class="nav-link ">
                                            <span class="title">Solicitud reclamo de pago</span>
                                        </a>
                                    </li>
                                    <!-- <li class="nav-item">
                                            <a href="solicitud_permisos.php" class="nav-link ">
                                                <span class="title">Solicitud Permisos</span>
                                            </a>
                                        </li> -->

                                    <!-- <li class="nav-item">
                                            <a href="solicitud_actualizacion_anual_bienes.php?tipo=7" class="nav-link ">
                                                <span class="title">Solicitud actualizacion anual-bienes</span>
                                            </a>
                                        </li>
                                         <li class="nav-item">
                                            <a href="solicitud_prestamos_compras_creditos.php?tipo=6" class="nav-link ">
                                                <span class="title">Solicitud de prestamos, compras y creditos</span>
                                            </a>
                                        </li>
                                      <li class="nav-item">
                                            <a href="solicitud_reclamo_pago.php?tipo=5" class="nav-link ">
                                                <span class="title">Solicitud reclamo de pago</span>
                                            </a>
                                        </li> -->
                                    <!-- <li class="nav-item">
                                            <a href="solicitud_carta_trabajo.php" class="nav-link ">
                                                <span class="title">Solicitud Carta de Trabajo</span>
                                            </a>
                                        </li> -->
                                </ul>
                            </li>
                            <?php
                            if ($esjefe) {
                            ?>
                                <li class="nav-item">
                                    <a href="solicitud_busqueda.php" class="nav-link ">
                                        <span class="title">Reporte solicitudes</span>
                                    </a>
                                </li>
                            <?php
                            }
                            ?>
                            <li class="nav-item">
                                <a href="rhexpress_bandeja_entrada.php" class="nav-link ">
                                    <i class="font green fa fa-circle" style="color:orange" aria-hidden="true"></i>
                                    <span class="title">Bandeja de Entrada</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="rhexpress_bandeja_aprobados.php" class="nav-link ">
                                    <i class="font green fa fa-circle" style="color:green" aria-hidden="true"></i>
                                    <span class="title">Bandeja de Aprobados</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="rhexpress_bandeja_rechazados.php" class="nav-link ">
                                    <i class="font green fa fa-circle" style="color:red" aria-hidden="true"></i>
                                    <span class="title">Bandeja de Rechazados</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item  ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-briefcase"></i>
                            <span class="title">Tiempos</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="rhexpress_tiempos.php" class="nav-link ">
                                    <span class="title">Resumen</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item  ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="icon-docs"></i>
                            <span class="title">Documentos</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="reportes/pdf/constancia_pdf.php?ficha_rhexpress=<?php echo $_SESSION['ficha_rhexpress'] ?>" class="nav-link" id="myButton">
                                    <span class="title">Carta Trabajo</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="recibo_pago.php?action=7" class="nav-link ">
                                    <span class="title">Comprobante de pago</span>
                                </a>
                            </li>
                        </ul>

                    </li>
                    <li class="nav-item">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-clock-o"></i>
                            <span class="title">Marcaciones</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item">
                                <a href="rhexpress_marcaciones.php" class="nav-link ">
                                    <span class="title">Resumen</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="rhexpress_reloj_detalle.php" class="nav-link ">
                                    <span class="title">Marcaciones Reloj</span>
                                </a>
                            </li>
                            <!-- <li class="nav-item">
                                    <a href="rhexpress_reloj_detalle_manual.php" class="nav-link ">
                                        <span class="title">Marcaciones Manuales</span>
                                    </a>
                                </li> -->
                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="rhexpress_vacaciones_acumuladas.php" class="nav-link nav-toggle">
                            <i class="fa fa-plane" aria-hidden="true"></i>
                            <span class="title">Vacaciones</span>
                        </a>
                    </li>

                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">
                <!-- BEGIN PAGE HEAD-->
                <div class="page-head">
                    <!-- BEGIN PAGE TITLE -->
                    <div class="page-title">
                        <table class="table table-bordered">
                            <caption>
                                <strong>
                                    <h4>Colaborador #<?= $colaborador->ficha ?></h4>
                                </strong>
                            </caption>
                            <tr>
                                <td class="col-xs-4 col-sm-4 col-md-4 col-lg-2"><strong>Nombre</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Cedula</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Nacionalidad</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Sexo</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="apenom" value="<?= $_SESSION['nombre_rhexpress'] ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="cedula" value="<?= $_SESSION['cedula_rhexpress'] ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="nacionalidad" value="<?= $nacionalidad ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="sexo" value="<?= $colaborador->sexo ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Estado Civil</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Fecha de Nacimiento</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Edad</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Lugar de Nacimiento</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="estado_civil" value="<?= $colaborador->estado_civil ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="fecha_nacimiento" value="<?= utf8_encode($fecha_nacimiento) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="edad" id="edad" value="<?= $edad ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="lugar_nacimiento" value="<?= utf8_encode($colaborador->lugarnac) ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Fecha de Ingreso</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Antiguedad</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Salario</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Dirección</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="fecha_ingreso" value="<?= $fecha_ingreso ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="antiguedad" value="<?= $antiguedad ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="salario" value="<?= utf8_encode($colaborador->sueldopro) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="direccion" value="<?= utf8_encode($colaborador->direccion) ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Teléfonos</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>E-mail Sugerido</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Situación</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Bancos</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="telefonos" value="<?= utf8_encode($colaborador->telefonos) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="email" value="<?= utf8_encode($colaborador->email) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="estado" value="<?= utf8_encode($colaborador->estado) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="banco_colaborador" value="<?= utf8_encode($banco_colaborador) ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Cuenta</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Seguro Social</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Código Seguro Social Sipe</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2"><strong>Cargo</strong></td>
                            </tr>
                            <tr>
                                <td>
                                    <input name="cuentacob" value="<?= utf8_encode($colaborador->cuentacob) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="seguro_social" value="<?= utf8_encode($colaborador->seguro_social) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="segurosocial_sipe" value="<?= utf8_encode($colaborador->segurosocial_sipe) ?>" type="text" class="form-control" readonly>
                                </td>
                                <td>
                                    <input name="cargo" value="<?= $_SESSION['cargo_rhexpress'] ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                            <tr>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" colspan="2"><strong>Nivel Organizacional</strong></td>
                                <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2" colspan="2"><strong>Departamento Workflow</strong></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input name="gerencia" value="<?= $_SESSION['gerencia_rhexpress'] ?>" type="text" class="form-control" readonly>
                                </td>
                                <td colspan="2">
                                    <input name="dpto" value="<?= $_SESSION['_Departamento'] ?>" type="text" class="form-control" readonly>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!-- END PAGE TITLE -->
                    <!-- BEGIN PAGE TOOLBAR -->
                    <div class="page-toolbar">
                        <!-- BEGIN THEME PANEL -->
                        <div class="btn-group btn-theme-panel">
                            <a href="javascript:;" class="btn dropdown-toggle" data-toggle="dropdown">
                                <i class="icon-settings"></i>
                            </a>
                            <div class="dropdown-menu theme-panel pull-right dropdown-custom hold-on-click">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-xs-12">
                                        <h3>CABECERA</h3>
                                        <ul class="theme-colors">
                                            <li class="theme-color theme-color-default active" data-theme="default">
                                                <span class="theme-color-view"></span>
                                                <span class="theme-color-name">Cabecera Oscura</span>
                                            </li>
                                            <li class="theme-color theme-color-light " data-theme="light">
                                                <span class="theme-color-view"></span>
                                                <span class="theme-color-name">Cabecera Clara</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-8 col-sm-8 col-xs-12 seperator">
                                        <h3>DISEÑO</h3>
                                        <ul class="theme-settings">
                                            <li> Diseño
                                                <select class="layout-option form-control input-small input-sm">
                                                    <option value="fluid" selected="selected">Fluido</option>
                                                    <option value="boxed">En caja</option>
                                                </select>
                                            </li>
                                            <li> Cabecera
                                                <select class="page-header-option form-control input-small input-sm">
                                                    <option value="fixed" selected="selected">Fija</option>
                                                    <option value="default">Por Defecto</option>
                                                </select>
                                            </li>
                                            <!--
                                                <li> Top Dropdowns
                                                    <select class="page-header-top-dropdown-style-option form-control input-small input-sm">
                                                        <option value="light">Light</option>
                                                        <option value="dark" selected="selected">Dark</option>
                                                    </select>
                                                </li>
                                                <li> Sidebar Mode
                                                    <select class="sidebar-option form-control input-small input-sm">
                                                        <option value="fixed">Fixed</option>
                                                        <option value="default" selected="selected">Default</option>
                                                    </select>
                                                </li>
                                                -->
                                            <li> Menu lateral
                                                <select class="sidebar-menu-option form-control input-small input-sm">
                                                    <option value="accordion" selected="selected">Acordeón</option>
                                                    <option value="hover">Flotar</option>
                                                </select>
                                            </li>
                                            <li> Posición de barra lateral
                                                <select class="sidebar-pos-option form-control input-small input-sm">
                                                    <option value="left" selected="selected">Izquierda</option>
                                                    <option value="right">Derecha</option>
                                                </select>
                                            </li>
                                            <li> Pie de pagina
                                                <select class="page-footer-option form-control input-small input-sm">
                                                    <option value="fixed" selected="selected">Fijo</option>
                                                    <option value="default">Por Defecto</option>
                                                </select>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END THEME PANEL -->
                    </div>
                    <!-- END PAGE TOOLBAR -->
                </div>
                <!-- END PAGE HEAD-->
                <br>
                <script type="text/javascript">
                    document.getElementById("myButton").onclick = function() {

                        location.href = '../reportes/word/carta_trabajo_administrativo.php?ficha_rhexpress=' + $_SESSION['ficha_rhexpress'];
                    };
                </script>
                <script>
                    // Calculos
                    function calcular_edad(fecha) {
                        // Obtengo la fecha actual
                        hoy = new Date()

                        // Empleo la fecha que recibo como parametro
                        // La descompongo en un array
                        var array_fecha = fecha.split("-")
                        //si el array no tiene tres partes, la fecha es incorrecta
                        if (array_fecha.length != 3)
                            return "La fecha introducida es incorrecta"
                        //compruebo que los ano, mes, dia son correctos
                        var ano
                        ano = parseInt(array_fecha[2]);
                        if (isNaN(ano))
                            return "El año es incorrecto"
                        var mes
                        mes = parseInt(array_fecha[1]);
                        if (isNaN(mes))
                            return "El mes es incorrecto"
                        var dia
                        dia = parseInt(array_fecha[0]);
                        if (isNaN(dia))
                            return "El dia introducido es incorrecto"
                        //si el año de la fecha que recibo solo tiene 2 cifras hay que cambiarlo a 4
                        if (ano <= 99) {
                            ano += 1900
                        }
                        //resto los años de las dos fechas
                        edad = hoy.getFullYear() - ano - 1; //-1 porque no se si ha cumplido años ya este año

                        //si resto los meses y me da menor que 0 entonces no ha cumplido años. Si da mayor si ha cumplido
                        if (hoy.getMonth() + 1 - mes < 0) //+ 1 porque los meses empiezan en 0
                            return edad
                        if (hoy.getMonth() + 1 - mes > 0)
                            return edad + 1

                        //entonces es que eran iguales. miro los dias
                        //si resto los dias y me da menor que 0 entonces no ha cumplido años. Si da mayor o igual si ha cumplido
                        if (hoy.getUTCDate() - dia >= 0)
                            return edad + 1

                        return edad
                    }

                    function AbrirVentana(Ventana, Largo, Alto, Modal) {
                        if (Modal == 1) {
                            mainWindow = showModalDialog(Ventana, 'mainWindow', 'dialogWidth:' + Alto + 'px;dialogHeight:' + Largo + 'px;resizable:yes;toolbar:no;menubar:no;scrollbars:yes;help: no');
                        } else {

                            mainWindow = window.open(Ventana, 'mainWindow', 'menub ar=no,resizable=no,width=' + Alto + ',height=' + Largo + ',left=0,top=0,titlebar=yes,alwaysraised=yes,status=no,scrollbars=yes');
                        }


                    }
                    // jQuery(document).ready(function() {    
                    //    App.init();
                    // });
                    // function Abrir_Ventana2(){
                    //     AbrirVentana('licencia.php',500,600,0);
                    // }
                    function Abrir_Ventana3() {
                        AbrirVentana('../rhexpress/config/cambiar_clave.php', 400, 420, 0);
                    }
                    // function recarga(){
                    //     document.location.href = "frame.php?menu=inicio";
                    // }
                </script>