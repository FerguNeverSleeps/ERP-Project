<?php
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

include("../lib/common.php"); // conexion();
//include ("../header.php");
include("func_bd.php");
include("funciones_nomina.php");

ini_set("display_errors", "1");
error_reporting(0);

ini_set("memory_limit", "-1");
set_time_limit(0);

$registro_id   = isset($_GET['registro_id'])   ? $_GET['registro_id']   : '';       
$codigo_nomina = isset($_GET['codigo_nomina']) ? $_GET['codigo_nomina'] : '';   

$op = isset($_POST['op']) ? $_POST['op'] : '';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title>Generar Planilla</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<!--<link rel="shortcut icon" href="favicon.ico"/>-->
<style>
body {  /* En uso */
  background-color: white !important; 
}

.page-content-wrapper { /* En uso */
  background-color: white !important; 
}

.page-sidebar-closed .page-content { /* En uso */
  margin-left: 0px !important;
}

.portlet > .portlet-title > .caption { /* En uso */
  font-family: helvetica, arial, verdana, sans-serif;
  font-size: 13px;
  font-weight: bold;
  line-height: 21px;
  margin-bottom: 5px;
}

label.error { /* En uso */
    color: #b94a48;
}  

.margin-bottom-0{
    margin-bottom: 0px;
} 

.margin-left-10{
    margin-left: 10px;
}

.margin-right-10 {
    margin-right: 10px;
}
</style>
</head>
<body class="page-full-width">
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-6">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-reorder"></i> Generar Planilla de Vacaciones
                            </div>
                        </div>
                        <div class="portlet-body form" id="blockui_portlet_body">
                            <br>
                            <div class="alert alert-info margin-left-10 margin-right-10">
                                Presione <strong>Aceptar</strong> para generar la planilla
                            </div>
                            <form id="frmPrincipal" name="frmPrincipal" method="post" role="form" class="margin-bottom-0">
                                <input type="hidden" name="op" id="op" value="0">
                                <div class="form-actions text-center">
                                    <button type="button" class="btn blue btn-md" id="btn-aceptar" 
                                            onclick="javascript: enviar();"><i class="fa fa-check"></i> Aceptar</button>
                                    <button type="button" class="btn red btn-md" 
                                            onclick="javascript: cerrarVentana();"><i class="fa fa-times"></i> Salir</button>                                    
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
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
<script src="../../includes/assets/scripts/core/app1.js"></script>
<!-- <script src="../../includes/assets/scripts/core/app1.js"></script> -->
<script>
jQuery(document).ready(function() {    
    App.init();
});
</script>
<script>
var op = '<?php echo $op; ?>';

if(op!='')
    showProcesando();

function cerrarVentana()
{
    window.close();
}

function enviar()
{
    showProcesando();

    document.frmPrincipal.op.value=1;
    document.frmPrincipal.submit();
}

function nominaGenerada()
{
    hideProcesando();

    $(".alert").removeClass("alert-info").addClass("alert-success");

    $(".alert").html("<i class='fa fa-check'></i>  Se ha generado exitosamente la planilla");

    $("#btn-aceptar").hide();
}

function showProcesando()
{
    App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Generando Planilla'
    });
}

function hideProcesando()
{
    App.unblockUI('#blockui_portlet_body');
}
</script>
<!-- END JAVASCRIPTS -->
<?php
if ($op==1)
{
    // BORRA LOS MOVIMIENTO DE LA NOMINA A PROCESAR
    $sql = "DELETE FROM nom_movimientos_nomina 
            WHERE tipnom='{$_SESSION['codigo_nomina']}' AND codnom='{$registro_id}' AND contractual='1'";           
    $res = $db->query($sql);
    // PARA ACTIVAR EL BONO POR RAZON DE SERVICIO EN EL CSB
    unset($res);

    $sql = "SELECT frecuencia, periodo_ini, periodo_fin, mes, anio 
            FROM   nom_nominas_pago 
            WHERE  codnom='{$registro_id}' AND tipnom='{$_SESSION['codigo_nomina']}'";
    $fila_nom = $db->query($sql)->fetch_assoc();

    $sql = "SELECT monsalmin FROM nomempresa";
    $fila_salmin = $db->query($sql)->fetch_array();
    
    $sql = "SELECT * 
            FROM nomconceptos AS c
            INNER JOIN nomconceptos_tiponomina  AS ct ON c.codcon = ct.codcon
            INNER JOIN nomconceptos_frecuencias AS cf ON c.codcon = cf.codcon
            INNER JOIN nomconceptos_situaciones AS cs ON c.codcon = cs.codcon 
            INNER JOIN nompersonal AS pe ON cs.estado = pe.estado
            INNER JOIN nom_progvacaciones AS vac ON vac.ficha=pe.ficha
            WHERE vac.fechavac<>'0000-00-00' AND vac.fechareivac<>'0000-00-00' AND vac.estado='Pendiente' 
            AND  vac.fechavac >= '{$fila_nom['periodo_ini']}' AND vac.fechareivac <= '{$fila_nom['periodo_fin']}'
            AND  (vac.tipooper='DA' OR vac.tipooper='DV') 
            AND   cf.codfre='{$fila_nom['frecuencia']}' AND pe.tipnom='{$_SESSION['codigo_nomina']}' 
            AND   ct.codtip='{$_SESSION['codigo_nomina']}' AND cs.estado=pe.estado AND c.contractual='1'
            GROUP BY pe.apenom, pe.ficha, c.formula, c.codcon, cs.estado 
            ORDER BY c.codcon";
    $res = $db->query($sql);
    $end = $res->num_rows; 

    $cont = 0;    
    
    
    // pertenece a los campos pero es el mismo valor para todos
    $FECHAHOY    = date("d/m/Y");
    
    $CODNOM      = $registro_id;
    $FECHANOMINA = $fila_nom['periodo_ini'];
    $FECHAFINNOM = $fila_nom['periodo_fin'];
    $FRECUENCIA  = $fila_nom['frecuencia'];
    $LUNES       = lunes($FECHANOMINA); 
    $LUNESPER    = lunes_per($FECHANOMINA, $FECHAFINNOM);
    $SALARIOMIN  = $fila_salmin['monsalmin'];
    
    while( $fila = $res->fetch_assoc() )
    {
        // Prepara las variables con los valores
        $NOMBRE      = $fila['apenom'];
        $FECHARETIRO = $fila['fecharetiro'];
        
        $CEDULA = $fila['cedula'];
        $FICHA  = $fila['ficha'];
        $SUELDO = $fila['suesal'];
        $SEXO   = ".".$fila['sexo']."'";
        $FECHANACIMIENTO = date("d/m/Y", strtotime($fila['fecnac']));
        $EDAD   = date("Y") - date("Y", $fila['fecnac']);
        $TIPONOMINA   = $fila['tipnom'];
        $FECHAINGRESO = $fila['fecing'];
        $CODPROFESION = $fila['codpro'];
        $CODCATEGORIA = $fila['codcat'];
        $CODCARGO     = $fila['codcargo'];
        $SITUACIONPER = $SITUACION = $fila['estado'];
        $SUELDOPROPUESTO = $fila['sueldopro'];
        $TIPOCONTRATO    = $fila['contrato'];
        $FORMACOBRO      = $fila['forcob'];
        $NIVEL1 = $fila['codnivel1'];
        $NIVEL2 = $fila['codnivel2'];
        $NIVEL3 = $fila['codnivel3'];
        $NIVEL4 = $fila['codnivel4'];
        $NIVEL5 = $fila['codnivel5'];
        $NIVEL6 = $fila['codnivel6'];
        $NIVEL7 = $fila['codnivel7'];
        $FECHAAPLICACION  = $fila['fechaplica'];
        $TIPOPRESENTACION = $fila['tipopres'];
        $FECHAFINSUS      = $fila['fechasus'];
        $FECHAINISUS      = $fila['fechareisus'];
        $FECHAFINCONTRATO = $fila['fecharetiro'];
        $FECHAINICON       =$fila['inicio_periodo'];
	    $FECHAFINCON       =$fila['fin_periodo'];
        $REF = 0;
        $CONTRACTUAL = $fila['contractual'];

        if($fila['fechavac'])
            $FECHAVAC = date("Y-m-d", strtotime("{$fila['fechavac']} -1 day"));
        else
            $FECHAVAC = '0000-00-00';

        $FECHAREIVAC = $fila['fechareivac'];
        $PRT         = $fila['proratea'];
        $SALDOPRE    = 0;

        $cont = $cont + 1;

        if( $fila['formula']!='' )
        {
            $formula=$fila['formula'];

            if( $fila['contractual']==1 )
            {
                eval($formula);

                if( $MONTO<=0  &&  $fila['montocero']==1 )
                {
                    $entrar=0;
                }
                else
                {
                    $entrar=1;
                }
                
                if(!isset($GASTOADMON) || empty($GASTOADMON))
                    $GASTOADMON = 0.00;
                
                if( $entrar==1 )
                {   
                    if(gettype ( $MONTO )=='array')
                    {
                        for($i=0;$i<count($MONTO);$i++)
                        {
                            $monto=$MONTO[$i]["montocuo"];
                            $codigopr=$MONTO[$i]["codigopr"];
                            $tipopr=$MONTO[$i]["id_tipoprestamo"];
                            $numpre=$MONTO[$i]["numpre"];
                            $tipocuo=$MONTO[$i]["tipocuo"];
                            $numcuo=$MONTO[$i]["numcuo"];
                            $fechaven=$MONTO[$i]["fechaven"];
                            $salinicial=$MONTO[$i]["salinicial"];
                            $salfinal=$MONTO[$i]["salfinal"];
                            $sql2 = "INSERT INTO nom_movimientos_nomina 
                                    (codnom, 
                                    codcon, 
                                    ficha, 
                                    mes, 
                                    anio, 
                                    monto, 
                                    cedula, 
                                    tipcon, 
                                    unidad, 
                                    valor, 
                                    descrip, 
                                    codnivel1, 
                                    codnivel2, 
                                     codnivel3, 
                                     codnivel4, 
                                     codnivel5, 
                                     codnivel6, 
                                     codnivel7, 
                                     tipnom, 
                                     contractual, 
                                     saldopre, 
                                     refcheque, 
                                     suesal, 
                                     cod_cargo,
                                     gastos_admon, 
                                     tipopr,
                                    numpre,
                                    numcuo,
                                    fechaven,
                                    tipocuo,
                                    montocuo,
                                    salinicial,
                                    salfinal) 
                                    VALUES 
                                    ('{$registro_id}',"
                                    . "'{$fila['codcon']}', "
                                    . "'{$fila['ficha']}', "
                                    . "'{$fila_nom['mes']}', "
                                    . "'{$fila_nom['anio']}', 
                                    '{$monto}', "
                                    . "'{$CEDULA}', "
                                    . "'{$fila['tipcon']}', "
                                    . "'{$fila['unidad']}', "
                                    . "'{$REF}', "
                                    . "'{$fila['descrip']}', 
                                    '{$fila['codnivel1']}', "
                                    . "'{$fila['codnivel2']}', "
                                    . "'{$fila['codnivel3']}', "
                                    . "'{$fila['codnivel4']}',
                                    '{$fila['codnivel5']}', "
                                    . "'{$fila['codnivel6']}', "
                                    . "'{$fila['codnivel7']}', "
                                    . "'{$codigo_nomina}', 
                                    '{$fila['contractual']}', "
                                    . "'{$SALDOPRE}', "
                                    . "'{$cheque}', "
                                    . "'{$SUELDO}', "
                                    . "'{$CODCARGO}', "
                                    . "'{$GASTOADMON}' , "
                                    . "'$tipopr',"
                                    . "'$numpre',"
                                    . "'$numcuo',"
                                    . "'$fechaven',"
                                    . "'$tipocuo',"
                                    . "'$monto',"
                                    . "'$salinicial',"
                                    . "'$salfinal')";
//                                            
                            $res2 = $db->query($sql2); 
                        }
                    }    
                    else
                    {
                        $sql2 = "INSERT INTO nom_movimientos_nomina 
                                 (codnom, codcon, ficha, mes, anio, monto, cedula, tipcon, unidad, valor, descrip, codnivel1, codnivel2, 
                                  codnivel3, codnivel4, codnivel5, codnivel6, codnivel7, tipnom, contractual, saldopre, suesal, cod_cargo,gastos_admon) 
                                 VALUES
                                 ('{$registro_id}', '{$fila['codcon']}', '{$fila['ficha']}', '{$fila_nom['mes']}', '{$fila_nom['anio']}', 
                                  '{$MONTO}', '{$CEDULA}', '{$fila['tipcon']}', '{$fila['unidad']}', '{$REF}', '{$fila['descrip']}', 
                                  '{$fila['codnivel1']}', '{$fila['codnivel2']}', '{$fila['codnivel3']}', '{$fila['codnivel4']}', 
                                  '{$fila['codnivel5']}', '{$fila['codnivel6']}', '{$fila['codnivel7']}', '{$_SESSION['codigo_nomina']}', 
                                  '{$fila['contractual']}', '{$SALDOPRE}', '{$SUELDO}', '{$CODCARGO}', '{$GASTOADMON}')";
                        $res2 = $db->query($sql2); 
                    }
                    if( $_SESSION['codigo_nomina']==2  ||  $_SESSION['codigo_nomina']==4 )
                        mysqli_free_result($res2);

                    unset($res2);
                }
            }
        }

        unset($MONTO);
        unset($T01);
        unset($T02);
        unset($T03);
        unset($T04);
        unset($T05);
        unset($T06);
        unset($T07);    
        unset($FICHA);
        unset($SUELDO);
        unset($SEXO);
        unset($FECHANACIMIENTO);
        unset($EDAD);
        unset($TIPONOMINA);
        unset($FECHAINGRESO);
        unset($CODPROFESION);
        unset($CODCATEGORIA);
        unset($CODCARGO);
        unset($SITUACION);
        unset($FORMACOBRO);
        unset($GASTOADMON);
    }

    $codigo_nuevo  = AgregarCodigo("nom_nominas_pago", "codnom", "WHERE codtip='{$_SESSION['codigo_nomina']}'");
    $codigo_nuevo -= 1;

    $sql = "UPDATE nomtipos_nomina SET codnom='{$codigo_nuevo}' WHERE codtip='{$_SESSION['codigo_nomina']}'";
    $db->query($sql);

    echo "<script>nominaGenerada();</script>";
}
?>
</body>
</html>