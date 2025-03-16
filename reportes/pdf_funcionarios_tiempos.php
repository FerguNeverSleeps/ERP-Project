<?php 
	session_start();
	ob_start();
	date_default_timezone_set('America/Panama');

	require_once('../../includes/dependencias.php');
	require_once('../../nomina/lib/database.php');
  	require_once('../../nomina/tcpdf/tcpdf.php'); 

  	function obt_datos(){
    $datos = '';
    $db = new Database($_SESSION['bd']);
    $sql = "SELECT np.cedula, np.nombres, np.nombres2, np.apellidos, np.apellido_materno FROM nompersonal AS np WHERE np.estado != 'De Baja' ORDER BY np.nombres LIMIT 200";

    $result = $db->query($sql, $conexion) or die(mysqli_error($conexion));

    while($row = mysqli_fetch_array($result)){
      $datos .='
        <tr>
          <td width="195">'.$row["nombres"].' '.$row["nombres2"].' '.$row["apellidos"].' '.$row["apellido_materno"].'</td>
          <td width="343.5">
            <table border="0.3" cellpadding="2"> 
      ';
      $datos .= obtenerTiempoFuncionario($row["cedula"]);
      $datos .= '
            </table>
          </td>
        </tr>';
    }
    return $datos;
  }

  function obtenerTiempoFuncionario($cedula){
    $datos = '';
    $db = new Database($_SESSION['bd']);
    $sql2 = "SELECT di.tipo_justificacion, SUM(di.tiempo) AS stiempo, SUM(di.dias) AS sdias, SUM(di.horas) AS shoras, SUM(di.minutos) AS sminutos, tj.descripcion FROM dias_incapacidad AS di INNER JOIN tipo_justificacion AS tj ON di.tipo_justificacion = tj.idtipo WHERE di.cedula = '$cedula' GROUP BY di.tipo_justificacion";

    $resultDi = $db->query($sql2, $conexion) or die(mysqli_error($conexion));

      while ($row2 = mysqli_fetch_array($resultDi)) {
        $datos .= '
          <tr>
            <td width="173">'.$row2["descripcion"].'</td>
            <td width="40">'.number_format($row2["stiempo"], 2).'</td>
            <td width="40">'.$row2["sdias"].'</td>
            <td width="40">'.$row2["shoras"].'</td>
            <td width="40">'.$row2["sminutos"].'</td>
          </tr>
        ';
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
  $obj_pdf->SetMargins('10', '10', '10');  
  $obj_pdf->setPrintHeader(false);  
  $obj_pdf->setPrintFooter(false);  
  $obj_pdf->SetAutoPageBreak(TRUE, 16);  
  $obj_pdf->SetFont('helvetica', '', 6);  
  $obj_pdf->AddPage();  
  $content = '';
  $content .= '   
    <table border="0.3" cellpadding="2">
      <thead>
        <tr>
          <th colspan="2" style="font-size: 8pt;" bgcolor="#FFFF00"><strong>Tiempo Disponible por Funcionario</strong></th>
        </tr>
        <tr>
          <th width="195" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Nombre</strong></th> 
          <th width="343.5" bgcolor="#7698D4" style="color:#FFFFFF;"><strong>Tiempo</strong></th>
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