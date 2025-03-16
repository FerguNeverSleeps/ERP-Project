<?php 
error_reporting(1);
session_start();
ob_start();
$termino=$_SESSION['termino'];
    
include ("/var/www/html/amaxonia_planilla/nomina/header.php");
include ("/var/www/html/amaxonia_planilla/nomina/paginas/func_bd.php");
include ("/var/www/html/amaxonia_planilla/nomina/lib/common.php");
require("PHPMailer_5.2.4/class.phpmailer.php");
include("PHPMailer_5.2.4/class.smtp.php");
$mail = new PHPMailer();
$mail->IsSMTP();
$db_conf=new bd(SELECTRA_CONF_PYME);
$res_db_conf = $db_conf->query("SELECT * from nomempresa where codigo in ('22');");
$bd_planilla = $res_db_conf->fetch_assoc();
$db=new bd($bd_planilla['bd_nomina']);
$estatus_anulado = 3;
$sql= "SELECT * from nomempresa;";
$res1=$db->query($sql);
$nomempresa = $res1->fetch_assoc();

$fechaInicio = date('d/m/Y h:i:s a');

$fechaFin = date('d/m/Y h:i:s a');


$consulta = "SELECT
    np.ficha,
    np.cedula,
    np.apenom,
    nt.tolerancia_entrada,
    nt.tolerancia_salida,
    nt.tolerancia_llegada,
    nt.tolerancia_descanso,
    nt.entrada,
    nt.salida,
    nt.descripcion,
    rd.entrada entrada_rd,
    rd.salida salida_rd,
    COALESCE ( (SUBTIME(rd.entrada, nt.tolerancia_entrada)), '-' ) tardanza,
    IFNULL( rd.tardanza, '8' ) ausencia 
FROM
    nomcalendarios_personal ncp
    LEFT JOIN nompersonal np ON ncp.ficha = np.ficha
    LEFT JOIN nomturnos nt ON nt.turno_id = np.turno_id
    LEFT JOIN reloj_detalle rd ON ncp.fecha = rd.fecha 
    AND rd.ficha = np.ficha 
WHERE
    ncp.fecha = CAST( NOW( ) AS DATE ) 
    AND nt.turno_id != '11' 
    AND (   ! ( NOW( ) BETWEEN CAST( rd.entrada AS DATETIME ) AND CAST( nt.tolerancia_entrada AS DATETIME ) ) 
    OR ! ( NOW( ) BETWEEN CAST( rd.salida AS DATETIME ) AND CAST( nt.tolerancia_salida AS DATETIME ) )  )
HAVING
        tardanza != '00:00' AND (time_to_sec(tardanza) >= 0 or ausencia = '8');";
$resultAusTard = $db->query($consulta);

$html = "";
$html = '<table   style="font-size:11px">
        <thead>
        <tr>
            <th>
                Ficha
            </th>
            <th>
                Nombres
            </th>
            <th>
                Entrada
            </th>
            <th>
                Tardanza
            </th>
            <th>
                Ausencia
            </th>

        </tr>
        </thead>
        <tbody>';
            while($fila = $resultAusTard->fetch_assoc()){ 
            $fecha_entrada1 = new Datetime($fila['entrada']);
            $fecha_entrada_rd1 = new Datetime($fila['entrada_rd']);
            $fecha_tolerancia_entrada1 = new Datetime($fila['tolerancia_entrada']);
            $intervalo = $fecha_tolerancia_entrada1->diff($fecha_entrada_rd1);

            
            $html .= '<tr>';
            $html .= '<td>';
            $html .=  utf8_encode($fila['ficha']);
            $html .= '</td>';
            $html .= '<td>';
            $html .=  utf8_encode($fila['apenom']);
            $html .= '</td>';
            $html .= '<td>';
            $html .= utf8_encode($fila['entrada_rd']);
            $html .= '</td>';
            $html .= '<td>';
            $html .= utf8_encode($fila['tardanza']);
            $html .= '</td>';
            $html .= '<td>';
            $html .= utf8_encode($fila['ausencia']);
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .='</table>';

$subject = "Notificacion de Ausencias y Tardanzas";
$msgHTML = "
Resumen:<br>
{$html}
";
if ($resultAusTard-> num_rows > 0 ) {
    // enviar email a administrador las facturas procesadas con errores y sin errores.
    $empresa = $nomempresa["nombre_empresa"];
    $host_correo = $nomempresa['host_correo'];
    $smtp_correo = $nomempresa['smtp_correo'];
    $puerto_correo = $nomempresa['puerto_correo'];
    $correo_sistemas = $nomempresa['correo_sistemas'];
    $correo_sistemas_password = $nomempresa['correo_sistemas_password'];
    $facturas_correo1 = $nomempresa['correo_adicional4'];

    if ($facturas_correo1) {
        $mail = new PHPMailer();
        $mail->SMTPAuth = true;
        $mail->isSMTP();
        $mail->SMTPDebug = 1;
        $mail->Host = ($host_correo != '') ? $host_correo : 'smtp.gmail.com';
        $mail->SMTPSecure = ($smtp_correo != '') ? $smtp_correo : 'ssl';
        $mail->Port = ($puerto_correo != '') ? $puerto_correo : '465';

        if ($correo_sistemas and $correo_sistemas_password) {
            $mail->Username = $correo_sistemas;
            $mail->Password = $correo_sistemas_password;
            $mail->SetFrom($correo_sistemas, $empresa . ' - NOTIFICACIONES');
        } else {
            $mail->Username = "planillaexpresspanama@gmail.com";
            $mail->Password = 'wxjknutzwjuqbvop';
            $mail->SetFrom('planillaexpresspanama@gmail.com', $empresa . ' - NOTIFICACIONES');
        }

//        $mail->addAddress("hiram_loreto@yahoo.com");

        $mail->addAddress($facturas_correo1);
        $mail->Subject = utf8_decode($subject);
        $mail->msgHTML(utf8_decode($msgHTML));
        $mail->send();
    }
}
//echo "<br/>";
//echo "Archivos creados exitosamente";






?>
