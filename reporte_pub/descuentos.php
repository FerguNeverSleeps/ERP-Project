<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

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
    $persona = $conn->fetchAssoc('SELECT * FROM mov_contraloria WHERE id_mov_contraloria = ?', array($id));
    $apellido_paterno = $persona['apellido_paterno'];
    $apellido_materno = $persona['apellido_materno'];
    $apellido_casada = $persona['apellido_casada'];
    $idp = $persona['personal_id'];
    $personaf = $conn->fetchAssoc('SELECT * FROM nompersonal WHERE personal_id = ?', array($idp));
    $ficha = $personaf['ficha'];
}

// Datos tabla relacionada
if ($action == 'edit') {
    $persona2 = $conn->fetchAssoc('SELECT * FROM mov_descuento WHERE id_mov_contraloria = ?', array($id));
    $persona = array_merge($persona, $persona2);
}

// combo tipos decuento
$tipos_desc = array();
$tipos = $conn->fetchAll('SELECT * FROM mov_descuento_tipo');
foreach ($tipos as $value){
    $tipos_desc[$value['codigo']] = $value['descripcion'];
}

// quincena
$quincena = @$persona['quincena'];
if ($quincena == 1) {$quicena1 = 'checked';}
if ($quincena == 2) {$quicena2 = 'checked';}

if ( !empty($_POST['guardar']) ){
    $data = array(
        'personal_id' => @$_POST['idp'],
        'quincena' => @$_POST['quincena'],
        'mes' => @$_POST['mes'],
        'ano' => @$_POST['ano'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => fecha_sql($_POST['fecha_decreto']),
        'id_mov_tipo' => 5,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],

    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $accion = 'Agregar';
        $descripcion = 'Agregar Descuento a Ficha '.$ficha; 
        

    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $accion = 'Modificar';
        $descripcion = 'Modificar Descuento a Ficha '.$ficha; 
    }
    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Descuentos Datos Contraloria',
            'url' => 'descuentos.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);

    $data2 = array(
        'id_mov_contraloria' => $id,
        'id_descuento_tipo' => $_POST['id_descuento_tipo'],
        'fecha' =>date("Y-m-d"),
        'descuento_monto_pendiente' => $_POST['descuento_monto_pendiente'],
        'descuento_porcentaje' => $_POST['descuento_porcentaje'],
    );

    if ($action == 'add') {
        $conn->insert('mov_descuento', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_descuento', $data2,  array('id_mov_contraloria' => $id));
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

    <form name="form1" method="post" class="form-horizontal">
    <input type="hidden" name="idp" value="<?= $idp ?>">

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Quincena:</label>
            <div class="col-sm-5">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">1ra</td>
                        <td class="col-xs-2">2da</td>
                        <td class="col-xs-2">Mes</td>
                        <td class="col-xs-2">Año</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="quincena" value="1" <?= $quicena1 ?>></td>
                        <td><input type="radio" name="quincena" value="2" <?= $quicena2 ?>></td>
                        <td><input name="mes" value="<?= $persona['mes'] ?>" type="number" min="1" max="12" class="form-control"></td>
                        <td><input name="ano" value="<?= $persona['ano'] ?>" type="number" min="2010" class="form-control"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Tipo de descuento:</label>
            <div class="col-sm-2">
                <?= dropdown('id_descuento_tipo',$tipos_desc,$persona['id_descuento_tipo'], 'class="form-control"') ?>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Monto pendiente:</label>
            <div class="col-sm-2">
                <input type="text" name="descuento_monto_pendiente" value="<?= $persona['descuento_monto_pendiente'] ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Porcentaje:</label>
            <div class="col-sm-2">
                <input type="text" name="descuento_porcentaje" value="<?= $persona['descuento_porcentaje'] ?>" class="form-control">
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

</body>
</html>