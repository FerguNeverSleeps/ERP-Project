<?php
if (!isset($_SESSION)) {
  session_start();
}
require('../../nomina/fpdf/fpdf.php');

require_once '../../nomina/lib/config.php';
require_once '../../nomina/lib/pdfcommon.php';
require_once '../../nomina/lib/common.php';

include ("../../nomina/paginas/funciones_nomina.php");

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
$cantidad_registros=13;
//error_reporting(0);
$conexion=conexion();


function fechas($fecha,$formato)
{
    $meses = array("ene","feb","mar","abr","may","jun","jul","ago","sep","oct","nov","dic");
    if (strlen($fecha)<2) {
        return "";
    }
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
         case 3:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia."-".$meses[$mes-1]. "-".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}


function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{
//Cabecera de página
function header(){




    $conexion=conexion();
    $var_sql="select * from nomempresa";
    $rs = query($var_sql,$conexion);
    $row_rs = fetch_array($rs);
    $var_encabezado=$row_rs['nom_emp'];
    $var_imagen_izq=$row_rs['imagen_izq'];

    $this->SetFont('Arial','',10);
    $date1=date("Y-m-d");
    $date2=date("h:m:s");
    if($var_imagen_izq!='' && file_exists("../../nomina/imagenes/".$var_imagen_izq)){
        $this->Image("../../nomina/imagenes/".$var_imagen_izq,8,6,25);
    }
    $this->ln();
    $this->Cell(30);
    $this->Cell(120,5,utf8_decode($var_encabezado),0,0,'L');
    $this->Cell(19,5,'Fecha:  ',0,0,'C');
    $this->Cell(19,5,fechas($date1,3),0,1,'C');
    $this->Cell(30);
    $this->Cell(120,5,'RECURSOS HUMANOS',0,0,'L');
    $this->Cell(19,5,'Hora:  ',0,0,'C');
    $this->Cell(19,5,$date2,0,1,'C');
    $this->Ln(10);


}

//Pie de página
function Footer()
{
    //Posición: a 1,5 cm del final
    $this->SetY(-15);

     $this->SetFont('Arial','I',8);
    $this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
    $this->Cell(0,5,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');

    //Número de página
   // $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
//Hacer que sea multilinea sin que haga un salto de linea
var $widths;
var $aligns;
var $celdas;
var $ancho;
var $nro_ocs;
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
// Marco de la celda
function Setceldas($cc)
{

    $this->celdas=$cc;
}
// Ancho de la celda
function Setancho($aa)
{
    $this->ancho=$aa;
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
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        //$this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,$this->ancho[$i],$data[$i],$this->celdas[$i],$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}
//fin

function documentos($ficha, $cedula)
{
    $conexion = conexion();
    //mysqli_set_charset($conexion, "utf8");

    $sql = "SELECT imagen_cedula, apenom FROM nompersonal WHERE ficha='{$ficha}'";
    $res = query($sql, $conexion);
    $integrante = $res->fetch_object();

    $this->AddPage();

    //$this->Ln(5);
    $this->SetFont('Arial','B',12);
    $this->Cell(188,5,'DOCUMENTOS',0,1,'C');

    if(isset($integrante->imagen_cedula))
    {
        $dir = '../paginas/';

        if(file_exists($dir . $integrante->imagen_cedula))
        {
            $this->Image($dir . $integrante->imagen_cedula, 55, 45, 100, 65);

            $this->SetFont('Arial','B',11);
            $this->SetXY(80, 118);
            $this->Write(0, 'Nombre:');
            $this->SetFont('Arial','',11);
            $this->SetXY(100, 118);
            $this->Write(0, utf8_decode("Cédula"));
        }
    }

    $sql = "SELECT *
            FROM  nomexpediente_documentos
            WHERE cod_expediente_det IN (SELECT cod_expediente_det FROM nomexpediente WHERE cedula='{$cedula}' AND tipo_registro='Documentos')";

    $res = query($sql, $conexion);
    $this->Ln(30);

    $i=0;

    while($fila = $res->fetch_assoc())
    {

        $dir = '../expediente/';

        if(file_exists($dir . $fila['url_documento']))
        {

            if($i==1)
                $this->AddPage();
            else if($i!=0)
            {
                if($i%2!=0)
                    $this->AddPage();
                else
                    $this->Ln(30);
            }

            $fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $fila['fecha_vencimiento']);
            $fecha_vencimiento = ($fecha_vencimiento !== false) ? $fecha_vencimiento->format('d/m/Y') : '';

            $this->Image($dir . $fila['url_documento'], 55, $this->GetY(), 100, 65);
            $this->SetXY(80, $this->GetY()+73);
            $this->SetFont('Arial','B',11);
            $this->Write(0, 'Nombre:');
            $this->SetX(100);
            $this->SetFont('Arial','',11);
            $this->Write(0, utf8_decode($fila['nombre_documento']));
            $this->Ln(6);
            $this->SetX(80);
            $this->SetFont('Arial','B',11);
            $this->Write(0, 'Vence:');
            $this->SetX(100);
            $this->SetFont('Arial','',11);
            $this->Write(0, $fecha_vencimiento);

            $i++;
        }
    }
}

function datos($ficha,$cedula){
    $conexion=conexion();
    $tipo=$_SESSION['codigo_nomina'];
    $consulta="select * from nompersonal where ficha='{$ficha}' and cedula='{$cedula}' ";
    $resultado=query($consulta,$conexion);
    $rc=fetch_array($resultado);
    $this->Ln(20);
    $this->SetFont('Arial','B',14);
    $this->Cell(188,5,utf8_decode('DATOS GENERALES '),0,1,'C');
    $this->Ln(10);

    if($rc['foto']!='fotos/' && $rc['foto']!=''){
        if(file_exists("../../nomina/paginas/".$rc['foto'])){
            $this->Image("../../nomina/paginas/".$rc['foto'],150,25,33);
        }else{
            $this->Image("../../nomina/paginas/fotos/silueta.gif",150,30,43);
            //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
        }
    }else{
            //$this->Image("../paginas/fotos/silueta.gif" ,160,30,43);
            $this->Image('../../includes/assets/img/profile/profile_black.jpg',153,40,43);
            //$this->Image('../../includes/assets/img/profile/profile.png',160,30,43);
            //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
        }
        $this->SetFont('Arial','B',10);
    $this->SetFillColor(194,194,194);
    $this->Cell(97,5,'APELLIDOS Y NOMBRES ',1,0,'C',true);
    $this->Cell(37,5,utf8_decode('NO. DE CIP '),1,0,'C',true);
    $this->Ln();
    // llamado para hacer multilinea sin que haga salto de linea
    $this->SetWidths(array(97,37));
    $this->SetAligns(array('C','C'));
    $this->Setceldas(array(1,1));

    $this->SetFont('Arial','',10);
    $this->Cell(97,10,utf8_decode($rc['apenom']),1,0,'C',false);
    $this->Cell(37,10,$rc['cedula'],1,0,'C',false);
    $this->Ln();
    $this->Setancho(array(5,5));

    $query="select * from nomcargos where cod_car = '".$rc['codcargo']."'";
    $resultado1 = query($query,$conexion);
    $cargo1 = fetch_array($resultado1);
    $cargo=$cargo1['des_car'];

    $this->SetFont('Arial','B',10);
    $this->Cell(48.5,5,'FECHA DE INGRESO',1,0,'C',true);
    $this->Cell(48.5,5,utf8_decode('CARGO'),1,0,'C',true);
    $this->Cell(37,5,utf8_decode('Nº DE UNIDAD'),1,0,'C',true);
    $this->ln();

   $this->SetWidths(array(48.5,48.5,37));
   $this->SetAligns(array('C','C','C'));
   $this->Setancho(array(5,5,5));
   $this->Setceldas(array(1,1,1));
   $this->Setceldas(array(1,1,1));





    $this->SetFont('Arial','',10);


   if($rc[nacionalidad]==0){
       $nacio='Extranjero';
   }Else{
       $nacio='Panameño';
   }
   $this->Cell(48.5,10,fechas($rc['fecing'],3),1,0,'C',false);
   $this->Cell(48.5,10,$cargo,1,0,'C',false);
   $this->Cell(37,10,$ficha,1,0,'C',false);
   $this->Ln();



    $this->SetWidths(array(64,64,64));
    $this->SetAligns(array('C','C','C'));
        $this->Setancho(array(5,5,5));
    $this->SetFont('Arial','B',10);
    $this->Setceldas(array(1,1,1));
    $this->Cell(64,5,'NACIONALIDAD',1,0,'C',true);
    $this->Cell(64,5,utf8_decode('SEXO'),1,0,'C',true);
    $this->Cell(64,5,utf8_decode('ESTADO CIVIL'),1,0,'C',true);
    $this->ln();


    $this->SetFont('Arial','',10);
    $this->Setceldas(array(1,1,1));
    if($rc[nacionalidad]==0){
        $nacio='Extranjero';
    }Else{
        $nacio='Panameño';
    }
    $this->Cell(64,10,utf8_decode($nacio),1,0,'C',false);
    $this->Cell(64,10,$rc['sexo'],1,0,'C',false);
    $this->Cell(64,10,$rc['estado_civil'],1,0,'C',false);
    $this->Ln();


    $this->SetWidths(array(64,64,64));
    $this->SetAligns(array('C','C','C'));
        $this->Setancho(array(5,5,5));
    $this->SetFont('Arial','B',10);
    $this->Setceldas(array(1,1,1));
    $this->Cell(64,5,utf8_decode('FECHA DE NACIMIENTO'),1,0,'C',true);
    $this->Cell(64,5,utf8_decode('EDAD'),1,0,'C',true);
    $this->Cell(64,5,utf8_decode('LUGAR DE NACIMIENTO'),1,0,'C',true);
    $this->ln();
    $this->SetFont('Arial','',10);

    $edad=antiguedad($rc['fecnac'],date('Y-m-d'),"A");

    if ($edad!=0) {
        $edad.=" años";
    }
    $this->Setceldas(array(1,1,1));
    $this->Cell(64,10,fechas($rc['fecnac'],3),1,0,'C',false);
    $this->Cell(64,10,utf8_decode($edad),1,0,'C',false);
    $this->Cell(64,10,$rc['lugarnac'],1,0,'C',false);
    $this->Ln();


    $aux = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"A"));
    if ($aux > 1) {
      $aux = $aux." años, ";
    }
    else
    {
      $aux = $aux." año, ";
    }

    $aux2 = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"M"));

    if ($aux2 > 1) {
      $aux2 = $aux2." meses y ";
    }
    else
    {
      $aux2 = "y ";
    }

    $aux3 = ($rc['antiguedadap'] + antiguedad($rc['fecing'],date("Y-m-d"),"D"));

    $aux4 = $aux3 % 30;
    if ($aux4 > 1) {
      $aux4 = $aux4." dias";
    }
    else
    {
      $aux4 = $aux4." dia";
    }


    $this->SetWidths(array(64,64,64));
    $this->SetAligns(array('C','C','C'));
    $this->Setancho(array(5,5,5));
    $this->SetFont('Arial','B',10);
    $this->Setceldas(array(1,1,1));

    $this->Cell(64,5,utf8_decode('TIPO DE SANGRE'),1,0,'C',true);
    $this->Cell(64,5,utf8_decode('TIEMPO DE SERVICIO'),1,0,'C',true);
    $this->Cell(64,5,utf8_decode('PROX. VACACIONES'),1,0,'C',true);
    $this->ln();

    $query="select * from tiposangre where IdTipoSangre = '".$rc['IdTipoSangre']."'";
    $resultado2 = query($query,$conexion);
    $aux5 = fetch_array($resultado2);
    $tiposangre=$aux5['Descripcion'];

    $this->SetFont('Arial','',10);
    $this->Setceldas(array(1,1,1));
    $edad=antiguedad($rc['fecnac'],date('Y-m-d'),"A");
    $this->Cell(64,10,utf8_decode($tiposangre),1,0,'C',false);
    $this->Cell(64,10,utf8_decode($aux.$aux2.$aux4),1,0,'C',false);
    $this->Cell(64,10,"",1,0,'C',false);
    $this->Ln();


    $this->SetWidths(array(64,128));
    $this->SetAligns(array('C','C'));
    $this->Setancho(array(5,5));
    $this->SetFont('Arial','B',10);
    $this->Setceldas(array(1,1));
    $this->Cell(64,5,utf8_decode('TELÉFONO'),1,0,'C',true);
    $this->Cell(128,5,utf8_decode('EMAIL'),1,0,'C',true);

    $this->ln();
    $this->SetFont('Arial','',10);
    $this->Setceldas(array(1,1));
    $cod_prof=$rc['codpro'];
    $query="select codorg,descrip from nomprofesiones where codorg='{$cod_prof}'";
    $result=query($query,$conexion);
    $rp=fetch_array($result);
    $this->Cell(64,10,utf8_decode($rc['telefonos']),1,0,'C',false);
    $this->Cell(128,10,utf8_decode($rc['email']),1,0,'C',false);
    $this->Ln();

    $this->SetFont('Arial','B',10);
    $this->Cell(192,5,utf8_decode('DIRECCIÓN'),1,1,'C',true);
    $this->SetFont('Arial','',10);
    $this->MultiCell(192,10,utf8_decode($rc['direccion']),1,'C');

}

function logros($cedula){
    $conexion=conexion();
    //$this->Ln(300);
    $this->AddPage();

    $query="select * from nomexpediente where cedula='{$cedula}' AND tipo_registro<>'Documentos' ORDER BY tipo_tiporegistro, fecha_salida";
    $resex=query($query,$conexion);
    //$this->Ln(5);
    $this->SetFont('Arial','B',12);
    $this->Cell(188,5,'LOGROS',0,1,'C');
    $this->SetFont('Arial','I',12);
    $this->Ln(10);
    $cantidad_registros=28;
    $totalwhile=num_rows($resex);
    $contar=1;
    $conta=1;
    $anterior=0;

    while($totalwhile>=$contar)
    {

        $fila=fetch_array($resex);

        //if($anterior!=$fila['tipo_tiporegistro']){

            $this->SetFont('Arial','B',12);
            $this->Ln(5);
            $this->Cell(188,5,$rre['descrip'],0,1,'L');
            $this->Ln(5);
            $this->SetFont('Arial','I',12);
            $cantidad_registros-=1;
            $conta=1;

        $this->SetFont('Arial','',12);
        $this->MultiCell(188,5,$conta.'.- ( '.$fila['tipo_registro'].' ) '.utf8_decode($fila['descripcion']),0,'J');
        if($conta==$cantidad_registros){
            $this->Ln(300);

            $this->SetFont('Arial','',12);
            $this->Cell(188,5,'LOGROS',0,1,'C');

        }
        $conta++;
        $contar++;



  }
}

function observacion($cedula){
    $conexion=conexion();
    //$this->Ln(300);
    $this->AddPage();
    $query="select * from nomexpediente where cedula='{$cedula}' and tipo_registro='Observacion' ORDER BY fecha_salida ";
    $resex=query($query,$conexion);
    $this->Ln(5);
    $this->SetFont('Arial','B',12);
    $this->Cell(188,5,'OBSERVACIONES',0,1,'C');
    $this->SetFont('Arial','I',12);
    $this->Ln(10);
    $cantidad_registros=28;
    $totalwhile=num_rows($resex);
    $contar=1;
    $conta=1;
    $anterior=0;
    while($totalwhile>=$contar)
    {

        $fila=fetch_array($resex);


        $this->SetFont('Arial','',12);
        $this->MultiCell(188,5,$conta.'.- ( '.fecha($fila['fecha_salida']).' ) '.utf8_decode($fila['descripcion']),0,'J');
        if($conta==$cantidad_registros){
            $this->Ln(300);

            $this->SetFont('Arial','',12);
            $this->Cell(188,5,'OBSERVACIONES',0,1,'C');


        }
        $conta++;
        $contar++;


    }

}


}


//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$ficha=$_GET['ficha'];
$cedula=$_GET['cedula'];
$conexion=conexion();
$pdf->datos($ficha,$cedula);


$pdf->Output();
?>