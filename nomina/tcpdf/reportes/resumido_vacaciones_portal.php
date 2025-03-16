<?php
set_time_limit(0);

require_once('../tcpdf.php');
require_once '../../lib/config.php';
include("../../lib/common.php");
include ("../../paginas/funciones_nomina.php");
require_once('../../lib/database.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $fechainicio, $fechafinal, $fecharegistro, $quincena, $ficha, $cedula, $id, $anio;
	//Page header
	public function Header() 
	{
		// Logo
		$db                = new Database($_SESSION['bd']);
		$var_sql           ="SELECT * FROM nomempresa";
		$res               = $db->query($var_sql);
		$row_rs            = $res->fetch_object();
		$var_encabezado1   =$row_rs->nom_emp;
		$var_izquierda     ='../../imagenes/'.$row_rs->imagen_der;
		$var_derecha       ='../../imagenes/'.$row_rs->imagen_izq;
		$image_file        = K_PATH_IMAGES.'../../vista/Encabezado.png';
		$this->SetFont('helvetica', 16);
		$fecha             = date("d")."-".date("m")."-".date("Y");
		$consultar   = "SELECT periodo_ini,periodo_fin 
		from nom_nominas_pago  
		where codnom ='{$this->codnom}' 
		AND codtip   ='{$this->tipnom}'";
		$cabecera    = $db->query($consultar)->fetch_assoc();
		$fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $cabecera['periodo_fin'] ))) ; // resta 11 mes
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
		$membrete          ="<br><b>".$var_encabezado1."<br>Planilla de Vacaciones <br>";


        $this->Image($var_izquierda,11,11,35,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(110, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=160,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->Ln();
	}
	function Footer()
	{
	    $this->SetY(-15);

		$this->SetFont('helvetica','I',8);
	    $this->Cell(0,5,utf8_decode('Pagina ').$this->getAliasNumPage().'/'.$this-> getAliasNbPages(),0,1,'C');
	}
    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
			$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
			$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
			return $buena;
		}
	}
	public function CuerpoTablaHtml()
	{
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=35;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=30;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}

		$fill              = 1;
		$border            = 0;
		$ln                = 0;
		$fill              = 0;
		$align             ='C';
		$link              = 0;
		$stretch           = 1;
		$ignore_min_height = 0;
		$calign            ='T';
		$valign            ='T';
		$height            = 5.75;//alto de cada columna
		$YMax              = 260;
		$altura            = 7;
		$db = new Database($_SESSION['bd']);

		$consultar   = "SELECT np.ficha, nc.des_car, np.apenom, np.cedula, np.codcargo, vac.fechavac,vac.fechareivac
		from nom_progvacaciones  as vac
		LEFT JOIN nompersonal AS np ON ( vac.ficha = np.ficha)
		LEFT JOIN nomcargos AS nc ON (np.codcargo = nc.cod_car)
		where vac.ficha ='{$this->ficha}' 
		AND vac.periodo   ='{$this->anio}'";
		$db          = new Database($_SESSION['bd']);
		
		$rc          = $db->query($consultar)->fetch_assoc();
		
		$fechavac    = new Datetime($rc['fechavac']);
		$fechareivac = new Datetime($rc['fechareivac']);


		$fechavac    = $fechavac->format('d-m-Y');
		$fechareivac = $fechareivac->format('d-m-Y');
		
		$this->Ln(5);
		$this->SetFont('helvetica','B',8);
		$this->Cell(10,5,utf8_decode('FICHA'),1,0);
		$this->Cell(54,5,'APELLIDO Y NOMBRES ',1,0);
		$this->Cell(15,5,utf8_decode('CEDULA'),1,0);
		$this->Cell(60,5,utf8_decode('CARGO '),1,0);
		$this->Cell(22,5,utf8_decode('FECHA INICIO '),1,0);
		$this->Cell(29,5,utf8_decode('FECHA REINGRESO '),1,0);
		$this->Ln();
		$this->SetFont('helvetica','I',7);
		$this->Cell(10,5,utf8_decode($rc['ficha']),0,0);
		$this->Cell(54,5,$rc['apenom'],0,0);
		$this->Cell(15,5,utf8_decode($rc['cedula']),0,0);
		$this->Cell(60,5,utf8_decode($rc['des_car']),0,0);

	
		$this->Cell(22,5,utf8_decode($fechavac),0,0);
		$this->Cell(29,5,utf8_decode($fechareivac),0,0);
		// llamado para hacer multilinea sin que haga salto de linea
		$this->Ln();
		$this->SetFont('helvetica','B',8);
		$this->Cell(190,5,'PROMEDIOS','LRT',0);
		$this->Ln();
		$this->Cell(90,5,'SALARIOS','L',0);
		$this->Cell(100,5,'GASTOS DE REPRESENTACION','RB',0);
		$this->Ln();
		$this->Cell(25,5,'FECHA',1,0);
		$this->Cell(40,5,'Tipo De Planilla',1,0);
		$this->Cell(25,5,'Salario',1,0);
		$this->Ln();

		$this->Cell(190, 0, '', 0,0);

		for ($i=11; $i > 0; $i--) 
		{ 
			$mes = "-".$i." month";
			$fechaanio = date('Y', strtotime($mes, strtotime ( $fechavac ))) ; // resta n cantidad de meses
			$fechames = date('m', strtotime($mes, strtotime ( $fechavac ))) ; // resta n cantidad de meses

			$db = new Database($_SESSION['bd']);
			$consultar = "SELECT periodo_fin, periodo_ini, codnom, codtip
			FROM nom_nominas_pago 
			WHERE anio = '{$fechaanio}' AND mes = '{$fechames}' AND codtip = '{$_SESSION['codigo_nomina']}'";

			//echo $sql,"<br>";	
			$cabecera    = $db->query($consultar)->fetch_assoc();



			$consulta = "
			SELECT nmn.ficha, nmn.monto, netos.neto
				FROM nom_movimientos_nomina AS nmn 
				LEFT JOIN nom_nomina_netos AS netos ON ( nmn.ficha = netos.ficha )
				WHERE nmn.codnom='{$cabecera[codnom]}' AND nmn.tipnom='{$cabecera[codtip]}'
				GROUP BY nmn.ficha";


			$res1 = $db->query($consulta);
			while ($rc = $res1->fetch_assoc()) 
			{
				
				
					
				//}	
			}

		}


	}
}//fin clase
		
// create new PDF document
$pdf         = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->anio   = $_REQUEST['anio'];
$pdf->cedula = $_REQUEST['cedula'];
$pdf->ficha  = $_REQUEST['ficha'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Selectra');
$pdf->SetTitle('Listado de Personal Vacaciones');
$pdf->SetSubject('Reporte Listado de Personal Vacaciones');
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
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('P', 'A4');

$reg           = $_REQUEST['reg'];
$pdf->quincena = $_REQUEST['quincena'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Informe_Listado_Vacaciones.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+