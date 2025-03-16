<?php
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
include ("../header4.php");
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
//-----------------------------------------------------
//print_r($_GET);

$ficha=$_GET['ficha'];
$concepto=$_GET['concepto'];
$anio=$_GET['anio'];
/*
$consulta="select nomconceptos.codcon as codcon,nomconceptos.descrip as descrip,nomconceptos.tipcon as tipcon,nomconceptos.unidad as unidad,nomconceptos.formula as formula,
nomconceptos.modifdef as modifdef from nomconceptos join nomconceptos_frecuencias on(nomconceptos_frecuencias.codcon=nomconceptos.codcon)
join nomconceptos_tiponomina on(nomconceptos_tiponomina.codcon=nomconceptos.codcon) join nom_nominas_pago on(nomconceptos_tiponomina.codtip=nom_nominas_pago.codtip)
where nomconceptos_tiponomina.codtip='".$_SESSION['codigo_nomina']."' group by nomconceptos.tipcon, nomconceptos.codcon, nomconceptos.descrip
order by nomconceptos.tipcon,nomconceptos.descrip";*/
$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
if ($concepto != 'all') {
	$sql = "SELECT sum(monto) as acumulado,cod_tac,des_tac,mes FROM nomvis_acumulados
	where ficha='{$ficha}' and cod_tac='{$concepto}' and anio='{$anio}' group by mes,cod_tac order by mes asc";
}else{
	$sql = "SELECT sum(monto) as acumulado,cod_tac,descrip,mes FROM nomvis_acumulados
	where ficha='{$ficha}' and anio='{$anio}' and cod_tac!='CON' group by mes,cod_tac order by mes asc";
}
$acumulados = $db1->query($sql);
$mesAnt='';
?>

<div class="page-container">
  <div class="page-content-wrapper">

    <div class="row">
      <div class="col-md-12">
        <div class="portlet box blue">
          <div class="portlet-body">
						<?php while ($acum=mysqli_fetch_array($acumulados)): ?>
								<?php if ($acum['mes']!=$mesAnt): ?>

								<div class="row">
									<div class="col-lg-12">
											<div class="col-lg-6">
												<h4><strong><?php echo $meses[$acum['mes']]; ?></strong></h4>
											</div>
									</div>
								</div>

								<?php $mesAnt=$acum['mes'];
										endif;
								?>

								<div class="row">
		              <div class="col-lg-12">
											<div class="col-lg-6">
												<h4><strong><?php echo $acum['cod_tac']." (".$acum['cod_tac'].")" ?></strong></h4>
			                </div>
											<div class="col-lg-6">
												<h5><?php echo $acum['acumulado'] ?></h5>
			                </div>

		              </div>
		            </div>
						<?php endwhile ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="../../includes/js/jquery.min.js"></script>
<script>
