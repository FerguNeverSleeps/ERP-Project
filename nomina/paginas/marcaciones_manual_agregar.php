<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$termino = $_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
?>
<script>
    function ActivarNivel(chkNivel, txtNivel) {

        if (chkNivel.checked == false) {
            txtNivel.disabled = "disabled"
        } else {
            txtNivel.disabled = ""
        }
    }

    function Enviar() {

        //var fecha=new Date();
        //var ano_actual=fecha.getYear();                               

        //alert(document.frmAgregarIntegrantes.registro_id.value);          
        if (document.frmEmpresas.txtnombre.value == 0) {
            document.frmEmpresas.op_tp.value = -1
            alert("Debe ingresar un nombre valido. Verifique...");
        } else {
            document.frmEmpresas.op_tp.value = 2
        }
    }
</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">
//Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
//Script featured on JavaScript Kit (http://www.javascriptkit.com)
//For this script, visit http://www.javascriptkit.com
</script>

<?php

$op_tp = $_POST['op_tp'];

if ($op_tp == 2) {

    if (isset($_POST['chkReportoNetosNevativos'])) {
        $NetosNevativos = "1";
    } else {
        $NetosNevativos = "0";
    }
    if (isset($_POST['chkSueldosCero'])) {
        $SueldosCero = "1";
    } else {
        $SueldosCero = "0";
    }
    if (isset($_POST['chkMediaJornada'])) {
        $MediaJornada = "1";
    } else {
        $MediaJornada = "0";
    }
    if (isset($_POST['chkIncluirNuevasSit'])) {
        $NuevasSituaciones = "1";
    } else {
        $NuevasSituaciones = "0";
    }
    if (isset($_POST['chkContratos'])) {
        $Contratos = "1";
    } else {
        $Contratos = "0";
    }
    if (isset($_POST['chkValidadPorcDeduccion'])) {
        $ValidarPorcDeducc = "1";
    } else {
        $ValidarPorcDeducc = "0";
    }

    if (isset($_POST['chkNivel1'])) {
        $nivel1 = 1;
    } else {
        $nivel1 = 0;
    }
    if (isset($_POST['chkNivel2'])) {
        $nivel2 = 1;
    } else {
        $nivel2 = 0;
    }
    if (isset($_POST['chkNivel3'])) {
        $nivel3 = 1;
    } else {
        $nivel3 = 0;
    }
    if (isset($_POST['chkNivel4'])) {
        $nivel4 = 1;
    } else {
        $nivel4 = 0;
    }
    if (isset($_POST['chkNivel5'])) {
        $nivel5 = 1;
    } else {
        $nivel5 = 0;
    }
    if (isset($_POST['chkNivel6'])) {
        $nivel6 = 1;
    } else {
        $nivel6 = 0;
    }
    if (isset($_POST['chkNivel7'])) {
        $nivel7 = 1;
    } else {
        $nivel7 = 0;
    }
    $sql     = "select * from nomempresa";
    $resulta = sql_ejecutar($sql);
    $fila    = mysqli_fetch_array($resulta);
    $archivo = $_FILES['imagen_izq']['name']; #HTTP_POST_FILES

    // echo $archivo;exit;
    // echo "nombre de archivo:" . $archivo;
    //exit(0);



    if (!empty($_FILES['imagen_izq']['name'])) {
        $nombre_archivo1 = $_FILES['imagen_izq']['name'];
        $ruta_destino1 = "../imagenes/" . $nombre_archivo1;
        $ruta_destino11 = "../../includes/imagenes/" . $nombre_archivo1;

        if (move_uploaded_file($_FILES['imagen_izq']['tmp_name'], $ruta_destino1)) {
            chmod($ruta_destino1, 0777);
            if (copy($ruta_destino1, $ruta_destino11)) {
                chmod($ruta_destino11, 0777);
            } else {
                echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurrió un problema copiando el archivo 1 a la segunda ubicación</div>";
            }
        } else {
            echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurrió un problema moviendo el archivo 1 a la primera ubicación</div>";
        }
    } else {
        $nombre_archivo1 = $fila['imagen_izq'];
    }

    if (!empty($_FILES['imagen_der']['name'])) {
        $nombre_archivo2 = $_FILES['imagen_der']['name'];
        $ruta_destino2 = "../imagenes/" . $nombre_archivo2;
        $ruta_destino22 = "../../includes/imagenes/" . $nombre_archivo2;

        if (move_uploaded_file($_FILES['imagen_der']['tmp_name'], $ruta_destino2)) {
            chmod($ruta_destino2, 0777);
            if (copy($ruta_destino2, $ruta_destino22)) {
                chmod($ruta_destino22, 0777);
            } else {
                echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurrió un problema copiando el archivo 2 a la segunda ubicación</div>";
            }
        } else {
            echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurrió un problema moviendo el archivo 2 a la primera ubicación</div>";
        }
    } else {
        $nombre_archivo2 = $fila['imagen_der'];
    }

    $query = "UPDATE nomempresa set 
                nom_emp             ='" . utf8_decode($_POST[txtnombre]) . "',
                rif                 ='" . utf8_decode($_POST[txtidentificador1]) . "',
                nit                 ='" . utf8_decode($_POST[txtidentificador2]) . "',
                dir_emp             ='" . utf8_decode($_POST[txtdireccion]) . "',
                ciu_emp             ='" . utf8_decode($_POST[txtciudad]) . "',
                edo_emp             ='" . utf8_decode($_POST[txtestado]) . "',
                zon_emp             ='" . utf8_decode($_POST[txtzonapostal]) . "',
                tel_emp             ='" . utf8_decode($_POST[txttelefonos]) . "',
                pre_sid             ='" . utf8_decode($_POST[txtrepresentante]) . "',
                ger_rrhh            ='" . utf8_decode($_POST[txtencargadoRRHH]) . "',
                ced_rrhh            ='" . utf8_decode($_POST[txtcedulaRRHH]) . "',
                contralor           ='" . utf8_decode($_POST[contralor]) . "',
                ced_contralor       ='" . utf8_decode($_POST[ced_contralor]) . "',
                jefe_planilla       ='" . utf8_decode($_POST[jefe_planilla]) . "',
                ced_jefe_planilla   ='" . utf8_decode($_POST[ced_jefe_planilla]) . "',
                jefe_registro       ='" . utf8_decode($_POST[jefe_registro]) . "',
                ced_jefe_registro   ='" . utf8_decode($_POST[ced_jefe_registro]) . "',
                cargo_jefe_registro ='" . utf8_decode($_POST[cargo_jefe_registro]) . "',
                depto_registro      ='" . utf8_decode($_POST[depto_registro]) . "',
                correo_emp          ='$_POST[correo_emp]',
                correo_rrhh         ='$_POST[correo_rrhh]',
                correo_planilla     ='$_POST[correo_planilla]',
                correo_sistemas1    ='$_POST[correo_sistemas1]',
                correo_sistemas2    ='$_POST[correo_sistemas2]',
                correo_sistemas2_password    ='$_POST[correo_sistemas2_password]',
                correo_sistemas2_remitente    ='$_POST[correo_sistemas2_remitente]',
                correo_sistemas2_host    ='$_POST[correo_sistemas2_host]',
                correo_sistemas2_puerto    ='$_POST[correo_sistemas2_puerto]',
                correo_sistemas2_modo    ='$_POST[correo_sistemas2_modo]',
                nivel1              ='" . utf8_decode($nivel1) . "',
                nivel2              ='" . utf8_decode($nivel2) . "',
                nivel3              ='" . utf8_decode($nivel3) . "',
                nivel4              ='" . utf8_decode($nivel4) . "',
                nivel5              ='" . utf8_decode($nivel5) . "',
                nivel6              ='" . utf8_decode($nivel6) . "',
                nivel7              ='" . utf8_decode($nivel7) . "',               
                nomniv1             ='$_POST[txtnivel1]',
                nomniv2             ='$_POST[txtnivel2]',
                ced_presid           ='$_POST[ced_presid]',
                nomniv3             ='$_POST[txtnivel3]',
                nomniv4             ='$_POST[txtnivel4]',
                nomniv5             ='$_POST[txtnivel5]',
                nomniv6             ='$_POST[txtnivel6]',
                nomniv7             ='$_POST[txtnivel7]',
                imagen_izq          ='$nombre_archivo1',
                imagen_der          ='$nombre_archivo2',
                monsalmin           ='$_POST[salariominimo]',
                moneda              ='" . $_POST["moneda"] . "',
                moneda_nombre       ='" . $_POST["moneda_nombre"] . "',
                recibonom           ='$_POST[recibonom]',
                conficha            ='$_POST[conficha]',
                actividad_economica ='$_POST[actividad_economica]',
                cedula_juridica     ='$_POST[cedula_juridica]',
                cedula_natural      ='$_POST[cedula_natural]',
                representante_licencia ='$_POST[representante_licencia]',
                representante_telef ='$_POST[representante_telef]',
                numero_patronal     ='$_POST[numero_patronal]',
                tipo_empresa        ='$_POST[opttipo_empresa]',
                cant_empleados      ='$_POST[cant_empleados]',
                pais                ='$_POST[pais]',
		correo_adicional1    ='$_POST[correo_adicional1]',
		correo_adicional2    ='$_POST[correo_adicional2]',
		correo_adicional3    ='$_POST[correo_adicional3]',	
		correo_adicional4    ='$_POST[correo_adicional4]',
                consecutivo_reporte_incidencia='$_POST[consecutivo_reporte_incidencia]'";



    $result = sql_ejecutar($query);

    echo '<font color="#FF0000"><strong> Se actualizo correctamente el registro. </strong></font>';
}

$query               = "select * from nomempresa";
$result              = sql_ejecutar($query);
$row                 = mysqli_fetch_array($result);
$nompre_empresa      = utf8_encode($row["nom_emp"]);
$codigo_empresa      = utf8_encode($row["cod_emp"]);
$direccion           = utf8_encode($row["dir_emp"]);
$ciudad              = utf8_encode($row["ciu_emp"]);
$estado              = utf8_encode($row["edo_emp"]);
$zona_postal         = utf8_encode($row["zon_emp"]);
$telefono            = utf8_encode($row["tel_emp"]);
$rif                 = utf8_encode($row["rif"]);
$nit                 = utf8_encode($row["nit"]);
$edad_guarderia      = $row[edadmax];
$representante       = utf8_encode($row["pre_sid"]);
$ced_presid          = utf8_encode($row["ced_presid"]);
$encargadoRRHH       = utf8_encode($row["ger_rrhh"]);
$cedulaRRHH          = utf8_encode($row[ced_rrhh]);
$contralor           = utf8_encode($row[contralor]);
$ced_contralor       = utf8_encode($row[ced_contralor]);
$jefe_planilla       = utf8_encode($row[jefe_planilla]);
$ced_jefe_planilla   = utf8_encode($row[ced_jefe_planilla]);
$jefe_registro       = utf8_encode($row[jefe_registro]);
$ced_jefe_registro   = utf8_encode($row[ced_jefe_registro]);
$cargo_jefe_registro = utf8_encode($row[cargo_jefe_registro]);
$depto_registro      = utf8_encode($row[depto_registro]);
$correo_emp          = utf8_encode($row[correo_emp]);
$contratos           = $row[contratos];
$serial              = $row[serial];
$nosueldocero        = $row[nosueldocero];
$netonegativo        = $row[netoneg];
$MediaJornada        = $row[mediajornada];
$NuevasSituaciones   = $row[nuevassituaciones];
$TipoFicha           = $row[tipoficha];
$for_recibo_vac      = $row[recibovac];
$for_recibo_liq      = $row[reciboliq];
$for_recibo_pago     = $row[recibopago];
$por_diff            = $row[porcdiff];
$validar_porc_deducc = $row[reportdiff];
$monsalmin           = $row[monsalmin];
$recibonom           = $row[recibonom];
$material            = $row[cod_material];
$unidad              = $row[unidad];
$ccosto              = $row[ccosto];
$proveedor           = $row[proveedor];
$moneda              = $row[moneda];
$moneda_nombre       = $row[moneda_nombre];
$conficha            = $row[conficha];
$actividad_economica = $row[actividad_economica];
$cedula_juridica     = $row[cedula_juridica];
$cedula_natural      = $row[cedula_natural];
$representante_licencia = $row[representante_licencia];
$representante_telef = $row[representante_telef];
$numero_patronal     = $row[numero_patronal];
$correo_rrhh         = $row[correo_rrhh];
$correo_planilla     = $row[correo_planilla];
$correo_sistemas1    = $row[correo_sistemas1];
$correo_sistemas2    = $row[correo_sistemas2];
$correo_sistemas2_password = $row[correo_sistemas2_password];
$correo_sistemas2_remitente = $row[correo_sistemas2_remitente];
$correo_sistemas2_host = $row[correo_sistemas2_host];
$correo_sistemas2_puerto = $row[correo_sistemas2_puerto];
$correo_sistemas2_modo = $row[correo_sistemas2_modo];
$cant_empleados      = $row[cant_empleados];
$correo_adicional1      = $row[correo_adicional1];
$correo_adicional2      = $row[correo_adicional2];
$correo_adicional3      = $row[correo_adicional3];
$correo_adicional4      = $row[correo_adicional4];
$consecutivo_reporte_incidencia = $row["consecutivo_reporte_incidencia"];

$imagen_derecha     = "../../includes/imagenes/" . $row[imagen_der];

$imagen_izquierda   = "../../includes/imagenes/" . $row[imagen_izq];
$tipo_empresa        = $row[tipo_empresa];
$pais        = $row[pais];
$nivel1 = $row[nivel1];
$nomnivel1 = $row[nomniv1];
$nivel2 = $row[nivel2];
$nomnivel2 = $row[nomniv2];
$nivel3 = $row[nivel3];
$nomnivel3 = $row[nomniv3];
$nivel4 = $row[nivel4];
$nomnivel4 = $row[nomniv4];
$nivel5 = $row[nivel5];
$nomnivel5 = $row[nomniv5];
$nivel6 = $row[nivel6];
$nomnivel6 = $row[nomniv6];
$nivel7 = $row[nivel7];
$nomnivel7 = $row[nomniv7];
//msgbox($for_recibo_liq);

$sql_pais = "SELECT *
        FROM pais
        WHERE id='" . $pais . "'";
$result_pais         = sql_ejecutar($sql_pais);
$row_pais            = mysqli_fetch_array($result_pais);
$iso_pais = $row_pais[iso];
$nombre_pais = $row_pais[nombre];
$moneda_simbolo = $row_pais[moneda_simbolo];
$moneda_nombre = $row_pais[moneda_nombre];
$gentilicio = $row_pais[gentilicio];
$identificacion_tributaria = $row_pais[identificacion_tributaria];
$identificacion_personal = $row_pais[identificacion_personal];
?>

<!-- BEGIN GLOBAL MANDATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />
<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css" />
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css" />
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/css/components.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .tile-icon {
        color: white;
        line-height: 125px;
        font-size: 80px;
    }
</style>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css" />
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed page-full-width" marginheight="0">
    <meta lang="es">
    <meta charset="utf-8">

    <div class="page-container">
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <!-- BEGIN PAGE CONTENT-->
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <h4>Marcación</h4>
                            </div>
                            <div class="portlet-body">
                                <form action="" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <label>Ficha:</label>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <input class="form-control" name="txtFicha" type="text" id="txtFicha" placeholder="Ingrese el número de ficha">
                                            </div>
                                        </div>
                                        <BR>
                                        <div class="row">
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <label>Tipo marcación:</label>
                                            </div>
                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                <select name="tipo_marcacion" id="tipo_marcacion" class="form-control select2">
                                                    <option value="1">Entrada</option>
                                                    <option value="2">Salida Alm.</option>
                                                    <option value="3">Entrada Alm.</option>
                                                    <option value="4">Salida</option>
                                                </select>
                                            </div>
                                        </div>
                                        <BR>
                                        <div class="row">
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <label>Hora:</label>
                                            </div>
                                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                                <input class="form-control" name="txtHora" type="time" id="txtHora" placeholder="Ingrese una hora">
                                            </div>
                                        </div>
                                        <br>
                                        
                                        <div class="row">
                                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <?php boton_metronic('ok', 'Enviar(); document.frmEmpresas.submit();', 2) ?>
                                            </div>

                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <?php boton_metronic('cancel', 'history.back();', 2) ?>
                                            </div>
                                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            </div>
                                        </div>


                                    </div>
                                    <!-- END PORTLET BODY-->
                                </form>
                            </div>
                            <!-- END EXAMPLE TABLE PORTLET-->
                        </div>
                    </div>
                </div>
                <!-- END PAGE CONTENT-->
            </div>
        </div>
        <!-- END CONTENT -->
    </div>



    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
    <script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#pais').select2();
        });
    </script>
    <p>&nbsp;</p>
    </form>
    <p>&nbsp;</p>
</body>

</html>