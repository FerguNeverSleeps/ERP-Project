<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function Recibos()
{
    AbrirVentana('../fpdf/reportes/recibos_federal2.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
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
								Reporte Recibos de Pago Planilla 5
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  href="submenu_reportes.php?modulo=45">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row"><br>
		                        <div class="col-md-3 text-right">Planilla 5
		                        </div>   
								<div class="col-md-6 text-center">
									<select name="cboTipoNomina" id="select2" class="form-control">
									<option value="">Seleccione una Planilla</option>
									<?php
										$query="SELECT codnom, descrip, codtip 
										        FROM   nom_nominas_pago WHERE 
										        codtip='".$_SESSION['codigo_nomina']."'
												ORDER BY codnom DESC, codtip ASC";
												
										$result=sql_ejecutar($query);
										while ($row = fetch_array($result))
										{
											$codtip = $row['codtip']; // Tabla nomtipos_nomina
											// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
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
							<div class="row" id="busqueda">
		                        <div class="col-md-3 text-right">Código
		                        </div>    
		                        <div class="col-md-2">
									<input type="number" class="form-control" name="ficha_buscar" id="ficha_buscar" >                                	
		                        </div>   
		                        <div class="col-md-6 text-left">Si desea ver un colaborador, escriba el número de ficha.
		                        </div>
							</div>
							<br>
							<div class="row">
		                        <div class="col-md-5">
		                        </div>
		                        <div class="col-md-1">
                                	<div  class="btn blue button-next" onClick="javascript:Recibos()">Todos</div>
		                        </div>
		                        <div class="col-md-1">
                                	<div  class="btn blue button-next" id="buscar">Buscar</div>
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
 <script type="text/javascript">
 $(document).ready(function()
 {
	 $("#busqueda").show();
	 $("#buscar").on('click',function(){
 
		 let ficha_buscar = $("#ficha_buscar").val();
		 if(ficha_buscar !== "" && ficha_buscar !== null && ficha_buscar>0)
		 {
			 var url = "../fpdf/reportes/recibos_federal2.php?nomina_id="+document.form1.cboTipoNomina.value+"&codt="+document.form1.codt.value+"&ficha_buscar="+ficha_buscar;
			 window.open(url);
		 }
		 else
		 {
			 alert("Por favor, seleccione un rango válido");
		 }
	 });
 
 });
 </script>
</html>


 