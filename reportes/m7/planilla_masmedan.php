<?php 
    if (!isset($_SESSION)) {
        session_start();
        ob_start();
    }
    require_once('../../nomina/lib/database.php');

    $db = new Database($_SESSION['bd']);
    if(isset($_POST['codnom']))
        $codnom = $_POST['codnom'];
    else
        $codnom = 1;

    $sql = "select c.apenom,a.ficha,c.codnivel1,d.descrip descripcion_nivel1,b.codnom,b.descrip descripcion_planilla,c.suesal,
    IF(a.codcon=100,a.monto,0) As 'salario',
    IF(a.codcon in (141,157),a.monto,0) As 'comision',
    IF(a.codcon=199,a.monto,0) As 'tardanza',
    IF(a.codcon=158,a.monto,0) As 'bono',
    IF(a.codcon=200,a.monto,0) As 'seguro_social',
    IF(a.codcon=201,a.monto,0) As 'seguro_educativo',
    IF(a.codcon=208,a.monto,0) As 'seguro_social_xiii',
    IF(a.codcon=145,a.monto,0) As 'reembolso',
    IF(a.codcon in (147,156),a.monto,0) As 'uso_auto',
    IF(a.codcon=198,a.monto,0) As 'ausencia',
    IF(a.codcon in (140,169),a.monto,0) As 'incapacidad',
    IF(a.codcon IN (202, 601, 605),a.monto,0) As 'isr',
    IF(a.codcon IN (207, 208, 606, 607),a.monto,0) As 'isr_gastos',
    IF(a.codcon BETWEEN 508 AND 599,a.monto,0) As 'descuentos',
    IF(a.codcon=508,a.monto,0) As 'cxc',
    IF(a.codcon=114,a.monto,0) As 'vacaciones',
    IF(a.codcon=299,a.monto,0) As 'descuentos_varios'
    from nom_movimientos_nomina a 
    left join nom_nominas_pago b on b.codnom = a.codnom
    left join nompersonal c on a.ficha = c.ficha
    left join nomnivel1 d on c.codnivel1 = d.codorg
    where b.codnom=$codnom 
    group by a.ficha
    order by a.codnom,c.codnivel1,c.ficha";
    
    $res = $db->query($sql);
    
    $query  = "SELECT codnom, descrip, codtip FROM  nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
    $planillas = $db->query($query);

?>
<!DOCTYPE html>
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
                        Planilla Quincenal
                        <span class="d-block text-muted pt-2 font-size-sm">Pagos por empleado</span>
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
                            <th>Empleado</th>
                            <th>Ficha</th>
                            <th>Salario</th>
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
                        <?php while ($empleado = mysqli_fetch_array($res)) { ?>
                        <tr>
                            <td><?= $empleado['codcon'] ?></td>
                            <td><?= $empleado['descrip'] ?></td>
                            <td><?= $empleado['ENERO'] ?></td>
                            <td><?= $empleado['FEBRERO'] ?></td>
                            <td><?= $empleado['MARZO'] ?></td>
                            <td><?= $empleado['ABRIL'] ?></td>
                            <td><?= $empleado['MAYO'] ?></td>
                            <td><?= $empleado['JUNIO'] ?></td>
                            <td><?= $empleado['JULIO'] ?></td>
                            <td><?= $empleado['AGOSTO'] ?></td>
                            <td><?= $empleado['SEPTIEMBRE'] ?></td>
                            <td><?= $empleado['OCTUBRE'] ?></td>
                            <td><?= $empleado['NOVIEMBRE'] ?></td>
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
                    <form class="form" method="post" action="planilla_masmedan.php">
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-form-label text-right col-lg-3 col-md-3 col-sm-12">Seleccione la planilla</label>
                                <div class="col-lg-9 col-md-9 col-sm-12">
                                    <select class="form-control selectpicker" name='nomina' id='nomina'>
                                        <?php while ($planilla = mysqli_fetch_array($planillas)) { ?>
                                        <option value="<?= $planilla['codnom'] ?>"><?= $planilla['descrip'] ?></option>
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