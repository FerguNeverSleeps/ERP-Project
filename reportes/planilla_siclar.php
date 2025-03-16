<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

require_once('../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

$sql = "SELECT nomposicion_id,IdDepartamento,apellidos,nombres,cedula,fecnac,fecing,suesal,estado,num_decreto,tipnom,codcargo,nomfuncion_id FROM nompersonal";
$result1 = $db->query($sql, $conexion);
//------------------------------------------------------------
$sql3 = "SELECT * FROM departamento";
$result3 = $db->query($sql3, $conexion);
$i=0;
while($departamentos=mysqli_fetch_array($result3))
{
    $dep[$i]=$departamentos['IdDepartamento'];
	$descripcion_d[$i]=$departamentos['Descripcion'];
	$i++;
}
$total_dep=$i;
//------------------------------------------------------------
$sql4 = "SELECT * FROM nomcargos";
$result4 = $db->query($sql4, $conexion);
$i=0;
while($cargos=mysqli_fetch_array($result4))
{
    $codcargo[$i]=$cargos['cod_car'];
	$desc_car[$i]=$cargos['des_car'];
	$i++;
}
$total_car=$i;
$j=0;
while($j<$total_car)
{
     $codcargo[$j];
     $desc_car[$j];
	 $j++;
}
//------------------------------------------------------------
$sql2 = "SELECT * FROM nomfuncion";
$result2 = $db->query($sql2, $conexion);
$i=0;
while($funcion=mysqli_fetch_array($result2))
{
    $nom_id[$i]=$funcion['nomfuncion_id'];
	$desc_fun[$i]=$funcion['descripcion_funcion'];
	$i++;
}
$total_fun=$i;
$j=0;
while($j<$total_fun)
{
     $nom_id[$j];
     $desc_fun[$j];
	 $j++;
}
//-----------------------------------------------------------
?>
<?php include("../includes/dependencias.php");?>
<style>
tbody{
	font-size: 0.6em;
}
thead{
	font-size: 0.6em;
}
</style>
<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<!-- COMIENZO DEL CONTENIDO-->
		<div class="row">
		    <div class="col-md-12">
		        <!-- COMIENZO DE LA TABLA-->
		        <div class="portlet box blue">
		            <div class="portlet-title">
		                <div class="caption">
		                	<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Planilla SICLAR
		                </div>
		                <div class="pull-right">
                	    	<a class="btn btn-primary" href="resumen_planilla_siclar.php">
                			<i class="fa fa-newspaper-o"></i>
                			Resumen
                			</a>&nbsp;
							<!-- Trigger the modal with a button -->
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
							<i class="fa fa-cloud-download"></i>
							Exportar
							</button>
		                </div>
		            </div>
		            <div class="portlet-body">
		              <table class="table table-striped table-bordered table-hover" id="table_datatable">
		              <thead>
		              <tr>
		                <th>Departamento</th>
		                <th>Planilla</th>
		                <th>Posicion</th>
		                <th>Apellidos</th>
		                <th>Nombres</th>
		                <th>Cedula</th>
		                <th>F. Nacimiento</th>
		                <th>Cargo</th>
		                <th>Funcion</th>
		                <th>F. Inicio</th>
		                <th>Salario</th>
		                <th>Estado</th>
		                <th>Resolucion</th>
		              </tr>
		              </thead>
		              <tbody>
		              <?php
		                $count=0;
		              	$departamento=NULL;
		                while($fila=mysqli_fetch_array($result1))
		                {
							$date = date_create($fila["fecnac"]);
							$nuevofn = date_format($date, "d-m-Y");
        					$fecha = date_create($fila["fecing"]);
        					$nuevofi = date_format($fecha,"d-m-Y");
			            ?>
			                <tr>
			                    <td>
			                    	<?php 
		                    	        $exist=0;
		                    		    $per_dep=$fila['IdDepartamento'];
		                    			// esto es para saber si el departamento existe
		                                $j=0;
										while($j<$total_dep)
										{
										    if($per_dep==$dep[$j])
										    {
										    	$exist=1;
										    	$desc=$descripcion_d[$j];
										    } 
											$j++;
										}
										if($exist==0)
										{
											echo "SIN ASIGNAR";
										}
										if($exist==1)
										{
											echo $desc;
										}
									?>
								</td>
			                    <td><?php echo $fila['tipnom']; ?></td>
			                    <td><?php echo $fila['nomposicion_id']; ?></td>
			                    <td><?php echo $fila['apellidos']; ?></td>
			                    <td><?php echo $fila['nombres']; ?></td>
			                    <td><?php echo $fila['cedula']; ?></td>
			                    <td><?php echo $nuevofn; ?></td>
			                    <td>
			                    	<?php
		                    			$exist_car=0;
		                    			$per_car=$fila['codcargo'];
		                    		    $j=0;
										while($j<$total_car)
										{
											if($per_car==$codcargo[$j])
											{
										    	$exist_car=1;
										    	$cargo_persona=$desc_car[$j];
										    }
											$j++;
										}
										if($exist_car==0)
										{
											echo "SIN ASIGNAR";
										}
										if($exist_car==1)
										{
											echo $cargo_persona;
										}
									?>
			                    </td>
			                    <td><?php
			                    			$exist_fun=0;
			                    			$per_fun=$fila['nomfuncion_id'];
			                    		    $j=0;
											while($j<$total_fun)
											{
												if($per_fun==$nom_id[$j])
												{
											    	$exist_fun=1;
											    	$funcion_persona=$desc_fun[$j];
											    }
												$j++;
											}
											if($exist_fun==0)
											{
												echo "SIN ASIGNAR";
											}
											if($exist_fun==1)
											{
												echo $funcion_persona;
											}
									?>
			                    </td>
			                    <td><?php echo $nuevofi; ?></td>
			                    <td><?php echo $fila['suesal']; ?></td>
			                    <td><?php echo $fila['estado']; ?></td>
			                    <td><?php echo $fila['num_decreto']; ?></td>
			                </tr>
			            <?php
						$count++;
						}
		              	?>
		              	</tbody>
		              </table>
		            </div>
		        </div>
		        <!-- FIN DE LA TABLA-->
		    </div>
		</div>
		<!-- FIN DE LA PAGINA DE CONTENIDO-->
	</div>
</div>
<!-- Ventana modal deexportacion -->
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Opciones de Exportacion</h4>
	    </div>
      	<div class="modal-body">
	    	<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/pdf/pdf_planilla_siclar.php">
			<i class="fa fa-file-pdf-o"></i>
			PDF
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/word/word_planilla_siclar.php">
			<i class="fa fa-file-word-o"></i>
			WORD
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/excel/excel_planilla_siclar.php">
			<i class="fa fa-file-excel-o"></i>
			EXCEL
			</a>
      	</div>
      	<div class="modal-footer">
        	<a class="btn btn-primary" data-dismiss="modal">
			<i class="fa fa-remove"></i>
			Cerrar
			</a>
      	</div>
    </div>

  </div>
</div>
<!-- FIn de Ventana modal deexportacion -->
<script type="text/javascript">
   $(document).ready(function() { 
    //$('#table_datatable').DataTable();
            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended", 
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [ 3 ] },
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "8%", "aTargets": [3] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });
</script>
</body>
</html>