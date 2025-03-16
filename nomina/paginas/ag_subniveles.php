<?php 
session_start();
ob_start();
include("../header4.php");
include("../lib/common.php");
include("func_bd.php");
?>
<script>
function Enviar(){
	
	if (document.frmPrincipal.registro_id.value==0) 
	{ 
		document.frmPrincipal.op_tp.value=1
	}
	else
	{ 	

		document.frmPrincipal.op_tp.value=2
	}		
	
	if (document.frmPrincipal.txtdescripcion.value==0)
	{
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");
	}
}


</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">

</script>


<?php
$registro_id=$_POST['registro_id'];
$op_tp=$_POST['op_tp'];
$validacion=0;
$nivel=$_POST['nivel'];
$tabla_nivel = "nomnivel$nivel";
if ($registro_id==0) // Si el registro_id es 0 se va a agregar un registro nuevo
{			
	
	if ($op_tp==1)
	{
		
		$codigo_nuevo=AgregarCodigo("$tabla_nivel","codorg");
		$query="INSERT INTO $tabla_nivel "
                     . "VALUES "
                     . "('$_POST[txtcodigo]', "
                        . "'$_POST[txtdescripcion]',"
                        . "'$_POST[asociado]',"
                        . "'$_POST[txtmarkar]',"
                        . "'$_POST[txtee]',"
                        . "'$_POST[descripcion_corta]',"
                        . "'$_POST[descripcion_completa]',"
                        . "'$_POST[tipo_presup]',"
                        . "'$_POST[programa]',"
                        . "'$_POST[fuente_finan]',"
                        . "'$_POST[sub_programa]',"
                        . "'$_POST[actividad]',"
                        . "'$_POST[objeto_gasto]',"
                        . "'$_POST[estatus]',"
                        . "'$_POST[porcentaje]',"
                        . "'$_POST[id_subsidiaria]')";
		$result=sql_ejecutar($query);
		activar_pagina("subniveles.php?nivel=$nivel");
	}
}
else // Si el registro_id es mayor a 0 se va a editar el registro actual
{	
	$query="SELECT * "
                . "FROM $tabla_nivel "
                . "WHERE codorg='$registro_id'";		
	$result=sql_ejecutar($query);	
	$row = fetch_array ($result);	
	$codigo=$row[codorg];	
	$nombre=$row[descrip];
}

if ($op_tp==2)
{

	if($_SESSION[tipo_empresa]==1)
	{
		$_POST[txtmarkar]=$_POST[tipo_presup].'-'.$_POST[programa].'-'.$_POST[fuente_finan].'-'.$_POST[sub_programa].'-'.$_POST[actividad].'-'.$_POST[objeto_gasto];
	}

	$query="UPDATE $tabla_nivel "
                . "SET "
                . "codorg='$_POST[txtcodigo]', "
                . "descrip='$_POST[txtdescripcion]', "
                . "markar='$_POST[txtmarkar]', "
                . "ee='$_POST[txtee]', "
                . "descripcion_corta='$_POST[descripcion_corta]' ,"
                . "descripcion_completa='$_POST[descripcion_completa]', "
                . "tipo_presup='$_POST[tipo_presup]',"
                . "programa='$_POST[programa]', "
                . "fuente_finan='$_POST[fuente_finan]',"
                . "sub_programa='$_POST[sub_programa]',"
                . "actividad='$_POST[actividad]',"
                . "objeto_gasto='$_POST[objeto_gasto]',"
                . "estatus='$_POST[estatus]', "
                . "gerencia='$_POST[asociado]', "
                . "porcentaje='$_POST[porcentaje]', "
                . "id_subsidiaria='$_POST[id_subsidiaria]' "
                . "WHERE "
                . "codorg='$registro_id'";
	$result=sql_ejecutar($query);	

	activar_pagina("subniveles.php?nivel=$nivel");
}

if($nivel>1)
{
	$nivelant= $nivel-1;
	$consulta="SELECT * "
                . "FROM "
                . "nomnivel$nivelant ";//where codorg='$registro_id'";
	$result3=sql_ejecutar($consulta);

	
	$consulta="SELECT "
                . "nomniv$nivelant as nombrenivel "
                . "FROM nomempresa ";//where codorg='$registro_id'";
	$result4=sql_ejecutar($consulta);
	$fetch4=fetch_array($result4);
	
}else{
	$select_subsidiaria = "SELECT * from subsidiaria;";
	$result_subs=sql_ejecutar($select_subsidiaria);
}
$consulta="SELECT * "
        . "FROM nomnivel$nivel "
        . "WHERE codorg='$registro_id'";
$result2=sql_ejecutar($consulta);
$fetch=fetch_array($result2);

?>
<html lang="es">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<input name="op_tp" type="Hidden" id="op_tp" value="-1">
<input name="nivel" type="Hidden" id="nivel" value="<?php echo $_POST[nivel]; ?>">
<input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php
									if ($registro_id==0)
									{
										echo "Agregar Sub Nivel";
									}
									else
									{
										echo "Modificar Sub Nivel";
									}
								?>
							</div>	
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='subniveles.php?nivel=<?= $nivel?>'">
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>

						<div class="portlet-body">
							<?php
							if($nivel>1)
							{?>
							<div class="row">
							
								<div class="form-group">
									<label class="col-md-3"><h5>Asociado a <?php echo $fetch4[nombrenivel]?>:</h5></label>
									<div class="col-md-9">



										<select name="asociado" id="asociado" class="form-control form-control-inline input-medium">
										<?php
										while($fetch3=fetch_array($result3))
										{
										?>
										<option value="<?php echo $fetch3[codorg]?>" <?php if($fetch[gerencia]==$fetch3[codorg]) {echo "selected";}?>><?php echo utf8_encode($fetch3[descrip])?></option>
										<?php
										}
										?>
										</select>
										<!-- onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" -->

									</div>
								</div>
							</div>

							<?php
							}
							?>
							
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>C&oacute;digo:</h5></label>
									<div class="col-md-9">
										<input name="txtcodigo" type="text" id="txtcodigo" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $registro_id; }  ?>">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Descripci&oacute;n:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="txtdescripcion" type="text" id="txtdescripcion" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[descrip]; }  ?>">
									</div>
								</div>
							</div>	
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Partidas Presupuestaria:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="txtmarkar" type="text" id="txtmarkar" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[markar]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Cuenta Contable:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="txtee" type="text" id="txtee" class="form-control form-control-inline input-medium"  value="<?php if ($registro_id!=0){ echo $fetch[ee]; }  ?>">
									</div>
								</div>
							</div>
                                                    <div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Porcentaje:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="porcentaje" type="text" id="porcentaje" class="form-control form-control-inline input-medium"  value="<?php if ($registro_id!=0){ echo $fetch[porcentaje]; }  ?>">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group">
									<label class="col-md-12"><h5>Área para ser tomada por una institución gubernamental</h5></label>
								</div>
							</div>							
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Descripción Corta:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="descripcion_corta" type="text" id="descripcion_corta" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[descripcion_corta]; }  ?>">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Descripción Larga:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="descripcion_completa" type="text" id="descripcion_completa" class="form-control form-control-inline input-large" value="<?php if ($registro_id!=0){ echo $fetch[descripcion_completa]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Tipo de Presupuesto:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="tipo_presup" type="text" id="tipo_presup" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[tipo_presup]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Programa:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="programa" type="programa" id="txtee" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[programa]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Fuente de Financiamiento:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="fuente_finan" type="text" id="fuente_finan" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[fuente_finan]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Sub Programa:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="sub_programa" type="text" id="sub_programa" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[sub_programa]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Actividad:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="actividad" type="text" id="actividad" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[actividad]; }  ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Objeto Gasto:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="objeto_gasto" type="text" id="objeto_gasto" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[objeto_gasto]; }  ?>">
									</div>
								</div>
							</div>
							<?php if($nivel ==1): ?>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Subsidiaria:&nbsp;</h5></label>
									<div class="col-md-3">
										<select class="select2 form-control" name = "id_subsidiaria" id="id_subsidiara">
										<option value=''>SELECCIONE...</option>

											<?php while($subsidiarias = fetch_array($result_subs)){ 
												$selected = $fetch['id_subsidiaria'] == $subsidiarias["id_subsidiaria"] ? " selected " : "";
												?> 
												
												<option value='<?= $subsidiarias["id_subsidiaria"] ?>' <?= $selected ?>><?= $subsidiarias["descripcion"] ?></option>
											<?php

											} ?>
										</select>
									</div>
								</div>
							</div>
							<?php endif ?>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Status:&nbsp;</h5></label>
									<div class="col-md-9">
										<input name="estatus" type="text" id="estatus" class="form-control form-control-inline input-medium" value="<?php if ($registro_id!=0){ echo $fetch[estatus]; }  ?>">
									</div>
								</div>
							</div>

					<?php
						if(($nivel==4)&&($registro_id!=0))
						{
							$i=1;
							while($fetch=fetch_array($result2))
							{
								$des=$fetch['descrip'];
								if($des[0]=="*")
								{
							?>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Cod. Con.: <?php echo $fetch['codcon'];?></h5></label>
									<div class="col-md-6">
										<input type="hidden" name="<?php echo "codcon".$i;?>" id="<?php echo "codcon".$i;?>" value="<?php echo $fetch['codcon'];?>">
									</div>
									<div class="col-md-3">
										Concepto: <strong><?php echo "---".str_replace("*","",$fetch['descrip'])."---";?></strong>
									</div>
								</div>
							</div>
								<?
							}
							else
							{
								?>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Cod. Con.: <?php echo $fetch['codcon'];?></h5></label>
									<div class="col-md-6">
										<input type="hidden" name="<?php echo "codcon".$i;?>" id="<?php echo "codcon".$i;?>" value="<?php echo $fetch['codcon'];?>">
									</div>
									<div class="col-md-3">
										Concepto: <?php echo str_replace("*","",$fetch['descrip'])."---";?>
									</div>
								</div>
							</div>

								<?
							}
							$consulta="SELECT ctacon FROM nomconceptos_ctager WHERE codnivel4=$registro_id AND codcon=$fetch[codcon]";
							$result_cta=sql_ejecutar($consulta);
							$fetch_cta=fetch_array($result_cta);
							?>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h5>Cta. cont.:</h5></label>
									<div class="col-md-9">
										<input type="text" name="<?php echo "ctacon".$i;?>" id="<?php echo "ctacon".$i;?>" value="<? echo $fetch_cta['ctacon']?>">
									</div>

								</div>
							</div>
	
							<?
							$i+=1;
									}
								}
								?>
		                   <div class="row">
		                        <div class="col-md-5">
		                        </div>                    
		                        <div class="col-md-1">                            
									<?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?>
		                        </div>                    

		                        <div class="col-md-1">
									<?php boton_metronic('cancel',"document.location.href='subniveles.php?nivel=$nivel'",2) ?>
		                        </div>                    
		                        <div class="col-md-5">                            
		                        </div>                
		                    </div>
						<input type="hidden" name="cantidad" id="cantidad" value="<?php echo $i;?>">


							
							
							
						</div>
						<!-- END PORTLET BODY-->
						
					</div>
				<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
		<!-- END PAGE CONTENT-->
		</div>
	</div>
	  <!-- END CONTENT -->
</div>

</form>


<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<?php include("../footer4.php"); ?>


</body>
</html>

