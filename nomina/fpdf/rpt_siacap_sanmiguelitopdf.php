<?php
session_start();
ob_start();

require('fpdf.php');
include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if (!empty($value))
        return substr($value, 8, 2) . "/" . substr($value, 5, 2) . "/" . substr($value, 0, 4);
}
$anio = $_POST['anio'];
$mes = $_POST['mes'];

class PDF extends FPDF {

    function header()
    {
        $anio = $_POST['anio'];
        $mes = $_POST['mes'];
        $fecha_ini = '01-'.$mes.'-'.$anio;
        $Conn = conexion();
        $var_sql = "select * from nomempresa";
        $rs = query($var_sql, $Conn);
        $row_rs = fetch_array($rs);
        $var_izquierda = '../imagenes/' . $row_rs['imagen_izq'];

        $consulta = "SELECT periodo_fin FROM nom_nominas_pago WHERE anio = '".$anio."' AND mes = '".$mes."'";
        $resultado = query($consulta,$Conn);
        //$fetch = fetch_array($resultado);
        while ($fila = fetch_array($resultado))
        {
            $fecha_fin = $fila['periodo_fin'];
        }
        $this->SetFont("Arial", "B", 14);
        $this->Cell(0, 5, $row_rs['nom_emp'], 0, 1, 'L');
        $this->SetFont("Arial",'', 14);
        $this->Cell(0, 5, 'SIACAP-APORTES MENSUALES', 0, 1, 'L');
        $this->Cell(0, 5, 'PLANILLA CORRESPONDIENTE AL PERIODO DEL', 0, 1, 'L');
        $this->Cell(0, 5, $fecha_ini.' al '.$fecha_fin, 0, 1, 'L');
        $this->SetFont("Arial", "", 10);
        $this->Ln(5);
        $this->SetFont("Arial", "B", 8);
        $this->SetWidths(array(18,20,20,15,11,18,25,20,20,25,25,20,17,27));
        $this->SetAligns(array('R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0','0','0'));
        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5,5));
        $this->Row(array('NUM EMPLEADO','CEDULA','SEGURO SOCIAL','SALARIO','DOS%','TREINTA%','RECARGO DEL 1%(por cada mes de atraso)','RECARGO DEL 10%','TOTALES','NOMBRE','APELLIDO','#PATRONAL','FECHA DEL MES','OBSERVACIONES'));
        $this->Ln(4);
    }
//Hacer que sea multilinea sin que haga un salto de linea
    var $widths;
    var $aligns;
    var $celdas;
    var $ancho;

    function SetWidths($w) {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        //Set the array of column alignments
        $this->aligns = $a;
    }

// Marco de la celda
    function Setceldas($cc) {

        $this->celdas = $cc;
    }

// Ancho de la celda
    function Setancho($aa) {
        $this->ancho = $aa;
    }

    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l+=$cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                }
                else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    function Row($data) {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            //$this->Rect($x,$y,$w,$h);
            //Print the text
            $this->MultiCell($w, $this->ancho[$i], $data[$i], $this->celdas[$i], $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

//fin

    function personas($anio,$mes,$pdf)
    {
        $conexion = conexion();
        $sql = "SELECT MAX(codorg) AS codorg FROM nomnivel1";
        $result1 = query($sql, $conexion);
        $max = fetch_array($result1);

        $query2 = "SELECT a.ficha,a.nombres,a.apellidos,a.cedula,a.seguro_social,a.suesal,a.codnivel1 FROM nompersonal AS a,nomnivel1 AS b WHERE a.codnivel1 = b.codorg ORDER BY ficha ASC";
        $result = query($query2, $conexion);

        $consulta = "SELECT codnom,fechapago,codtip FROM nom_nominas_pago WHERE anio = '".$anio."' AND mes = '".$mes."'";
        $resultado = query($consulta,$conexion);
        $a=1;
        while ($fetch = fetch_array($resultado))
        {
            $fecha_pago = $fetch[fechapago];
            if ($a == 1)
            {
                $cod_nomina1 = $fetch[codnom];
                $cod_tipo1 = $fetch[codtip];
            }elseif ($a == 2) 
            {
                $cod_nomina2 = $fetch[codnom];
                $cod_tipo2 = $fetch[codtip];
            }
            $a=$a+1;
        }
        $contador = 1;
        $num = 1;
        $posicion= "";
        $persona = 0;
        $totalpersonas = 0;
        $totalbd = num_rows($result);
        $pers = 0;
        $i=0;
        $sub_i=0;
        $j=1;
        while ($fila = fetch_array($result))
        {
            $sficha = $fila[ficha];
                    $salario_bruto=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$cod_tipo1."' and codnom = '".$cod_nomina1."' AND codcon=100";
                    $result2 = query($query, $conexion);                
                    $fetch2=fetch_array($result2);
                    $salario1=$fetch2[monto];

            $sficha = $fila[ficha];
                    $salario_bruto=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$cod_tipo2."' and codnom = '".$cod_nomina2."' AND codcon=100";
                    $result3 = query($query, $conexion);                
                    $fetch3=fetch_array($result3);
                    $salario2=$fetch3[monto];

            $salario=$salario1+$salario2;
            $dos=$salario*0.02;
            $treinta=$dos*0.30;
            $total=$dos+$treinta;
            $this->SetFont("Arial", "B", 8);
            $this->SetWidths(array(18,20,20,15,11,18,25,20,20,25,25,20,17,27));
            $this->SetAligns(array('R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0','0','0'));
            $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5,5));
            $this->Row(array($fila[ficha],$fila[cedula],$fila[seguro_social],$salario,$dos,$treinta,'0','0',$total,$fila[nombres],$fila[apellidos],'878100038',$fecha_pago,'Afiliado Recurrente'));
            $this->Ln(1);
        }
    }
}
//Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', 'A4');
$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);
$pdf->personas($anio,$mes,$pdf);
$pdf->Output();
?>
