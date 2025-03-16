<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once '../lib/common.php';
include_once('clases/database.class.php');
include('obj_conexion.php');
session_start();
ob_start();
if(isset($_GET['codigo']) )
{
    $codigo = $_GET['codigo'];
}
if(isset($_GET['cedula']) )
{
    $cedula = $_GET['cedula'];
}
if(isset($_GET['adjunto']) )
{
    $adjunto = $_GET['adjunto'];
}

$consultap="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
$resultadop=$db->query($consultap);
$fetchCon=$db->fetch_array($resultadop);
$nombre=$fetchCon['apenom'];

if(isset($_GET['adjunto']) && $_GET['adjunto']!='')
{
    $consulta_adjunto="SELECT * FROM expediente_adjunto WHERE id_adjunto='$adjunto'";
    $resultado_adjunto=$db->query($consulta_adjunto);
    $fetch_adjunto=$db->fetch_array($resultado_adjunto);
    $nombre_adjunto=$fetch_adjunto['nombre_adjunto'];
    $descripcion=$fetch_adjunto['descripcion'];
    $archivo=$fetch_adjunto['archivo'];
    $principal=$fetch_adjunto['principal'];

}

?>


<SCRIPT  language="JavaScript" type="text/javascript" src="../lib/common.js?<?php echo time(); ?>"></SCRIPT>
<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.7.2.custom.css" />
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>


<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<!-- END PAGE LEVEL SCRIPTS -->


<!--<script type="text/javascript" src="../lib/jquery.js"></script> -->
<!--<script type="text/javascript" src="../lib/jquery-1.3.2.min.js"></script>-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../lib/jquery-ui.min.js"></script>

<script src="../../lib/jquery.maskedinput.js" type="text/javascript"></script>


<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>


<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app.js" type="text/javascript"></script>
<?php 

include("../header4.php"); // <html><head></head><body> ?>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<!-- <i class="fa fa-globe"></i> -->
								<img src="../imagenes/21.png" width="22" height="22" class="icon"> Agregar Adjunto: <?php echo " Expediente N°: "; echo $codigo; echo " - Apellidos/Nombres: "; echo $nombre; echo " - Cédula: "; echo $cedula; ?>
							</div>
							<div class="actions">
								
								<a class="btn btn-sm blue"  onclick="javascript: window.location='expediente_adjunto.php?codigo=<?=$codigo?>&cedula=<?=$cedula?>'">
									<!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
									<i class="fa fa-arrow-left"></i> Regresar
								</a>								
							</div>
						</div>
                                            <div class="portlet-body form ">
                                                 
                                                <FORM name="formulario1" id="formulario1" class="form-horizontal" action="expediente_adjunto_upload.php" method="POST" enctype="multipart/form-data">                                                        
                                                        <div class="form-body">
                                                            <div clss="col-sm-12">
                                                                <!--<TD height="25" class="tb-head" align="left"><strong> TIPO DE REGISTRO </strong>-->
                                                                <input type="hidden" name="editar" id="editar" value="<? echo $editar;?>">
                                                                <input type="hidden" name="cedula" id="cedula" value="<? echo $cedula;?>">
                                                                <input type="hidden" name="codigo" id="codigo" value="<? echo $codigo;?>">  
                                                                <input type="hidden" name="archivo" id="archivo" value="<? echo $archivo;?>">
                                                                <input type="hidden" name="adjunto" id="adjunto" value="<? echo $adjunto;?>">
                                                                <br />

                                                                <div class="form-group">
                                                                    <label class="col-sm-2 control-label">Archivo: </label>
                                                                    <div class="col-md-7">
                                                                         <input type="file" class="form-control" id="archivo_expediente" name="archivo_expediente" <? if (isset($archivo)) echo "value='$archivo'"?> />
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label class="col-sm-2 control-label">Nombre: </label>
                                                                    <div class="col-md-7">
                                                                        <input type="text" name="tx_nombre" id="tx_nombre" class="form-control" <? if (isset($nombre_adjunto)) echo "value='$nombre_adjunto'"?> />
                                                                    </div>
                                                                </div>
                                                                
                                                                 <div class="form-group">
                                                                    <label class="col-sm-2 control-label">Principal (S/N): </label>
                                                                    <div class="col-md-1">
                                                                        <? 
                                                                        if ($principal==0)
                                                                        {?>
                                                                            <input type="checkbox" name="principal" id="principal" class="form-control" />
                                                                        <?}
                                                                        else
                                                                        {?>
                                                                            <input type="checkbox" name="principal" id="principal" class="form-control" checked />
                                                                        <?}?>
                                                                    </div>
                                                                </div> 
                                                                
                                                                <div class="form-group">
                                                                    <label class="col-sm-2 control-label">Observaci&oacute;n: </label>
                                                                    <div class="col-md-7">
                                                                        <textarea rows="6" cols="30" class="form-control" name="tx_observacion" id="tx_observacion"><?php if (isset($descripcion)) echo "$descripcion"?></textarea>
                                                                    </div>
                                                                </div> 
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                    </div>                    
                                                                    <div class="col-md-2">                            
                                                                              <?php boton_metronic('cancel',"expediente_adjunto.php?cedula=$cedula&codigo=$codigo",0); ?>
                                                                    </div>                    

                                                                    <div class="col-md-2">
                                                                        <BUTTON type="submit" class="btn blue"><i class="fa fa-plus"></i> Aceptar</BUTTON>                                                                           
                                                                    </div>                    
                                                                    <div class="col-md-4">                            
                                                                    </div>

                                                                </div>                                                               
                                                            </div>
                                                        </div>
                                                        </FORM>
                                                    </div>
                                                </div>                                                     
                                            </div>
                                        </div>
                                </div>
                        </div>
  </div>
       