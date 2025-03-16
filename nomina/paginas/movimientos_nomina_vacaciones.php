<?php 
require_once('../lib/database.php');

error_reporting(E_ALL);

$db = new Database($_SESSION['bd']);
// include("../lib/common.php");
// include("func_bd.php");
$termino      = isset($_SESSION['termino']) ? $_SESSION['termino'] : '';

$prestaciones = isset($_GET['prestaciones'])  ? $_GET['prestaciones']  : '' ;
$cod_nomina   = isset($_GET['codigo_nomina']) ? $_GET['codigo_nomina'] : '' ;
$ficha        = isset($_GET['ficha'])         ? $_GET['ficha']         : '' ; 
$tipob        = isset($_GET['tipob'])         ? $_GET['tipob']         : '' ;
$flag         = isset($_GET['flag'])          ? $_GET['flag']          : '' ;
$codt         = isset($_GET['codt'])          ? $_GET['codt']          : '' ;
$tipo         = isset($_GET['tipo'])          ? $_GET['tipo']          : '' ;
$vac          = isset($_GET['vac'])           ? $_GET['vac']           : '' ;
$des          = isset($_GET['des'])           ? $_GET['des']           : '' ;
$pagina       = isset($_GET['pagina'])        ? $_GET['pagina']        : 0 ;

$buscar       = isset($_POST['buscar'])       ? $_POST['buscar']       : '' ;

if( $tipob == '' )
    $tipob = $tipo;
else
    $des   = $ficha;

$dir = "nomina_de_vacaciones.php";

if( $prestaciones == 1  &&  $vac != 1 )
    $dir = "nomina_de_prestaciones.php";

if( $flag == 1 )
    $buscar = $ficha;

$sql    = "SELECT periodo_ini, periodo_fin, status, 
                  DATE_FORMAT(periodo_ini, '%d/%m/%Y') AS fecha_ini,
                  DATE_FORMAT(periodo_fin, '%d/%m/%Y') AS fecha_fin
           FROM nom_nominas_pago WHERE codnom='{$cod_nomina}' AND tipnom='{$_SESSION['codigo_nomina']}'";
$nomina = $db->query($sql)->fetch_assoc();


$sql1 = " SELECT pe.foto AS foto, pe.ficha AS ficha, pe.apenom AS apenom, pe.cedula AS cedula, pe.suesal AS suesal, pe.estado AS estado, 
                 pe.codnivel1 AS codnivel1, DATE_FORMAT(pe.fecing, '%d/%m/%Y') AS fecing, ca.des_car AS cargo 
          FROM nompersonal AS pe 
          LEFT JOIN nomcargos AS ca ON pe.codcargo=ca.cod_car ";

/*$sql2 = " WHERE (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%')
          AND   pe.tipnom = '{$_SESSION['codigo_nomina']}' ";*/
$sql2 = " WHERE pe.tipnom = '{$_SESSION['codigo_nomina']}'";

if( $buscar != ''  ||  $tipob != '')
{
    if( ! $tipob ) // $tipob == ''
    {
        $busqueda = isset($_POST['busqueda']) ? $_POST['busqueda'] : '' ;

        $_GET['ficha']         = $des = $buscar;
        $_GET['codigo_nomina'] = $_POST['codigo_nomina'];
        $_GET['codt']          = $_POST['codt'];
    }

    $sql2 .= " AND pe.ficha = {$des} 
               AND  (  ( pe.fecharetiro >= '{$nomina['periodo_ini']}' AND pe.fecharetiro <= '{$nomina['periodo_fin']}' ) 
                      OR pe.fecharetiro = '0000-00-00' ) ";        
}
else
{
    if( $vac == 1 )
    {
        $sql1 .= " LEFT JOIN nom_progvacaciones AS vac ON pe.ficha=vac.ficha 
        LEFT JOIN nom_movimientos_nomina as nmn on nmn.ficha = vac.ficha
";
        if ($nomina['status'] == 'A') 
        {
            /*$sql2 .= " AND  ( vac.fechavac <> '0000-00-00' AND vac.fechareivac <> '0000-00-00' AND vac.estado = 'Pendiente') 
                 AND (vac.fechavac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}') 
                 AND (vac.fechareivac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}') ";*/
            $sql2 .= " AND  
                        (
                        vac.fechavac <> '0000-00-00' AND 
                        vac.fechareivac <> '0000-00-00' AND 
                        vac.fechavac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}' AND 
                        vac.fechareivac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}'
                        )";
        }
        else
        {
            /*$sql2 .= " AND  ( vac.fechavac <> '0000-00-00' AND vac.fechareivac <> '0000-00-00' AND vac.estado = 'Pendiente'
                 AND (nmn.tipnom='{$_SESSION['codigo_nomina']}' AND nmn.codnom='{$cod_nomina}') 
)
                ";*/
            /*$sql2 .= " AND  
                        ( 
                        vac.fechavac <> '0000-00-00' AND 
                        vac.fechareivac <> '0000-00-00' AND 
                        vac.estado = 'Pendiente' AND 
                        vac.fechavac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}' AND
                        vac.fechareivac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}' AND
                        vac.tipnom=nmn.tipnom                
                        )";*/
            $sql2 .= " AND  
                        (                        
                            vac.tipnom=nmn.tipnom AND nmn.codnom='{$cod_nomina}'                
                        )";///*nmn.codnom='{$cod_nomina}'*/
        }

        /*$sql2 .= " AND  ( vac.fechavac <> '0000-00-00' AND vac.fechareivac <> '0000-00-00' AND vac.estado = 'Pendiente'
        AND (vac.fechavac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}') AND (vac.fechareivac BETWEEN '{$nomina['periodo_ini']}'  AND  '{$nomina['periodo_fin']}'))
        ";*/
    }
    else
    {
        if( $_SESSION['codigo_nomina'] != 2 )
        {
            $sql2 .= " AND ( ( pe.fecharetiro >= '{$nomina['periodo_ini']}' AND pe.fecharetiro <= '{$nomina['periodo_fin']}' ) 
                           OR pe.fecharetiro = '0000-00-00' ) ";
        }
    }
}
     
$sql = $sql1 . $sql2 . "  GROUP BY pe.ficha ORDER BY pe.ficha ASC ";
//print $sql;
$numero_filas = $db->query($sql)->num_rows;
$num_paginas  = ceil($numero_filas / 1);
//echo $sql;
if( $pagina < 1 )
    $pagina = 1;
else
{
    if( $pagina > $num_paginas  &&  $num_paginas != 0 )
        $pagina = $num_paginas;
}

$sql .= " LIMIT " . ($pagina - 1) . ", " . $pagina; 
$personal = $db->query($sql)->fetch_assoc();
if( $ficha == '' )
    $ficha = $personal['ficha'];


$cons_obs="	SELECT COALESCE(observacion,'') observacion 
			FROM nom_movimientos_nomina 
			WHERE codcon='114' AND codnom='{$cod_nomina}' AND tipnom='{$_SESSION['codigo_nomina']}' and ficha = '".$personal['ficha']."'";
$observacion = $db->query($cons_obs)->fetch_assoc()['observacion'];
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title>Movimientos de Planilla Vacaciones</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
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
<link href="../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
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
/*  font-size: 16px;*/
}

label.error { /* En uso */
	color: #b94a48;
}

.text-left {
	text-align: left !important;
}

.padding-left-20 {
	padding-left: 20px;
}	

.padding-right-5{
	padding-right: 5px;
}

.form-group {
    margin-bottom: 5px;
}

.tile {
    height: 200px;
    width:  200px !important;
    float: none;
    margin: 0 auto;
}

.tabbable-custom .nav-tabs > li.active {
   border-top: 3px solid #0362fd;
}

.table {
    margin-bottom: 5px;
}

.fancybox-wrap {
    top: 50px !important;
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
			<!-- BEGIN PAGE HEADER-->
			<div class="row hide">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">Vacaciones</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li class="btn-group">
							<button type="button" class="btn blue" 
							        onclick="javascript: window.location='../fpdf/vacaciones_nomina.php'">
							<span>Imprimir Recibo</span> <i class="fa fa-print"></i> 
							</button>
						</li>
						<li><i class="glyphicon glyphicon glyphicon-sort"></i>
							<a class="hide-underline">Transacciones</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li><a class="hide-underline">Vacaciones</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li><a href="nomina_de_vacaciones.php">Planilla de Vacaciones</a></li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-12">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption"><?php echo "Movimientos de {$termino} Vacaciones Nº: {$cod_nomina}, ".
                            								"Fecha de Inicio: {$nomina['fecha_ini']} - Fecha Fin: {$nomina['fecha_fin']}";  ?>                            						
                            </div>
                            <div class="actions">
								<a class="btn btn-sm red btnObservacion">
									<i class="fa fa-print"></i> <span>Observación</span>  
								</a>
								<a class="btn btn-sm blue active" onclick="javascript: verRecibo();">
									<i class="fa fa-print"></i> <span>Imprimir Recibo</span>  
								</a>

                                <a class="btn btn-sm blue active" onclick="javascript: window.location='<?php echo $dir; ?>'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body" id="blockui_portlet_body">       

							<form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">

								<div class="navbar" role="navigation" style="margin-bottom: 0px;">

										<div class="col-md-3" style="padding-left: 14px;">
											<div class="input-group">
												<input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar" style="height: 30px;">
												<span class="input-group-btn">
													<button type="submit" class="btn btn-md blue"><i class="fa fa-search"></i></button>
												</span>
											</div>							
										</div>
                                        
                                        <div class="col-md-2 hide" style="padding-left: 0px; padding-right: 5px;">
                                            <select name="busqueda" id="busqueda" class="form-control input-sm" style="height: 30px;">
                                                <option value="ficha">C&oacute;digo ficha</option>
                                            </select> 
                                        </div>

										<div class="col-md-2" style="padding-left: 0px;">
								            <?php
								                $url_show_all = "movimientos_nomina_vacaciones.php?pagina={$pagina}&codigo_nomina={$cod_nomina}&codt={$codt}";

								                if( $vac != '' )
								                    $url_show_all .= "&vac=1";
								            ?>
											<button type="button" class="btn btn-md blue" onclick="javascript: window.location='<?php echo $url_show_all; ?>'">Mostrar todo</button>
										</div>

										<div class="col-md-7" style="padding-right: 0px;">										
											<div class="btn-group pull-right">
												<?php
													if( $nomina['status'] == 'A' /*and $_SESSION['planilla_vacaciones_editar']*/)
													{
														?>
                            <?php if($_SESSION['planilla_vacaciones_detalle_agregar']):?>
														<button type="button" class="btn btn-md btn-default" onclick="javascript: enviar(1,0);"><i class="fa fa-plus"></i> Agregar</button>
                            <?php endif;?>
                            <?php if($_SESSION['planilla_vacaciones_detalle_editar']):?>
														<button type="button" class="btn btn-md btn-default" onclick="javascript: enviar(7,0);"><i class="fa fa-edit"></i> Editar</button>
                            <?php endif;?>
                            <?php if($_SESSION['planilla_vacaciones_detalle_generar']):?>
														<button type="button" class="btn btn-md btn-default" onclick="javascript: enviar(6,0);"><i class="fa fa-cogs"></i> Generar</button>
                            <?php endif;?>
                            <?php if($_SESSION['planilla_vacaciones_detalle_generar']):?>
                                                        <button type="button" class="btn btn-md btn-default" onclick="javascript: enviar(8,0);"><i class="fa fa-cogs"></i> Recalcular</button>
                            <?php endif;?>

                            <?php if($_SESSION['planilla_vacaciones_detalle_eliminar']):?>
														<button type="button" class="btn btn-md red" onclick="javascript: enviar(5);"
														        style="border-bottom-width: 1px; border-top-width: 1px; border-left-width: 1px; border-right-width: 1px;">
														        <i class="fa fa-trash-o"></i> Borrar</button>
                            <?php endif;?>
														<?php
													}
												?>
											</div>
										</div>

								</div>                        
                                <div class="form-body" style="padding-bottom: 0px">

									<div class="row">										
										<div class="col-md-6 padding-left-20">
                                             <? $aprobado ='';
                                             if($nomina['status']=='A')
                                                $aprobado='<label class=" label label-danger" style=" padding: 10px 10px;">No</label>'; 
                                             else 
                                                $aprobado='<label class=" label label-primary" style=" padding: 10px 10px;">Si</label>';
                                            ?>
                                             <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Aprobada : </label>
                                                <div class="col-md-8">
                                                    <? echo $aprobado;?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left"><?php echo $termino; ?></label>
                                                <div class="col-md-8">
                                                	<input type="text" name="txtnomina" id="txtnomina" class="form-control input-sm input-medium" value="<?php echo $cod_nomina; ?>">
                                                	
                                                    <!--
													<div class="input-inline input-medium">
														<div class="input-group">
															<input type="text" id="txtnomina" name="txtnomina" class="form-control input-sm" value="<?php echo $cod_nomina; ?>">

															<span class="input-group-btn">
																<button type="button" class="btn blue btn-sm" 
																        onclick="javascript: mostrarNomina('<?php echo $_SESSION['codigo_nomina']; ?>');">
			                                 							<i class="fa fa-search"></i> Mostrar</button>
															</span>
														</div>	
													</div>
													<span class="help-inline">
														<a href="javascript: buscarNomina();" title="Listar <?php echo $termino; ?>s Disponibles">
															<i class="fa fa-file-text-o"></i>
						                				</a>
													</span>
                                                    -->
                                                </div>
                                            </div>

                                           

                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Ficha</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="textfield" id="textfield" class="form-control input-sm input-medium" value="<?php echo $personal['ficha']; ?>" readonly="true">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Nombre</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="txtnombre" id="txtnombre" class="form-control input-sm input-medium" 
                                                           value="<?php echo $personal['apenom'];?>" readonly="true">
                                                </div>
                                            </div>       
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">C&eacute;dula</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="txtcedula" id="txtcedula" class="form-control input-sm input-medium" value="<?php echo $personal['cedula'];?>" readonly="true">
                                                </div>
                                            </div>   
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Sueldo</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="sueldo" id="sueldo" class="form-control input-sm input-medium" value="<?php echo $personal['suesal']; ?>" readonly="true">
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Nivel Funcional</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="niv_funcional" id="niv_funcional" class="form-control input-sm input-medium" value="<?php echo $personal['codnivel1']; ?>" readonly="true">
                                                </div>
                                            </div>  
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Fecha de Ingreso</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="fec_ing" id="fec_ing" class="form-control input-sm input-medium" value="<?php echo $personal['fecing']; ?>" readonly="true">
                                                </div>
                                            </div>        
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Cargo</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="cargo" id="cargo" class="form-control input-sm input-medium" value="<?php echo $personal['cargo'];?>" readonly="true">
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Situaci&oacute;n</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="situacion" id="situacion" class="form-control input-sm input-medium" value="<?php echo $personal['estado']; ?>" readonly="true">
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Datos de Vacaciones</label>
                                                <div class="col-md-6 text-center">
                                                   <a class="btn btn-md blue" data-toggle="modal" href="#basic" id="boton_vac"> Vacaciones </a>
                                                </div>
                                            </div> 


                                            <!--<div class="form-group">
                                                <label class="col-md-4 control-label text-left">Datos de Vacaciones</label>
                                                <div class="col-md-6 text-center">
                                                   <a class="btn btn-md blue" data-toggle="modal" href="#basic" id="boton_vac"> Cobra y continúa laborando </a>
                                                </div>
                                            </div> 
                                            -->
                                            <?$sql = "SELECT continua_laborando, ultimo_ingreso
                                                                    FROM   nom_movimientos_nomina 
                                                                    WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codnom='{$cod_nomina}' AND ficha='{$ficha}' AND codcon=114";
                                                                    $db = new Database($_SESSION['bd']);


                                            $res = $db->query($sql);
                                            $checked_continua_laborando=''; 
                                            $checked_ultimo_ingreso ='';
                                            if( $fila = $res->fetch_assoc() ) {
                                                if($fila['continua_laborando']==1)
                                                    $checked_continua_laborando='checked="checked"';
                                                if($fila['ultimo_ingreso']==1) 
                                                    $checked_ultimo_ingreso ='checked="checked"';
                                            }

                                            ?>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left" >Cobra y continúa laborando</label>
                                                <div class="mt-checkbox-list">
                                                    <label class="mt-checkbox mt-checkbox-outline">
                                                        <input type="checkbox"  id="continua_laborando" name="continua_laborando" <?=$checked_continua_laborando?>>
                                                        <span></span>
                                                    </label>                                                    
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">&Uacute;ltimo ingreso del año</label>
                                                <div class="mt-checkbox-list">
                                                    <label class="mt-checkbox mt-checkbox-outline">
                                                        <input type="checkbox" id="ultimo_ingreso" name="ultimo_ingreso" <?=$checked_ultimo_ingreso?> >
                                                        <span></span>
                                                    </label>                                                    
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-4 control-label text-left">Observaci&oacute;n</label>
                                                <div class="col-md-8">
                                                    <textarea name="situacion" id="situacion" class="form-control input-sm input-medium" value="<?php echo $personal['estado']; ?>" readonly="true"><?= $observacion  ?></textarea>
                                                </div>
                                            </div>



										</div>
                                        <div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                                        <h4 class="modal-title">Vacaciones</h4>
                                                    </div>
                                                    <div class="modal-body"> <div id="modal_vacaciones"></div></div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-md red" data-dismiss="modal">Cerrar</button>
                                                        <?php if($_SESSION['planilla_vacaciones_detalle_agregar']):?>
                                                        <button type="button" class="btn btn-md blue" id="guardar_vac">Guardar</button>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <!-- /.modal-content -->
                                            </div>
                                            <!-- /.modal-dialog -->
                                        </div>
                                        <!-- /.modal -->
										<div class="col-md-3 text-center" style="padding-left: 5px;">
											<?php                                                
                                                $no_disponible = '../../includes/assets/img/profile/no_disponible.png';
                                                $no_disponible = 'fotos/silueta.gif';

												$foto = ( $personal['foto']!='' && file_exists($personal['foto']) ) ? $personal['foto'] : $no_disponible ;
											?>
											<img src="<?php echo $foto; ?>" class="img-responsive img-thumbnail" alt="Foto" style="height: 200px; width: 200px" />

											<div class="text-center margin-top-20 margin-bottom-20">
												<a href="<?php echo $foto; ?>" class="btn btn-sm btn-default fancybox"><i class="fa fa-search"></i> Ampliar</a>
                                                   <!-- onClick="verFoto();" -->
											</div>	
											
										</div>
										<div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-5 control-label">Asignaciones</label>
                                                <div class="col-md-7 padding-right-5">
                                                    <input type="text" name="txtasignaciones" id="txtasignaciones" class="form-control text-right" value="0,00" readonly="true"
                                                           style="color: #428bca;">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-md-5 control-label">Deducciones</label>
                                                <div class="col-md-7 padding-right-5">
                                                    <input type="text" name="txtdeducciones" id="txtdeducciones" class="form-control text-right" value="0,00" readonly="true"
                                                           style="color: #DD1144;">
                                                </div>
                                            </div>
                                            <div class="form-group"> 
                                                <label class="col-md-5 control-label">Neto</label>
                                                <div class="col-md-7 padding-right-5">
                                                    <input type="text" name="txtneto" id="txtneto" class="form-control text-right" value="0,00" readonly="true" 
                                                           style="color: #428bca;">
                                                </div>
                                            </div>	                                            	                                            											
										</div>
									</div>

									<h4 class="block"></h4>

									<div class="tabbable-custom">
										<ul class="nav nav-tabs">
											<li class="active">
												<a href="#tab_1" data-toggle="tab">
													Conceptos Imprimibles
												</a>
											</li>
											<li>
												<a href="#tab_2" data-toggle="tab">
													Conceptos No Imprimibles
												</a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tab_1">

												<div class="table-responsive" style="padding-top: 10px">						
													<table class="table table-striped table-hover"><!-- table-bordered -->
														<thead>
															<tr>
																<th class="text-center">Concepto</th>
																<th class="text-center">Descripci&oacute;n</th>
																<th class="text-center">Referencia</th>
																<th class="text-center">Unidad</th>
																<th class="text-right">Asignaciones</th>
																<th class="text-right">Deducciones</th>
																<th class="text-right">Patronales</th>
																<th class="text-center"></th>
																<th class="text-center"></th>
															</tr>
														</thead>
														<tbody>
										                <?php 
										                	$band = false;

										                    $sql = "SELECT id, codcon, descrip, tipcon, monto, valor, unidad 
										                            FROM   nom_movimientos_nomina 
										                            WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codnom='{$cod_nomina}' AND ficha='{$ficha}' "
                                                                                                           . "ORDER BY codcon ASC";
                                                                    $db = new Database($_SESSION['bd']);


										                    $res = $db->query($sql);

										                    if( $res->num_rows > 0 )
										                    {  
										                    	$monto_asig = $monto_ded = 0; 

										                        while( $fila = $res->fetch_assoc() )
										                        { 
										                            $sql1  = "SELECT impdet, verref FROM nomconceptos WHERE codcon='{$fila['codcon']}'";
                                                                    $db = new Database($_SESSION['bd']);

										                            $fila1 = $db->query($sql1)->fetch_assoc();
										                                
										                            if( $fila1['impdet'] == 'S' )
										                            {
										                            	$band = true;

										                                if( $fila['tipcon'] == 'D' )   
										                                    $monto_ded += $fila['monto'];
										                                else if( $fila['tipcon'] == 'A' ) 
										                                    $monto_asig += $fila['monto'];
										                            ?>
											                        <tr>
											                            <td class="text-center"><?php echo $fila['codcon'];  ?></td>
											                            <td><?php echo $fila['descrip']; ?></td>
											                            <td class="text-center"><?php echo ($fila['valor']!=0  &&  $fila1['verref']==1) ? $fila['valor'] : '';  ?></td>
											                            <td class="text-center"><?php echo  $fila['unidad'];  ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'A') ? number_format($fila['monto'], 2, ',', '.') : '';  ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'D') ? number_format($fila['monto'], 2, ',', '.') : '';  ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'P') ? number_format($fila['monto'], 2, ',', '.') : '';  ?></td>
											                            <td class="text-center">
                                                                           
									                                    <a href="javascript: enviar(2, <?php echo $fila['id']; ?>);">
									                                    	<img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16">
									                                    </a>
											                              
											                            </td>
											                            <td class="text-center">
									                                    <a href="javascript: enviar(3, <?php echo $fila['id']; ?>);">
									                                    	<img src="../imagenes/delete.gif" alt="Eliminar" width="16" height="16">
									                                    </a>
											                                
											                            </td>
											                        </tr>
										                            <?php   
										                            }
										                        }
										                        $monto_neto = $monto_asig - $monto_ded;
										                    }
										                    if(!$band)
										                    {
										                        ?> <td colspan="9" class="text-center">No existen Conceptos Imprimibles para esta ficha</td> <?php
										                    }
										                ?>
														</tbody>
													</table>
												</div>

												<?php
													if($band)
													{
													?>
											            <script>
											                document.frmPrincipal.txtasignaciones.value = '<?php echo number_format($monto_asig, 2, ',', '.'); ?>';
											                document.frmPrincipal.txtdeducciones.value  = '<?php echo number_format($monto_ded,  2, ',', '.'); ?>';
											                document.frmPrincipal.txtneto.value         = '<?php echo number_format($monto_neto, 2, ',', '.'); ?>';
											            </script>
													<?php
													}
												?>
											</div>
											<div class="tab-pane" id="tab_2">

												<div class="table-responsive" style="padding-top: 10px">
													<table class="table table-striped"><!-- table-bordered  -->
														<thead>
															<tr>
																<th class="text-center">Concepto</th>
																<th class="text-center">Descripci&oacute;n</th>
																<th class="text-center">Referencia</th>
																<th class="text-center">Unidad</th>
																<th class="text-right">Asignaciones</th>
																<th class="text-right">Deducciones</th>
																<th class="text-right">Patronales</th>
																<th></th>
															</tr>
														</thead>
														<tbody>
										                <?php 
										                	$band = false;

										                    $sql = "SELECT id, codcon, descrip, tipcon, monto, valor, unidad 
										                            FROM   nom_movimientos_nomina 
										                            WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codnom='{$cod_nomina}' AND ficha='{$ficha}' "
                                                                                                            . "ORDER BY codcon ASC";
										                    $res = $db->query($sql);

										                    if( $res->num_rows > 0 )
										                    {   
										                    	$monto_asig2 = $monto_ded2 = 0;

										                        while( $fila = $res->fetch_assoc() )
										                        { 
										                            $sql1  = "SELECT impdet, verref FROM nomconceptos WHERE codcon='{$fila['codcon']}'";
										                            $fila1 = $db->query($sql1)->fetch_assoc();
										                        
										                            if( $fila1['impdet'] == 'N' )
										                            {
										                            	$band = true;
										                            ?>
										                            <tr> 
											                            <td class="text-center"><?php echo $fila['codcon'];  ?></td>
											                            <td><?php echo  $fila['descrip']; ?></td>
											                            <td class="text-center"><?php echo ($fila['valor']!=0  &&  $fila1['verref']==1) ? $fila['valor'] : ''; ?></td>
											                            <td class="text-center"><?php echo  $fila['unidad']; ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'A') ? number_format($fila['monto'], 2, ',', '.') : ''; ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'D') ? number_format($fila['monto'], 2, ',', '.') : ''; ?></td>
											                            <td class="text-right"><?php  echo ($fila['tipcon'] == 'P') ? number_format($fila['monto'], 2, ',', '.') : ''; ?></td>
											                            <td class="text-center"><?php 
											                                if( $nomina['status'] == 'A' )
											                                {
											                                ?>
											                                    <a href="javascript: enviar(3, <?php echo $fila['id']; ?>);">
											                                    <img src="../imagenes/delete.gif" title="Eliminar" alt="Eliminar" width="16" height="16">
											                                    </a>
											                                <?php
											                                }
											                                ?>
											                            </td>
										                            </tr>
										                            <?php   
										                            }
										                        }
										                        $monto_neto2 = $monto_asig2 - $monto_ded2;
										                    }
										                    
										                    if(!$band)
										                    { 
										                        ?> <td colspan="8" class="text-center">No existen Conceptos No Imprimibles para esta ficha</td> <?php 
										                    }
										                ?>
														</tbody>
													</table> 
												</div> 

												<?php
													/*if($band)
													{
													?>
											            <script>
											                document.frmPrincipal.txtasignaciones.value = '<?php echo number_format($monto_asig2, 2, ',', '.'); ?>';
											                document.frmPrincipal.txtdeducciones.value  = '<?php echo number_format($monto_ded2,  2, ',', '.'); ?>';
											                document.frmPrincipal.txtneto.value         = '<?php echo number_format($monto_neto2, 2, ',', '.'); ?>';
											            </script>
													<?php
													}*/
												?>

											</div>
										</div>
									</div>
                                </div>

								<!-- Paginado -->
								<div class="row">
									<?php
										$url    = 'movimientos_nomina_vacaciones';
										$campos = 'codigo_nomina='.$cod_nomina.'&codt='.$codt.'&prestaciones='.$prestaciones.'&vac='.$vac.
												  '&tipo='.$tipob.'&des='.$des; // '&busqueda='.$busqueda
									?>
									<div class="col-md-12 col-sm-12">
										<div class="text-center" style="margin: 0; padding: 0; font-size: 13px"><!-- float: right; -->
											<div class="pagination-panel"> P&aacute;gina 

												<a href="<?php echo "{$url}.php?pagina=".($pagina-1)."&{$campos}"; ?>" class="btn btn-sm default prev" title="Página Anterior">
												<i class="fa fa-angle-left"></i></a>

												<input type="text" id="pagination" name="pagination" class="pagination-panel-input form-control input-mini input-inline input-sm text-center" 
												 	   maxlenght="5" value="<?php echo $pagina; ?>" style="margin: 0 5px;">

												<a href="<?php echo "{$url}.php?pagina=".($pagina+1)."&{$campos}"; ?>" class="btn btn-sm default next" title="Página Siguiente">													
												<i class="fa fa-angle-right"></i></a> de <span class="pagination-panel-total"><?php echo $num_paginas; ?></span>
											</div>
										</div>											
									</div>
								</div>
								<!-- Fin Paginado -->

							    <input type="hidden" name="registro_id"    value="">
							    <input type="hidden" name="opt"            value="">
							    <input type="hidden" name="op"             value=""> 
							    <input type="hidden" name="prestaciones"   value="<?php echo $prestaciones; ?>">
							    <input type="hidden" name="codigo_nomina" id="codigo_nomina"  value="<?php echo $cod_nomina; ?> ">
							    <input type="hidden" name="pagina"         value="<?php echo $pagina; ?>">
							    <input type="hidden" name="codt" id="codt" value="<?php echo $codt; ?>">


                        </div>
                    </div>
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- Modal -->

<!--=====================================
=          Editar Usuario Modal        =
======================================-->
<!-- Modal -->
<div id="modalAgregarObservacion" class="modal fade">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Agregar Observación</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>

			</div>
			<div class="modal-body">

			<!--<form method="post" enctype="multipart/form-data" id="formAgregarObservacion">-->

				<div class = "form-group">
					<label>Observación</label>
					<textarea class="form-control" id="observacion"></textarea>
				</div>

		    </div>
			<!--<input type="hidden" name= "id_local" id="id_local" value="">-->
			<div class="modal-footer justify-content-between">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Salir</button>
				<button type="submit" class="btn btn-primary btnAgregarObservacion" >Guardar</button>
			</div>
			<!--</form>-->
		</div>

	</div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {    
    App.init();
});

$('a.fancybox').fancybox();
</script>
<script>
function abrirVentana(pagina, alto, ancho, left=0, top=0) 
{
    window.open(pagina, 'mainWindow', 'width='+ ancho +', height='+ alto +', left='+ left +', top='+top);
}

// function buscarNomina()
// {
//     abrirVentana('buscar_nomina_pago.php', 660, 700);
// }

function mostrarNomina(tipnom)
{
    var codnom = document.frmPrincipal.txtnomina.value; 
    var ficha  = document.frmPrincipal.textfield.value; 
                                        
    //console.log('codnom: '+codnom+' ficha: '+ficha+' tipnom: '+tipnom);    
    document.frmPrincipal.action = 'movimientos_nomina_vacaciones.php?codigo_nomina=' + codnom + 
                                   '&codt=' + tipnom + '&ficha=' + ficha;
    document.frmPrincipal.submit();     
} 

function enviar(op, id)
{  
	codt   = document.frmPrincipal.codt.value;
	ficha  = document.frmPrincipal.textfield.value;
	nomina = document.frmPrincipal.txtnomina.value;
	pagina = document.frmPrincipal.pagina.value;

    if( op == 1 ) // Agregar
        abrirVentana('movimientos_nomina_pago_agregar.php?ficha=' + ficha + '&nomina=' + nomina + "&pagina2=" + pagina , 660, 700);

    if( op == 2 ) // Editar concepto   
    {   
        abrirVentana("movimientos_nomina_vacaciones_editar.php?nomina="+nomina+"&codt="+codt+"&pagina="+pagina+"&concepto="+id+"&ficha="+ficha+"&accion=modificar",660,700,0);
    	//document.location.href = "movimientos_nomina_pago_editar.php?nomina=" + nomina + "&codt=" + codt + "&pagina=" + pagina + 
        						 //"&concepto=" + id + "&ficha=" + ficha + "&accion=modificar";
    }
    
    if( op == 3 ) // Eliminar concepto
    {   
    	if(confirm("\u00BFEst\u00E1 seguro que desea eliminar este concepto?"))
        {                   
        	document.location.href = "movimientos_nomina_pago_eliminar.php?nomina=" + nomina + "&concepto=" + id + "&ficha=" + ficha+ "&pagina=" + pagina+"&vacaciones=1";
        }       
    } 

    if( op == 5 ) // Borrar
    {   
        if(confirm("\u00BFEst\u00E1 seguro que desea eliminar los conceptos para esta ficha?"))
        {                   
            document.frmPrincipal.registro_id.value = id;
            document.frmPrincipal.op.value = op;
            document.location.href = "movimientos_nomina_pago_eliminar.php?todo=1&nomina=" + nomina + "&concepto=" + id + 
                                     "&ficha=" + ficha + "&pagina=" + pagina;
        }       
    }

    if( op == 6 ) // Generar
    {
        var continua_laborando  = document.getElementById("continua_laborando").checked;
        var ultimo_ingreso      = document.getElementById("ultimo_ingreso").checked;
        console.log(continua_laborando+" "+ultimo_ingreso);
        
        abrirVentana('movimientos_nomina_persona_generar.php?todo=1&ficha=' + ficha + '&nomina=' + nomina + "&pagina2=" + pagina+'&continua_laborando='+continua_laborando+'&ultimo_ingreso='+ultimo_ingreso, 600, 700);
    }  
        

    if( op == 7 ) // Editar
    	abrirVentana('otrosdatos_integrantes.php?txtficha=' + ficha, 600, 700);

    if( op == 8 ) // Recalcular
    {
        var continua_laborando  = document.getElementById("continua_laborando").value;
        var ultimo_ingreso      = document.getElementById("ultimo_ingreso").checked;
        
        abrirVentana('movimientos_nomina_persona_recalcular.php?todo=1&ficha=' + ficha + '&nomina=' + nomina + "&pagina2=" + pagina+'&continua_laborando='+continua_laborando+'&ultimo_ingreso='+ultimo_ingreso+"&nombre="+document.frmPrincipal.txtnombre.value+"&cedula="+document.frmPrincipal.txtcedula.value, 300, 900);
    }  
}

function verFoto()
{
    abrirVentana('mostrar_foto_empleado.php', 360, 390);
}

function verRecibo()
{  
	var ficha         = document.frmPrincipal.textfield.value;
	var codt          = document.frmPrincipal.codt.value;
	var codigo_nomina = document.frmPrincipal.codigo_nomina.value; 

    abrirVentana('rpt_recibo_pago.php?registro_id=' + ficha + '&codt=' + codt + '&codigo_nomina=' + codigo_nomina, 660, 800);
}
</script>
<script>
function paginacion(pagina)
{
	var url = "movimientos_nomina_vacaciones.php?pagina=" + pagina + 
	          "&codigo_nomina=<?php echo $cod_nomina; ?>&codt=<?php echo $codt; ?>" + 
	          "&prestaciones=<?php echo $prestaciones; ?>&vac=<?php echo $vac; ?>";

	document.location.href = url;
}

$( document ).ready(function() {

	$('#pagination').keypress(function(e) {

		var page = $(this).val();
		var code = e.keyCode || e.which;

	 	if(code == 13) 
	 	{
			paginacion(page);
    		return false;
	    }	

	});

	$("#pagination").blur(function(){
		var page = $(this).val();

    	paginacion(page);
    });

	$(".btnObservacion").off("click").on("click",function(){
		$("#modalAgregarObservacion").modal("show");
		$(".btnAgregarObservacion").off("click").on("click", function(ev){
		    ev.preventDefault();
			var observacion = $("#observacion").val();
			var ficha = $("#textfield").val();
			var codnom = $("#codigo_nomina").val();
			var tipnom = $("#codt").val();

			var datos = new FormData();
			datos.append("agregarObservacion", "yes" );
			datos.append("observacion", observacion  );
			datos.append("codnom", codnom  );
			datos.append("tipnom", tipnom  );
			datos.append("ficha", ficha  );
			
			$.ajax({
			url : "./ajax/planilla.ajax.php",
				method: "POST",
				data: datos,
				cache: false,
				contentType : false,
				processData : false,
				dataType : "json" ,
				success: function(resp){
					if(resp["data"] == "1"){
			    		alert("Observación agregada");
			            $("#modalAgregarObservacion").modal("hide");
			        }
		            else
		            {
                		alert("Error al crear la observacion");
                        $("#modalAgregarObservacion").modal("hide");
		            }
		        }
		    });	
		    
		});

	});
   
    $("#boton_vac").click(function(){
        var ficha         = $("#textfield").val();
        var cedula        = $("#txtcedula").val();
        var codt          = $("#codt").val();
        var codigo_nomina = $("#codigo_nomina").val(); 
        var niv_funcional = $("#niv_funcional").val(); 
        console.log(codigo_nomina);

        $.get("ajax/vacaciones_nomina.php",{ficha:ficha, cedula:cedula},function(res)
        {
            $( "#modal_vacaciones").empty();
            $( "#modal_vacaciones").append(res);

            $("#guardar_vac").on('click',function(){
                periodo = new Array();
                $('input[type=checkbox]:checked').each(function() 
                {
                    periodo.push($(this).val());
                });
                $.get("ajax/agregar_vacacion_persona.php",{ficha:ficha,cedula:cedula,codnom:codigo_nomina,tipnom:codt, periodo:periodo}, function(res){
                    if (res) {
                        alert("Se han agregado las vacaciones al colaborador");
                        window.location='movimientos_nomina_vacaciones.php?codigo_nomina='+codigo_nomina+'&codt='+codt+'&vac=1';
                    }
                    else
                    {
                        alert("No se agregaron las vacaciones al colaborador");
                    }
                });
            });
        });
    });
});
</script>
</body>
</html>