<?php
$id = empty( $_GET['id'] ) ? 0 : $_GET['id'];

 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="images/favicon.ico">
    <title></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>


<div class="container">
    <div class="alert alert-info" role="alert">
        <h4 class="block">Traslados</h4>

    </div>
    <form name="form1" method="post" class="form-horizontal">
    <input type="hidden" name="id" value="<?= $id ?>">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Quincena:</label>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">1ra</td>
                        <td class="col-xs-2">2da</td>
                        <td class="col-xs-2">Mes</td>
                        <td class="col-xs-2">Año</td>
                    </tr>
                    <tr>
                        <input type="hidden" id="id" name="id" value="<?= $id;?>">

                        <td><input type="radio" id="quincena" name="quincena" value="1" checked></td>
                        <td><input type="radio" id="quincena" name="quincena" value="2"></td>
                        <td><select id="mes">
                            <option value="01">Enero</option>
                            <option value="02">Frebrero</option>
                            <option value="03">Marzo</option>
                            <option value="04">Abril</option>
                            <option value="05">Mayo</option>
                            <option value="06">Junio</option>
                            <option value="07">Julio</option>
                            <option value="08">Agosto</option>
                            <option value="09">Septiembre</option>
                            <option value="10">Octubre</option>
                            <option value="11">Noviembre</option>
                            <option value="12">Diciembre</option>
                        </select></td>
                        <td><select id="ano">
                            <option value="<?php echo date('Y');?>"><?php echo date('Y');?></option>
                            <option value="<?php echo (date('Y')-1);?>"><?php echo (date('Y')-1);?></option>
                            <option value="<?php echo (date('Y')-2);?>"><?php echo (date('Y')-2);?></option>
                            <option value="<?php echo (date('Y')-3);?>"><?php echo (date('Y')-3);?></option>
                            <option value="<?php echo (date('Y')-4);?>"><?php echo (date('Y')-4);?></option>
                            <option value="<?php echo (date('Y')-5);?>"><?php echo (date('Y')-5);?></option>
                            <option value="<?php echo (date('Y')-6);?>"><?php echo (date('Y')-6);?></option>
                            <option value="<?php echo (date('Y')-7);?>"><?php echo (date('Y')-7);?></option>
                            <option value="<?php echo (date('Y')-8);?>"><?php echo (date('Y')-8);?></option>
                            <option value="<?php echo (date('Y')-9);?>"><?php echo (date('Y')-9);?></option>
                            <option value="<?php echo (date('Y')-10);?>"><?php echo (date('Y')-10);?></option>

                        </select></td>
                    </tr>
                </table>
            </div>
        </div>

        

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a  id="imprimir" value="imprime" class="btn btn-primary">Generar reporte</a>
            </div>
        </div>
    </form>

</div> <!-- /container -->
<br><br><br>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript">
$(document).ready(function() { 
    $("#imprimir").on("click", function(){
        quincena=$("#quincena:checked").val();
        mes=$("#mes").val();
        ano=$("#ano").val();
        id=$("#id").val();
        //alert(id);
        if(quincena=="")
            alert("Selecciones la quincena");
        else
        {
            //alert(quincena);
            window.open("../nomina/tcpdf/reportes/reporte_traslados.php?id="+id+"&quincena="+quincena+"&mes="+mes+"&ano="+ano);
            parent.$.fancybox.close();   
        }
        
    });
    
});
</script>

</body>
</html>