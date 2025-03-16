<?php
	session_start();
	ob_start();
	date_default_timezone_set('America/Panama');

	require_once('../../includes/dependencias.php');
	require_once('../../nomina/lib/database.php');
  require_once('../../nomina/tcpdf/tcpdf.php');  

  $tiempo_total = array('notfiReincorporacion' => 0, 'tPersonal' => 0, 'tCompensatorio' => 0, 
                        'mOficial' => 0, 'incap' => 0, 'incapDiscap' => 0, 'vacaciones' => 0, 
                        'incapFamiliar' => 0);

  function obtenerNombreDepartamento() {
    
    $db = new Database($_SESSION['bd']);
    $idDepartamento=$_POST['departamento'];
    $resulDepar = $db->query("SELECT d.Descripcion FROM departamento AS d WHERE d.IdDepartamento='$idDepartamento'", $conexion);
    $filaD=mysqli_fetch_array($resulDepar);
    return $filaD['Descripcion'];
  }

	function obt_datos(&$tiempo_total){

    $db = new Database($_SESSION['bd']);
    $idDepartamento = $_POST['departamento'];
    $datos = ''; 
  	$sql = "SELECT np.cedula, np.nomposicion_id, np.nombres, np.nombres2, np.apellidos, np.apellido_materno FROM nompersonal AS np WHERE np.IdDepartamento = '$idDepartamento' AND np.estado != 'De Baja' ORDER BY np.nomposicion_id";

    $result1 = $db->query($sql, $conexion);

    while($fila = mysqli_fetch_array($result1)){
      
      $datos .= '
	     <tr>
        <td width="47">'.$fila["cedula"].'</td>
	      <td width="35">'.$fila["nomposicion_id"].' </td>
        <td width="112.5">'.$fila["apellidos"].' '.$fila["apellido_materno"].'</td>
        <td width="112.5">'.$fila["nombres"].' '.$fila["nombres2"].'</td>
	    ';
      $datos .= obtenerTiempoFuncionario($fila["cedula"], $tiempo_total);
      $datos .= '
	     </tr>
      ';
    }
    return $datos;
  }

  function obtenerTiempoFuncionario($cedula, &$tiempo_total)
  {
    $datos = '';
    $db = new Database($_SESSION['bd']);
    $sql2 = "SELECT di.tipo_justificacion, SUM(di.tiempo) AS stiempo FROM dias_incapacidad AS di WHERE di.cedula = '$cedula' GROUP BY di.tipo_justificacion";

    $resultDi = $db->query($sql2, $conexion);

    $notfiReincorporacion = 0;
    $tPersonal = 0;
    $tCompensatorio = 0;
    $mOficial = 0;
    $incap = 0;
    $incapDiscap = 0;
    $vacaciones = 0;
    $incapFamiliar = 0;

    while ($row2 = mysqli_fetch_array($resultDi)) {

      switch ($row2["tipo_justificacion"]) {
        case '1':
            $notfiReincorporacion = number_format($row2["stiempo"], 2);
            $tiempo_total["notfiReincorporacion"] += $notfiReincorporacion;
            break;
          case '2':
            $tPersonal = number_format($row2["stiempo"], 2);
            $tiempo_total["tPersonal"] += $tPersonal;
            break;
          case '3':
            $tCompensatorio = number_format($row2["stiempo"], 2);
            $tiempo_total["tCompensatorio"] += $tCompensatorio;
            break;
          case '4':
            $mOficial = number_format($row2["stiempo"], 2);
            $tiempo_total["mOficial"] += $mOficial;
            break;
          case '5':
            $incap = number_format($row2["stiempo"], 2);
            $tiempo_total["incap"] += $incap;
            break;
          case '6':
            $incapDiscap = number_format($row2["stiempo"], 2);
            $tiempo_total["incapDiscap"] += $incapDiscap;
            break;
          case '7':
            $vacaciones = $row2["stiempo"];
            $tiempo_total["vacaciones"] += $row2["stiempo"];
            break;
          case '8':
            $incapFamiliar = number_format($row2["stiempo"], 2);
            $tiempo_total["incapFamiliar"] += $incapFamiliar;
            break;
      }
    }
      $datos .= '
        <td width="60">'.$notfiReincorporacion.' Horas</td>
        <td width="60">'.$tPersonal.' Horas</td>
        <td width="60">'.$tCompensatorio.' Horas</td>
        <td width="60">'.$mOficial.' Horas</td>
        <td width="60">'.$incap.' Horas</td>
        <td width="60">'.$incapDiscap.' Horas</td>
        <td width="60">'.$vacaciones.' Días</td>
        <td width="75">'.$incapFamiliar.' Horas</td>
      ';
    return $datos;
  }

  class MYPDF extends TCPDF {

    public function Header() {
      // Get the current page break margin
      $bMargin = $this->getBreakMargin();
      // Get current auto-page-break mode
      $auto_page_break = $this->AutoPageBreak;
      // Disable auto-page-break
      $this->SetAutoPageBreak(false, 0);
      // Define the path to the image that you want to use as watermark.
      $img_file = '../../includes/assets/img/marca_agua.jpg';
      // Render the image
      $this->Image($img_file, 86, 40, 135, 148, '', '', '', false, 300, '', false, false, 0);
      // Restore the auto-page-break status
      $this->SetAutoPageBreak($auto_page_break, $bMargin);
      // Set the starting point for the page content
      $this->setPageMark();
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 9);
        // Page number
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }
}

  $obj_pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $obj_pdf->SetCreator(PDF_CREATOR);  
  $obj_pdf->SetTitle("Reporte de Tiempos por Departamento");  
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
  $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
  $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
  $obj_pdf->SetDefaultMonospacedFont('helvetica');  
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
  $obj_pdf->SetMargins('7', '7', '7');     
  $obj_pdf->SetAutoPageBreak(TRUE, 19);  
  $obj_pdf->SetFont('helvetica', '', 7);  
  $obj_pdf->AddPage("L");  
  $content = '';  
  $content .= '
  	<table cellpadding="2">
		<thead>
			<tr>
    			<th><img src="../../includes/assets/img/logo_selectra.png" alt="logo" height="70" width="70"></th>
    			<th colspan="4" align="center" style="font-size: 9pt;"><br><br><strong>REPUBLICA DE PANAMA</strong><br><strong>SERVICIO NACIONAL DE MIGRACION</strong><br><strong>UNIDAD DE RECURSOS HUMANOS</strong></th>
  			</tr>
        <tr>
          <th colspan="5" align="center" style="font-size: 9pt;"><strong>Reporte de Tiempos por Departamento</strong><br></th>
        </tr>
  			<tr>
    			<td colspan="4" align="right">Fecha de</td>
    			<td colspan="1" align="left">
  ';

  $content .= date("d/m/Y g:i A");
  $content .='
  	      <br><br></td>
  			</tr>
  			<tr>
    			<th colspan="5" style="font-size: 8pt;"><strong>
  ';
  $content .= obtenerNombreDepartamento();
  $content .= '</strong><br></th>
  			</tr>
			<tr>
				<td width="47">Cédula</td>
        <td width="35">Posición</td>
    		<td width="112.5">Apellidos</td>
    		<td width="112.5">Nombres</td>
    		<td width="60">Notificación de Reincorporacion</td>
        <td width="60">Tiempo Personal</td>
        <td width="60">Tiempo Compensatorio</td>
        <td width="60">Mision Oficial</td>
        <td width="60">Incapacidad</td>
        <td width="60">Incapacidad por Discapacidad</td>
        <td width="60">Vacaciones</td>
        <td width="75">Incapacidad por Familiar Discapacitado</td>
			</tr>
		</thead>
		<tbody>
  ';
  $content .= obt_datos($tiempo_total);    
  $content .= '<tr>
          <td colspan="4" align="right"><strong>Total de Tiempos por Departamento</strong></td>
          <td width="60"><strong>'.$tiempo_total["notfiReincorporacion"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["tPersonal"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["tCompensatorio"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["mOficial"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["incap"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["incapDiscap"].' Horas</strong></td>
          <td width="60"><strong>'.$tiempo_total["vacaciones"].' Días</strong></td>
          <td width="75"><strong>'.$tiempo_total["incapFamiliar"].' Horas</strong></td>
        </tr>
  		</tbody>
  	</table>
  ';  
  $obj_pdf->writeHTML($content);  
  ob_end_clean();
  $obj_pdf->Output('repTiemposDepartamentos.pdf', 'I');
?>