<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
//include ("../header.php");
//include ("func_bd.php");
include ("../paginas/func_bd.php");
$conexion = conexion();

$numpre=$_REQUEST['numpre'];



if(isset($_REQUEST["procesar"])):	
	//actuallizar el encabezado del prestamo
	$sql="update nomprestamos_cabecera set monto='".$_POST["salini1"]."', totpres='".$_POST["salini1"]."', mtocuota='".$_POST["mtocuo1"]."', cuotas='".$_POST["numcuo".$_POST['numcuota']]."' where numpre=$_POST[numpre] and ficha=$_POST[ficha] ";
	$resultado_update_cabecera=query($sql,$conexion);

	//borrar cuotas >= a la primera fecha
	$sql="delete from nomprestamos_detalles where numpre=$_POST[numpre] and ficha=$_POST[ficha] and fechaven>='".fecha_sql($_POST["vence1"])."'";
	$resultado_delete=query($sql,$conexion);

	$observacion=", detalle='".$_POST['observacion']."'";

	$i=1;
	while($i<=$_POST['numcuota'])
	{
		$numcuo="numcuo".$i;
		$vence="vence".$i;
		$cad=explode("/",$_POST[$vence]);
		$salini="salini".$i;
		$mtocuo="mtocuo".$i;
		$salfin="salfin".$i;

		$consulta="INSERT INTO nomprestamos_detalles SET numpre=$_POST[numpre], ficha=$_POST[ficha], numcuo='".$_POST[$numcuo]."', fechaven='".fecha_sql($_POST[$vence])."', anioven=$cad[2], mesven=$cad[1], salinicial=".$_POST[$salini].", montocuo=".$_POST[$mtocuo].", salfinal=".$_POST[$salfin].", estadopre='Pendiente', codnom=$_SESSION[codigo_nomina] ".$observacion;
		$observacion="";
		$resultado2=query($consulta,$conexion);
		$i+=1;
	}
	if($resultado2)
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO GUARDADO EXITOSAMENTE!!!")
		window.location.href="prestamos_list.php"
 		</script>
		<?php	
	}
	else
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO NO GUARDADO !!!")
 		</script>
		<?php	
	}
	exit;
endif;


?>

<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="../imagenes/logo.ico" />
	<title>.: AMAXONIA :.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
	<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
	<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME STYLES -->
	<link href="../../includes/css/components.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->
	<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

	<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />



	<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../lib/jscalendar/calendar.js"></script> 
	<script type="text/javascript" src="../lib/jscalendar/lang/calendar-es.js"></script> 
	<script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script> 
	<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script  language="JavaScript" type="text/javascript" src="../paginas/ewp.js"></script>
	<script  language="JavaScript" type="text/javascript" src="../lib/common.js?1553466731"></script>
	<style type="text/css">
		.texto1 {
			padding: 50px 20px 20px 20px;
		    text-align: center;
		    font-size: large;
		}
		.control-label {
			font-weight: bold;
		}

		.btn-success:hover, .btn-success:focus, .btn-success:active, .btn-success.active {
		    color: #fff !important;
		    background-color: #3b9c96 !important;
		    border-color: #307f7a !important;
		}

		.btn-success {
		    color: #fff !important;
		    background-color: #45B6AF !important;
		    border-color: #3ea49d !important;
		}

		.tb-head {
			display: none;
		}

		#divcuo {
			
		}

		.cabecera_divcuo{
			font-size: 12px;
		}

		.cabecera_divcuo td{
			font-size: 12px !important;
			text-align: left !important;
			padding: 10px;
		}
	</style>
	<script src="../../includes/assets/plugins/moment.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>        
	<script src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="../../includes/assets/scripts/core/app1.js"></script>
	<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
	<script>
	jQuery(document).ready(function() {       
	   App.init();
	  // TableManaged.init();
	 // $('#sample_1').DataTable(); 
	});
	</script>
	<script type="text/javascript">
		window.onload=function(){
			$('select').select2({
	            width: "100%",
	            theme: "bootstrap"
	        });
			
		}		
		$.fn.datepicker.dates.es.format="dd/mm/yyyy";

		function procesar_prestamo(){



			$("#form1").submit();
		}

		function generar_cuotas()
			{
				var numpre=document.getElementById('numpre')
				var ficha=document.getElementById('ficha')
				var monto=document.getElementById('montopre')
				var cuota=document.getElementById('montocuota')


				resultado=monto.value/cuota.value;
				a=resultado.toFixed(2)
				b=resultado.toFixed(0)
				
				b=parseInt(b)
				if(a==b)
				{
					b=parseInt(b)
				}
				else if(a>b)
				{
					b=parseInt(b)+1
				}	

				var numcuota=b;
				document.getElementById('numcuota').value=numcuota;


				var fecha=document.getElementById('fecha1').value;
				fecha=fecha.split("--");
				var fecha_numcuo=fecha[0];
				var fecha1=fecha[1];
				var fecha_monto=fecha[2];

				var division=document.getElementById('divcuo')
				var diciembre=document.getElementById('diciembre')
				var len = document.form1.frededu
				var frededux
				var i

				for (i = 0; i < len.length; i++)
				{
					if ( len[i].checked )
					{
						frededux = len[i].value;
						break;
					}
				}
/*
				if((monto.value=='')||(monto.value==0)||(cuota.value=='')||(cuota.value==0)||(numcuota.value=='')||(numcuota.value==0)||(fecha1.value=='')||(ficha.value=='')||(tipo.value=='')||(frededux==''))
				{
					alert("Complete los campos marcados con asterisco (*)")
					return
				}*/

				var contenido_division=abrirAjax()
				contenido_division.open("GET", "prestamos_calc_cuotas.php?numpre="+numpre.value+"&ficha="+ficha.value+"&montopre="+monto.value+"&montocuota="+cuota.value+"&numcuota="+numcuota+"&fecha1="+fecha1+"&frededu="+frededux+"&diciembre="+diciembre.value, true)
			   	contenido_division.onreadystatechange=function() 
				{
					if (contenido_division.readyState==4)
					{
						//division.parentNode.innerHTML = contenido_division.responseText;
						division.innerHTML = contenido_division.responseText;

						for(var i=0;i<numcuota;i++) {
							$("#numcuo"+(i+1)).val(fecha_numcuo*1+i);
						}


					}
				}
				contenido_division.send(null);
				
			}

	</script>
</head>


<body class="page-header-fixed page-full-width"  marginheight="50">
<?php
//$sql="select idDispositivo, idProyecto, numProyecto, descripcionLarga, cod_dispositivo, nombre from proyectos, reloj_info where idDispositivo=id_dispositivo and idProyecto in (SELECT DISTINCT proyecto FROM nompersonal where estado like 'Activo' and tipnom='$tipo_nomina') order by cod_dispositivo";  
//$proyectos=$db->Execute($sql);
?>
<div id='div_procesar' class="page-container" style="margin-bottom: 50px;">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue" style="width: 100%;">
            <div class="portlet-title">
                <div class="caption">Refinanciar Prestamo</div>
                <div class="actions">
					<a class="btn btn-sm blue" onclick="javascript: window.location='prestamos_list.php'">
						<i class="fa fa-arrow-left"></i> Regresar
					</a>
				</div>
            </div>
            <div class="portlet-body form">
            	<?php
						$consulta = "SELECT np.apenom, npp.descrip, nc.* 
						             FROM   nomprestamos_cabecera nc 
						             JOIN   nompersonal np   ON (np.ficha=nc.ficha) 
						             JOIN   nomprestamos npp ON (npp.codigopr=nc.codigopr) 
						             WHERE  nc.numpre=$numpre";
						$resultado = query($consulta,$conexion);
						$fetch     = fetch_array($resultado);

						//print_r($fetch);
				?>

            	<form id='form1' action="#" method="POST" class="form-horizontal" style="margin-bottom: 0px;" name="form1">
					<div class="form-body">
						<h3 style="font-size: 14px;margin-bottom: 0px;margin-top: 5px;"> <!-- class="form-section" -->
							<div class="row">
								<div class="col-md-3"><strong>N&uacute;mero de pr&eacute;stamo:</strong>&nbsp;&nbsp;<?php echo $fetch['numpre'];?><input type="hidden" name="numpre" id="numpre" value="<?php echo $fetch['numpre'];?>" disabled></div>
								<div class="col-md-2"><strong>Ficha:</strong>&nbsp;&nbsp;<?php echo $fetch['ficha']; ?></div>
								<div class="col-md-3"><strong><?php echo $fetch['apenom'];?></strong></div>
								<div class="col-md-4"><strong>Tipo:</strong>&nbsp;&nbsp;<?php echo $fetch['codigopr']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $fetch['descrip'];?></div>
							</div>
						</h3>
						<input type="hidden" id="numpre" name="numpre" value="<?php print $fetch['numpre'];?>">
						<input type="hidden" id="ficha" name="ficha" value="<?php print $fetch['ficha'];?>">
						<input type="hidden" id="diciembre" name="diciembre" value="<?php print $fetch['diciembre'];?>">
						<input type="hidden" id="numcuota" name="numcuota" value="">
						<input type="hidden" id="procesar" name="procesar" value="1">
						<hr style="margin: 10px 0;">
						<br><br>
						<div class="row">

							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-12" style="text-align: left;">Fecha:</label>
									<div class="col-md-12">
										<!--<div class="input-group date date-picker" data-provide="datepicker">
											<input type="text" class="form-control" name="fecha1" id="fecha1" value="">
											<span class="input-group-btn">
												<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
											</span>
										</div>-->
										<select name="fecha1" id="fecha1">
											<?php
											$sql="SELECT * FROM nomprestamos_detalles where numpre=$numpre order by numcuo";
											$resultado_nomprestamos_detalles = query($sql,$conexion);				
											while($nomprestamos_detalles     = fetch_array($resultado_nomprestamos_detalles)) {
												print "<option value='".$nomprestamos_detalles["numcuo"]."--".fecha($nomprestamos_detalles["fechaven"])."--".$nomprestamos_detalles["salinicial"]."'>".$nomprestamos_detalles["numcuo"]." -- ".fecha($nomprestamos_detalles["fechaven"])." -- ".$nomprestamos_detalles["salinicial"]."</option>";
											}

											?>
										</select>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-12" style="text-align: left;">Monto Saldo:</label>
									<div class="col-md-12">
										<input type="text" id="montopre" class="form-control" > 
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-12" style="text-align: left;">Monto Cuota:</label>
									<div class="col-md-12">
										<input type="text" id="montocuota" class="form-control" value="<?php print $fetch['mtocuota'];?>"> 
									</div>
								</div>
							</div>							

							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label col-md-12" style="text-align: left;">Observacion:</label>
									<div class="col-md-12">
										<textarea class="form-control" id='observacion' name="observacion"></textarea> 
									</div>
								</div>
							</div>

							<div class="col-md-9">
								<div class="form-group">
									<label class="control-label col-md-12" style="text-align: left;">Frecuencia de deduccion:</label>
									<div class="col-md-12">
				  					<label class="radio-inline"><input type="radio" name="frededu" id="frededu" value="1">Semanal</label>
										<label class="radio-inline"><input type="radio" name="frededu" id="frededu" value="2" checked>Quincenal</label>
										<label class="radio-inline"><input type="radio" name="frededu" id="frededu" value="3">Mensual</label>
									</div>
								</div>
							</div>
							<div class="col-md-3">

								<div class='form-group' style="text-align: center;">
									<div class="col-md-12">
										<br>
										<br>
										<button type="button" class="btn btn-success" style="font-size: 18px; font-weight: bold; border-radius: 5px !important;" onclick="generar_cuotas();"><i class="fa fa-calculator" style="font-size: 20px; margin-right: 5px;"></i> Calcular</button>
						            	<br>								
						            	<br>								
									</div>
								</div>
							</div>

						</div>
            	
		            	
						

						<table width="100%" border="0">
						<TR>
						<TD>
						<table width="100%" class="cabecera_divcuo">
							<tr style="font-weight : bold; background-color: #5084A9; color: #EEEEEE;">
							<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="">Saldo fin</TD><td width="20"></td>
							</tr>
						</table>
						<div id="divcuo" style="height: 300px;overflow-y: auto; border: 1px solid #5084a9;"></div>
						</TD>
						</TR>
						</table>

						<div class='form-group' style="text-align: center;">
							<div class="col-md-12">
								<br>
								<br>
								<button type="button" class="btn btn-success" style="font-size: 18px; font-weight: bold; border-radius: 5px !important;" onclick="procesar_prestamo();"><i class="fa fa-gear" style="font-size: 20px; margin-right: 5px;"></i> Procesar</button>
				            	<br>								
				            	<br>								
							</div>
						</div>

					</div>
				</form>
					

            <!-- END PORTLET BODY-->
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

<div id='cargando' style='text-align: center; padding-top: 80px; display: none;'>
	<svg width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling">
        <circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#555555" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(36 50 50)">
          <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
        </circle>
    </svg>
	<br>PROCESANDO<br><small>No cierre hasta culminar el proceso.</small>
</div>

<div class="modal fade" id="modal_confirm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Asignar de Horas - Procesar</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-12">
		            	<div class="form-group">
							<label class="control-label col-md-12">Proyecto Destino: </label>
							<div class="col-md-12">
								<select id="dispositivo_destino">
								<?php
								for($i=0;$i<count($proyectos);$i++){
									print "<option value='".$proyectos[$i]["idDispositivo"]."'> ".$proyectos[$i]["cod_dispositivo"]." ".$proyectos[$i]["nombre"]."</option>";
								}
								?>
								</select>
							</div>
						</div>
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="procesar()">Aceptar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


</body>
</html>