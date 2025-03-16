<?php 
session_start();
ob_start();
?>

<script>

function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 
		document.frmPrincipal.op_tp.value=1}
	else{ 
		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.numBisemana.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar un numero de bisemana. Verifique...");}
	else if (document.frmPrincipal.fechaInicio.value==''){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una fecha de Inicio. Verifique...");}
	else if (document.frmPrincipal.fechaFin.value==''){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una fecha fin. Verifique...");}
}

</script>


<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
	include("func_bd.php");	
	$registro_id =$_POST[registro_id];
	$op_tp       =$_POST[op_tp];
	if ($registro_id=='') 
	{// Si el registro_id es 0 se va a agregar un registro nuevo
		if ($op_tp==1)
		{
			$query="INSERT INTO bisemanas 
			(idBisemanas,fechaInicio,fechaFin)
			values ('$_POST[fechaInicio]','$_POST[fechaFin]',0,0)";
			$result=sql_ejecutar($query);	
			activar_pagina("bisemanas_list.php");
		}
	}
	else 
	{// Si el registro_id es mayor a 0 se va a editar el registro actual		
		$query="select * from bisemanas where idBisemanas='$registro_id'";
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);
		
		$codigo      = $row[idBisemanas];
		$numBisemana = $row[numBisemana];
		$fechaInicio = date('d-m-Y',strtotime($row[fechaInicio]));
		$fechaFin    = date('d-m-Y',strtotime($row[fechaFin]));
	}
	if ($op_tp==2)
	{		
		$fechaInicio = date('Y-m-d',strtotime($_POST[fechaInicio]));
		$fechaFin    = date('Y-m-d',strtotime($_POST[fechaFin]));
		$query       = "UPDATE bisemanas set 
		numBisemana       ='$_POST[numBisemana]',
		fechaInicio       ='$fechaInicio',
		fechaFin          ='$fechaFin'
		where idBisemanas ='$_POST[registro_id]'";	
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
					        	Editar bisemana
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='bisemanas_list.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="2">
								<div class="row">
								&nbsp;
								</div>
  								<input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
								<div class="row">
									<div class="col-md-4 text-right"><label for="fechaInicio">Num. Bisemana</label></div>
									<div class="col-md-3">
									<input class="form-control" name="numBisemana" type="text" id="numBisemana"  onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $numBisemana; }  ?>"></div>
								</div>
								<div class="row">
								&nbsp;
								</div>
								<div class="row">
									<div class="col-md-4 text-right"><label for="fechaInicio">Fecha Inicio:</label></div>
									<div class="col-md-4">
									    <div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy">
									        <input type="text" class="form-control" readonly="" name="fechaInicio" id="fechaInicio" pattern="[0-9]+" value="<?php if ($registro_id!=0){ echo $fechaInicio; }  ?>">
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
								            <input type="text" class="form-control" readonly="" value="<?php if ($registro_id!=0){ echo $fechaFin; }  ?>" name="fechaFin" id="fechaFin">
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