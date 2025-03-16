<?php 
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
       
//include_once('../clases/database.class.php');
//include('../obj_conexion.php');





class PDF extends FPDF 
{
   

var $usuario;
var $pdff;
var $odp;
var $tipo;
var $dbC ; //echo DB_HOST.' '.DB_USUARIO.' '.DB_CLAVE.' '.$_SESSION['bd'];	;
//Cabecera de página
function Header()
{		
   
        
        // Arial bold 10
        $this->SetFont('Arial','B',10);       
        // Title
        //$this->Cell(80);
        $this->Cell(190,6,'MINISTERIO DE SALUD',0,0,'C');
        $this->Ln(2);
        $this->Cell(190,12,'DIRECCION DE RECURSOS HUMANOS',0,0,'C');
        $this->Ln(2); 
        $this->Cell(190,18,'ANALISIS DE CAMBIOS DE CATEGORIA / GREMIO',0,0,'C');
        
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


function imprimir_datos($pdf,$dataExpediente,$dataNompersonal,$dataBianal,$dataAnalisis)
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
        
        $this->Ln(2);
        $this->Cell(190,24,'REGION:'.$region,0,0,'C');      
        $this->Ln(20);
        
        $pdf->SetFont('Times','',12);
        $this->pdff=$pdf;		
        $this->SetFont('Arial','B',10);
        $this->Cell(63,8,utf8_decode('POSICIÓN: '.$posicion),0,0,"L");
        $this->cell(63,8,'PLANILLA: '.$planilla,0,0,'L');
        $this->cell(63,8,'PARTIDA: '.$partida,0,0,'L');        
        $this->Ln();
        $this->Cell(190,8,'NOMBRE: '.$nombre,0,0,"L");
        $this->Ln();
        $this->Cell(64,8,utf8_decode('CÉDULA: '.$cedula),0,0,"L");
        $this->cell(126,8,'SEGURO SOCIAL: '.$segurSocial,0,0,'L');
        $this->Ln();
        $this->Cell(190,8,utf8_decode('CARGO: '.$cargo),0,0,"L");
        $this->Ln();
        
        while($expediente = $dataExpediente->fetch_assoc())
	{                   
            $array_fecha_idoneidad=explode('-',$expediente['fecha_idoneidad']); 
            $fecha_idoneidad=$array_fecha_idoneidad[2].'/'.$array_fecha_idoneidad[1].'/'.$array_fecha_idoneidad[0];
            $this->cell(63,8,'FECHA DE IDONEIDAD:'.$fecha_idoneidad,0,0,'L');
             $this->Cell(63,8,'REGISTRO: '.$expediente['registro'],0,0,"L");
            $this->cell(63,8,'FOLIO: '.$expediente['folio'],0,0,'L');        
            $this->Ln();
            $this->cell(90,8,'INICIO DE LABORES EN OTRAS INST. DE SALUD :',0,0,'L');
            $array_fecha_ini_labor_otra_inst = explode('-',$expediente['fecha_ini_labor_otra_inst']);
            $fecha_ini_labor_otra_inst = $array_fecha_ini_labor_otra_inst[2].'/'.$array_fecha_ini_labor_otra_inst[1].'/'.$array_fecha_ini_labor_otra_inst[0];
            $this->Cell(70,8,$fecha_ini_labor_otra_inst,0,0,"L");
            $this->Ln();
             
            $array_fecha_ini_labor_contrato = explode('-',$expediente['fecha_ini_labor_contrato']);
            $fecha_ini_labor_contrato = $array_fecha_ini_labor_contrato[2].'/'.$array_fecha_ini_labor_contrato[1].'/'.$array_fecha_ini_labor_contrato[0];
            
            $this->cell(90,8,'INICIO DE LABORES POR CONTRATO :',0,0,'L');
            $this->Cell(70,8,$fecha_ini_labor_contrato,0,0,"L"); 
            $this->Ln();
            
            $array_fecha_labor_permanent = explode('-',$expediente['fecha_labor_permanent']);
            $fecha_labor_permanent = $array_fecha_labor_permanent[2].'/'.$array_fecha_labor_permanent[1].'/'.$array_fecha_labor_permanent[0];
            
            $this->cell(90,8,'INICIO DE LABORES POR PERMANENTE :',0,0,'L');
            $this->Cell(70,8,$fecha_labor_permanent,0,0,"L");

            $this->Ln(); 
           
            //
            $this->cell(190,8,'SOBRESUELDOS :',0,0,'L');
            $this->Ln(3); 
            $this->cell(50,10,'JEFATURA:',0,0,'L');
            $this->Cell(45,8,number_format((float)$expediente['sobresueldo_jefatura'],2,',','.'),0,0,"L");

            $this->cell(50,8,'EXCLUSIVIDAD:',0,0,'L');
            $this->Cell(45,8,number_format((float)$expediente['sobresueldo_exclusividad'],2,',','.'),0,0,"L");
            $this->Ln(); 
            $this->cell(50,8,'ALTO RIESGO:',0,0,'L');
            $this->Cell(45,8,number_format((float)$expediente['sobresueldo_altoriesgo'],2,',','.'),0,0,"L");

            $this->cell(50,8,'ESPECIALIDAD:',0,0,'L');
            $this->Cell(45,8,number_format((float)$expediente['sobresueldo_especialidad'],2,',','.'),0,0,"L");
            $this->Ln();            
                        
        }
        
        $this->SetFont('Arial','B',10);
        $this->cell(190,8,'ANALISIS :',0,0,'L');
        $this->Ln();
        $headerTabla = array('INICIO DE LAB.', 'ETAPA', 'SALARIO', 'RESUELTO','20%','40%','RESUELTO');
        // Carga de datos  
         while($analisis = $dataAnalisis->fetch_assoc())
        {
                $array_fecha=explode('-',$analisis['fecha']); 
                $fecha=$array_fecha[2].'/'.$array_fecha[1].'/'.$array_fecha[0];
                $dataTable[$i] = array($fecha,$analisis['etapa'],number_format((float)$analisis['salario'],2,'.',','),$analisis['resuelto_1'],number_format((float)$analisis['veinte_porciento'],2,'.',','),number_format((float)$analisis['cuarenta_porciento'],2,'.',','),$analisis['resuelto_2']);
                $i++;
        }    
        //$dataTable = array(array('','','','','','',''),array('','','','','','',''),array('','','','','','',''),array('','','','','','',''),array('','','','','','',''),array('','','','','','',''),array('','','','','','',''));
        $dataTableCell = array(30,30,30,30,30,20,20);
        $this->SetFont('Arial','',8);
        $dataTableAlign = array('C','C','R','C','R','R','C');
        $this->BasicTable($headerTabla,$dataTable,$dataTableCell,$dataTableAlign);
                
        $this->Ln(); 
        $this->SetFont('Arial','B',10);
        $this->cell(190,8,'BIENALES :',0,0,'L');
        $this->Ln();
        
       
            
        $headerTabla2 = array('FECHA', 'N° DE BIENAL', 'SALARIO', 'RESUELTO','MONTO MENSUAL','ACUMULATIVO');
        // Carga de datos
        $this->SetFont('Arial','',8);
        $i=0;
            
                         
        while($bianal = $dataBianal->fetch_assoc())
        {
            $array_fecha=explode('-',$bianal['fecha']); 
            $fecha=$array_fecha[2].'/'.$array_fecha[1].'/'.$array_fecha[0];
            $dataTable2[$i] = array($fecha,$bianal['numero'],number_format((float)$bianal['salario'],2,'.',','),$bianal['resuelto'],number_format((float)$bianal['monto_mensual'],2,'.',','),number_format((float)$bianal['acumulativo'],2,'.',','));
            $i++;
        }            
        //$dataTable2  = array(array('','','','','',''),array('','','','','',''),array('','','','','',''),array('','','','','',''),array('','','','','',''),array('','','','','',''),array('','','','','',''));
        $dataTable2Cell = array(30,30,30,30,30,40);
        $dataTable2Align = array('C','C','R','C','R','R');
        $this->BasicTable($headerTabla2,$dataTable2,$dataTable2Cell,$dataTable2Align);    
        
        
}

function imprimir_datos2($pdf)
{
	$this->pdff=$pdf;
	
	$this->Ln(20);
	$this->SetFont('Arial','B',10);
	$this->Cell(63,8,utf8_decode('Posición: ______________'),0,0,"L");
	$this->cell(63,8,'Planilla: _____________',0,0,'L');
        $this->cell(63,8,'Partida: ______________',0,0,'L');        
	$this->Ln();
        $this->Cell(190,8,'Nombre: _______________________________________________',0,0,"L");
	$this->Ln();
        $this->Cell(64,8,utf8_decode('Cédula: _______________'),0,0,"L");
	$this->cell(126,8,'Seguro Social: _____________',0,0,'L');
        $this->Ln();
        $this->Cell(150,8,utf8_decode('Cargo Actual: _______________________________________________'),0,0,"L");
        $this->Cell(40,8,utf8_decode('Grado: ________'),0,0,"L");
        $this->Ln();       
	
        $this->Ln();
        $this->cell(80,8,utf8_decode('Cargo según estructura :'),0,0,'L');
        $this->Cell(90,8,'___________________________________________',0,0,"L");
        $this->Ln();
        
        $this->cell(80,8,utf8_decode('Cargo según funciones :'),0,0,'L');
        $this->Cell(90,8,'___________________________________________',0,0,"L");
        
        $this->Ln(); 
        $this->cell(40,8,'Salario base :',0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        $this->cell(40,8,'Ajuste :',0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        $this->cell(50,8,'porcentaje (10 - 20 14%) :',0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        $this->cell(50,8,'Salario base/Porcentaje :',0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        //
        
        
        $this->cell(50,8,'Analisis :',0,0,'L');
        //$this->Cell(45,8,'____________________',0,0,"L");
        $this->Ln(); 
        
        $headerTabla = array('fecha.', 'Grado-etapa', 'Monto', 'Resuelto y/o decreto');
        // Carga de datos
        
        $dataTable  = array(array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''),array('','','',''));
        $dataTable2Cell = array(40,30,30,90);
        $this->SetFont('Arial','',8);        
        $this->BasicTable($headerTabla,$dataTable,$dataTable2Cell);
        $this->Ln(); 
        $this->SetFont('Arial','B',10);
        $this->cell(190,8,'Bienables :',0,0,'L');
        $this->Ln();
        
        $this->cell(30,8,utf8_decode('Observación :'),0,0,'L');
        //$this->SetFont('Arial','U',10);
        $this->Cell(140,8,'__________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        
}

function imprimir_datos3($pdf)
{
	$this->pdff=$pdf;
	
	$this->Ln(20);
	$this->SetFont('Arial','B',10);
	$this->Cell(190,8,utf8_decode('Vigencias Expiradas'),0,0,"C");
        $this->Ln();
        
        
        
        $this->cell(40,8,'Nombre:',0,0,'L');
        $this->Cell(60,8,'_____________',0,0,"L");
        
        $this->cell(40,8,utf8_decode('Cédula:'),0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        
        $this->Ln();
        
        $this->cell(40,8,utf8_decode('Posición :'),0,0,'L');
        $this->Cell(60,8,'_____________',0,0,"L");
        
         $this->cell(40,8,'Seguro social:',0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        
        $this->Ln();
        
        
        
        $this->cell(40,8,utf8_decode('Planilla :'),0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        $this->cell(40,8,utf8_decode('Cargo :'),0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        $this->cell(40,8,utf8_decode('Región :'),0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        $this->cell(40,8,utf8_decode('Ubicación :'),0,0,'L');
        $this->Cell(40,8,'_____________',0,0,"L");
        $this->Ln();
        
        
        //
        
        
        $this->cell(190,8,'Monto que adude el estado',0,0,'C');        
        $this->Ln(); 
        
        $this->cell(90,8,'En concepto de: ___________________________',0,0,'L');        
        $this->Ln();
        $headerTabla = array('Periodo adecuado.', utf8_decode('salario que devengó'), utf8_decode('Categoría'), utf8_decode('Salario de debió de devengar'),utf8_decode('categoría'),'Diferencia','Periodo adeudado A/M/D','Monto a pagar');
        // Carga de datos
        
        //$this->Row(array(utf8_decode($beneficiario),'R.I.F.:  '.utf8_decode($rif)));
       
        //$bodyTable  = array(array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''),array('','','','','','','',''));
        /*$dataTable2Cell = array(23,23,23,23,23,23,23,29);
        $this->SetFont('Arial','',8);        
        $dataTable2Alto =  array(6,6,12,6,12,12,4,12);
        */
        
        $this->SetFont('Arial','',8);                     
        $this->SetWidths(array(23,23,23,23,23,23,23,29));
        $this->SetAligns(array('L','L','L','L','L','L','L','L'));
       /* $this->Setceldas(array(1,1,1,1,1,1,1,1));
        $this->Setancho(array(6,6,12,6,12,12,4,12));*/
        $this->Row($headerTabla);       
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        $this->Row(array('','','','','','','',''));
        
                        
        //$this->BasicTable2($headerTabla,$dataTable,$dataTable2Cell,$dataTable2Alto);
        
        //$this->Ln(); 
        $this->SetFont('Arial','B',10);
        $this->cell(190,8,'Bienables :',0,0,'L');
        $this->Ln();
        
        $this->cell(30,8,utf8_decode('Observación :'),0,0,'L');
        //$this->SetFont('Arial','U',10);
        $this->Cell(140,8,'__________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();
        $this->Cell(190,8,'_________________________________________________________________________________________________',0,0,"L");
        $this->Ln();         
}

// Tabla simple


function BasicTable($header, $data,$cell,$align)
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
            $this->Cell($cell[$i],6,$col,1,0,$align[$i]);
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
$sql3   = "SELECT * FROM expediente_bienal WHERE cod_expediente_det=".$_GET['codigo'];
$sql4   = "SELECT * FROM  expediente_analisis WHERE cod_expediente_det=".$_GET['codigo'];

 

$dataNompersonal = $db->query($sql1);
$dataExpediente  = $db->query($sql2);
$dataBianal      = $db->query($sql3);
$dataAnalisis    = $db->query($sql4);

$pdf->imprimir_datos($pdf,$dataExpediente, $dataNompersonal, $dataBianal,$dataAnalisis);


ob_end_clean();
$pdf->Output();
?>
