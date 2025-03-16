<?php 
session_start();
ob_start();
?>
<script>


</script>
<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
        include("func_bd.php");	
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
					        	Agregar Proyectos / Centro De Costo
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='proyectos_centro_costo.php'">
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
									<div class="col-md-3 text-right"><label for="codigo">C&oacute;digo:</label></div>
									<div class="col-md-6"><input name="codigo" type="text" id="codigo"  class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><input name="descripcion" type="text" id="descripcion"  class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="cuenta_contable">Cuenta Contable:</label></div>
									<div class="col-md-6">
										<select name="cuenta_contable" id="cuenta_contable" class="form-control select2" >
                                                                                <?php 
                                                                                $query="select id, cuenta, Descrip from cwconcue";
                                                                                $result=sql_ejecutar($query);
                                                                              //ciclo para mostrar los datos
                                                                                while ($row = fetch_array($result))
                                                                                {     
                                                                                  // Opcion de modificar, se selecciona la situacion del registro a modificar   
                                                                                   if($row[id]==$dispositivo)
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
                                                                          </select>
									</div>
								</div>
							                 
                                <div class="row">
									&nbsp;
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
 <script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>




<script type="text/javascript">
function Enviar(){
	codigo      = $("#codigo").val();
	descripcion = $("#descripcion").val();
	
	cuenta_contable = $("#cuenta_contable").val();
	sw = 1;

	if (codigo =="") {
		alert("Ingrese el numero de proyecto");
		sw=0;
	}
	else{
		if (descripcion =="") {
			alert("Ingrese la descripción");
			sw=0;
		}
		if (descripcion =="") {
			alert("Ingrese la cuenta contable");
			sw=0;
		}
		else{
			$.get("proyectos_centro_costo_buscar.php",{codigo:codigo},function(res){
				console.log(res);
				if(res > 0)
				{
					alert("Numero de código repetido");
					sw=0;
				}
				if (sw){
					$.get("proyectos_centro_costo_agregar.php",
					{codigo:codigo,descripcion:descripcion,cuenta_contable:cuenta_contable},function(){
						alert("Proyecto Agregado exitosamente");
						location.href="proyectos_centro_costo.php";
					});
				}
			});
		}
	}
}
 </script>
</body>
</html>
