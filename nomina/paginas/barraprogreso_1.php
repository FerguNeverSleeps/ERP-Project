<?php
session_start();
ob_start();
?>
<?php
include("../lib/common.php");
include ("../header4.php");
include ("func_bd.php");
include ("funciones_nomina.php");
include("../../includes/dependencias.php");


ini_set("memory_limit", "-1");
set_time_limit(0);
?>
<html>
<head>
<title>Generando Planilla</title>

<script type="text/javascript">
function CerrarVentana(){
	javascript:window.close();
}

function Enviar(){
	$('#in-process').show();
	$('#go-process').hide(); 
	$('#td_ok').hide(); 
	$('#td_cerrar').hide();
	$('#mensaje_generar').hide();
	$.blockUI({ message: '<span><img src="../imagenes/loader.gif"></span><br><span>Generando Planilla...</b></span>' });
	//document.getElementById('progressBarMsg').innerHTML='Generando N&oacute;mina, Espere por favor...';
	var registro_id  = $("#registro_id").val();
	var codigo_nomina= $("#codigo_nomina").val();
	$("#progressBar").load("barra_query.php", {registro_id: registro_id, codigo_nomina: codigo_nomina},
		function(response, status, xhr){
			console.log(response);

			$('#td_cerrar').show();
			if(status == "error"){
				$("#progressBar").html('<p>Ha ocurrido un error</p>');
                                $('#boton-aceptar').hide();
			}else{
				$('#boton-aceptar').hide();
				$("#progressBar").html('<div class="alert alert-success"><br><img src="../imagenes/ok.gif" align="absmiddle"> Se ha generado exitosamente la Planilla</div>');
			}
			
		});
	$(document).ajaxStop($.unblockUI);
}
</script>
</head>
<body class="page-full-width">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<form id="frmPrincipal" name="frmPrincipal" method="post" action="">
				<input type="hidden" name="registro_id" id="registro_id" value="<?php echo $_GET['registro_id']; ?>">
				<input type="hidden" name="codigo_nomina" id="codigo_nomina" value="<?php echo $_SESSION['codigo_nomina'];?>">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						<!-- BEGIN EXAMPLE TABLE PORTLET-->
						<div class="portlet box blue">
						
							<div class="portlet-title">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-122">
										Generando planilla de pago
									</div>
								</div>
							</div>
							<div class="portlet-body">
								<div class="row" id="mensaje_generar">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										Presione aceptar para generar la planilla
									</div>
								</div>
								<div class="row text-center">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										<div id="progressBar">
											<div id="in-process" class="center" style="display: none">
												
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
										&nbsp;
									</div>
								</div>
								<div class="row">
                                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 col-xs-offset-5 col-sm-offset-5 col-md-offset-5 col-lg-offset-5 text-center">
                                            <button id="boton-aceptar" type="button" class="btn btn-sm blue active" onclick="javascript:Enviar();">Aceptar</button>&nbsp;
                                             
                                        </div>
                                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 text-center">
                                            <button type="button" class="btn btn-sm red active" onclick="javascript:CerrarVentana();">Salir</button>&nbsp;
                                             
                                        </div>
                                    </div>

							</div>						
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>	

<?php include ("../footer4.php");
?>
</body>
</html>
