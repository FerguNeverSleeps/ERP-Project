<?php
require_once('../lib/database.php');

error_reporting(E_ALL);

$db = new Database($_SESSION['bd']);

$pagina = isset($_GET['pagina'])  ? $_GET['pagina']  : 0;
$tipob  = isset($_GET['tipo'])    ? $_GET['tipo']    : '';
$des    = isset($_GET['des'])     ? $_GET['des']     : '';
$buscar = isset($_REQUEST['buscar']) ? $_REQUEST['buscar'] : '';

$sql = "SELECT pe.apenom AS apenom, pe.cedula AS cedula, pe.ficha AS ficha, DATE_FORMAT(pe.fecing, '%d/%m/%Y') AS fecing, 
               pe.suesal AS suesal, car.des_car AS cargo 
        FROM   nompersonal pe 
        LEFT JOIN nomcargos car ON pe.codcargo=car.cod_car 
        WHERE  tipnom={$_SESSION['codigo_nomina']} and estado <> 'Egresado'";

if( $buscar!=''  ||  $tipob!='' )
{
    if($tipob == '')
        $des = $buscar;

    $sql .= " AND (pe.ficha={$des} OR pe.cedula={$des}) ";
}

$num_filas   = $db->query($sql)->num_rows;
$num_paginas = ceil($num_filas / 1);

if($pagina < 1)
    $pagina = 1;
else
{
    if( $pagina > $num_paginas  &&  $num_paginas != 0 )
        $pagina = $num_paginas;
}

if( $buscar==''  &&  $tipob=='' ) // ! ($buscar!=''  ||  $tipob!='')
{
    $ini  = ($pagina * 1) - 1;
    $sql .= " LIMIT {$ini} , 1 ";  
}


$fila = $db->query($sql)->fetch_assoc();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<head>
<meta charset="utf-8"/>
<title>Mantenimiento de Vacaciones</title>
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
  font-size: 16px;
}

label.error { /* En uso */
	color: #b94a48;
}	

.text-left {
	text-align: left !important;
}

.text-bold-thin {
	font-weight: 600;
}

.padding-left-20 {
	padding-left: 20px;
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
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">Vacaciones</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li class="btn-group">
							<button type="button" class="btn blue" 
							        onclick="javascript: window.location='../fpdf/vacaciones_nomina.php'">
							<span>Imprimir</span> <i class="fa fa-print"></i>
							</button>
						</li>
						<li><i class="glyphicon glyphicon glyphicon-sort"></i>
							<a class="hide-underline">Transacciones</a><i class="fa fa-angle-right"></i>
						</li>
						<li><a href="submenu_vacaciones.php">Vacaciones</a></li>
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
                            <div class="caption">Mantenimiento de vacaciones</div>
                            <div class="actions">
                            	<a class="btn blue active" href="<?php echo 'vacaciones_mantenimiento_agregar.php?buscar='.$_REQUEST['buscar'].'&ficha='.$fila['ficha'].'&cedula='.$fila['cedula'].'&pagina='.$pagina; ?>"
                                    <i class="fa fa-plus"></i> Agregar
                                </a>

                                <a class="btn blue active" onclick="javascript: window.location='submenu_vacaciones.php'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body" id="blockui_portlet_body">       
							<form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">

								<div class="navbar" role="navigation">
									<!--<form class="navbar-form navbar-left" role="search" style="padding-left: 0px">-->

										<div class="col-md-3" style="padding-left: 14px;">
											<div class="input-group">
												<input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar" value="<?php print $_REQUEST['buscar']?>" autocomplete="off">
												<span class="input-group-btn">
													<button type="submit" class="btn blue"><i class="fa fa-search"></i></button>
												</span>
											</div>
										</div>

										<button type="button" class="btn blue" onclick="javascript: window.location='vacaciones_mantenimiento.php'"><!--<i class="fa fa-user"></i> -->Mostrar todo</button>
									<!--</form>-->
								</div>                        

                            <!--<form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">-->

                                <div class="form-body" style="padding-bottom: 0px">

									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-6 text-left text-bold-thin padding-left-20">Ficha:</label>
												<div class="col-md-6">
													<p class="form-control-static"><?php echo $fila['ficha']; ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-5 text-left text-bold-thin">C&eacute;dula:</label>
												<div class="col-md-7">
													<p class="form-control-static"><?php echo $fila['cedula']; ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-3 text-left text-bold-thin">Nombre:</label>
												<div class="col-md-9">
													<p class="form-control-static"><?php echo ucwords(strtolower($fila['apenom'])); ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
									</div>      

									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-6 text-left text-bold-thin padding-left-20">Fecha de ingreso:</label>
												<div class="col-md-6">
													<p class="form-control-static"><?php echo $fila['fecing']; ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-5 text-left text-bold-thin">Sueldo Mensual:</label>
												<div class="col-md-7">
													<p class="form-control-static"><?php echo $fila['suesal']; ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-3 text-left text-bold-thin">Cargo:</label>
												<div class="col-md-9">
													<p class="form-control-static"><?php echo $fila['cargo']; ?></p>
												</div>
											</div>
										</div>
										<!--/span-->
									</div>

									<div class="table-responsive">						
										<table class="table table-bordered table-striped table-hover">
											<thead>
												<tr>
													<th class="text-center">A&ntilde;o</th>
													<th class="text-center">Operaci&oacute;n</th>
													<th class="text-center">Fecha Inicio</th>
													<th class="text-center">Fecha Fin</th>
													<th class="text-center">D&iacute;as Solic. Pagar</th>
													<th class="text-center">Saldo días Vac</th>
													<th class="text-center">Días Disfrute Solic.</th>
													<th class="text-center">Saldo días disfrute</th>
													<th class="text-center">Estado</th>
													<th class="text-center"></th>
													<th class="text-center"></th>
												</tr>
											</thead>
											<tbody>		
												<?php
												    $sql2 = "SELECT periodo, desoper, ddisfrute, DATE_FORMAT(fecha_venc, '%d/%m/%Y') AS fecha_venc, 												     
       																DATE_FORMAT(fechavac, '%d/%m/%Y') AS fechavac, DATE_FORMAT(fechareivac, '%d/%m/%Y') AS fechareivac, estado,
       																tipooper, dpagob , saldo_vacaciones,dias_solic_ppagar,saldo_dias_pdisfrutar,	dias_solic_pdisfrutar
												             FROM   nom_progvacaciones 
												             WHERE  ficha='{$fila['ficha']}' AND ceduda='{$fila['cedula']}'
												             ORDER BY periodo DESC, tipooper DESC";

												    $res2 = $db->query($sql2);

												    $total = $i = $anio2 = $diasdisfrutados = 0;
												    $sql3 = "SELECT periodo
												    		FROM nom_progvacaciones
												    		WHERE ficha='{$fila['ficha']}' AND ceduda='{$fila['cedula']}' AND estado<>'Pagado'";
												    $res3 = $db->query($sql3);

												    $cant =  $res3->num_rows;

												    while($fila2 = $res2->fetch_assoc())
												    {
												        $anio = $fila2['periodo'];

												        if($i==0) // En vacaciones_persona.php la condición es $i==0
												            $anio2 = $anio;

												        

												        $anio2 = $anio;

												        $i++;
													?>
													<tr>
													<td class="text-center"><?php echo $fila2['periodo']; ?></td>
													<td class="text-center"><?php echo $fila2['desoper']; ?></td>
													<td class="text-center"><?php echo $fila2['fechavac']; ?></td>
													<td class="text-center"><?php echo $fila2['fechareivac']; ?></td>
													<td class="text-center"><?php echo ($fila2['dias_solic_ppagar']   != 0) ? $fila2['dias_solic_ppagar']   : '-'; ?></td>
													<td class="text-center"><?php echo ($fila2['saldo_vacaciones']  != 0) ? $fila2['saldo_vacaciones']  : '-'; ?></td>
													<td class="text-center"><?php echo ($fila2['dias_solic_pdisfrutar']  != 0) ? $fila2['dias_solic_pdisfrutar']  : '-'; ?></td>
													<td class="text-center"><?php echo ($fila2['saldo_dias_pdisfrutar']  != 0) ? $fila2['saldo_dias_pdisfrutar']  : '-'; ?></td>
													
													<td class="text-center"><?php echo $fila2['estado']; ?></td>
													<td class="text-center"><a href="<?php echo 'vacaciones_mantenimiento_editar.php?buscar='.$buscar.'&pagina='.$pagina.'&anio='.$anio.'&ficha='.$fila['ficha'].'&cedula='.$fila['cedula']; ?>" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a></td>
													<td class="text-center">
														<?php
														if ($fila2['fechavac'] == "00/00/0000" OR $fila2['fechareivac'] == "00/00/0000") {
															echo " ";
														}
														else
														{
															?>
															<a href="<?php echo '../tcpdf/reportes/resumido_vacaciones.php?anio='.$anio.'&ficha='.$fila['ficha'].'&cedula='.$fila['cedula']; ?>" title="Imprimir"><img src="../../includes/imagenes/icons/report.png" alt="Imprimir" width="16" height="16"></a><?php
														}?>
														
													</td>
												        

													</tr>
											    	<?php
														if( $fila2['tipooper'] == "DA" )
															$total += $fila2['ddisfrute'];			// Días de vacaciones adicionales
														elseif( $fila2['tipooper'] == "DV" )
														{
															$total += $fila2['ddisfrute']; 			// Días de vacaciones
															$diasdisfrutados += $fila2['dpagob'];
														}
											    	}

											    	if($res2->num_rows <= 0)
											    	{
											    	?>
											    		<tr><td colspan="11" class="text-center">No se encontraron datos</td></tr>
											    	<?php
											    	}
											    ?>
											</tbody>
										</table>
									</div>
									<!--/span

									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-8 text-bold-thin">Total d&iacute;as de vacaciones:</label>
												<div class="col-md-4">
													<p class="form-control-static"><?php echo $total; ?></p>
												</div>
											</div>
										</div>
										
										<div class="col-md-5">
											<div class="form-group">
												<label class="control-label col-md-6 text-bold-thin">Total d&iacute;as disfrutados:</label>
												<div class="col-md-6">
													<p class="form-control-static"><?php echo($diasdisfrutados == '') ? $diasdisfrutados=0 : $diasdisfrutados; ?></p>
												</div>
											</div>
										</div>

										<div class="col-md-1">
											<div class="form-group">
												<div class="col-md-10 text-center">
													<a href="<?php echo "../fpdf/vacaciones_persona.php?ficha={$fila['ficha']}&cedula={$fila['cedula']}"; ?>">
            										<img title="Imprimir Persona" src="../imagenes/ico_print.gif" width="20" height="20">
            										</a>
												</div>
											</div>
										</div>

									</div>
										/span-->

									<div class="row">


										<div class="col-md-12 col-sm-12">
											<div style="text-align: center; margin: 0; padding: 0; font-size: 13px"><!-- float: right; -->
												<div class="pagination-panel"> P&aacute;gina 

													<a href="vacaciones_mantenimiento.php?buscar=<?php echo $buscar."&pagina=".($pagina - 1)."&tipo=".$tipob."&des=".$des; ?>" class="btn btn-sm default prev" title="Página Anterior">
													<i class="fa fa-angle-left"></i></a>

													<input type="text" id="pagination" name="pagination" class="pagination-panel-input form-control input-mini input-inline input-sm" 
													 	   maxlenght="5" value="<?php echo $pagina; ?>" style="text-align:center; margin: 0 5px;">

													<a href="vacaciones_mantenimiento.php?buscar=<?php echo $buscar."&pagina=".($pagina + 1)."&tipo=".$tipob."&des=".$des; ?>" class="btn btn-sm default next" title="Página Siguiente">													
													<i class="fa fa-angle-right"></i></a> de <span class="pagination-panel-total"><?php echo $num_paginas; ?></span>
												</div>
											</div>											
										</div>

									</div>

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
<script>
      jQuery(document).ready(function() {    
         App.init();
      });
</script>
<script>
function paginacion(pagina)
{
	var url = "vacaciones_mantenimiento.php?pagina=" + pagina + "&tipo=<?php echo $tipob; ?>&des=<?php echo $des; ?>";

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

});
</script>
<!-- END JAVASCRIPTS -->
</body>
</html>