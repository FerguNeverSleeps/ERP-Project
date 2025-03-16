<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function planilla_forma_pago()
{

	var codnom = document.form1.cboTipoNomina.value; // PK nom_nominas_pago
	var codtip = document.form1.codt.value; // PK nomtipos_nomina
        var tipo_forma_pago = document.form1.tipo_forma_pago.value;
        if(tipo_forma_pago==="00001")
        {
            location.href='../fpdf/reportes/planilla3_forma_pago_efectivo_pdf.php?codnom='+codnom+'&codtip='+codtip; 
        }
        else if(tipo_forma_pago==="00003")
        {
            location.href='../fpdf/reportes/planilla3_forma_pago_ach_pdf.php?codnom='+codnom+'&codtip='+codtip; 
        }
        else
        {
            alert("Forma de Pago no disponible para esta Planilla");
        }
}
</script>
<form id="form1" name="form1" method="post" action="">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class=" col-md-offset-2 col-md-8">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Reporte Forma de Pago - Planilla 3
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  href="submenu_reportes.php?modulo=45">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row"><br>
                                                            <div class="col-md-3 text-right">Planilla 3
                                                            </div>   
								<div class="col-md-7 text-center">
									<select name="cboTipoNomina" id="select2" class="form-control">
									<option value="">Seleccione Planilla</option>
									<?php
										$query="SELECT codnom, descrip, codtip 
										        FROM   nom_nominas_pago WHERE 
										        codtip='".$_SESSION['codigo_nomina']."'
												ORDER BY codnom DESC, codtip ";
												
										$result=sql_ejecutar($query);
										while ($row = fetch_array($result))
										{
											$codtip = $row['codtip']; // Tabla nomtipos_nomina
										?>
											<option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip']; ?></option>
										<?php
										}	
									?>
									</select>
									<input type="hidden" name="codt" id="codt" value="<?php echo $codtip; ?>" >

								</div>
							</div><br>
							<br>
                                                        <div class="row"><br>
                                                            <div class="col-md-3 text-right">Forma de Pago
                                                            </div>   
								<div class="col-md-7 text-center">
									<select name="tipo_forma_pago" id="tipo_forma_pago" id="select2" class="form-control">
									<option value="">Seleccione Forma de Pago</option>
									<?php
										$query="SELECT id, codigo, nombre 
										        FROM   tipo_forma_pago";
												
										$result=sql_ejecutar($query);
										while ($row = fetch_array($result))
										{
											$forma_pago = $row['codigo']; // Tabla nomtipos_nomina
										?>
											<option value="<?php echo $row['codigo'];?>"><?php echo $row['nombre']; ?></option>
										<?php
										}	
									?>
									</select>
									<input type="hidden" name="forma_pago" id="forma_pago" value="<?php echo $forma_pago; ?>" >

								</div>
							</div><br>
							<br>
							<div class="row">
                                                            <div class="col-md-5">
                                                            </div>                    
                                                            <div class="col-md-1">                            
                                                            <div  class="btn blue button-next" onClick="javascript:planilla_forma_pago()">Generar</div>
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


