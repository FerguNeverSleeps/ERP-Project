<?php
session_start();
//session_destroy();
require_once("func_bd.php") ;
$config = parse_ini_file("../lib/selectra.ini");

{
    if ($_SESSION['bd'] == "" || !isset($_POST["seleccion_nomina"])) {
        #echo 'salio';exit;
        $_SESSION['bd'] = $config['bdnombre'];
        $_SESSION['termino'] = $config['termino'];
    }
    require_once "../lib/common.php";
 
    
    $seleccion=$_POST["seleccion_nomina"];
    if ($seleccion==1){
            foreach($_POST['opt'] as $key => $value){
                    $valuetxt=$value;
            }
            ///echo $_SESSION['bd'];exit;
            $strsql= "select codtip from nomtipos_nomina where descrip = '$valuetxt'";
            $result =sql_ejecutar($strsql);			
            $fila = mysqli_fetch_array($result);
            $_SESSION['codigo_nomina'] = $fila[0];
            $_SESSION['nomina'] = $valuetxt;
            activar_pagina("frame.php");
    }
    
        $bValidPwd = false;
        $sUsername = $_SESSION['usuario'];
        $sPassword = $_SESSION['clave'];
        //echo $_SESSION['bd'];
        $sSql = new bd($_SESSION['bd']);
        $result = $sSql->query("select bd_nomina,bd_contabilidad,nombre nom_emp from nomempresa where bd_nomina = '".$_SESSION['bd_nomina']."'");
        $filaemp = $result->fetch_assoc();
        $_SESSION["bd"] = $filaemp['bd_nomina'];
        $_SESSION["bdc"] = $filaemp['bd_contabilidad'];
        $_SESSION["nombre_empresa_nomina"] = $filaemp['nom_emp'];

        $sSql = new bd($_SESSION['bd']);
        //echo $_SESSION['bd'];
        //echo "select * from nomusuarios where login_usuario='$sUsername' and clave='".hash("sha256",$sPassword)."'";
        
        $result = $sSql->query("select * from nomusuarios where login_usuario='$sUsername'");// and clave='" . ($sPassword) . "'
        //echo "select * from nomusuarios where login_usuario='$sUsername'";
        //@TODO colocar clave en el query.
        //echo $result->num_rows;
        //exit;
        if ($result->num_rows > 0) {
            //cerrar_conexion($Conn);
            $fila = $result->fetch_assoc();
            $_SESSION['ewSessionStatus'] = "login";            
            $_SESSION['ewSessionUserName'] = $sUsername;
            $_SESSION['nombre'] = $fila["descrip"];
            $_SESSION['coduser'] = $fila["coduser"];
            $_SESSION['id_usuario'] = $fila["coduser"];
            $_SESSION['ewSessionSysAdmin'] = 0; // Non system admin

            $_SESSION['acce_configuracion'] = $fila['acce_configuracion'];
            $_SESSION['acce_usuarios'] = $fila['acce_usuarios'];
            $_SESSION['acce_elegibles'] = $fila['acce_elegibles'];
            $_SESSION['acce_personal'] = $fila['acce_personal'];
            $_SESSION['acce_prestamos'] = $fila['acce_prestamos'];
            $_SESSION['acce_consultas'] = $fila['acce_consultas'];
            $_SESSION['acce_transacciones'] = $fila['acce_transacciones'];
            $_SESSION['acce_procesos'] = $fila['acce_procesos'];
            $_SESSION['acce_reportes'] = $fila['acce_reportes'];
            $_SESSION['acce_enviar_nom'] = $fila['acce_enviar_nom'];
            $_SESSION['acce_autorizar_nom'] = $fila['acce_autorizar_nom'];
            $_SESSION['acce_estuaca'] = $fila['acce_estuaca'];
            $_SESSION['acce_xestuaca'] = $fila['acce_xestuaca'];
            $_SESSION['acce_permisos'] = $fila['acce_permisos'];
            $_SESSION['acce_logros'] = $fila['acce_logros'];
            $_SESSION['acce_penalizacion'] = $fila['acce_penalizacion'];
            $_SESSION['acce_movpe'] = $fila['acce_movpe'];
            $_SESSION['acce_evalde'] = $fila['acce_evalde'];
            $_SESSION['acce_experiencia'] = $fila['acce_experiencia'];
            $_SESSION['acce_antic'] = $fila['acce_antic'];
            $_SESSION['acce_uniforme'] = $fila['acce_uniforme'];
            $_SESSION['acce_generarordennomina'] = $fila['acce_generarordennomina'];
            
            $_SESSION["ewSessionStatus"] = "login";
            $_SESSION['termino'] = $config['termino'];
        } else {
            $_SESSION["ewSessionMessage"] = "no";
        }
}
?>
<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 2.0.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<link rel="shortcut icon" href="../imagenes/logo.ico" />
<title>.: <?php echo $_SESSION['nombre_sistema']." :: N&oacute;mina"; ?> :.</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>


<link href="../../includes/assets/css/pages/pricing-tables.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/pages/modulos.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/custom_nomina.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN HEADER -->
<div class="logo">
	<a href="index.php">
		<img width="260" height="80" src="../../includes/assets/img/logox.png" alt=""/>
	</a>
</div>

		<div class="content"> 
			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN INLINE NOTIFICATIONS PORTLET-->
					<div class="portlet">
						
						<div class="portlet-body">
							<div class="row margin-bottom-40">
								<div class="col-md-12">
											<div class="pricing hover-effect">
												<div class="pricing-head">
													<h3>Seleccione  tipo de <?php echo $config['termino']; ?><br></br>
													</h3>
												</div>
												<form action="" class="form-horizontal" method="post" name="frmseleccionar" id="frmseleccionar">
												<input name="seleccion_nomina" type="hidden" value="0">
													<div class="form-body">
														<div class="form-group">
															<div class="radio-list">
														<?php 
													  	//operaciones para paginaciones
														$strsql= "select * from nomtipos_nomina";
														$result =sql_ejecutar($strsql);		
														
													  	$num_fila = 0;
													  	$in=1+(($pagina-1)*5);

													  	//ciclo para mostrar los datos 
													  	while ($fila = mysqli_fetch_array($result)){ 
													  	?>	
													    		<div>
																	<label>
																	<input type="radio" name="opt[]" id="<?php echo $fila[codtip]; ?>" value="<?php echo $fila[descrip]; ?>"/>
																	<?php echo trim($fila[descrip]);?>
																	</label>
																</div>
													    <?php
													  	}//fin del ciclo while
													  	//operaciones de paginacion
														$num_fila++;
													  	$in++;  
													  	?>
													  		</div>
														</div>
													</div>
												</form>
												<div class="pricing-footer">
													<div class="form-actions fluid">
														<div>
															<button onClick="javascript:VerificarSeleccion();" type="submit" class="btn green">Aceptar <i class="m-icon-swapright m-icon-white"></i></button>
														</div>
													</div>
												</div>
											</div>
										</div>
							</div>
						</div>
					</div>
					<!-- END INLINE NOTIFICATIONS PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>

	
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

	<script src="../../includes/assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>

	<!-- END CORE PLUGINS -->
	<script src="../../includes/assets/scripts/core/app.js"></script>
	<script src="../../includes/assets/scripts/custom/lock-nomina.js"></script>
<script>
	jQuery(document).ready(function() {    
	   App.init();
	   Lock.init();
	});
	function VerificarSeleccion()
	{
		seleccion=0
		for(i=0; ele=document.frmseleccionar.elements[i]; i++){  		
			if (ele.name=='opt[]'){			
				if (ele.checked == true){
				seleccion=1}		
			}			
		}	
		
		if (seleccion==0){
		alert("Debe seleccionar un tipo de <?echo $termino?>. Verifique...")}
		else{
		//document.frmseleccionar.action="frame.php";	
		//document.frmseleccionar.submit();
		//alert("SS");
		document.frmseleccionar.seleccion_nomina.value=1;
		document.frmseleccionar.submit();
		}
	}
</script>
	<!-- END JAVASCRIPTS -->
	<?php 
	if (@$_SESSION[ewSessionMessage] <> ""){	
		$_SESSION[ewSessionMessage] = ""; 
	}
	?>
	</body>
	<!-- END BODY -->
</html>