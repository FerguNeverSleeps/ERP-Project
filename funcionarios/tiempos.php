<?php 
require_once '../generalp.config.inc.php';
session_start();
ob_start();

//$ficha = $_GET["ficha"];  //Anterior parametro
$cedula = $_GET["cedula"];
$anio = $_GET["anio"];
$factual = $_GET["FICHA_ACTUAL"];
//$usr = $_GET["usr_uid"];
// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
//require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);
$useruid='';
$sql_personal = "SELECT useruid FROM nompersonal WHERE cedula='".$cedula."'";
$res_personal = $db->query($sql_personal);
$fila_personal=fetch_array($res_personal);
 $useruid = $fila_personal["useruid"];

//$sql = "SELECT * FROM departamento";
$sql = "SELECT b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, "
        . "SUM(a.minutos) as minutos , SUM(a.dias) as dias FROM dias_incapacidad as a, tipo_justificacion as b "
        . "WHERE a.tipo_justificacion=b.idtipo AND a.cedula='$cedula' GROUP BY a.tipo_justificacion";
$res = $db->query($sql);

$sql_historico = "SELECT b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, "
        . "SUM(a.minutos) as minutos , SUM(a.dias) as dias,  YEAR(a.fecha) as anio "
        . "FROM dias_incapacidad_historial as a, tipo_justificacion as b "
        . "WHERE a.tipo_justificacion=b.idtipo AND a.usr_uid='$useruid' "
        . "GROUP BY a.tipo_justificacion, YEAR(a.fecha)";
//echo $sql_historico;
//exit;
$res_historico = $db->query($sql_historico);

?>
<html>
<head>
<?php include("../includes/dependencias.php"); // <html><head></head><body> ?>
<meta charset="utf-8">
<link href="../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
</head>
<!-- BEGIN PAGE CONTENT-->
<body>
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<!-- <i class="fa fa-globe"></i> -->
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempos
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-toolbar" style="display: none">
					<div class="btn-group">&nbsp;
						<!--<button id="sample_editable_1_new" class="btn green">
						Add New <i class="fa fa-plus"></i>
						</button>-->
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover" id="table_datatable">
				<thead>
				<tr>
					<th>Tipo</th>
					<th>Tiempo</th>	
                                        <th>Dias</th>	
                                        <th>Horas</th>	
                                        <th>Minutos</th>	
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php
					while($fila=fetch_array($res))
					{ 
//						
                                            $dia   = intval(abs($fila['tiempo'])/8);
                                            $hora  = intval(abs($fila['tiempo']) - ($dia*8));
                                            $MIN   = intval(round((abs($fila['tiempo']) - ($dia*8) - $hora),2) * 60);
                                             $tipo=$fila['idtipo'];
                                            if ($fila['tiempo'] >= 0)
                                            {
                                                    $dias=$dia;
                                                    $horas=$hora;
                                                    $minutos=$MIN;	
                                            }
                                            else
                                            {
                                                    $dias=-1*$dia;
                                                    $horas=-1*$hora;
                                                    $minutos=-1*$MIN;	
                                            }    
                                            
                                            if($fila['justificacion']=="VACACIONES")
                                            {
                                                    $dias=$fila['tiempo'];
                                                    $horas=$minutos=0;
                                            }
					?>
						<tr class="odd gradeX">
							<td>
								<?php echo $fila['justificacion'];?>
							</td>
							<td>
								<?php 
								if($fila['tiempo']==NULL){
									echo 0;
								}else{
									echo number_format($fila['tiempo'], 2, ",", ".");
								}
								if($fila['justificacion']=="VACACIONES"){
									echo " (Dias)";
								}else{
									echo " (Horas)";
								} 
								?>		
							</td>							
							<td>
								<?php //echo $fila['dias'];?>
                                                                <?php echo $dias;?>
							</td>
                                                        <td>
								<?php//cho $fila['horas'];?>
                                                                <?php echo $horas;?>
							</td>
                                                        <td>
								<?php echo $minutos;?>
							</td>
							<td style="text-align: center">
								<a href="tiempos_detalles.php?cedula=<?php echo $cedula; ?>&tipo=<?php echo $tipo?>" title="Detalles">
								<img src="../includes/imagenes/icons/clock.png" width="16" height="16">
								</a>
							</td>							
						</tr>
					  <?php									
					}
				?>
				</tbody>
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<!-- END PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<!-- <i class="fa fa-globe"></i> -->
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempo Historico
				</div>
			</div>
			<div class="portlet-body">
				<div class="table-toolbar" style="display: none">
					<div class="btn-group">&nbsp;
						<!--<button id="sample_editable_1_new" class="btn green">
						Add New <i class="fa fa-plus"></i>
						</button>-->
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover" id="table_datatable2">
				<thead>
				<tr>
					<th>Tipo</th>
                                        <th>Año</th>
                                        <th>Tiempo</th>
					<th>Dias</th>	
                                        <th>Horas</th>
                                        <th>Minutos</th>
					<th>&nbsp;</th>
				</tr>
				</thead>
				<tbody>
				<?php
					while($fila_historio=fetch_array($res_historico))
					{ 
						$tipo = $fila_historio["idtipo"];
						/*$sql1 = "SELECT dias FROM dias_incapacidad WHERE cedula='$cedula' AND tipo_justificacion='$tipo'";
						$res1 = $db->query($sql1);
						$resta=0;
						while($coll = fetch_array($res1))
						{
							$valor = $coll["dias"];
							$resta = $valor + $resta;
						}
						if($resta<0)
							$resta=(-$resta);
						else
							$resta;*/
					?>
						<tr class="odd gradeX">
							<td>
								<?php echo $fila_historio['justificacion'];?>
							</td>
                                                        <td>
								<?php echo $fila_historio['anio'];?>
							</td>
							<td>
								<?php 
								if($fila_historio['tiempo']==NULL){
									echo 0;
								}else{
									echo number_format($fila_historio['tiempo'], 2, ",", ".");
								}
								if($fila_historio['justificacion']=="VACACIONES"){
									echo " (Dias)";
								}else{
									echo " (Horas)";
								} 
								?>		
							</td>							
							 <td>
								<?php echo $fila_historio['dias'];?>
							</td>
                                                         <td>
								<?php echo $fila_historio['horas'];?>
							</td>
                                                         <td>
								<?php echo $fila_historio['minutos'];?>
							</td>
							<td style="text-align: center">
								<a href="tiempos_detalles.php?cedula=<?php echo $cedula; ?>&tipo=<?php echo $tipo?>" title="Detalles">
								<img src="../includes/imagenes/icons/clock.png" width="16" height="16">
								</a>
							</td>							
						</tr>
					  <?php									
					}
				?>
				</tbody>
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<?php //include("../nomina/footer4.php"); ?>

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
                   
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    
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