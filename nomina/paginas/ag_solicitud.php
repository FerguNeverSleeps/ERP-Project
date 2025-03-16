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
    if(isset($_POST['nombre'])) {
        $nombre=$_POST['nombre']; 
        $sql="INSERT INTO `solicitudes_tipos` ( `id_solicitudes_tipos`,`descrip_solicitudes_tipos`) VALUES ('','".$_POST['nombre']."') ";
        $insertado = sql_ejecutar($sql);
        if($insertado) 
        {           
            echo "<script>alert('NUEVA SOLICITUD CREADA EXITOSAMENTE');location.href='config_solicitudes.php';</script>";
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
								Agregar nueva solicitud
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
                                        <div class="float-left" style="margin-right:95%;">
                                            <label for="nombre">Nombre:</label>
                                        </div>
                                        <input type="text" name="nombre" maxlength="60" class="form-control" placeholder="Ingrese nombre de la nueva solicitud" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required>
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
