<?php 
session_start();
ob_start();
?>

<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
	include("func_bd.php");	
	$registro_id=$_POST[registro_id];
	$cwconcue=$_POST[cwconcue];
	$op_tp=$_POST[op_tp];
	
		
		$query="select * from proyecto_cc where id='$registro_id'";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$codigo           = $row[id];	
		$numProyecto      = $row[codigo];	
		$descripcion = $row[descripcion];
		$cuentaContable = $row[cuenta_contable];
        

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
					        	Editar proyectos
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='proyectos_centro_costo.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="-1">
  								<input name="id" type="Hidden" id="id" value="<?php echo $codigo; ?>">
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="codigo">C&oacute;digo:</label></div>
									<div class="col-md-6">
									<input class="form-control" name="codigo" readonly="readonly" type="text" id="codigo" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $numProyecto; }  ?>" title="Sólo números"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><input name="descripcion" type="text" id="descripcion" pattern="[A-Za-z0-9]+" class="form-control" title="Sólo letras y números." value="<?php if ($registro_id!=0){ echo $descripcion; }  ?>"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
                                                                <div class="row">
									&nbsp;
								</div>
                                <div class="row">
									<div class="col-md-3 text-right"><label for="cuenta_contable">Cuenta Contable</label></div>
									<div class="col-md-6">
<select name="cuenta_contable" id="cuenta_contable" class="form-control select2" >
                                                                                <?php 
                                                                                $query="select id, cuenta, Descrip from cwconcue";
                                                                                $result=sql_ejecutar($query);
                                                                              //ciclo para mostrar los datos
                                                                                while ($row = fetch_array($result))
                                                                                {     
                                                                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                                                                   if($row[cuenta]==$cwconcue)
                                                                                   { 
                                                                                ?>
                                                                                  <option value="<?php echo $row[cuenta];?>" selected > <?php echo $row[cuenta] . ' - '. $row[Descrip];?> </option>
                                                                                  <?php 
                                                                                   }
                                                                                   else // opcion de agregar
                                                                                   { 
                                                                                  ?>
                                                                                  <option value="<?php echo $row[cuenta];?>"> <?php echo $row[cuenta] . ' - '. $row[Descrip];?> </option>
                                                                                    <?php 
                                                                                   } 
                                                                                }//fin del ciclo while
                                                                                    ?>
                                    	</select>									</div>
								</div>
								<div class="row">
									&nbsp;
								</div>


<div class="row">&nbsp;</div>

								<div class="row">
									<div class="col-md-offset-4 col-md-1"><?php boton_metronic('ok','Enviar();',2) ?></div>
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
     $(document).ready(function(){

    $('#cuenta_contable').select2();
});
function Enviar(){
	id      = $("#id").val();
	codigo      = $("#codigo").val();
	descripcion = $("#descripcion").val();
	cuenta_contable = $("#cuenta_contable").val();

	sw = 1;

	if (codigo =="") {
		alert("Ingrese el numero de código");
		sw=0;
	}
	else{
		if (descripcion =="") {
			alert("Ingrese la descripción");
			sw=0;
		}
		else{
			if (sw){
				$.get("proyectos_centro_costo_editar.php",
				{id:id,codigo:codigo,descripcion:descripcion,cuenta_contable:cuenta_contable},
				function(){
					alert("Proyecto editado exitosamente");
					location.href="proyectos_centro_costo.php";
				});
			}

		}
	}
}
 </script>
</body>
</html>
