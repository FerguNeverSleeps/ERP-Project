<?php
session_start();
ob_start();

require('fpdf.php');
include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if (!empty($value))
        return substr($value, 8, 2) . "/" . substr($value, 5, 2) . "/" . substr($value, 0, 4);
}
$nomina_id = $_GET['nomina_id'];
$codt = $_GET['codt'];

class PDF extends FPDF {

    function header()
    {
        $nomina_id = $_GET['nomina_id'];
        $codt = $_GET['codt'];
        $Conn = conexion();
        $var_sql = "select * from nomempresa";
        $rs = query($var_sql, $Conn);
        $row_rs = fetch_array($rs);
        $var_izquierda = '../imagenes/' . $row_rs['imagen_izq'];

        $consulta = "SELECT periodo_ini,periodo_fin FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codt."'";
        $resultado = query($consulta,$Conn);
        $fetch = fetch_array($resultado);
        $fecha_ini = $fetch['periodo_ini'];
        $fecha_fin = $fetch['periodo_fin'];

        $this->SetFont("Arial", "B", 12);
        $this->Cell(150, 5, $row_rs['nom_emp'], 0, 0, 'L');
        $this->SetFont("Arial",'', 10);
        $this->Cell(60, 5,'FECHA',0,0,'R');
        $this->Cell(60, 5,'',0,1,'R');
        $this->SetFont("Arial", "B", 12);
        $this->Cell(150, 5,'IMPUTACION A LAS PARTIDAS DEL PRESUPUESTO', 0, 0, 'L');
        $this->SetFont("Arial",'', 10);
        $this->Cell(30, 5,'Dia  Mes  Ano', 0, 0, 'R');
        $this->Cell(30, 5,'No. Cheque', 0, 0, 'R');
        $this->Cell(30, 5,'No. Auditor', 0, 0, 'R');
        $this->Cell(30, 5,'No. Planilla', 0, 1, 'R');
        $this->SetFont("Arial", "B", 12);
        $this->Cell(150, 5,'TRANSFERENCIAS', 0, 0, 'L');
        $this->SetFont("Arial",'', 10);
        $this->Cell(30, 5,date('d   m  Y'), 0, 0,'R');
        $this->Cell(30, 5,'_', 0, 0,'R');
        $this->Cell(30, 5,'_', 0, 0,'R');
        $this->Cell(30, 5,'_', 0, 1,'R');
        $this->Ln(10);
        $this->SetFont("Arial", "B", 8);
        $this->SetWidths(array(45,25,25,25,25,25,25,25,25,28,28,25));
        $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0'));
        $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
        $this->Row(array('PARTIDA','TOTAL BRUTO','S.SOCIAL.EMP','SIACAP EMP','Impuesto Renta','OBLIGACIONES','TOTAL NETO','SEGURO SOCIAL PRATRONO','SEGURO EDUC PRATRONO','RIESGO PROFESIONAL','SIACAP PRATRONO','TOTALES'));
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

    function personas($nomina_id, $codt, $pdf)
    {
        $conexion = conexion();
        $sql = "SELECT MAX(codorg) AS codorg FROM nomnivel1";
        $result1 = query($sql, $conexion);
        $max = fetch_array($result1);

        $query2 = "SELECT a.ficha,a.nombres, a.apellidos,a.cedula,a.seguro_social,a.suesal,c.codorg,c.descrip,c.markar FROM nompersonal AS a,nom_movimientos_nomina AS b,nomnivel1 AS c WHERE a.ficha = b.ficha AND a.estado = 'Activo' AND b.tipnom = '".$codt."' AND a.codnivel1 = c.codorg GROUP BY a.ficha ORDER BY a.ficha";
        $result = query($query2, $conexion);
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
        $lin=0;
            while ($fila = fetch_array($result))
            {
                    $persona+=1;
                    $sficha = $fila['ficha'];
                    $salario_bruto=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=100";
                    $result2 = query($query, $conexion);                
                    $fetch2=fetch_array($result2);
                    $salario_bruto=$fetch2['monto'];

                    $ss=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=200";
                    $result4 = query($query, $conexion);                
                    $fetch4=fetch_array($result4);
                    $ss=$fetch4['monto'];

                    $se=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=201";
                    $result5 = query($query, $conexion);
                    $fetch5=fetch_array($result5);
                    $se=$fetch5['monto'];

                    $isr=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=202";
                    $result3 = query($query, $conexion);                
                    $fetch3=fetch_array($result3);
                    $isr=$fetch3['monto'];

                    $fc=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=204";
                    $result6 = query($query, $conexion);
                    $fetch6=fetch_array($result6);
                    $fc=$fetch6['monto'];
                    //Calculos de pratrono
                    //----------------------------------------------------------------------------------------------------------------
                    $ssp=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=3000";
                    $result4 = query($query, $conexion);                
                    $fetch4=fetch_array($result4);
                    $ssp=$fetch4['monto'];

                    $sep=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=3001";
                    $result5 = query($query, $conexion);
                    $fetch5=fetch_array($result5);
                    $sep=$fetch5['monto'];

                    $rp=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=3002";
                    $result6 = query($query, $conexion);
                    $fetch6=fetch_array($result6);
                    $fcp=$fetch6['monto'];

                    $fcp=0;
                    $query = "select monto from nom_movimientos_nomina 
                where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=3003";
                    $result6 = query($query, $conexion);
                    $fetch6=fetch_array($result6);
                    $fcp=$fetch6['monto'];
                    //----------------------------------------------------------------------------------------------------------------
                    $ob=0;
                    $query = "select ifnull(sum(monto),0) as monto from nom_movimientos_nomina where ficha = '".$sficha."' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (200,201,202,204)";
                    $result7 = query($query, $conexion);
                    $fetch7=fetch_array($result7);
                    $ob=$fetch7['monto'];

                    $total_deduccion = $isr+$ss+$se+$fc+$ob;
                    $salario_neto =  $salario_bruto-$total_deduccion;

                    if ($fila["codorg"] > $j)
                    {
                        if ($lin == 14)
                        {
                            $this->AddPage('L', 'LEGAL','A4');
                            $lin=0;
                        }
                        $lin=$lin+1;
                    $this->SetFont("Arial",'',10);
                    $this->SetWidths(array(45,25,25,25,25,25,25,25,25,28,28,25));
                    $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
                    $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0'));
                    $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
                    $this->Row(array($partida,$sub_total_sq,$sub_total_ss,$sub_total_fc,$sub_total_isr,$sub_total_ob,$sub_total_neto,$sub_total_ssp,$sub_total_sep,$sub_total_rp,$sub_total_fcp,$sub_total_planilla));
                    $this->SetWidths(array(330));
                    $this->SetAligns(array('C' ));
                    $this->Setceldas(array('B'));
                    $this->Setancho(array(2));
                    $this->Row(array(''));
                        $j=$fila["codorg"];
                        $sub_i=0;
                        $sub_total_sq   = 0;
                        $sub_total_ss   = 0;
                        $sub_total_fc   = 0;
                        $sub_total_isr  = 0;
                        $sub_total_ob   = 0;
                        $sub_total_neto = 0;
                        $sub_total_ssp  = 0;
                        $sub_total_sep  = 0;
                        $sub_total_rp   = 0;
                        $sub_total_fcp  = 0;
                        $sub_total_planilla = 0;
                    }
                if ($fila['codorg'] >= $j)
                {
                        
                    $partida = $fila['codorg']." ".$fila['markar'];
                    $sub_i=$sub_i+1;
                    if($ot>0)
                    {
                        $query = "select codcon, descrip, monto from nom_movimientos_nomina where ficha = '".$sficha."' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (200,201,202,204)";
                        $result8 = query($query, $conexion);
                        while($fetch8=fetch_array($result8))
                        {

                        }
                    }
                    $i=$i+1;
                    //-----------------------------------------------------------------------------------------------------------------------------------------
                    $sub_total_sq    = $sub_total_sq+$salario_bruto;
                    $sub_total_ss    = $sub_total_ss+$ss;
                    $sub_total_fc    = $sub_total_fc+$fc;
                    $sub_total_isr   = $sub_total_isr+$isr;
                    $sub_total_ob    = $sub_total_ob+$ob;
                    $sub_total_neto  = $sub_total_neto+$salario_neto;
                    $sub_total_ssp   = $sub_total_ssp+$ssp;
                    $sub_total_sep   = $sub_total_sep+$sep;
                    $sub_total_rp    = $sub_total_rp+$rp;
                    $sub_total_fcp   = $sub_total_fcp+$fcp;
                    $sub_total_planilla = $sub_total_ssp + $sub_total_sep + $sub_total_rp + $sub_total_fcp;
                    //------------------------------------------------------------------------------------------------------------------------------------------
                    $total_sq    = $total_sq+$salario_bruto;
                    $total_ss    = $total_ss+$ss;
                    $total_fc    = $total_fc+$fc;
                    $total_isr   = $total_isr+$isr;
                    $total_ob    = $total_ob+$ob;
                    $total_neto  = $total_neto+$salario_neto;
                    $total_ssp   = $total_ssp+$ssp;
                    $total_sep   = $total_sep+$sep;
                    $total_rp    = $total_rp+$rp;
                    $total_fcp   = $total_fcp+$fcp;
                    $total_planilla = $total_ssp + $total_sep + $total_rp + $total_fcp;
                    //-----------------------------------------------------------------------------------------------------------------------------------------
                }
                
            }
            
            $this->SetWidths(array(45,25,25,25,25,25,25,25,25,28,28,25));
            $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0'));
            $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
            $this->Row(array($partida,$sub_total_sq,$sub_total_ss,$sub_total_fc,$sub_total_isr,$sub_total_ob,$sub_total_neto,$sub_total_ssp,$sub_total_sep,$sub_total_rp,$sub_total_fcp,$sub_total_planilla));
            $this->SetWidths(array(330));
            $this->SetAligns(array('C' ));
            $this->Setceldas(array('B'));
            $this->Setancho(array(2));
            $this->Row(array(''));
                $j=$fila["codorg"];
                $sub_i=0;
                $sub_total_sq   = 0;
                $sub_total_ss   = 0;
                $sub_total_fc   = 0;
                $sub_total_isr  = 0;
                $sub_total_ob   = 0;
                $sub_total_neto = 0;
                $sub_total_ssp  = 0;
                $sub_total_sep  = 0;
                $sub_total_rp   = 0;
                $sub_total_fcp  = 0;
                $sub_total_planilla = 0;
            $this->SetFont("Arial",'',12);
            $this->SetWidths(array(45,25,25,25,25,25,25,25,25,28,28,25));
            $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0'));
            $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
            $this->Row(array('TOTALES',$total_sq,$total_ss,$total_fc,$total_isr,$total_ob,$total_neto,$total_ssp,$total_sep,$total_rp,$total_fcp,$total_planilla));
    }
    function firmas()
    {
        $this->Ln(5);
        $this->SetFont("Arial",'',14);
        $this->SetWidths(array(90,20,60,60,60));
        $this->SetAligns(array('L','C','C','C','C'));
        $this->Setceldas(array('0','0','0','0','0'));
        $this->Setancho(array(5, 5, 5, 5, 5));
        $this->Row(array('Seccion de Tramites (Sellos)','','','',''));
        $this->Ln(15);

        $this->SetFont("Arial",'',12);
        $this->SetWidths(array(60,30,60,60,60,60));
        $this->SetAligns(array('L','C','C','C','C','C'));
        $this->Setceldas(array('0','0','0','0','0','0'));
        $this->Setancho(array(5,5,5,5,5,5));
        $this->Row(array('___________________','','___________________','_______________','___________________','_______________'));
        $this->Row(array('Confeccionado por:','','Alcalde Municipal','Fecha de Firma','Jefe de Contabilidad','Fecha de Firma'));
        $this->Ln(10);
        $this->Row(array('___________________','','___________________','_______________','',''));
        $this->Row(array('Dir. De Rec Humanos','','Jefe de Fiscalizacion','Fecha de Firma','',''));
        $this->Ln(10);
        $this->SetWidths(array(90,30,90,60,30,30));
        $this->SetAligns(array('L','C','C','C','C','C'));
        $this->Setceldas(array('0','0','0','0','0','0'));
        $this->Setancho(array(5,5,5,5,5,5));
        $this->Row(array('Monto: _________ Fecha: _________','','Autorizado Por:__________________','','',''));
        $this->Row(array('','','                     Tesorero Municipal','','',''));
    }
}
//Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', 'LEGAL','A4');
$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);
$pdf->personas($nomina_id, $codt, $pdf);
$pdf->firmas();
$pdf->Output();
?>