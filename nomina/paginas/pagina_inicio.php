<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
require_once("func_bd.php");
require_once("../lib/common.php");
include("../../config.ini.php");

$sSql = new bd($_SESSION['bd']);
$empresas = new bd(SELECTRA_CONF_PYME);
$sql = "SELECT 'Activos' tipo, count(cedula) cantidad FROM nompersonal WHERE estado='Activo' and estado='REGULAR'  group by 1
union all 
(
SELECT 'En Vacaciones' tipo, count(cedula) cantidad FROM nompersonal WHERE estado='Vacaciones' group by 1
) ";
$result = $sSql->query($sql);


$sql = "SELECT ficha,cedula,apenom,fecing,inicio_periodo,foto,fin_periodo,estado,tipo_empleado,nacionalidad,niv.descrip as Descripcion from nompersonal 
LEFT JOIN nomnivel1 niv ON nompersonal.codnivel1=niv.codorg
where estado != 'De Baja' and estado != 'Egresado' and isnull(fin_periodo)!=1 and DATEDIFF(fin_periodo,curdate()) <=60";
$resultContratos = $sSql->query($sql);


$sql = "SELECT ficha,cedula,apenom,fecing,niv.descrip as Descripcion,inicio_periodo,fin_periodo,fin_probatorio, estado,nacionalidad,tipo_empleado "
    . "FROM nompersonal "
    . "LEFT JOIN nomnivel1 niv ON nompersonal.codnivel1=niv.codorg"
    . " WHERE (estado != 'De Baja' and estado != 'Egresado' and isnull(fin_periodo)!=1 and DATEDIFF(fin_probatorio,curdate()) <=60)";
$resultProbatorio = $sSql->query($sql);

$anio = date("Y");
$mes  = date("m");
$sqld = "SELECT expediente.cod_expediente_det,dep.Descripcion,expediente.cedula,expediente_tipo.nombre_tipo tipo,nombre_subtipo tipoRegistro,fecha_inicio,fecha_fin,nombre_tipo,apenom,ficha,nombre_subtipo,
numero_resolucion nResolucion ,expediente.fecha_resolucion,fecha_enterado,fecha_aprobado ,fecing fechaIngreso,
disp.dias,disp.horas,disp.minutos,disp.disponible , 
concat(disp.dias,'/',disp.horas,'/',disp.minutos) registrado,
expediente.desde desdeHora,expediente.hasta hastaHora ,
expediente.descripcion obsercaciones  

            FROM  expediente 
                INNER JOIN expediente_tipo ON expediente.tipo=expediente_tipo.id_expediente_tipo 
                INNER JOIN expediente_subtipo ON expediente.subtipo=expediente_subtipo.id_expediente_subtipo
                INNER JOIN nompersonal ON expediente.cedula=nompersonal.cedula
                LEFT JOIN departamento as dep ON dep.IdDepartamento=nompersonal.IdDepartamento
                left join (SELECT e2.cedula,sum(e2.dias)as dias,sum(e2.horas) as horas,sum(e2.minutos) as minutos, (120 - sum(e2.horas))disponible FROM expediente e2 where  fecha BETWEEN '" . date('Y') . "-01-01' and '" . date('Y') . "-12-31' AND tipo IN (15,16,17) group by e2.cedula) disp on disp.cedula = expediente.cedula 
            WHERE isnull(fecha_fin)!=1 
            AND DATEDIFF(fecha_fin,curdate()) <=60  AND fecha_fin>=NOW()
            AND YEAR(fecha_fin)=$anio AND month(fecha_fin)=$mes "
    . "AND tipo IN (15,16,17)";
$resultexpediente = $sSql->query($sqld);

$sqlvac = "SELECT A.estado,A.ficha,A.cedula,niv.descrip as Descripcion,A.apenom,A.fecing,B.fechavac,B.fechareivac,A.tipo_empleado "
    . "from nompersonal as A "
    . "LEFT JOIN nom_progvacaciones as B ON (B.ficha=A.ficha)"
    . " LEFT JOIN nomnivel1 niv ON A.codnivel1=niv.codorg"
    . " WHERE A.estado = 'Vacaciones' and B.periodo=YEAR(CURDATE())
        GROUP BY A.ficha";
$resultvac = $sSql->query($sqlvac);

$sqlC = "SELECT a.*,niv.descrip as Descripcion,  a.fecnac, ntn.descrip as planilla from nompersonal a "
    . " LEFT JOIN nomnivel1 niv ON a.codnivel1=niv.codorg"
    . " LEFT JOIN nomtipos_nomina ntn ON a.tipnom=ntn.codtip "
    . " WHERE estado NOT LIKE 'De Baja' AND estado NOT LIKE 'Egresado' "
    . " AND MONTH(fecnac) = MONTH(NOW()) "
    . " ORDER BY Descripcion ASC, DAY(fecnac) ASC";
$resultCump = $sSql->query($sqlC);

$consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre"
    . " FROM reloj_detalle "
    . " INNER JOIN nompersonal on reloj_detalle.ficha=nompersonal.ficha "
    . " LEFT JOIN reloj_info on  reloj_detalle.marcacion_disp_id=reloj_info.id_dispositivo "
    . " WHERE fecha = '" . date('Y-m-d') . "' "
    . " ORDER by nompersonal.ficha ASC, reloj_info.cod_dispositivo ASC, fecha ASC";
$resultRelog = $sSql->query($consulta);


$consulta = "SELECT
	np.ficha,
	np.cedula,
	np.apenom,
	nt.tolerancia_entrada,
	nt.tolerancia_salida,
	nt.tolerancia_llegada,
	nt.tolerancia_descanso,
	nt.entrada,
	nt.salida,
	nt.descripcion,
	rd.entrada entrada_rd,
	rd.salida salida_rd,
    COALESCE ( (SUBTIME(rd.entrada, nt.tolerancia_entrada)), '-' ) tardanza,
	IFNULL( rd.tardanza, '8' ) ausencia 
FROM
	nomcalendarios_personal ncp
	LEFT JOIN nompersonal np ON ncp.ficha = np.ficha
	LEFT JOIN nomturnos nt ON nt.turno_id = np.turno_id
	LEFT JOIN reloj_detalle rd ON ncp.fecha = rd.fecha 
	AND rd.ficha = np.ficha 
WHERE
	ncp.fecha = CAST( NOW( ) AS DATE ) 
	AND nt.turno_id != '11' 
	AND (	! ( NOW( ) BETWEEN CAST( rd.entrada AS DATETIME ) AND CAST( nt.tolerancia_entrada AS DATETIME ) ) 
	OR ! ( NOW( ) BETWEEN CAST( rd.salida AS DATETIME ) AND CAST( nt.tolerancia_salida AS DATETIME ) ) 	)
HAVING
        tardanza != '00:00' AND (time_to_sec(tardanza) >= 0 or ausencia = '8');";

$resultAusTard = $sSql->query($consulta);

$sqlV = "SELECT a.foto,a.nomposicion_id,x.Descripcion, b.codorg, b.descrip as departamento, DATE_FORMAT(a.fecing,'%Y-%m-%d') as fecha_permanencia,a.apellidos,a.ficha,
a.nombres,a.apenom,a.cedula,CONCAT (YEAR(CURDATE()),'-',DATE_FORMAT(DATE_ADD(a.fecing, INTERVAL '11' MONTH),'%m-%d')) as dias_vac 
from nompersonal as a
LEFT JOIN dias_incapacidad dias on (a.cedula = dias.cedula)
LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
LEFT JOIN departamento as x ON a.IdDepartamento=x.IdDepartamento
WHERE (MONTH(DATE_ADD(a.fecing, INTERVAL '11' MONTH)) = MONTH(CURDATE())) AND a.estado <> 'Vacaciones' AND a.estado <> 'Egresado'  OR (MONTH(DATE_ADD(a.fecing, INTERVAL '10' MONTH)) = MONTH(CURDATE())) AND estado <> 'Vacaciones' AND  estado <> 'Egresado' 
GROUP BY a.ficha
ORDER BY dias_vac ASC";
$resultVacaciones = $sSql->query($sqlV);
//6 lunes en un año
//$sql="SELECT DISTINCT(cedula) FROM `expediente` WHERE fecha BETWEEN '2021-01-01' and '2021-12-31'";
$sql = "SELECT DISTINCT(ex.cedula),b.Descripcion,n.* FROM expediente as ex 
INNER JOIN nompersonal as n on n.cedula=ex.cedula 
LEFT JOIN departamento as b ON n.IdDepartamento=b.IdDepartamento
WHERE fecha BETWEEN '2021-01-01' and '2021-12-31'";
$resultglobal = $sSql->query($sql);
$resultglobal2 = $sSql->query($sql);
$resultglobal3 = $sSql->query($sql);
$contador = 0;
$contador2 = 0;
$contador3 = 0;
$mes = date("m");
$anio = date("Y");
//echo $fecha_actual = date("d-m-Y");
$fecha_actual = date("Y-m-d");
//echo date("y-m-d",strtotime($fecha_actual."- 1 month"));
function get_nombre_dia($fecha)
{
    $fechats = strtotime($fecha); //pasamos a timestamp
    //el parametro w en la funcion date indica que queremos el dia de la semana
    //lo devuelve en numero 0 domingo, 1 lunes,....
    switch (date('w', $fechats)) {
        case 0:
            return "Domingo";
            break;
        case 1:
            return "Lunes";
            break;
        case 2:
            return "Martes";
            break;
        case 3:
            return "Miercoles";
            break;
        case 4:
            return "Jueves";
            break;
        case 5:
            return "Viernes";
            break;
        case 6:
            return "Sabado";
            break;
    }
}

//Noticias



?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8">
    <!--title>.: <?php echo $_SESSION['nombre_sistema'] . " Planilla"; ?> :.</title-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="description">
    <meta content="" name="author">

    <!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css"> -->
    <link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css">

    <link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css">
    <!--link href="../../includes/assets/css/themes/green.css" rel="stylesheet" type="text/css"-->
    <link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css">
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
    <script src="../../includes/assets/plugins/respond.min.js"></script>
    <script src="../../includes/assets/plugins/excanvas.min.js"></script> 
    <![endif]-->
    <script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
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
    <script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../includes/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../../includes/assets/plugins/flot/jquery.flot.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.resize.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.pie.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.stack.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.crosshair.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script>
        var DIR_INCLUDES = "../../";
    </script>
    <script src="../../includes/assets/scripts/core/app.js"></script>
    <script src="../../includes/assets/scripts/core/helpers.js"></script>
    <script src="../../includes/assets/scripts/core/datatable.js"></script>
    <script src="../../includes/js/gui/numeros.js"></script>
    <script src="../../includes/assets/scripts/custom/calendar-inicio.js" type="text/javascript"></script>
    <script src="../../includes/assets/scripts/custom/components-pickers.js"></script>
    <script src="../../includes/assets/scripts/custom/charts-inicio.js"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        jQuery(document).ready(function() {
            App.init();
        });
    </script>
    <!-- END JAVASCRIPTS -->
    <style type="text/css">
        .page-content {
            margin-left: 0px;
            margin-top: 0px;
            min-height: 600px;
            padding: 25px 20px 20px 20px;
        }
    </style>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* Estilos personalizados para DataTables */
        table.dataTable thead {
            background-color: #1d4ed8;
            /* Azul Tailwind */
            color: white;
        }

        table.dataTable tbody tr:nth-child(odd) {
            background-color: #f9fafb;
            /* Gris claro Tailwind */
        }

        table.dataTable tbody tr:nth-child(even) {
            background-color: #ffffff;
            /* Blanco */
        }

        table.dataTable tbody td {
            text-align: center;
        }
    </style>

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body>

    <div class="container mx-auto">
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Tardanzas y Ausencias del día</h2>
            <table id="attendanceTable" class="stripe hover w-full text-sm">
                <thead>
                    <tr>
                        <th>Ficha</th>
                        <th>Nombres</th>
                        <th>Ausencia</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>134</td>
                        <td>Vasquez, Juan</td>
                        <td>8</td>
                    </tr>
                    <tr>
                        <td>137</td>
                        <td>Vasquez, Angela</td>
                        <td>8</td>
                    </tr>
                    <tr>
                        <td>117</td>
                        <td>Sanchez, Katrina</td>
                        <td>8</td>
                    </tr>
                    <tr>
                        <td>105</td>
                        <td>Pitty, Rubiela</td>
                        <td>8</td>
                    </tr>
                    <tr>
                        <td>126</td>
                        <td>Perez, Pepito</td>
                        <td>8</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- BEGIN CONTAINER -->
    <div>
        <div class="page-content" style="margin-left: 0px;">





            <div class="modal fade" id="portlet-config-calendar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Evento</h4>
                        </div>
                        <div class="modal-body">

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn blue">Guardar</button>
                            <button type="button" class="btn default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <div id="evento-modal" class="modal fade" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title">Evento</h4>
                        </div>
                        <div class="modal-body">
                            <form action="#" class="form-horizontal">
                                <div class="form-group">
                                    <label class="control-label col-md-4">Evento</label>
                                    <div class="col-md-8">
                                        <input type="text" value="" class="form-control" placeholder="Event Title..." id="nombre-evento" /><br />
                                    </div>
                                    <label class="control-label col-md-4">Fecha y Hora Inicio</label>
                                    <div class="col-md-8">
                                        <div class="input-group date form_datetime input-large" data-date="2014-01-21T15:25:00Z">
                                            <input type="text" size="16" readonly class="form-control" id="fecha-inicio-calendar">
                                            <span class="input-group-btn">
                                                <button class="btn default date-reset" type="button"><i class="fa fa-times"></i></button>
                                            </span>
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <!-- /input-group -->
                                    </div>
                                    <br></br>
                                    <label class="control-label col-md-4">Fecha y Hora Fin</label>
                                    <div class="col-md-8">
                                        <div class="input-group date form_datetime input-large" data-date="2014-01-21T15:25:00Z">
                                            <input type="text" size="16" readonly class="form-control" id="fecha-fin-calendar">
                                            <span class="input-group-btn">
                                                <button class="btn default date-reset" type="button"><i class="fa fa-times"></i></button>
                                            </span>
                                            <span class="input-group-btn">
                                                <button class="btn default date-set" type="button"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                        <!-- /input-group -->
                                    </div>
                                    <br></br>

                                    <label class="control-label col-md-4">Color</label>
                                    <div class="col-md-4">
                                        <select id="color-evento" class="bs-select form-control" data-show-subtext="true">
                                            <option style="background-color:yellow" value="yellow">Amarillo</option>
                                            <option style="background-color:#35aa47;" value="green">Verde</option>
                                            <option style="background-color:#4b8df8;" value="blue">Azul</option>
                                            <option style="background-color:#e02222;" value="red">Rojo</option>
                                            <option style="background-color:#852b99;" value="purple">Morado</option>
                                            <option style="background-color:#fafafa;" value="gray">Gris</option>
                                        </select>
                                    </div>


                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button class="btn " data-dismiss="modal" aria-hidden="true">Cerrar</button>
                            <button class="btn blue btn-primary" data-dismiss="modal" onclick="javascript:CalendarIndex.guardarEvento()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>


            <div id="expediente-modal" class="modal fade" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                            <h4 class="modal-title" id="exp-apenom">Licencia</h4>
                        </div>
                        <div class="modal-body">
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    Datos de Licencia
                                </div>
                                <div class="portlet-body">
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Tipo
                                        </div>
                                        <div class="col-md-4 name" id="exp-tipo" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Tipo Registro
                                        </div>
                                        <div class="col-md-4 name" id="exp-tipoRegistro" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Ingreso
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaIngreso" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Registrado <?php echo "(" . date('Y') . ")" ?>
                                        </div>
                                        <div class="col-md-4 name" id="exp-registrado" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Disponible
                                        </div>
                                        <div class="col-md-4 name" id="exp-disponible" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Desde Hora(s)
                                        </div>
                                        <div class="col-md-4 name" id="exp-desdeHora" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Hasta Hora (s)
                                        </div>
                                        <div class="col-md-4 name" id="exp-hastaHora" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Inicio
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaInicio" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Fin
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaFin" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Duracion
                                        </div>
                                        <div class="col-md-3 name" id="exp-duracionAnio" style="font-size:12px">
                                            Años
                                        </div>
                                        <div class="col-md-3 name" id="exp-duracionMes" style="font-size:12px">
                                            Meses
                                        </div>
                                        <div class="col-md-3 name" id="exp-duracionDia" style="font-size:12px">
                                            Dias
                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Numero Resolucion
                                        </div>
                                        <div class="col-md-4 name" id="exp-nResolucion" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Resolucion
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaResolucion" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Aprobado
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaAprobado" style="font-size:12px">

                                        </div>
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Fecha Enterado
                                        </div>
                                        <div class="col-md-4 name" id="exp-fechaEnterado" style="font-size:12px">

                                        </div>
                                    </div>
                                    <div class="row static-info">
                                        <div class="col-md-2 value" style="font-size:12px">
                                            Observaciones
                                        </div>
                                        <div class="col-md-4 name" id="exp-observaciones" style="font-size:12px">

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn " data-dismiss="modal" aria-hidden="true">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>


            <!--            <div class="row">
                <div class="col-md-6 col-sm-6">
                     BEGIN PORTLET                    
                    <a href="#evento-modal" data-toggle="modal" class="btn green">
                    Agregar Evento +
                    </a>
                    <div class="form-group">
                        <label>Calendarios</label>
                        <select id="tipo_calendario" class="form-control" onchange="CalendarIndex.initCalendar();">
                            <option value="0">Mi Calendario</option>
                            <option value="1">De Empresa</option>
                            option value="2">De Personal</option>
                            <option value="3">De Tipos Planilla</option
                        </select>
                    </div>
                    <div class="portlet box grey calendar">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-calendar"></i>Calendario de Eventos
                            </div>
                        </div>
                        <div class="portlet-body light-grey">
                            <div id="calendar">
                            </div>
                        </div>
                    </div>
                     END PORTLET
                </div>
            </div>    -->


            <!-- marcaciones del dia -->
            <?php
            $permisos = new Permisos();
            $login = new Login();


            if (VALIDAR_LICENCIA == "1") {


                $query = "SELECT de.fecha_vencimiento FROM   nomempresa n  
                    LEFT JOIN datos_empresa de on de.cod_empresa = n.codigo   
                WHERE n.bd_nomina='" . $_SESSION['bd'] . "';";

                $resultado_datos_empresa = $empresas->query($query);
                $vencimiento =  $resultado_datos_empresa->fetch_assoc();


                $datetime1 = new DateTime($vencimiento['fecha_vencimiento']);
                $datetime2 = new DateTime(date('Y-m-d'));
                $intervalo = $datetime1->diff($datetime2);
                $fecha_venc = $datetime1->format("d-m-Y");
                if ($intervalo->days < 5 and $intervalo->days > 0) {
            ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet box yellow">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-clock"></i>Licencia
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="alert alert-warning"> Su Licencia termina el <b> <?php echo $fecha_venc;  ?> </b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
            }
            if ($permisos->getPermisoRol($login->getRol(), 555)) {

                $hoy = date("Y-m-d H:i:s");
                //Noticias
                $sqlNoticias = "SELECT * from noticias where estatus = '1' AND '{$hoy}' >= fecha_inicio  AND '{$hoy}' <= fecha_vencimiento  ORDER BY fecha_vencimiento DESC;";

                $resultNoticias = $sSql->query($sqlNoticias);
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-clock"></i>Noticias
                                </div>
                                <!--<div class="actions" style="margin-top: 10px;">
    
                                        <a class="btn btn-default green"  onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                                            <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                        </a>
                                    </div>-->
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <?php while ($fila = $resultNoticias->fetch_assoc()) {
                                    ?>

                                        <div class="alert alert-info">
                                            <b>Titulo:</b>
                                            <?php echo $fila["titulo"];
                                            ?> <br>
                                            <?php echo $fila["descripcion"]; ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

            if ($permisos->getPermisoRol($login->getRol(), 483)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-clock"></i>Marcaciones del dia
                                </div>
                                <!--<div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default green"  onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>-->
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table id="tabla_marcaciones" class="table table-hover" style="font-size:11px">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Foto
                                                </th>
                                                <th>
                                                    Ficha
                                                </th>
                                                <th>
                                                    Nombres
                                                </th>
                                                <th>
                                                    Apellidos
                                                </th>
                                                <th>
                                                    Dispositivo
                                                </th>
                                                <th>
                                                    Entrada
                                                </th>
                                                <th>
                                                    S. Almu
                                                </th>
                                                <th>
                                                    E. Almu
                                                </th>
                                                <th>
                                                    Salida
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($fila = $resultRelog->fetch_assoc()) {
                                                $foto = ($fila['foto'] != ''  && file_exists($fila['foto'])) ?  $fila['foto'] : 'fotos/silueta.gif';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <img width="32" height="32" style="border-radius: 50% !important;" src="<?php echo $foto ?>" />
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['ficha']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['nombres']) . " " . utf8_encode($fila['nombres2']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apellidos']) . " " . utf8_encode($fila['apellido_materno']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['nombre']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['entrada']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['salmuerzo']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['ealmuerzo']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['salida']) ?>
                                                    </td>


                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-clock"></i>Tardanzas y Ausencias del dia
                                </div>
                                <!--<div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default green"  onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>-->
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table id="tabla_aus_tar" class="table table-hover" style="font-size:11px">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Ficha
                                                </th>
                                                <th>
                                                    Nombres
                                                </th>
                                                <th>
                                                    Entrada
                                                </th>
                                                <th>
                                                    Tardanza
                                                </th>
                                                <th>
                                                    Ausencia
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($fila = $resultAusTard->fetch_assoc()) {
                                                $fecha_entrada1 = new Datetime($fila['entrada']);
                                                $fecha_entrada_rd1 = new Datetime($fila['entrada_rd']);
                                                $fecha_tolerancia_entrada1 = new Datetime($fila['tolerancia_entrada']);
                                                $intervalo = $fecha_tolerancia_entrada1->diff($fecha_entrada_rd1);

                                            ?>
                                                <tr>
                                                    <td>
                                                        <?php echo utf8_encode($fila['ficha']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apenom']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['entrada_rd']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['tardanza']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['ausencia']) ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            /*
               //if(isset($_SESSION['noticias_ver_427']))               
              // {
                                
                $hoy = date("Y-m-d H:i:s");
                //Noticias
                $sqlNoticias = "SELECT * from noticias where estatus = '1' AND '{$hoy}' >= fecha_inicio  AND '{$hoy}' <= fecha_vencimiento  ORDER BY fecha_vencimiento DESC;";
                
                $resultNoticias = $sSql->query($sqlNoticias);
                   ?>
                   <div class="row">
                       <div class="col-md-12">
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-clock"></i>Noticias
                                    </div>
                                    <!--<div class="actions" style="margin-top: 10px;">
    
                                        <a class="btn btn-default green"  onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                                            <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                        </a>
                                    </div>-->
                                </div>
                                <div class="portlet-body">
                                    <div class="table-responsive">
                                       <?php while($fila = $resultNoticias->fetch_assoc()){ 
                                           ?>
                                           
                                           <div class="alert alert-info">
                                           <b>Titulo:</b>
                                           <?php echo $fila["titulo"];
                                           ?> <br>
                                           <?php echo $fila["descripcion"];?>
                                           </div>
                                       <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php //}
            */
            if ($permisos->getPermisoRol($login->getRol(), 478)) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box green">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-gift"></i>Proximos Cumpleaños
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table id="tabla_cumple" class="table table-hover" style="font-size:11px">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Foto
                                                </th>
                                                <th>
                                                    Ficha
                                                </th>
                                                <th>
                                                    Cédula
                                                </th>
                                                <th>
                                                    Nombres
                                                </th>
                                                <th>
                                                    Apellidos
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Planilla
                                                </th>
                                                <th>
                                                    Fecha de nacimiento
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($fila = $resultCump->fetch_assoc()) {
                                                $foto = ($fila['foto'] != ''  && file_exists($fila['foto'])) ?  $fila['foto'] : 'fotos/silueta.gif';
                                            ?>
                                                <tr>
                                                    <td>
                                                        <img width="32" height="32" style="border-radius: 50% !important;" src="<?php echo $foto ?>" />
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['ficha']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['cedula']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['nombres']) . " " . utf8_encode($fila['nombres2']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apellidos']) . " " . utf8_encode($fila['apellido_materno']) ?>
                                                    </td>
                                                    <td>
                                                        <?php

                                                        $caracter = "<br>";
                                                        $descripcion = wordwrap(utf8_encode($fila['Descripcion']), 50, $caracter, false);

                                                        echo $descripcion;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php

                                                        echo $fila['planilla'];
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($fila['fecnac'] != "0000-00-00" && $fila['fecnac'] != "" && $fila['fecnac'] != NULL) {

                                                            $fecha_nac = strftime('%d/%b/%Y', strtotime($fila['fecnac']));
                                                        } else {
                                                            $fecha_nac = "";
                                                        }
                                                        echo $fecha_nac;
                                                        ?>
                                                    </td>

                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 479)) {
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Contratos Por Vencer
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_contratos">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Foto
                                                </th>
                                                <th>
                                                    Status
                                                </th>

                                                <th>
                                                    Tipo
                                                </th>

                                                <th>
                                                    #
                                                </th>
                                                <th>
                                                    Cedula
                                                </th>
                                                <th>
                                                    Nombre
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Inicio
                                                </th>
                                                <th>
                                                    Fin
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultContratos->fetch_assoc()) { ?>
                                                <?php
                                                $foto = ($fila['foto'] != ''  && file_exists($fila['foto'])) ?  $fila['foto'] : 'fotos/silueta.gif';
                                                if ($fila['nacionalidad'] == 1) {
                                                    $nac = 'Nacional';
                                                } else {
                                                    $nac = 'Extranjero';
                                                }
                                                ?>
                                                <tr>

                                                    <td>
                                                        <img width="32" height="32" style="border-radius: 50% !important;" src="<?php echo $foto ?>" />
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['estado'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $nac ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['ficha'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['cedula'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apenom']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['Descripcion']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fecing'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fin_periodo'] ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 479)) {
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Probatorios Por Vencer
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_probatorios">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Status
                                                </th>

                                                <th>
                                                    Tipo
                                                </th>

                                                <th>
                                                    #
                                                </th>
                                                <th>
                                                    Cedula
                                                </th>
                                                <th>
                                                    Nombre
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Inicio
                                                </th>
                                                <th>
                                                    Fin
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultProbatorio->fetch_assoc()) { ?>

                                                <tr>
                                                    <td>
                                                        <?php echo $fila['estado'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['nacionalidad'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['ficha'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['cedula'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apenom']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['Descripcion']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fecing'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fin_pprobatorio'] ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 480)) {
            ?>
                <!-- Licencias por vencer -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Licencias Por Vencer
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px">
                                        <thead>
                                            <tr>

                                                <th>
                                                    Cedula
                                                </th>
                                                <th>
                                                    Ficha
                                                </th>
                                                <th>
                                                    Nombres
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Licencia
                                                </th>
                                                <th>
                                                    Tipo
                                                </th>

                                                <th>
                                                    Fin
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultexpediente->fetch_assoc()) { ?>
                                                <?php $cod_expediente_det = $fila['cod_expediente_det'] ?>
                                                <tr onclick="setModalExpediente('<?php echo $fila['apenom'] ?>','<?php echo $cod_expediente_det ?>','<?php echo $fila['tipo'] ?>','<?php echo utf8_encode($fila['tipoRegistro']) ?>','<?php echo $fila['nSecuencial'] ?>','<?php echo $fila['fechaIngreso'] ?>','<?php echo $fila['registrado'] ?>','<?php echo $fila['disponible'] ?>','<?php echo $fila['desdeHora'] ?>','<?php echo $fila['hastaHora'] ?>','<?php echo $fila['fecha_inicio'] ?>','<?php echo $fila['fecha_fin'] ?>','<?php echo $fila['duracionAnio'] ?>','<?php echo $fila['duracionMes'] ?>','<?php echo $fila['exp-duracionDia'] ?>','<?php echo $fila['nResolucion'] ?>','<?php echo $fila['fecha_resolucion'] ?>','<?php echo $fila['fecha_aprobado'] ?>','<?php echo $fila['fecha_enterado'] ?>','<?php echo $fila['obsercaciones'] ?>')" href="#expediente-modal" data-toggle="modal">
                                                    <td>
                                                        <?php echo $fila['cedula'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['ficha'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['apenom'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['Descripcion']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['nombre_tipo'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['nombre_subtipo']) ?>
                                                    </td>

                                                    <td>

                                                        <?php echo $fila['fecha_fin'] ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php }

            if ($permisos->getPermisoRol($login->getRol(), 481)) {
            ?>
                <!-- Vacaciones -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Colaboradores en Vacaciones
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_colaboradores_vacaciones.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_vacaciones">
                                        <thead>
                                            <tr>

                                                <th>
                                                    #
                                                </th>
                                                <th>
                                                    Cedula
                                                </th>
                                                <th>
                                                    Nombre
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Inicio
                                                </th>
                                                <th>
                                                    Fin
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultvac->fetch_assoc()) { ?>

                                                <tr>


                                                    <td>
                                                        <?php echo $fila['ficha'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['cedula'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apenom']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['Descripcion']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fechavac'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['fechareivac'] ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php }

            if ($permisos->getPermisoRol($login->getRol(), 481)) {
            ?>
                <!-- Vacaciones -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Colaboradores con Derecho a Vacaciones
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_colaboradores_derecho_vacacion.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_derecho_vacacion">
                                        <thead>
                                            <tr>

                                                <th>
                                                    Foto
                                                </th>
                                                <th>
                                                    Nº Emp.
                                                </th>
                                                <th>
                                                    Nombres y Apellidos
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    Cédula
                                                </th>
                                                <th>
                                                    I. Labores
                                                </th>
                                                <th>
                                                    F. Vacaciones
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultVacaciones->fetch_assoc()) {
                                                $foto = ($fila['foto'] != ''  && file_exists($fila['foto'])) ?  $fila['foto'] : 'fotos/silueta.gif';
                                            ?>

                                                <tr>
                                                    <td>


                                                        <img width="32" height="32" class="img-circle" src="<?php echo $foto ?>" />
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['ficha'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['apenom']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo utf8_encode($fila['Descripcion']) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fila['cedula'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y', strtotime($fila['fecha_permanencia'])) ?>
                                                    </td>
                                                    <td>
                                                        <?php echo date('d-m-Y', strtotime($fila['dias_vac'])) ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 481)) {
            ?>
                <!-- Colaboradores con 6 amonestaciones en un año  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>Colaboradores con 6 amonestaciones en un año
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_colaboradores_derecho_vacacion.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_derecho_vacacion">
                                        <thead>
                                            <tr>

                                                <th>
                                                    Nombres y Apellidos
                                                </th>
                                                <th>
                                                    cedula
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    cantidad de lunes en el anio
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultglobal->fetch_array()) {

                                                $cedula_colaborador = $fila['cedula'];
                                                $sql = "SELECT fecha FROM expediente WHERE cedula='$cedula_colaborador' AND tipo='5'";
                                                $result_cedula = $sSql->query($sql);
                                                while ($registros_fechas = $result_cedula->fetch_array()) {
                                                    $nuevafecha = $registros_fechas['fecha'];
                                                    $dias = get_nombre_dia($nuevafecha);
                                                    if ($dias == 'Lunes') {
                                                        $contador++;
                                                    }
                                                }

                                                if ($contador >= 2) { ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo utf8_encode($fila['apenom']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $fila['cedula'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo utf8_encode($fila['Descripcion']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $contador ?>
                                                        </td>
                                                    </tr>
                                            <?php $contador = 0;
                                                }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 481)) {
            ?>
                <!-- 2 lunes en un mes  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>2 lunes en un mes
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_colaboradores_derecho_vacacion.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_derecho_vacacion">
                                        <thead>
                                            <tr>

                                                <th>
                                                    Nombres y Apellidos
                                                </th>
                                                <th>
                                                    cedula
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    cantidad de lunes en el mes
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultglobal2->fetch_array()) {

                                                $cedula_colaborador = $fila['cedula'];
                                                $sql = "SELECT fecha FROM expediente WHERE cedula='$cedula_colaborador' AND tipo='5'
                                                    AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio'";
                                                $result_cedula = $sSql->query($sql);
                                                while ($registros_fechas = $result_cedula->fetch_array()) {
                                                    $nuevafecha = $registros_fechas['fecha'];
                                                    $dias = get_nombre_dia($nuevafecha);
                                                    if ($dias == 'Lunes') {
                                                        $contador2++;
                                                    }
                                                }

                                                if ($contador2 >= 2) { ?>
                                                    <tr>

                                                        <td>
                                                            <?php echo utf8_encode($fila['apenom']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $fila['cedula'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo utf8_encode($fila['Descripcion']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $contador2 ?>
                                                        </td>
                                                    </tr>
                                            <?php $contador2 = 0;
                                                }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 481)) {
            ?>
                <!-- 2 lunes en un mes  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-cogs"></i>2 amonestaciones en 30 dias
                                </div>
                                <div class="actions" style="margin-top: 10px;">

                                    <a class="btn btn-default blue" onclick="javascript: window.location='../../reportes/excel/excel_colaboradores_derecho_vacacion.php'">
                                        <i class="fa fa-print"></i> IMPRIMIR LISTADO
                                    </a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-responsive">
                                    <table class="table table-hover" style="font-size:11px" id="tabla_derecho_vacacion">
                                        <thead>
                                            <tr>

                                                <th>
                                                    Nombres y Apellidos
                                                </th>
                                                <th>
                                                    cedula
                                                </th>
                                                <th>
                                                    Departamento
                                                </th>
                                                <th>
                                                    amonestaciones en 30 dias
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            while ($fila = $resultglobal3->fetch_array()) {
                                                $cedula_colaborador = $fila['cedula'];
                                                $sql = "SELECT COUNT(*) AS number FROM expediente WHERE cedula='$cedula_colaborador' AND tipo='5'
                                                        AND MONTH(fecha)='$mes' AND YEAR(fecha)='$anio' and fecha BETWEEN (DATE_ADD('$fecha_actual',INTERVAL -30 DAY)) AND '$fecha_actual'";
                                                $result_cedula = $sSql->query($sql);

                                                $filas = $result_cedula->fetch_array();
                                                $number = $filas['number'];
                                                // if ($number>1) {
                                                //     $contador3++;
                                                // }

                                                // while($registros_fechas = $result_cedula->fetch_array()){
                                                //     $nuevafecha=$registros_fechas['fecha'];
                                                //     if (isset($nuevafecha)) {
                                                //         $contador3++;
                                                //     }
                                                //     //echo $registros_fechas['fecha'];
                                                // //     //echo $fex=date("y-m-d",strtotime($nuevafecha."- 1 month"));exit;
                                                // //     $sql="SELECT COUNT(*) FROM expediente WHERE fecha BETWEEN '$fex' AND '$nuevafecha'";
                                                // //     //$dias=get_nombre_dia($nuevafecha);
                                                // //     if ($dias=='Lunes') {
                                                // //         $contador2++;
                                                // //     }
                                                // }                                     

                                                if ($number > 2) { ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo utf8_encode($fila['apenom']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $fila['cedula'] ?>
                                                        </td>
                                                        <td>
                                                            <?php echo utf8_encode($fila['Descripcion']) ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $number ?>
                                                        </td>
                                                    </tr>
                                            <?php  }
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            if ($permisos->getPermisoRol($login->getRol(), 482)) {
            ?>

                <div class="row"> <!--  Activo y Vacaciones-->
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-reorder"></i>Empleados / Vacaciones
                                </div>
                            </div>
                            <div class="portlet-body">
                                <h4>.</h4>
                                <div id="pie_chart_1" class="chart"> </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>



                <!--[if lt IE 9]>
<script src="../../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#tabla_cumple').dataTable({
                            "aaSorting": [
                                [7, "asc"]
                            ]
                        });

                        $('#tabla_marcaciones').dataTable({
                            "aaSorting": [
                                [2, "asc"]
                            ]
                        });
                        $('#tabla_vacaciones').dataTable({
                            "aaSorting": [
                                [1, "asc"]
                            ]
                        });
                        $('#tabla_aus_tar').dataTable({
                            "aaSorting": [
                                [1, "asc"]
                            ]
                        });
                        $('#tabla_derecho_vacacion,#tabla_contratos,#tabla_probatorios').dataTable();
                        //
                        //    $('#tabla_personal_externo').dataTable({
                        //        "aaSorting": [[1, "asc" ]]
                        //    });
                        //    $('#cambio_categoria').dataTable({
                        //        "aaSorting": [[1, "asc" ]]
                        //    });

                    });
                    var dataEmpleadosVacaciones = Array();
                    <?php
                    while ($fila = $result->fetch_assoc()) { ?>
                        descripcionV = "<?php echo $fila['tipo'] ?>";
                        cantidadV = "<?php echo $fila['cantidad'] ?>";

                        dataEmpleadosVacaciones.push({
                            label: descripcionV,
                            data: cantidadV
                        });
                    <?php
                    } ?>

                    var DB_CONNECT = "";

                    jQuery(document).ready(function() {
                        CalendarIndex.initCalendar();
                        ComponentsPickers.init();
                        Charts.initPieCharts();
                        //Charts.initBarCharts();

                    });

                    function setModalExpediente(apenom, codigo, tipo, tipoRegistro, nSecuencial, fechaIngreso, registrado, disponible, desdeHora, hastaHora, fechaInicio, fechaFin, duracionAnio, duracionMes, duracionDia, nResolucion, fechaResolucion, fechaAprobado, fechaEnterado, obsercaciones) {
                        $('#exp-apenom').html("Licencia de " + apenom);
                        $('#exp-tipo').html(tipo);
                        $('#exp-tipoRegistro').html(tipoRegistro);
                        $('#exp-nSecuencial').html(nSecuencial);
                        $('#exp-fechaIngreso').html(fechaIngreso);
                        $('#exp-registrado').html(registrado + ' (Dias/Horas/Minutos)');
                        $('#exp-disponible').html('Dias: ' + parseInt(disponible / 8) + ' Horas: ' + disponible);
                        $('#exp-desdeHora').html(desdeHora);
                        $('#exp-hastaHora').html(hastaHora);
                        $('#exp-fechaInicio').html(fechaInicio);
                        $('#exp-fechaFin').html(fechaFin);
                        $('#exp-duracionAnio').html(duracionAnio);
                        $('#exp-duracionMes').html(duracionMes);
                        $('#exp-duracionDia').html(duracionDia);
                        $('#exp-nResolucion').html(nResolucion);
                        $('#exp-fechaResolucion').html(fechaResolucion);
                        $('#exp-fechaAprobado').html(fechaAprobado);
                        $('#exp-fechaEnterado').html(fechaEnterado);
                        $('#exp-observaciones').html(observaciones);
                    }
                </script>





                </div>
                <!-- END CONTENT -->
        </div>
        <!-- END CONTAINER -->

        <!-- END BODY -->

        <!-- JQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- DataTables JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <!-- Configuración de DataTables -->
        <script>
            $(document).ready(function() {
                $('#attendanceTable').DataTable({
                    language: {
                        lengthMenu: "Mostrar _MENU_ entradas",
                        search: "Buscar:",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                        paginate: {
                            previous: "Anterior",
                            next: "Siguiente",
                        },
                    },
                });
            });
        </script>

</body>
<!-- END BODY -->

</html>