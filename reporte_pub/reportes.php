<?php
include("vendor/autoload.php"); 
include("config.php");
$config = new \Doctrine\DBAL\Configuration();
$conn = \Doctrine\DBAL\DriverManager::getConnection($connection, $config);
use Dompdf\Dompdf;

$id = (empty($_REQUEST['id'])) ? '' : $_REQUEST['id'];
$tipo = (empty($_REQUEST['tipo'])) ? '' : $_REQUEST['tipo'];

// Tabla segun tipo
switch ($tipo) {
    case 1: $tabla = "mov_inclusiones"; $titulo = 'INCLUSIONES'; break;
    case 2: $tabla = "mov_licencia"; $titulo = 'ENVIO DE LICENCIA'; break;
    case 3: $tabla = "mov_ajuste"; $titulo = 'AJUSTE AL SUELDO SEGÚN PLANILLA'; break;
    case 4: $tabla = "mov_retorno"; $titulo = 'RETORNO DE LICENCIA'; break;
    case 6: $tabla = "mov_adicional"; $titulo = 'ADICIONAL, DIFERENCIA Y CANCELACION DE PAGO'; break;
}

// logos
$logos = $conn->fetchAssoc('SELECT imagen_izq, imagen_der FROM nomempresa LIMIT 1');

// Datos
$entidad = $conn->fetchAssoc('SELECT * FROM parametro_inclusion');
$row  = $conn->fetchAssoc("SELECT * FROM mov_contraloria WHERE id_mov_contraloria = $id");
$row2 = $conn->fetchAssoc("SELECT * FROM $tabla WHERE id_mov_contraloria = $id");
$row  = array_merge($row, $row2);
$personal_id = @$row['personal_id'];
$nomposicion_id = @$row['nomposicion_id'];

$rowp = $conn->fetchAssoc("SELECT * FROM nompersonal WHERE personal_id = $personal_id");
$codnivel1 = @$rowp['codnivel1'];
$planilla = @$rowp['tipnom'];

$rowr= $conn->fetchAssoc("SELECT * FROM nomnivel1 WHERE codorg = $codnivel1");

$rowp2 = $conn->fetchAssoc("SELECT * FROM nomposicion WHERE nomposicion_id = $nomposicion_id");

$rowpl = $conn->fetchAssoc("SELECT * FROM nomtipos_nomina WHERE codtip = $planilla");


 
 
 

 


// quincena
$quincena = @$row['quincena'];
if ($quincena == 1) {$quicena1 = 'XX';}
if ($quincena == 2) {$quicena2 = 'XX';}

// sexo
$sexo = @$row['sexo'];
if ($sexo == 'M') {$sexo_m = 'XX';}
if ($sexo == 'F') {$sexo_f = 'XX';}

// titular_interino
if (@$row['titular_interino'] == 'Titular'){$titular = 'X';} else {$interino = 'X';}

// licencia_tipo
if (@$row['licencia_tipo'] == 0){$tipo_con = 'X';} else {$tipo_sin = 'X';}

// adicional_tipopago
if (@$row['adicional_tipopago'] == 0){$tipo_adi = 'X';}
if (@$row['adicional_tipopago'] == 1){$tipo_dif = 'X';}
if (@$row['adicional_tipopago'] == 2){$tipo_can = 'X';}

ob_start();
?>
<!DOCTYPE html>
<head>
<meta charset="utf-8">
<title></title>
<style>
.centrado{ text-align:center;}
.borde{ border:1px solid #000;}
.fondo{ background-color:#EBEBEB;}
</style>
</head>
<body>
<table style="width: 100%; border-collapse:collapse;">
  <tr>
    <td class="borde">
    <table style="width: 100%">
        <tr>
		  <? if ($logos['imagen_izq'] != ''){ ?>
          <td><img src="../nomina/imagenes/<?= $logos['imagen_izq'] ?>" style="width: 110px;"></td>
          <?php } ?>
          
          <td class="centrado" style="font-size: 16px;">REPÚBLICA DE PANAMÁ<br>
            CONTRALORÍA GENERAL DE LA REPÚBLICA DE PANAMÁ<br>
            Dirección General de Fiscalización<br>
            <?= $titulo ?>
          </td>
          
		  <? if ($logos['imagen_der'] != ''){ ?>
          <td align="right"><img src="../nomina/imagenes/<?= $logos['imagen_der'] ?>" style="width: 110px;"></td>
          <?php } ?>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="borde">
    <table width="100%" border="0" cellpadding="0" cellspacing="8">
        <tr>
          <td>Ministerio:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">Area</td>
                  <td class="borde centrado fondo">Entidad</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= $entidad['area'] ?></td>
                  <td class="borde centrado"><?= $entidad['ministerio'] ?></td>
                </tr>
              </table></td>
              <td width="5%">&nbsp;</td>
              <td width="75%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">Nombre de la Entidad</td>
                  </tr>
                <tr>
                  <td class="borde centrado"><?= $entidad['nombre_entidad'] ?></td>
                  </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td width="15%">Quincena:</td>
          <td width="85%"><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="30%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">1ra</td>
                  <td class="borde centrado fondo">2da</td>
                  <td class="borde centrado fondo">Mes</td>
                  <td class="borde centrado fondo">Año</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= $quicena1 ?></td>
                  <td class="borde centrado"><?= $quicena2 ?></td>
                  <td class="borde centrado"><?= $row['mes'] ?></td>
                  <td class="borde centrado"><?= $row['ano'] ?></td>
                </tr>
              </table></td>
              <td width="40%" valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="51%">Decreto/Resuelto:</td>
                  <td width="49%" class="borde centrado"><?= $row['num_decreto'] ?></td>
                </tr>
              </table></td>
              <td width="30%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">Dia</td>
                  <td class="borde centrado fondo">Mes</td>
                  <td class="borde centrado fondo">Año</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= substr($row['fecha_decreto'],8,2) ?></td>
                  <td class="borde centrado"><?= substr($row['fecha_decreto'],5,2) ?></td>
                  <td class="borde centrado"><?= substr($row['fecha_decreto'],0,4) ?></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <?php if ($tipo == 1){ ?>
        <tr>
          <td nowrap="nowrap">Número de <br> posición:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%" valign="top">&nbsp;</td>
              <td width="40%" valign="top">&nbsp;</td>
              <td width="40%">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="49%" class="borde centrado"><?= $row['nomposicion_id'] ?></td>
                </tr>
              </table></td>
              <td valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="40%">Cédula:</td>
                  <td width="60%" class="borde centrado"><?= $row['cedula'] ?></td>
                </tr>
              </table></td>
              <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="40%">Seguro Social:</td>
                  <td width="60%" class="borde centrado"><?= $row['seguro_social'] ?></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Clave I/R:</td>
          <td><table width="50%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%" valign="top">&nbsp;</td>
              <td width="40%" rowspan="2" valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" class="centrado">Sexo:</td>
                  <td width="50%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                      <td width="30" class="borde centrado fondo">M</td>
                      <td width="30" class="borde centrado fondo">F</td>
                    </tr>
                    <tr>
                      <td class="borde centrado"><?= $sexo_m ?></td>
                      <td class="borde centrado"><?= $sexo_f ?></td>
                    </tr>
                  </table></td>
                  </tr>
              </table></td>
              </tr>
            <tr>
              <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="49%" class="borde centrado"><?= $row['clave_ir'] ?></td>
                  </tr>
              </table></td>
              </tr>
          </table></td>
        </tr>
        <?php } ?>
        <?php if ($tipo == 2 or $tipo == 3 or $tipo == 4 or $tipo == 6){ ?>
        <tr>
          <td nowrap="nowrap">Número de <br>
            posición:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%" valign="top">&nbsp;</td>
              <td width="40%" rowspan="2" valign="top"><table width="80%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td width="60%" class="centrado">Titular</td>
                  <td width="40%" class="borde centrado"><?= @$titular ?></td>
                </tr>
                <tr>
                  <td class="centrado">Interino</td>
                  <td class="borde centrado"><?= @$interino ?></td>
                </tr>
              </table></td>
              <td width="40%">&nbsp;</td>
            </tr>
            <tr>
              <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="49%" class="borde centrado"><?= $row['nomposicion_id'] ?></td>
                  </tr>
              </table></td>
              <td><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="40%">Cédula:</td>
                  <td width="60%" class="borde centrado"><?= $row['cedula'] ?></td>
                  </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td nowrap="nowrap">Número de <br>
            Planilla:</td>
          <td><table width="20%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="25%" class="borde centrado"><?= $rowp['tipnom'] ?></td>
              <td width="90%" class="borde centrado"><?= $rowpl['descrip'] ?></td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        <tr>
          <td>Nombre</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr class="centrado">
              <td width="25%"><?= $row['nombres'] ?></td>
              <td width="25%"><?= $row['apellido_paterno'] ?></td>
              <td width="25%"><?= $row['apellido_materno'] ?></td>
              <td width="25%"><?= $row['apellido_casada'] ?></td>
            </tr>
            <tr class="centrado">
              <td style="border-top:1px solid #000;">Nombres</td>
              <td style="border-top:1px solid #000;">A Paterno</td>
              <td style="border-top:1px solid #000;">A Materno</td>
              <td style="border-top:1px solid #000;">A de Casada</td>
            </tr>
          </table></td>
        </tr>
        <?php if ($tipo == 2){ ?>
        <tr>
          <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="20%"><table width="80%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td colspan="2" class="borde centrado fondo">Tipo de Licencia</td>
                  </tr>
                <tr>
                  <td width="55%" nowrap="nowrap">Con sueldo</td>
                  <td width="45%" align="center" class="borde"><?= @$tipo_con ?></td>
                </tr>
                <tr>
                  <td>Sin Sueldo</td>
                  <td align="center" class="borde"><?= @$tipo_sin ?></td>
                </tr>
              </table></td>
              <td width="25%" align="center"><table width="80%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td colspan="2" class="borde centrado fondo">Meses / Dias a Enviar</td>
                </tr>
                <tr>
                  <td width="55%" nowrap>Meses:</td>
                  <td width="45%" align="center" class="borde"><?= $row['licencia_meses'] ?></td>
                </tr>
                <tr>
                  <td>Dias:</td>
                  <td align="center" class="borde"><?= $row['licencia_dias'] ?></td>
                </tr>
              </table></td>
              <td width="50%" align="right"><table width="80%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td colspan="3" class="borde centrado fondo">Desde</td>
                  <td colspan="3" class="borde centrado fondo">Hasta</td>
                  </tr>
                <tr>
                  <td class="borde centrado ">Dia</td>
                  <td class="borde centrado ">Mes</td>
                  <td class="borde centrado ">Año</td>
                  <td class="borde centrado ">Dia</td>
                  <td class="borde centrado ">Mes</td>
                  <td class="borde centrado ">Año</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= substr($row['licencia_desde'],8,2) ?></td>
                  <td class="borde centrado"><?= substr($row['licencia_desde'],5,2) ?></td>
                  <td class="borde centrado"><?= substr($row['licencia_desde'],0,4) ?></td>
                  <td class="borde centrado"><?= substr($row['licencia_hasta'],8,2) ?></td>
                  <td class="borde centrado"><?= substr($row['licencia_hasta'],5,2) ?></td>
                  <td class="borde centrado"><?= substr($row['licencia_hasta'],0,4) ?></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td>Tipo de licencia:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="49%" class="borde centrado"><?= $row['licencia_descripcion'] ?></td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        <?php if ($tipo == 3){ ?>
        <tr>
          <td>Dias a descontar:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="30%">&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td class="borde centrado"><?= $row['ajuste_dias'] ?></td>
              <td><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="40%">Monto a Descontar:</td>
                  <td width="60%" class="borde centrado" style="font-size:20px;"><?= $row['ajuste_monto'] ?></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        <?php if ($tipo == 4){ ?>  
        <tr>
          <td>Fecha de Retorno:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="30%" valign="top"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">Dia</td>
                  <td class="borde centrado fondo">Mes</td>
                  <td class="borde centrado fondo">Año</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= substr($row['retorno_fecha'],8,2) ?></td>
                  <td class="borde centrado"><?= substr($row['retorno_fecha'],5,2) ?></td>
                  <td class="borde centrado"><?= substr($row['retorno_fecha'],0,4) ?></td>
                </tr>
              </table></td>
              <td width="5%">&nbsp;</td>
              <td width="40%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td width="60%" align="right" class="">Días a Pagar&nbsp; </td>
                  <td width="40%" class="borde centrado"><?= $row['retorno_dias'] ?></td>
                </tr>
                <tr>
                  <td align="right" class="">Monto a Descontar &nbsp;</td>
                  <td class="borde centrado"><?= $row['retorno_monto'] ?></td>
                </tr>
              </table></td>
              <td width="25%" valign="top">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        <?php if ($tipo == 6){ ?>
        <tr>
          <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="35%" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td colspan="2" class="borde centrado fondo">Tipo de Pago</td>
                </tr>
                <tr>
                  <td width="76%" align="right" nowrap="nowrap">Adicional:</td>
                  <td width="24%" align="center" class="borde"><?= @$tipo_adi ?></td>
                </tr>
                <tr>
                  <td align="right">Diferencia:</td>
                  <td align="center" class="borde"><?= @$tipo_dif ?></td>
                </tr>
                <tr>
                  <td align="right">Cancelación de Pago:</td>
                  <td align="center" class="borde"><?= @$tipo_can ?></td>
                </tr>
              </table></td>
              <td width="35%" align="center" valign="top"><table width="80%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td colspan="2" class="borde centrado fondo">Quincenas y/o Días a Pagar</td>
                </tr>
                <tr>
                  <td width="71%" align="right" nowrap>Quincenas:</td>
                  <td width="29%" align="center" class="borde"><?= $row['adicional_quincenas'] ?></td>
                </tr>
                <tr>
                  <td align="right">Dias:</td>
                  <td align="center" class="borde"><?= $row['adicional_dias'] ?></td>
                </tr>
              </table></td>
              <td width="30%" align="right" valign="top"><table width="90%" border="0" cellspacing="0" cellpadding="1">
                <tr>
                  <td nowrap="nowrap" class="borde centrado fondo">Monto a  Pagar o Cancelar</td>
                </tr>
                <tr>
                  <?php
                  $monto=number_format($row['adicional_monto'], 2, '.',',');
                  ?>
                  <td align="center" nowrap class="borde"><?= $monto; ?></td>
                  </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        <?php if ($tipo == 1){ ?>
        <tr>
          <td nowrap="nowrap">Fecha de<br>
            Iinicio de Lab.:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td class="borde centrado fondo">Dia</td>
                  <td class="borde centrado fondo">Mes</td>
                  <td class="borde centrado fondo">Año</td>
                </tr>
                <tr>
                  <td class="borde centrado"><?= substr($row['fecing'],8,2) ?></td>
                  <td class="borde centrado"><?= substr($row['fecing'],5,2) ?></td>
                  <td class="borde centrado"><?= substr($row['fecing'],0,4) ?></td>
                </tr>
              </table></td>
              <td width="5%">&nbsp;</td>
              <td width="37%"><table width="80%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                <tr>
                  <td width="60%" class="centrado">Titular</td>
                  <td width="40%" class="borde centrado"><?= @$titular?></td>
                </tr>
                <tr>
                  <td class="centrado">Interino</td>
                  <td class="borde centrado"><?= @$interino ?></td>
                </tr>
              </table></td>
              <td width="38%" valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="40%">Condición:</td>
                  <td width="60%" class="borde centrado"><?= $row['tipemp'] ?></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td>Dias a Pagar:</td>
          <td><table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="20%" valign="top">&nbsp;</td>
              <td width="20%" valign="top">&nbsp;</td>
              <td width="40%" rowspan="2" valign="top"><table width="80%" border="0" align="center" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="50%" class="centrado">&nbsp;</td>
                  <td width="50%" rowspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="0" style="border-collapse:collapse;">
                    <tr>
                      <td class="borde centrado fondo">Qnas.</td>
                      <td class="borde centrado fondo">Días</td>
                    </tr>
                    <tr>
                      <td class="borde centrado"><?= $row['quincenas_pagar'] ?></td>
                      <td class="borde centrado"><?= $row['dias'] ?></td>
                    </tr>
                  </table></td>
                </tr>
                <tr>
                  <td class="centrado"><table width="80%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td width="49%" class="borde centrado"><?= $row['suesal'] ?></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width="49%" class="borde centrado"><?= $row['dias_pagar'] ?>&nbsp;</td>
                </tr>
              </table></td>
              <td valign="top">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2"><table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td width="20%" class="borde">Sueldo</td>
              <td width="8%" align="center" class="borde">001</td>
              <td width="17%" align="center" class="borde"><?= $row['c001'] ?></td>
              <td style="border-top:1px solid #000;" width="30%">&nbsp;</td>
              <td style="border-top:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
              <td align="center" class="fondo" style="border-top:1px solid #000; border-right:1px solid #000;">Qnas.</td>
            </tr>
            <tr>
              <td class="borde">&nbsp;</td>
              <td align="center" class="borde">002</td>
              <td align="center" class="borde"><?= $row['c002'] ?></td>
              <td>Diferencia de Salario B/.</td>
              <td width="17%" align="center" class="borde"><?= $row['diferencia'] ?></td>
              <td width="8%" align="center" class="borde"><?= $row['diferencia_quincena'] ?></td>
            </tr>
            <tr>
              <td class="borde">&nbsp;</td>
              <td align="center" class="borde">003</td>
              <td align="center" class="borde"><?= $row['c003'] ?></td>
              <td>Numero de planilla:</td>
              <td colspan="2" align="center" class="borde"><?= $row['tipnom'] ?></td>
            </tr>
            <tr>
              <td class="borde">Antigüedad</td>
              <td align="center" class="borde">011</td>
              <td align="center" class="borde"><?= $row['c011'] ?></td>
              <td>Centro de pago:</td>
              <td colspan="2" align="center" class="borde"><?= $row['descrip_centro_pago'] ?></td>
            </tr>
            <tr>
              <td class="borde">Zonas apartadas</td>
              <td align="center" class="borde">012</td>
              <td align="center" class="borde"><?= $row['c012'] ?></td>
              <td>Provincia</td>
              <td colspan="2" align="center" class="borde"><?= $row['codnivel1'] ?></td>
            </tr>
            <tr>
              <td class="borde">Jefaturas</td>
              <td align="center" class="borde">013</td>
              <td align="center" class="borde"><?= $row['c013'] ?></td>
              <td>Cargo Segun Planilla:</td>
              <td colspan="2" align="center" class="borde"><?= $row['des_car'] ?></td>
            </tr>
            <tr>
              <td class="borde">Especialidad o Exc</td>
              <td align="center" class="borde">019</td>
              <td align="center" class="borde"><?= $row['c019'] ?></td>
              <td>Grado o etapa del cargo</td>
              <td colspan="2" align="center" class="borde"><?= $row['grado'] ?></td>
            </tr>
            <tr>
              <td class="borde">Otros </td>
              <td align="center" class="borde">080</td>
              <td align="center" class="borde"><?= $row['c080'] ?></td>
              <td>Codigo de cargo</td>
              <td colspan="2" align="center" style="border-right:1px solid #000;"><?= $row['codcargo'] ?></td>
            </tr>
            <tr>
              <td class="borde">Gastos de Represent.</td>
              <td align="center" class="borde">030</td>
              <td align="center" class="borde"><?= $row['c030'] ?></td>
              <td style="border-bottom:1px solid #000;">&nbsp;</td>
              <td colspan="2" style="border-bottom:1px solid #000; border-right:1px solid #000;">&nbsp;</td>
            </tr>
          </table></td>
        </tr>
        <?php } ?>
        
        
        <tr>
          <td>Observación:</td>
          <td style="border-bottom:1px solid #000;"><?= $row['observacion'] ?></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="borde centrado fondo">AUTORIZACIONES / APROBACIONES</td>
  </tr>
  <tr>
    <td class="borde"><table width="100%" border="0" cellspacing="10" cellpadding="0">
  <tr>
    <td width="33%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="borde centrado fondo">Firmas</td>
      </tr>
      <tr>
        <td class="centrado" height="80">
        <div style="border-top:1px solid #000; margin-top:30px;">Analista de Planillas</div>
        <div style="border-top:1px solid #000; margin-top:30px;">Jefe de Planillas</div>
        <div style="border-top:1px solid #000; margin-top:30px;">Fiscalización General</div>
        </td>
      </tr>
    </table></td>
    <td width="33%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="borde centrado fondo">Registro de presupuesto</td>
      </tr>
      <tr>
        <td class="borde centrado" height="90">SELLO</td>
      </tr>
    </table></td>
    <td width="33%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="borde centrado fondo">Fiscalización de planillas</td>
      </tr>
      <tr>
        <td class="borde centrado" height="90">SELLO</td>
      </tr>
    </table></td>
  </tr>
</table>
 </td>
  </tr>
</table>
</body>
</html>
<?php
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();
$dompdf->stream("reporte", array("Attachment" => false));
exit(0);