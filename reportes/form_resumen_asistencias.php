<?php include("../includes/dependencias2.php");
require_once('../nomina/lib/database.php');

$db = new Database($_SESSION['bd']);
$gerencia = mysqli_fetch_array($db->query("SELECT a.descrip AS gerencia,b.descrip AS departamento FROM nomnivel1 AS a,nomnivel2 AS b WHERE a.codorg = '".$_SESSION['region']."' AND b.codorg = '".$_SESSION['departamento']."'"));

$res = $db->query("SELECT * FROM nomnivel2 WHERE codorg IN (SELECT DISTINCT codnivel2 FROM nompersonal WHERE codnivel1 = '".$_SESSION['region']."')");
?>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<label>Parametros del Reporte</label>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" method="post" action="excel/excel_resumen_asistencias.php">
								
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-5 control-label">Gerencia:</label>
										<div class="col-md-7">
											<input type="hidden" name="gerencia" value="<?= $_SESSION['region'] ?>">
											<input name="ger_des" id="ger_des" class="form-control" type="text" value="<?php echo $gerencia['gerencia'] ?>" readonly>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label">Filtral Datos por:</label>
										<div class="col-md-7">
											<?php if (isset($_SESSION['dpto_caa'])): ?>
											<select name="dpto" id="dpto" class="form-control">
												<option value="#">Seleccione</option>
												<option value="all">VER TODOS LOS DEPARTAMENTOS</option>
												<option value="ger"><?= $gerencia['gerencia'] ?></option>
												<?php while ($dpto = mysqli_fetch_array($res)) {?>
												<option value="<?php echo $dpto['codorg'] ?>"><?php echo "DPTO DE ".$dpto['descrip'] ?></option>
												<?php }?>
											</select>
											<?php else: ?>
											<input name="dpto" id="dpto" type="hidden" value="<?php echo $_SESSION['departamento'] ?>">
											<input class="form-control" type="text" value="<?php echo $gerencia['departamento'] ?>" readonly>
											<?php endif ?>
										</div>
									</div>
									<div class="form-group">
				                        <label class="col-md-5 control-label">Mes:</label>
				                        <div class="col-md-7">
				                        	<select name="mes" id="mes" class="form-control" required>
				                        	    <option value="-">Seleccione</option>
				                        	    <option value="<?= date('Y-01') ?>"> Enero </option>
				                        	    <option value="<?= date('Y-02') ?>"> Febrero </option>
				                        	    <option value="<?= date('Y-03') ?>"> Marzo </option>
				                        	    <option value="<?= date('Y-04') ?>"> Abril </option>
				                        	    <option value="<?= date('Y-05') ?>"> Mayo </option>
				                        	    <option value="<?= date('Y-06') ?>"> Junio </option>
				                        	    <option value="<?= date('Y-07') ?>"> Julio </option>
				                        	    <option value="<?= date('Y-08') ?>"> Agosto </option>
				                        	    <option value="<?= date('Y-09') ?>"> Septiembre </option>
				                        	    <option value="<?= date('Y-10') ?>"> Octubre </option>
				                        	    <option value="<?= date('Y-11') ?>"> Noviembre </option>
				                        	    <option value="<?= date('Y-12') ?>"> Diciembre </option>
				                        	</select>
										</div>
				                    </div>
									<div class="form-group">
										<div align="right" style="padding-right: 2%;">
											<input name="enviar" class="btn btn-sm blue" type="submit">
											<a class="btn btn-sm blue" href="../nomina/paginas/control_asistencia/procesar_registros.php">
					                            <i class="fa fa-hand-o-left" aria-hidden="true"></i>
					                            Atras
					                        </a>
										</div>	
									</div>
									
								</div>
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
				<div class="col-md-3"></div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<script type="text/javascript">
$("#fecfin").change(function(event){
    cargar_fecha($('#fecfin').val());
});
function cargar_fecha(fecha){
    $.ajax({
        url :'ajax/cargar_fecha_rpt.php',
        type:'POST',
        data:{
            fecha:fecha,
            meses:1,
            ajax : true
        },
        dataType : 'json',
        success : function(response) {
        	$('#fecini').val(response['fecini']);
        },
        error : function(xhr, status) {
            console.log(xhr);
        },
        complete : function(xhr, status) {
        }
    });
}
</script>