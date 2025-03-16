<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

// Datos entidad
$entidad = $conn->fetchAssoc('SELECT * FROM parametro_inclusion');

$action = empty( $_GET['action'] ) ? '' : $_GET['action'];
$id = empty( $_GET['id'] ) ? 0 : $_GET['id'];
$idp = empty( $_GET['idp'] ) ? 0 : $_GET['idp'];

// agregar cargamos desde nompersonal
if ($action == 'add') {
    $persona = $conn->fetchAssoc('SELECT * FROM nompersonal WHERE personal_id = ?', array($idp));
    $apellidos = explode(' ',$persona['apellidos']);
    $ficha = $persona['ficha'];
    $apellido_paterno = @$apellidos[0];
    $apellido_materno = @$apellidos[1];
    $apellido_casada = '';
    
} else {
// editar cargamos de mov_contraloria
    $contraloria = $conn->fetchAssoc('SELECT * FROM mov_contraloria WHERE id_mov_contraloria = ?', array($id));
    $detalle = $conn->fetchAssoc('SELECT * FROM mov_imputaciones WHERE id_mov_contraloria = ?', array($id));
    $ajuste = array_merge($contraloria, $detalle);
    $idp = $ajuste['personal_id'];
    $persona = $conn->fetchAssoc('SELECT * FROM nompersonal WHERE personal_id = ?', array($idp));
    $ficha = $persona['ficha'];
}

if ( !empty($_POST['guardar']) ){
    $data = array(
        'personal_id' => @$_POST['idp'],
        'quincena' => @$_POST['quincena'],
        'mes' => @$_POST['mes'],
        'ano' => @$_POST['ano'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => fecha_sql($_POST['fecha_decreto']),
        'id_mov_tipo' => 10,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],
    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $accion = 'Agregar';
        $descripcion = 'Agregar imputacion a Ficha '.$ficha;

    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $accion = 'Modificar';
        $descripcion = 'Modificar imputacion a Ficha '.$ficha;
    }
    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'imputaciones',
            'url' => 'imputaciones.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);

    $data2 = array(
        'id_mov_contraloria' => $id,
        'personal_id' => $_POST['personal_id'],
        'nomposicion_id' => $_POST['nomposicion_id'],
        'num_decreto' => $_POST['num_decreto'],
        'observaciones' => $_POST['observaciones'],
        'sueldopro' => $_POST['sueldopro'],
        'c071' => $_POST['c071'],
        'c072' => $_POST['c072'],
        'c073' => $_POST['c073'],
        'c074' => $_POST['c074'],
        'total' => $_POST['total'],
        'fecha' => $_POST['fecha'],
        'num_cheque' => $_POST['num_cheque'],
        'num_planilla' => $_POST['num_planilla'],
        'num_registro' => $_POST['num_registro'],
    );

    if ($action == 'add') {
        $conn->insert('mov_imputaciones', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_imputaciones', $data2,  array('id_mov_contraloria' => $id));
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
</head>
<body>
<div class="container" style="margin-top: 50px;">
    
    <div class="col-sm-offset-1 col-lg-offset-1">
    <form name="form1" method="post" class="form-horizontal">
        <input type="hidden" name="idp" value="<?= $idp ?>">
        <div class="form-group">
            <div class="col-sm-10 col-lg-10">
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
            <div class="col-sm-6">
                <table class="table table-bordered">
                    <caption><strong>Quincena:</strong></caption>
                    <tr>
                        <td class="col-xs-2">1ra</td>
                        <td class="col-xs-2">2da</td>
                        <td class="col-xs-4">Mes</td>
                        <td class="col-xs-8">Año</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="radio" name="quincena" id="quincena" value="1" <?= $quicena1 ?>>
                        </td>
                        <td>
                            <input type="radio" name="quincena" id="quincena" value="2" <?= $quicena2 ?>>
                        </td>
                        <td>
                        <input name="mes" id="mes" value="<?= $ajuste['mes'] ?>" type="number" min="01" max="12" class="form-control" required></td>
                        <td><input name="ano" id="ano" value="<?= $ajuste['ano'] ?>" type="number" min="2010" max="<?php echo date('Y'); ?>" class="form-control" required></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <caption><strong>Tipo de Movimiento:</strong></caption>
                    <tr>
                        <td class="col-xs-12">Movimiento</td>
                    </tr>
                    <tr>
                        <td class="col-xs-12">
                            <select name="tipo_m" id="tipo_m" value="<?= $ajuste['tipo_m'] ?>" type="text" class="form-control">
                                <option value="<?php if(isset($ajuste['tipo_m'])){echo $ajuste['tipo_m'];}?>" selected>
                                    <?php if(isset($ajuste['tipo_m'])){echo $ajuste['tipo_m'];}else{echo "SELECCIONE";} ?>
                                </option>
                                <option value="M">MODIFICACION</option>
                                <option value="E">ELIMINACION</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
        <div class="form-group">
            <div class="col-sm-12"><br>
                <table class="table table-bordered">
                    <caption><strong>Datos del Colaborador:</strong></caption>
                    <tr>
                        <td class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Colaborador</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cedula</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Sueldo</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Posicion</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Planilla</td>
                    </tr>
                    <tr>
                        <td>
                        <input name="apenom" value="<?= $persona['apenom'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="cedula" value="<?= $persona['cedula'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="suesal" value="<?= $persona['sueldopro'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="nomposicion_id" value="<?= $persona['nomposicion_id'] ?>" type="text" class="form-control">
                        </td>
                        <td>
                        <input name="planilla" value="<?= $persona['tipnom'] ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-12"><br>
                <table class="table table-bordered">
                    <caption><strong>Datos del Colaborador:</strong></caption>
                    <tr>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">071</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">072</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">073</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">074</td>
                        <td class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Total</td>
                    </tr>
                    <tr>
                        <td>
                        <input name="apenom" value="<?= $persona['apenom'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="cedula" value="<?= $persona['cedula'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="suesal" value="<?= $persona['sueldopro'] ?>" type="text" class="form-control"></td>
                        <td>
                        <input name="nomposicion_id" value="<?= $persona['nomposicion_id'] ?>" type="text" class="form-control">
                        </td>
                        <td>
                        <input name="planilla" value="<?= $persona['tipnom'] ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12"><br>
                <table class="table table-bordered">
                    <caption><strong>Datos de la Operacion:</strong></caption>
                    <tr>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Clave de operacion</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">T-I-B</td>
                        <td class="col-xs-6 col-sm-6 col-md-6 col-lg-6">Observaciones</td>
                    </tr>
                    <tr>
                        <td>
                        <input name="tipo_operacion" id="tipo_operacion" value="<?= $ajuste['tipo_operacion'] ?>" type="text" class="form-control"></td>
                        <td>
                        <select name="tib" id="tib" class="form-control">
                            <option value="<?php if(isset($ajuste['tib'])){echo $ajuste['tib'];}?>" selected>
                                <?php if(isset($ajuste['tib'])){echo $ajuste['tib'];}else{echo "SELECCIONE";} ?>
                            </option>
                            <option value="T">Titular</option>
                            <option value="I">Interino</option>
                            <option value="B">"B"</option>
                        </select>
                        </td>
                        <td>
                        <input name="observaciones" id="observaciones" value="<?= $ajuste['observaciones'] ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="guardar" value="guarda" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>

</div> <!-- /container -->
<br><br><br>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>
<script src="js/bootstrap-datepicker.es.min.js"></script>
<script>
$( document ).ready(function() {

    $('#tipo_m').change(function() {
        var pago = $('#tipo_m option:selected').val();
        $('#tipo_operacion').val( pago );
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
        if (quincena == 1) {
            var observacion = 'PAGAR DEL 01/'+mes+'/'+ano+' AL 15/'+mes+'/'+ano;
        }else{
            var observacion = 'PAGAR DEL 16/'+mes+'/'+ano+' AL 30/'+mes+'/'+ano;
        }
        $('#observaciones').val( observacion );
    });
});
</script>
</body>
</html>