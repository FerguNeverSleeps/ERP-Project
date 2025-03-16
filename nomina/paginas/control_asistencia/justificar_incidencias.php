<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "utils/funciones_procesar.php";
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$ficha   = (isset($_GET['ficha'])) ? $_GET['ficha']:$_POST['ficha'];
$fecha   = (isset($_GET['fecha'])) ? $_GET['fecha'] : date_format( date_create( $_POST['fecha'] ), 'Y-m-d' );
$mes     = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.*,b.descrip AS direccion,c.descrip AS dpto
	FROM nompersonal AS a
	LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
	LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
	WHERE a.ficha ='$ficha'") or die(mysqli_error($conexion));
$empleado = mysqli_fetch_array($res);
//---------------------------------------------------------------
$res_inc = $conexion->query("SELECT * FROM caa_incidencias") or die(mysqli_error($conexion));
//---------------------------------------------------------------
if (isset($_POST['btn_enviar']))
{
	//Agregar un nuevo registro a la '$ficha'
	$fecha = date_format( date_create($_POST['fecha']), 'Y-m-d');
	
	if (!validar_just($_POST['ficha'],$fecha,$_POST['id_incidencia'],$conexion))
	{
		$res = $conexion->query("INSERT INTO `caa_justificacion`(`ficha`, `fecha`, `id_incidencia`, `justificacion`) VALUES ( '".$_POST['ficha']."', '".$fecha."', '".$_POST['id_incidencia']."', '".$_POST['justificacion']."')") or die(mysqli_error($conexion));
		if ($res) {
			header("location:list_justificaciones.php?ficha=".$ficha."&fecha=".$fecha."&mes=".$mes."&msj=1");
		}else{
			header("location:list_justificaciones.php?ficha=".$ficha."&fecha=".$fecha."&mes=".$mes."&msj=2");
		}
	}else{
		header("location:justificar_incidencias.php?ficha=".$ficha."&fecha=".$fecha."&mes=".$mes."&msj=2");
	}
}
//---------------------------------------------------------------
include("vistas/layouts/header.php");
?>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<img src="../../../nomina/imagenes/21.png" width="22" height="22" class="icon"> Justificar Incidencias
				</div>
				<div class="actions">
					<a class="btn btn-sm blue" href="list_justificaciones.php?ficha=<?php echo $ficha ?>&fecha=<?php echo $fecha ?>&mes=<?php echo $mes ?>">
						<i class="fa fa-arrow-left"></i> Regresar
					</a>
				</div>
			</div>
			<div class="portlet-body">
				<form action="justificar_incidencias.php" method="POST">
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
			  					<label for="usr">Funcionario:</label>
			  					<input type="hidden" name="ficha" id="ficha" value="<?= $ficha ?>">
			  					<input type="text" class="form-control" id="funcionario" name="funcionario" value="<?= $empleado['apenom'] ?>" readonly>
							</div>
						</div>
						<div class="col-sm-3 col-md-3 col-lg-3">
							<div class="form-group">
							<label for="usr">Cedula:</label>
			  					<input type="text" class="form-control" id="cedula" name="cedula" value="<?= $empleado['cedula'] ?>" readonly>
							</div>
						</div>
						<div class="col-sm-3 col-md-3 col-lg-3">
							<div class="form-group">
							<label for="usr">Posicion:</label>
			  					<input type="text" class="form-control" id="posicion" name="posicion" value="<?= $empleado['nomposicion_id'] ?>" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
			  					<label for="usr">Direccion:</label>
			  					<input type="text" class="form-control" id="direccion" name="direccion" value="<?= $empleado['direccion'] ?>" readonly>
							</div>
						</div>
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
			  					<label for="usr">Departamento:</label>
			  					<input type="text" class="form-control" id="dpto" name="dpto" value="<?= $empleado['dpto'] ?>" readonly>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
			  					<label for="usr">Fecha:</label>
  								<div class="form-group">
  					                <div class="input-group date" id="datetimepicker0">
  					                    <input type="text" id="fecha" name="fecha" class="form-control"  required />
  					                    <span class="input-group-addon">
  					                        <span class="glyphicon glyphicon-calendar"></span>
  					                    </span>
  					                </div>
  					            </div>
							</div>
						</div>
						<div class="col-sm-6 col-md-6 col-lg-6">
							<div class="form-group">
			  					<label for="usr">Incidencia:</label>
			  					<select id="id_incidencia" name="id_incidencia" class="form-control">
			  						<option textvalue="0">Seleccione</option>
			  					</select>
							</div>	
						</div>
					</div>
					<div class="form-group">
	  					<label for="usr">Justificacion:</label>
	  					<input type="text" class="form-control" id="justificacion" name="justificacion" value="<?= $permiso['justificacion']?>" placeholder="Justificacion de Incidencia" required>
					</div>
	            	<div align="right">
                        <input class="btn btn-primary" type="submit" id="btn_enviar" name="btn_enviar" value="Registrar">&nbsp;<input type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript: window.location='list_justificaciones.php?ficha=<?php echo $ficha ?>&fecha=<?php echo $fecha ?>&mes=<?php echo $mes ?>'">
                    </div>
				</form>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php include("vistas/layouts/footer.php");?>
<script src="../../../includes/assets/plugins/moment/min/moment-with-locales.js"></script>
<script src="../../../includes/assets/plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="utils/funciones_ajax.js"></script>
<script>
	$(document).ready(function(){
	    //Checkbox
    	$('#datetimepicker0').datetimepicker({
            format: 'DD-MM-YYYY'
        });

	    $("#fecha").blur(function(event){
	        var fecha = $(this);
	        var ficha = $("#ficha");
	        cargarSelect( ficha.val(),fecha.val() );
	    });
	    function fillSelect(data) {
	        $.each(data, function(i, item) {
	            $('#id_incidencia').append('<option value="' + item.id + '" > ' + item.descripcion + '</option>');
	        });
	        
	    }
	    function cargarSelect( ficha, fecha ){
	        $.ajax({
	            url  : 'ajax/server_side/getIncidencias.php',
	            type : 'POST',
	            data : {
	                ficha : ficha,
	                fecha : fecha,
	                ajax : true
	            },
	            dataType : 'json',
	            success : function(response) {
	                $("#id_incidencia").find('option').remove();
	                fillSelect(response['respuesta']);
	            },
	            error : function(xhr, status) {
	                console.log(xhr);
	            },
	            complete : function(xhr, status) {
	            }
	        });
	    }
	    function validarCodigo(cosigo){
	        $.ajax({
	            url  : '../ajax/validar_codigo_permiso.php',
	            type : 'POST',
	            data : {
	                cosigo:cosigo,
	                ajax    : true
	            },
	            dataType : 'json',
	            success : function(response) {
	                // Limpiamos el select
	                $("#submodulo").find('option').remove();
	                fillSelect(response['respuesta']);
	            },
	            error : function(xhr, status) {
	                console.log(xhr);
	            },
	            complete : function(xhr, status) {
	            }
	        });
	    }
	});
</script>
</body>
</html>