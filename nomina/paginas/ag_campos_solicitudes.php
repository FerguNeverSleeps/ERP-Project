<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
// function desglose()
// {

// 	var codnom = document.form1.cboTipoNomina.value; // PK nom_nominas_pago
// 	var codtip = document.form1.codt.value; // PK nomtipos_nomina
// 	location.href='horizontal_nomina_vacaciones.php?codnom='+codnom+'&codtip='+codtip; 
// }
</script>
<?php
	$id_solicitud=$_SESSION['id'];
    if(isset($_POST['nombre_campo'])) {
        $nombre=$_POST['nombre_campo']; 
        $tipo=$_POST['tipo_campo']; 
        $valor=$_POST['valor']; 
        $sql="INSERT INTO `solicitudes_campos` ( `id_solicitudes_campos`,`solicitudes_tipo_id`,`nombre`,`tipo_campo`,`valores_por_Defecto`)
         VALUES ('','".$id_solicitud."','".$_POST['nombre_campo']."','".$_POST['tipo_campo']."','".$_POST['valor']."') ";
        $insertado = sql_ejecutar($sql);
        if($insertado) 
        {           
            echo "<script>alert('CAMPO AGREGADO EXITOSAMENTE');location.href='config_solicitudes.php';</script>";
        }
        else
        {
            echo "<script>alert('HUBO UN ERROR AL AGREGAR UN NUEVO CAMPO');location.href='config_solicitudes.php';</script>";     
        }
    }
?>
<form id="form1" name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Agregar campos a solicitud
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  href="config_solicitudes.php">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-12 text-center">
                                    <div class="form-group">                                       
                                        <div class="float-left" style="margin-right:80%;">
                                            <label for="nombre">Nombre del campo:</label>
                                        </div>
                                        <input type="text" name="nombre_campo" maxlength="60" class="form-control" placeholder="Ingrese nombre del campo de la solicitud" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required>
                                        <div class="float-left" style="margin-right:87%;">
                                            <label for="tipo_campo">tipo campo :</label>
                                        </div>
                                        <!-- <input type="text" name="tipo_campo" maxlength="60" class="form-control" placeholder="Ingrese el tipo de campo" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required> -->
										<select name="tipo_campo" class="form-control" type="text" id="tipo_solicitud">
                                        <option value="0">Elegir el tipo de campo:</option>
                                            <option value="text">text</option>
                                            <option value="select">select</option>
											<option value="number">number</option>
											<option value="textarea">textarea</option>
											<option value="checkbox">checkbox</option>											
                                        </select>
                                        <div class="float-left" style="margin-right:75%;">
                                            <label for="valor">valor por defecto(si posee):</label>
                                        </div>
                                        <input type="text" name="valor" maxlength="60" class="form-control" placeholder="Ingrese valor por defecto que tendra el campo" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required>
                                    </div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">&nbsp;</div>
							</div>
							<div class="row">
		                        <div class="col-md-5">
		                        </div>                    
		                        <div class="col-md-1">                            
                                	<!-- <div class="btn blue button-next">Guardar</div> -->
                                    <button type="submit" class="btn blue button-next">Crear</button>
		                        </div>      
                                		                        
		                    </div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</form>

</body>
<?php
include ("../footer4.php");

 ?>
</html>
