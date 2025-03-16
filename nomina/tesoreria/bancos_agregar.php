<?php

require_once '../../generalp.config.inc.php';
session_start();
ob_start();

include("../lib/common.php") ;
include("../header4.php");
$conexion = conexion();
?>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('#guardar').on('click',function(e)
        {
            e.preventDefault();
            var nombre_banco = $('#nombre_banco').val();
            var num_cuenta = $('#num_cuenta').val();
            var tipo_cuenta = $('input:radio[name=tipo_cuenta]:checked').val();
            var cta_cont_presup = $('#cta_cont_presup').val();
            var cta_cont_fiscal = $('#cta_cont_fiscal').val();
            var monto_apertura = $('#monto_apertura').val();
            var monto_disponible = $('#monto_disponible').val();
            var fecha = $('#fecha').val();
            if(cta_cont_presup == "--Seleccione--" || cta_cont_presup == "" || cta_cont_presup == null)
            {
                cta_cont_presup = "";
            }
            if(cta_cont_fiscal == "--Seleccione--" || cta_cont_fiscal == "" || cta_cont_fiscal == null)
            {
                cta_cont_fiscal = "";
            }
            if(num_cuenta == "" || num_cuenta == null || tipo_cuenta == "" || tipo_cuenta == null)
            {
                if(num_cuenta == "" || num_cuenta == null)
                {
                    alert("Debe Ingresar Un Número De Cuenta");
                    $('#num_cuenta').focus();
                }
                else
                {
                    if(tipo_cuenta == "" || tipo_cuenta == null)
                    {
                        alert("Debe Seleccionar Un Tipo De Cuenta");
                        $('#tipo_cuenta').focus();
                    }
                }
            }
            else
            {
                $.ajax(
                {
                    type: 'POST',
                    data: 'nombre_banco='+nombre_banco+'&num_cuenta='+num_cuenta+'&tipo_cuenta='+tipo_cuenta+'&cta_cont_presup='+cta_cont_presup+'&cta_cont_fiscal='+cta_cont_fiscal+'&monto_apertura='+monto_apertura+'&monto_disponible='+monto_disponible+'&fecha='+fecha,
                    url: 'agregar_bancos.php',
                    success: function(data)
                    {
                        if(data == "PERFECTO")
                        {
                            alert("Se Ha Agregado Satisfactoriamente");
                            window.location.href = "bancos_agregar.php";
                        }
                        else
                        {
                            alert("Hubo Un Error Al Agregar");
                        }
                    }
                })
            }
        });
    });
    function solo_numeros(e)
    {
        var keynum = window.event ? window.event.keyCode : e.which;
        if ((keynum == 8) || (keynum == 46))
            return true;
            return /\d/.test(String.fromCharCode(keynum));
    }
</script>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Bancos
                            </div>
                            <div class="actions">
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='bancos.php?pagina=1'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <FORM name="<?echo $url?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-12" id="error"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Nombre Del Banco:</label>  
                                        </div>  
                                        <div class="col-md-4">
                                            <input class="form-control" name="nombre_banco" type="text" id="nombre_banco" autofocus style="width:320px">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="num_cuenta">N&uacute;mero De Cuenta:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="num_cuenta" type="text" id="num_cuenta" style="width:320px" onkeypress="return solo_numeros(event);">
                                        </div>                
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="tipo_cuenta">Tipo de Cuenta:</label>
                                        </div>
                                        <div class="col-md-10">
                                            <input name="tipo_cuenta" id="tipo_cuenta1" type="radio" value="C">Corriente 
                                            <input name="tipo_cuenta" id="tipo_cuenta2" type="radio" value="AL">Activos líquidos
                                            <input name="tipo_cuenta" id="tipo_cuenta3" type="radio" value="A">
                                            Ahorro
                                            <input name="tipo_cuenta" id="tipo_cuenta4" type="radio" value="P">Participantes 
                                            <input name="tipo_cuenta" id="tipo_cuenta5" type="radio" value="F">Fideicomisos 
                                        </div>                
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Cuenta Contable Presupuestaria:</label>  
                                        </div>  
                                        <div class="col-md-4">
                                            <select name="cta_cont_presup" id="cta_cont_presup" class="form-control" >
                                                <option value="--Seleccione--" selected>--Seleccione-- </option>
                                                <?php
                                                $cta_cont_presup = "SELECT * FROM `cwprecue`";
                                                $res_cta_cont_presup = query($cta_cont_presup, $conexion);
                                                while ($row_cta_cont_presup = fetch_array($res_cta_cont_presup))
                                                {    
                                                    $id_cta_cont_presup = $row_cta_cont_presup['id'];
                                                    $CodCue = $row_cta_cont_presup['CodCue'];
                                                    $Denominacion = $row_cta_cont_presup['Denominacion'];
                                                    $Tipocta = $row_cta_cont_presup['Tipocta'];
                                                    $Tipopuc = $row_cta_cont_presup['Tipopuc'];
                                                    ?>
                                                    <option value="<?php echo $id_cta_cont_presup;?>" > <?php echo $CodCue;?> </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Cuenta Contable Fiscal:</label>  
                                        </div>  
                                        <div class="col-md-4">
                                            <select name="cta_cont_fiscal" id="cta_cont_fiscal" class="form-control" >
                                                <option value="--Seleccione--" selected>--Seleccione-- </option>
                                                <?php
                                                $cta_cont_fiscal = "SELECT * FROM `cwconcue`";
                                                $res_cta_cont_fiscal = query($cta_cont_fiscal, $conexion);
                                                while ($row_cta_cont_fiscal = fetch_array($res_cta_cont_fiscal))
                                                {    
                                                    $id_cta_cont_fiscal = $row_cta_cont_fiscal['id'];
                                                    $Cuenta = $row_cta_cont_fiscal['Cuenta'];
                                                    $Descrip = $row_cta_cont_fiscal['Descrip'];
                                                    ?>
                                                    <option value="<?php echo $id_cta_cont_fiscal;?>" > <?php echo $Cuenta;?> </option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="monto_apertura">Monto De Apertura:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="monto_apertura" type="text" id="monto_apertura" style="width:320px" onkeypress="return solo_numeros(event);">
                                        </div>                
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="monto_disponible">Monto Disponible:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="monto_disponible" type="text" id="monto_disponible" style="width:320px" onkeypress="return solo_numeros(event);">
                                        </div>                
                                    </div> 
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="monto_disponible">Fecha:</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input class="form-control" name="fecha" type="date" id="fecha" value="<?php echo date('Y-m-d');?>" style="width:320px">
                                        </div>                
                                    </div>           
                                </div>
                                <br>                 
                                <div class="row">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-sm blue active" id="guardar" name="guardar">Guardar</button>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-sm default active" 
                                        onclick="javascript: document.location.href='bancos.php?pagina=1'">Cancelar</button>
                                    </div>   
                                    <div class="col-md-4"></div>              
                                </div> 
                            </div>         
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>