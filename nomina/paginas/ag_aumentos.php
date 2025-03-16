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
	
	if (document.frmPrincipal.txtdescripcion.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");}
}

</script>


<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
	include("func_bd.php");	
	
	
	$registro_id=$_POST[registro_id];
	$op_tp=$_POST[op_tp];
	
	if ($registro_id==0) {// Si el registro_id es 0 se va a agregar un registro nuevo
				
		if ($op_tp==1){
		$codigo_nuevo=AgregarCodigo("nomaumentos","codlogro");
				
		$query="insert into nomaumentos 
		(codlogro,descrip,ee)
		values ($codigo_nuevo,'$_POST[txtdescripcion]',0)";
		
		$result=sql_ejecutar($query);	
		activar_pagina("aumentos.php");				
		}
	}
	else {// Si el registro_id es mayor a 0 se va a editar el registro actual		
		
		$query="select * from nomaumentos where codlogro=$registro_id";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$codigo=$row[codlogro];	
		$nombre=$row[descrip];
	}	
		
	if ($op_tp==2){					
		
		$query="UPDATE nomaumentos set codlogro=$registro_id,
		descrip='$_POST[txtdescripcion]'
		where codlogro=$registro_id";	
		
		$result=sql_ejecutar($query);				
		activar_pagina("aumentos.php");										
		{			
	}
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
					        	 <?php
									if ($registro_id==0)
									{
										echo "Agregar Tipo Aumentos / Logros";
									}
									else
									{
										echo "Modificar Tipo Aumentos / Logros";
									}
								?>
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='aumentos.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						
				
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="-1">
								<input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
								<div class="row">
									<div class="col-md-3">C&oacute;digo:</div>
									<div class="col-md-3">
									<input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $codigo; }  ?>"></div>
								</div>
								<div class="row">
									<div class="col-md-3"><label for="txtdescripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><input name="txtdescripcion" type="text" id="txtdescripcion"  class="form-control" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>"></div>
								</div>
								<div class="row">
								&nbsp;
								</div>
								<div class="row">
									<div class="col-md-offset-3 col-md-3"><?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?></div>
									<div class="col-md-6"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
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
</body>
</html>