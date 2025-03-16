<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

$termino = $_SESSION['termino']; 
?>
<!DOCTYPE html>
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo $_SESSION['LIVEURL']; ?>/lib/jscalendar/calendar-blue.css" title="win2k-cold-1" /> 
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2.css"/>

<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2-metronic.css"/>

<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2.min.js"></script>

<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2_locale_es.js"></script>

</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width" marginheight="0">