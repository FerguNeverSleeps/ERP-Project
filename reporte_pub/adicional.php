<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

// Datos entidad
$entidad = $conn->fetchAssoc('SELECT * FROM parametro_inclusion');

$tipos_licencia = array( 0 => 'Con sueldo', 1 => 'Sin sueldo');
$tipos_pago = array( 0 => 'Adicional', 1 => 'Diferencia', 2 => 'Cancelacion de Pago');

$action = empty( $_GET['action'] ) ? '' : $_GET['action'];
$id = empty( $_GET['id'] ) ? 0 : $_GET['id'];
$idp = empty( $_GET['idp'] ) ? 0 : $_GET['idp'];

// combo Centro de pago
$niv = $conn->fetchAll('SELECT codorg, descrip FROM nomnivel1 ORDER BY descrip');
foreach ($niv as $value){
    $nivel1[$value['codorg']] = $value['descrip'];  
} 
function fecha_mod($fecha){
    if (empty($fecha)) {
        return "";
    }else{
        $s = explode("-",$fecha);
        $f=$s[2]."-".$s[1]."-".$s[0];
        return $f;
    }
}
// agregar cargamos desde nompersonal
if ($action == 'add') {
    $persona = $conn->fetchAssoc('SELECT * FROM nompersonal WHERE personal_id = ?', array($idp));
    $apellidos = explode(' ',$persona['apellidos']);
    $ficha = $persona['ficha'];
    $apellido_paterno = @$apellidos[0];
    $apellido_materno = @$apellidos[1];
    $apellido_casada = $persona['apellido_casada'];
    $observacion  = $persona['observacion'];
    $mes = $persona['mes'];
    $ano = $persona['ano'];
    $quincena = $persona['quincena'];
    $num_decreto = $persona['num_decreto'];
    $fecha_decreto = $persona['fecha_decreto'];
    $idp = $persona['personal_id'];
    $ficha = $persona['ficha'];
    $region  = $persona['codnivel1'];
    $posicion = $persona['nomposicion_id'];
    
} else {
// editar cargamos de mov_contraloria
    $persona = $conn->fetchAssoc('SELECT * FROM mov_contraloria WHERE id_mov_contraloria = ?', array($id));
    $apellido_paterno = $persona['apellido_paterno'];
    $apellido_materno = $persona['apellido_materno'];
    $apellido_casada = $persona['apellido_casada'];
    $observacion  = $persona['observacion'];
    $mes = $persona['mes'];
    $ano = $persona['ano'];
    $quincena = $persona['quincena'];
    $num_decreto = $persona['num_decreto'];
    $fecha_decreto = $persona['fecha_decreto'];
    $idp = $persona['personal_id'];
    $persona = $conn->fetchAssoc('SELECT * FROM nompersonal WHERE personal_id = ?', array($idp));
    $ficha = $persona['ficha'];
    $region  = $persona['codnivel1'];
    $posicion = $persona['nomposicion_id'];
}

//echo $ti;
//echo $quincena;
 
// Datos tabla relacionada
if ($action == 'edit') {
    $persona2 = $conn->fetchAssoc('SELECT * FROM mov_adicional WHERE id_mov_contraloria = ?', array($id));
    $persona = array_merge($persona, $persona2);
}

// quincena
//$quincena = @$persona['quincena'];
if ($quincena == 1) {$quicena1 = 'checked';}
if ($quincena == 2) {$quicena2 = 'checked';}

$sexo = strtoupper(substr($persona['sexo'],0,1));
if ($sexo == 'M') {$sexom = 'checked';}
if ($sexo == 'F') {$sexof = 'checked';}

$ti = $persona['tipo_empleado'];
if ($ti == 'Titular') {$tit = 'checked';}
if ($ti == 'Interino') {$tii = 'checked';}

if ( !empty($_POST['guardar']) ){
    $data = array(
        'personal_id' => @$_POST['idp'],
        'quincena' => @$_POST['quincena'],
        'mes' => @$_POST['mes'],
        'ano' => @$_POST['ano'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => fecha_mod($_POST['fecha_decreto']),
        'nomposicion_id' => $_POST['nomposicion_id'],
        'cedula' => $_POST['cedula'],
        'seguro_social' => $_POST['seguro_social'],
        'clave_ir' => $_POST['clave_ir'],
        'sexo' => $_POST['sexo'],
        'nombres' => $_POST['nombres'],
        'apellido_paterno' => $_POST['apellido_paterno'],
        'apellido_materno' => $_POST['apellido_materno'],
        'apellido_casada' => $_POST['apellido_casada'],
        'fecing' => fecha_mod($_POST['fecing']),
        'titular_interino' => $_POST['titular_interino'],
        'tipemp' => $_POST['tipemp'],
        'observacion' => $_POST['observacion'],
        'id_mov_tipo' => 6,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],
    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $descripcion = 'Agregar adicional a Ficha '.$ficha; 
        $accion = 'Agregar';
    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $descripcion = 'Modificar adicional a Ficha '.$ficha; 
        $accion = 'Modificar';
    }

    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Adicional Datos Contraloria',
            'url' => 'adicional.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);


    $val=explode(',',$_POST['adicional_monto']);
    $monto=$val[0].$val[1];
    $data2 = array(
        'id_mov_contraloria' => $id,
        'adicional_tipopago' => $_POST['adicional_tipopago'],
        'adicional_quincenas' => $_POST['adicional_quincenas'],
        'adicional_dias' => $_POST['adicional_dias'],
        'adicional_monto' => $monto,
    );

    if ($action == 'add') {
        $conn->insert('mov_adicional', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_adicional', $data2,  array('id_mov_contraloria' => $id));
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
    <link href="css/bootstrap-datepicker3.min.css" rel="stylesheet">
    <link href="css/select2.min.css" rel="stylesheet">
</head>
<body>


<div class="container" style="margin-top: 50px;">

    <form name="form1" method="post" class="form-horizontal">
        <input type="hidden" name="idp" value="<?= $idp ?>">
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Ministerio:</label>
            <div class="col-sm-10">
                <table class="table table-bordered">
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
            <label for="" class="col-sm-2 control-label">Quincena:</label>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">1ra</td>
                        <td class="col-xs-2">2da</td>
                        <td class="col-xs-2">Mes</td>
                        <td class="col-xs-4">Año</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="quincena" value="1" <?= $quicena1 ?>></td>
                        <td><input type="radio" name="quincena" value="2" <?= $quicena2 ?>></td>
                        <td><input name="mes" value="<?= $mes ?>" type="text" class="form-control"></td>
                        <td><input name="ano" value="<?= $ano ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
            <label for="" class="col-sm-2 control-label">Decreto:</label>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">Numero</td>
                        <td class="col-xs-4">Fecha</td>
                    </tr>
                    <tr>
                        <td><input name="num_decreto" value="<?= $num_decreto ?>" type="text" class="form-control"></td>
                        <td>
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd-mm-yyyy">
                                <input name="fecha_decreto" value="<?= fecha_mod($fecha_decreto) ?>" type="text" class="form-control">
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
            <label for="" class="col-sm-2 control-label">Numero de posicion:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" name="nomposicion_id" value="<?php if($action=='edit'){echo $posicion;}else{echo $persona['nomposicion_id'];} ?>">
            </div>
            <label for="" class="col-sm-2 control-label">Titular:</label>
            <div class="col-sm-2">
                <label class="radio">
                    <input type="radio" name="titular_interino" value="Titular" <?= @$tit ?>> Titular
                </label>
                <label class="radio">
                    <input type="radio" name="titular_interino" value="Interino" <?= @$tii ?>> Interino
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Numero de planilla:</label>
            <div class="col-sm-2">
                <input type="text" name="tipnom" value="<?= $persona['tipnom'] ?>" class="form-control">
            </div>
            <label for="" class="col-sm-2 control-label">Región:</label>
            <div class="col-sm-2">
                 <td><input class="form-control" type="text" name="descrip_centro_pago" id="descrip_centro_pago" value="<?= $nivel1[$region] ?>"></td>
            </div>
            <label for="" class="col-sm-2 control-label">Cedula:</label>
            <div class="col-sm-2">
                <input type="text" name="cedula" value="<?= $persona['cedula'] ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Nombre:</label>
            <div class="col-sm-10">
                <table class="table table-bordered">
                    <tr>
                        <td><input name="nombres" value="<?= $persona['nombres'] ?>" type="text" class="form-control"></td>
                        <td><input name="apellido_paterno" value="<?= $apellido_paterno ?>" type="text" class="form-control"></td>
                        <td><input name="apellido_materno" value="<?= $apellido_materno ?>" type="text" class="form-control"></td>
                        <td><input name="apellido_casada" value="<?= $apellido_casada ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="col-xs-3">Nombres</td>
                        <td class="col-xs-3">A Paterno</td>
                        <td class="col-xs-3">A Materno</td>
                        <td class="col-xs-3">A de casada</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Tipo de pago:</label>
            <div class="col-sm-4">
                <?= dropdown('adicional_tipopago',$tipos_pago,$persona['adicional_tipopago'], 'class="form-control"') ?>
            </div>

            <label for="" class="col-sm-2 control-label">Quincenas/Dias a pagar:</label>
            <div class="col-sm-2">
                Quincenas:<input type="text" name="adicional_quincenas" value="<?= $persona['adicional_quincenas'] ?>" class="form-control">
                Dias:<input type="text" name="adicional_dias" value="<?= $persona['adicional_dias'] ?>" class="form-control">
            </div>

            <label for="" class="col-sm-2 control-label">Monto a pagar o cancelar:</label>
            <div class="col-sm-2">
                <input type="text" name="adicional_monto" id="adicional_monto" value="<?= $persona['adicional_monto'] ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Observacion:</label>
            <div class="col-sm-6">
                <textarea name="observacion" class="form-control" rows="3"><?= $observacion ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" name="guardar" value="guarda" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>

</div> <!-- /container -->


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datepicker.min.js"></script>
<script src="js/bootstrap-datepicker.es.min.js"></script>
<script src="js/select2.full.min.js"></script>
<script src="js/jquery_price_format.js"></script>
<script>
    $( document ).ready(function() {
        $('#adicional_monto').priceFormat();
        $('#posicion').select2({
            ajax: {
                url: 'ajax_posiciones.php',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term }
                },
                processResults: function (data) {
                    return { results: data };
                },
                cache: true
            }
        });

    });
</script>

</body>
</html>