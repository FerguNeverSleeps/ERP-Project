<?php
session_start();
ob_start();
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
/*
require_once('../tcpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';
*/
require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $cedula, $fechafinal, $fecharegistro, $quincena, $mes, $año, $id;
	//Page header
	public function Header() {
		// Logo
		/*$bd=$_SESSION['bd'];

		$Conn              =conexion();
		$var_sql           ="select * from nomempresa";
		$rs                = query($var_sql,$Conn);
		$row_rs            = fetch_array($rs);
		$var_encabezado1   =$row_rs['nom_emp'];
		$var_izquierda     ='../../imagenes/'.$row_rs[imagen_der];
		$var_derecha       ='../../imagenes/'.$row_rs[imagen_izq];
		$fill              = 1;
		$border            ="LTRB";
		$ln                =0;
		$fill              = 0;
		$align             ='C';
		$link              =0;
		$stretch           =1;
		$ignore_min_height =0;
		$calign            ='T';
		$valign            ='T';
		$height            =5.75;//alto de cada columna
		$YMax              =260;
		$altura            =21;
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 8);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$membrete="<b>REPÚBLICA DE PANAMÁ<br>
					CONTRALORÍA GENERAL DE LA REPÚBLICA<br>
					DIRECCIÓN GENERAL DE FISCALIZACIÓN<br>
					ACTIVACIÓN DE BAJA</b>";
        $this->Image($var_izquierda,11,11,19,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(195, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=245,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->Ln();*/
		// Set font
		// Page number
		
	}

    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
	  //$buena= $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha

		$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
	  }
	}
	public function CuerpoTablaHtml()
	{
		$conexion = new bd($_SESSION['bd']);
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=14;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=14;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}

		$fill              = 1;
		$border            =0;
		$ln                =0;
		$fill              = 0;
		$align             ='C';
		$link              =0;
		$stretch           =1;
		$ignore_min_height =0;
		$calign            ='T';
		$valign            ='m';
		$height            =5.75;//alto de cada columna
		$YMax              =260;
		$altura            =7;
		$sql2        = "SELECT per_vac.*,
						date_format(per_vac.fini_periodo,'%d/%b/%Y') ini_periodo,
						date_format(per_vac.ffin_periodo,'%d/%b/%Y') fin_periodo,
						date_format(per_vac.fecha_resolucion,'%d/%b/%Y') fec_resolucion,
						date_format(per_vac.vac_desde,'%d/%b/%Y') vaca_desde,
						date_format(per_vac.vac_hasta,'%d/%b/%Y') vaca_hasta,
						np.apenom,np.nombres,np.apellidos,np.apellido_materno,np.nombres2
						FROM periodos_vacaciones as per_vac 
						LEFT JOIN nompersonal as np on (np.cedula = per_vac.cedula)
						WHERE np.cedula = '{$this->cedula}'
						ORDER BY per_vac.fini_periodo ASC, per_vac.vac_desde ASC, per_vac.saldo DESC";

		$this->SetFont('helvetica', 'B', 7);
		$this->SetTextColor(0);
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(100, 100, 100);
		$w = array(25, 15,13,13,10,22,30,60,60,30);

		$sql         = "SELECT np.apenom, np.cedula, np.nombres, np.apellidos,dep.Descripcion as departamento"
                            . " FROM nompersonal as np "
                            . " LEFT JOIN departamento as dep on (np.IdDepartamento = dep.IdDepartamento)"
                            . "where cedula = '{$this->cedula}'";
		$result      = $conexion->query($sql, $conexion);
		$nombre_titulo = $result->fetch_array();
		$this->Cell(200,8,"VACACIONES REGISTRADAS ", 0, 0, 'L', 1); 	 
		$this->Ln();
		$this->Cell(200,8,"FUNCIONARIO: ".utf8_encode($nombre_titulo['apenom'])." CEDULA: ".$nombre_titulo['cedula'], 0, 1, 'L', 1); 	 
		$this->Cell(200,8,"DEPARTAMENTO: ".utf8_encode($nombre_titulo['departamento']), 0, 0, 'L', 1); 
                $this->Ln();
		$this->Ln();
        $this->SetFont('helvetica', 'B', 8);


		$cabecera = array('Nombres', 'Apellidos' , 'F. Inicio','F.Fin','No. Resol.','F. Resol.','Tipo','Vac. Desde','Vac. Hasta','Asignado','Días','Saldo','Observacion');
    	$cuerpo=array($fecha,$turno);
       	$w = array(30, 30,20,20,20,20,20,20,20,15,10,10,40);
  	  	$num_headers = count($cabecera);

		for($i = 0; $i < $num_headers; ++$i) {
			$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C', 1);

		}
		$this->Ln();
		$this->SetFont('helvetica', '', 7);
		$res1=$conexion->query($sql2);
                $saldo_actual=0;
                while($fila = mysqli_fetch_array($res1))

                {
                    $tipo="Periodo";
                    if($fila['tipo']==2)
                    {
                        $tipo="Solicitud";
                    }
                    if($fila['tipo']==3 || $fila['tipo']==4)
                    {
                        $tipo="Ajuste";
                    }
                    //$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

                                    $this->SetFillColor(255, 255, 255);
                                    $this->SetTextColor(0,0,0);
                                    $this->SetDrawColor(100, 100, 100);
                                    $this->SetLineWidth(0.3);
                                    // Header
                                    $num_headers = count($header);			

                                    if(	$this->GetY() >172)
                                    {
                                            $this->AddPage();
                                    }

                                    $border=1;
                                    $ixx=0;

                                    $this->Multicell(30,$altura,utf8_encode($fila['nombres']." ".$fila['nombres2']),"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(30,$altura,utf8_encode($fila['apellidos']." ".$fila['apellido_materno']),"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,$fila['ini_periodo'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,$fila['fin_periodo'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,substr($fila['no_resolucion'], 0,10),"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,$fila['fec_resolucion'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,$tipo,"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);
                                    
                                    $this->Multicell(20,$altura,$fila['vaca_desde'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(20,$altura,$fila['vaca_hasta'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(15,$altura,$fila['asignados'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(10,$altura,$fila['dias'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(10,$altura,$fila['saldo'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                    $this->Multicell(40,$altura,$fila['observacion'] ,"TBLR",$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

                                $this->Ln();
                        $saldo_actual+= $fila['saldo'] ;  
                     }//fin del while
         
           
//            $consulta_periodos= " SELECT DISTINCT(fini_periodo) as ini_periodo, ffin_periodo as fin_periodo"
//                    . " FROM periodos_vacaciones WHERE cedula='$this->cedula'";
//            $resultado_periodos=$conexion->query($consulta_periodos);		
//
//	    
//            while ($registros = mysqli_fetch_array($resultado_periodos)) 
//            {
//                $ini_periodo = $registros[ini_periodo];
//                $fin_periodo = $registros[fin_periodo];
//                $consulta_saldo= " SELECT saldo, vac_desde "
//                                ." FROM periodos_vacaciones"
//                                ." WHERE cedula='$this->cedula'"
//                                ." AND fini_periodo='".$ini_periodo."'"
//                                ." AND vac_desde=(SELECT MAX(vac_desde) FROM periodos_vacaciones WHERE cedula='$this->cedula' AND fini_periodo='".$ini_periodo."')"
//                                ."";                
//                $resultado_saldo=$conexion->query($consulta_saldo);	
//                $fetch_saldo = mysqli_fetch_array($resultado_saldo);        
//                $vac_desde = $fetch_saldo[vac_desde];
//
//                if($vac_desde=='' || $vac_desde==0 || $vac_desde==NULL || $vac_desde=='0000-00-00')
//                {
//                    $consulta_saldo= " SELECT saldo, vac_desde "
//                                ." FROM periodos_vacaciones"
//                                ." WHERE cedula='$this->cedula'"
//                                ." AND fini_periodo='".$ini_periodo."'";
//                    $resultado_saldo=$conexion->query($consulta_saldo);	
//                    $fetch_saldo = mysqli_fetch_array($resultado_saldo);       
//                }
//                $saldo = $fetch_saldo[saldo];
//                $saldo_actual+=$saldo;
//            }
             $this->Multicell(30,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(30,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);
            
            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(20,$altura,'',"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(25,$altura,"TOTAL DISPONIBLE","TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            //$this->Multicell(10,$altura,$fila['dias'],"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(10,$altura,$saldo_actual,"TBLR",$alineacion='C',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=true,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

            $this->Multicell(40,$altura,'',"TBLR",$alineacion='L',$fondo=false,$salto_de_linea=0,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=0,$maxh=50,$alineacion_vertical='T', $fitcell=false);

	}
	public function Footer() {
		// Logo
		//$this->SetY(-40);

		// Set font
		// Page number
		
	}
}//fin clase
		
		
		

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->cedula=$_REQUEST['cedula'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Amaxonia');
$pdf->SetTitle('Vacaciones por funcionario');
$pdf->SetSubject('Reporte Vacaciones por funcionario');
$pdf->SetKeywords('TCPDF, PDF, reporte, Vacaciones, funcionario');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10,true);
$pdf->SetLeftMargin(10);
$pdf->SetHeaderMargin(10);
$pdf->SetRightMargin(10);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15	);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('L', 'A4');
$pdf->cedula=$_GET['cedula'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml();
ob_clean();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('pdf_vacaciones_por_funcionario.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();