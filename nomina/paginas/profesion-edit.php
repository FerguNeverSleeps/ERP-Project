<?php
$codorg = isset($_GET['codorg']) ? $_GET['codorg'] : '';
$descrip = isset($_GET['descrip']) ? $_GET['descrip'] : '';
 ?>
<html>

<head>
<meta charset="utf-8"/>
<link href="../../includes/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/css/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/css/datatables/dataTables.bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/css/bootstrap-datepicker/css/datepicker.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/plugins.css" rel="stylesheet" type="text/css"/>
</head>
<body class="page-header-fixed page-sidebar-closed-hide-logo ">

<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
		<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Profesion</h4>
						</div>
						<div class="modal-body">
							
							<input id="AgregarDescripcion" class="form-control" rows="3" placeholder="Agregar Profesión"></input>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
							<button id="Agregar" type="button" class="btn btn-primary">Guardar</button>
						</div>
					</div>
				</div>
			</div>
			<!-- /.modal -->
			<!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div  class="col-md-12">
					<div id="mensajes"></div>
				</div>
			</div>
			<div class="row">

				<div class="col-md-12">	
					<ol class="breadcrumb">
				  		<li><a href="profesion.html">Profesiones</a> 
						</li>
						<li>
							Editar
						</li>
				  	</ol>

					<div class="portlet box green">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-graduation-cap"></i>Profesiones
							</div>	

						</div>
						<div class="portlet-body">


							<div class="row">	
								<div  class="col-md-12">
									<div id="mensajes">
									</div>
								</div>
							</div>
							<div class="row">
								<div  class="col-md-12">
									<label>Codigo</label>
									<input id="codorg" class="form-control" rows="3" value="<?php echo $codorg;?>"></input>
									<label>Descripción</label>
									<input id="descrip" class="form-control" rows="3" value="<?php echo $descrip;?>"></input>
								</div>
							</div>
							<div class="row">
								&nbsp;
							</div>
							<div class="row">
								<div class="col-md-12">
									<button id="Editar" type="button" class="btn btn-primary">Editar</button>
								</div>		
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/js/scripts/jquery/jquery.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery-migrate.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../includes/js/scripts/jquery/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/bootstrap/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/bootstrap/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/jquery/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/bootstrap/bootstrap-switch.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../includes/js/scripts/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/js/scripts/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../../includes/js/scripts/datatables/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="../../includes/js/scripts/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/js/scripts/metronic.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/layout.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/demo.js" type="text/javascript"></script>
<script src="../../includes/js/scripts/datatable.js"></script>
<script src="../../includes/js/scripts/table-profesion.js"></script>
<script src="../../includes/js/scripts/profesion-edit.js"></script>

</body>
</html>