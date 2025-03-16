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
					        	Noticias - Agregar
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='noticias_list.php'">
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
									<div class="col-md-3 text-right"><label for="titulo">Titulo:</label></div>
									<div class="col-md-6"><input name="titulo" type="text" id="titulo"  class="form-control"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><textarea name="descripcion" type="text" id="descripcion"  class="form-control"></textarea></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="inicio">Inicio:</label></div>
									<div class="col-md-6"><input name="inicio" type="text" id="inicio"  class="form-control"></div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="vencimiento">Vencimiento:</label></div>
									<div class="col-md-6"><input name="vencimiento" type="text" id="vencimiento"  class="form-control"></div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="estado">Estado:</label></div>
									<div class="col-md-6">
                                        <select name="estado" type="text" id="estado"  class="form-control">
                                        <option value= "1">Activo</option>
                                        <option value= "0">Inactivo</option>
                                        </select>
                                    </div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
                                                                
								<div class="row">
									&nbsp;
								</div>
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
    $("#inicio").datetimepicker();
    $("#vencimiento").datetimepicker();
function Enviar(){
	
	titulo = $("#titulo").val();
	descripcion = $("#descripcion").val();
    vencimiento = $("#vencimiento").val();
    inicio = $("#inicio").val();
    estado = $("#estado :selected").val();
	sw = 1;

	if (titulo =="") {
		alert("Ingrese el titulo");
		sw=0;
	}
	else{
		if (vencimiento =="") {
			alert("Ingrese cantidad de dias de vencimiento");
			sw=0;
		}
		else{
            var data = {
                titulo:titulo,
                descripcion:descripcion,
                inicio:inicio,
                vencimiento:vencimiento,
                estado:estado,
                op:"agregar"
            }
            $.post("ajax/noticias.ajax.php",data
            ,function(){
                location.href="noticias_list.php";
            });
		}
	}
}
 </script>
</body>
</html>