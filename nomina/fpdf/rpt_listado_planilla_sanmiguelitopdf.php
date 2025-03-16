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

        $this->SetFont("Arial", "B", 14);
        $this->Cell(0, 5, $row_rs['nom_emp'], 0, 1, 'C');
        
        $this->SetFont("Arial", "B", 12);
        $this->Cell(89, 5, 'Fecha: '.date('Y-m-d'), 0, 0, 'C');
        $this->SetFont("Arial", "B", 14);
        $this->Cell(100, 5, 'LISTADO DE LA PLANILLA DEL PERIODO', 0, 0, 'C');
        $this->SetFont("Arial", "B", 12);
        $this->Cell(89, 5,$_SESSION['nombre'].' '.date('h:m:s'), 0, 1, 'C');

        $this->Cell(89, 5,'', 0, 0, 'C');
        $this->Cell(100, 5, $fecha_ini.' al '.$fecha_fin, 0, 0, 'C');
        $this->Cell(89, 5, utf8_decode('Página ') . $this->PageNo(), 0, 1, 'C');

        $this->SetFont("Arial", "", 10);
        $this->Ln(5);
        $this->SetFont("Arial", "B", 8);
        $this->SetWidths(array(10,80,20,18,18,18,18,18,18,18,18,18,18));
        $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','C'));
        $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0','0'));
        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5));
        $this->Row(array('No', 'Nombre', 'Salario Quincenal', 'Desc por Lic', 'Total Devengado', 'SIACAP', 'ISR', 'Seguro Educativo', 'Seguro Social', 'Otros Descuentos', 'Total Descuentos', 'Sueldo Neto'));
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

        $query2 = "SELECT * FROM nom_nominas_pago WHERE codnom = '".$nomina_id."'";
        $result = query($query2, $conexion);
        $nomina = fetch_array($result);

        //Consulta anterior
        /*$query2 = "SELECT a.ficha,a.apenom,a.cedula,a.seguro_social,a.suesal,a.nomposicion_id,c.codorg,c.descrip FROM nompersonal AS a,nom_movimientos_nomina AS b,nomnivel1 AS c WHERE a.ficha = b.ficha AND a.estado = 'Activo' AND b.tipnom = '".$codt."' AND a.codnivel1 = c.codorg GROUP BY a.ficha ORDER BY c.codorg,a.nomposicion_id";
        -------------------------------------------------------------
        consulta nueva
        $query2 = "SELECT a.ficha,a.apenom,a.cedula,a.seguro_social,a.nomposicion_id,c.codorg,c.descrip FROM nompersonal AS a,nom_movimientos_nomina AS b,nomnivel1 AS c WHERE a.ficha = b.ficha AND b.tipnom = '$codt' AND codnom = '$nomina_id' AND a.codnivel1 = c.codorg GROUP BY a.ficha ORDER BY c.codorg,a.nomposicion_id";
        -------------------------------------------------------------
        consulta con cambios
        1)- verifique en la bd la cantidad de personas en la planilla usando (tipnom,codnom,codnivel1) y note que habian 8 funcionarios
            pero el reporte de planilla colocaba a 7 nada mas de ahi la diferencia en los totales de planilla asi que modifique la consulta
            con el primer sql el total neto da 4,275.00 (7 funcionarios) pero cuando sume a los 8 que estan registrados en la BD da 4,600.00

        */
        $query2 = "SELECT b.ficha,b.apenom,b.cedula,b.seguro_social,b.nomposicion_id,c.codorg,c.descrip FROM nom_movimientos_nomina AS a 
            LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
            LEFT JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id'
            GROUP BY b.ficha ORDER BY c.codorg,b.nomposicion_id";
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
        $lineas=0;
        $l=0;
        $tmp="";
        $descrip="";
        $z=0;
        while ($fila = fetch_array($result))
        {
            if ($fila['descrip']!=$tmp) {
                $descrip=$tmp;
                $tmp=$fila['descrip'];
            }else{
                $tmp=$fila['descrip'];
            }
            $lsp=0;
            $this->Ln(2);
            $query = "select codcon, descrip, monto from nom_movimientos_nomina where ficha = '".$sficha."' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (200,201,202,204)";
            $result8 = query($query, $conexion);
            $prestamos=0;
            while($fetch8=fetch_array($result8))
            {
                $l=$l+1;
            }
            $lsp=$l+4;
            $l=0;
            if ($lineas < 6)
            {
                $this->AddPage('L', 'A4');
                $lineas=24;
            }
                $persona+=1;
                $sficha = $fila['ficha'];
                $sposicion = $fila['nomposicion_id'];
                $sueldo_bruto=0;

                $query = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=100";
                $result2 = query($query, $conexion);
                $fetch2=fetch_array($result2);
                $sueldo_bruto=$fetch2['monto'];

                $dias=0;
                $lic=0;
                $query = "SELECT SUM(monto) AS monto,valor FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=150";
                $result4 = query($query, $conexion);                
                $fetch4=fetch_array($result4);
                $dias='- '.number_format($fetch4['valor'], 0).' DIAS LIC';
                $lic=$fetch4['monto'];

                $ss=0;
                $query = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=200";
                $result4 = query($query, $conexion);                
                $fetch4=fetch_array($result4);
                $ss=$fetch4['monto'];

                $se=0;
                $query = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=201";
                $result5 = query($query, $conexion);
                $fetch5=fetch_array($result5);
                $se=$fetch5['monto'];

                $isr=0;
                $query = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=202";
                $result3 = query($query, $conexion);                
                $fetch3=fetch_array($result3);
                $isr=$fetch3['monto'];
                
                $siacap=0;
                $query = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=204";
                $result6 = query($query, $conexion);
                $fetch6=fetch_array($result6);
                $siacap=$fetch6['monto'];
                $ot=0;
                $query = "SELECT ifnull(SUM(monto),0) AS monto FROM nom_movimientos_nomina AS a WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND tipcon='D' AND codcon NOT IN (100,150,200,201,202,204)";
                $result7 = query($query, $conexion);
                $fetch7=fetch_array($result7);
                $ot=$fetch7['monto'];

                $total_deduccion = $isr+$ss+$se+$siacap+$ot;
                $devengado       = $sueldo_bruto-$lic;
                $sueldo_neto     = $devengado-$total_deduccion;

                if ($fila['codorg'] > $j)
                {
                $this->SetFont("Arial", "B", 10);
                $this->SetWidths(array(90,20,18,18,18,18,18,18,18,18,18));
                $this->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R'));
                $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B'));
                $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5));
                $z++;
                $this->Row(array($descrip, $sub_total_per, number_format($sub_total_lic, 2, '.',','), number_format($sub_total_sq, 2, '.',','), number_format($sub_total_siacap, 2, '.',','), number_format($sub_total_isr, 2, '.',','), number_format($sub_total_se, 2, '.',','), number_format($sub_total_ss, 2, '.',','), number_format($sub_total_ot, 2, '.',','), number_format($sub_total_desc, 2, '.',','), number_format($sub_total_neto, 2, '.',',')
                ));
                    $j=$fila["codorg"];
                    $sub_i=0;
                    $sub_total_per    = 0;
                    $sub_total_lic    = 0;
                    $sub_total_sq     = 0;
                    $sub_total_siacap = 0;
                    $sub_total_isr    = 0;
                    $sub_total_se     = 0;
                    $sub_total_ss     = 0;
                    $sub_total_ot     = 0;
                    $sub_total_desc   = 0;
                    $sub_total_neto   = 0;
                $lineas=$lineas-1;
                }
            if ($fila['codorg'] >= $j)
            {
                $query = "SELECT * FROM nomcampos_adic_personal WHERE ficha ='".$sficha."' AND id=6 AND valor != '0'";
                $r = query($query, $conexion);
                $f=fetch_array($r);

                $sub_i=$sub_i+1;
                $this->SetFont('Arial','', 8);
                $this->SetWidths(array(10,80,20,18,18,18,18,18,18,18,18,18,18));
                $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','C'));
                $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0','0'));
                $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5));
                if (!empty($f['valor']))
                {
                $nombre=$f['valor'];
                }else{
                $this->SetFont('Arial','B', 8);
                $nombre="'".$fila['apenom']."'";
                }
                $this->Row(array($sposicion,utf8_decode($nombre),number_format($sueldo_bruto, 2, '.',','),number_format($lic, 2, '.',','),number_format($devengado, 2, '.',','),number_format($siacap, 2, '.',','),number_format($isr, 2, '.',','),number_format($se, 2, '.',','),number_format($ss, 2, '.',','),'','',number_format($sueldo_neto, 2, '.',','),''));
                $lineas=$lineas-1;
                $this->SetFont('Arial','', 8);
                $this->SetWidths(array(30,30,18,30,20,20,20,30,30,30,30,20));
                $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','C'));
                $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0'));
                $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5));
                $this->Row(array('Ced: '.$fila['cedula'],'','','','','','','','','','','',''));
                $lineas=$lineas-1;
                if($fila['seguro_social'] != ''){$dat=' S.S: '.$fila['seguro_social'];}else{$dat=' S.S: '.'S/S';}
                $this->Row(array($dat,'','','','','','','','','','','',''));
                $lineas=$lineas-1;

                if($ot>0)
                {
                    $query="SELECT numpre,codigopr FROM nomprestamos_cabecera 
                            WHERE ficha = '".$sficha."' AND estadopre = 'Pendiente' AND numpre in(SELECT numpre 
                            FROM nomprestamos_detalles WHERE ficha = '".$sficha."' AND fechaven= '".$nomina['periodo_fin']."')";
                    $result9 = query($query, $conexion);
                    $prestamos=0;
                    while($fetch9=fetch_array($result9))
                    {
                        $prestamos=$prestamos+1;
                        if ($prestamos == 4){$lineas=$lineas+1;}
                        $query = "SELECT montocuo FROM nomprestamos_detalles WHERE numpre = '".$fetch9['numpre']."' AND fechaven = '".$nomina['periodo_fin']."'";
                        $r = query($query, $conexion);
                        $cuota=fetch_array($r);

                        $query = "SELECT * FROM nomprestamos WHERE codigopr = '".$fetch9['codigopr']."'";
                        $res = query($query, $conexion);
                        $formula=fetch_array($res);
                        
                        $this->SetFont('Arial', '', 8);
                        $this->SetWidths(array(10,15,15,6,6,6,6,6,18,130,18,18,18));
                        $this->SetAligns(array('L', 'L', 'L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R' ));
                        $this->Setceldas(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
                        $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
                        if (isset($formula['formula']))
                        {
                            $dato=$formula['formula'].' '.utf8_decode($formula['descrip']);
                        }elseif (!isset($formula['formula']))
                        {
                            $dato='Formula sin definir '.utf8_decode($formula['descrip']);
                        }
                        $this->Row(array('', '','', '', '', '', '', '', '', $dato, number_format($cuota['montocuo'], 2, '.',','),'', ''));
                        $lineas=$lineas-1;
                    }
                }

                $this->SetFont("Arial", "B", 8);
                $this->SetWidths(array(12,80,18,18,18,18,18,18,18,18,18,18));
                $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R'));
                $this->Setceldas(array('TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB'));
                $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5));

                $this->Row(array('','','Total:',number_format($lic, 2, '.',','),number_format($devengado, 2, '.',','),number_format($siacap, 2, '.',','),number_format($isr, 2, '.',','),number_format($se, 2, '.',','),number_format($ss, 2, '.',','),number_format($ot, 2, '.',','),number_format($total_deduccion, 2, '.',','),number_format($sueldo_neto, 2, '.',',')));
                $i=$i+1;
                $lineas=$lineas-1;
                $lsp=0;
                //---------------------------------------------------------------------------------------------
                $sub_total_per      = $sub_i;
                $sub_total_lic      = $sub_total_lic+$lic;
                $sub_total_sq       = $sub_total_sq+$devengado;
                $sub_total_siacap   = $sub_total_siacap+$siacap;
                $sub_total_isr      = $sub_total_isr+$isr;
                $sub_total_se       = $sub_total_se+$se;
                $sub_total_ss       = $sub_total_ss+$ss;
                $sub_total_ot       = $sub_total_ot+$ot;
                $sub_total_desc     = $sub_total_desc+$total_deduccion;
                $sub_total_neto     = $sub_total_neto+$sueldo_neto;
                //---------------------------------------------------------------------------------------------
                $total_per      = $i;
            }
        }
        $this->SetFont("Arial", "B", 10);
        $this->SetWidths(array(90,20,18,18,18,18,18,18,18,18,18));
        $this->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B'));
        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5));
        $z++;
        $this->Row(array($descrip, $sub_total_per, number_format($sub_total_lic, 2, '.',','), number_format($sub_total_sq, 2, '.',','), number_format($sub_total_siacap, 2, '.',','), number_format($sub_total_isr, 2, '.',','), number_format($sub_total_se, 2, '.',','), number_format($sub_total_ss, 2, '.',','), number_format($sub_total_ot, 2, '.',','), number_format($sub_total_desc, 2, '.',','), number_format($sub_total_neto, 2, '.',',')
        ));
        //-------------------------------------------------------------------------------
        $total_per      = $i;

        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=100";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_bruto = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=150";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_lic = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=200";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_ss = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=201";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_se = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=202";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_isr = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=204";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_siacap = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $sql="SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.tipcon='D' AND a.codcon NOT IN (100,150,200,201,202,204)";
        $result2 = query($sql, $conexion);
        $fetch2=fetch_array($result2);
        $total_ot = $fetch2['monto'];
        //-------------------------------------------------------------------------------
        $total_desc      = $total_siacap + $total_isr + $total_se + $total_ss + $total_ot;
        $total_devengado = $total_bruto-$total_lic;
        $total_neto      = $total_devengado - $total_desc;

        $this->SetFont("Arial", "B", 9);
        $this->SetWidths(array(72,20,18,18,18,18,18,18,18,18,18,18));
        $this->SetAligns(array('R','L','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B'));
        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5));
        $this->Row(array('Totales:', $total_per, number_format($total_bruto, 2, '.',','), number_format($total_lic, 2, '.',','), number_format($total_devengado, 2, '.',','), number_format($total_siacap, 2, '.',','), number_format($total_isr, 2, '.',','), number_format($total_se, 2, '.',','), number_format($total_ss, 2, '.',','),number_format($total_ot, 2, '.',','), number_format($total_desc, 2, '.',','), number_format($total_neto, 2, '.',',')));
        $this->Ln(10);
        $this->SetFont("Arial", "B", 8);
        $this->SetWidths(array(50, 40, 50, 40, 52, 40));
        $this->SetAligns(array('C','L','C','L','C','L'));
        $this->Setceldas(array('0', 'B', '0', 'B', '0', 'B'));
        $this->Setancho(array(5, 5, 5, 5, 5, 5));
        $this->Row(array('Revisado Por','', 'Confeccionado Por:','', 'Directora de Recursos  POR:',''));
    }
}
//Creación del objeto de la clase heredada
$pdf = new PDF();

$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);
$pdf->personas($nomina_id, $codt, $pdf);
$pdf->Output();
?>