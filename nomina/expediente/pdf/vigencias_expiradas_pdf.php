g<?php 
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}
//require_once('../../../lib/database.php');
//$db           = new Database($_SESSION['bd']);

/*$sql1 =  "SELECT * FROM expediente WHERE cod_expediente_det=".$_GET['codigo'];
$dateExpediente = $db->query($sql1);
$dateExpediente->fetch_array();*/
//$data1=$db->fetch_all_array($sql1);

/*$sql2="SELECT * FROM expediente_analisis WHERE cod_expediente_det=".$_GET['codigo'];
$data2=$db->fetch_all_array($sql2);

$sql3="SELECT * FROM expediente_analisis WHERE cod_expediente_det=".$_GET['codigo'];
$data3=$db->fetch_all_array($sql3);*/



/*INSERT INTO expediente_analisis
                        (id_analisis, fecha, etapa, salario, resuelto_1, veinte_porciento, 
                        cuarenta_porciento, resuelto_2, cod_expediente_det)*/

/*INSERT INTO expediente_bienal
                        (id_bienal, fecha, numero, salario, resuelto, monto_mensual, 
                        acumulativo, cod_expediente_det)*/

require('fpdf.php');
require_once('../../lib/database.php');
$db = new Database($_SESSION['bd']);
       
/*include_once('../clases/database.class.php');
include('../obj_conexion.php');*/



class PDF extends FPDF
{
var $usuario;
var $pdff;
var $odp;
var $tipo;
//Cabecera de página
function Header()
{

//       $Conn=conexion_conf();
//	$var_sql="select encabezado1,encabezado2,encabezado3,encabezado4,imagen_izq,imagen_der from parametros";
//	$rs = query($var_sql,$Conn);
//	$row_rs = fetch_array($rs);
		
        // Arial bold 10
        $this->SetFont('Arial','B',10);       
        // Title
        //$this->Cell(80);
        $this->Cell(190,6,'MINISTERIO DE SALUD',0,0,'C');
        $this->Ln(2);
        $this->Cell(190,12,'DIRECCION DE RECURSOS HUMANOS',0,0,'C');
        $this->Ln(2); 
        $this->Cell(190,18,utf8_decode('DEPARTAMENTO DE CLASIFICACIÓN Y RETRIBUCIÓN DE PUESTO'),0,0,'C');
        $this->Ln(2);
        
	/*$var_encabezado1="MINISTERIO DE SALUD";
	$var_encabezado2="DIRECCION DE RECURSOS HUMANOS";
	$var_encabezado3="ANALISIS DE CAMBIOS DE CATEGORIA / GREMIO";
	$var_encabezado4="REGION:";*/
//	$var_imagen_izq=$row_rs['imagen_izq'];
//	$var_imagen_der=$row_rs['imagen_der'];


//	$var_sql="select codigo,nomemp,departamento,presidente,periodo,cargo,nivel,desislr,ctaisrl,desiva,ctaiva,por_isv,compra,servicio,rif,nit,direccion,telefono,por_im,por_bomberos,lugar,sobregirop,autorizacionodp,claveodp,contrato,gas_dir from parametros";
//	$rsu = query($var_sql,$Conn);
//	$row_rsu = fetch_array($rsu);
//	$var_nomemp=$row_rsu['nomemp'];
//	
//	cerrar_conexion($Conn);
	
//        $this->Image($var_imagen_izq,10,8,22); 
        /*$this->Ln(2);
        $this->Cell(25);
        $this->Cell(100,8,utf8_decode($var_encabezado1),0,0,"L");
    //		$this->Image($var_imagen_der,175,8,22);
        $this->Ln(6);
        $this->Cell(25);
        $this->Cell(120,8,utf8_decode($var_encabezado2),0,0,"L");
        $this->Ln(6);
        $this->Cell(25);
        $this->Cell(170,8,utf8_decode($var_encabezado3),0,0,"L");
        $this->Ln(6);
        $this->Cell(25);
        $this->Cell(170,8,utf8_decode($var_encabezado4),0,0,"L");
        $this->Ln(7);*/

	//echo " Entro Analisis Cambio Categoria PDF - HEADER "; 

}


//Hacer que sea multilinea sin que haga un salto de linea

var $widths;
var $aligns;

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}

function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}



function imprimir_datos($pdf,$dataExpediente, $dataNompersonal, $dataAnalisis)
{    
        while($nompersonal = $dataNompersonal->fetch_assoc())
	{
            $posicion       =   $nompersonal['nomposicion_id'];
            $planilla       =   $nompersonal['tipnom'];
            $partida        =   $nompersonal['ctacontab'];
            $nombre         =   $nompersonal['apenom'];
            $cedula         =   $nompersonal['cedula'];
            $segurSocial    =   $nompersonal['seguro_social'];
            $cargo          =   $nompersonal['des_car'];
            $region         =   $nompersonal['descrip'];
             
        }
    
        $pdf->SetFont('Times','',10);
        $this->pdff=$pdf;
        
        $this->Ln();
        $this->Cell(190,8,utf8_decode('VIGENCIAS EXPIRADAS'),0,0,"C");
        $this->Ln();
        $this->cell(120,8,'NOMBRE: '.$nombre,0,0,'L');
        
        $this->cell(80,8,utf8_decode('CÉDULA: '.$cedula),0,0,'L');
        
        $this->Ln();
        $this->cell(120,8,utf8_decode('POSICIÓN: '.$posicion),0,0,'L');     
        $this->cell(80,8,utf8_decode('SEGURO SOCIAL: '.$segurSocial),0,0,'L');
        $this->Ln();
        $this->cell(40,8,'PLANILLA: '.$planilla,0,0,'L');         
        $this->Ln();
        $this->cell(40,8,'CARGO: '.$cargo,0,0,'L');         
        $this->Ln();
        $this->cell(40,8,utf8_decode('REGIÓN: '.$region),0,0,'L');         
        $this->Ln();
        $this->cell(40,8,utf8_decode('UBICACIÓN: '.$region),0,0,'L');         
        $this->Ln();
       // $this->Cell(190,8,'NOMBRE: ',0,0,"L");
        //$this->Ln();
        while($expediente = $dataExpediente->fetch_assoc())
	{  
            $this->Cell(190,8,utf8_decode('MONTO QUE ADEUDA EL ESTADO'),0,0,"C");
            $this->Ln();

            $this->Cell(190,8,utf8_decode('EN CONCEPTO DE: '.$expediente['concepto']),0,0,"L");
            $this->Ln();       
       
        
            $headerTabla = array('PERIODO ADEUDADO', utf8_decode('SALARIO QUE DEVENGÓ'), utf8_decode('CATEGORÍA'), utf8_decode('SALARIO QUE DEBIÓ DEVENGAR'), utf8_decode('CATEGORÍA'),'DIFERENCIA', 'PERIODO ADEUDADO A/M/D','MONTO A PAGAR');
            // Carga de datos
           $this->SetFont('Arial','',8);
            while($analisis = $dataAnalisis->fetch_assoc())
            {
                    
                    $dataTable[$i] = array($analisis['periodo_adeudado'],number_format((float)$analisis['salario_devengado'],2,'.',','),$analisis['categoria_1'],number_format((float)$analisis['salario_devengar'],2,'.',','),$analisis['categoria_2'],number_format((float)$analisis['diferencia'],2,'.',','),$analisis['periodo_adeudado_amd'],number_format((float)$analisis['monto_pagar'],2,'.',','));
                    $i++;
            } 

            //
            //$dataTable = array(array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''));
            $dataTableCell = array(23,23,23,23,23,23,23,29);
            $this->SetWidths(array(23,23,23,23,23,23,23,29));
            $this->SetAligns(array('C','C','C','C','C','C','C','C'));
            $this->SetFont('Arial','',8);
            $dataTableAlign = array('C','C','C','C','C','C','C','C');
            $this->BasicTable2($headerTabla,$dataTable,$dataTableCell,$dataTableAlign);

            $this->Ln();     
            $this->MultiCell(190,5,'OBSERVACIONES: '.utf8_decode($expediente['descripcion']),0,'L');
            $this->Ln(); 

            $this->Cell(190,8,'ANALISTA RESPONSABLE: '.utf8_decode($expediente['analista']),0,0,"L");
            $this->Ln();
            $this->Cell(190,8,'AUDITOR: '.utf8_decode($expediente['auditor']),0,0,"L");
            $this->Ln();
            $array_fecha=explode('-',$expediente['fecha']); 
            $fecha=$array_fecha[2].'/'.$array_fecha[1].'/'.$array_fecha[0];
            $this->Cell(190,8,utf8_decode('Fecha de levantamiento de información: '.$fecha),0,0,"l");
            $this->Ln();  
        }
}

// Tabla simple


function BasicTable($header, $data,$cell)
{    
    $j=0;
    // Cabecera
    foreach($header as $col){
        $this->Cell($cell[$j],7,$col,1,0,'C');
        $j++;
    }
    $this->Ln();
    // Datos
    foreach($data as $row)
    {
        $i=0;
        foreach($row as $col){
            $this->Cell($cell[$i],6,$col,1);
            $i++;
        }
            
        $this->Ln();
    }
}




function BasicTable2($header, $data,$cell,$alto)
{   
    
    // Cabecera                      
    $this->Row($header);   
    //
    //Cuerpo
        foreach($data as $row)
        {      
                $this->Row($row);                                             
        }               
    // Datos
    
}




//Pie de página
function Footer()
{
   
    	//Posición: a  cm del final
   	//$this->SetY(-47);
    	
	

	//reajustar posicion tabla
	//$x=$this->GetX();
        //$y=$this->GetY();
        
	//$this->SetXY($x,$y-8);
//	$bool=validar_firma("ODP".$this->tipo);
//	if ($bool==true){
//		firma_dinamica("ODP".$this->tipo,$this->pdff,6,10);
//	}else{
//		odp($this->pdff);
//	}

    	//$this->SetFont('Arial','I',8);
    	//$this->Cell(0,5,'Elaborado Por: ',0,1,'L');
        
        //echo " Entro Analisis Cambio Categoria PDF - FOOTER ";
//	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();


$sql1   = "SELECT A.*, B.des_car,B.cod_cargo, C.descrip FROM nompersonal AS A, nomcargos AS B, nomnivel1 AS C WHERE A.cedula='".$_GET['cedula']."' AND A.codcargo=B.cod_car AND A.codnivel1=C.codorg";
$sql2   = "SELECT * FROM expediente  WHERE cod_expediente_det=".$_GET['codigo'];
$sql4   = "SELECT * FROM  expediente_vigencia WHERE cod_expediente_det=".$_GET['codigo'];
 
$dataNompersonal = $db->query($sql1);
$dataExpediente  = $db->query($sql2);
$dataAnalisis    = $db->query($sql4);

$pdf->imprimir_datos($pdf,$dataExpediente, $dataNompersonal, $dataAnalisis);

//$pdf->imprimir_datos2($pdf);


ob_end_clean();
$pdf->Output();
?>
