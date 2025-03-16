<?php
  session_start();
  ob_start();
  date_default_timezone_set('America/Panama');
  error_reporting(E_ALL & ~E_NOTICE);
  ini_set('display_errors', 0);
  ini_set('log_errors', 1);

  require_once('../../nomina/lib/database.php');
  include("../../includes/dependencias.php");
  require_once('../../nomina/tcpdf/tcpdf.php');  

  function obt_datos(){
    $db = new Database($_SESSION['bd']);
    $datos = '';
    $db->query("SET lc_time_names = 'es_PA'", $conexion);
    $sql = "SELECT a.*, b.Descripcion, b.IdPlanilla, c.cod_car, c.des_car, d.descripcion_funcion, DATE_FORMAT(a.fecing, '%d/%b/%Y') AS fecIng, f.nro_resolucion, DATE_FORMAT(f.fecha_resolucion, '%d/%b/%Y') AS fechaResolucion, DATE_FORMAT(f.fecha_notificacion, '%d/%b/%Y') AS fechaNotificacion FROM nompersonal AS a INNER JOIN departamento AS b ON a.IdDepartamento=b.IdDepartamento INNER JOIN nomcargos AS c ON a.codcargo=c.cod_car INNER JOIN nomfuncion d ON a.nomfuncion_id=d.nomfuncion_id INNER JOIN  destituciones AS f ON a.useruid=f.usr_uid WHERE a.estado = 'De Baja'";
    $result1 = $db->query($sql, $conexion);

    while($fila=mysqli_fetch_array($result1)){
      $datos .= '<tr>
          <td width="63" align="center">'.$fila["ctacontab"].'</td>
          <td width="25" align="center">'.$fila["IdPlanilla"].'</td>
          <td width="230">'.$fila["Descripcion"].'</td>
          <td width="30" align="center">'.$fila["nomposicion_id"].'</td>
          <td width="45">'.$fila["apellidos"].'</td>
          <td width="45">'.$fila["nombres"].'</td>
          <td width="35">'.$fila["cedula"].'</td>
          <td width="90">'.$fila["des_car"].'</td>
          <td width="106">'.$fila["descripcion_funcion"].'</td>
          <td width="36" align="center">'.$fila["fecIng"].'</td>
          <td width="36">'.$fila["nro_resolucion"].'</td>
          <td width="36" align="center">'.$fila["fechaResolucion"].'</td>
          <td width="36" align="center">'.$fila["fechaNotificacion"].'</td>
        </tr>';
    }
    return $datos;
  }

  $obj_pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $obj_pdf->SetCreator(PDF_CREATOR);  
  $obj_pdf->SetTitle("Listado de Funcionarios Destituidos");  
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
  $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
  $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
  $obj_pdf->SetDefaultMonospacedFont('helvetica');  
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
  $obj_pdf->SetMargins('5', '5', '5');  
  $obj_pdf->setPrintHeader(false);  
  $obj_pdf->setPrintFooter(false);  
  $obj_pdf->SetAutoPageBreak(TRUE, 16);  
  $obj_pdf->SetFont('helvetica', '', 5.5);  
  $obj_pdf->AddPage('L','A4');  
  $content = '';  
  $content .= '   
    <table border="0.3" cellpadding="2">
    <thead>
      <tr>
        <th width="813" colspan="13" style="font-size: 7.5pt;" bgcolor="#FFFF00"><strong>Listado de Funcionarios Destituidos</strong></th>
      </tr>
      <tr>
        <th width="63" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cuenta Contable</strong></th> 
        <th width="25" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Planilla</strong></th>
        <th width="230" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Departamento</strong></th>
        <th width="30" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Posición</strong></th>
        <th width="45" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Apellidos</strong></th>
        <th width="45" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Nombres</strong></th>
        <th width="35" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cédula</strong></th>
        <th width="90" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cargo</strong></th>
        <th width="106" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Función</strong></th>
        <th width="36" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Fecha Inicio</strong></th>
        <th width="36" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>No. Resolución</strong></th>
        <th width="36" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>F. Resolución</strong></th>
        <th width="36" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>F. Notificación</strong></th>
      </tr>
    </thead>
    <tbody>
  ';
  $content .= obt_datos();    
  $content .= '
      </tbody>
    </table>
  ';  
  $obj_pdf->writeHTML($content);  
  ob_end_clean();
  $obj_pdf->Output('repDestituidos.pdf', 'I'); 
?>
