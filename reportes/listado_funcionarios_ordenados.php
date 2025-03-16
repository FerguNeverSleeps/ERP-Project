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
	public $cedula, $fechafinal, $fecharegistro, $quincena, $mes, $año, $id,$posicion,$nombres,$apellidos,$funcion,$cargo,$genero,
	$promocion,$situacion;
	//Page header
	public function Header() {
		// Logo
		 global $empresa;
        $this->SetFont('times', 'B', 11);
        $image_file = "/var/www/html/migracion_rrhh/nomina/imagenes/migracion.jpg";
        $altura=8;

        $this->Image($image_file, $x=10, $y=5, $w=16,   $h=16, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);
        $this->Multicell(210,6,'REPÚBLICA DE PANAMÁ',$border=0,$align='L',$fill=0,$ln=1,$x='30',$y='5',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=6,$valign='M',$fitcell=false);
         $this->Multicell(210,6,'SERVICIO NACIONAL DE MIGRACION',$border=0,$align='L',$fill=0,$ln=1,$x='30',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=6,$valign='M',$fitcell=false); 
         $this->Multicell(210,6,'UNIDAD DE RECURSOS HUMANOS',$border=0,$align='L',$fill=0,$ln=1,$x='30',$y='',$reseth=true,$stretch=0,$ishtml=false,$autopadding=true,$maxh=6,$valign='M',$fitcell=false); 
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
			$Y=24;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=24;
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
  		$sql_columnas = "SELECT np.personal_id, np.id_promo, np.cedula, np.apenom, np.nomposicion_id,np.seguro_social, date_format(np.fecnac,'%d-%m-%Y') fecnac,np.fecing,date_format(np.fecha_permanencia,'%d-%m-%Y') fecha_permanencia,np.sexo, pos.sueldo_1, np.estado,  (pos.sueldo_1 - pos.gastos_representacion) as sueldo_total, nc.des_car as cargo, nf.descripcion_funcion as funcion,  nivel1.descrip as departamento,instruc.descripcion as grado_instruccion,  np.personal_externo, prom.descripcion as promocion,categorias.descrip categoria, tipo.Descripcion as tipo, np.num_doc_reintegro, date_format(np.fecha_doc_reintegro,'%d-%m-%Y') fecha_doc_reintegro,np.direccion,np.TelefonoResidencial, np.cm_numero_resolucion, date_format(np.cm_fecha_resolucion,'%d-%m-%Y') cm_fecha_resolucion ";

		$sql_FROM = " FROM nompersonal as np LEFT JOIN nomposicion as pos ON (np.nomposicion_id=pos.nomposicion_id)
		LEFT JOIN nomcargos as nc ON (nc.cod_cargo = np.codcargo)
		LEFT JOIN nomfuncion as nf ON (np.nomfuncion_id = nf.nomfuncion_id)
		LEFT JOIN nomnivel1 as nivel1 ON (nivel1.codorg = np.codnivel1)
		LEFT JOIN nominstruccion as instruc ON (instruc.codigo = np.IdNivelEducativo)
		LEFT JOIN promocion as prom ON (prom.id = np.id_promo) 
		LEFT JOIN tipoempleado as tipo ON (tipo.IdTipoEmpleado = np.tipo_funcionario) 
		LEFT JOIN nomcategorias as categorias ON (categorias.codorg = np.codcat) ";

		$sql_WHERE = " WHERE np.estado <> 'De Baja' ";
		$fragmento_sql = "";

		if ($this->posicion!="" && $this->posicion!=0 && $this->posicion!=NULL) {
		  $fragmento_sql.=" AND np.nomposicion_id like '%".$this->posicion."%' ";
		}
		if ($this->nombres!="" && $this->nombres!=0 && $this->nombres!=NULL) {
		  $fragmento_sql.=" AND np.apenom like '%".$this->nombres."%' ";
		}
		if ($this->apellidos!="" && $this->apellidos!=0 && $this->apellidos!=NULL) {
		  $fragmento_sql.=" AND np.apenom like '%".$this->apellidos."%' ";
		}
		if ($this->funcion!="" && $this->funcion!=0 && $this->funcion!=NULL) {
		  $fragmento_sql.=" AND nf.nomfuncion_id like '%".$this->funcion."%' ";
		}
		if ($this->cargo!="" && $this->cargo!=0 && $this->cargo!=NULL) {
		  $fragmento_sql.=" AND nc.cod_cargo like '%".$this->cargo."%' ";
		}
		if ($this->genero!="" && $this->genero!=0 && $this->genero!=NULL) {
		  $fragmento_sql.=" AND np.sexo like '%".$this->genero."%'";
		}
		if ($this->promocion!="" && $this->promocion!=0 && $this->promocion!=NULL) {
		$comp = explode(",", $this->promocion);
		if(count($comp)>1)
		{
		  $fragmento_sql.=" AND np.id_promo in (".$this->promocion.")";
		}
		else
		{
		  $fragmento_sql.=" AND np.id_promo like '%".$this->promocion."%'";

		}
		}
		if ($this->situacion!=""  && $this->situacion!=NULL) {
		  $fragmento_sql.=" AND np.estado like '%".$this->situacion."%'";
		}
		if ($this->tipo!="" && $this->tipo!=0 && $this->tipo!=NULL) {
		  $fragmento_sql.=" AND np.tipo_funcionario like '%".$this->tipo."%'";
		}

		if ($this->categoria!="" && $this->categoria!=0 && $this->categoria!=NULL) {
		  $fragmento_sql.=" AND np.codcat = '{$this->categoria}'";
		}


		$sql_WHERE .= $fragmento_sql;
		$sql2       .= $sql_columnas;
		$sql2       .= $sql_FROM;
		$sql2       .= $sql_WHERE;

		$this->SetFont('helvetica', 'B', 6);
		$this->SetTextColor(0);
		$this->SetFillColor(255, 255, 255);
		$this->SetTextColor(0,0,0);
		$this->SetDrawColor(100, 100, 100);
		$w = array(25, 15,13,13,10,22,30,60,60,30);

		$sql         = "SELECT apenom, cedula, nombres, apellidos from nompersonal where cedula = '{$this->cedula}'";
		$result      = $conexion->query($sql, $conexion);
		$nombre_titulo = $result->fetch_array();
		//$this->Cell(200,8,"LISTADO FUNCIONARIOS", 0, 0, 'L', 1); 	 
		//$this->Ln();
		
        $this->SetFont('helvetica', 'B', 6);


		/*$cabecera = array('Pos.','Cédula', 'Nombre' , 'F. Nac.','Función', 'Salario', 'Género', 'Departamento','Niv. Educ.','Promoción', 'Doc. Carr.','F. Doc. Carr.', 'Estado');*/
		$cabecera = array('Pos.','Cédula', 'Nombre' , 'F. Nac.','Fec. Per.','Función', 'Salario', 'Género', 'Departamento','Niv. Educ.','Promoción', 'Estado','Doc. Carr.','F. Doc. Carr','Tipo','Categoria','Tel.','Dirección');
    	$cuerpo=array($fecha,$turno);
       	#$w = array(8,15,40,15,35,10,15,80,20,20,14,16,14);
       	$w = array(6,12,35,10,10,35,8,10,60,16,15,10,10,13,15,25,10,40);
  	  	$num_headers = count($cabecera);

		for($i = 0; $i < $num_headers; ++$i) {
			$this->Cell($w[$i], 8, $cabecera[$i], 1, 0, 'C', 1);

		}
		$this->Ln();
		$this->SetFont('helvetica', '', 6);
		$res1=$conexion->query($sql2);		
		//echo $sql2;exit;	
		$cant = 1;
	    while($fila = mysqli_fetch_array($res1))

	    {
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
				$cuerpo=array($fecha,$turno);
       			$ww = array(6,12,35,10,10,35,8,10,60,16,15,10,10,13,15,25,10,40);
		  	  	$num_headers = count($cabecera);
				$this->SetFont('helvetica', 'B', 6);

				for($i = 0; $i < $num_headers; ++$i) {
					$this->Cell($ww[$i], 8, $cabecera[$i], 1, 0, 'C', 1);

				}
				$this->Ln();
				$this->SetFont('helvetica', '', 6);

			}

			$border=1;
			$ixx=0;
			$this->Cell($w[$ixx++], $altura, $fila['nomposicion_id'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['cedula']), $border,$ln,'C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['apenom']), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['fecnac'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['fecha_permanencia'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['funcion'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['sueldo_total'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['sexo'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['departamento']), $border,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['grado_instruccion']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['promocion'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['estado']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['cm_numero_resolucion']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, $fila['cm_fecha_resolucion'], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['tipo']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['categoria']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['TelefonoResidencial']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
			$this->Cell($w[$ixx++], $altura, utf8_encode($fila['direccion']), $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		    $this->Ln();
		    $cant++;

         }//fin del whileç
         if(	$this->GetY() >172)
		{
			$this->AddPage();
			$this->SetFont('helvetica', 'B', 7);

         	$this->Cell(143, $altura, "Total de Funcionarios: ".$cant, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

			$this->Ln();

		}else
		{

			$this->SetFont('helvetica', 'B', 7);

         	$this->Cell(126, $altura, "Total de Funcionarios: ".$cant, $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

			$this->Ln();
		}

	}
	public function Footer() {
		// Logo
		//$this->SetY(-40);

		// Set font
		// Page number
		
	}
}//fin clase
		
		
		

// create new PDF document
$pdf            = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->cedula    = isset($_REQUEST['cedula']) ? $_REQUEST['cedula'] : NULL ;
$pdf->posicion  = isset($_REQUEST['posicion']) ? $_REQUEST['posicion'] : NULL ;
$pdf->nombres   = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : NULL ;
$pdf->apellidos = isset($_REQUEST['apellido']) ? $_REQUEST['apellido'] : NULL ;
$pdf->funcion   = isset($_REQUEST['funcion']) ? $_REQUEST['funcion'] : NULL ;
$pdf->cargo     = isset($_REQUEST['cargo']) ? $_REQUEST['cargo'] : NULL ;
$pdf->genero    = isset($_REQUEST['genero']) ? $_REQUEST['genero'] : NULL ;
$pdf->externo   = isset($_REQUEST['externo']) ? $_REQUEST['externo'] : NULL ;
$pdf->tipo      = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : NULL ;
$pdf->promocion = isset($_REQUEST['promocion']) ? $_REQUEST['promocion'] : NULL ;
$pdf->situacion = isset($_REQUEST['situacion']) ? $_REQUEST['situacion'] : NULL ;
$pdf->categoria = isset($_REQUEST['categoria']) ? $_REQUEST['categoria'] : NULL ;

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Amaxonia');
$pdf->SetTitle('Listado ordenado de funcionarios');
$pdf->SetSubject('Reporte Listado ordenado de funcionarios');
$pdf->SetKeywords('TCPDF, PDF, reporte, Listado ordenado de funcionarios');

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
$pdf->AddPage('L', 'legal');

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml();
ob_clean();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('pdf_listado_funcionarios_ordenados.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();