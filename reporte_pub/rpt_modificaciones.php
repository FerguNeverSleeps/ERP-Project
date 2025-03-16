<?php
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

require_once('../nomina/tcpdf/tcpdf.php');
require_once '../nomina/lib/config.php';
require_once '../nomina/lib/common.php';


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF 
{
	public $fechainicio, $fechafinal, $fecharegistro, $quincena, $mes, $año, $idp, $id,$tipo;
	//Page header
	public function Header() 
	{
		// Logo
		$Conn            =conexion();
		$var_sql         ="select * from nomempresa";
		$rs              = query($var_sql,$Conn);
		$row_rs          = fetch_array($rs);
		$var_encabezado1 =$row_rs['nom_emp'];
		$var_izquierda   ='../nomina/imagenes/'.$row_rs[imagen_der];
		$var_derecha     ='../nomina/imagenes/'.$row_rs[imagen_izq];
		
		$image_file      = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 8);
		$fecha = date("d")."-".date("m")."-".date("Y");
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
		$membrete="<b>REPÚBLICA DE PANAMÁ<br>
					CONTRALORÍA GENERAL DE LA REPÚBLICA<br>
					DIRECCIÓN GENERAL DE FISCALIZACIÓN<br>
					MODIFICACIONES O CAMBIOS DE CEDULA</b>";
        $this->Image($var_izquierda,11,11,19,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(195, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=245,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		$this->Ln();
		// Set font
		// Page number
		
	}

    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha = explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
		//$buena    = $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha
		
		$buena      = $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
		}
	}
	public function CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro)
	{
		$conexion=conexion();
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=44;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=40;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}
		
		$fill              = 1;
		$border            =1;
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
		$altura            =7;
		$sql1              = " 
			SELECT DISTINCT *
			FROM mov_contraloria as A, mov_modificaciones AS B WHERE A.id_mov_contraloria = B.id_mov_contraloria
			AND A.id_mov_contraloria=".$this->id." AND A.personal_id=".$this->idp;

		$res1            =query($sql1, $conexion);
		$row_mov         =fetch_array($res1);
		$quincena        =$row_mov['quincena'];
		$var_encabezado1 =$row_rs['nom_emp'];
		$sql2     = " SELECT DISTINCT *
		FROM parametro_inclusion  ";
		
		$res2     =query($sql2, $conexion);
		$cabecera = array('Decreto/Resuelto', 'Fecha' , 'Posición','Planilla','*T/I','Cédula Anterior','Cédula Nueva','Seguro Social','Sexo','CR. I/R','Nombres y Apellidos','Sueldo','T/S','Sobresueldo','Observación');
		$cuerpo   =array($fecha,$turno);
		
		$row      =fetch_array($res2);
		$this->SetFont('helvetica', 'B', 8);
		$this->SetTextColor(0);
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(100, 100, 100);
		$num_headers = count($cabecera);
		$w           = array(25, 15,13,13,10,22,22,20,10,13,30,15,20,20);
		$this->Cell(20,8,  "", 0, 0, 'C', 1);
		$this->Cell(15,8,  "Área", 1, 0, 'C', 1);
		$this->Cell(15,8,  "Entidad", 1, 0, 'C', 1);
		$this->Cell(100,8,  "Nombre de la Entidad:", 1, 0, 'C', 1);
		$this->Cell(40,8,  "", 0, 0, 'C', 1);
		$this->Cell(15,8,  "1ra", 1, 0, 'C', 1);
		$this->Cell(15,8,  "2da", 1, 0, 'C', 1);
		$this->Cell(15,8,  "Mes", 1, 0, 'C', 1);
		$this->Cell(15,8,  "Año:", 1, 0, 'C', 1);
		$this->Ln();
        $this->SetFont('helvetica', '', 8);
        if($quincena==1)
        {
			$quincena1 ="X";
			$quincena2 ="";
        }
        else
        {
			$quincena1 ="";
			$quincena2 ="X";
        }
		$this->Cell(20,8,  "Ministerio:", 0, 0, 'C', 1); 
		$this->Cell(15,8,  $row[area], 1, 0, 'C', 1); 
		$this->Cell(15,8,  $row[ministerio], 1, 0, 'C', 1); 
		$this->Cell(100,8,  $row[nombre_entidad], 1, 0, 'C', 1); 
		$this->Cell(40,8,  "", 0, 0, 'C', 1); 
		$this->Cell(15,8,   $quincena1, 1, 0, 'C', 1); 
		$this->Cell(15,8,   $quincena2, 1, 0, 'C', 1); 
		$this->Cell(15,8,  $this->mes, 1, 0, 'C', 1); 
		$this->Cell(15,8,   $this->ano, 1, 0, 'C', 1); 	 
		$this->Ln();
		$this->Ln();
		$this->SetFont('helvetica', 'B', 8);

		for($i = 0; $i < $num_headers; ++$i) 
		{
			$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C', 1);
		}
		$this->Ln();
		$this->SetFont('helvetica', '', 8);
		$consulta_sql          = " 
		SELECT DISTINCT *
		FROM mov_contraloria as A, mov_modificaciones AS B WHERE A.id_mov_contraloria = B.id_mov_contraloria
		AND A.id_mov_contraloria  =".$this->id." AND A.personal_id=".$this->idp;
		
		$respuesta_sql          =query($consulta_sql, $conexion);

	    while($fila=fetch_array($respuesta_sql))
	    {
			//$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

	    	/*if ($fila['c001']!=0) 
	    	{
	    		$tipo_sueldo="001";
	    		$valor_sobresueldo=$fila['002'];

	    	}*/
	    	if ($fila['c002']!=0) 
	    	{
	    		$tipo_sueldo="002";
	    		$valor_sobresueldo=$fila['c002'];
	    	}
	    	elseif ($fila['c003']!=0) 
	    	{
	    		$tipo_sueldo="003";
	    		$valor_sobresueldo=$fila['c003'];
	    	}
	    	elseif ($fila['c011']!=0) {
	    		$tipo_sueldo="011";
	    		$valor_sobresueldo=$fila['c011'];
	    	}
	    	elseif ($fila['c012']!=0) {
	    		$tipo_sueldo="012";
	    		$valor_sobresueldo=$fila['c012'];
	    	}
	    	elseif ($fila['c013']!=0) {
	    		$tipo_sueldo="013";
	    		$valor_sobresueldo=$fila['c013'];
	    	}
	    	elseif ($fila['c019']!=0) {
	    		$tipo_sueldo="019";
	    		$valor_sobresueldo=$fila['c019'];
	    	}
	    	elseif ($fila['c080']!=0) {
	    		$tipo_sueldo="080";
	    		$valor_sobresueldo=$fila['c080'];
	    	}
	    	elseif ($fila['c030']!=0) {
	    		$tipo_sueldo=$fila['030'];
	    		$valor_sobresueldo=$fila['c030'];
	    	}
	    	
			$this->SetFillColor(255, 255, 255);
			$this->SetTextColor(0,0,0);
			$this->SetDrawColor(100, 100, 100);
			$this->SetLineWidth(0.3);
			// Header
			$num_headers = count($header);

			if(	$this->GetY() >144)
			{
				$this->AddPage();
				$this->SetFont('helvetica', 'B', 8);
				$this->SetTextColor(0);
				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$num_headers = count($cabecera);
				$w           = array(25, 15,13,13,10,22,22,20,10,13,30,15,20,20);
				$this->Cell(20,8,  "", 0, 0, 'C', 1); 
				$this->Cell(15,8,  "Área", 1, 0, 'C', 1); 
				$this->Cell(15,8,  "Entidad", 1, 0, 'C', 1); 
				$this->Cell(100,8,  "Nombre de la Entidad:", 1, 0, 'C', 1);
				$this->Cell(40,8,  "", 0, 0, 'C', 1); 
				$this->Cell(15,8,  "1ra", 1, 0, 'C', 1); 
				$this->Cell(15,8,  "2da", 1, 0, 'C', 1); 
				$this->Cell(15,8,  "Mes", 1, 0, 'C', 1); 
				$this->Cell(15,8,  "Año:", 1, 0, 'C', 1);
				$this->Ln();
		        $this->SetFont('helvetica', '', 8);
		        if($this->quincena==1)
		        {
					$quincena1 ="X";
					$quincena2 ="";
		        }
		        else
		        {
					$quincena1 ="";
					$quincena2 ="X";
		        }
				$this->Cell(20,8,  "Ministerio:", 0, 0, 'C', 1); 
				$this->Cell(15,8,  $row[area], 1, 0, 'C', 1); 
				$this->Cell(15,8,  $row[ministerio], 1, 0, 'C', 1); 
				$this->Cell(100,8,  $row[nombre_entidad], 1, 0, 'C', 1); 
				$this->Cell(40,8,  "", 0, 0, 'C', 1); 
				$this->Cell(15,8,   $quincena1, 1, 0, 'C', 1); 
				$this->Cell(15,8,   $quincena2, 1, 0, 'C', 1); 
				$this->Cell(15,8,  $this->mes, 1, 0, 'C', 1); 
				$this->Cell(15,8,   $this->ano, 1, 0, 'C', 1); 	 
				$this->Ln();
				$this->Ln();
		        $this->SetFont('helvetica', 'B', 8);

				for($i = 0; $i < $num_headers; ++$i) 
				{
					$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C', 1);
				}
				$this->Ln();
			}
			$this->SetFont('helvetica', '', 8);
			$border =1;
			$ixx    =0;
			if ($fila['titular_interino']=="1") 
			{
				$titular_interino="T";
			}
			else 
			{
				$titular_interino="I";
			}
			$this->Cell($w[$ixx++], $altura, $fila['num_decreto'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['fecha_decreto'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['nomposicion_id'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['tipnom'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $titular_interino, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['cedula'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['cedula'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['seguro_social'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['sexo'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);			
			$this->Cell($w[$ixx++], $altura, $fila['clave_ir'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['nombres']." ".$fila['apellido_paterno'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['suesal'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $tipo_sueldo, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $valor_sobresueldo, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['observacion'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $this->Ln();

         }//fin del while
		
         

         while ($this->GetY() <144) 
         {
         	$ixx=0;
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, " ", $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $this->Ln();
         }
        
	}
	public function Footer() 
	{
		// Logo
		$this->SetY(-50);


		$this->Ln();
		$align="C";
		$this->SetFont('helvetica', '',8);

		$this->Cell(92, $altura,"Firmas", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"Registro de Presupuesto",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"Fiscalización de Planillas", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"Analista de Planillas", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"SELLO",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"SELLO", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"Jefe de Planillas", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(92, $altura,"General de Fiscalización", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

		$this->Ln();
		$this->Cell(92, $altura,"", $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"",  $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(92, $altura,"", $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		// Set font
		// Page number
		
	}
}//fin clase
		
		
		

// create new PDF document
$pdf                = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio   =$_REQUEST['fecha_ini'];
$pdf->fechafinal    =$_REQUEST['fecha_fin'];
$pdf->fecharegistro =$_REQUEST['fecha_reg'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Selectra');
$pdf->SetTitle('MODIFICACIONES O CAMBIOS DE CEDULA');
$pdf->SetSubject('Reporte MODIFICACIONES O CAMBIOS DE CEDULA');
$pdf->SetKeywords('TCPDF, PDF, reporte, cambio, cedula');

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
$pdf->SetFont('helvetica', LEGAL);

// add a page
$pdf->AddPage('L', 'A4');

$reg           =$_REQUEST['reg'];
$pdf->id       =$_REQUEST['id'];
$pdf->idp      =$_REQUEST['idp'];
$pdf->quincena =$_REQUEST['quincena'];
$pdf->mes      =$_REQUEST['mes'];
$pdf->ano      =$_REQUEST['ano'];
$pdf->tipo     =$_REQUEST['tipo'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

/* Limpiamos la salida del búfer y lo desactivamos */
ob_end_clean();
/* Finalmente generamos el PDF */

//Close and output PDF document
$pdf->Output('Informe_cambio_cedula.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();