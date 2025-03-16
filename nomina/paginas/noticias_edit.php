<?php 
session_start();
ob_start();
?>

<?php 
	include ("../header4.php");
	include("../lib/common.php") ;
	include("func_bd.php");	
	$id=$_POST[id];
	$op_tp=$_POST[op_tp];
	
		
		$query="select * from noticias where id='$id'";		
		$result=sql_ejecutar($query);	
		$row = mysqli_fetch_array ($result);	
		
		$id           = $row[id];	
		$titulo      = $row[titulo];	
		$descripcion = $row[descripcion];
		$fecha_inicio = $row[fecha_inicio];
		$fecha_vencimiento = $row[fecha_vencimiento];
        $activo = $row[estatus] ? "selected":"";
        $inactivo = $row[estatus] ? "":"selected";

	

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
					        	Noticias - Editar
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='noticias_list.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<input name="op_tp" type="Hidden" id="op_tp" value="-1">
  								<input name="id" type="Hidden" id="id" value="<?php echo $_POST[id]; ?>">
								<div class="row">
									&nbsp;
								</div>	
                                                                <div class="row">
									<div class="col-md-3 text-right"><label for="titulo">Titulo:</label></div>
									<div class="col-md-6"><input name="titulo" type="text" id="titulo" pattern="[A-Za-z0-9]+" class="form-control" title="Sólo letras y números." value="<?php if ($id!=0){ echo $titulo; }  ?>"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="descripcion">Descripci&oacute;n:</label></div>
									<div class="col-md-6"><input name="descripcion" type="text" id="descripcion" pattern="[A-Za-z0-9]+" class="form-control" title="Sólo letras y números." value="<?php if ($id!=0){ echo $descripcion; }  ?>"></div>
								</div>
								<div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="fecha_inicio">Inicio:</label></div>
									<div class="col-md-6"><input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" title="Sólo letras y números." value="<?php if ($id!=0){ echo $fecha_inicio; }  ?>"></div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="fecha_vencimiento">Vencimiento:</label></div>
									<div class="col-md-6"><input name="fecha_vencimiento" type="text" id="fecha_vencimiento" class="form-control" title="Sólo letras y números." value="<?php if ($id!=0){ echo $fecha_vencimiento; }  ?>"></div>
								</div>
                                <div class="row">
									&nbsp;
								</div>
								<div class="row">
									<div class="col-md-3 text-right"><label for="estado">Estado:</label></div>
									<div class="col-md-6">
                                        <select name="estado" type="text" id="estado"  class="form-control">
                                        <option value= "1" <?= $activo ?>>Activo</option>
                                        <option value= "0" <?= $inactivo ?>>Inactivo</option>
                                        </select>
                                    </div>
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
 <script type="text/javascript">
     $(document).ready(function(){

    $('#dispositivo').select2();
    $("#fecha_inicio").datetimepicker();
    $("#fecha_vencimiento").datetimepicker();
});
function Enviar(){
	id      = $("#id").val();
	titulo = $("#titulo").val();
	descripcion = $("#descripcion").val();
    fecha_inicio = $("#fecha_inicio").val();
    fecha_vencimiento = $("#fecha_vencimiento").val();
    estado = $("#estado :selected").val();
	sw = 1;

	if (titulo =="") {
		alert("Ingrese el titulo");
		sw=0;
	}
	else{
		if (fecha_vencimiento =="") {
			alert("Ingrese cantidad de dias de fecha_vencimiento");
			sw=0;
		}
		else{
			if (sw){
                var data = {
                    id:id,
                    titulo:titulo,
                    descripcion:descripcion,
                    fecha_inicio:fecha_inicio,
                    fecha_vencimiento:fecha_vencimiento,
                    estado:estado,
                    op:"editar"
                };
                $.post("ajax/noticias.ajax.php",
				data,
				function(){
					alert("Notica editada exitosamente");
					location.href="noticias_list.php";
				});
			}

		}
	}
}
 </script>
</body>
</html>