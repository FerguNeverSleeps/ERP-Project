<?php 
session_start();
ob_start();
error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

 $consulta="SELECT * FROM expediente_tipo ";                        
 $resultado=query($consulta,$conexion);

if( isset($_POST['btn-guardar']) ){
	
	$descripcion = ( isset($_POST['descripcion']) ) ? utf8_decode($_POST['descripcion']) : '';
        $correlativo = ( isset($_POST['correlativo']) ) ? $_POST['correlativo'] : 0;
        $expediente_tipo = ( isset($_POST['expediente_tipo']) ) ? $_POST['expediente_tipo'] : 0;

	if( empty($id) )
	{
		$sql = "INSERT INTO accion_funcionario_tipo (id_accion_funcionario_tipo,nombre_accion,"
                        . "correlativo,id_expediente_tipo) "
                        . "VALUES (NULL,'{$descripcion}','{$correlativo}','{$_POST['expediente_tipo']}')";
	}
	else
	{
		$sql = "UPDATE accion_funcionario_tipo SET 
				nombre_accion  = '{$descripcion}', 
				correlativo = '{$correlativo}', 
                                id_expediente_tipo  =   '{$expediente_tipo}'
				WHERE id_accion_funcionario_tipo=" . $id;
	}

	$res = query($sql, $conexion);

	activar_pagina("tipo_accion_funcionario.php");	 
}


if(isset($_GET['edit']))
{
	$sql = "SELECT * FROM accion_funcionario_tipo WHERE id_accion_funcionario_tipo=" . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		
		$descripcion = $fila['nombre_accion'];
                $correlativo = $fila['correlativo'];
                $expediente_tipo = $fila['id_expediente_tipo'];
                
	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    font-size: 13px;
    font-weight: bold;
    font-family: helvetica, arial, verdana, sans-serif;
    margin-bottom: 3px;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-control {
    border: 1px solid silver;
}

.btn{
	border-radius: 3px !important;
}

.form-body{
	padding-bottom: 5px;
}
</style>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Tipo Accion Funcionario
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Descripción:</label>
										<div class="col-md-4">
											<input type="text" class="form-control input-sm" 
                                                                                               id="descripcion" name="descripcion" value="<?php echo utf8_encode ($descripcion); ?>" required>
										</div>
									</div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label" for="txtcodigo">Tipo Expediente: </label>                            
                                                                            <div class="col-md-4">
                                                                                <select name="expediente_tipo" id="expediente_tipo" class="form-control">
                                                                                   <option value="">Seleccione</option>
                                                                                    <?php                        
                   
                                                                                        while($fila=fetch_array($resultado))
                                                                                        {
                                                                                           
                                                                                            if (!isset($_GET['edit']))
                                                                                            {                        
                                                                                                {?>
                                                                                                    <option  value="<?=$fila['id_expediente_tipo'];?>"><?=utf8_encode($fila['nombre_tipo']);?></option>
                                                                                                <?}

                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                if($expediente_tipo==$fila['id_expediente_tipo'])
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_expediente_tipo'];?>" selected><?=utf8_encode($fila['nombre_tipo']);?></option> 
                                                                                                <?}
                                                                                                else
                                                                                                {?>
                                                                                                     <option  value="<?=$fila['id_expediente_tipo'];?>"><?=utf8_encode($fila['nombre_tipo']);?></option>
                                                                                                <?}         
                                                                                            }

                                                                                        }

                                                                                    ?>
                                                                                
                                                                                </select>
                                                                            </div>
									</div>
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Secuencial:</label>
										<div class="col-md-4">
											<input type="text" class="form-control input-sm" 
                                                                                               id="correlativo" name="correlativo" value="<?php echo utf8_encode ($correlativo); ?>" required>
										</div>
									</div>
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='tipo_accion_funcionario.php'">Cancelar</button>
									
								</div>
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
	$(document).ready(function() {
		$( "#btn-guardar" ).click(function() {
			var cuenta      = $('#cuenta').val();
		    var descripcion = $('#descrip').val();

		    if( cuenta == '' || descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		});
	});
</script>
</body>
</html>