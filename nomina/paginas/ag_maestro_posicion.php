<?php 
session_start();
ob_start();
//error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
//error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

if( isset($_POST['btn-guardar']) )
{
	$descripcion            = ( isset($_POST['descripcion']) )      ? $_POST['descripcion']      : '' ;
	$sueldo_propuesto       = ( isset($_POST['sueldo_propuesto']) ) ? $_POST['sueldo_propuesto'] : '' ; 
	$sueldo_anual           = ( isset($_POST['sueldo_anual']) )     ? $_POST['sueldo_anual']     : '' ;
	$partida                = ( isset($_POST['partida']) )          ? $_POST['partida']          : '' ;
	$gastos_repr            = ( isset($_POST['gastos_repr']) )      ? $_POST['gastos_repr']      : '' ;
	$paga_gr                = ( isset($_POST['paga_gr']) )          ? $_POST['paga_gr']          : '' ;
	$categoria_id           = ( isset($_POST['categoria_id']) )     ? $_POST['categoria_id']     : '' ;
	$cargo_id               = ( isset($_POST['cargo_id']) )         ? $_POST['cargo_id']         : '' ; 
	$nomposicion_id         = ( isset($_POST['id']) )               ? $_POST['id']               : '' ;
	$sueldo_2               = ( isset($_POST['sueldo_2']) )         ? $_POST['sueldo_2']         : '' ;
	$sueldo_3               = ( isset($_POST['sueldo_3']) )         ? $_POST['sueldo_3']         : '' ;
	$sueldo_4               = ( isset($_POST['sueldo_4']) )         ? $_POST['sueldo_4']         : '' ;
	$mes_1                  = ( isset($_POST['mes_1']) )            ? $_POST['mes_1']            : '' ;
	$mes_2                  = ( isset($_POST['mes_2']) )            ? $_POST['mes_2']            : '' ;
	$mes_3                  = ( isset($_POST['mes_3']) )            ? $_POST['mes_3']            : '' ;
	$mes_4                  = ( isset($_POST['mes_4']) )            ? $_POST['mes_4']            : '' ;
	$dieta                  = ( isset($_POST['dieta']) )            ? $_POST['dieta']            : '' ;
	$combustible            = ( isset($_POST['combustible']) )      ? $_POST['combustible']      : '' ;	
	
	$status                 = ( isset($_POST['status']) )           ? $_POST['status']           : '' ;
	
	$partida011             = ( isset($_POST['partida011']) )       ? $_POST['partida011']       : '' ;
	$partida012             = ( isset($_POST['partida012']) )       ? $_POST['partida012']       : '' ;
	$partida013             = ( isset($_POST['partida013']) )       ? $_POST['partida013']       : '' ;
	$partida019             = ( isset($_POST['partida019']) )       ? $_POST['partida019']       : '' ;
	$partida030             = ( isset($_POST['partida030']) )       ? $_POST['partida030']       : '' ;
	$partida080             = ( isset($_POST['partida080']) )       ? $_POST['partida080']       : '' ;
	$sueldo_1               = ( isset($_POST['sueldo_1']) )         ? $_POST['sueldo_1']         : '' ;
	$sobresueldo_esp_1      = ( isset($_POST['sobresueldo_esp_1'])) ? $_POST['sobresueldo_esp_1']  : '' ;
	$sobresueldo_esp_2      = ( isset($_POST['sobresueldo_esp_2'])) ? $_POST['sobresueldo_esp_2']  : '' ;
	$mes_esp_1              = ( isset($_POST['mes_esp_1']) )        ? $_POST['mes_esp_1']        : '' ;
	$mes_esp_2              = ( isset($_POST['mes_esp_2']) )        ? $_POST['mes_esp_2']        : '' ;
	$gasto_representacion_2 = ( isset($_POST['gasto_representacion_2']) ) ? $_POST['gasto_representacion_2'] : '' ;
	$mes_gasto_1            = ( isset($_POST['mes_gasto_1']) )      ? $_POST['mes_gasto_1']      : '' ;
	$mes_gasto_2            = ( isset($_POST['mes_gasto_2']) )      ? $_POST['mes_gasto_2']      : '' ;
	$sueldo_propuesto_11    = ( isset($_POST['sueldo_propuesto_11']) ) ? $_POST['sueldo_propuesto_11'] : '' ;
	$sueldo_propuesto_12    = ( isset($_POST['sueldo_propuesto_12']) ) ? $_POST['sueldo_propuesto_12'] : '' ;
	$sueldo_propuesto_13    = ( isset($_POST['sueldo_propuesto_13']) ) ? $_POST['sueldo_propuesto_13'] : '' ;
	$sueldo_propuesto_19    = ( isset($_POST['sueldo_propuesto_19']) ) ? $_POST['sueldo_propuesto_19'] : '' ;
	$sueldo_propuesto_30    = ( isset($_POST['sueldo_propuesto_30']) ) ? $_POST['sueldo_propuesto_30'] : '' ;
	$sueldo_propuesto_80    = ( isset($_POST['sueldo_propuesto_80']) ) ? $_POST['sueldo_propuesto_80'] : '' ;
	$objeto                 = ( isset($_POST['objeto']) ) ? $_POST['objeto'] : '' ;
	$unidad                 = ( isset($_POST['unidad']) ) ? $_POST['unidad'] : '' ;
	$estado                 = ( isset($_POST['estado']) ) ? $_POST['estado'] : '' ;

	if( empty($id) )
	{
		$sql = "INSERT INTO nomposicion (nomposicion_id, descripcion_posicion, sueldo_propuesto, sueldo_anual,
			                             partida, gastos_representacion, paga_gr,categoria_id,cargo_id,sueldo_2,sueldo_3,sueldo_4,mes_1,mes_2,mes_3,mes_4,status,partida011,partida012,partida013,partida019,partida030,partida080,sueldo_1,sobresueldo_esp_1,sobresueldo_esp_2,mes_esp_1,mes_esp_2,gasto_representacion_2,mes_gasto_1,mes_gasto_2,sueldo_propuesto_11,	sueldo_propuesto_12,sueldo_propuesto_13,sueldo_propuesto_19,sueldo_propuesto_30,sueldo_propuesto_80,objeto,unidad,estado,dieta,combustible) 
                VALUES ('{$nomposicion_id}','{$descripcion}','{$sueldo_propuesto}', '{$sueldo_anual}', 
                	   '{$partida}', '{$gastos_repr}', '{$paga_gr}', '{$categoria_id}', '{$cargo_id}','{$sueldo_2}','{$sueldo_3}','{$sueldo_4}','{$mes_1}','{$mes_2}','{$mes_3}','{$mes_4}','{$status}','{$partida011}','{$partida012}','{$partida013}','{$partida019}','{$partida030}','{$partida080}','{$sueldo_1}','{$sobresueldo_esp_1}','{$sobresueldo_esp_2}','{$mes_esp_1}','{$mes_esp_2}','{$gasto_representacion_2}','{$mes_gasto_1}','{$mes_gasto_2}','{$sueldo_propuesto_11}','{$sueldo_propuesto_12}','{$sueldo_propuesto_13}','{$sueldo_propuesto_19}','{$sueldo_propuesto_30}','{$sueldo_propuesto_80}','{$objeto}','{$unidad}','{$estado}','{$dieta}','{$combustible}')";
				$descripcion     = 'AGREGAR FICHA A '.$_POST['ficha'].'A LA POSICION'.$_POST['id'];
	
			$sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
			VALUES (NULL, '".$descripcion."', now(), 'Posición-Agregar', 'ag_maestro_posicion.php', 'AGREGAR','".$_POST['codcargo']."','".$_SESSION['nombre'] ."')";
			
			$res_transaccion = query($sql_transaccion, $conexion);

	}
	else
	{
		$sql = "UPDATE nomposicion SET 
			
			descripcion_posicion   = '{$descripcion}',
			sueldo_propuesto       = '{$sueldo_propuesto}',
			sueldo_anual           = '{$sueldo_anual}',
			partida                = '{$partida}',
			gastos_representacion  = '{$gastos_repr}',
			paga_gr                = '{$paga_gr}',
			categoria_id           = '{$categoria_id}',
			cargo_id               = '{$cargo_id}',
			sueldo_2               = '{$sueldo_2}',
			sueldo_3               = '{$sueldo_3}',
			sueldo_4               = '{$sueldo_4}',
			mes_1                  = '{$mes_1}',
			mes_2                  = '{$mes_2}',
			mes_3                  = '{$mes_3}',
			mes_4                  = '{$mes_4}',
			mes_4                  = '{$mes_4}',
			status                 = '{$status}',
			partida011             = '{$partida011}',
			partida012             = '{$partida012}',
			partida013             = '{$partida013}',
			partida019             = '{$partida019}',
			partida030             = '{$partida030}',
			partida080             = '{$partida080}',
			sueldo_1               = '{$sueldo_1}',
			sobresueldo_esp_1      = '{$sobresueldo_esp_1}',
			sobresueldo_esp_2      = '{$sobresueldo_esp_2}',
			mes_esp_1              = '{$mes_esp_1}',
			mes_esp_2              = '{$mes_esp_2}',
			gasto_representacion_2 = '{$gasto_representacion_2}',
			mes_gasto_1            = '{$mes_gasto_1}',
			mes_gasto_2            = '{$mes_gasto_2}',
			sueldo_propuesto_11    = '{$sueldo_propuesto_11}',
			sueldo_propuesto_12    = '{$sueldo_propuesto_12}',
			sueldo_propuesto_13    = '{$sueldo_propuesto_13}',
			sueldo_propuesto_19    = '{$sueldo_propuesto_19}',
			sueldo_propuesto_30    = '{$sueldo_propuesto_30}',
			sueldo_propuesto_80    = '{$sueldo_propuesto_80}',
			objeto                 = '{$objeto}',
			unidad                 = '{$unidad}',
			dieta                  = '{$dieta}',
			combustible            = '{$combustible}',
			estado                 = '{$estado}'
			
			WHERE nomposicion_id   = " . $id;
			$descripcion     = 'Modificacion de posicion a ficha '.$_POST['ficha'];
	
			$sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
			VALUES (NULL, '".$descripcion."', now(), 'POSICION-EDITAR', 'ag_maestro_posicion.php', 'editar','".$_POST['codcargo']."','".$_SESSION['nombre'] ."')";
			
			$res_transaccion = query($sql_transaccion, $conexion);
	}
	
	$res             = query($sql, $conexion);

	activar_pagina("maestro_posicion.php");	 
}

$descripcion = $sueldo_propuesto = $sueldo_anual = $partida = $gastos_repr = $paga_gr = $categoria_id = $cargo_id = '';

if(isset($_GET['edit']))
{
	$sql = "SELECT nomposicion_id, descripcion_posicion, sueldo_propuesto, sueldo_anual,  
	               partida, gastos_representacion, paga_gr, categoria_id, cargo_id ,sueldo_2,sueldo_3,sueldo_4,mes_1,mes_2,mes_3,mes_4,sobresueldo_antiguedad_1,sobresueldo_antiguedad_2,sobresueldo_zona_apartada_1,sobresueldo_zona_apartada_2,sobresueldo_jefatura_1,sobresueldo_jefatura_2,sobresueldo_otros_1,sobresueldo_otros_2,mes_ant_1,mes_ant_2,mes_za_1,mes_za_2,mes_jef_1,mes_jef_2,mes_ot_1,mes_ot_2,mes_ot_2,partida011,partida012,partida013,partida019,partida030,partida080,sueldo_1,sobresueldo_esp_1,sobresueldo_esp_2,mes_esp_1,mes_esp_2,gasto_representacion_2,mes_gasto_1,mes_gasto_2,sueldo_propuesto_11,	sueldo_propuesto_12,sueldo_propuesto_13,sueldo_propuesto_19,sueldo_propuesto_30,sueldo_propuesto_80,objeto,unidad,estado,dieta,combustible
	        FROM   nomposicion WHERE nomposicion_id = " . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$descripcion            = $fila['descripcion_posicion'];
		$sueldo_propuesto       = $fila['sueldo_propuesto'];
		$sueldo_anual           = $fila['sueldo_anual'];
		$partida                = $fila['partida'];
		$gastos_repr            = $fila['gastos_representacion'];
		$paga_gr                = $fila['paga_gr'];
		$categoria_id           = $fila['categoria_id'];
		$cargo_id               = $fila['cargo_id'];
		$sueldo_2               = $fila['sueldo_2'];
		$sueldo_3               = $fila['sueldo_3'];
		$sueldo_4               = $fila['sueldo_4'];
		$mes_1                  = $fila['mes_1'];
		$mes_2                  = $fila['mes_2'];
		$mes_3                  = $fila['mes_3'];
		$mes_4                  = $fila['mes_4'];
		
		$status                 = $fila['status'];
		$partida011             = $fila['partida011'];
		$partida012             = $fila['partida012'];
		$partida013             = $fila['partida013'];
		$partida019             = $fila['partida019'];
		$partida080             = $fila['partida080'];
		$partida030             = $fila['partida030'];
		$sueldo_1               = $fila['sueldo_1'];
		$sobresueldo_esp_1      = $fila['sobresueldo_esp_1'];
		$sobresueldo_esp_2      = $fila['sueldo_2'];
		$mes_esp_1              = $fila['mes_esp_1'];
		$mes_esp_2              = $fila['mes_esp_2'];
		$gasto_representacion_2 = $fila['gasto_representacion_2'];
		$mes_gasto_1            = $fila['mes_gasto_1'];
		$mes_gasto_2            = $fila['mes_gasto_2'];
		$sueldo_propuesto_11    = $fila['sueldo_propuesto_11'];
		$sueldo_propuesto_12    = $fila['sueldo_propuesto_12'];
		$sueldo_propuesto_13    = $fila['sueldo_propuesto_13'];
		$sueldo_propuesto_19    = $fila['sueldo_propuesto_19'];
		$sueldo_propuesto_30    = $fila['sueldo_propuesto_30'];
		$sueldo_propuesto_80    = $fila['sueldo_propuesto_80'];
		$objeto                 = $fila['objeto'];
		$unidad                 = $fila['unidad'];
		$dieta                  = $fila['dieta'];
		$combustible            = $fila['combustible'];
		$estado                 = $fila['estado'];
	 

	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    font-size: 13px;
    font-weight: bold;
    font-family: helvetica, arial, verdana, sans-serif;
    margin-bottom: 3px;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-control {
    /* border: 1px solid silver; */
}

.btn{
	border-radius: 3px !important;
}

.form-body{
	padding-bottom: 5px;
}
</style>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
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
								POSICI&Oacute;N
							</div>
							<div class="actions">
							<a class="btn btn-sm blue"  onclick="javascript: window.location='maestro_posicion.php'">
									<i class="fa fa-arrow-left" aria-hidden="true"></i>
									REGRESAR
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
								<div class="portlet box blue-madison"> 
									<div class="portlet-title">SUELDO</div>
									<div class="portlet-body">
										<div class="form-group">
											<label class="col-md-3 control-label" for="descripcion"><b>POSICIÓN:</b></label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="id" name="id" value="<?php echo $id; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="cargo_id">CARGO:</label>
											<div class="col-md-5">
											<?php 
												$query = "SELECT * FROM nomcargos";
												$res   = query($query, $conexion);
											?>
												<select name="cargo_id" id="cargo_id" class="select2 form-control">
													<option value="">SELECCIONE UN CARGO</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $cargo_id == $fila['cod_car'] )
														{ ?>
															<option value="<?php echo $fila['cod_car']; ?>" selected><?php echo $fila['des_car']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['cod_car']; ?>"><?php echo $fila['des_car']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_1">SUELDO MENSUAL 1:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_1" name="sueldo_1" value="<?php echo $sueldo_1; ?>"  >
											</div>
											<label class="col-md-1 control-label" for="mes_1">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" style="float:right"
											       id="mes_1" name="mes_1" value="<?php echo $mes_1; ?>" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_2">SUELDO MENSUAL 2:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_2" name="sueldo_2" value="<?php echo $sueldo_2; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_2">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_2" name="mes_2" value="<?php echo $mes_2; ?>" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_3">SUELDO MENSUAL 3:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_3" name="sueldo_3" value="<?php echo $sueldo_3; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_3">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_3" name="mes_3" value="<?php echo $mes_3; ?>" >
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_4">SUELDO MENSUAL 4:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_4" name="sueldo_4" value="<?php echo $sueldo_4; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_4">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_4" name="mes_4" value="<?php echo $mes_4; ?>" >
											</div>
										</div>
									 
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_anual">SUELDO ANUAL:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_anual" name="sueldo_anual" value="<?php echo $sueldo_anual; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_t">TOTAL MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_t" name="mes_t" value="<?php echo $mes_t; ?>" readonly>
											</div>

											
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_propuesto">SUELDO DE PLANILLA:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_propuesto" name="sueldo_propuesto" value="<?php echo $sueldo_propuesto; ?>">
											</div>
 
										</div>



										<div class="form-group">
											<label class="col-md-3 control-label" for="partida">Partida Presupuestaria Salario:</label>
											<div class="col-md-5">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida" id="partida" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
									
											<div class="col-md-offset-5 col-md-1"><button type="submit" class="btn blue active" id="btn-guardar" name="btn-guardar">GUARDAR</button></div>
											<div class="col-md-3"><button type="button" class="btn default active" onclick="javascript: document.location.href='maestro_posicion.php'">CANCELAR</button></div>
										</div>
									</div>
								</div>
								<div class="portlet box blue-steel">
									<div class="portlet-title">
										SOBRESUELDO
									</div>
									<div class="portlet-body">
										<div class="form-group">
											<label class="col-md-3 control-label" for="sobresueldo_otros_1">SOBRESUELDO 1:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sobresueldo_otros_1" name="sobresueldo_otros_1" value="<?php echo $sobresueldo_otros_1; ?>">
											</div>
											<label class="col-md-1 control-label" for="mes_ot_1">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_ot_1" name="mes_ot_1" value="<?php echo $mes_ot_1; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sobresueldo_otros_2">SOBRESUELDO 2:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sobresueldo_otros_2" name="sobresueldo_otros_2" value="<?php echo $sobresueldo_otros_2; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_ot_2">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_ot_2" name="mes_ot_2" value="<?php echo $mes_ot_2; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_propuesto_80">SOBRESUELDO:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_propuesto_80" name="sueldo_propuesto_80" value="<?php echo $sueldo_propuesto_80; ?>">
											</div>
											 
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label" for="partida080">Partida Presupuestaria SobreSueldo</label>
											<div class="col-md-5">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida080" id="partida080" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida080== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
									
											<div class="col-md-offset-5 col-md-1"><button type="submit" class="btn blue active" id="btn-guardar" name="btn-guardar">GUARDAR</button></div>
											<div class="col-md-3"><button type="button" class="btn default active" onclick="javascript: document.location.href='maestro_posicion.php'">CANCELAR</button></div>
										</div>
									</div>
								</div>
								<div class="portlet box blue-hoki">
									<div class="portlet-title"> GASTOS DE REPRESENTACIÓN</div>
										<div class="portlet-body">
											<div class="form-group">
											<label class="col-md-3 control-label" for="gastos_repr">GASTOS DE REPRESENTACI&Oacute;N 030 1:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="gastos_repr" name="gastos_repr" value="<?php echo $gastos_repr; ?>" >
											</div>
											<label class="col-md-1 control-label" for="mes_gasto_1">MESES:</label>
											<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_gasto_1" name="mes_gasto_1" value="<?php echo $mes_gasto_1; ?>">
											</div>
										</div>	
										<div class="form-group">
											<label class="col-md-3 control-label" for="gasto_representacion_2">GASTOS DE REPRESENTACI&Oacute;N 030 2:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="gasto_representacion_2" name="gasto_representacion_2" value="<?php echo $gasto_representacion_2; ?>">
											</div>
											<label class="col-md-1 control-label" for="mes_gasto_2">MESES:</label>
												<div class="col-md-1">
												<input type="text" class="form-control input-sm" 
											       id="mes_gasto_2" name="mes_gasto_2" value="<?php echo $mes_gasto_2; ?>">
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="gastos_repr">PAGA GASTOS REPRESENTACI&Oacute;N:</label>
											<div class="col-md-3">												
												<select name="paga_gr" id="paga_gr" class="select2 form-control">
													<option value="">SELECCIONE PAGA GR</option>
													<option value="0" <?php echo ($paga_gr=='0') ? 'selected' : ''; ?> >No</option>
													<option value="1" <?php echo ($paga_gr=='1') ? 'selected' : ''; ?> >Si</option>
												</select>
											</div>
										</div>										
										<div class="form-group">
											<label class="col-md-3 control-label" for="sueldo_propuesto_30">SUELDO PROPUESTO 030:</label>
											<div class="col-md-3">
												<input type="text" class="form-control input-sm" 
											       id="sueldo_propuesto_30" name="sueldo_propuesto_30" value="<?php echo $sueldo_propuesto_30; ?>">
											</div>
											 
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="partida030">Partida Presupuestaria Gastos</label>
											<div class="col-md-5">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida030" id="partida030" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida030== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>



										<div class="form-group">
									
											<div class="col-md-offset-5 col-md-1"><button type="submit" class="btn blue active" id="btn-guardar" name="btn-guardar">GUARDAR</button></div>
											<div class="col-md-3"><button type="button" class="btn default active" onclick="javascript: document.location.href='maestro_posicion.php'">CANCELAR</button></div>
										</div>
									</div>
								</div>
								<div class="portlet box green-seagreen">
									<div class="portlet-title">Otros</div>
									<div class="portlet-body">
										<div class="form-group">
											<label class="col-md-3 control-label" for="dieta">DIETAS:</label>
											<div class="col-md-3">
											<input type="text" class="form-control input-sm" 
											       id="dieta" name="dieta" value="<?php echo $dieta; ?>">
											</div>
											<label class="col-md-2 control-label" for="partida011">Partida Presupuestaria Dieta</label>
											<div class="col-md-4">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida011" id="partida011" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida011== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="combustible">COMBUSTIBLE:</label>
											<div class="col-md-3">
											 <input type="text" class="form-control input-sm" 
											       id="combustible" name="combustible" value="<?php echo $combustible; ?>">
											</div>
											<label class="col-md-2 control-label" for="partida012">Partida Presupuestaria Combustible</label>
											<div class="col-md-4">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida012" id="partida012" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida012== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3 control-label" for="viaticos">VIATICOS:</label>
											<div class="col-md-3">
											 <input type="text" class="form-control input-sm" 
											       id="sueldo_propuesto_13" name="sueldo_propuesto_13" value="<?php echo $sueldo_propuesto_13; ?>">
											</div>
											<label class="col-md-2 control-label" for="partida013">Partida Presupuestaria Viaticos</label>
											<div class="col-md-4">
											<?php 
												$query = "SELECT * FROM cwprecue";
												$res   = query($query, $conexion);
											?>
												<select name="partida013" id="partida013" class="select2 form-control">
													<option value="">SELECCIONE ......</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $partida013== $fila['CodCue'] )
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>" selected><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['CodCue']; ?>"><?php echo $fila['CodCue']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
		

										
										<div class="form-group">
											<label class="col-md-3 control-label" for="categoria_id">TIPO EMPLEADO:</label>
											<div class="col-md-5">
											<?php 
												$query = "SELECT * FROM nomcategorias";
												$res = query($query, $conexion);
											?>
												<select name="categoria_id" id="categoria_id" class="select2 form-control">
													<option value="">SELECCIONE UN TIPO EMPLEADO</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $categoria_id == $fila['codorg'] )
														{ ?>
															<option value="<?php echo $fila['codorg']; ?>" selected><?php echo $fila['descrip']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['codorg']; ?>"><?php echo $fila['descrip']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-md-3 control-label" for="objeto">OBJETO:</label>
											<div class="col-md-2">
												<input type="text" class="form-control input-sm" 
											       id="objeto" name="objeto" value="<?php echo $objeto; ?>" >
											</div>
											<label class="col-md-1 control-label" for="unidad">UNIDAD:</label>
											<div class="col-md-2">
												<input type="text" class="form-control input-sm" 
											       id="unidad" name="unidad" value="<?php echo $unidad; ?>">
											</div>										
										</div>	
										<div class="form-group">
											<label class="col-md-3 control-label" for="estado">STATUS:</label>
											<div class="col-md-5">											
												<select name="estado" id="estado" class="select2 form-control">
													<option value="">Seleccione</option>
													<option value="0" <?php echo (!$estado) ? 'selected' : ''; ?> >VACANTE</option>
													<option value="1" <?php echo ($estado) ? 'selected' : ''; ?> >OCUPADO</option>
												</select>
											</div>
										</div>
										<div class="form-group">
									
											<div class="col-md-offset-5 col-md-1"><button type="submit" class="btn blue active" id="btn-guardar" name="btn-guardar">GUARDAR</button></div>
											<div class="col-md-3"><button type="button" class="btn default active" onclick="javascript: document.location.href='maestro_posicion.php'">CANCELAR</button></div>
										</div>
									</div>
									</div>
								</div>								
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#mes_t").val(sumar());
		$('#formPrincipal').validate({
	            rules: {
	                descripcion: {
	                    required: true
	                },
	                sueldo_propuesto: { required: true },
	                sueldo_anual: { required: true },
	                mes_1:{ range:[1,12] },
	               // mes_2:{ range:[1,12] },
	              //  mes_3:{ range:[1,12] },
	              //  mes_4:{ range:[1,12] },
	                mes_t:{ max:12 }

	            },

	            messages: {
	                descripcion: { required: " Campo requerido" },
	                sueldo_propuesto: { required: "Campo requerido "},
	                sueldo_anual: { required: " Campo requerido" },
	                mes_1:{range: "Numero entre 1 y 12", required:false},
	                mes_2:{range: "Numero entre 1 y 12", required:false},
	                mes_3:{range: "Numero entre 1 y 12", required:false},
	                mes_4:{range: "Numero entre 1 y 12", required:false},
	                mes_t:{max: "Numero menor a 12", required:false}

	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },
	    });
/*
		$("#partida").select2({
		 	placeholder: 'Seleccione una partida',
            allowClear: true,
        });

		$("#paga_gr").select2({
		 	placeholder: 'Seleccione paga GR',
            allowClear: true,
        });
*/		function numeralizar(num){
			if (num!="") 
				return parseInt(num);			
			else
				return 0;
		}
		function deshabilitar1() {
			if(sumar()==12)
			{
				$("#mes_2").attr("disabled",true);
				$("#mes_3").attr("disabled",true);
				$("#mes_4").attr("disabled",true);
				$("#sueldo_2").attr("disabled",true);
				$("#sueldo_3").attr("disabled",true);
				$("#sueldo_4").attr("disabled",true);				
			}
			else
				if(sumar()<12)
			{
				$("#mes_2").attr("disabled",false);
				$("#mes_3").attr("disabled",false);
				$("#mes_4").attr("disabled",false);
				$("#sueldo_2").attr("disabled",false);
				$("#sueldo_3").attr("disabled",false);
				$("#sueldo_4").attr("disabled",false);				
			}
		}
		function deshabilitar2() {
			if(sumar()==12)
			{
				$("#mes_3").attr("disabled",true);
				$("#mes_4").attr("disabled",true);
				$("#sueldo_3").attr("disabled",true);
				$("#sueldo_4").attr("disabled",true);				
			}
			else
				if(sumar()<12)
			{
				$("#mes_3").attr("disabled",false);
				$("#mes_4").attr("disabled",false);
				$("#sueldo_3").attr("disabled",false);
				$("#sueldo_4").attr("disabled",false);				
			}
		}
		function deshabilitar3() {
			if(sumar()==12)
			{
				$("#mes_4").attr("disabled",true);
				$("#sueldo_4").attr("disabled",true);				
			}
			else
				if(sumar()<12)
			{
				$("#mes_4").attr("disabled",false);
				$("#sueldo_4").attr("disabled",false);				
			}
		}
		function cant_meses()
		{
			$("#sueldo_anual").empty();
			cant_mes1=numeralizar($("#mes_1").val());
			cant_mes2=numeralizar($("#mes_2").val());
			cant_mes3=numeralizar($("#mes_3").val());
			cant_mes4=numeralizar($("#mes_4").val());
			sueldo1=numeralizar($("#sueldo_1").val());
			sueldo2=numeralizar($("#sueldo_2").val());			
			sueldo3=numeralizar($("#sueldo_3").val());
			sueldo4=numeralizar($("#sueldo_4").val());
			sueldo_anual=cant_mes1*sueldo1+cant_mes2*sueldo2+cant_mes3*sueldo3+cant_mes4*sueldo4;
			$("#sueldo_anual").val(sueldo_anual);
		}
		function sumar(){
			$("#mes_t").empty();
			mes1 =numeralizar($("#mes_1").val());
			mes2 =numeralizar($("#mes_2").val());
			mes3 =numeralizar($("#mes_3").val());
			mes4 =numeralizar($("#mes_4").val());
			mest =mes1+mes2+mes3+mes4;	
			return mest;
		}
		function sumarsueldos(){
			$("#sueldo_anual").empty();
			mes1 =numeralizar($("#sueldo_1").val());
			mes2 =numeralizar($("#sueldo_2").val());
			mes3 =numeralizar($("#sueldo_3").val());
			mes4 =numeralizar($("#sueldo_4").val());
			
			mest =mes1+mes2+mes3+mes4;	
			return mest;
		}
		function validar(){
			$("#mes_t").validate({
				rules: { mes_t:{ max:12}},
				messages: {mes_t:{max: "Mes menor a 12", required:false}}
			});
		}
		$("#mes_1").change(function(){
			mest =sumar();
			console.log("hola "+mest);
			$("#mes_t").val(sumar());
			deshabilitar1();
			cant_meses();
			validar();
		});
		$("#mes_2").change(function(){
			$("#mes_t").val(sumar());
			deshabilitar2();
			cant_meses();

			validar();
		});
		$("#mes_3").change(function(){
			$("#mes_t").val(sumar());
			deshabilitar3();
			cant_meses();

			validar();
		});
		$("#mes_4").change(function(){
			$("#mes_t").val(sumar());
			cant_meses();

			validar();
			
		});
		$("#sueldo_1").change(function(){
			$("#sueldo_anual").val(sumarsueldos());
		});
		$("#sueldo_2").change(function(){
			$("#sueldo_anual").val(sumarsueldos());
		});
		$("#sueldo_3").change(function(){
			$("#sueldo_anual").val(sumarsueldos());
		});
		$("#sueldo_4").change(function(){
			$("#sueldo_anual").val(sumarsueldos());
			
		});
		$("#categoria_id").select2({
		 	placeholder: 'Seleccione un Tipo Empleado',
            allowClear: true,
        });

		$("#cargo_id").select2({
		 	placeholder: 'Seleccione un cargo',
            //allowClear: true,
        });

		$( "#btn-guardar" ).click(function() {
			/*
		    var descripcion = $('#descripcion').val();

		    if( descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		    */
		});
	});
</script>
</body>
</html>