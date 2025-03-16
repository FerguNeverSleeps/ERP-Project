<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

// Datos entidad
$entidad = $conn->fetchAssoc('SELECT * FROM parametro_inclusion');

$action = empty( $_GET['action'] ) ? 0 : $_GET['action'];
$idp = empty( $_GET['idp'] ) ? 0 : $_GET['idp'];

// agregar cargamos desde nompersonal
$consulta = "SELECT a.ficha as ficha,
        b.apenom as apenom,
        a.cedula as cedula,
        b.seguro_social as segurosocial,
        b.nomposicion_id as posicion,
        a.unidad as unidad,
        a.codcon as codcon,
        a.valor as valor,
        a.monto as monto,
        a.codnom as codnom,
        c.codtip as codtip,
        b.clave_ir as claveir,
        b.lugarnac as lugarnac,
        d.codorg as codorg,
        b.fecha_decreto as fecha_decreto,
        b.num_decreto as num_decreto
        FROM nom_movimientos_nomina as a, nompersonal as b, nomtipos_nomina as c, nomnivel1 as d WHERE a.codnivel1=d.codorg AND c.codnom=a.codnom AND a.ficha = '$idp' AND b.ficha ='$idp' GROUP BY a.ficha";
if ($action == 'add') {
    $persona = $conn->fetchAssoc($consulta);
}

if ( !empty($_POST['guardar']) ){
    $nombres = explode(" ",$_POST['apenom']);
    $data = array(
        'personal_id' => @$_POST['idp'],
        'quincena' => @$_POST['quincena'],
        'mes' => @$_POST['mes'],
        'ano' => @$_POST['ano'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => fecha_sql($_POST['fecha_decreto']),
        'nomposicion_id' => $_POST['nomposicion_id'],
        'cedula' => $_POST['cedula'],
        'seguro_social' => $_POST['sc'],
        'clave_ir' => $_POST['clave'],
        'sexo' => $_POST['sexo'],
        'nombres' => $nombres[1],
        'apellido_paterno' => $nombres[2],
        'apellido_materno' => "",
        'apellido_casada' => "",
        'fecing' => "",
        'observacion' => $_POST['observacion'],
        'id_mov_tipo' => 11,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],
    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $accion = 'Agregar';
        $descripcion = 'Agregar Reclamo a Ficha '.$ficha; 
        

    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $accion = 'Modificar';
        $descripcion = 'Modificar Reclamo a Ficha '.$ficha; 
    }
    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Reclamo Datos Contraloria',
            'url' => 'reclamos.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);

    $data2 = array(
        'id_mov_contraloria' => $id,
        'personal_id' => $data['personal_id'],
        'nomposicion_id' => $_POST['posicion'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => $_POST['fecha_decreto'],
        'deduccionescla' => $_POST['cla1'],
        'deduccionesvalor' => $_POST['valor1'],
        'observaciones' => $_POST['observaciones'],
    );

    if ($action == 'add') {
        $conn->insert('mov_reclamos', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_reclamos', $data2,  array('id_mov_contraloria' => $id));
    }
?>
<script>
    parent.location.reload();
    parent.$.fancybox.close();
</script>
<?php } ?>


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

<div class="container" style="margin-top: 50px;">
    <div class="col-sm-offset-1 col-lg-offset-1">
    <form name="form1" id="form1" method="post" class="form-horizontal">
        <input type="hidden" name="idp" value="<?= $idp ?>">
        <div class="form-group">
            <div class="col-sm-12 col-lg-12">
                <table class="table table-bordered">
                    <caption><strong>Ministerio:</strong></caption>
                    <tr>
                        <td class="col-xs-2">Area</td>
                        <td class="col-xs-2">Entidad</td>
                        <td class="col-xs-8">Nombre de la entidad</td>
                    </tr>
                    <tr>
                        <td><?= $entidad['area'] ?></td>
                        <td><?= $entidad['ministerio'] ?></td>
                        <td><?= $entidad['nombre_entidad'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-8">
                <table class="table table-bordered">
                    <caption><strong>Quincena:</strong></caption>
                    <tr>
                        <td class="col-xs-2">1ra</td>
                        <td class="col-xs-2">2da</td>
                        <td class="col-xs-4">Mes</td>
                        <td class="col-xs-8">Año</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="quincena" value="1" <?= $quicena1 ?>></td>
                        <td><input type="radio" name="quincena" value="2" <?= $quicena2 ?>></td>
                        <td><input name="mes" id="mes" value="<?= $persona['mes'] ?>" type="number" min="1" max="12" class="form-control"></td>
                        <td><input name="ano" id="ano" value="<?= $persona['ano'] ?>" type="number" min="2010" class="form-control"></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <caption><strong>Decreto:</strong></caption>
                    <tr>
                        <td class="col-xs-2">Numero</td>
                        <td class="col-xs-8">Fecha</td>
                    </tr>
                    <tr>
                        <td><input name="num_decreto" id="num_decreto" value="<?= $persona['num_decreto'] ?>" type="text" class="form-control"></td>
                        <td>
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                <input name="fecha_decreto" value="<?= fecha($persona['fecha_decreto']) ?>"  type="text" class="form-control">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <caption><strong>Reclamo de salario a nombre de:</strong></caption>
                    <tr>
                        <td class="col-sm-8">Nombre</td>
                        <td class="col-sm-4">Cedula</td>
                    </tr>
                    <tr>
                        <td><input name="nombre" value="<?= $persona['apenom'] ?>" type="text" class="form-control" disabled></td>
                        <td><input name="cedula" value="<?= $persona['cedula'] ?>" type="text" class="form-control" disabled></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <tr>
                        <td>Seg.Social</td>
                        <td>Prov</td>
                        <td>Plan</td>
                        <td>Posicion</td>
                        <td>Clave I/R</td>
                    </tr>
                    <tr>
                        <td><input name="sc" value="<?= $persona['segurosocial'] ?>" type="text" class="form-control" disabled></td>
                        <td><input name="prov" value="<?= $persona['codorg'] ?>" type="text" class="form-control" disabled></td>
                        <td><input name="plan" value="<?= $persona['codtip'] ?>" type="text" class="form-control" disabled></td>
                        <td><input name="posicion" value="<?= $persona['posicion'] ?>" type="text" class="form-control" disabled></td>
                        <td><input name="clave" value="<?= $persona['claveir'] ?>" type="text" class="form-control" disabled></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
            $sqlnom = "SELECT monto from nom_movimientos_nomina WHERE ficha='$idp' AND codcon=100";
            $datanom = $conn->fetchAssoc($sqlnom);
            $salario=$datanom['monto'];
            //Seguro Social
            $sqlnom = "SELECT monto from nom_movimientos_nomina WHERE ficha='$idp' AND codcon=200";
            $datanom = $conn->fetchAssoc($sqlnom);
            $ss=$datanom['monto'];
            //Seguro educativo
            $sqlnom = "SELECT monto from nom_movimientos_nomina WHERE ficha='$idp' AND codcon=201";
            $datanom = $conn->fetchAssoc($sqlnom);
            $se=$datanom['monto'];
            //Salario
            $sqlnom = "SELECT monto from nom_movimientos_nomina WHERE ficha='$idp' AND codcon=202";
            $datanom = $conn->fetchAssoc($sqlnom);
            $islr=$datanom['monto'];

            $total=$salario-$ss-$se-$islr;
        ?>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <tr>
                        <td>Sueldo Bruto</td>
                        <td>Seg. Social</td>
                        <td>IMP S/R</td>
                        <td>Seg.Educ</td>
                    </tr>
                    <tr>
                        <td><input id="salario" name="salario" value="<?= $salario ?>" type="text" class="form-control"></td>
                        <td><input id="ss" name="ss" value="<?= $ss ?>" type="text" class="form-control"></td>
                        <td><input id="" name="islr" value="<?= $islr ?>" type="text" class="form-control"></td>
                        <td><input name="se" value="<?= $se ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>  
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <tr>
                        <td colspan="4" align="center">Otras Deducciones</td>
                        <td>Sueldo Neto</td>
                    </tr>
                    <tr>
                        <td>CLA</td>
                        <td>VALOR</td>
                        <td>CLA</td>
                        <td>VALOR</td>
                    </tr>
                    <tr>
                        <td><input id="cla1" name="cla1" value="<?= $persona['ano'] ?>" type="text" class="form-control"></td>
                        <td><input id="valor1" name="valor1" value="<?= $persona['ano'] ?>" type="text" class="form-control"></td>
                        <td><input id="cla2" name="cla2" value="<?= $persona['ano'] ?>" type="text" class="form-control"></td>
                        <td><input id="valor2" name="valor2" value="<?= $persona['nomposicion_id'] ?>" type="text" class="form-control"></td>
                        <td><input id="total" name="total" value="<?= $total ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <table class="table table-bordered">
                    <tr><strong>Observaciones del reclamo</strong></tr>
                    <tr>
                        <td><input name="observaciones" id="observaciones" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>    
    </div> 
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" id="guardar" name="guardar" value="guardar" class="btn btn-primary">Guardar</button>
        </div>
    </div>
    </form>
</div> <!-- /container -->
<br><br><br>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>
<script src="js/bootstrap-datepicker.es.min.js"></script>
<script src="js/select2.full.min.js"></script>
<script>
$( document ).ready(function() {

    $('#tipo_m').change(function() {
        var pago = $('#tipo_m option:selected').val();
        $('#operacion').val( pago );
    });

     $('#ano').change(function() {
        var quincena = document.getElementsByName("quincena");
        var quincena1 = null;
        for(var i=0; i<quincena.length; i++){
            if (quincena[i].checked == true){
                quincena1 = quincena[i].value;
            }
        }
        if (quincena1 == null){
            alert("Seleccione un quincena..!!");
        }
        //var quincena = $('#quincena').val();
        var mes = $('#mes').val();
        var ano = $('#ano').val();
        var num_decreto = $('#num_decreto').val();
        if (quincena == 1) {
            var observacion = 'RECLAMO GENERADO DEL 01/'+mes+'/'+ano+' AL 15/'+mes+'/'+ano+' CON NUMERO DE DECRETO '+num_decreto;
        }else{
            var observacion = 'RECLAMO GENERADO DEL 16/'+mes+'/'+ano+' AL 30/'+mes+'/'+ano+' CON NUMERO DE DECRETO '+num_decreto;
        }
        $('#observaciones').val( observacion );
    });
});
</script>
<!--
<script>
$( document ).ready(function() {
     $('#fecha_decreto').change(function() {
        var num = $('#num_decreto').val();
        var fecha = $('#fecha_decreto').val();
            var observacion = 'RECLAMO CON NUMERO DE DECRETO '+num+' Y FECHA '+fecha;
        $('#observaciones').val( observacion );
    });
});
</script>
-->
</body>
</html>