<?php
session_start();
ob_start();
##$termino = $_SESSION['termino'];
#$tipo = $_GET['tipo'];
#echo $termino,'-',$tipo;exit;
$nomina_id = $_GET['nomina_id'];
$codt = $_SESSION['codigo_nomina'];

require('fpdf.php');
include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if (!empty($value))
        return substr($value, 8, 2) . "/" . substr($value, 5, 2) . "/" . substr($value, 0, 4);
}

class PDF extends FPDF {

    function header() {
        $Conn = conexion();
        $var_sql = "select * from nomempresa";
        $rs = query($var_sql, $Conn);
        $row_rs = fetch_array($rs);
        #$var_encabezado1 = $row_rs['nom_emp'];
        $var_izquierda = '../imagenes/' . $row_rs['imagen_izq'];
        #$var_derecha='../imagenes/'.$row_rs[imagen_der];
        #$query = "select * from nom_nominas_pago where codnom = '" . $_GET['nomina_id'] . "' AND codtip= '" . $_SESSION['codigo_nomina'] . "' ";
        #$result2 = query($query, $Conn);
        #$fila2 = fetch_array($result2);

        $this->SetFont("Arial", "", 10);

        //$this->Cell(100,6,$var_encabezado1,0,0,"L");
       // $this->Image($var_izquierda, 10, 6, 40, 15);
        //$this->Image($var_derecha,170,6,30,15);
        $this->SetFont("Arial", "", 14);
        $this->Cell(180, 5, $row_rs['nom_emp'], 0, 1, 'L');
        $this->Cell(180, 5, 'DIRECCION DE RECURSOS HUMANOS', 0, 1, 'L');
        $this->Cell(180, 5, 'DETALLE DE PLANILLA', 0, 1, 'L');
        $this->SetFont("Arial", "", 10);
        //$this->Ln(5);
        //$this->Cell(180, 6, $_SESSION['termino'] . ': ' . $_GET['nomina_id'] . ' - ' . $_SESSION['nomina'], 0, 0, 'C');
        $this->Ln(5);
        $this->SetFont("Arial", "", 8);

      //  $this->SetFont('Arial', '', 8);
        $this->SetWidths(array(30, 60, 18,18,18, 18,18,18, 18,18,18,18,18));
        $this->SetAligns(array('L', 'C', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'C' ));
        $this->Setceldas(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
        $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
        $this->Row(array('Posicion', 'Clave', 'S. Mensual', 'S. Quincenal', 'Isr', 'Ss', 'Se', 'Fc', '', 'T. Des.', 'M. Cheque', 'Cheque'));
        $this->Ln(5);
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

    function personas($nomina_id, $codt, $pdf) {

        $conexion = conexion();
        $query = "select per.apenom, per.cedula, per.ficha, per.suesal, per.nomposicion_id as posicion, npos.descripcion_posicion as dposicion, cue.CodCue, cue.Denominacion as descue from nompersonal as per LEFT join nom_movimientos_nomina as nom on (per.ficha = nom.ficha)  join nomposicion npos on (npos.nomposicion_id=per.nomposicion_id) left join cwprecue cue on (cue.CodCue=npos.partida) where  nom.codnom = '$nomina_id' and nom.tipnom = " . $codt . " and per.tipnom=" . $codt . "  group by per.ficha order by per.nomposicion_id,per.ficha,  per.apenom";

        $result = query($query, $conexion);


        $query = "select * from nom_nominas_pago where codnom = '" . $nomina_id . "' AND codtip= '" . $codt . "' ";
        $result2 = query($query, $conexion);
        $fila2 = fetch_array($result2);
        $contador = 1;
        $num = 1;
        $posicion= "";
        $persona = 0;
        $totalpersonas = 0;
        $totalbd = num_rows($result);
        $pers = 0;
        $CANTIDAD = 52;
        while ($fila = fetch_array($result)) 
        {

            if ($posicion != $fila['CodCue']) {
                
                if ($posicion != "")
                {

                    $this->Ln(250);
                    $this->SetFont('Arial', '', 8);
                    $this->Cell(100, 5,$_SESSION['nomina'], 0, 1, 'L');
                    $this->Cell(100, 5, $fila['CodCue'].'  '.$fila['descue'], 0, 1, 'L');
                }
                else
                {
                    $this->SetFont('Arial', '', 8);
                    $this->Cell(100, 5,$_SESSION['nomina'], 0, 1, 'L');
                    $this->Cell(100, 5, $fila['CodCue'].'  '.$fila['descue'], 0, 1, 'L');
                }
                $posicion=$fila['CodCue'];
            }
 


            //if ($gertmp != $fila['codnivel3']) {
                $persona+=1;



                $sficha = $fila['ficha'];

                
                $squincenal=0;
                $query = "select monto from nom_movimientos_nomina 
            where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=100";
                $result2 = query($query, $conexion);                
                $fetch2=fetch_array($result2);
                $squincenal=$fetch2[monto];


                $isr=0;
                $query = "select monto from nom_movimientos_nomina 
            where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=202";
                $result3 = query($query, $conexion);                
                $fetch3=fetch_array($result3);
                $isr=$fetch3[monto];

                $ss=0;
                $query = "select monto from nom_movimientos_nomina 
            where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=200";
                $result4 = query($query, $conexion);                
                $fetch4=fetch_array($result4);
                $ss=$fetch4[monto];

                $se=0;
                $query = "select monto from nom_movimientos_nomina 
            where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=201";
                $result5 = query($query, $conexion);
                $fetch5=fetch_array($result5);
                $se=$fetch5[monto];


                $fc=0;
                $query = "select monto from nom_movimientos_nomina 
            where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND codcon=20";
                $result6 = query($query, $conexion);
                $fetch6=fetch_array($result6);
                $fc=$fetch6[monto];


                $ot=0;
                $query = "select ifnull(sum(monto),0) as monto from nom_movimientos_nomina where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (202,200,201)";
                $result7 = query($query, $conexion);
                $fetch7=fetch_array($result7);
                $ot=$fetch7[monto];

                $total_deduccion = $isr+$ss+$se+$fc+$ot;
                $monto_cheque =  $squincenal-$total_deduccion;

                $this->SetFont('Arial', '', 8);
                $this->SetWidths(array(10, 20, 60, 18,18,18, 18,18,18, 18,18,18,18));
                $this->SetAligns(array('L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R' ));
                $this->Setceldas(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
                $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
                $this->Row(array($sficha, $fila[cedula], $fila[apenom], $fila[suesal], $squincenal, $isr, $ss, $se, $fc, $ot, $total_deduccion, $monto_cheque, ''));

                if($ot>0)
                {
                    $query = "select descrip, codcon, monto from nom_movimientos_nomina where ficha = '" . $sficha . "' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (202,200,201)";
                    $result8 = query($query, $conexion);
                    while($fetch8=fetch_array($result8))
                    {
                        $this->SetFont('Arial', '', 8);
                        $this->SetWidths(array(10, 20, 60, 18,18,18, 18,18,18, 18,18,18,18));
                        $this->SetAligns(array('L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R' ));
                        $this->Setceldas(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
                        $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
                        $this->Row(array('', '',$fetch8[codcon].'   '.$fetch8[descrip], '', '', '', '', '', '', $fetch8[monto], '','', ''));
                    }

                }
                $this->SetWidths(array(270));
                $this->SetAligns(array('C' ));
                $this->Setceldas(array('B'));
                $this->Setancho(array(2));
                $this->Row(array(''));
        }
        //$pdf->firmas($total_asig, $total_dedu, $CANTIDAD);
        $this->Ln();
        $this->Ln();
       // $pdf->huellasPersonal();
       // $pdf->Vacaciones($fila2['periodo_ini'], $fila2['periodo_fin']);
    }

    function Vacaciones($fecha_ini, $fecha_fin) {

        $conexion = conexion();
        $consulta_vac = "SELECT per.ficha, apenom, vaca.fechareivac FROM nompersonal as per, nom_progvacaciones as vaca WHERE per.tipnom=vaca.tipnom and per.tipnom =" . $_SESSION['codigo_nomina'] . " and per.ficha=vaca.ficha and '" . $fecha_ini . "'<=vaca.fechareivac and vaca.fechareivac<='" . $fecha_fin . "' ORDER BY per.ficha";
        $resultado_vac = query($consulta_vac, $conexion);

        $this->Ln(20);
        $this->SetFont('Arial', 'I', 12);
        if (num_rows($resultado_vac) != 0) {
            $this->Ln(300);
            $this->Cell(188, 8, 'PERSONAL DE VACACIONES', 0, 0, 'C');
            $this->Ln(10);
            $this->SetFont('Arial', '', 10);
            $cantidad_registros = 40;
            $totalwhile = num_rows($resultado_vac);
            $contar = 1;
            while ($totalwhile >= $contar) {
                $fetchvac = fetch_array($resultado_vac);
                $this->Cell(60, 5, $fetchvac['ficha'], 0, 0, 'R');
                $this->Cell(80, 5, '   ' . $fetchvac['apenom'], 0, 0, 'L');
                $this->Cell(20, 5, '   ' . fecha($fetchvac['fechareivac']), 0, 0, 'L');
                $this->Ln();
                if ($contar == $cantidad_registros) {
                    $this->Ln(300);
                    $this->SetFont('Arial', 'I', 12);
                    $this->Cell(188, 8, 'PERSONAL DE VACACIONES', 0, 0, 'C');
                    $this->SetFont('Arial', 'I', 10);
                    $this->Ln(10);
                }
                $contar++;
            }
        }
    }

    function Footer() {
        $this->SetY(-15);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    function firmas($total_asig, $total_dedu, $cantidad) {

        $conexion = conexion();
        $consulta_vac = "SELECT ficha, apenom FROM nompersonal WHERE tipnom =" . $_SESSION['codigo_nomina'] . " and estado='Vacaciones' ORDER BY ficha";

        $resultado_vac = query($consulta_vac, $conexion);

        $consultaa = "SELECT ficha, apenom FROM nompersonal WHERE tipnom =" . $_SESSION['codigo_nomina'] . " and estado<>'Egresado'";
        $resultadooo = query($consultaa, $conexion);
        $cant_personal+=num_rows($resultadooo);

        $query = "select * from nom_nominas_pago where codnom = '" . $_GET['nomina_id'] . "' AND codtip= '" . $_SESSION['codigo_nomina'] . "' ";
        $result2 = query($query, $conexion);
        $fila2 = fetch_array($result2);

        $this->SetFont('Arial', '', 10);

        $queda = $cantidad - 5;

        if ($queda < 0) {
            $this->Ln(300);
            $this->Cell(188, 8, 'Desde: ' . fecha($fila2['periodo_ini']) . ' Hasta: ' . fecha($fila2['periodo_fin']) . ' Pago: ' . fecha($fila2['fechapago']), 0, 0, 'C');
            $this->Ln();
        }

        $this->Cell(50, 5, 'Cant. de Personas: ' . $cant_personal, 0, 1, 'L');

       // $this->Cell(188, 5, 'Total Generales: ' . number_format($total_asig, 2, ',', '.') . '       ' . number_format($total_dedu, 2, ',', '.'), 0, 1, 'R');

        //$this->Cell(188, 5, 'Neto: ' . number_format($total_asig - $total_dedu, 2, ',', '.'), 0, 1, 'R');
        $this->Ln(10);

        $this->SetFont('Arial', 'I', 7);

        $this->Cell(62, 5, ' ', 'LT', 0);
        $this->Cell(62, 5, ' ', 'LT', 0);
        $this->Cell(62, 5, ' ', 'LTR', 1);

        $this->Cell(62, 10, '', 'L', 0);
        $this->Cell(62, 10, '', 'L', 0);
        $this->Cell(62, 10, '', 'LR', 1);

        // llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(62, 62, 62));
        $this->SetAligns(array('C', 'C', 'C'));
        $this->Setceldas(array('1', '1', '1'));
        $this->Setancho(array(5, 5, 5));
        $this->Row(array('RECIBIDO POR', 'AUTORIZADO POR', 'PREPARADO POR'));
    }

    function huellasPersonal() {
        $conexion = conexion();
        $sql = "SELECT apenom FROM nompersonal WHERE tipnom = {$_SESSION['codigo_nomina']} AND estado<>'Egresado'";
        $result = query($sql, $conexion);
        $cant = num_rows($result);
        $cont = 1;
        $x = 10;
        $y = $this->GetY();
        $this->SetFont('Arial', '', 4);

        while ($empleados = fetch_array($result)) {
            $n = $empleados['apenom'];
            $apenom = explode(',', $n);
            $apellidos = explode(' ', trim($apenom[0]));
            $nombres = explode(' ', trim($apenom[1]));

            $this->SetXY($x, $y);
            $this->Cell(20, 20, '', 1, 1);
            $this->SetXY($x, $y + 20);
            $this->Cell(20, 10, $apellidos[0].' '.  substr($apellidos[1], 0, 1) . '., ' . $nombres[0].' '.  substr($nombres[1], 0, 1) . '.', 1, 1);

            $x+=23;
            if ($cont == $cant) {
                $y+=30;
            }
            $cont++;
        }
    }

}

//Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L', 'A4');
$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);

$pdf->personas($nomina_id, $codt, $pdf);

//$pdf->Vacaciones();

$pdf->Output();
?>
