<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();
$termino=$_SESSION['termino'];

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();


$prestaciones = $_GET['prestaciones'];
$posicion     = $_GET['posicion'];
$buscar     = $_GET['buscar'];
if(!$buscar){
	$posicion="";
}


$ver_planilla=$_SESSION['ver_planilla3_jd'];

$dir          = "nomina_de_pago.php";
if($prestaciones == 1)
{
	$dir = "nomina_de_prestaciones.php";
}
if($_GET['vac']==1){
	$dir="nomina_de_vacaciones.php";

}
//abrimos la conexion
$conexion=conexion();
$selectra=$conexion;
//constantes
$url="movimientos_nomina_pago.php";


$consulta_fecha="SELECT * from nom_nominas_pago where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resul_fecha=query($consulta_fecha,$conexion);
$lista=fetch_array($resul_fecha);
$fila_nomina=$lista;


if ($_GET['tipob']!="") {
	$tipob=$_GET['tipob'];
	$des = $_GET['ficha'];
	//$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo from nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) where pe.estado <> 'Egresado' and pe.tipnom='".$_SESSION['codigo_nomina']."' ";
}
//echo $_GET['buscar'];
if($_GET['buscar']!="")
{
	
		
		$des=$_GET['buscar'];
//		$_GET['codigo_nomina']=$_POST['codigo_nomina'];
		$_GET['codt']=$_POST['codt'];
		$cod_nomina=$_GET['codigo_nomina'];


		if($lista["status"]=="A"){
                $consulta="SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado  ,pe.codnivel3 as codnivel3, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id "
                        . "FROM nompersonal as pe "
                        . "LEFT JOIN nomcargos as ca on(pe.codcargo=ca.cod_car) "
                        . "WHERE pe.tipnom='".$_SESSION['codigo_nomina']."' AND pe.ficha=$des AND ('".$lista['periodo_fin']."'<=pe.fecharetiro OR pe.fecharetiro='0000-00-00' OR pe.fecharetiro IS NULL) $consulta_add "; //buscar_exacta($tabla,$des,$busqueda);
        }
        else{
        	$consulta="SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado  ,pe.codnivel3 as codnivel3, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id "
                        . "FROM nompersonal as pe "
                        . "LEFT JOIN nomcargos as ca on(pe.codcargo=ca.cod_car) "
                        . "WHERE pe.tipnom='".$_SESSION['codigo_nomina']."' AND pe.ficha=$des AND pe.ficha IN (SELECT DISTINCT ficha FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."') "; //buscar_exacta($tabla,$des,$busqueda);
        }

}
else
{
	if($_GET['vac']==1  )
	{
		$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id from nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) inner join nom_progvacaciones as vac on pe.ficha=vac.ficha where (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%') and vac.fechavac<>'0000-00-00' and vac.fechareivac<>'0000-00-00' and vac.estado='Pendiente' and pe.tipnom='".$_SESSION['codigo_nomina']."' and ((pe.fecharetiro>='".$lista['periodo_ini']."' and pe.fecharetiro<='".$lista['periodo_fin']."') or pe.fecharetiro='0000-00-00' OR pe.fecharetiro IS NULL) and pe.estado='Activo' group by pe.cedula";

		if($lista["status"]=="A"){
			$consulta ="SELECT pe.foto AS foto, pe.ficha AS ficha, pe.apenom AS apenom, pe.cedula AS cedula, pe.suesal AS suesal, 
                            pe.codnivel1 AS codnivel1, pe.codnivel2 AS codnivel2, pe.estado AS estado, pe.codnivel3 AS codnivel3, pe.codnivel4 AS codnivel4, pe.codnivel5 AS codnivel5, pe.codnivel6 AS codnivel6, pe.codnivel7 AS codnivel7, 
		                    pe.fecing AS fecing, ca.des_car AS cargo, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad 
					FROM nompersonal AS pe 
					LEFT JOIN nomcargos AS ca ON pe.codcargo=ca.cod_car
					LEFT JOIN nom_progvacaciones AS vac ON pe.ficha=vac.ficha 
					WHERE (pe.estado  NOT LIKE '%De Baja%') AND pe.tipnom='{$_SESSION['codigo_nomina']}'
					AND ((pe.fecharetiro>='{$lista['periodo_ini']}' AND pe.fecharetiro<='{$lista['periodo_fin']}') OR pe.fecharetiro='0000-00-00' OR pe.fecharetiro IS NULL)
					AND ((vac.fechavac<>'0000-00-00' AND vac.fechareivac<>'0000-00-00' AND vac.estado='Pendiente') 
					      OR pe.ficha IN (SELECT ficha FROM nom_movimientos_nomina WHERE codnom='{$_GET['codigo_nomina']}' AND tipnom='{$_SESSION['codigo_nomina']}' AND codcon=114)			 
					"; // AND pe.estado='Activo' GROUP BY pe.cedula
		}
		else{
			$consulta ="SELECT pe.foto AS foto, pe.ficha AS ficha, pe.apenom AS apenom, pe.cedula AS cedula, pe.suesal AS suesal, 
                            pe.codnivel1 AS codnivel1, pe.codnivel2 AS codnivel2, pe.estado AS estado, pe.codnivel3 AS codnivel3, pe.codnivel4 AS codnivel4, pe.codnivel5 AS codnivel5, pe.codnivel6 AS codnivel6, pe.codnivel7 AS codnivel7, 
		                    pe.fecing AS fecing, ca.des_car AS cargo, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad 
					FROM nompersonal AS pe 
					LEFT JOIN nomcargos AS ca ON pe.codcargo=ca.cod_car
					LEFT JOIN nom_progvacaciones AS vac ON pe.ficha=vac.ficha 
					WHERE pe.tipnom='{$_SESSION['codigo_nomina']}' AND pe.ficha IN (SELECT DISTINCT ficha FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."')
					"; // AND pe.estado='Activo' GROUP BY pe.cedula
		}

	}else{
		//obtenemos el personal de ese tipo de nomina
		//if($_SESSION['codigo_nomina']!=2){

			$consulta_add="";
			if($lista["status"]=="A"){
				$consulta_add.=" 
					AND (pe.estado  NOT LIKE '%De Baja%' AND pe.estado  NOT LIKE '%Egresado%')  
					AND ((pe.fecharetiro>='".$lista['periodo_ini']."' and pe.fecharetiro<='".$lista['periodo_fin']."') or pe.fecharetiro='0000-00-00' OR pe.fecharetiro IS NULL) ";
			}
			else{
				$consulta_add.=" AND pe.ficha IN (SELECT DISTINCT ficha FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."') ";
			}	

			if ($posicion=='') 
			{
				$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad
				from nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) 
				where pe.tipnom='".$_SESSION['codigo_nomina']."' $consulta_add";
			}
			elseif ($posicion == 'posicion') {
				$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad
				FROM nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) 
				WHERE nomposicion_id='".$buscar."' AND pe.tipnom='".$_SESSION['codigo_nomina']."' $consulta_add";
			}
			elseif ($posicion == 'ficha') {
				$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad
				from nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) 
				where pe.ficha='".$buscar."' AND  pe.tipnom='".$_SESSION['codigo_nomina']."' $consulta_add";
			}
			
		/*}else{
			$consulta= "SELECT pe.foto as foto, pe.ficha as ficha, pe.apenom as apenom, pe.cedula as cedula, pe.suesal as suesal, pe.codnivel1 as codnivel1, pe.codnivel2 as codnivel2, pe.estado as estado, pe.codnivel3 as codnivel3, pe.codnivel4 as codnivel4, pe.codnivel5 as codnivel5, pe.codnivel6 as codnivel6, pe.codnivel7 as codnivel7, pe.fecing as fecing, ca.des_car as cargo, pe.nomposicion_id, pe.gastos_representacion, pe.antiguedad, pe.zona_apartada, pe.jefaturas, pe.especialidad
			from nompersonal as pe left join nomcargos as ca on(pe.codcargo=ca.cod_car) 
			where (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%') and pe.tipnom='".$_SESSION['codigo_nomina']."' ";
		}*/
	}
}
$consulta=$consulta." ORDER BY pe.ficha ASC";
//echo $consulta;
$pagina=@$_GET['pagina'];
$num_paginas=obtener_num_paginas($consulta,1);
$pagina=obtener_pagina_actual($pagina, $num_paginas);

$resultado_personal=query($consulta." limit ".($pagina-1).", ".$pagina."",$conexion);

//array de la ficha
$fila_personal=fetch_array($resultado_personal);

//ajustar codificacion de todo (los datos del array $fila_personal)
foreach($fila_personal as $key => $value) {
	if(!mb_detect_encoding($fila_personal["$key"],["UTF-8"],true)){
		$fila_personal["$key"]=utf8_encode($fila_personal["$key"]);
	}
}
//verificar si la foto existe, si no existe dejar el campo vacio para que coloque la imagen default
if(!file_exists($fila_personal['foto'])){
	$fila_personal['foto']='';
}
else{//si existe la imagen
	//si existe la miniatura
	if(file_exists($fila_personal['foto'].".min")){
		$fila_personal['foto']=$fila_personal['foto'].".min";
	}
	else{//si no existe la miniatura generarla
	    $path    = str_replace("\\","/",dirname(__FILE__)); 
	    $fuente  = $path."/".$fila_personal['foto'];
	    $destino = $path."/".$fila_personal['foto'].".min";
	    include_once("../../includes/phpthumb/phpthumb.class.php");

	    $phpThumb = new phpThumb();
	    $imagen_data = explode(".", $fuente);
	    $extension = strtolower(end($imagen_data));
	    $calidad_foto = 85;
	    
	    $phpThumb->resetObject();
	    $phpThumb->setSourceFilename($fuente);
	    $phpThumb->setParameter('h',200);
	    $phpThumb->setParameter('w',200);
	    $phpThumb->setParameter('ar','x');
	    $phpThumb->setParameter('config_allow_src_above_docroot', true);
	    $phpThumb->setParameter('config_cache_directory', $path."/../../includes/phpthumb/cache/");
	    $phpThumb->setParameter('config_output_format', $extension);
	    $phpThumb->setParameter('q', $calidad_foto);
	    if ($phpThumb->GenerateThumbnail()){
	        $phpThumb->RenderToFile($destino);
	        if(file_exists($destino)){
	        	chmod( $destino, 0777);
	        }  
	        $fila_personal['foto']=$fila_personal['foto'].".min";      	
	    }
	}
}


//variables
if(isset($_GET['ficha']) AND $_GET['ficha']!=''){
$ficha=$_GET['ficha'];
}else{
$ficha=$fila_personal['ficha'];
}
//echo "<br> Codigo Nomina: ".$_GET['codigo_nomina']." Tipo nomina: ".$_SESSION['codigo_nomina'];
//echo $tipo_nomina=$_SESSION['codigo_nomina'];
//echo "<br> select * from nom_nominas_pago where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
$cod_nomina=$_GET['codigo_nomina'];
//echo $cod_nomina;
$codt=$_GET['codt'];
//echo $ficha;

/*
no es necesario ya hay una consulta al inicio que obtiene los datos
$selectra= new bd($_SESSION['bd']);
//echo "select * from nom_nominas_pago where codnom='".$cod_nomina."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado=$selectra->query("select * from nom_nominas_pago where codnom='".$cod_nomina."' and tipnom='".$_SESSION['codigo_nomina']."'");
$fila_nomina=$resultado->fetch_assoc();
*/
?>


<html lang="es">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8">
<!-- BEGIN GLOBAL M&&ATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL M&&ATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">

<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
  	<input type="hidden" name="registro_id" value="">
	<input type="hidden" name="opt"  value="">
	<input type="hidden" name="prestaciones"  value="<? echo $prestaciones?>">
	<input type="hidden" name="codigo_nomina" id="codigo_nomina" value=" <?echo $cod_nomina;?> ">
	<input type="hidden" name="pagina" value="<?echo $pagina;?>">
	<input type="hidden" name="codt" id="codt" value="<?echo $codt;?>">
	<input type="hidden" name="ver_planilla" id="ver_planilla" value="<?= $ver_planilla;?>">

<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Datos del Funcionario</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-4">Sueldo</div>
								<div class="col-md-8"><input name="sueldo" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[suesal];?>" id="sueldo" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Gastos de Representación:</div>
								<div class="col-md-8"><input name="gastos_respre" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[gastos_representacion];?>" id="gastos_respre" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Antiguedad</div>
								<div class="col-md-8"><input name="antiguedad" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[antiguedad];?>" id="antiguedad" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Zonas Apartadas</div>
								<div class="col-md-8"><input name="zona_apartada" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[zona_apartada];?>" id="zona_apartada" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Jefaturas</div>
								<div class="col-md-8"><input name="jefaturas" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[jefaturas];?>" id="jefaturas" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Especialidad:</div>
								<div class="col-md-8">
								<input name="especialidad" class="form-control input-sm" type="text" readonly="true" value="<?php echo $fila_personal[especialidad];?>" id="especialidad" >
							</div>
							</div>
							<div class="row">
								<div class="col-md-4">Cargo</div>
								<div class="col-md-8"><input value="<?php echo "$fila_personal[cargo]";?>" name="fec_ing" type="text" class="form-control input-sm" readonly="true" id="fec_ing" size="35" ></div>
							</div>
							<div class="row">
								<div class="col-md-4">Situación</div>
								<div class="col-md-8"><input value="<?php echo "$fila_personal[estado]";?>" name="cargo" type="text" readonly="true" class="form-control input-sm" id="cargo" ></div>
							</div>
							<div class="row"><?php
								$resultado=$selectra->query("select * from nomnivel1 where codorg='".$fila_personal['codnivel1']."'");
								$resultado_nivel1=$resultado->fetch_assoc();
								?>
								<div class="col-md-4">Nivel Funcional:</div>
								<div class="col-md-8">
								<input class="form-control input-sm"  name="niv_funcional" type="text" readonly="true" id="niv_funcional" value="<?php if($resultado_nivel1['descrip']!='')echo $resultado_nivel1['descrip'];?>">
								</div>
							</div>
							<div class="row"><?php
							$resultado=$selectra->query("select * from nomnivel2 where codorg='".$fila_personal['codnivel2']."'");
							$resultado_nivel2=$resultado->fetch_assoc();

							?>

								<div class="col-md-4"></div>
								<div class="col-md-8"><input class="form-control input-sm" name="niv_funcional2" type="text" readonly="true" id="niv_funcional" value="<?php if($resultado_nivel2['descrip']!='')
							echo $resultado_nivel2['descrip'];

							?>"></div>
							</div>
							
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo "Movimientos de ".$termino."
							 Nº: ".$cod_nomina.", Fecha de Inicio: ".fecha($fila_nomina[periodo_ini]).", Fecha Fin: ".fecha($fila_nomina[periodo_fin]) ?>
							</div>	
							<div class="actions">
								<?php //boton_metronic('print','VerRecibo();',2,'Imprimir Recibo'); ?>
								<?php if($_SESSION["planilla_pago_detalle_agregar"] && $fila_nomina[status]=="A")  boton_metronic('add','enviar(1,0)',2); ?>
								<?php //boton_metronic('edit','enviar(7,0)',2) ?>
								<?php if($_SESSION["planilla_pago_detalle_generar"] && $fila_nomina[status]=="A")  boton_metronic('generar','enviar(6,0)',2); ?>
								<?php if($_SESSION["planilla_pago_detalle_generar"] && $fila_nomina[status]=="A")  boton_metronic('recalcular','enviar(8,0)',2); ?>
								<?php if($_SESSION["planilla_pago_detalle_eliminar"] && $fila_nomina[status]=="A") boton_metronic('del','enviar(5)',2); ?>

								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_de_pago.php'">
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>

						<div class="portlet-body">
						<div class="row">
							<div class="col-md-4">
								<div class="input-group">
									
									<input type="text" class="form-control input-sm" id="buscar" placeholder="Buscar">							<span class="input-group-addon">
										<i class="fa fa-search"></i>
									</span>	
								</div>
							</div>
							<div class="col-md-2"><select type="text" class="form-control input-sm" name="posicion" id="posicion"><option value="ficha">Por colaborador</option><option value="posicion">Por posición</option></select></div>
							<div class="col-md-1"><?php boton_metronic('search',"BuscarFichaPosicion(document.frmPrincipal.txtnomina.value, ".$_SESSION['codigo_nomina'].",document.frmPrincipal.textfield.value, document.frmPrincipal.buscar.value, document.frmPrincipal.posicion.value);",2,'Buscar') ?>	</div>
							<div class="col-md-1">&nbsp;</div>
							<div class="col-md-1">Asignaciones</div>
							<div class="col-md-1"><p class="text-primary"><input  name="txtasignaciones" type="text" class="input-sm" readonly="true" value=''>
                                                                                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(-)<input  name="txtasignaciones_resta" type="text" class="input-sm" readonly="true" value=''>                                                                                                  
                                                                                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(=)<input  name="txtasignaciones_neto" type="text" class="input-sm" readonly="true" value=''>
                                                                            </p>
                                                        </div>
						</div>
						
						<div class="row">
							<div class="col-md-1"><?php echo $termino?>:</div>

							<div class="col-md-3">
								   <input name="txtnomina" class="form-control input-sm"  type="text" id="txtnomina" value="<?php echo $cod_nomina;?>">
						        
							</div>
							<div class="col-md-4"></div>
							<div class="col-md-1">Deducciones</div>

							<div class="col-md-1"><p class="text-danger"><input name="txtdeducciones" type="text" id="txtdeducciones" class="input-sm"  readonly="true"></p></div>
							
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-3">Colaborador:</div>
									<div class="col-md-9">
										 <input name="textfield" type="text" class="form-control input-sm" id="textfield" value="<?php echo $fila_personal['ficha']?>">
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">Posicion:</div>
									<div class="col-md-9">
										 <input name="posicion_id" type="text" class="form-control input-sm" id="posicion_id" value="<?php echo $fila_personal['nomposicion_id']?>">
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">Nombre:</div>
									<div class="col-md-9">
										<input name="txtnombre" type="text" value="<?php echo "$fila_personal[apenom]";?>" id="txtnombre" readonly="true" class="form-control input-sm" >
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">Cedula:</div>
									<div class="col-md-9">
										<input class="form-control input-sm" name="txtcedula" value="<?php echo "$fila_personal[cedula]";?>" type="text" readonly="true" id="txtcedula">
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">Fecha de ingreso: </div>
									<div class="col-md-9"><input value="<?php echo fecha($fila_personal[fecing]);?>" name="fec_ing" type="text"class="form-control input-sm" readonly="true" id="fec_ing" ></div>
								</div>
								<div class="row">
									<div class="col-md-12">&nbsp;</div>
								</div>
								<div class="row">
									<div class="col-md-12"><a type="button" class="btn btn-primary" href="#" role="button" data-toggle="modal" data-target="#myModal"> Más datos </a>									</div>
								</div>
									
									
																	
							</div>
							<div class="col-sm-4 col-md-2 col-md-offset-1">
								<div class="thumbnail"><img src="<?if($fila_personal['foto']==""){?>fotos/silueta.gif<?}else{echo $fila_personal['foto'];}?>" id="imgFoto" name="imgFoto" class="img-thumbnail"  style="height: 180px; width: 180px; display: block;">
								</div>
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-3">&nbsp;</div>
									<div class="col-md-3">Neto</div>
									<div class="col-md-1"><p class="text-primary"><input name="txtneto" type="text" id="txtneto" class="input-sm" value="" readonly="true"></p></div>
								</div>
								<div class="row" id="ver_netoplanilla3">
									<div class="col-md-3">&nbsp;</div>
									<div class="col-md-3">Neto Planilla 3</div>
									<div class="col-md-1"><p class="text-primary"><input name="neto_planilla3" type="text" id="neto_planilla3" class="input-sm" value="" readonly="true"></p></div>
								</div>
								
							</div>	
						</div>
						
						<div class="row">
						<div class="col-md-12">
						<div class="tabbable tabbable-custom">
							<ul class="nav nav-tabs">
								&nbsp;&nbsp;<li class="active"><a href="#tab_0" role="tab" data-toggle="tab">Conceptos Imprimibles</a></li>
								<li><a href="#tab_1" role="tab" data-toggle="tab">Conceptos No Imprimibles</a></li>
								
							</ul>
							<div class="tab-content">
								
								<div class="tab-pane active" id="tab_0"><div class="table-responsive">
									<table class="table table-condensed">
									<tr>
										<td><h4><small>Concepto</small></h4></td>
										<td><h4><small>Descripci&oacute;n</small></h4></td>
										<td><h4><small>Referencia</small></h4></td>
										<td><h4><small>Unidad</small></h4></td>
										<td><h4><small>Asignaciones</small></h4></td>
										<td><h4><small>Deducciones</small></h4></td>
										<td><h4><small>Patronales</small></h4></td>
										<td class="text-center"><h4><small>Acciones</small></h4></td>
									</tr>
								     <?php 
								   	$consulta="SELECT nm.*,tp.nom_tipos_prestamos "
                                                                                . "FROM nom_movimientos_nomina as nm "
                                                                                . "LEFT JOIN tipos_prestamos as tp ON (nm.tipopr = tp.id_tipos_prestamos) "
                                                                                . "WHERE tipnom='".$_SESSION['codigo_nomina']."' AND codnom='".$cod_nomina."' AND ficha='".$ficha."' "
                                                                                . "ORDER BY nm.codcon ASC";
//										echo $consulta ;
										// echo $_SESSION['codigo_nomina'];
										$resultado_movimientos=query($consulta,$conexion);

								 
										if (num_rows($resultado_movimientos)>0)
										{
									  	while ($fila = mysqli_fetch_array($resultado_movimientos))
									  	{ 
										$consulta="select * from nomconceptos where codcon='".$fila['codcon']."'";
										$res=query($consulta,$conexion);
										$fila_con=fetch_array($res);
												
										if ($fila_con[impdet]=='S')
										{
											if ($fila[tipcon]=='D')
											{
                                                                                            if ($fila[codcon]!=190 && $fila[codcon]!=198 && $fila[codcon]!=199)
                                                                                            {
                                                                                                $monto_ded=$monto_ded+$fila[monto];
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                $monto_asig_resta=$monto_asig_resta+$fila[monto];
                                                                                            }  
                                                                                        }
											else if ($fila[tipcon]=='A')
											{
                                                                                            $monto_asig=$monto_asig+$fila[monto];
                                                                                        
                                                                                        }
										  	?>
										  	<tr>
												<td><h5><small><?php echo $fila[codcon];?></small></h5></td>
												<td><h5><?php echo $fila[descrip]." - ".$fila[nom_tipos_prestamos];?></h5></td>
												<td><h5><?php if($fila[valor]!=0 and $fila_con['verref']==1){echo $fila[valor];}?></h5></td>
												<td><h5><?php echo $fila[unidad];?></h5></td>
												<td><h5><?php if ($fila[tipcon]=='A'){echo number_format($fila['monto'],2,'.','');}?></h5></td>
												<td><h5><?php if ($fila[tipcon]=='D'){echo number_format($fila['monto'],2,'.','');}?></h5></td>
												<td><h5><?php if ($fila[td]=='P'){echo number_format($fila[monto],2,',','.');}?></h5></td>
												<td class="text-center"><h5>
													<?php if($fila_nomina['status']=="A" and $_SESSION["planilla_pago_detalle_editar"]){?> 
													<a href="javascript:enviar(<?php echo(2); ?>,<?php echo($fila['id']); ?>);">
													<img src="../imagenes/edit.gif" alt="Edita el Registro Actual" width="16" height="16" border="0" align="absmiddle" >
													</a>
													<?php }?>
													<?php if($fila_nomina['status']=="A" and $_SESSION["planilla_pago_detalle_eliminar"]){?> 
													<a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['id']); ?>);">
													<img src="../imagenes/delete.gif" alt="Elimina el Registro Actual" width="16" height="16" border="0" align="absmiddle" >
													</a>
													<?php }?></h5></td>
											</tr>
											    <?php
											}
										}
                                                                                $monto_asig_neto=$monto_asig-$monto_asig_resta;
										$monto_neto=$monto_asig_neto-$monto_ded-$monto_asig_resta;
										$num_fila++;
									  	$in++;  
									}
									else
									{?>
											<td>No existen Conceptos Imprimibles para esta ficha</td>
										<?php } ?>
									    <input name="registro_id" type="hidden" value="">
										<input name="nombre_tabla" type="hidden" value="<?php echo $nombre_tabla; ?>">
									    <input name="op" type="hidden" value="">	

								</table>
								</div>
								</div>
									<!-- Segunda Pestaña-->
								<div class="tab-pane" id="tab_1"><div class="table-responsive">
									<table class="table table-striped table-condensed">
									<tr>
										<td><h4><small>Concepto</small></h4></td>
										<td><h4><small>Descripci&oacute;n</small></h4></td>
										<td><h4><small>Referencia</small></h4></td>
										<td><h4><small>Unidad</small></h4></td>
										<td><h4><small>Asignaciones</small></h4></td>
										<td><h4><small>Deducciones</small></h4></td>
										<td><h4><small>Patronales</small></h4></td>
										<td class="text-center"><h4><small>Acciones</small></h4></td>
									</tr>
								           <?php 
											$consulta="SELECT * FROM nom_movimientos_nomina "
                                                                                                . "WHERE tipnom='".$_SESSION['codigo_nomina']."' AND codnom='".$cod_nomina."' AND ficha='".$ficha."' "
                                                                                                . "ORDER BY codcon ASC";

											$resultado_movimientos=query($consulta,$conexion);

											if (num_rows($resultado_movimientos)>0)
											{

											?>

											<?php	

											while ($fila = fetch_array($resultado_movimientos))
											{ 
											$consulta="select * from nomconceptos where codcon='".$fila['codcon']."'";
											$res=query($consulta,$conexion);
											$fila_con=fetch_array($res);

											if ($fila_con[impdet]=='N'){
											// 	if ($fila[tipcon]=='D')
											// 		{$monto_ded=$monto_ded+$fila[monto];}
											// 	else if ($fila[tipcon]=='A')
											// 		{$monto_asig=$monto_asig+$fila[monto];}
											// 	
											?>
										  	<tr>
												<td><h5><?php echo $fila[codcon];?></h5></td>
												<td><h5><?php echo $fila[descrip];?></h5></td>
												<td><h5><?php if($fila[valor]!=0 and $fila_con['verref']==1){echo $fila[valor];}?></h5></td>
												<td><h5><?php echo $fila[unidad];?></h5></td>
												<td><h5><?php if ($fila[tipcon]=='A'){echo number_format($fila['monto'],2,'.','');}?></h5></td>
												<td><h5><?php if ($fila[tipcon]=='D'){echo number_format($fila['monto'],2,'.','');}?></h5></td>
												<td><h5><?php if ($fila[tipcon]=='P'){echo number_format($fila['monto'],2,'.','');}?></h5>
												</td>
												<td class="text-center"><h5>
													<?php if($fila_nomina['status']=="A" and $_SESSION["planilla_pago_detalle_editar"]){?> 
													<a href="javascript:enviar(<?php echo(2); ?>,<?php echo($fila['id']); ?>);">
													<img src="../imagenes/edit.gif" alt="Edita el Registro Actual" width="16" height="16" border="0" align="absmiddle" >
													</a>
													<?php }?>
													<?php if($fila_nomina['status']=="A" and $_SESSION["planilla_pago_detalle_eliminar"]){?> 
													<a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['id']); ?>);">
													<img src="../imagenes/delete.gif" alt="Elimina el Registro Actual" width="16" height="16" border="0" align="absmiddle" >
													</a>
													<?php }?></h5>
												</td>
											</tr>
											    <?php
											}
										}
										$monto_neto=$monto_asig-$monto_ded-$monto_asig_resta;
										$num_fila++;
									  	$in++;  
									}
									else
									{?>
											<td>No existen Conceptos Imprimibles para esta ficha</td>
										<?php } ?>
									    <input name="registro_id" type="hidden" value="">
										<input name="nombre_tabla" type="hidden" value="<?php echo $nombre_tabla; ?>">
									    <input name="op" type="hidden" value="">	

								</table>

								</div></div>
							</div></div>&nbsp;</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php pie_pagina_bootstrap($url,$pagina,"codigo_nomina=".$cod_nomina."&codt=".$codt."&prestaciones=".$prestaciones."&vac=".$_GET['vac'],$num_paginas); ?>
							</div>
							
						</div>
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
	<script>
	document.frmPrincipal.txtasignaciones.value='<?php  echo number_format($monto_asig,2,'.','');?>';
        document.frmPrincipal.txtasignaciones_resta.value='<?php  echo number_format($monto_asig_resta,2,'.','');?>';
        document.frmPrincipal.txtasignaciones_neto.value='<?php  echo number_format($monto_asig_neto,2,'.','');?>';
	document.frmPrincipal.txtdeducciones.value='<?php echo number_format($monto_ded,2,'.',''); ?>';
	document.frmPrincipal.txtneto.value='<?php echo number_format($monto_neto,2,'.',''); ?>';
	</script>


<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script language="JavaScript" type="text/javascript">

function VerRecibo()
{
	
AbrirVentana('rpt_recibo_pago.php?registro_id='+document.frmPrincipal.textfield.value+'&codt='+document.frmPrincipal.codt.value+'&codigo_nomina='+document.frmPrincipal.codigo_nomina.value,660,800,0);

}

function VerFoto()
{
AbrirVentana('mostrar_foto_empleado.php',360,390,0);
}

function BuscarPersonal()
{
AbrirVentana('buscar_empleado33.php?codigo_nomina='+document.frmPrincipal.txtnomina.value,660,700,0);
}

function BuscarNomina()
{
AbrirVentana('buscar_nomina_pago.php',660,700,0);
}
function BuscarFichaPosicion(codnom,tipnom,ficha,buscar,posicion)
{
//	alert('codnom: '+codnom+' tipnom: '+tipnom+' ficha: '+ficha+' buscar: '+buscar+' posicion: '+posicion);
        document.frmPrincipal.action='movimientos_nomina_pago.php?codigo_nomina='+codnom+'&codt='+tipnom+'&buscar='+buscar+'&posicion='+posicion;
	//document.frmPrincipal.opt.value=op;
	document.frmPrincipal.submit();		
}

function Buscar(codnom,ficha,tipnom)
{
	document.frmPrincipal.action='movimientos_nomina_pago.php?codigo_nomina='+codnom+'&codt='+tipnom+'&ficha='+ficha;
	//document.frmPrincipal.opt.value=op;
	document.frmPrincipal.submit();		
}

function enviar(op,id,codnom,ficha,tipnom,pagina)
{
	
	if (op==1){		// Opcion de Agregar
		//document.frmAgregar.registro_id.value=id;
		//document.frmPrincipal.op.value=op;
		AbrirVentana('movimientos_nomina_pago_agregar1.php?ficha='+document.frmPrincipal.textfield.value+'&nomina='+document.frmPrincipal.txtnomina.value+"&pagina2="+document.frmPrincipal.pagina.value+"&nombre="+document.frmPrincipal.txtnombre.value+"&cedula="+document.frmPrincipal.txtcedula.value,660,900,0);
	}
	if (op==6){		// Opcion de Generar
// 		if(confirm("Seguro desea generar nuevamente los conceptos para esta ficha?"))
// 		{
			AbrirVentana('movimientos_nomina_persona_generar.php?todo=1&ficha='+document.frmPrincipal.textfield.value+'&nomina='+document.frmPrincipal.txtnomina.value+"&pagina2="+document.frmPrincipal.pagina.value+"&nombre="+document.frmPrincipal.txtnombre.value+"&cedula="+document.frmPrincipal.txtcedula.value,250,900,0);
// 		}
	}
	if (op==8){		// Opcion de recalcular
			AbrirVentana('movimientos_nomina_persona_recalcular.php?todo=1&ficha='+document.frmPrincipal.textfield.value+'&nomina='+document.frmPrincipal.txtnomina.value+"&pagina2="+document.frmPrincipal.pagina.value+"&nombre="+document.frmPrincipal.txtnombre.value+"&cedula="+document.frmPrincipal.txtcedula.value,250,900,0);
	}
	if (op==2){	 	// Opcion de Modificar
		//alert($op);		
		//document.location.href="movimientos_nomina_pago_editar.php?nomina="+document.frmPrincipal.txtnomina.value+"&codt="+document.frmPrincipal.codt.value+"&pagina="+document.frmPrincipal.pagina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&accion=modificar";
		//AbrirVentana("movimientos_nomina_pago_editar.php?nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&accion=modificar",660,700,0);
                AbrirVentana("movimientos_nomina_pago_editar.php?nomina="+document.frmPrincipal.txtnomina.value+"&codt="+document.frmPrincipal.codt.value+"&pagina="+document.frmPrincipal.pagina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&accion=modificar",660,700,0);
	}
	
	if (op==3){		// Opcion de Eliminar
		if (confirm("Esta seguro que desea eliminar este concepto?"))
		{					
			document.frmPrincipal.registro_id.value=id;
			document.frmPrincipal.op.value=op;
			<?php if($_GET['buscar']):?>
				document.location.href="movimientos_nomina_pago_eliminar.php?nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value;
			<?php else: ?>
				document.location.href="movimientos_nomina_pago_eliminar.php?nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&pagina="+document.frmPrincipal.pagina.value;
			<?php endif;?>
		}		
	}
	
	if (op==4){		// Opcion de copiar
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.submit();
	}	
	if (op==5){		// Opcion de Eliminar
		if (confirm("Seguro desea eliminar los conceptos para esta ficha?"))
		{					
			document.frmPrincipal.registro_id.value=id;
			document.frmPrincipal.op.value=op;
			<?php if($_GET['buscar']):?>
				document.location.href="movimientos_nomina_pago_eliminar.php?todo=1&nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value;
			<?php else: ?>
				document.location.href="movimientos_nomina_pago_eliminar.php?todo=1&nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&pagina="+document.frmPrincipal.pagina.value;
			<?php endif;?>
		}		
	}
	if (op==7)
	{
		AbrirVentana('otrosdatos_integrantes.php?txtficha='+document.frmPrincipal.textfield.value,600,700,0);
	}
		

	
}


function AbrirVentana(Ventana,Largo,Alto,Modal)
{
	if (Modal==1)
	{
	mainWindow = showModalDialog(Ventana,'mainWindow','dialogWidth:'+Alto+'px;dialogHeight:'+Largo+'px;resizable:yes;toolbar:no;menubar:no;scrollbars:yes;help: no');
	}
	else
	{

	mainWindow = window.open(Ventana,'mainWindow','menub ar=no,resizable=no,width='+Alto+',height='+Largo+',left=0,top=0,titlebar=yes,alwaysraised=yes,status=no,scrollbars=yes');
	}


}





$(document).ready(function(){
	$( "#expansor" ).hide();
$( "#expandir" ).click(function() {
  $( "#expansor" ).toggle();
  //console.log("Hola");
});
let flag = $("#ver_planilla").val();;
$("#ver_netoplanilla3").hide();
if (flag === "1")
{
	let data = {};
	data.tipnom = $("#codt").val();
	data.codnom = $("#txtnomina").val();
	data.ficha = $("#textfield").val();
	let url = "ajax/obtenerMontoPlanilla3.php";

    $.ajax({
        url: url,
        data: data,
        dataType: "JSON",
        success: function (resultado) {
        	console.log(resultado.net);
			$("#neto_planilla3").val(resultado.neto);
			$("#ver_netoplanilla3").show();
        },
        "type": "GET",
        "cache": false,
        "async": false,
        "error": function (resultado) {
            alert("Error");
        }
    });
}
});
</script>
<!-- Button trigger modal -->


</body>
</html>
