<?php
include("vendor/autoload.php");
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);

$id = (empty($_REQUEST['id'])) ? '' : $_REQUEST['id'];

// Datos del colaborador
$colab = $conn->fetchAssoc("SELECT ficha, cedula, apenom, estado, nomposicion_id FROM nompersonal WHERE personal_id = $id");

// Documentos
$stmt = $conn->query("SELECT mov_contraloria.*, mov_tipo.descripcion FROM mov_contraloria
 LEFT OUTER JOIN mov_tipo ON (mov_contraloria.id_mov_tipo = mov_tipo.id_mov_tipo)
 WHERE personal_id = $id");

$archivos = array(
    1 => 'inclusiones.php',
    2 => 'envios.php',
    3 => 'ajustes.php',
    4 => 'retornos.php',
    5 => 'descuentos.php',
    6 => 'adicional.php',
    7 => 'modificaciones.php',
);

$reportes = array(
    1 => 'reportes.php?tipo=1',
    2 => 'reportes.php?tipo=2',
    3 => 'reportes.php?tipo=3',
    4 => 'reportes.php?tipo=4',
    5 => 'reportes.php?tipo=5',
    6 => 'reportes.php?tipo=6',
    7 => 'reportes.php?tipo=7',
);

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
                    <td><?= $colab['apenom'] ?></td>
                    <td><?= $colab['cedula'] ?></td>
                    <td><?= $colab['estado'] ?></td>
                    <td><?= $colab['nomposicion_id'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-bottom: 10px;">
            <a target="_blank" href="inclusiones.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Incluciones
            </a>
            <a target="_blank" href="descuentos.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Descuentos
            </a>
            <a target="_blank" href="envios.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Envio Licencia
            </a>
            <a target="_blank" href="ajustes.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Ajuste Sueldo
            </a>
            <a target="_blank" href="retornos.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Retorno Licencia
            </a>
            <a target="_blank" href="adicional.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Adicional
            </a>
            <a target="_blank" href="modificaciones.php?idp=<?= $colab['ficha'] ?>&action=add" class="btn btn-default fancybox fancybox.iframe">
                <img src="../includes/imagenes/icons/application.png"> Modificaciones
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
                </tr>
                <?php while ($row = $stmt->fetch()) {?>
                <tr>
                    <td><?= fecha($row['fecha']) ?></td>
                    <td><?= $row['descripcion'] ?></td>
                    <td><?= $meses[$row['mes']] ?> <?= $row['ano'] ?> <?= $quincena[$row['quincena']] ?></td>
                    <td><?= $row['num_decreto'] ?> (<?= fecha($row['fecha_decreto']) ?>)</td>
                    <td><a target="_blank" href="<?= $archivos[$row['id_mov_tipo']] ?>?id=<?= $row['id_mov_contraloria'] ?>&action=edit" class="fancybox fancybox.iframe">
                            <img src="../includes/imagenes/icons/pencil.png"></a></td>
                    <td><?php if ($row['id_mov_tipo'] != 5 ) { ?>
                        <a target="_blank" href="<?= $reportes[$row['id_mov_tipo']] ?>&id=<?= $row['id_mov_contraloria'] ?>">
                            <img src="../includes/imagenes/icons/printer.png"></a>
                        <?php } ?>
                    </td>
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