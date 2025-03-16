<?php 
	session_start();
	ob_start();
?>
<script>
function Enviar(){					
	if (document.frmPrincipal.numBisemana.value==''){
		alert("Debe ingresar un número de Bisemana. Verifique...");}
	if (document.frmPrincipal.fechaInicio.value==''){
		alert("Debe ingresar una fecha inicial. Verifique...");}
	if (document.frmPrincipal.fechaFin.value==''){
		alert("Debe ingresar una fecha final. Verifique...");}

}

</script>
<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
	include("func_bd.php");	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];	
	if ($op_tp==1){
		$fechaInicio = date('Y-m-d',strtotime($_POST[fechaInicio]));
		$fechaFin = date('Y-m-d',strtotime($_POST[fechaFin]));
		$query="INSERT INTO bisemanas 
		(numBisemana,fechaInicio,fechaFin)
		values ('$_POST[numBisemana]','$fechaInicio','$fechaFin')";
		$result=sql_ejecutar($query);	
		activar_pagina("bisemanas_list.php");
	}	
?>
<body class="page-full-width"  marginheight="0">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
					        	Agregar bisemana
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='bisemanas_list.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="1">
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="numBisemana">Número bisemana:</label></div>
									<div class="col-md-3"><input name="numBisemana" type="text" id="numBisemana" pattern="[0-9]+" class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="fechaInicio">Fecha Inicio:</label></div>
									<div class="col-md-4">
                                        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
                                            <input type="text" class="form-control" readonly="" name="fechaInicio" id="fechaInicio">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <!-- /input-group -->
                                        <span class="help-block"> Seleccione una fecha </span>
                                    </div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="fechaFin">Fecha Fin:</label></div>
									<div class="col-md-4">
								        <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
								            <input type="text" class="form-control" readonly="" name="fechaFin" id="fechaFin">
								            <span class="input-group-btn">
								                <button class="btn default" type="button">
								                    <i class="fa fa-calendar"></i>
								                </button>
								            </span>
								        </div>
								        <!-- /input-group -->
								        <span class="help-block"> Seleccione una fecha </span>
								    </div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-offset-4 col-md-1"><?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?></div>
									<div class="col-md-2"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 	include ("../footer4.php");
 ?>
 <script type="text/javascript">
 	
$('.date-picker').datepicker({
        orientation: "left",
        language: 'es',
    autoclose: true
    }); 

 </script>
</body>
</html>