<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
//print_r($_SESSION);
?>

<form id="form1" name="form1" method="post" action="reporte_horas_trabajadas.php" class="form-horizontal form-bordered">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-xs-offset-3 col-xs-6 col-sm-offset-3 col-sm-6 col-md-offset-3 col-md-6 col-lg-offset-3 col-lg-6">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								HORAS TRABAJADAS
							</div>
							<div class="actions">

								<!--<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45'">
									<i class="fa fa-arrow-left"></i> REGRESAR
								</a>-->

							</div>
						</div>
						<div class="portlet-body form">
							<div class="form-body">

								<div class="row">
									<div class="form-group">

	                              	<label class="col-md-3 control-label" for="fecha_inicio">FECHA INICIO</label>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center">
											<div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="fecha_inicio" type="text"  class="form-control" placeholder="INSERTE FECHA INICIO" id="fecha_inicio" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>

										</div>
									</div>	                                
								</div>
								<div class="row">
									<div class="form-group">

	                              	<label class="col-md-3 control-label" for="fecha_fin">FECHA FIN</label>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center">
											<div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="fecha_fin" type="text"  class="form-control" placeholder="INSERTE FECHA FIN" id="fecha_fin" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>

										</div>
									</div>	                                
								</div>
								<div class="row">
									<div class="form-group">

		                                <label class="col-md-3 control-label" for="txtcodigo">DIRECCIÓN</label>
										<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center">
											<select name="nivel1" id="nivel1" class="form-control">
											<option value="0">TODOS</option>
											<?php
												$query="SELECT * 
												        FROM   nomnivel1 ";
														
												$result=sql_ejecutar($query);
												while ($row = fetch_array($result))
												{
													$codtip = $row['codtip']; // Tabla nomtipos_nomina
													// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
												?><option value="<?php echo $row['codorg'];?>"><?php echo $row['descrip']; ?></option>
												<?php
												}	
											?>
											</select>

										</div>
									</div>
								</div>
								<div class="row">
									<span id="nivel2_div"></span>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
										<input type="hidden" name="codt" id="codt" value="<?= $_SESSION['codigo_nomina']; ?>" >

								</div>
								<div class="row">
			                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" align="center">
										<div class="btn blue" id="desglosar"><i class="fa fa-download"></i> GENERAR REPORTE</div>
			                        </div>
			                    </div>
			                    <div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
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
$(document).ready(function() {

	$("#nivel1").change(function () 
        {
		url="";
		nivel1 = $("#nivel1").val();
		if(nivel1 !== 0 )
		{		
			$.get("ajax/obtenerNivel2.php",{nivel1:nivel1},function(res){
				nivel1     = $("#nivel1").val();
				console.log(nivel1+" - Bien");
				$("#nivel2_div").empty();
				$("#nivel2_div").append(res);
			});
		}
	});	
        
        $("#desglosar").on("click",function()
        {
            fecha_inicio = $("#fecha_inicio").val();
            fecha_fin = $("#fecha_fin").val();
            

            if(fecha_fin=="" || fecha_inicio=="")
            {
                    alert('Por favor, seleccione una fecha');
            }
            else
            {
                    codt    = $("#codt").val();
                    nivel1  = $("#nivel1").val();
                    nivel2  = $("#nivel2").val();
                    //alert(nivel1);
                    
                    if(nivel2==undefined)
                    {
                        nivel2=0;
                    }
                    //alert(nivel2);
                    console.log(fecha_inicio+". "+fecha_fin+": "+nivel1+"> "+nivel2);
                    var url = "reporte_horas_trabajadas.php?nomina_id="+fecha_inicio+"&codt="+fecha_fin+"&nivel1="+nivel1+"&nivel2="+nivel2; // the script where you handle the form input.
                    window.open(url);
                    //window.reload();
            }
        });
});
</script>
</html>
