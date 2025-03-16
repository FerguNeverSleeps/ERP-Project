<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

$id = (empty($_REQUEST['id'])) ? '' : $_REQUEST['id'];
$idm = (empty($_REQUEST['idm'])) ? '' : $_REQUEST['idm'];
$tipo = (empty($_REQUEST['tipo'])) ? '' : $_REQUEST['tipo'];

// Datos del colaborador
if ( $id != '' ){
$colab = $conn->fetchAssoc("SELECT ficha, cedula, apenom, estado, nomposicion_id FROM nompersonal WHERE personal_id = $id");

// Documentos
$stmt = $conn->query("SELECT mov_contraloria.*, mov_tipo.descripcion FROM mov_contraloria
 LEFT OUTER JOIN mov_tipo ON (mov_contraloria.id_mov_tipo = mov_tipo.id_mov_tipo)
 WHERE personal_id = $id");
}

$archivos = array(
    1 =>  'inclusiones.php',
    2 =>  'envios.php',
    3 =>  'ajustes.php',
    4 =>  'retornos.php',
    5 =>  'descuentos.php',
    6 =>  'adicional.php',
    7 =>  'modificaciones.php',
    8 =>  'suspensiones.php',
    9 =>  'ajustes_planilla.php',
    10 => 'imputaciones.php',
    11 => 'reclamos.php',
);
$tablas = array(
    1 =>  'mov_inclusiones',
    2 =>  'mov_licencia',
    3 =>  'mov_ajuste',
    4 =>  'mov_retorno',
    5 =>  'mov_descuento',
    6 =>  'mov_adicional',
    7 =>  'mov_modificaciones',
    8 =>  'mov_suspension',
    9 =>  'mov_ajuste_planilla',
    10 => 'mov_imputaciones',
    11 => 'mov_reclamos',
);
$reportes = array(
    1 => 'reportes.php?tipo=1',
    2 => 'reportes.php?tipo=2',
    3 => 'reportes.php?tipo=3',
    4 => 'reportes.php?tipo=4',
    5 => 'reportes.php?tipo=5',
    6 => 'reportes.php?tipo=6',
    7 => 'rpt_modificaciones.php?tipo=7',
);


if(@$_REQUEST['action'] == 'deleted'){
    $conn->delete('mov_contraloria', array('id_mov_contraloria' => $idm));
    $conn->delete($tablas[$tipo], array('id_mov_contraloria' => $idm));
    // agregar al Log
    
    $accion = 'Borrar';
    $descripcion = 'Borrar Movimiento a Ficha '.$colab['ficha']; 
    $flog = date("Y-m-d H:i:s");
        
    $log = array(
            'descripcion' => $descripcion,
            'fecha_hora' => $flog,
            'modulo' => 'Movimientos Datos Contraloria',
            'url' => 'colaborador.php',
            'accion' => $accion,
            'valor' => '',
            'usuario' => $_SESSION['nombre'], 
    );
        
    $conn->insert('log_transacciones', $log); 
    // hasta aqui agregar Log
?>
<script>
    parent.location.reload();
    parent.$.fancybox.close();
</script>
<?php
    exit;
}


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

<?php if(@$_REQUEST['action'] == 'delete'){ ?>
    <h3>Seguro de borrar el registro?</h3>
    <a class="btn btn-default" href="colaborador.php?id=<?= $id ?>&idm=<?= $idm ?>&tipo=<?= $tipo ?>&action=deleted">Si</a>
    <a class="btn btn-default" onclick="parent.$.fancybox.close();" href="#">No</a>
<?php exit; }  ?>



<div class="container">

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr class="bg-primary">
                    <td>Apellido y Nombre</td>
                    <td>Cedula</td>
                    <td>Situacion</td>
                    <td>Posicion</td>
                </tr>
                <tr>
                    <td><?= utf8_encode($colab['apenom']) ?></td>
                    <td><?= $colab['cedula'] ?></td>
                    <td><?= $colab['estado'] ?></td>
                    <td><?= $colab['nomposicion_id'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a target="_blank" href="inclusiones.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Inclusiones
            </a>
            <a target="_blank" href="descuentos.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Descuentos
            </a>
            <a target="_blank" href="envios.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Envio Licencia
            </a>
            <a target="_blank" href="ajustes.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Ajuste Sueldo
            </a>
            <a target="_blank" href="retornos.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Retorno Licencia
            </a>
            <a target="_blank" href="adicional.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Adicional
            </a>
            <a target="_blank" href="modificaciones.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Modificaciones
            </a>
            <hr>
            <a target="_blank" href="suspensiones.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Suspensiones
            </a>
            <a target="_blank" href="ajustes_planilla.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Ajustes Planilla
            </a>
            <a target="_blank" href="imputaciones.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Imputaciones
            </a>
            <a target="_blank" href="reclamos.php?idp=<?= $id ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Reclamos
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <tr class="info">
                    <td>Fecha Creacion</td>
                    <td>Tipo</td>
                    <td>Quincena</td>
                    <td>Decreto</td>
                    <td style="width: 20px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 20px;"></td>
                    <td style="width: 20px;"></td>
                </tr>
                <?php $i=0; while ($row = $stmt->fetch()) {?>
                <tr>
                    <td><?= fecha($row['fecha']) ?></td>
                    <td><?= $row['descripcion'] ?></td>
                    <td><?= $meses[$row['mes']] ?> <?= $row['ano'] ?> <?= $quincena[$row['quincena']] ?></td>
                    <td><?= $row['num_decreto'] ?> (<?= fecha($row['fecha_decreto']) ?>)</td>
                    <td><a target="_blank" href="<?= $archivos[$row['id_mov_tipo']] ?>?id=<?= $row['id_mov_contraloria'] ?>&action=edit" class="fancybox fancybox.iframe">
                            <img src="../includes/imagenes/icons/pencil.png"></a></td>
                    <td><a target="_blank" href="colaborador.php?id=<?= $id ?>&idm=<?= $row['id_mov_contraloria'] ?>&tipo=<?= $row['id_mov_tipo'] ?>&action=delete" class="fancybox fancybox.iframe">
                            <img src="../includes/imagenes/delete.gif"></a></td>
                    <td><?php if ($row['id_mov_tipo'] != 5) { ?>
                        <a target="_blank" href="<?= $reportes[$row['id_mov_tipo']] ?>&id=<?= $row['id_mov_contraloria'] ?>&idp=<?= $id; ?>&mes=<?= $row['mes'] ?>&ano=<?= $row['ano'] ?>">
                            <img src="../includes/imagenes/icons/printer.png"></a>
                        <?php } ?>
                    </td>
                    
                    <?php if ($row['id_mov_tipo'] == 2 || $row['id_mov_tipo'] == 4)
                        {
                        $num_decreto = $row['num_decreto'];
                        $fecha_decreto = $row['fecha_decreto'];
                        $cod_expediente_det = $row['cod_expediente_det'];
                        if ($cod_expediente_det=='')
                            $cod_expediente_det=0;
                        $sqlº = "SELECT cod_expediente_det FROM expediente                    
                                                    WHERE (fecha_resolucion = $fecha_decreto AND numero_resolucion = $num_decreto) "
                                . "                 OR cod_expediente_det = $cod_expediente_det";
                        //echo $sql;
                        $expediente = $conn->query("SELECT cod_expediente_det FROM expediente                    
                                                    WHERE (fecha_resolucion = '$fecha_decreto' AND numero_resolucion = '$num_decreto') "
                                . "                 OR cod_expediente_det = $cod_expediente_det");
                        $fila_expediente = $expediente->fetch();
                        $codigo_expediente = $fila_expediente['cod_expediente_det'];
//                        echo $codigo_expediente;
                        $sql2 = "SELECT * FROM expediente_adjunto                    
                                                    WHERE cod_expediente_det = '$codigo_expediente' AND principal=1";
//                        echo $sql2;
                        $adjunto = $conn->query("SELECT * FROM expediente_adjunto                    
                                                    WHERE cod_expediente_det = '$codigo_expediente' AND principal=1");
                        $fila_adjunto = $adjunto->fetch();
                        $archivo = $fila_adjunto['archivo'];
//                        echo $archivo;
                        ?>
                            <td>
                                <a target="_blank" href="../nomina/expediente/archivos/<?=$archivo?>" class="fancybox fancybox.iframe">
                                    <img title="Ver Adjunto" src="../nomina/imagenes/documento-icono.png" width="22" height="22"></a>

                            </td> 
                        <?php                         
                        } 
                        else
                        {?>
                            <td></td>
                        <?php                        
                        }

                    ?>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div> <!-- /container -->


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.fancybox.css" media="screen" />
<script>
    $(document).ready(function() {
        $('.fancybox').fancybox( {topRatio:0,width:1000} );
    });
</script>
</body>
</html>