<?php
	session_start();
	ob_start();
	date_default_timezone_set('America/Panama');

	require_once('../../includes/dependencias.php');
	require_once('../../nomina/lib/database.php');
  require_once('../../nomina/tcpdf/tcpdf.php');  
 $departamento = isset($_REQUEST['departamento']) ? $_REQUEST['departamento'] : NULL ;

if ($departamento!="" && $departamento!=0 && $departamento!=NULL) {
 $comp = explode(",", $departamento);
 $i=0;
 if(count($comp)>1)
 {
   $fragmento_sql=" AND d.IdDepartamento IN(".$departamento.")";
 }
 else
 {
   $fragmento_sql=" AND d.IdDepartamento LIKE '%".$departamento."%'";

 }
}
          
  function obtenerNombreDepartamento($departamento) 
  {
    
    $db = new Database($_SESSION['bd']);
//    $idDepartamento=$_POST["departamento"];
    $resulDepar = $db->query("SELECT d.Descripcion FROM departamento AS d WHERE d.IdDepartamento=".$departamento."", $conexion);
    $filaD=mysqli_fetch_array($resulDepar);
    $nombreDepartamento = $filaD["Descripcion"];
    return $nombreDepartamento;
  }

  function obt_datos($departamento)
  {

            $db = new Database($_SESSION['bd']);
        //    $idDepartamento=$_POST["departamento"];            

                $datos = ''; 
                $sql = "SELECT p.nomposicion_id, p.nombres, p.nombres2, p.apellidos, p.apellido_materno, p.estado "
                        . "FROM nompersonal AS p "
                        . " WHERE p.estado NOT LIKE '%Baja%' AND p.IdDepartamento =".$departamento.""
                        . " ORDER BY p.nomposicion_id ASC";
                /*
                $sql = "SELECT p.nomposicion_id, p.nombres, p.nombres2, p.apellidos, p.apellido_materno, p.estado, d.* "
                        . "FROM departamento AS d "
                        . "INNER JOIN nompersonal AS p ON d.IdDepartamento=p.IdDepartamento"
                        . " WHERE p.estado NOT LIKE '%Baja%' AND d.IdDepartamento LIKE '%".$departamento."%' "
                        . " ORDER BY d.Descripcion ASC, p.nomposicion_id ASC";*/
//            echo $sql;
//            exit;
            $result1 = $db->query($sql, $conexion);

            while($fila=mysqli_fetch_array($result1)){
              $datos .= '
                     <tr style="line-height: 18px;">
                                         <td width="120"><strong>____________________</strong></td>
                       <td width="45.5">'.$fila["nomposicion_id"].' </td>
                       <td width="135">'.$fila["apellidos"].' '.$fila["apellido_materno"].'</td>
                       <td width="135">'.$fila["nombres"].' '.$fila["nombres2"].'</td>
                       <td width="120"><strong>____________________</strong></td>
                     </tr>
              ';
            }
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
      $this->Image($img_file, 14.5, 52, 184, 197, '', '', '', false, 300, '', false, false, 0);
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
  $obj_pdf->SetTitle("Listado de Remisoria de Pagos");  
  $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
  $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
  $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
  $obj_pdf->SetDefaultMonospacedFont('helvetica');  
  $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
  $obj_pdf->SetMargins('7', '7', '7');     
  $obj_pdf->SetAutoPageBreak(TRUE, 13);  
  $obj_pdf->SetFont('helvetica', '', 9);  
  $obj_pdf->AddPage();  
  $content = '';  
 
  
  foreach ($comp as $dept) 
  {
    
      $content .= '
  	<table cellpadding="5">
		<thead>
			<tr>
    			<th><img src="../../includes/assets/img/logo_selectra.png" alt="logo" height="70" width="70"></th>
    			<th colspan="4" align="center" style="font-size: 11pt;"><br><br><strong>REPUBLICA DE PANAMA</strong><br><strong>SERVICIO NACIONAL DE MIGRACION</strong><br><strong>UNIDAD DE RECURSOS HUMANOS</strong></th>
  			</tr>
        <tr>
          <th colspan="5" align="center" style="font-size: 11pt;"><strong>Listado de Remisoria de Pagos</strong><br></th>
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
    			<th colspan="5" style="font-size: 10pt;"><strong>
  ';
    $content .= obtenerNombreDepartamento($dept);
    $content .= '</strong><br></th>
                          </tr>
                          <tr>
                                  <td width="120">No. de Cheque</td>
          <td width="45.5">Posición</td>
                  <td width="135">Apellidos</td>
                  <td width="135">Nombres</td>
                  <td width="120">Firmas</td>
                          </tr>
                  </thead>
                  <tbody>
    ';
    $content .= obt_datos($dept);    
    $content .= '
                  </tbody>
          </table>
    ';  
    $content .='<p style="page-break-after: always;">&nbsp;</p>';
    $i++;
  }
   $obj_pdf->writeHTML($content);  
  ob_end_clean();
  $obj_pdf->Output('listadoRemisoria.pdf', 'I');
?>

