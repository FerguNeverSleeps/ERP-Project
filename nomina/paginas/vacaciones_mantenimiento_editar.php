<?php
require_once('../lib/database.php');

error_reporting(E_ALL);

date_default_timezone_set('America/Panama');

$db            = new Database($_SESSION['bd']);
$buscar        = isset($_REQUEST['buscar'])  ? $_REQUEST['buscar']  : '';
$pagina        = isset($_GET['pagina'])  ? $_GET['pagina']  : '';
$anio          = isset($_GET['anio'])    ? $_GET['anio']    : '';
$ficha         = isset($_GET['ficha'])   ? $_GET['ficha']   : '';
$cedula        = isset($_GET['cedula'])  ? $_GET['cedula']  : '';
$opcion        = isset($_POST['opcion']) ? $_POST['opcion'] : '';
$monto_vac     = isset($_POST['monto_vac']) ? $_POST['monto_vac'] : 0;
$dias_pagados  = isset($_POST['dias_pagados']) ? $_POST['dias_pagados'] : '';
$dias_disfrute = isset($_POST['dias_disfrute']) ? $_POST['dias_disfrute'] : '';
$id_dias_incapacidad = isset($_POST['id_dias_incapacidad']) ? $_POST['id_dias_incapacidad'] : '';
$tipo          = isset($_POST['tipo']) ? $_POST['tipo'] : 0;

if(!$_POST['dias_solic_pdisfrutar'] or $_POST['dias_solic_pdisfrutar']<=0) $_POST['dias_solic_pdisfrutar']=0;

$sql_persona = "SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecing, useruid, usuario_workflow  from nompersonal WHERE ficha = '{$ficha}'";
function fecha_sql($fecha)
{
	$fecha_tmp = explode("/", $fecha);
	return $fecha_tmp[2]."-".$fecha_tmp[1]."-".$fecha_tmp[0];
}
$fila_persona=$db->query($sql_persona)->fetch_assoc();
if( $opcion == 1 )
{
    $sql  = "SELECT ddisfrute, coalesce(id_dias_incapacidad, 0 ) id_dias_incapacidad
             FROM   nom_progvacaciones 
             WHERE  tipooper='DV' AND periodo={$_POST['anio']} AND ficha={$_POST['ficha']} AND ceduda='{$_POST['cedula']}'";
    $fila = $db->query($sql)->fetch_assoc();

    $sql1 = "SELECT ddisfrute , coalesce(id_dias_incapacidad, 0 ) id_dias_incapacidad
             FROM   nom_progvacaciones 
             WHERE  tipooper='DA' AND periodo={$_POST['anio']} AND ficha={$_POST['ficha']} AND ceduda='{$_POST['cedula']}'";
    $fila1 = $db->query($sql1)->fetch_assoc();
    
    if( isset($_POST['ddisfrutados'])  &&  ($fila['ddisfrute'] + $fila1['ddisfrute']) == $_POST['ddisfrutados'] ) {
            $estado = 'Pagado';
    } else {
        $estado = 'Pendiente';
    }

	//$sql_dias_incapacidad = "UPDATE dias_incapacidad SET "
	$fecha_vac   = fecha_sql($_POST['fechavac']) ;
	$fechareivac = fecha_sql($_POST['fechareivac']) ;
	//fechainivac  dias_solic_pdisfrutar
	// $sql_dias_incapacidad = "INSERT INTO dias_incapacidad SET
	// fecha              = '{$fecha_vac}',
	// fecha_vence        = '{$fechareivac}',
	// tiempo             = {$_POST['saldo_dias_pdisfrutar']},
	// dias               = {$_POST['dias_solic_pdisfrutar']},
	// cedula             = '{$_POST['cedula']}',
	// tipo_justificacion = '7',
	// observacion        = 'Dias Vacaciones',
	// cod_user           = '{$fila_persona['nomposicion_id']}',
	// usr_uid            = '{$fila_persona['useruid']}'";
	// $db->query($sql_dias_incapacidad);
	
	// $insert_id="SELECT max(id) as id FROM dias_incapacidad;";
	
	// $ultimo_id = $db->query($insert_id)->fetch_assoc();
	$ultimo_id = $fila['id_dias_incapacidad'];

    if ($tipo == 1) 
    {
		 $sql = "UPDATE nom_progvacaciones SET 
				fechavac              = '{$fecha_vac}',
				fechareivac           = '{$fechareivac}',
				saldo_dias_pdisfrutar = {$_POST['saldo_dias_pdisfrutar']}, 
				dias_vac_disfrute     = {$_POST['dias_vac_disfrute']}, 
				dias_solic_pdisfrutar = {$_POST['dias_solic_pdisfrutar']}, 
				estado                = '{$_POST['estado']}',
				marca                 = '1' ,
				id_dias_incapacidad   = '{$ultimo_id['id']}'
	            WHERE tipooper='DV' 
	            AND periodo={$_POST['anio']} 
	            AND ficha={$_POST['ficha']} 
	            AND ceduda='{$_POST['cedula']}'";
	            $res=$db->query($sql);

	    if( isset($_POST['fechavac']) )
	    {
	        $sql = "UPDATE nompersonal SET 
	                fechavac    = '{$fecha_vac}',
	                fechareivac = '{$fechareivac}'  
	                WHERE ficha={$_POST['ficha']} AND cedula='{$_POST['cedula']}'";
	        $db->query($sql);
	    }
	    if ($res) {
	    	echo "<script>alert('Días de disfrute agregados exitosamente');</script>";
			header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
	    }

	}
	if ($tipo == 2) 
	{
		 $sql = "UPDATE nom_progvacaciones SET 
				fechavac              = '{$fecha_vac}',
				fechareivac           = '{$fechareivac}',
				ddisfrute             = {$_POST['diasvac']}, 
				saldo_vacaciones      = {$_POST['saldo_vacaciones']}, 
				dias_solic_ppagar     = {$_POST['dias_solic_ppagar']}, 
				saldo_dias_pdisfrutar = {$_POST['saldo_dias_pdisfrutar']}, 
				dias_vac_disfrute     = {$_POST['dias_vac_disfrute']}, 
				dias_solic_pdisfrutar = {$_POST['dias_solic_pdisfrutar']}, 
				estado                = '{$_POST['estado']}',
				marca                 = '1' ,
				id_dias_incapacidad   = '{$ultimo_id['id']}'
	            WHERE tipooper='DV' 
	            AND periodo={$_POST['anio']} 
	            AND ficha={$_POST['ficha']} 
	            AND ceduda='{$_POST['cedula']}'";
	            $res=$db->query($sql);
	            if ($res) {
	    	echo "<script>alert('Días pagados y de disfrute agregados exitosamente');</script>";
			header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
	    }
	}
	if ($tipo == 3) 
	{

	
		//echo $dias_pagados," ",$dias_disfrute;exit;
	    $sql = "UPDATE nom_progvacaciones SET 
				fechavac              = '{$fecha_vac}',
				fechareivac           = '{$fechareivac}',
				ddisfrute             = {$_POST['saldo_vacaciones']}, 
				saldo_vacaciones      = {$_POST['saldo_vacaciones']}, 
				dias_solic_ppagar     = {$_POST['dias_solic_ppagar']}, 
				estado                = '{$_POST['estado']}',
				marca                 = '1'  ,
				id_dias_incapacidad   = '{$ultimo_id['id']}'
	            WHERE tipooper='DV' 
	            AND periodo={$_POST['anio']} 
	            AND ficha={$_POST['ficha']} 
	            AND ceduda='{$_POST['cedula']}'";
	    $res=$db->query($sql);

	    if( isset($_POST['fechavac']) )
	    {
	        $sql = "UPDATE nompersonal SET 
	                fechavac    = '{$fecha_vac}',
	                fechareivac = '{$fechareivac}'  
	                WHERE ficha={$_POST['ficha']} AND cedula='{$_POST['cedula']}'";
	        $db->query($sql);
	    }
	    if ($res) {
	    	echo "<script>alert('Días pagados agregados exitosamente');</script>";
			header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
	    }
	 }

	if ($dias_pagados != '' AND $dias_disfrute != '' AND $tipo == 4) 
	{
	
		$sql1 = "UPDATE nom_progvacaciones SET 
				dias_solic_ppagar     = '{$dias_pagados}',	
				ddisfrute             = '{$dias_pagados}',	
				saldo_vacaciones      = '{$dias_pagados}',	
				saldo_dias_pdisfrutar = '{$dias_disfrute}',
				dias_vac_disfrute     = '{$dias_disfrute}', 	
				marca                 = '1',
				estado                = 'Pendiente', 
				tipooper              = 'DV', 
				desoper               = 'Dias Vacaciones', 
				tipnom                = '{$_SESSION['codigo_nomina']}' ,
				id_dias_incapacidad   = '{$ultimo_id['id']}'
				
				WHERE tipooper='DV' 
		            AND periodo={$_POST['anio']} 
		            AND ficha={$_POST['ficha']} 
		            AND ceduda='{$_POST['cedula']}'
				"; 

		$res=$db->query($sql1);
		if ($res) {
	    	echo "<script>alert('Vacaciones acumuladas agregadas exitosamente');</script>";
			header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
	    }
	}

    //echo "<script> window.location.href='vacaciones_mantenimiento.php?pagina={$_POST['pagina']}'; </script>";
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Vacaciones</title>
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
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/css/bootstrap-datepicker3.min.css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
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

@media (min-width: 768px)
{
	.form-horizontal .control-label {
		text-align: left;
	  	padding-left: 40px;
	}
}

.hide-underline{
	text-decoration: none !important;
}

.btn-md {
	padding: 5px 12px; 
}

.margin-top-15{ /* En uso */
	margin-top: 15px !important; 
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
	<div class="page-content">
	  <!-- BEGIN PAGE HEADER-->
	  
	  <div class="row">
		<div class="col-md-12">
		  <h3 class="page-title">Vacaciones</h3>
		  <ul class="page-breadcrumb breadcrumb">
			<li><i class="glyphicon glyphicon glyphicon-sort"></i>
			  <a class="hide-underline">Transacciones</a><i class="fa fa-angle-right"></i>
			</li>
			<li><a class="hide-underline">Vacaciones</a><i class="fa fa-angle-right"></i></li>
			<li><a href="vacaciones_mantenimiento.php?pagina=<?php echo $pagina; ?>">Mantenimiento de vacaciones</a></li>
		  </ul>
		</div>
	  </div>

	  <!-- END PAGE HEADER-->
	  <!-- BEGIN PAGE CONTENT-->
	  <div class="row">
		<div class="col-md-12">
		  <div class="row">
			<div class="col-md-12">
			  <div class="tab-content">
				  <div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-edit"></i> Modificaci&oacute;n de vacaciones
						</div>
					  <!--
					  <div class="actions">
						<a class="btn btn-sm blue active"  onclick="javascript: window.location='../submenu_constancias.php'">
						  <i class="fa fa-arrow-left"></i> Regresar
						</a>
					  </div>
					  -->
					</div>
					<div class="portlet-body form">

					  <form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">
					  	<input type='hidden' name="buscar" value="<?php print $_REQUEST['buscar']?>">
						<div class="form-body">
							<div class="form-group">
								<div class="col-md-4"></div>
							  	<div class="col-md-6">
									<label> Disfrute
							                <input type="radio" value="1" name="tipo" id="tipo1" />
							                <span></span>
							            </label>
							            <label> Disfrute y Pagados
							                <input type="radio" value="2" name="tipo" id="tipo2" checked />
							                <span></span>
							            </label>
							            <label> Pagados
							                <input type="radio" value="3" name="tipo" id="tipo3" />
							                <span></span>
							            </label>
							             <label> Vac. Acumuladas
							                <input type="radio" value="4" name="tipo" id="tipo4" />
							                <span></span>
							            </label>
							  	</div>
							</div>
							<?php
							$sql = "SELECT dpago,dpagob, ddisfrute, DATE_FORMAT(fechavac, '%d/%m/%Y') AS fechavac, DATE_FORMAT(fechareivac, '%d/%m/%Y') AS fechareivac, estado, tipooper, saldo_vacaciones, dias_solic_ppagar, dias_ppagar, dias_vac_disfrute, saldo_dias_pdisfrutar, dias_solic_pdisfrutar, monto_vac
							        FROM   nom_progvacaciones
							        WHERE  ficha={$ficha} AND ceduda='{$cedula}' AND periodo={$anio} 
							        ORDER BY tipooper DESC";
							$res = $db->query($sql);

							$total = $i = 0;

							while( $fila = $res->fetch_assoc() )
							{

							    if( $fila['tipooper'] == "DV" )
							    {
							        $diaspag = $fila['dpagob'];
							        $total  += $fila['ddisfrute'];

							        ?>
							        <div id="fechas">
										<div class="form-group margin-top-15">
											
											<label class="control-label col-md-3">Fecha de salida de vacaciones</label>
											<div class="col-md-3">
												<div class="input-group date date-picker" data-provide="datepicker"> 
													<input type="text" class="form-control" name="fechavac" id="fechavac" value="<?php echo ($fila['fechavac']!='00/00/0000') ? $fila['fechavac'] : ''; ?>">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>	
											</div>
										</div>

										<div class="form-group">
											
											<label class="control-label col-md-3">Fecha de reintegro</label>
											<div class="col-md-3">
												<div class="input-group date date-picker" data-provide="datepicker">
													<input type="text" class="form-control" name="fechareivac" id="fechareivac" value="<?php echo ($fila['fechareivac']!='00/00/0000') ? $fila['fechareivac'] : ''; ?>">
													<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												</div>	
											</div>
										</div>
									</div>
										<div id="pagados">

											<div class="form-group">
												
											  	<label class="control-label col-md-3">Días de vacaciones</label>
											  	<div class="col-md-3">
													<input type="text" name="diasvac" id="diasvac" class="form-control"  value="<?php echo $fila['ddisfrute']; ?>" placeholder="Días de vacaciones"  >
											  	</div>
												
											  	<label class="control-label col-md-3">Saldo vacaciones</label>
											  	<div class="col-md-3">
													<input type="text" name="saldo_vacaciones" id="saldo_vacaciones" class="form-control" value="<?php echo $fila['saldo_vacaciones']; ?>" placeholder="Saldo vacaciones">
											  	</div>
											</div>	
											<div class="form-group">
												
											  	<label class="control-label col-md-3">Días solicitados por pagar</label>
											  	<div class="col-md-3">
													<input type="text" name="dias_solic_ppagar" id="dias_solic_ppagar" class="form-control" value="<?php echo $fila['dias_solic_ppagar']; ?>"  placeholder="Días solicitados por pagar">
											  	</div>
											</div>
										</div>											
										<div id = "disfrute">
											<div class="form-group">
												
											  	<label class="control-label col-md-3">Días vacaciones de disfrute</label>
											  	<div class="col-md-3">
													<input type="text" name="dias_vac_disfrute" id="dias_vac_disfrute" class="form-control"  value="<?php echo $fila['dias_vac_disfrute']; ?>"  placeholder="Días vacaciones disfrutados" >
											  	</div>
												
											  	<label class="control-label col-md-3">Saldo Días de disfrute</label>
											  	<div class="col-md-3">
													<input type="text" name="saldo_dias_pdisfrutar" id="saldo_dias_pdisfrutar" class="form-control" value="<?php echo $fila['saldo_dias_pdisfrutar']; ?>"  placeholder="Saldo de Días por disfrutar">
											  	</div>
											</div>	
											<div class="form-group">
												
											  	<label class="control-label col-md-3">Días solicitados de disfrute</label>
											  	<div class="col-md-3">
													<input type="text" name="dias_solic_pdisfrutar" id="dias_solic_pdisfrutar" class="form-control" value="<?php echo $fila['dias_solic_pdisfrutar']; ?>" placeholder="Días solicitados por disfrutar">
											  	</div>
											</div>	
											

										</div>
										<div class="row">
											&nbsp;	
										</div>
										<div id = "acum">

											<div class="form-group">
												
											  	<label class="control-label col-md-3">Días Pagados</label>
											  	<div class="col-md-3">
													<input type="text" name="dias_pagados" id="dias_pagados" class="form-control" placeholder="Días Pagados" value="<?php echo $fila['dias_solic_ppagar']; ?>">
											  	</div>
											</div>
											<div class="form-group">
												
											  	
											  	<label class="control-label col-md-3">Días de disfrute</label>
											  	<div class="col-md-3">
													<input type="text" name="dias_disfrute" id="dias_disfrute" class="form-control" value="<?php echo $fila['dias_solic_pdisfrutar']; ?>" placeholder="Días de disfrutar">
											  	</div>
											</div>									
												

										</div>
										<div class="row">
											&nbsp;	
										</div>


										<!--	

										<div class="form-group">
											
										  	<label class="control-label col-md-3">Monto</label>
										  	<div class="col-md-3">
												<input type="text" name="monto_vac" id="monto_vac" class="form-control" value="<?php echo $fila['monto_vac']; ?>">
										  	</div>
										</div>	-->



										<div class="form-group">
											
										  	<label class="control-label col-md-3">Estado</label>
										  	<div class="col-md-3">

												<select name="estado" id="estado" class="form-control">
												    <option value="Pendiente" <?php if ($fila['estado'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
												    <option value="Pagado" <?php if ($fila['estado'] == 'Pagado') echo 'selected'; ?>>Pagado</option>
												</select>

										  	</div>
										</div>	

										
										

							        <?php
								    }							
								    elseif( $fila['tipooper'] == "DA" )
								    {
								        ?>
											<div class="form-group margin-top-15">
												
											  	<label class="control-label col-md-3">D&iacute;as de vacaciones adicionales</label>
											  	<div class="col-md-3">
													<input type="text" name="diasvacad" id="diasvacad" class="form-control" value="<?php echo $fila['ddisfrute']; ?>">
											  	</div>
											</div>
								        <?php
								        $total += $fila['ddisfrute'];
								    }
								    elseif( $fila['tipooper'] == "DB" )
								    {
								        ?>
											<div class="form-group margin-top-15">
												
											  	<label class="control-label col-md-3">D&iacute;as de bono</label>
											  	<div class="col-md-3">
													<input type="text" name="diasbono" id="diasbono" class="form-control" value="<?php echo $fila['dpagob']; ?>">
											  	</div>
											</div>								        
								        <?php
								        $estadobono = $fila['estado'];
								    }
								    elseif( $fila['tipooper'] == "DI" )
								    {
								        ?>
											<div class="form-group margin-top-15">
												
											  	<label class="control-label col-md-3">D&iacute;as de incremento de bono</label>
											  	<div class="col-md-3">
													<input type="text" name="diasbonoin" id="diasbonoin" class="form-control" value="<?php echo $fila['dpagob']; ?>">
											  	</div>
											</div>								        
								        <?php        
								    }
								}
								?>	

								<p class="text-center"><strong>Total d&iacute;as de vacaciones: <?php echo $total; ?></strong></p>
								<p class="text-center"><strong>Per&iacute;odo: <?php echo $anio; ?></strong></p>						
						</div>

						<div class="form-actions fluid">
							<div class="col-md-12 text-center">
								<button type="button" class="btn blue btn-md" 
								onclick="javascript: enviar();"><i class="fa fa-check"></i> Aceptar</button>
								<button type="button" class="btn default btn-md"
								onclick="javascript: window.location='vacaciones_mantenimiento.php?buscar=<?php print $_REQUEST['buscar']?>&pagina=<?php echo $pagina; ?>';">Cancelar</button>
							</div>
						</div>

			            <input type="hidden" name="pagina" id="pagina" value="<?php echo $pagina; ?>">
			            <input type="hidden" name="anio"   id="anio"   value="<?php echo $anio; ?>">            
			            <input type="hidden" name="ficha"  id="ficha"  value="<?php echo $ficha; ?>">
			            <input type="hidden" name="cedula" id="cedula" value="<?php echo $cedula; ?>">
			            <input type="hidden" name="total_vac" id="total_vac" value="<?php echo $total; ?>">
			            <input type="hidden" name="opcion" id="opcion" value="1">
			            <input type="hidden" name="id_dias_incapacidad" id="id_dias_incapacidad" value="<?php echo $total; ?>">

					  </form>

					</div>
				  </div>
				
			  </div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/locales/bootstrap-datepicker.es.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {
  App.init();
});
</script>
<script>
$(document).ready(function(){
	$("#pagados").show();
	$("#disfrute").show();
	$("#fechas").show();
	$("#acum").hide();
	$("#diasvac").on('keyup',function(){
		saldo = $('#diasvac').val();
		$('#saldo_vacaciones').empty();
		$('#saldo_vacaciones').val(saldo);
		$("#dias_solic_ppagar").on('keyup',function(){
			dias_solic_ppagar = $('#dias_solic_ppagar').val();
			total_vac = $('#diasvac').val();
			total = total_vac - dias_solic_ppagar;
			console.log(total+" "+total_vac+" "+dias_solic_ppagar);

			if (total < 0) 
			{
				alert("El saldo de días no puede ser negativo");
				$('#dias_solic_ppagar').val(0);

			}
			if (total > total_vac) 
			{
				alert("Los días a disfrutar no deben ser mayores que los días pendientes");
				$('#dias_solic_ppagar').empty();
				$('#dias_solic_ppagar').val(0);

			}
			if(total >= 0 && total <= total_vac)
			{
				console.log("aqui");
				$('#saldo_vacaciones').empty();
				$('#saldo_vacaciones').val(total);
			}		
		});
		$("#dias_solic_pdisfrutar").on('keyup',function(){
			dias_solic_pdisfrutar = $('#dias_solic_pdisfrutar').val();
			total_vac = $('#dias_vac_disfrute').val();
			total = total_vac - dias_solic_pdisfrutar;
			console.log(total+" "+total_vac+" "+dias_solic_pdisfrutar);

			if (total < 0) 
			{
				alert("El saldo de días no puede ser negativo");
				$('#dias_solic_pdisfrutar').val(0);

			}
			if (total > total_vac) 
			{
				alert("Los días a disfrutar no deben ser mayores que los días pendientes");
				$('#dias_solic_pdisfrutar').empty();
				$('#dias_solic_pdisfrutar').val(0);

			}
			if(total >= 0 && total <= total_vac)
			{
				console.log("aqui");
				$('#saldo_dias_pdisfrutar').empty();
				$('#saldo_dias_pdisfrutar').val(total);
			}		
		});
	});
	$("#dias_vac_disfrute").on('keyup',function(){
		saldo = $('#dias_vac_disfrute').val();
		$('#saldo_dias_pdisfrutar').empty();
		$('#saldo_dias_pdisfrutar').val(saldo);
		$("#dias_solic_ppagar").on('keyup',function(){
			dias_solic_ppagar = $('#dias_solic_ppagar').val();
			total_vac = $('#diasvac').val();
			total = total_vac - dias_solic_ppagar;
			console.log(total+" "+total_vac+" "+dias_solic_ppagar);

			if (total < 0) 
			{
				alert("El saldo de días no puede ser negativo");
				$('#dias_solic_ppagar').val(0);

			}
			if (total > total_vac) 
			{
				alert("Los días a disfrutar no deben ser mayores que los días pendientes");
				$('#dias_solic_ppagar').empty();
				$('#dias_solic_ppagar').val(0);

			}
			if(total >= 0 && total <= total_vac)
			{
				console.log("aqui");
				$('#saldo_vacaciones').empty();
				$('#saldo_vacaciones').val(total);
			}		
		});
		$("#dias_solic_pdisfrutar").on('keyup',function(){
			dias_solic_pdisfrutar = $('#dias_solic_pdisfrutar').val();
			total_vac = $('#dias_vac_disfrute').val();
			total = total_vac - dias_solic_pdisfrutar;
			console.log(total+" "+total_vac+" "+dias_solic_pdisfrutar);

			if (total < 0) 
			{
				alert("El saldo de días no puede ser negativo");
				$('#dias_solic_pdisfrutar').val(0);

			}
			if (total > total_vac) 
			{
				alert("Los días a disfrutar no deben ser mayores que los días pendientes");
				$('#dias_solic_pdisfrutar').empty();
				$('#dias_solic_pdisfrutar').val(0);

			}
			if(total >= 0 && total <= total_vac)
			{
				console.log("aqui");
				$('#saldo_dias_pdisfrutar').empty();
				$('#saldo_dias_pdisfrutar').val(total);
			}		
		});
	});
	$("#dias_solic_ppagar").on('keyup',function(){
		dias_solic_ppagar = $('#dias_solic_ppagar').val();
		total_vac = $('#diasvac').val();
		total = total_vac - dias_solic_ppagar;
		console.log(total+" "+total_vac+" "+dias_solic_ppagar);

		if (total < 0) 
		{
			alert("El saldo de días no puede ser negativo");
			$('#dias_solic_ppagar').val(0);

		}
		if (total > total_vac) 
		{
			alert("Los días a disfrutar no deben ser mayores que los días pendientes");
			$('#dias_solic_ppagar').empty();
			$('#dias_solic_ppagar').val(0);

		}
		if(total >= 0 && total <= total_vac)
		{
			console.log("aqui");
			$('#saldo_vacaciones').empty();
			$('#saldo_vacaciones').val(total);
		}		
	});
	$("#dias_solic_pdisfrutar").on('keyup',function(){
		dias_solic_pdisfrutar = $('#dias_solic_pdisfrutar').val();
		total_vac = $('#dias_vac_disfrute').val();
		total = total_vac - dias_solic_pdisfrutar;
		console.log(total+" "+total_vac+" "+dias_solic_pdisfrutar);

		if (total < 0) 
		{
			alert("El saldo de días no puede ser negativo");
			$('#dias_solic_pdisfrutar').val(0);

		}
		if (total > total_vac) 
		{
			alert("Los días a disfrutar no deben ser mayores que los días pendientes");
			$('#dias_solic_pdisfrutar').empty();
			$('#dias_solic_pdisfrutar').val(0);

		}
		if(total >= 0 && total <= total_vac)
		{
			console.log("aqui");
			$('#saldo_dias_pdisfrutar').empty();
			$('#saldo_dias_pdisfrutar').val(total);
		}		
	});



	// Datepicker + jQuery Validate  
	$('.input-group.date').datepicker({
		format: "dd/mm/yyyy",
	    language: 'es',
		autoclose: true
	});
	$("#tipo1").on('click',function(){
		tipo=$("#tipo1").val();
		console.log(tipo);
		$("#pagados").hide();
		$("#disfrute").show();
		$("#acum").hide();
		$("#fechas").show();

	});
	$("#tipo2").on('click',function(){
		tipo=$("#tipo2").val();
		console.log(tipo);
		$("#pagados").show();
		$("#disfrute").show();
		$("#acum").hide();
		$("#fechas").show();
	});
	$("#tipo3").on('click',function(){
		tipo=$("#tipo3").val();
		console.log(tipo);
		$("#fechas").show();
		$("#pagados").show();
		$("#disfrute").hide();
		$("#acum").hide();
	});
	$("#tipo4").on('click',function(){
		tipo=$("#tipo4").val();
		console.log(tipo);
		$("#pagados").hide();
		$("#disfrute").hide();
		$("#fechas").hide();
		$("#acum").show();
	});

});

function enviar()
{
	if( $('#fechavac').length ) // Si existe el elemento
	{ 
		// Validar fechas

		var fecha_sal = $("#fechavac").val();
		var fecha_rei = $("#fechareivac").val();

		if(fecha_sal!=''  &&  fecha_rei!='')
		{
			var d1 = fecha_sal.split("/");
			var d2 = fecha_rei.split("/");

			var date1 = new Date(d1[2], d1[1] - 1, d1[0], 0, 0, 0, 0);
			var date2 = new Date(d2[2], d2[1] - 1, d2[0], 0, 0, 0, 0);

			if(date2 < date1)
			{
				alert("\u00A1Error! La fecha de reintegro no puede ser menor a la fecha de salida");
				return false;
			}
		}
		else{
			//if(!fecha_sal) alert("La fecha de salida se encuentra vacia.");
			//else           alert("La fecha de reintegro se encuentra vacia.");
		}
	}

		document.frmPrincipal.submit();
}
</script>
</body>
</html>