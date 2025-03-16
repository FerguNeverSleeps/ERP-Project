<?php
session_start();
ob_start();

require('fpdf.php');
include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if (!empty($value))
        return substr($value, 8, 2) . "/" . substr($value, 5, 2) . "/" . substr($value, 0, 4);
}
$nomina_id = $_GET['codnom'];
$codt      = $_GET['codtip'];

class PDF extends FPDF {
    var $codnom, $tipnom, $meses, $meses_salarios, $periodo_ini, $periodo_fin;
    function mes_sig( $periodo_ini, $periodo_fin ){
        
        $fecha_ini  = new Datetime($periodo_ini);
        $fecha_fin  = new Datetime($periodo_fin);
        $fecha_temp = $fecha_ini->format("Y-m-d");
        $meses      = [];
        $meses[] = $fecha_ini->format("Y-m-d");
        
        $fecha1 = strtotime($fecha_ini->format("Y-m-d"));
        $fecha2 = strtotime($fecha_fin->format("Y-m-d"));
        while((int)$fecha1 < (int)$fecha2){
            $fecha_modificada = $fecha_ini->modify("next month");
            $meses[] = $fecha_modificada->format("Y-m-d");
            $fecha_temp =$fecha_modificada->format("Y-m-d");
            
            $fecha1 = strtotime( $fecha_temp );
        }
        return $meses;
    }
    function sueldos_acumulados( $ficha ){
        $Conn          = conexion();
        foreach ($this->meses as $key => $mes) {
            $frecuencia = "";
            //La última quincena del primer mes del décimo y la primera quincena del último mes del décimo...
            switch ($key) {
                case 0:
                    $frecuencia = "(3,8)";
                    # code...
                    break;
                case 4:
                    $frecuencia = "(2,8)";
                    break;
                
                default:
                    $frecuencia = "(2,3,8)";
                    break;
            }
            $mes_salario = date("m", strtotime($mes));
            $mes_anio = date("Y", strtotime($mes));
            
            $consulta      = "SELECT COALESCE(SUM(salario_bruto) + SUM(vacac),0) salario,COALESCE(SUM(gtorep),0) gtorep, MONTH(fecha_pago) mes, YEAR(fecha_pago) anio
            from salarios_acumulados WHERE ficha = '".$ficha."' AND MONTH(fecha_pago) ='".$mes_salario."' AND YEAR(fecha_pago) ='".$mes_anio."'
            AND frecuencia_planilla in $frecuencia
            GROUP BY MONTH(fecha_pago), YEAR(fecha_pago);";
            $resultado     = query($consulta,$Conn);
            $fetch         = fetch_array($resultado);
            $meses_salarios[] = array("salario"=> $fetch["salario"],"gr"=> $fetch["gtorep"], "mes"=> $mes_salario , "anio"=> $mes_anio, "frecuencia" =>  $frecuencia);
            
        }
        return $meses_salarios;
    }

    function header()
    {
        $Conn          = conexion();
        $var_sql       = "select * from nomempresa";
        $rs            = query($var_sql, $Conn);
        $row_rs        = fetch_array($rs);
        $var_izquierda = '../imagenes/' . $row_rs['imagen_izq'];
        
        $consulta      = "SELECT periodo_ini,periodo_fin FROM nom_nominas_pago WHERE codnom = '".$this->codnom."' AND codtip = '".$this->tipnom."'";
        $resultado     = query($consulta,$Conn);
        $fetch         = fetch_array($resultado);
        $fecha_ini     = $fetch['periodo_ini'];
        $fecha_fin     = $fetch['periodo_fin'];
        $this->periodo_ini = $fecha_ini;
        $this->periodo_fin = $fecha_fin;
        $this->meses   = $this->mes_sig( $fecha_ini, $fecha_fin );
        setlocale(LC_TIME,"es_ES");
        foreach ($this->meses as $key => $value) {
            $mes[$key] = ucfirst(strftime("%B", strtotime($value)));
        }
        $this->SetFont("Arial", "B", 14);
        $this->Cell(35, 5,'', 0, 0, 'C');

        $this->Cell(185, 5, $row_rs['nom_emp'], 0, 0, 'C');
        $this->SetFont("Arial", "B", 9);
        $this->Cell(60, 5,$_SESSION['nombre'], 0, 1, 'C');
        
        $this->SetFont("Arial", "B", 9);
        $this->Cell(35, 5, 'Fecha: '.date('Y-m-d'), 0, 0, 'C');
        $this->SetFont("Arial", "B", 14);
        $this->Cell(185, 5, 'LISTADO DE LA PLANILLA DECIMO DEL PERIODO', 0, 0, 'C');
        $this->SetFont("Arial", "B", 9);
        $this->Cell(60, 5, utf8_decode('Página ') . $this->PageNo(), 0, 1, 'C');

        $this->Cell(35, 5,'', 0, 0, 'C');
        $this->SetFont("Arial", "B", 12);
        $this->Cell(185, 5, fecha($fecha_ini).' al '.fecha($fecha_fin), 0, 0, 'C');
        $this->SetFont("Arial", "B", 9);
        $this->Cell(60, 5,date('h:m:s'), 0, 1, 'C');

        $this->SetFont("Arial", "", 10);
        $this->Ln(5);
        $this->SetFont("Arial", "B", 6);
        $this->SetWidths(array(50,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5,16.5));
        $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
        $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
        $this->Row(array('Nombre de Empleado','','','','','','', '','Salarios', 'Gastos R.', 'Salarios', 'Gastos R.' , 'Salarios', 'Gastos R.' ));
        $this->Row(array('',$mes[0],$mes[1],$mes[2],$mes[3],$mes[4],'Salarios', 'Gastos R.','XIII Bruto', 'XIII Bruto', 'S.S.','S.S.', 'I.S.R',  'I.S.R', 'XIII NETO'));
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
        $sql_param = "SELECT * FROM config_reportes_planilla_columnas WHERE id_reporte = '12';";
        $result_param = query($sql_param, $conexion);
        $decimo = $decimo_ss = $decimo_isr = $decimo_gr = $decimo_gr_ss = $decimo_gr_isr = $descuentos_decimo = "";
        while($params = fetch_array($result_param))
        {
            
            if($params["nombre_corto"] == "con_xiii")
            {
                $decimo = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_s_s")
            {
                $decimo_ss = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_isr")
            {
                $decimo_isr = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_xiii_gr")
            {
                $decimo_gr = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_ss_gr_xiii")
            {
                $decimo_gr_ss = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_isr_gr_xii")
            {
                $decimo_gr_isr = $params["conceptos"];
            }
            if($params["nombre_corto"] == "con_descuentos_decimo")
            {
                $descuentos_decimo = $params["conceptos"];
            }
        }
        
        $conexion = conexion();
        $sql = "SELECT MAX(codorg) AS codorg FROM nomnivel1";
        $result1 = query($sql, $conexion);
        $max = fetch_array($result1);

        $query2 = "SELECT * FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND tipnom = '$codt'";
        $result = query($query2, $conexion);
        $nomina = fetch_array($result);
        
        $query2 = "SELECT b.ficha,b.apenom,b.suesal,b.cedula,b.seguro_social,b.nomposicion_id,c.codorg,c.descrip FROM nom_movimientos_nomina AS a 
            LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
            LEFT JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$this->tipnom' AND a.codnom = '$this->codnom'
            GROUP BY b.ficha ORDER BY c.codorg,b.nomposicion_id";
        $result        = query($query2, $conexion);
        $contador      = 1;
        $num           = 1;
        $posicion      = "";
        $persona       = 0;
        $totalpersonas = 0;
        $totalbd       = num_rows($result);
        $pers          = 0;
        $i             = 0;
        $sub_i         = 0;
        $j             = 1;
        $lineas        = 0;
        $l             = 0;
        $tmp           = "";
        $descrip       = "";
        $z             = $neto_suesal = $neto_bruto = $netos_ss = $netos_isr = $neto_gr13 = $netos_ss_gr = $netos_isr_gr= 0;
        while ($fila = fetch_array($result))
        {
            $sficha = $fila['ficha'];
            if ($fila['descrip']!=$tmp) 
            {
                $descrip=$tmp;
                $tmp=$fila['descrip'];
            }
            else
            {
                $tmp=$fila['descrip'];
            }
            $lsp=0;
            $this->Ln(2);

            $query = "SELECT codcon, descrip, monto from nom_movimientos_nomina where ficha = '".$sficha."' and tipnom='".$this->tipnom."' and codnom = '" . $this->codnom . "' AND tipcon='D' 
            and codcon not in (".$descuentos_decimo.")";
            $result8 = query($query, $conexion);
            $prestamos=0;
            while($fetch8=fetch_array($result8))
            {
                $l=$l+1;
            }
            $lsp=$l+4;
            $l=0;
            if ($lineas < 3 OR $this->GetY() > 186)
            {
                $this->AddPage('L', 'A4');
                $this->SetY(40);
                $lineas=24;
            }
            $persona     += 1;
            $sficha       = $fila['ficha'];
            $sposicion    = $fila['nomposicion_id'];
            $sueldo_bruto = 0;

            $query = "SELECT ifnull( monto ,0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo)";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $sueldo_bruto13=round($fetch2['monto'],2);                
            $neto_bruto     += $sueldo_bruto13;

            $ss13=0;
            $query = "SELECT ifnull(ROUND( SUM( monto ) , 2 ),0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo_ss) ";
            $result4 = query($query, $conexion);    
            while ( $fetch4=fetch_array($result4)) 
            {
                $ss13     =round($fetch4['monto'],2);
                $netos_ss +=round($fetch4['monto'],2);
            }    

            $isr13=0;
            $query = "SELECT ifnull(ROUND( SUM( monto ) , 2 ),0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo_isr)";
            $result5 = query($query, $conexion);    
            while ( $fetch5=fetch_array($result5)) 
            {
                $isr13     =round($fetch5['monto'],2);
                $netos_isr +=round($fetch5['monto'],2);
            } 

            $gr13=0;
            $query = "SELECT ifnull( monto ,0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo_gr)";
            $result3 = query($query, $conexion);
            $fetch3=fetch_array($result3);
            $gr13=round($fetch3['monto'],2);                
            $neto_gr13     += $gr13;


            $ss_gr13=0;
            $query = "SELECT ifnull(ROUND( SUM( monto ) , 2 ),0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo_gr_ss)";
            $result4 = query($query, $conexion);    
            while ( $fetch4=fetch_array($result4)) 
            {
                $ss_gr13     =round($fetch4['monto'],2);
                $netos_ss_gr +=round($fetch4['monto'],2);
            }  
            $isr_gr13=0;
            $query = "SELECT ifnull(ROUND( SUM( monto ) , 2 ),0) AS monto FROM nom_movimientos_nomina WHERE ficha = '$sficha' AND tipnom = '$this->tipnom' AND codnom = '$this->codnom' AND codcon in ($decimo_gr_isr)";
            $result5 = query($query, $conexion);    
            while ( $fetch5=fetch_array($result5)) 
            {
                $isr_gr13     =round($fetch5['monto'],2);
                $netos_isr_gr +=round($fetch5['monto'],2);
            }
            /*$fetch4=fetch_array($result4);
            $ss13=round($fetch4['monto'],2);
            $netos_ss+=round($fetch4['monto'],2);*/


            $total_deduccion = round($ss_gr13,2) + round($ss13,2) + round($isr13,2) + round($isr_gr13,2) ;
            $devengado       = round($sueldo_bruto13,2)+round($gr13,2);
            $sueldo_neto     = $devengado-$total_deduccion;
		$totales_netos += $sueldo_neto;
            
               
              if ($lineas < 3 OR $this->GetY() > 186)
                {
                    $this->AddPage('L', 'A4');
                    $this->SetY(40);
                    $lineas=24;
                }

                $sub_i=$sub_i+1;
                $this->SetFont("Arial", "B", 5);
                $this->SetWidths(array(50,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12));
                $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
                $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
                $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
                $nombre="'".utf8_encode($fila['apenom'])."'";
                $meses_acumulados = $this->sueldos_acumulados($sficha);
                $total_meses_acumulados = 0;
                $total_meses_acumulados_gr = 0;
                foreach ($meses_acumulados as $key => $value) {
                    $total_meses_acumulados += $value["salario"];
                    $total_meses_acumulados_gr += $value["gr"];
                }
                $total_mes1 += $meses_acumulados[0]["salario"];
                $total_mes2 += $meses_acumulados[1]["salario"];
                $total_mes3 += $meses_acumulados[2]["salario"];
                $total_mes4 += $meses_acumulados[3]["salario"];
                $total_mes5 += $meses_acumulados[4]["salario"];
                $this->Row(array(utf8_decode($nombre),
                'B./',number_format($meses_acumulados[0]["salario"], 2, '.',','),
                'B./',number_format($meses_acumulados[1]["salario"], 2, '.',','),
                'B./',number_format($meses_acumulados[2]["salario"], 2, '.',','),
                'B./',number_format($meses_acumulados[3]["salario"], 2, '.',','),
                'B./',number_format($meses_acumulados[4]["salario"], 2, '.',','),
                'B./',number_format($total_meses_acumulados, 2, '.',','),
                'B./',number_format($total_meses_acumulados_gr, 2, '.',','),
                'B./',number_format($sueldo_bruto13, 2, '.',','),
                'B./',number_format($gr13, 2, '.',','),
                'B./',number_format($ss13, 2, '.',','),
                'B./',number_format($ss_gr13, 2, '.',','),
                'B./',number_format($isr13, 2, '.',','),
                'B./',number_format($isr_gr13, 2, '.',','),
                'B./',number_format($sueldo_neto, 2, '.',',')));
                $lineas=$lineas-1;
                if ($lineas < 3 OR $this->GetY() > 186)
                {
                    $this->AddPage('L', 'A4');
                    $this->SetY(40);
                    $lineas=24;
                }

                
                $lsp=0;
                //---------------------------------------------------------------------------------------------
                $sub_total_per    = $sub_i;
                $subtotal13       = $subtotal13+round($sueldo_bruto13,2);
                $sub_total_ss     = $sub_total_ss+$ss13;
                $sub_total_suesal = $sub_total_suesal+round($fila['suesal'],2);
                $total_suesal     += $sub_total_suesal;
                
                $sub_total_neto   = $sub_total_neto+$sueldo_neto;
                //---------------------------------------------------------------------------------------------
                $total_per        = $i;


                if ($lineas < 3 OR $this->GetY() > 186)
                {
                    $this->AddPage('L', 'A4');
                    $this->SetY(40);
                    $lineas=24;
                }
                $netos_13         += $subtotal13;
                //$netos_ss         += $sub_total_ss;
                $netos_totales    += $sub_total_neto;

        }
                
        $this->SetFont("Arial", "BI", 5.5);
        $this->SetWidths(array(45,5,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12,4.5,12));
        $this->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
        $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
        $z++;
        $este_sql      = "select descrip from nomnivel1 where codorg  = '{$j}'";
        $conection     = new bd($_SESSION['bd_nomina']);
        $res_nivel1    = $conection->query($este_sql);
        $nombre_nivel1 = $res_nivel1->fetch_assoc();
        $this->Row(array($nombre_nivel1['descrip'], $sub_total_per, 'B./',$total_mes1, 'B./',$total_mes2, 'B./',$total_mes3, 'B./',$total_mes4, 'B./',$total_mes5, 'B./',number_format(($total_mes1+$total_mes2+$total_mes3+$total_mes4+$total_mes5), 2, '.',','), 
        'B./',number_format($neto_gr13, 2, '.',','),
         'B./',number_format($neto_bruto, 2, '.',','), 'B./',number_format($neto_gr13, 2, '.',','), 
        'B./',number_format($netos_ss, 2, '.',','), 'B./',number_format($netos_ss_gr, 2, '.',','), 'B./',number_format($netos_isr, 2, '.',','), 'B./',number_format($netos_isr_gr, 2, '.',','), 'B./',number_format($totales_netos, 2, '.',',')));
        $lineas=$lineas-1;

        if ($lineas < 3 OR $this->GetY() > 186)
        {
            $this->AddPage('L', 'A4');
            $this->SetY(40);
            $lineas=24;
        }
        $this->SetFont("Arial", "B", 8);
        $this->SetWidths(array(50, 40, 50, 40, 52, 40));
        $this->SetAligns(array('C','L','C','L','C','L'));
        $this->Setceldas(array('0', 'B', '0', 'B', '0', 'B'));
        $this->Setancho(array(5, 5, 5, 5, 5, 5));
        $this->Ln(10);
        $this->Row(array('Revisado Por','', 'Confeccionado Por:','', 'Directora de Recursos  POR:',''));
    }
}
//Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->codnom = $nomina_id;
$pdf->tipnom = $codt;

$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);
$pdf->personas($nomina_id, $codt, $pdf);
$pdf->Output();
?>