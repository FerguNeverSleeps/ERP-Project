<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

// Datos entidad
$entidad = $conn->fetchAssoc('SELECT * FROM parametro_inclusion');


// combo Centro de pago
$nivel1 = array();
$niv = $conn->fetchAll('SELECT codorg, descrip FROM nomnivel1 ORDER BY descrip');
foreach ($niv as $value){
    $nivel1[$value['codorg']] = $value['descrip'];
}

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
    $persona2 = $conn->fetchAssoc('SELECT * FROM mov_modificaciones WHERE id_mov_contraloria = ?', array($id));
    $persona = array_merge($persona, $persona2);
}

// quincena
$quincena = @$persona['quincena'];
if ($quincena == 1) {$quicena1 = 'checked';}
if ($quincena == 2) {$quicena2 = 'checked';}

$sexo = strtoupper(substr($persona['sexo'],0,1));
if ($sexo == 'M') {$sexom = 'checked';}
if ($sexo == 'F') {$sexof = 'checked';}

$ti = $persona['titular_interino'];
if ($ti == 'Titular') {$tit = 'checked';}
if ($ti == 'Interino') {$tii = 'checked';}

if ( !empty($_POST['guardar']) ){
    $data = array(
        'personal_id' => @$_POST['idp'],
        'quincena' => @$_POST['quincena'],
        'mes' => @$_POST['mes'],
        'ano' => @$_POST['ano'],
        'num_decreto' => $_POST['num_decreto'],
        'fecha_decreto' => fecha_sql($_POST['fecha_decreto']),
        'nomposicion_id' => $_POST['nomposicion_id'],
        'cedula' => $_POST['cedula'],
        'seguro_social' => $_POST['seguro_social'],
        'clave_ir' => $_POST['clave_ir'],
        'sexo' => $_POST['sexo'],
        'nombres' => $_POST['nombres'],
        'apellido_paterno' => $_POST['apellido_paterno'],
        'apellido_materno' => $_POST['apellido_materno'],
        'apellido_casada' => $_POST['apellido_casada'],
        'fecing' => fecha_sql($_POST['fecing']),
        'titular_interino' => $_POST['titular_interino'],
        'tipemp' => $_POST['tipemp'],
        'observacion' => $_POST['observacion'],
        'id_mov_tipo' => 7,
        'fecha' => date("Y-m-d"),
        'usuario' => @$_SESSION['usuario'],

    );

    if ($action == 'add') {
        $conn->insert('mov_contraloria', $data);
        $id = $conn->lastInsertId();;
        $descripcion = 'Agregar modificaciones a Ficha '.$ficha; 
        $accion = 'Agregar';
    } else {
        unset($data['personal_id'], $data['fecha'], $data['id_mov_tipo']);
        $conn->update('mov_contraloria', $data,  array('id_mov_contraloria' => $id));
        $descripcion = 'Modificar modificaciones a Ficha '.$ficha; 
        $accion = 'Modificar';
    }

    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Modificaciones Datos Contraloria',
            'url' => 'modificaciones.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['usuario'], 
    );
        
    $conn->insert('log_transacciones', $log);

    $data2 = array(
        'id_mov_contraloria' => $id,
        'dias_pagar' => $_POST['dias_pagar'],
        'suesal' => $_POST['suesal'],
        'quincenas_pagar' => $_POST['quincenas_pagar'],
        'dias' => $_POST['dias'],
        'c001' => $_POST['c001'],
        'c002' => $_POST['c002'],
        'c003' => $_POST['c003'],
        'c011' => $_POST['c011'],
        'c012' => $_POST['c012'],
        'c013' => $_POST['c013'],
        'c019' => $_POST['c019'],
        'c080' => $_POST['c080'],
        'c030' => $_POST['c030'],
        'diferencia' => $_POST['diferencia'],
        'diferencia_quincena' => $_POST['diferencia_quincena'],
        'tipnom' => $_POST['tipnom'],
        'descrip_centro_pago' => $_POST['descrip_centro_pago'],
        'codnivel1' => $_POST['codnivel1'],
        'des_car' => $_POST['des_car'],
        'grado' => $_POST['grado'],
        'codcargo' => $_POST['codcargo'],
    );

    if ($action == 'add') {
        $conn->insert('mov_modificaciones', $data2);
    } else {
        unset($data2['id_mov_contraloria']);
        $conn->update('mov_modificaciones', $data2,  array('id_mov_contraloria' => $id));
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
                        <td class="col-xs-2">Año</td>
                    </tr>
                    <tr>
                        <td><input type="radio" name="quincena" value="1" <?= $quicena1 ?>></td>
                        <td><input type="radio" name="quincena" value="2" <?= $quicena2 ?>></td>
                        <td><input name="mes" value="<?= $persona['mes'] ?>" type="text" class="form-control"></td>
                        <td><input name="ano" value="<?= $persona['ano'] ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
            <label for="" class="col-sm-2 control-label">Decreto:</label>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">Numero</td>
                        <td class="col-xs-2">Fecha</td>
                    </tr>
                    <tr>
                        <td><input name="num_decreto" value="<?= $persona['num_decreto'] ?>" type="text" class="form-control"></td>
                        <td>
                            <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                <input name="fecha_decreto" value="<?= fecha($persona['fecha_decreto']) ?>" type="text" class="form-control">
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
            <div class="col-sm-2">
                <select id="posicion" class="form-control" name="nomposicion_id">
                    
                    <option selected="selected" value="<?= $persona['nomposicion_id'] ?>"><?= $persona['nomposicion_id'] ?></option>
                    
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Cedula:</label>
            <div class="col-sm-2">
                <input type="text" name="cedula" value="<?= $persona['cedula'] ?>" class="form-control">
            </div>
            <label for="" class="col-sm-2 control-label">Seguro social:</label>
            <div class="col-sm-2">
                <input type="text" name="seguro_social" value="<?= $persona['seguro_social'] ?>" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Clave I/R:</label>
            <div class="col-sm-2">
                <input type="text" name="clave_ir" value="<?= $persona['clave_ir'] ?>" class="form-control">
            </div>
            <label for="" class="col-sm-2 control-label">Sexo:</label>
            <div class="col-sm-4">
                <label class="radio-inline">
                    <input type="radio" name="sexo" value="F" <?= @$sexof ?>> Femenino
                </label>
                <label class="radio-inline">
                    <input type="radio" name="sexo" value="M" <?= @$sexom ?>> Masculino
                </label>
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
            <label for="" class="col-sm-2 control-label">Inicio de Labores:</label>
            <div class="col-sm-2">
                <div class="input-group date" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                    <input name="fecing" value="<?= fecha($persona['fecing']) ?>" type="text" class="form-control">
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
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
            <label for="" class="col-sm-2 control-label">Condicion:</label>
            <div class="col-sm-2">
                <input name="tipemp" value="<?= $persona['tipemp'] ?>" type="text" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Dias a pagar:</label>
            <div class="col-sm-1">
                <input name="dias_pagar" value="<?= $persona['dias_pagar'] ?>" type="text" class="form-control" id="dias_pagar">
            </div>
            <div class="col-sm-4">
                <table class="table table-bordered">
                    <tr>
                        <td><input name="suesal" value="<?= $persona['suesal'] ?>" type="text" class="form-control" id="suesal">
                        </td>
                        <td><input name="quincenas_pagar" value="<?= $persona['quincenas_pagar'] ?>" type="text" class="form-control"></td>
                        <td><input name="dias" value="<?= $persona['dias'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td class="col-xs-2">Monto</td>
                        <td class="col-xs-1">Qnas.</td>
                        <td class="col-xs-1">Dias</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label"></label>
            <div class="col-sm-5">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">Sueldo</td>
                        <td class="col-xs-2">001</td>
                        <td class="col-xs-4"><input name="c001" value="<?= $persona['suesal'] ?>" type="text" class="form-control" id="c001"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>002</td>
                        <td><input name="c002" value="<?= $persona['c002'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>003</td>
                        <td><input name="c003" value="<?= $persona['c003'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Antigüedad</td>
                        <td>011</td>
                        <td><input name="c011" value="<?= $persona['c011'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Zonas apartadas</td>
                        <td>012</td>
                        <td><input name="c012" value="<?= $persona['c012'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Jefaturas</td>
                        <td>013</td>
                        <td><input name="c013" value="<?= $persona['c013'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Especialidad o exclusividad</td>
                        <td>019</td>
                        <td><input name="c019" value="<?= $persona['c019'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Otros</td>
                        <td>080</td>
                        <td><input name="c080" value="<?= $persona['c080'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Gastos de representación</td>
                        <td>030</td>
                        <td><input name="c030" value="<?= $persona['c030'] ?>" type="text" class="form-control"></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-5">
                <table class="table table-bordered">
                    <tr>
                        <td class="col-xs-2">Qnas.</td>
                        <td class="col-xs-3"><input name="diferencia" value="<?= $persona['diferencia'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Diferencia de salario B/.</td>
                        <td><input name="diferencia_quincena" value="<?= $persona['diferencia_quincena'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Numero de planilla:</td>
                        <td><input name="tipnom" value="<?= $persona['tipnom'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Centro de pago:</td>
                        <td><?= dropdown('descrip_centro_pago_combo',$nivel1,$persona['codnivel1'], 'class="form-control" id="descrip_centro_pago_combo" ') ?></td>
                    </tr>
                    <tr>
                        <td>Provincia:</td>
                        <td><input name="codnivel1" id="codnivel1" value="<?= $persona['codnivel1'] ?>" readonly type="text" class="form-control">
                            <input name="descrip_centro_pago" id="descrip_centro_pago" value="<?= $persona['descrip_centro_pago'] ?>" type="hidden">
                        </td>
                    </tr>
                    <tr>
                        <td>Cargo segun planilla:</td>
                        <td>
                            <select id="des_car_combo" class="form-control" name="des_car_combo" style="width: 270px;">
                                <?php if ($action == 'add') { ?>
                                    <option selected="selected" value="">Seleccione</option>
                                <?php } else { ?>
                                    <option selected="selected" value="<?= $persona['codcargo'] ?>"><?= $persona['codcargo'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Grado o etapa del cargo:</td>
                        <td><input name="grado" value="<?= $persona['grado'] ?>" type="text" class="form-control"></td>
                    </tr>
                    <tr>
                        <td>Codigo del cargo:</td>
                        <td><input name="codcargo" id="codcargo" value="<?= $persona['codcargo'] ?>" type="text" readonly class="form-control">
                            <input name="des_car" id="des_car" value="<?= $persona['des_car'] ?>" type="hidden">
                        </td>
                    </tr>
                </table>
            </div>

        </div>

        <div class="form-group">
            <label for="" class="col-sm-2 control-label">Observacion:</label>
            <div class="col-sm-6">
                <textarea name="observacion" class="form-control" rows="3"><?= $persona['observacion'] ?></textarea>
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
<script>
$( document ).ready(function() {

    //-------------------------------------------------
    $('#dias_pagar').change(function() {
        var dias_pagar = $('#dias_pagar').val();
        var suesal = $('#c001').val();
        var sueldo = (suesal*dias_pagar)/30;
        $('#suesal').val(sueldo);
    });
    //-------------------------------------------------
    $('#descrip_centro_pago_combo').change(function() {
        var pago = $('#descrip_centro_pago_combo option:selected').text();
        $('#descrip_centro_pago').val( pago );
        $('#codnivel1').val( $(this).val() );
    });

    $('#des_car_combo').change(function() {
        var cargo = $('#des_car_combo option:selected').text();
        $('#des_car').val( cargo );
        $('#codcargo').val( $(this).val() );
    });

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

    $('#des_car_combo').select2({
        ajax: {
            url: 'ajax_cargos.php',
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