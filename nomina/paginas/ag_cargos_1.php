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
	$cod_car                          	    = ( isset($_POST['txtcodigo']) )        ? $_POST['txtcodigo']      : '' ;
	$des_car          						= ( isset($_POST['txtdescripcion']) )   ? $_POST['txtdescripcion']          : '' ;
	$gremio                          	    = ( isset($_POST['gremio']) )            ? $_POST['gremio']      : '' ;
	$clave                          	    = ( isset($_POST['clave']) )            ? $_POST['clave']      : '' ;
	$sueldo_1   	  						= ( isset($_POST['sueldo_1']) )         ? $_POST['sueldo_1']         : '' ;
	$sueldo_2   	  						= ( isset($_POST['sueldo_2']) )         ? $_POST['sueldo_2']         : '' ;
    $sueldo_3   	  						= ( isset($_POST['sueldo_3']) )         ? $_POST['sueldo_3']         : '' ;
	$sueldo_4   	  						= ( isset($_POST['sueldo_4']) )         ? $_POST['sueldo_4']         : '' ;
	$mes_1       	  						= ( isset($_POST['mes_1']) )            ? $_POST['mes_1']            : '' ;
 	$mes_2       	  						= ( isset($_POST['mes_2']) )            ? $_POST['mes_2']            : '' ;
	$mes_3      	  						= ( isset($_POST['mes_3']) )            ? $_POST['mes_3']            : '' ;
	$mes_4      	  						= ( isset($_POST['mes_4']) )            ? $_POST['mes_4']            : '' ;
	$sueldo_propuesto 						= ( isset($_POST['sueldo_propuesto']) ) ? $_POST['sueldo_propuesto'] : '' ; 
	$sueldo_anual     						= ( isset($_POST['sueldo_anual']) )     ? $_POST['sueldo_anual']     : '' ;
	$sobresueldo_antiguedad_1 				= ( isset($_POST['sobresueldo_antiguedad_1']) ) ? $_POST['sobresueldo_antiguedad_1'] : '' ;
	$sobresueldo_antiguedad_2 				= ( isset($_POST['sobresueldo_antiguedad_2']) ) ? $_POST['sobresueldo_antiguedad_2'] : '' ;
	$mes_ant_1       	  					= ( isset($_POST['mes_ant_1']) )            ? $_POST['mes_ant_1']            : '' ;
	$mes_ant_2       	  					= ( isset($_POST['mes_ant_2']) )            ? $_POST['mes_ant_2']            : '' ;
	$sueldo_propuesto_11 					= ( isset($_POST['sueldo_propuesto_11']) ) ? $_POST['sueldo_propuesto_11'] : '' ;
	$sobresueldo_zona_apartada_1 			= ( isset($_POST['sobresueldo_zona_apartada_1']) ) ? $_POST['sobresueldo_zona_apartada_1'] : '' ;
	$sobresueldo_zona_apartada_2 			= ( isset($_POST['sobresueldo_zona_apartada_2']) ) ? $_POST['sobresueldo_zona_apartada_2'] : '' ;
	$mes_za_1       	  					= ( isset($_POST['mes_za_1']) )            ? $_POST['mes_za_1']            : '' ;
	$mes_za_2       	  					= ( isset($_POST['mes_za_2']) )            ? $_POST['mes_za_2']            : '' ;
	$sueldo_propuesto_12 					= ( isset($_POST['sueldo_propuesto_12']) ) ? $_POST['sueldo_propuesto_12'] : '' ;
	$sobresueldo_jefatura_1 				= ( isset($_POST['sobresueldo_jefatura_1']) ) ? $_POST['sobresueldo_jefatura_1'] : '' ;
	$sobresueldo_jefatura_2 				= ( isset($_POST['sobresueldo_jefatura_2']) ) ? $_POST['sobresueldo_jefatura_2'] : '' ;
	$mes_jef_1       	  					= ( isset($_POST['mes_jef_1']) )            ? $_POST['mes_jef_1']            : '' ;
	$mes_jef_2       	  					= ( isset($_POST['mes_jef_2']) )            ? $_POST['mes_jef_2']            : '' ;
	$sueldo_propuesto_13 					= ( isset($_POST['sueldo_propuesto_13']) ) ? $_POST['sueldo_propuesto_13'] : '' ;
	$sobresueldo_esp_1  					= ( isset($_POST['sobresueldo_esp_1']) ) ? $_POST['sobresueldo_esp_1']  : '' ;
    $sobresueldo_esp_2  					= ( isset($_POST['sobresueldo_esp_2']) ) ? $_POST['sobresueldo_esp_2']  : '' ;
	$mes_esp_1       	  					= ( isset($_POST['mes_esp_1']) )            ? $_POST['mes_esp_1']            : '' ;
    $mes_esp_2       	  					= ( isset($_POST['mes_esp_2']) )            ? $_POST['mes_esp_2']            : '' ;
    $sueldo_propuesto_19 					= ( isset($_POST['sueldo_propuesto_19']) ) ? $_POST['sueldo_propuesto_19'] : '' ;
	$sobresueldo_otros_1 					= ( isset($_POST['sobresueldo_otros_1']) ) ? $_POST['sobresueldo_otros_1'] : '' ;
	$sobresueldo_otros_2 					= ( isset($_POST['sobresueldo_otros_2']) ) ? $_POST['sobresueldo_otros_2'] : '' ;
	$mes_ot_1       	  					= ( isset($_POST['mes_ot_1']) )            ? $_POST['mes_ot_1']            : '' ;
	$mes_ot_2       	  					= ( isset($_POST['mes_ot_2']) )            ? $_POST['mes_ot_2']            : '' ;
	$sueldo_propuesto_30 					= ( isset($_POST['sueldo_propuesto_30']) ) ? $_POST['sueldo_propuesto_30'] : '' ;
	$gastos_repr      						= ( isset($_POST['gastos_repr']) )      ? $_POST['gastos_repr']      : '' ;
	$gasto_representacion_2 				= ( isset($_POST['gasto_representacion_2']) ) ? $_POST['gasto_representacion_2'] : '' ;
    $mes_gasto_1       	  					= ( isset($_POST['mes_gasto_1']) )          ? $_POST['mes_gasto_1']          : '' ;
    $mes_gasto_2       	  					= ( isset($_POST['mes_gasto_2']) )            ? $_POST['mes_gasto_2']        : '' ;
    $sueldo_propuesto_80 					= ( isset($_POST['sueldo_propuesto_80']) ) ? $_POST['sueldo_propuesto_80'] : '' ;
	$paga_gr          						= ( isset($_POST['paga_gr']) )          ? $_POST['paga_gr']          : '' ;
	$grupo 					                = ( isset($_POST['grupo']) ) ? $_POST['grupo'] : '' ;
    $total_car 					            = ( isset($_POST['total']) ) ? $_POST['total'] : '' ;
    $des_car1          						= ( isset($_POST['txtdescripcion1']) )   ? $_POST['txtdescripcion1']          : '' ;
	if( empty($id) )
	{
		$sql = "INSERT INTO nomcargos 
                    (cod_cargo,
                    cod_car,
                    des_car,
                    grado,
                    gremio,
                    clave,
                    antiguedad,
                    zona_apartada,
                    otros,
                    sueldo_1,
                    sueldo_2,
                    sueldo_3,
                    sueldo_4,
                    sueldo_anual,
                    sueldo_propuesto,
                    mes_1,
                    mes_2,
                    mes_3,
                    mes_4,
                    sobresueldo_antiguedad_1,
                    sobresueldo_antiguedad_2,
                    mes_ant_1,
                    mes_ant_2,
                    sobresueldo_zona_apartada_1,
                    sobresueldo_zona_apartada_2,
                    mes_za_1,
                    mes_za_2,
                    sobresueldo_esp_1,
                    sobresueldo_esp_2,
                    mes_esp_1,
                    mes_esp_2,
                    sueldo_propuesto_19,
                    sobresueldo_jefatura_1,
                    sobresueldo_jefatura_2,
                    mes_jef_1,
                    mes_jef_2,
                    sueldo_propuesto_13,
                    sobresueldo_otros_1,
                    sobresueldo_otros_2,
                    mes_ot_1,
                    mes_ot_2,
                    gastos_representacion,
                    gasto_representacion_2,
                    mes_gasto_1,
                    mes_gasto_2,
                    sueldo_propuesto_30,
                    paga_gr,
                    total,
                    disp,
                    descrip_corto)
             
            VALUES ('{}',
            '{$cod_car}',
            '{$des_car}',
            '{$grupo}',
            '{$gremio}',
            '{$clave}',
            '{$sueldo_propuesto_11}',
            '{$sueldo_propuesto_12}',
            '{$sueldo_propuesto_80}',
            '{$sueldo_1}',
            '{$sueldo_2}',
            '{$sueldo_3}',
            '{$sueldo_4}',
            '{$sueldo_anual}',
            '{$sueldo_propuesto}',
            '{$mes_1}',
            '{$mes_2}',
            '{$mes_3}',
            '{$mes_4}',
            '{$sobresueldo_antiguedad_1}',
            '{$sobresueldo_antiguedad_2}',
            '{$mes_ant_1}',
            '{$mes_ant_2}',
            '{$sobresueldo_zona_apartada_1}',
            '{$sobresueldo_zona_apartada_2}',
            '{$mes_za_1}',
            '{$mes_za_2}',
            '{$sobresueldo_esp_1}',
            '{$sobresueldo_esp_2}',
            '{$mes_esp_1}',
            '{$mes_esp_2}',
            '{$sueldo_propuesto_19}',
            '{$sobresueldo_jefatura_1}',
            '{$sobresueldo_jefatura_2}',
            '{$mes_jef_1}',
            '{$mes_jef_2}',
            '{$sueldo_propuesto_13}',
            '{$sobresueldo_otros_1}',
            '{$sobresueldo_otros_2}',
            '{$mes_ot_1}',
            '{$mes_ot_2}',
            '{$gastos_repr}',
            '{$gasto_representacion_2}',
            '{$mes_gasto_1}',
            '{$mes_gasto_2}',
            '{$sueldo_propuesto_30}',
            '{$paga_gr}',
            '{$total_car}',
            '{$total_car}',
            '{$des_car1}')";
            $res = query($sql, $conexion);
            activar_pagina("cargos.php");
	}
	else
	{
		$modif 					     = ( isset($_POST['modif']) ) ? $_POST['modif'] : '0' ;
		$modifpers					 = ( isset($_POST['modifpers']) ) ? $_POST['modifpers'] : '0' ;
		 $sql = "UPDATE nomcargos SET 
				cod_car  			  = '{$cod_car}',
				des_car      		  = '{$des_car}',
				grado      		      = '{$grupo}',
				gremio      		  = '{$gremio}',
				clave      		      = '{$clave}',
				antiguedad            = '{$sueldo_propuesto_11}',
				zona_apartada         = '{$sueldo_propuesto_12}',
				otros                 = '{$sueldo_propuesto_80}',
				sueldo_1              = '{$sueldo_1}',
				sueldo_2              = '{$sueldo_2}',
				sueldo_3              = '{$sueldo_3}',
				sueldo_4              = '{$sueldo_4}',
				sueldo_anual          = '{$sueldo_anual}',
				sueldo_propuesto      = '{$sueldo_propuesto}',
				mes_1                 = '{$mes_1}',
				mes_2                 = '{$mes_2}',
				mes_3                 = '{$mes_3}',
				mes_4                 = '{$mes_4}',
				sobresueldo_antiguedad_1  = '{$sobresueldo_antiguedad_1}',
				sobresueldo_antiguedad_2  = '{$sobresueldo_antiguedad_2}',
				mes_ant_1             = '{$mes_ant_1}',
				mes_ant_2             = '{$mes_ant_2}',
				sobresueldo_zona_apartada_1  = '{$sobresueldo_zona_apartada_1}',
				sobresueldo_zona_apartada_2  = '{$sobresueldo_zona_apartada_2}',
				mes_za_1              = '{$mes_za_1}',
				mes_za_2              = '{$mes_za_2}',
				sobresueldo_esp_1     = '{$sobresueldo_esp_1}',
 				sobresueldo_esp_2     = '{$sobresueldo_esp_2}',
				mes_esp_1  			  = '{$mes_esp_1}',
 				mes_esp_2 			  = '{$mes_esp_2}',
				sueldo_propuesto_19   = '{$sueldo_propuesto_19}',
				sobresueldo_jefatura_1  = '{$sobresueldo_jefatura_1}',
				sobresueldo_jefatura_2  = '{$sobresueldo_jefatura_2}',
				mes_jef_1             = '{$mes_jef_1}',
				mes_jef_2             = '{$mes_jef_2}',
				sueldo_propuesto_13   = '{$sueldo_propuesto_13}',
				sobresueldo_otros_1   = '{$sobresueldo_otros_1}',
				sobresueldo_otros_2   = '{$sobresueldo_otros_2}',
				mes_ot_1              = '{$mes_ot_1}',
			    mes_ot_2              = '{$mes_ot_2}',
				gastos_representacion = '{$gastos_repr}',
 				gasto_representacion_2 = '{$gasto_representacion_2}',
 				mes_gasto_1  		  = '{$mes_gasto_1}',
 				mes_gasto_2           = '{$mes_gasto_2}',
 				sueldo_propuesto_30   = '{$sueldo_propuesto_30}',
 				paga_gr               = '{$paga_gr}',
 				total                 = '{$total_car}',
 				disp                  = '{$total_car}',
 				descrip_corto     		  = '{$des_car1}'

				WHERE cod_cargo  = " . $id;
				$res = query($sql, $conexion);
				if($modif==1)
				{
					$sql= "UPDATE nomposicion SET 
					sueldo_1              = '{$sueldo_1}',
					sueldo_2              = '{$sueldo_2}',
					sueldo_3              = '{$sueldo_3}',
					sueldo_4              = '{$sueldo_4}',
					mes_1                 = '{$mes_1}',
					mes_2                 = '{$mes_2}',
					mes_3                 = '{$mes_3}',
					mes_4                 = '{$mes_4}',
					sueldo_propuesto      = '{$sueldo_propuesto}'
					WHERE cargo_id  = " . $id;
					$res = query($sql, $conexion);
				}
				if($modifpers==1)
				{
					$sql= "UPDATE nompersonal SET 
					suesal              = '{$sueldo_1}'
					WHERE codcargo = " . $id;
					$res = query($sql, $conexion);
				}
				activar_pagina("cargos.php");
	}		 
}

$descripcion = $sueldo_propuesto = $sueldo_anual = $partida = $gastos_repr = $paga_gr = $categoria_id = $cargo_id = '';

if(isset($_GET['edit']))
{
	 $sql = "SELECT cod_cargo, "
                 . "cod_car, "
                 . "des_car,"
                 . "grado,"
                 . "clave,"
                 . "gremio,"
                 . "sueldo_1,"
                 . "sueldo_2,"
                 . "sueldo_3,"
                 . "sueldo_4,"
                 . "mes_1,"
                 . "mes_2,"
                 . "mes_3,"
                 . "mes_4,"
                 . "sueldo_propuesto,"
                 . "sueldo_anual,"
                 . "sobresueldo_antiguedad_1,"
                 . "sobresueldo_antiguedad_2,"
                 . "mes_ant_1,"
                 . "mes_ant_2,"
                 . "sobresueldo_zona_apartada_1,"
                 . "sobresueldo_zona_apartada_2,"
                 . "mes_za_1,"
                 . "mes_za_2,"
                 . "sobresueldo_jefatura_1,"
                 . "sobresueldo_jefatura_2,"
                 . "mes_jef_1,"
                 . "mes_jef_2,"
                 . "sobresueldo_otros_1,"
                 . "sobresueldo_otros_2,"
                 . "mes_ot_1,mes_ot_2,"
                 . "sobresueldo_esp_1,"
                 . "sobresueldo_esp_2,"
                 . "mes_esp_1,"
                 . "mes_esp_2,"
                 . "gastos_representacion,"
                 . "gasto_representacion_2,"
                 . "mes_gasto_1,"
                 . "mes_gasto_2,"
                 . "antiguedad,"
                 . "zona_apartada,"
                 . "sueldo_propuesto_13,"
                 . "sueldo_propuesto_19,"
                 . "sueldo_propuesto_30,"
                 . "otros,"
                 . "paga_gr,"
                 . "total,"
                 . "descrip_corto "
                 . "FROM nomcargos "
                 . "WHERE cod_cargo = " . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$cod_cargo        = $fila['cod_car']; // CODIGO DEL CARGO EN LA EMPRESA
		$cargo            = $fila['des_car']; // NOMBRE DEL CARGO
		$cargo1            = $fila['descrip_corto']; // NOMBRE DEL CARGO corto
		$grupo            = $fila['grado']; // TIPO DE CARGO
		$gremio           = $fila['gremio']; // GRUPO DEL CARGO
		$clave            = $fila['clave']; // CLAVE
		$sueldo_1         = $fila['sueldo_1']; // SUELDO 1
		$sueldo_2         = $fila['sueldo_2']; // SUELDO 2
		$sueldo_3         = $fila['sueldo_3']; // SUELDO 3
		$sueldo_4         = $fila['sueldo_4']; // SUELDO 4
		$mes_1            = $fila['mes_1']; // MESES DEL SUELDO 1
		$mes_2            = $fila['mes_2']; // MESES DEL SUELDO 2
		$mes_3            = $fila['mes_3']; // MESES DEL SUELDO 3
		$mes_4            = $fila['mes_4']; // MESES DEL SUELDO 4
		$sueldo_propuesto = $fila['sueldo_propuesto']; // SUELDO PROPUESTO
		$sueldo_anual     = $fila['sueldo_anual']; // SUELDO ANUAL
		$sobresueldo_antiguedad_1  = $fila['sobresueldo_antiguedad_1']; // SOBRESUELDO POR ANTIGUEDAD 1
		$sobresueldo_antiguedad_2  =$fila['sobresueldo_antiguedad_2']; // SOBRESUELDO POR ANTIGUEDAD 1
		$mes_ant_1            = $fila['mes_ant_1']; // MESES DEL SOBRESUELDO POR ANTIGUEDAD 1
		$mes_ant_2            = $fila['mes_ant_2']; // MESES DEL SOBRESUELDO POR ANTIGUEDAD 2
		$sobresueldo_zona_apartada_1  = $fila['sobresueldo_zona_apartada_1']; // SOBRESUELDO POR ZONA APARTADA 1
		$sobresueldo_zona_apartada_2  = $fila['sobresueldo_zona_apartada_2']; // SOBRESUELDO POR ZONA APARTADA 2
		$mes_za_1            = $fila['mes_za_1']; // MESES DEL SOBRESUELDO POR ZONA APARTADA 1
		$mes_za_2            = $fila['mes_za_2']; // MESES DEL SOBRESUELDO POR ZONA APARTADA 2
		$sobresueldo_jefatura_1  = $fila['sobresueldo_jefatura_1']; // SOBRESUELDO POR JEFATURA 1
		$sobresueldo_jefatura_2  = $fila['sobresueldo_jefatura_2']; // SOBRESUELDO POR JEFATURA 2
		$mes_jef_1            = $fila['mes_jef_1']; // MESES DEL SOBRESUELDO POR JEFATURA 1
		$mes_jef_2            = $fila['mes_jef_2']; // MESES DEL SOBRESUELDO POR JEFATURA 1
		$sobresueldo_otros_1  = $fila['sobresueldo_otros_1']; // SOBRESUELDO POR OTROS CARGOS 1
		$sobresueldo_otros_2  = $fila['sobresueldo_otros_2']; // SOBRESUELDO POR OTROS CARGOS 2
		$mes_ot_1            = $fila['mes_ot_1']; // MESES DEL SOBRESUELDO POR OTROS CARGOS 1
		$mes_ot_2            = $fila['mes_ot_2']; // MESES DEL SOBRESUELDO POR OTROS CARGOS 2
		$sobresueldo_esp_1              = $fila['sobresueldo_esp_1']; //SOBRESUELDO POR ESPECIALIDAD 1
 		$sobresueldo_esp_2              = $fila['sobresueldo_esp_2']; //SOBRESUELDO POR ESPECIALIDAD 2
		$mes_esp_1              = $fila['mes_esp_1'];// MESES DEL SOBRESUELDO POR ESPECIALIDAD 1						
 		$mes_esp_2              = $fila['mes_esp_2'];// MESES DEL SOBRESUELDO POR ESPECIALIDAD 2
 		$gastos_repr      		= $fila['gastos_representacion']; // GASTOS POR REPRESENTACION 1
 		$gasto_representacion_2 = $fila['gasto_representacion_2']; // GASTOS POR REPRESENTACION 2
 		$mes_gasto_1              = $fila['mes_gasto_1'];// MESES DE LOS GASTOS POR REPRESENTACION 1
 		$mes_gasto_2              = $fila['mes_gasto_2'];// MESES DE LOS GASTOS POR REPRESENTACION 2
 		$sueldo_propuesto_11              = $fila['antiguedad'];// SOBRESUELDO PROPUESTO POR ANTIGUEDAD 
 		$sueldo_propuesto_12              = $fila['zona_apartada'];// SOBRESUELDO PROPUESTO POR ZONA APARTADA 
 		$sueldo_propuesto_13              = $fila['sueldo_propuesto_13'];// SOBRESUELDO PROPUESTO POR JEFATURA
 		$sueldo_propuesto_19              = $fila['sueldo_propuesto_19'];// SOBRESUELDO PROPUESTO POR ESPECIALIDAD
 		$sueldo_propuesto_30              = $fila['sueldo_propuesto_30'];// SOBRESUELDO PROPUESTO POR GASTOS POR REPRESENTACION
 		$sueldo_propuesto_80              = $fila['otros'];// SOBRESUELDO PROPUESTO POR OTROS CARGOS
 		$paga_gr          = $fila['paga_gr'];// PAGO DE GASTOS DE REPRESENTACION
 		$max_car          = $fila['total'];//MAXIMO DE CARGOS 
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
.panel-default > .panel-heading {
  color: #333 !important;
  background-color: #E0FFFF !important;
  border-color: #ddd;
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
							<?php 
							if(empty($id))
							{
								echo "Agregar Cargos";
							}
							else
							{
								echo "Modificar Cargos";
							}
							?>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
								<div class="panel panel-default">
  								<div class="panel-heading">Datos Principales del Cargo:</div>
  								<div class="panel-body">	
									<div class="form-group">
										<label class="col-md-3 control-label">1) Código del cargo:</label>
										<div class="col-md-3">
											<input name="txtcodigo"  class="form-control" type="number" id="txtcodigo" value="<?php echo $cod_cargo ?>" maxlength="10" required>
										</div>	
									</div>
										<div class="form-group">
										<label class="col-md-3 control-label">2) Descripción Corta:</label>
										<div class="col-md-3">
											<input name="txtdescripcion1"  class="form-control" type="text" id="txtdescripcion1" value="<?php echo $cargo1 ?>" maxlength="20" required>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">2) Descripción del cargo:</label>
										<div class="col-md-3">
											<input name="txtdescripcion"  class="form-control" type="text" id="txtdescripcion" value="<?php echo $cargo ?>" maxlength="100" required>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label">3) Tipo:</label>
										<div class="col-md-3">											
											<select name="gremio" id="gremio" class="select2 form-control">
												<option value="">Seleccione el tipo de cargo</option>
												<option value="0" <?php echo ($gremio=='0') ? 'selected' : ''; ?> >Gremio</option>
												<option value="1" <?php echo ($gremio=='1') ? 'selected' : ''; ?> >Administrativo</option>
											</select>
										</div>
									</div>	
									<div class="form-group">
										<label class="col-md-3 control-label">4) Clave:</label>
										<div class="col-md-3">
											<input name="clave"  class="form-control" type="number" id="clave" value="<?php echo $clave ?>" maxlength="20">
										</div>	
									</div>
								</div>
								</div>
								<div class="panel panel-default">
  								<div class="panel-heading">Sueldos del Cargo:</div>
  								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_1">5) Sueldo Mensual 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_1" name="sueldo_1" value="<?php echo $sueldo_1; ?>">
										</div>
										<label class="col-md-2 control-label" for="mes_1">5.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_1" name="mes_1" value="<?php echo $mes_1; ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_2">6) Sueldo Mensual 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_2" name="sueldo_2" value="<?php echo $sueldo_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_2">6.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_2" name="mes_2" value="<?php echo $mes_2; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_3">7) Sueldo Mensual 3:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_3" name="sueldo_3" value="<?php echo $sueldo_3; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_3">7.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_3" name="mes_3" value="<?php echo $mes_3; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_4">8) Sueldo Mensual 4:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_4" name="sueldo_4" value="<?php echo $sueldo_4; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_4">8.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_4" name="mes_4" value="<?php echo $mes_4; ?>" >
										</div>
									</div>
								 
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_anual">9) Sueldo Anual:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_anual" name="sueldo_anual" value="<?php echo $sueldo_anual; ?>" readonly>
										</div>
										<label class="col-md-2 control-label" for="mes_t">9.1) Total</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" id="mes_t" name="mes_t" value="<?php echo $mes_t; ?>" readonly>
										</div>
										<label class="col-md-2 control-label" for="mes_t">Meses</label>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto">10) Sueldo Propuesto :</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto" name="sueldo_propuesto" value="<?php echo $sueldo_propuesto; ?>">
										</div>
									 </div>
								</div>
								</div>
								<div class="panel panel-default">
  								<div class="panel-heading">Sobresueldos del cargo:</div>
  								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_antiguedad_1">11) Antiguedad (011) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_antiguedad_1" name="sobresueldo_antiguedad_1" value="<?php echo $sobresueldo_antiguedad_1; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ant_1">11.1)Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_ant_1" name="mes_ant_1" value="<?php echo $mes_ant_1; ?>" >
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_antiguedad_2">12)Antiguedad (011) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_antiguedad_2" name="sobresueldo_antiguedad_2" value="<?php echo $sobresueldo_antiguedad_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ant_1">12.1)Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_ant_2" name="mes_ant_2" value="<?php echo $mes_ant_2; ?>" >
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_11">13) Sueldo Propuesto (011):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto_11" name="sueldo_propuesto_11" value="<?php echo $sueldo_propuesto_11; ?>">
										</div>
									 </div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_zona_apartada_1">14) Zona Apartada (012) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_zona_apartada_1" name="sobresueldo_zona_apartada_1" value="<?php echo $sobresueldo_zona_apartada_1; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_za_1">14.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_za_1" name="mes_za_1" value="<?php echo $mes_za_1; ?>" >
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_zona_apartada_2">15) Zona Apartada (012) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_zona_apartada_2" name="sobresueldo_zona_apartada_2" value="<?php echo $sobresueldo_zona_apartada_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_za_2">15.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_za_2" name="mes_za_2" value="<?php echo $mes_za_2; ?>" >
										</div>
									</div>

										<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_12">16) Sueldo Propuesto (012):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto_12" name="sueldo_propuesto_12" value="<?php echo $sueldo_propuesto_12; ?>">
										</div>
									 </div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_jefatura_1">17) Jefatura (013) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_jefatura_1" name="sobresueldo_jefatura_1" value="<?php echo $sobresueldo_jefatura_1; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_jef_1">17.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_jef_1" name="mes_jef_1" value="<?php echo $mes_jef_1; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_jefatura_2">18) Jefatura (013) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_jefatura_2" name="sobresueldo_jefatura_2" value="<?php echo $sobresueldo_jefatura_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_jef_1">18.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_jef_2" name="mes_jef_2" value="<?php echo $mes_jef_2; ?>" >
										</div>
									</div>

										<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_13">19) Sueldo Propuesto (013):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto_13" name="sueldo_propuesto_13" value="<?php echo $sueldo_propuesto_13; ?>">
										</div>
									 </div>

									<div class="form-group">
										<label class="col-md-3 control-label" for="	sobresueldo_esp_1">20)Especialidad o Exc (019) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_esp_1" name="sobresueldo_esp_1" value="<?php echo $sobresueldo_esp_1; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ot_1">20.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_esp_1" name="mes_esp_1" value="<?php echo $mes_esp_1; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="	sobresueldo_esp_2">21) Especialidad o Exc (019) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_esp_2" name="sobresueldo_esp_2" value="<?php echo $sobresueldo_esp_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ot_2">21.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_esp_2" name="mes_esp_2" value="<?php echo $mes_esp_2; ?>" >
										</div>
									</div>
										<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_19">22) Sueldo Propuesto (019):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto_19" name="sueldo_propuesto_19" value="<?php echo $sueldo_propuesto_19; ?>">
										</div>
									 </div>


									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_otros_1">23) Otros (080) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_otros_1" name="sobresueldo_otros_1" value="<?php echo $sobresueldo_otros_1; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ot_1">23.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_ot_1" name="mes_ot_1" value="<?php echo $mes_ot_1; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sobresueldo_otros_2">24) Otros (080) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sobresueldo_otros_2" name="sobresueldo_otros_2" value="<?php echo $sobresueldo_otros_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_ot_2">24.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_ot_2" name="mes_ot_2" value="<?php echo $mes_ot_2; ?>" >
										</div>
									</div>
										<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_80">25) Sueldo Propuesto (080):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="sueldo_propuesto_80" name="sueldo_propuesto_80" value="<?php echo $sueldo_propuesto_80; ?>">
										</div>
									 </div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="gastos_repr">26) Gastos de representaci&oacute;n(030) 1:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="gastos_repr" name="gastos_repr" value="<?php echo $gastos_repr; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_gasto_1">26.1) Meses:</label>
										<div class="col-md-1">
											<input type="number" class="form-control input-sm" 
										       id="mes_gasto_1" name="mes_gasto_1" value="<?php echo $mes_gasto_1; ?>" >
										</div>
									</div>	
									<div class="form-group">
										<label class="col-md-3 control-label" for="gasto_representacion_2">27) Gastos de representaci&oacute;n(030) 2:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" 
										       id="gasto_representacion_2" name="gasto_representacion_2" value="<?php echo $gasto_representacion_2; ?>" >
										</div>
										<label class="col-md-2 control-label" for="mes_gasto_2">27.1) Meses:</label>
											<div class="col-md-1">
											<input type="number" class="form-control input-sm" id="mes_gasto_2" name="mes_gasto_2" value="<?php echo $mes_gasto_2; ?>" >
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="sueldo_propuesto_30">28) Sueldo Propuesto (30):</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" id="sueldo_propuesto_30" name="sueldo_propuesto_30" value="<?php echo $sueldo_propuesto_30; ?>">
										</div>
									</div>	
								</div>
								</div>
								<div class="panel panel-default">
  								<div class="panel-heading">Otros Campos:</div>
  								<div class="panel-body">
									<div class="form-group">
										<label class="col-md-3 control-label" for="gastos_repr">29) Paga gastos representaci&oacute;n:</label>
										<div class="col-md-3">											
											<select name="paga_gr" id="paga_gr" class="select2 form-control">
												<option value="">Seleccione paga GR</option>
												<option value="0" <?php echo ($paga_gr=='0') ? 'selected' : ''; ?> >No</option>
												<option value="1" <?php echo ($paga_gr=='1') ? 'selected' : ''; ?> >Si</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">30) Grupo:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" id="grupo" name="grupo" value="<?php echo $grupo; ?>">
										</div>
									</div>

									<div class="form-group">
										<label class="col-md-3 control-label">31) Total de Cargos:</label>
										<div class="col-md-3">
											<input type="number" class="form-control input-sm" id="total" name="total" value="<?php echo $max_car; ?>" >
										</div>
										<label class="col-md-3 control-label">Disponibles</label>
									</div>
									<?php
									if(isset($_GET['edit']))
									{
									?>
									<div class="form-group">
									<label class="col-md-3 control-label">32) ¿Asignar sueldos en posiciones?:</label>
										<div class="col-md-3">											
												<select name="modif" id="modif" class="select2 form-control">
													<option value="0">No</option>
													<option value="1">Si</option>
												</select>
										</div>
									</div>
									<div class="form-group">
									<label class="col-md-3 control-label">33) ¿Asignar sueldos a todo el personal asociado a este cargo?:</label>
										<div class="col-md-3">											
												<select name="modifpers" id="modifpers" class="select2 form-control">
													<option value="0">No</option>
													<option value="1">Si</option>
												</select>
										</div>
									</div>
									<?php 
									}
									?>	
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='cargos.php'">Cancelar</button>
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
	    	<a class="btn btn-primary" href="">
			<i class="fa fa-print"></i>
			PDF
			</a>&nbsp;
			<a class="btn btn-primary" href="">
			<i class="fa fa-print"></i>
			WORD
			</a>&nbsp;
			<a class="btn btn-primary" href="">
			<i class="fa fa-print"></i>
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
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {

		$('#formPrincipal').validate({
	            rules: {
	                txtcodigo: {
	                    required: true
	                },
	               // sueldo_propuesto: { required: true },
	               // sueldo_anual: { required: true },
	               // mes_1:{ range:[1,12] },
	               // mes_2:{ range:[1,12] },
	              //  mes_3:{ range:[1,12] },
	              //  mes_4:{ range:[1,12] },
	                mes_t:{ max:12 }

	            },

	            messages: {
	                txtcodigo: { required: " Campo requerido" },
	               // sueldo_propuesto: { required: "Campo requerido "},
	              //  sueldo_anual: { required: " Campo requerido" },
	               // mes_1:{range: "Numero entre 1 y 12", required:false},
	               // mes_2:{range: "Numero entre 1 y 12", required:false},
	               // mes_3:{range: "Numero entre 1 y 12", required:false},
	               // mes_4:{range: "Numero entre 1 y 12", required:false},
	               // mes_t:{max: "Numero menor a 12", required:false}

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

		$("#partida").select2({
		 	placeholder: 'Seleccione una partida',
            allowClear: true,
        });
/*
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
