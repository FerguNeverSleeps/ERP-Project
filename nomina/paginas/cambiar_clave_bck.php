<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
require_once "../header.php";
include('../../general.config.inc.php');
session_start();

$host = DB_HOST;
$usuario = DB_USUARIO; //Aqui se coloca el nombre de usuario
$contrasena = DB_CLAVE; //Aqui se coloca el password
$conectar = mysqli_connect($host, $usuario, $contrasena,$_SESSION['bd']); //Aqui se pasan datos conexion bd
?>

<style type="text/css">
<!--
.Estilo4 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.Estilo5 {font-size: 12px}
-->
</style>
<SCRIPT language="JavaScript" type="text/javascript">
function validar(){
  if (document.getElementById("nueva").value != document.getElementById("confirmar").value) {
      alert("La confirmacion de su nueva clave no es valida");
      return false;
  }
  return true;
}
</SCRIPT>
<BODY>
<?php 
 
if (isset($_POST['nueva'] )){
    $result = mysqli_query($conectar,"SELECT * FROM nomusuarios  WHERE coduser ='".$_SESSION['coduser'] ."' and clave='".hash("sha256",$_POST['actual'])."'"); 
    //echo "SELECT * FROM nomusuarios  WHERE coduser ='".$_SESSION['coduser'] ."' and clave='".hash("sha256","1234")."'";
    //exit;
    $row = mysqli_fetch_row($result);    
   
    if($row[0]['coduser'] == ""){
      $invalido = 1;
    }
    else{
      $result = mysqli_query($conectar,"update nomusuarios set clave = '".hash("sha256",$_POST['nueva'])."' WHERE coduser ='".$_SESSION['coduser'] ."'");       
      $invalido = 0;
    }    
}
?>
<?
if (isset($_POST['nueva'] ) && $invalido == 1){?>
<SCRIPT language="JavaScript" type="text/javascript">
  alert("La clave actual de usuario suministrada no es correcta");
</SCRIPT>
<?}
elseif(isset($_POST['nueva'] ) && $invalido == 0){?>
  <SCRIPT language="JavaScript" type="text/javascript">
  alert("Se realizo el cambio de clave con exito");
</SCRIPT>
<?}
?>

<form name="Editar"  action=<?php echo ($_SERVER["PHP_SELF"]); ?> method="post" onsubmit="return validar();">
<TABLE width="100%">
  <TBODY>
 
  <TR>
    <TD class=row-br>
      <DIV class=tb-body>
      <TABLE cellSpacing=0 cellPadding=4 width="100%" border=0>
        <TBODY>
        <TR>
          <TD>
            Clave Actual:
          </TD>
          <TD colspan="6">

   <input type="password" name="actual" id="actual" size=20 >
            
              
            </TD>
          </TR>
        <TR>
          <TD>
            Clave Nueva:
          </TD>
          <TD colspan="6">
<input type="password" name="nueva" id="nueva" size=20 >
            </TD>
          </TR>
        <TR>
          <TD>
            Confirmar:
          </TD>
          <TD colspan="6">
<input type="password" name="confirmar" id="confirmar" size=20 >
            </TD>
          </TR>
        <TR>
          <TD colspan="6">
              
          </TD>
          <TD>
            <input name="Modificar" type="submit" id="Modificar" value='Modificar'>
          </TD>
          </TR>
        </TBODY></TABLE>
      </DIV>
    </TD>
  </TR>
</TBODY>
</TABLE>
</br></br>

</form>

</BODY>
</HTML>



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
<title>Metronic | Form Stuff - Form Controls</title>
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
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">


<div class="row">
        <div class="col-md-6 ">
          <!-- BEGIN SAMPLE FORM PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                <i class="fa fa-reorder"></i> Default Form
              </div>
              <div class="tools">
              </div>
            </div>
            <div class="portlet-body form">
              <form role="form">
                <div class="form-body">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Text</label>
                    <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter text">
                    <span class="help-block">
                       A block of help text.
                    </span>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                      <span class="input-group-addon">
                        <i class="fa fa-user"></i>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="form-actions">
                  <button type="submit" class="btn blue">Submit</button>
                  <button type="button" class="btn default">Cancel</button>
                </div>
              </form>
            </div>
          </div>
          <!-- END SAMPLE FORM PORTLET-->
          <!-- BEGIN SAMPLE FORM PORTLET-->
          
          <!-- END SAMPLE FORM PORTLET-->
          <!-- BEGIN SAMPLE FORM PORTLET-->
          
          <!-- END SAMPLE FORM PORTLET-->
        </div>
      </div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<!-- END FOOTER -->
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
<script src="../../includes/assets/scripts/core/app.js"></script>
<script>
jQuery(document).ready(function() {   
   // initiate layout and plugins
   App.init();
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>




