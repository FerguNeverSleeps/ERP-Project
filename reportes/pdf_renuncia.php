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
    $sql = "SELECT a.*,b.Descripcion, b.IdPlanilla, c.cod_car, c.des_car, d.descripcion_funcion, DATE_FORMAT(a.fecing, '%d/%b/%Y') AS fecIng, DATE_FORMAT(f.fecha, '%d/%b/%Y') AS fechaRenuncia FROM nompersonal AS a INNER JOIN departamento AS b ON a.IdDepartamento=b.IdDepartamento INNER JOIN nomcargos AS c ON a.codcargo=c.cod_car INNER JOIN nomfuncion AS d ON a.nomfuncion_id=d.nomfuncion_id INNER JOIN  renuncia AS f ON a.useruid=f.usr_uid WHERE a.estado = 'De Baja'";
    $result1 = $db->query($sql, $conexion);

    while($fila=mysqli_fetch_array($result1)){
      $datos .= '<tr>
          <td width="70" align="center">'.$fila["ctacontab"].'</td>
          <td width="30" align="center">'.$fila["IdPlanilla"].'</td>
          <td width="255">'.$fila["Descripcion"].'</td>
          <td width="30" align="center">'.$fila["nomposicion_id"].'</td>
          <td width="50">'.$fila["apellidos"].'</td>
          <td width="50">'.$fila["nombres"].'</td>
          <td width="37">'.$fila["cedula"].'</td>
          <td width="100">'.$fila["des_car"].'</td>
          <td width="111">'.$fila["descripcion_funcion"].'</td>
          <td width="40" align="center">'.$fila["fecIng"].'</td>
          <td width="40" align="center">'.$fila["fechaRenuncia"].'</td>
        </tr>';
    }
    return $datos;
  }

  $obj_pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $obj_pdf->SetCreator(PDF_CREATOR);  
  $obj_pdf->SetTitle("Listado de Renuncias");  
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
  $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
  $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
  $obj_pdf->SetDefaultMonospacedFont('helvetica');  
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
  $obj_pdf->SetMargins('5', '5', '5');  
  $obj_pdf->setPrintHeader(false);  
  $obj_pdf->setPrintFooter(false);  
  $obj_pdf->SetAutoPageBreak(TRUE, 16);  
  $obj_pdf->SetFont('helvetica', '', 6);  
  $obj_pdf->AddPage('L', 'A4');  
  $content = '';
  $content .= '   
    <table border="0.3" cellpadding="2">
      <thead>
        <tr>
          <th width="813" colspan="13" style="font-size: 8pt;" bgcolor="#FFFF00"><strong>Listado de Renuncias</strong></th>
        </tr>
        <tr>
          <th width="70" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cuenta Contable</strong></th> 
          <th width="30" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Planilla</strong></th>
          <th width="255" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Departamento</strong></th>
          <th width="30" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Posición</strong></th>
          <th width="50" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Apellidos</strong></th>
          <th width="50" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Nombres</strong></th>
          <th width="37" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cédula</strong></th>
          <th width="100" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Cargo</strong></th>
          <th width="111" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Función</strong></th>
          <th width="40" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Fecha Inicio</strong></th>
          <th width="40" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Fecha Fin</strong></th>
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
  $obj_pdf->Output('repRenuncias.pdf', 'I');
?>
