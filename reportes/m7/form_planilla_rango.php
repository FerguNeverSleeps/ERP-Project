<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}
require_once('../../nomina/lib/database.php');

$db = new Database($_SESSION['bd']);

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
where year(b.periodo_fin)=2020 group by `a`.`codcon`";

$res = $db->query($sql);
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
                
        
        <div class="card card-custom">
            <div class="card-header">
                <h3 class="card-title">
                    Bootstrap Date Picker Examples
                </h3>
            </div>
            <!--begin::Form-->
            <form class="form">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-form-label text-right col-lg-3 col-sm-12">Modal Demos</label>
                        <div class="col-lg-4 col-md-9 col-sm-12">
                            <a href="#" class="btn font-weight-bold btn-light-primary" data-toggle="modal" data-target="#kt_datepicker_modal">Launch modal datepickers</a>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="form-group row">
                        <div class="col-lg-9 ml-lg-auto">
                            <button type="reset" class="btn btn-primary mr-2">Enviar</button>
                            <button type="reset" class="btn btn-secondary">Cancelar</button>
                        </div>
                    </div>
                </div>
            </form>
            <!--end::Form-->
        </div>
        
        <!--begin::Modal-->
        <div class="modal fade" id="kt_datepicker_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Bootstrap Date Picker Examples</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <form class="form">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-form-label text-right col-lg-3 col-sm-12">Minimum Setup</label>
                                <div class="col-lg-9 col-md-9 col-sm-12">
                                    <input type="text" class="form-control" id="kt_datepicker_1_modal" readonly placeholder="Select date"/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-right col-lg-3 col-sm-12">Input Group Setup</label>
                                <div class="col-lg-9 col-md-9 col-sm-12">
                                    <div class="input-group date" >
                                        <input type="text" class="form-control" readonly  placeholder="Select date" id="kt_datepicker_2_modal"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label text-right col-lg-3 col-sm-12">Enable Helper Buttons</label>
                                <div class="col-lg-9 col-md-9 col-sm-12">
                                    <div class="input-group date" >
                                        <input type="text" class="form-control" value="05/20/2017" id="kt_datepicker_3_modal"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <span class="form-text text-muted">Enable clear and today helper buttons</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary mr-2" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-secondary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

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

        <!--begin::Global Theme Bundle(used by all pages)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/global/plugins.bundle.js?v=7.0.6"></script>
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.6"></script>
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/scripts.bundle.js?v=7.0.6"></script>
        <!--end::Global Theme Bundle-->

        <!--begin::Page Vendors(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.0.6"></script>
        <!--end::Page Vendors-->

        <!--begin::Page Scripts(used by this page)-->
        <script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/metronic/7.0.6/demo1/dist/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js?v=7.0.6"></script>
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
                    clearBtn: true,
                    todayHighlight: true,
                    templates: arrows
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
        
        <!--end::Page Scripts-->
    </body>
    <!--end::Body-->
</html>