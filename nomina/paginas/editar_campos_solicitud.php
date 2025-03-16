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
    //$d_solicitud=$_SESSION['id'];
    $registro_id=$_POST['registro_id'];
    $tipo_solicitud=$_SESSION['id'];
    $sql="SELECT * FROM solicitudes_campos WHERE id_solicitudes_campos=$registro_id AND solicitudes_tipo_id=$tipo_solicitud";
    $datos = sql_ejecutar($sql);
    if(isset($_POST['nombre_campo'])) {
        $nombre=$_POST['nombre_campo']; 
        $tipo=$_POST['tipo_campo']; 
        $valor=$_POST['valor']; 
        
        $sql="UPDATE solicitudes_campos SET nombre = '$nombre',
        tipo_campo='$tipo',valores_por_Defecto='$valor'
        WHERE solicitudes_tipo_id = $tipo_solicitud AND id_solicitudes_campos=$registro_id";
        
        // $sql="UPDATE `solicitudes_campos` SET `nombre` = '$nombre',
        // `tipo_campo`=$tipo,`valores_por_Defecto`=$valor
        // WHERE `solicitudes_tipo_id` = '$registro_id'";

        // $sql="INSERT INTO `solicitudes_campos` ( `id_solicitudes_campos`,`solicitudes_tipo_id`,`nombre`,`tipo_campo`,`valores_por_Defecto`)
        //  VALUES ('','','".$_POST['nombre_campo']."','".$_POST['tipo_campo']."','".$_POST['valor']."') ";
        $insertado = sql_ejecutar($sql);
        if($insertado) 
        {           
            echo "<script>alert('CAMPOS ACTUALIZADOS EXITOSAMENTE');location.href='config_solicitudes.php';</script>";
        }
        else
        {
            echo "<script>alert('HUBO UN ERROR AL CREAR SU SOLICITUD');location.href='config_solicitudes.php';</script>";     
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
								Editar campos a solicitud
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
                                        <?php
                                            while ($campos=mysqli_fetch_array($datos)) {?>
                                                <!--nombre-->
                                                <div class="float-left" style="margin-right:80%;">
                                                    <label for="nombre">Nombre del campo:</label>
                                                    <input type="text" value="<?php echo $campos['nombre'] ?>" name="nombre_campo" maxlength="60" class="form-control" placeholder="Ingrese valor por defecto que tendra el campo" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required>
                                                    <!-- <input type="text" value="<?php echo $campos['nombre'] ?>" name="nombre_campo" maxlength="60" class="form-control" placeholder="Ingrese nombre del campo de la solicitud" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required> -->
                                                </div>
                                                <!--tipo de campo-->
                                                <div class="float-left" style="margin-right:87%;">
                                                    <label for="tipo_campo">tipo campo :</label>
                                                </div>
                                                  <!-- <input type="text" name="tipo_campo" maxlength="60" class="form-control" placeholder="Ingrese el tipo de campo" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required> -->
                                                <select name="tipo_campo" class="form-control" type="text" id="tipo_solicitud">
                                                <option value="<?php echo $campos['tipo_campo'] ?>"><?php echo $campos['tipo_campo'] ?></option>
                                                    <option value="text">text</option>
                                                    <option value="select">select</option>
                                                    <option value="number">number</option>
                                                    <option value="checkbox">checkbox</option>
                                                    <option value="textarea">textarea</option>                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										
                                                </select>
                                                <!--valor por defecto-->
                                                <div class="float-left" style="margin-right:75%;">
                                                    <label for="valor">valor por defecto(si posee):</label>
                                                </div>
                                                <input type="text" value="<?php echo $campos['valores_por_Defecto'] ?>" name="valor" maxlength="60" class="form-control" placeholder="Ingrese valor por defecto que tendra el campo" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)">
                                                <?php
                                            }
                                        ?>                                   
                                        
                                        
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
                                    <button type="submit" class="btn blue button-next">Actualizar</button>
                                    <input name="registro_id" type="hidden" value="<?php echo $registro_id ?>">
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
