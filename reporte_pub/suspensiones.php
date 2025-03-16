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
    $detalle = $conn->fetchAssoc('SELECT * FROM mov_suspension WHERE id_mov_contraloria = ?', array($id));
    $suspension = array_merge($contraloria, $detalle);
    $idp = $suspension['personal_id'];
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
        'id_mov_tipo' => 8,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],

    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $accion = 'Agregar';
        $descripcion = 'Agregar Suspension de pago a Ficha '.$ficha; 
        

    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $accion = 'Modificar';
        $descripcion = 'Modificar Suspension de pago a Ficha '.$ficha; 
    }
    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Suspensiones Datos Contraloria',
            'url' => 'suspensiones.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);

    $data2 = array(
        'id_mov_contraloria' => $id,
        'fecha' =>date("Y-m-d"),
        'detalles' => $_POST['detalles'],
    );

    if ($action == 'add') {
        $conn->insert('mov_suspension', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_suspension', $data2,  array('id_mov_contraloria' => $id));
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
                        <td><input type="radio" name="quincena" value="1" <?= $quicena1 ?>></td>
                        <td><input type="radio" name="quincena" value="2" <?= $quicena2 ?>></td>
                        <td><input name="mes" value="<?= $suspension['mes'] ?>" type="text" class="form-control"></td>
                        <td><input name="ano" value="<?= $suspension['ano'] ?>" type="text" class="form-control"></td>
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
                        <td><input name="num_decreto" value="<?= $suspension['num_decreto'] ?>" type="text" class="form-control"></td>
                        <td>
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                <input name="fecha_decreto" value="<?= fecha($suspension['fecha_decreto']) ?>" type="text" class="form-control">
                                <div class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>    
        <div class="form-group">
            <div class="col-sm-12"><br>
                <table class="table table-bordered">
                    <caption><strong>Suspension de pago:</strong></caption>
                    <tr>
                        <td class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Colaborador</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Cedula</td>
                        <td class="col-xs-1 col-sm-1 col-md-1 col-lg-1">Monto</td>
                        <td class="col-xs-2 col-sm-2 col-md-2 col-lg-2">Posicion</td>
                        <td class="col-xs-4 col-sm-4 col-md-4 col-lg-4">Detalle</td>
                    </tr>
                    <tr>
                        <td><input name="apenom" value="<?= $persona['apenom'] ?>" type="text" class="form-control"></td>
                        <td><input name="cedula" value="<?= $persona['cedula'] ?>" type="text" class="form-control"></td>
                        <td><input name="suesal" value="<?= $persona['suesal'] ?>" type="text" class="form-control"></td>
                        <td><input name="nomposicion_id" value="<?= $persona['nomposicion_id'] ?>" type="text" class="form-control"></td>
                        <td><input name="detalles" value="<?= $suspension['detalles'] ?>" type="text" class="form-control"></td>
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

</body>
</html>