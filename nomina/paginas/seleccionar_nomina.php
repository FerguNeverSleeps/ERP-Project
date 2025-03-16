<?php
session_start();
//session_destroy();
require_once("func_bd.php") ;
$config = parse_ini_file("../lib/selectra.ini");

{

    if ($_SESSION['bd'] == "" || !isset($_GET["seleccion_nomina"])) {
        #echo 'salio';exit;
        $_SESSION['bd'] = $config['bdnombre'];
        $_SESSION['termino'] = $config['termino'];
    }
    require_once "../lib/common.php";
 
    
    $seleccion=$_GET["seleccion_nomina"];
    //echo $_GET["seleccion_nomina"];exit;
    if ($seleccion==1){
        /*foreach($_GET['opt'] as $key => $value){
                $valuetxt=$_GET['opt'];
        }*/
        ///echo $_SESSION['bd'];exit;
        $strsql= "select codtip,descrip from nomtipos_nomina where codtip = '".$_GET['opt']."' ORDER BY codtip";
        $result =sql_ejecutar($strsql);			
        $fila = mysqli_fetch_array($result);
        $_SESSION['codigo_nomina'] = $fila[0];
        $_SESSION['nomina'] = $fila[1];//$valuetxt;
        //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
        activar_pagina("frame.php");
    }
    //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
        $bValidPwd = false;
        $sUsername = $_SESSION['usuario'];
        $sPassword = $_SESSION['clave'];
        //echo $_SESSION['bd'];
        $sSql = new bd($_SESSION['bd']);
echo        $quer_ = "SELECT bd_nomina,bd_contabilidad,nombre nom_emp from nomempresa where bd_nomina = '".$_SESSION['bd_nomina']."'";exit;
        $result = $sSql->query($quer_);
        $filaemp = $result->fetch_assoc();
        $_SESSION["bd"] = $filaemp['bd_nomina'];
        $_SESSION["bdc"] = $filaemp['bd_contabilidad'];
        $_SESSION["nombre_empresa_nomina"] = $filaemp['nom_emp'];

        $sSql = new bd($_SESSION['bd']);
        //echo $_SESSION['bd'];
        //echo "select * from nomusuarios where login_usuario='$sUsername' and clave='".hash("sha256",$sPassword)."'";
        
        $quer_2 = "select * from ".SELECTRA_CONF_PYME.".nomusuarios where login_usuario='$sUsername'";
        $result = $sSql->query($quer_2);
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
            $_SESSION['acceso_sueldo'] = $fila['acceso_sueldo'];
            $_SESSION['acceso_contraloria'] = $fila['acceso_contraloria'];
            $_SESSION['acceso_s_efecto'] = $fila['acceso_s_efecto'];
            $_SESSION['acceso_editar'] = $fila['acceso_editar'];
            $_SESSION['acceso_calendarios'] = $fila['acceso_calendarios'];
            $_SESSION['acceso_imprimir'] = $fila['acceso_imprimir'];
            $_SESSION['acceso_c_familiares'] = $fila['acceso_c_familiares'];
            $_SESSION['acceso_expedientes'] = $fila['acceso_expedientes'];
            
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
<head>
		<link rel="shortcut icon" href="../../imagenes/logo.ico" />
     	<title>.:: <?php echo $_SESSION['nombre_sistema']." - ".$config['termino']; ?> ::.</title>
        <?php if(!isset($_GET['mobile'])){ ?>
        <link rel="stylesheet" type="text/css" href="./../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css">
        <script type="text/javascript" src="./../../includes/js/ext-5.0.0/build/ext-all.js"></script>
        <script type="text/javascript" src="./../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/ext-theme-neptune.js"></script>
        <script type="text/javascript" src="./../../includes/js/ext-5.0.0/build/packages/ext-locale/build/ext-locale-es.js"></script>
        <?php }elseif(isset($_GET['mobile'])){?>
        <!-- INICIO MOBILE -->
        <link href="../../includes/js/touch-2.4.1/resources/css/sencha-touch.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../../includes/js/touch-2.4.1/sencha-touch-all-debug.js"></script>
        <!-- FIN MOBILE -->
        <?php } ?>
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<div id="content">
	
</div>
			
<script>

	var TITULO = "<?php echo $config['termino']; ?>";
	var ARRAY_NOMINAS = Array();
	var i = 1;
	<?php 
		$strsql= "select n.* from nomtipos_nomina n inner join ".SELECTRA_CONF_PYME.".nomusuario_nomina nu on nu.id_nomina = n.codtip and nu.id_usuario = '".$_SESSION['cod_usuario'] ."' and nu.acceso=1";
		echo $strsql;exit;
        $result =sql_ejecutar($strsql);		
														
		$num_fila = 0;
		$in=1+(($pagina-1)*5);

		//ciclo para mostrar los datos 
		while ($fila = mysqli_fetch_array($result)){ 
		?>	
			if(i == 1){
                if("<?php echo $_GET['mobile']?>" != "1")
                    ARRAY_NOMINAS.push({ boxLabel: '<?php echo $fila[descrip]; ?>', name: 'opt', id:'nomina_<?php echo $fila[codtip]; ?>', inputValue: '<?php echo $fila[codtip]; ?>',checked:true });
                else if("<?php echo $_GET['mobile']?>" == "1")
                    ARRAY_NOMINAS.push({ label: '<?php echo $fila[descrip]; ?>', name: 'opt', id:'nomina_<?php echo $fila[codtip]; ?>', value: '<?php echo $fila[codtip]; ?>', checked:true });
                
            }				
			else{
                if("<?php echo $_GET['mobile']?>" != "1")
                    ARRAY_NOMINAS.push({ boxLabel: '<?php echo $fila[descrip]; ?>', name: 'opt', id:'nomina_<?php echo $fila[codtip]; ?>', inputValue: '<?php echo $fila[codtip]; ?>' });
                else if("<?php echo $_GET['mobile']?>" == "1")
                ARRAY_NOMINAS.push({ label: '<?php echo $fila[descrip]; ?>', name: 'opt', id:'nomina_<?php echo $fila[codtip]; ?>', value: '<?php echo $fila[codtip]; ?>' });
            }
				
			i++;
		<?php
		}//fin del ciclo while
		//operaciones de paginacion
		$num_fila++;
		$in++;  
		?>

	/*jQuery(document).ready(function() {    
	   App.init();
	   Lock.init();
	});*/
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
<?php if(!isset($_GET['mobile'])){ ?>
<script src="../../includes/js/gui/login/SeleccionNomina.js" type="text/javascript"></script>
<?php }elseif(isset($_GET['mobile'])){?>
<script src="../../includes/js/gui/login/SeleccionNominaMobile.js" type="text/javascript"></script>
<?php } ?>
</body>
</html>
