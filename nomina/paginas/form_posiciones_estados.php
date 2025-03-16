<?php 
session_start();
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");
?>
<script>
    function exportar()
    {
        var estado = document.formulario.estado.value;
        if(estado == "")
        {
            alert('Por favor, seleccione un estado');
        }
        else
        {
            if(estado == 0)
            {
                var estados = "OCUPADAS";
            }
            if(estado == 1)
            {
                var estados = "VACANTES";
            }
            location.href='excel_posiciones_estados.php?estado='+estado+'&estados='+estados; 
        }
    }
    $(document).ready(function()
    {
        $("#estado").select2(
        {
            //placeholder: 'Seleccione un cargo',
            //allowClear: true,
        });
    });

</script>
<form id="formulario" name="formulario" method="post" action="">
    <div class="page-container">
        <div class="page-wrapper-containter">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    Parámetros del Reporte
                                </div>
                                <div class="actions">
                                    <a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes_integrantes.php'">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>                                    
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12 text-center">

                                        <div class="form-group">
                                            <?php 
                                                $sql = "SELECT DISTINCT `estado` FROM `nomposicion`";
                                                $res=sql_ejecutar($sql);
                                            ?>
                                            <select name="estado" id="estado" class="select2 form-control" autofocus>
                                                <option value="">Seleccione un Status</option>
                                                <?php
                                                while( $row = fetch_array($res) )
                                                { 
                                                    $estado = $row["estado"];
                                                    if($estado == 0)
                                                    {
                                                        $estados = "OCUPADAS";
                                                    }
                                                    if($estado == 1)
                                                    {
                                                        $estados = "VACANTES";
                                                    }
                                                    ?>
                                                    <option value="<?php echo $estado;?>"><?php echo $estados; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">&nbsp;</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" align="center">
                                        <div class="btn blue" onClick="javascript:exportar();"><i class="fa fa-download"></i> Exportar</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</body>
<?php include ("../footer4.php");?>
</html>