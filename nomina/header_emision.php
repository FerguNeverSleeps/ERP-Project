<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

$termino = $_SESSION['termino']; 
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
<!--<link rel="stylesheet" type="text/css" media="all" href="../lib/jscalendar/calendar-blue.css" title="win2k-cold-1" /> -->
<script type="text/javascript" src="../../../includes/assets/plugins/jquery.js"></script>
<!--<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap/js/bootstrap.js"></script>-->
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../lib/jscalendar/calendar.js"></script> 
<script type="text/javascript" src="../../lib/jscalendar/lang/calendar-es.js"></script> 
<script type="text/javascript" src="../../lib/jscalendar/calendar-setup.js"></script> 
<script  language="JavaScript" type="text/javascript" src="../ewp.js"></script>
<script  language="JavaScript" type="text/javascript" src="../../lib/common.js?<?php echo time(); ?>"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="../../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

<link href="../../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->


</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width" marginheight="0">