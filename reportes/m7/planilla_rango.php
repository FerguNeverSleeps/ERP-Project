<?php 
    if (!isset($_SESSION)) {
        session_start();
        ob_start();
    }
    require_once('../../nomina/lib/database.php');

    $db = new Database($_SESSION['bd']);
    if(isset($_POST['anio']))
        $anio = $_POST['anio'];
    else
        $anio = date('Y');

    $sql = "select `a`.`codcon`,a.descrip,
    SUM(IF(month(b.periodo_fin)=01,a.monto,0)) As 'ENERO',
    SUM(IF(month(b.periodo_fin)=02,a.monto,0)) As 'FEBRERO',
    SUM(IF(month(b.periodo_fin)=03,a.monto,0)) As 'MARZO',
    SUM(IF(month(b.periodo_fin)=04,a.monto,0)) As 'ABRIL',
    SUM(IF(month(b.periodo_fin)=05,a.monto,0)) As 'MAYO',
    SUM(IF(month(b.periodo_fin)=06,a.monto,0)) As 'JUNIO',
    SUM(IF(month(b.periodo_fin)=07,a.monto,0)) As 'JULIO',
    SUM(IF(month(b.periodo_fin)=08,a.monto,0)) As 'AGOSTO',
    SUM(IF(month(b.periodo_fin)=09,a.monto,0)) As 'SEPTIEMBRE',
    SUM(IF(month(b.periodo_fin)=10,a.monto,0)) As 'OCTUBRE',
    SUM(IF(month(b.periodo_fin)=11,a.monto,0)) As 'NOVIEMBRE',
    SUM(IF(month(b.periodo_fin)=12,a.monto,0)) As 'DICIEMBRE'
    from nom_movimientos_nomina a 
    left join `nom_nominas_pago` `b` on `b`.`codnom` = `a`.`codnom`
    where year(b.periodo_fin)=$anio group by `a`.`codcon`";
    
    $res = $db->query($sql);

    $anios = $db->query("select distinct anio from nom_movimientos_nomina where anio > 2000");

?>
<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 9 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="en" >
    <!--begin::Head-->
    <head><base href="">
        <meta charset="utf-8"/>
        <title>Metronic | Dashboard</title>
        <meta name="description" content="Updates and statistics"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

        <!--begin::Fonts-->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>        <!--end::Fonts-->

        <!--begin::Page Vendors Styles(used by this page)-->
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <!--end::Page Vendors Styles-->


        <!--begin::Global Theme Styles(used by all pages)-->
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/global/plugins.bundle.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/css/style.bundle.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <!--end::Global Theme Styles-->

        <!--begin::Layout Themes(used by all pages)-->

        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/css/themes/layout/header/base/light.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/css/themes/layout/header/menu/light.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/css/themes/layout/brand/dark.css?v=7.0.6" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/css/themes/layout/aside/dark.css?v=7.0.6" rel="stylesheet" type="text/css"/>        <!--end::Layout Themes-->

        <link rel="shortcut icon" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/media/logos/favicon.ico"/>

    </head>
    <!--end::Head-->

    <!--begin::Body-->
    <body  id="kt_body"  class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"  >
        
        <!-- CUERPO TABLA -->
        <!--begin::Card-->
        <div class="card card-custom gutter-b">
            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                <div class="card-title">
                    <h3 class="card-label">
                        Acumulados Anuales
                        <span class="d-block text-muted pt-2 font-size-sm">pagos de conceptos por mes</span>
                    </h3>
                </div>
                <div class="card-toolbar">
                    <!--begin::Dropdown-->
                    <div class="dropdown dropdown-inline mr-2">
                        <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="svg-icon svg-icon-md">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"/>
                                    <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3"/>
                                    <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000"/>
                                </g>
                                </svg>
                            </span>		Export
                        </button>

                        <!--begin::Dropdown Menu-->
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            <!--begin::Navigation-->
                            <ul class="navi flex-column navi-hover py-2">
                                <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">
                                    Seleccione un formato:
                                </li>
                                <li class="navi-item">
                                    <a href="../excel/planilla_m7.php" target="_blank" class="navi-link">
                                        <span class="navi-icon"><i class="la la-file-excel-o"></i></span>
                                        <span class="navi-text">Excel</span>
                                    </a>
                                </li>
                            </ul>
                            <!--end::Navigation-->
                        </div>
                        <!--end::Dropdown Menu-->
                    </div>
                    <!--end::Dropdown-->

                    <!--begin::Button-->
                    <a href="#" class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#kt_datepicker_modal">
                        <span class="svg-icon svg-icon-md"><!--begin::Svg Icon | path:assets/media/svg/icons/Design/Flatten.svg--><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24"/>
                            <circle fill="#000000" cx="9" cy="15" r="6"/>
                            <path d="M8.8012943,7.00241953 C9.83837775,5.20768121 11.7781543,4 14,4 C17.3137085,4 20,6.6862915 20,10 C20,12.2218457 18.7923188,14.1616223 16.9975805,15.1987057 C16.9991904,15.1326658 17,15.0664274 17,15 C17,10.581722 13.418278,7 9,7 C8.93357256,7 8.86733422,7.00080962 8.8012943,7.00241953 Z" fill="#000000" opacity="0.3"/>
                        </g>
                    </svg><!--end::Svg Icon--></span>	Filtro
                    </a>
                    <!--end::Button-->
                </div>
            </div>

            <div class="card-body">
                <!--begin: Datatable-->
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Enero</th>
                            <th>Febrero</th>
                            <th>Marzo</th>
                            <th>Abril</th>
                            <th>Mayo</th>
                            <th>Junio</th>
                            <th>Julio</th>
                            <th>Agosto</th>
                            <th>Septiembre</th>
                            <th>Octubre</th>
                            <th>Noviembre</th>
                            <th>Diciembre</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($concepto = mysqli_fetch_array($res)) { ?>
                        <tr>
                            <td><?= $concepto['codcon'] ?></td>
                            <td><?= $concepto['descrip'] ?></td>
                            <td><?= $concepto['ENERO'] ?></td>
                            <td><?= $concepto['FEBRERO'] ?></td>
                            <td><?= $concepto['MARZO'] ?></td>
                            <td><?= $concepto['ABRIL'] ?></td>
                            <td><?= $concepto['MAYO'] ?></td>
                            <td><?= $concepto['JUNIO'] ?></td>
                            <td><?= $concepto['JULIO'] ?></td>
                            <td><?= $concepto['AGOSTO'] ?></td>
                            <td><?= $concepto['SEPTIEMBRE'] ?></td>
                            <td><?= $concepto['OCTUBRE'] ?></td>
                            <td><?= $concepto['NOVIEMBRE'] ?></td>
                            <td nowrap><?= $concepto['DICIEMBRE'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>

                </table>
                <!--end: Datatable-->
            </div>
        </div>
        <!--end::Card-->
        
        <!--begin::Modal-->
        <div class="modal fade" id="kt_datepicker_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Seleccione el año a consultar</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form class="form" method="post" action="planilla_rango.php">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-form-label text-right col-lg-3 col-sm-12">Seleccione el año</label>
                                <div class="col-lg-4 col-md-9 col-sm-12">
                                    <select class="form-control selectpicker" name="anio">
                                        <?php while ($anio = mysqli_fetch_array($anios)) { ?>
                                        <option><?= $anio['anio'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary mr-2" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-secondary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

        <!-- FIN CUERPO TABLA-->
        
    

        <script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
        <!--begin::Global Config(global config for global JS scripts)-->
        <script>
            var KTAppSettings = {
                "breakpoints": {
                    "sm": 576,
                    "md": 768,
                    "lg": 992,
                    "xl": 1200,
                    "xxl": 1400
                },
                "colors": {
                    "theme": {
                        "base": {
                            "white": "#ffffff",
                            "primary": "#3699FF",
                            "secondary": "#E5EAEE",
                            "success": "#1BC5BD",
                            "info": "#8950FC",
                            "warning": "#FFA800",
                            "danger": "#F64E60",
                            "light": "#E4E6EF",
                            "dark": "#181C32"
                        },
                        "light": {
                            "white": "#ffffff",
                            "primary": "#E1F0FF",
                            "secondary": "#EBEDF3",
                            "success": "#C9F7F5",
                            "info": "#EEE5FF",
                            "warning": "#FFF4DE",
                            "danger": "#FFE2E5",
                            "light": "#F3F6F9",
                            "dark": "#D6D6E0"
                        },
                        "inverse": {
                            "white": "#ffffff",
                            "primary": "#ffffff",
                            "secondary": "#3F4254",
                            "success": "#ffffff",
                            "info": "#ffffff",
                            "warning": "#ffffff",
                            "danger": "#ffffff",
                            "light": "#464E5F",
                            "dark": "#ffffff"
                        }
                    },
                    "gray": {
                        "gray-100": "#F3F6F9",
                        "gray-200": "#EBEDF3",
                        "gray-300": "#E4E6EF",
                        "gray-400": "#D1D3E0",
                        "gray-500": "#B5B5C3",
                        "gray-600": "#7E8299",
                        "gray-700": "#5E6278",
                        "gray-800": "#3F4254",
                        "gray-900": "#181C32"
                    }
                },
                "font-family": "Poppins"
            };
        </script>
        <!--end::Global Config-->

        <script>
            // Class definition

            var KTBootstrapDatepicker = function () {

            var arrows;
            if (KTUtil.isRTL()) {
                arrows = {
                    leftArrow: '<i class="la la-angle-right"></i>',
                    rightArrow: '<i class="la la-angle-left"></i>'
                }
            } else {
                arrows = {
                    leftArrow: '<i class="la la-angle-left"></i>',
                    rightArrow: '<i class="la la-angle-right"></i>'
                }
            }

            // Private functions
            var demos = function () {
                // minimum setup
                $('#kt_datepicker_1').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows
                });

                // minimum setup for modal demo
                $('#kt_datepicker_1_modal').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows
                });

                // input group layout
                $('#kt_datepicker_2').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows
                });

                // input group layout for modal demo
                $('#kt_datepicker_2_modal').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    orientation: "bottom left",
                    templates: arrows
                });

                // enable clear button
                $('#kt_datepicker_3, #kt_datepicker_3_validate').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayBtn: "linked",
                    clearBtn: true,
                    todayHighlight: true,
                    templates: arrows
                });

                // enable clear button for modal demo
                $('#kt_datepicker_3_modal').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayBtn: "linked",
                    clearBtn: false,
                    todayHighlight: false,
                    templates: arrows,
                    viewMode:"years",
                    minViewMode: "years"
                });

                // orientation
                $('#kt_datepicker_4_1').datepicker({
                    rtl: KTUtil.isRTL(),
                    orientation: "top left",
                    todayHighlight: true,
                    templates: arrows
                });

                $('#kt_datepicker_4_2').datepicker({
                    rtl: KTUtil.isRTL(),
                    orientation: "top right",
                    todayHighlight: true,
                    templates: arrows
                });

                $('#kt_datepicker_4_3').datepicker({
                    rtl: KTUtil.isRTL(),
                    orientation: "bottom left",
                    todayHighlight: true,
                    templates: arrows
                });

                $('#kt_datepicker_4_4').datepicker({
                    rtl: KTUtil.isRTL(),
                    orientation: "bottom right",
                    todayHighlight: true,
                    templates: arrows
                });

                // range picker
                $('#kt_datepicker_5').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    templates: arrows
                });

                // inline picker
                $('#kt_datepicker_6').datepicker({
                    rtl: KTUtil.isRTL(),
                    todayHighlight: true,
                    templates: arrows
                });
            }

            return {
                // public functions
                init: function() {
                    demos();
                }
            };
            }();

            jQuery(document).ready(function() {
            KTBootstrapDatepicker.init();
            });
        </script>

        <!--begin::Global Theme Bundle(used by all pages)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/global/plugins.bundle.js?v=7.0.6"></script>
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.6"></script>
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/scripts.bundle.js?v=7.0.6"></script>
        <!--end::Global Theme Bundle-->

        <!--begin::Page Vendors(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.0.6"></script>
        <!--end::Page Vendors-->

        <!--begin::Page Scripts(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/pages/widgets.js?v=7.0.6"></script>
        <!--begin::Page Vendors(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/datatables/datatables.bundle.js?v=7.0.6"></script>
        <!--end::Page Vendors-->

        <!--begin::Page Scripts(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/pages/crud/datatables/basic/scrollable.js?v=7.0.6"></script>
        
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js?v=7.0.6"></script>
        
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/pages/crud/forms/widgets/bootstrap-select.js?v=7.0.6"></script>
        <!--end::Page Scripts-->
    </body>
    <!--end::Body-->
</html>