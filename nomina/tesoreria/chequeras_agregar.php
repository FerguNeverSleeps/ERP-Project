<?php
require_once '../../generalp.config.inc.php';
session_start();
ob_start();


// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();
$codigo_banco=@$_GET['banco'];

if(isset($_POST['guardar']))
{
    if((!$_POST['cantidad'])||(!$_POST['primer_cheque']))
    {
        echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
        alert(\"Datos imcompletos, no se puede realizar la operacion\")";
        echo "</SCRIPT>";
    }
    else
    {
        $consul="INSERT INTO nomchequera (cantidad, inicio, situacion, banco) VALUES ('".$_POST['cantidad']."', '".$_POST['primer_cheque']."', 'D', '".$_POST['banco']."')";
        $resultado = query($consul, $conexion);
        cerrar_conexion($conexion);
        echo"<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
        alert(\"Ha creado una nueva chequera\")
        parent.cont.location.href=\"chequeras.php?pagina=1&codigo=".$codigo_banco."\"
        </SCRIPT>";
    }
    
}
include("../header4.php"); // <html><head></head><body>
?>
<SCRIPT language="JavaScript" type="text/javascript">

function cerrar(retorno){
    parent.cont.location.href="chequeras.php?pagina=1&codigo="+retorno
}

</SCRIPT>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <img src="../imagenes/21.png" width="22" height="22" class="icon">Chequera Nueva
                            </div>
                            <div class="actions">
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='chequeras.php?codigo=<?php echo $codigo_banco;?>'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form action="" enctype="multipart/form-data" method="post" name="form_cheques" id="form_cheques" role="form">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="txtcodigo">Cantidad de Cheques:</label>
                                        </div>
                                        <div class="col-md-5">
                                            <INPUT class="form-control" type="text" name="cantidad" id="cantidad" size="30"><INPUT type="hidden" name="banco" value="<?echo $codigo_banco;?>">
                                        </div>
                                    </div>
                                    <BR>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="txtdescripcion">Número del Primer Cheque:</label>
                                        </div>
                                        <div class="col-md-5">
                                            <INPUT class="form-control" type="text" name="primer_cheque" id="primer_cheque" size="30">
                                        </div>               
                                    </div>
                                    <BR>
                                </div>
                                <BR>                   
                                <div class="row">
                                    <div class="col-md-4"></div>                    
                                    <div class="col-md-2">
                                        <button class="btn btn-sm btn-primary" type="submit" name="guardar" value="Guardar">
                                            <i class="fa fa-plus"></i> Agregar
                                        </button>

                                        <?php /*boton_metronic('ok', 'Enviar('.$codigo_banco.')', 2) */?>
                                    </div>                    
                                    <div class="col-md-2">
                                    <button class="btn btn-sm btn-danger" type="button" onclick="javascript: window.location='chequeras.php?codigo=<?php echo $codigo_banco;?>'">
                                            <i class="fa fa-close"></i> Cancelar
                                        </button>
                                        <?php /*boton_metronic('cancel', 'history.back();', 2) */?>
                                    </div>                    
                                    <div class="col-md-4"></div>                
                                </div> 
                            </div>
                            <!-- END PORTLET BODY-->            
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>