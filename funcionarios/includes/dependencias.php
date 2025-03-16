<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

$termino = $_SESSION['termino']; 
?>
<!-- BEGIN GLOBAL MANDATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
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
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-datepicker/css/datepicker.css">



<!-- Footer-->

<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/data-tables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>




<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo $_SESSION['LIVEURL']; ?>/includes/assets/scripts/core/app1.js"></script>
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
jQuery(document).ready(function() {       
   App.init();
  // TableManaged.init();
 // $('#sample_1').DataTable(); 
});
</script>