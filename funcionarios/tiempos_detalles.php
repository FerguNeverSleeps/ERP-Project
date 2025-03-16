<?php 
require_once '../generalp.config.inc.php';
session_start();
ob_start();
//$codigo = $_GET["codigo"]; //Anterior parametro 
$cedula = $_GET["cedula"];
$tipo   = $_GET["tipo"];
$ficha  = $_GET["ficha"];
// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
//require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);

//$sql = "SELECT * FROM departamento";
$sql = "SELECT A.*,B.descripcion as justificacion "
        . " FROM dias_incapacidad as A "
        . "LEFT JOIN tipo_justificacion AS B ON A.tipo_justificacion = B.idtipo "
        . "WHERE A.cedula='$cedula' AND A.tipo_justificacion='$tipo'";
$res = $db->query($sql);
$fila_justificacion=fetch_array($res);

$sql1   = "SELECT cedula, apenom "
                . "FROM nompersonal "
                . "WHERE cedula =  '".$cedula."'";
$res1            = $db->query($sql1);
$persona         = $res1->fetch_object();

$nombre_apellido = strtoupper($persona ->apenom);
$cedula          = $persona ->cedula;

?>


<?php include("../nomina/header_nuevo.php"); // <html><head></head><body> ?>
<meta charset="utf-8">
<!--<link href="../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>-->

<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title" style="font-size: 15px;">
				
					<!-- <i class="fa fa-globe"></i> -->
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> <?php echo $fila_justificacion['justificacion']." (Horas) ".$nombre_apellido." / ".$cedula?>
				
				<div class="actions">
                    <a class="btn btn-sm red"  onclick="javascript: window.location='../reportes/pdf/tiempos_detalles.php?cedula=<?php echo $cedula?>&tipo=<?php echo $tipo?>'">
                        <!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
                        <i class="fa fa-file-pdf-o"></i> PDF
                    </a>
					<a class="btn btn-sm grey"  onclick="javascript: window.location='tiempos.php?cedula=<?php echo $cedula?>'">
						<!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
						<i class="fa fa-arrow-left"></i> Regresar
					</a>
				</div>
			</div>
			<div class="portlet-body" style="padding-bottom: 4%">
				<div class="table-toolbar" style="display: none">
					<div class="btn-group">&nbsp;
						<!--<button id="sample_editable_1_new" class="btn green">
						Add New <i class="fa fa-plus"></i>
						</button>-->
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover" id="table_datatable">
				<thead>
				<tr style="min-height: 35px;" style="font-size: 14px; font-weight: bold; ">	
                    <th width="10%">Fecha</th>
                    <th width="10%">Tiempo</th>
                    <th width="60%">Observacion</th>
                    <th width="10%">Dias</th>
                    <th width="10%">horas</th>
                    <th width="10%">Minutos</th> 
                    <th width="10%">Opciones</th> 
				</tr>
				</thead>
				<tbody style="font-size: 13px;">
				<?php
				/*	while($fila=fetch_array($res))
					{ 
                                            if($fila['tipo_justificacion']==3)
                                            {
                                                if($fila['dias_restante']!=NULL)
                                                    $dias = $fila['dias_restante'];
                                                else
                                                    $dias =0;
                                                if($fila['horas_restante']!=NULL)
                                                    $horas = $fila['horas_restante'];
                                                else
                                                    $horas = 0;
                                                if($fila['minutos_restante']!=NULL)
                                                    $minutos = $fila['minutos_restante'];
                                                else
                                                    $minutos = 0;
                                                       
                                            }
                                            else
                                            {
                                                if($fila['dias']!=NULL)
                                                    $dias = $fila['dias'];
                                                else
                                                    $dias = 0;
                                                if($fila['horas']!=NULL)
                                                    $horas = $fila['horas'];
                                                else
                                                    $horas = 0;
                                                if($fila['minutos']!=NULL)
                                                    $minutos = $fila['minutos'];
                                                else
                                                   $minutos = 0;
                                            }
					?>
						<tr class="odd gradeX">
							<td><?php echo $fila['fecha'] ?></td>
                                                        <td><?php echo $fila['tiempo'] ?></td>
                                                        <td><?php echo $fila['observacion'] ?></td>
							<td><?php echo $dias ?></td>
							<td><?php echo $horas ?></td>
							<td><?php echo $minutos ?></td>
                                                        
						</tr>
					  <?php									
					}*/
				?>
				</tbody>
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<input type="hidden" name="tipo" id="tipo" value="<?= $tipo ?>">
<input type="hidden" name="cedula" id="cedula" value="<?= $cedula ?>">
<!-- END PAGE CONTENT-->
<?php include("../nomina/footer_nuevo.php"); ?>
<script type="text/javascript" src="js/tiempos_detalles.js"></script>

</body>
</html>