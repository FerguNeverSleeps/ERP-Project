<?php
require_once('../lib/database.php');

date_default_timezone_set('America/Panama');

$db = new Database($_SESSION['bd']);

$termino = isset($_SESSION['termino']) ? $_SESSION['termino'] : 'Planilla';

$registro_id = isset($_POST['registro_id']) ? $_POST['registro_id'] : '';
$op_tp       = isset($_POST['op_tp'])       ? $_POST['op_tp']       : '';

if($registro_id!='')
{
	// Editar Registro

	if($op_tp==2)
	{
		$codigo      = isset($_POST['codigo'])      ? $_POST['codigo']      : '';
		$frecuencia  = isset($_POST['frecuencia'])  ? $_POST['frecuencia']  : ''; 
		$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
		$periodo     = isset($_POST['periodo'])     ? $_POST['periodo']     : '';
                $anio_sipe     = isset($_POST['anio_sipe'])     ? $_POST['anio_sipe']     : '';
                $mes_sipe     = isset($_POST['mes_sipe'])     ? $_POST['mes_sipe']     : '';

		$fecha_inicio = DateTime::createFromFormat('d/m/Y', $_POST['fecha_inicio']);
		$mes_inicio   = ($fecha_inicio !== false) ? $fecha_inicio->format('m')     : '';
		$fecha_inicio = ($fecha_inicio !== false) ? $fecha_inicio->format('Y-m-d') : '';

		$fecha_final  = DateTime::createFromFormat('d/m/Y', $_POST['fecha_final']);
		$fecha_final  = ($fecha_final !== false) ? $fecha_final->format('Y-m-d') : '';

		$fecha_pago   = DateTime::createFromFormat('d/m/Y', $_POST['fecha_pago']); 
		$fecha_pago   = ($fecha_pago !== false) ? $fecha_pago->format('Y-m-d') : '';

		$fecha_contab   = DateTime::createFromFormat('d/m/Y', $_POST['fecha_contab']); 
		$fecha_contab   = ($fecha_contab !== false) ? $fecha_contab->format('Y-m-d') : '';

		$anio = date("Y");

		$sql = "UPDATE nom_nominas_pago SET    
		 		codnom      = '{$codigo}',
		 		descrip     = '{$descripcion}',
		 		fechapago   = '{$fecha_pago}',
		 		periodo_ini = '{$fecha_inicio}',
		 		periodo_fin = '{$fecha_final}',
		 		anio        = '{$anio}',
		 		frecuencia  = '{$frecuencia}',
		 		periodo     = '{$periodo}',
		 		anio_sipe   = '{$anio_sipe}',
		 		mes_sipe    = '{$mes_sipe}',
		 		fecha       = '{$fecha_contab}'
				WHERE codnom ='{$registro_id}' AND codtip='{$_SESSION['codigo_nomina']}' AND tipnom='{$_SESSION['codigo_nomina']}'";	
		
		$res = $db->query($sql);				
		
		echo "<script> window.location.href='nomina_de_vacaciones.php'; </script>";
	}

	$sql = "SELECT codnom as codigo, descrip as nombre, DATE_FORMAT(fechapago,'%d/%m/%Y') as fecha_pago,
				   DATE_FORMAT(periodo_ini,'%d/%m/%Y') as periodo_ini, DATE_FORMAT(periodo_fin,'%d/%m/%Y') as periodo_fin,
				   anio, frecuencia, periodo, anio_sipe, mes_sipe , DATE_FORMAT(fecha,'%d/%m/%Y' ) as fecha
	        FROM   nom_nominas_pago 
	        WHERE  codnom='{$registro_id}' 
	        AND    codtip='{$_SESSION['codigo_nomina']}' AND tipnom='{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);

	$planilla = $res->fetch_object();
        $anio_sipe               = $planilla->anio_sipe;
        $mes_sipe                = $planilla->mes_sipe;

                
}
else
{
	// Agregar Registro

	if($op_tp==1)
	{
		$codigo      = isset($_POST['codigo'])      ? $_POST['codigo']      : '';
		$frecuencia  = isset($_POST['frecuencia'])  ? $_POST['frecuencia']  : ''; 
		$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
		$periodo     = isset($_POST['periodo'])     ? $_POST['periodo']     : '';
                $anio_sipe     = isset($_POST['anio_sipe'])     ? $_POST['anio_sipe']     : '';
                $mes_sipe     = isset($_POST['mes_sipe'])     ? $_POST['mes_sipe']     : '';
                
		$fecha_inicio = DateTime::createFromFormat('d/m/Y', $_POST['fecha_inicio']);		
		$fecha_inicio = ($fecha_inicio !== false) ? $fecha_inicio->format('Y-m-d') : '';

		$fecha_final  = DateTime::createFromFormat('d/m/Y', $_POST['fecha_final']);
		$mes_final    = ($fecha_final !== false) ? $fecha_final->format('m')     : '';
		$fecha_final  = ($fecha_final !== false) ? $fecha_final->format('Y-m-d') : '';

		$fecha_pago   = DateTime::createFromFormat('d/m/Y', $_POST['fecha_pago']); 
		$fecha_pago   = ($fecha_pago !== false) ? $fecha_pago->format('Y-m-d') : '';

		$fecha_contab   = DateTime::createFromFormat('d/m/Y', $_POST['fecha_contab']); 
		$fecha_contab   = ($fecha_contab !== false) ? $fecha_contab->format('Y-m-d') : '';

		$anio = date("Y");

		$sql = "INSERT INTO nom_nominas_pago 
		        (codnom, descrip, fechapago, periodo_ini, periodo_fin, anio, mes, frecuencia, periodo, status, codtip, tipnom, anio_sipe, mes_sipe, fecha)
		        VALUES 
		        ('{$codigo}', '{$descripcion}', '{$fecha_pago}', '{$fecha_inicio}', '{$fecha_final}', '{$anio}', '{$mes_final}',
		         '{$frecuencia}', '{$periodo}', 'A', '{$_SESSION['codigo_nomina']}', '{$_SESSION['codigo_nomina']}','{$anio_sipe}','{$mes_sipe}','{$fecha_contab}');";
		$res = $db->query($sql);

		echo "<script> window.location.href='nomina_de_vacaciones.php'; </script>";
	}

	$sql = "SELECT MAX(codnom) + 1 as codigo FROM nom_nominas_pago WHERE codtip='{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);

	$codigo_nuevo = $db->query($sql)->fetch_object()->codigo;
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
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
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

.padding-left-0{ /* En uso */
  padding-left: 0px;
}

.padding-right-20{ /* En uso */
  padding-right: 20px !important;
}

.margin-bottom-0{ /* En uso */
  margin-bottom: 0px !important; 
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

.text-right{
  text-align: right !important;
}

.help-inline {
	padding-top: 7px;
}

.hide-underline{
	text-decoration: none !important;
}

.btn-md {
  padding: 5px 12px; 
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
			<li><a href="nomina_de_vacaciones.php">Planilla de Vacaciones</a></li>
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
						<?php
							if($registro_id=='')
							{
								?> <i class="fa fa-plus"></i> Agregar <?php echo $termino; ?> de Vacaciones <?php
							}
							else
							{
								?> <i class="fa fa-edit"></i> Editar <?php echo $termino; ?> de Vacaciones <?php
							}
							?>
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

						<input type="hidden" name="op_tp" id="op_tp" value="-1">
						<input type="hidden" name="registro_id" id="registro_id" value="<?php echo $registro_id; ?>">

						<div class="form-body">
							<div class="form-group">
							  <label class="control-label col-md-2">Tipo de <?php echo $termino; ?></label>
							  <div class="col-md-10 padding-right-20"><span class="help-inline"><?php echo $_SESSION['nomina']; ?></span></div>
							</div>

							<div class="form-group">
							  <label class="control-label col-md-2">C&oacute;digo</label>
							  <div class="col-md-3 padding-right-20">
								<input type="text" name="codigo" id="codigo" class="form-control" value="<?php  echo (isset($planilla->codigo)) ? $planilla->codigo : $codigo_nuevo; ?>">
							  </div>
							</div>

							<div class="form-group">
							  <label class="control-label col-md-2">Frecuencia</label>
							  <div class="col-md-10 padding-right-20">
							  		<?php
							  			$sql = "SELECT codfre, descrip FROM nomfrecuencias WHERE codfre=8";
							  			$res = $db->query($sql); 
							  		?>
									<select name="frecuencia" id="frecuencia" class="form-control select2me">
									<?php
										while($fila = $res->fetch_object())
									  	{
									  		$selected = ($fila->codfre == $planilla->frecuencia) ? 'selected' : '';

								  			?> <option value="<?php echo $fila->codfre; ?>" <?php echo $selected; ?>><?php echo $fila->descrip; ?></option> <?php
									  	}
									?>
									</select> 
							  </div>
							</div>

							<!--
							<div class="form-group">
								<label class="control-label col-md-2">Per&iacute;odo</label>
								<div class="col-md-10 padding-right-20">
									<select name="periodo" id="periodo" class="form-control select2me">
										<option value="0">Seleccione un per&iacute;odo</option>
									</select> 
								</div>
							</div>
							-->

							<div class="form-group">
								<label class="control-label col-md-2">Fecha de Inicio</label>
								<div class="col-md-2">
									<div class="input-group date date-picker" data-provide="datepicker"><!-- data-date-end-date="0d" -->
										<input type="text" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo isset($planilla->periodo_ini) ? $planilla->periodo_ini : ''; ?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>	
								</div>

							    <label class="control-label col-md-2 text-right">Fecha Final</label>
							    <div class="col-md-2">
									<div class="input-group date date-picker" data-provide="datepicker"><!-- data-date-end-date="0d" -->
										<input type="text" class="form-control" name="fecha_final" id="fecha_final" value="<?php echo isset($planilla->periodo_fin) ? $planilla->periodo_fin : ''; ?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>	
							    </div>

							    <label class="control-label col-md-2 text-right">Fecha de Pago</label>
							    <div class="col-md-2 padding-right-20">
									<div class="input-group date date-picker" data-provide="datepicker"><!-- data-date-end-date="0d" -->
										<input type="text" class="form-control" name="fecha_pago" id="fecha_pago" value="<?php echo isset($planilla->fecha_pago) ? $planilla->fecha_pago : ''; ?>">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>	
							    </div>
							</div>
                                                        
                                                        <div class="form-group">
								<div class="col-md-2 text-right">
                                                                    <label for="txtfechainicio">Mes Aplica:</label>
                                                                  </div>
                                                                  <div class="col-md-2">
                                                                      <select name="mes_sipe" id="mes_sipe" class="form-control">

                                                                                <option value="0" <?php if($mes_sipe==0) echo "selected";?>>Seleccione Mes...</option>
                                                                                <option value="1" <?php if($mes_sipe==1) echo "selected";?>>Enero</option>
                                                                                <option value="2" <?php if($mes_sipe==2) echo "selected";?>>Febrero</option>
                                                                                <option value="3" <?php if($mes_sipe==3) echo "selected";?>>Marzo</option>
                                                                                <option value="4" <?php if($mes_sipe==4) echo "selected";?>>Abril</option>
                                                                                <option value="5" <?php if($mes_sipe==5) echo "selected";?>>Mayo</option>
                                                                                <option value="6" <?php if($mes_sipe==6) echo "selected";?>>Junio</option>
                                                                                <option value="7" <?php if($mes_sipe==7) echo "selected";?>>Julio</option>
                                                                                <option value="8" <?php if($mes_sipe==8) echo "selected";?>>Agosto</option>
                                                                                <option value="9" <?php if($mes_sipe==9) echo "selected";?>>Septiembre</option>
                                                                                <option value="10" <?php if($mes_sipe==10) echo "selected";?>>Octubre</option>
                                                                                <option value="11" <?php if($mes_sipe==11) echo "selected";?>>Noviembre</option>
                                                                                <option value="12" <?php if($mes_sipe==12) echo "selected";?>>Diciembre</option>
                                                                        </select>
                                                                      <span class="help-block">
                                                                        Seleccione Mes</span>

                                                                    </div>

                                                                      <div class="col-md-2 text-right">
                                                                        <label for="txtfechafinal">Año Aplica:</label>
                                                                      </div>
                                                                      <div class="col-md-2">
                                                                     <select name="anio_sipe" id="anio_sipe" class="form-control">
                                                                              <option value="">Seleccione Año...</option>
                                                                              <?php 
                                                                                      function obtener_lista_anios($adelanta)
                                                                                      {
                                                                                          $anios = array();
                                                                                          for($i = date("Y"); $i >= date("Y") - 10; $i--){
                                                                                              $anios[] = array($i, $i + $adelanta);
                                                                                          }
                                                                                          return $anios;
                                                                                      }
                                                                                      $anio_sipe_array = obtener_lista_anios(2);
                                                                                      foreach($anio_sipe_array as $k => $v)
                                                                                      {

                                                                                              $selected_anio="";   
                                                                                              if($v[1]==$anio_sipe)
                                                                                                   $selected_anio="selected";   
                                                                                              echo "<option value='".$v[1]."' $selected_anio>".$v[1]."</option>";
                                                                                      }

                                                                                      ?>

                                                                              <?php ?>
                                                                      </select>
                                                                        <span class="help-block">
                                                                          Seleccione Año
                                                                        </span>
                                                                      </div>
                  
																		<div class="col-md-2 text-right">
																			<label for="fecha_contab">Fecha Contable:</label>
																		</div>
																		<div class="col-md-2">
																			<div class="input-group date date-picker3" data-date-format="dd/mm/yyyy" data-date-viewmode="years">
																			<input class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="fecha_contab" type="text" id="fecha_contab"  value="<?php if ($registro_id!=0){ echo $planilla->fecha; }  ?>" <?php echo $readonly;?> >
																				<span class="input-group-btn">
																				<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
																				</span>
																			</div>
																			<span class="help-block">
																				Seleccione la Fecha Contabilización
																			</span>
																		</div>
                                                        </div>

							<div class="form-group">
							  <label class="control-label col-md-2">Descripci&oacute;n</label>
							  <div class="col-md-10 padding-right-20">
								<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php  echo isset($planilla->nombre) ? $planilla->nombre : $_SESSION['nomina'].' - Vacaciones'; ?>">
							  </div>
							</div>

						</div>
						<div class="form-actions fluid">
						  <div class="col-md-12 text-center">
							<button type="button" class="btn blue btn-md" 
							onclick="javascript: enviar();"><i class="fa fa-check"></i> Aceptar</button>
							<button type="button" class="btn default btn-md"
							onclick="javascript: document.location.href='nomina_de_vacaciones.php';">Cancelar</button>
						  </div>
						</div>
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
<script src="../../includes/assets/scripts/core/app.js"></script>
<script>
jQuery(document).ready(function() {
  App.init();
});
</script>
<script>
$(document).ready(function(){

	$("#frmPrincipal").validate({
		rules: {
			codigo: {
			    required: true,
			    digits:   true
			},
			descripcion: {
				required: true
			},
			fecha_inicio: {
				required: true
			},
			fecha_final: {
				required: true
			},
			fecha_pago: {
				required: true
			}
		},
		messages: {
			codigo: {
				required: "Requerido"
			},
			descripcion: {
				required: "Requerido"
			},
			fecha_inicio: {
				required: "Requerido"
			},
			fecha_final: {
				required: "Requerido"
			},
			fecha_pago: {
				required: "Requerido"
			}
		},
		errorPlacement: function(error, element) {
		    if (element.attr("name") == "fecha_inicio" || element.attr("name") == "fecha_final" || element.attr("name") == "fecha_pago") 
		    	error.insertAfter(element.closest('.input-group')); 
		    else
		    	error.insertAfter(element);
		},
	});

	// Datepicker + jQuery Validate  
	$('.input-group.date').datepicker({
		format: "dd/mm/yyyy",
	    language: 'es',
		autoclose: true
	}).on('changeDate', function(ev){
		var elemento = $(this).children().eq(0).attr("id");
		var _this    = "#"+elemento;
		var _value   = $(_this).val();

		//console.log("Elemento: " + elemento + " _this: " + _this + " _value: " + _value);
		// Elemento: fecha_inicio _this: #fecha_inicio _value: 20/07/2016
		$(_this).valid();

		if(elemento == 'fecha_inicio' || elemento == 'fecha_final')
		{
			actualizarDescripcion();
		}	
	});

	$("#frecuencia").change(function(){
		actualizarDescripcion();
	});
});

function actualizarDescripcion()
{
	nomina     = '<?php echo $_SESSION['nomina']; ?>';
	frecuencia = $("#frecuencia option:selected").text(); //document.frmPrincipal.frecuencia.options[document.frmPrincipal.frecuencia.selectedIndex].text; 
	fecha_ini  = $("#fecha_inicio").val();
	fecha_fin  = $("#fecha_final").val(); 

	$("#descripcion").val( nomina + ' - ' + frecuencia + ' - DEL ' + fecha_ini + ' AL ' + fecha_fin ).valid();
}

function enviar()
{
	validate = $("#frmPrincipal").valid();

	if(! validate){ return false; }

	registro_id = $("#registro_id").val();

	op_tp = (registro_id =='') ? 1 : 2;

	$("#op_tp").val(op_tp);

	document.frmPrincipal.submit();
}
</script>
</body>
</html>