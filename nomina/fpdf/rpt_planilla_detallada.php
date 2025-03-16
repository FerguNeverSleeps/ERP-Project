<?php
session_start();
ob_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

require('fpdf.php');
include("../lib/common.php");
include("../paginas/funciones_nomina.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if (!empty($value))
        return substr($value, 8, 2) . "/" . substr($value, 5, 2) . "/" . substr($value, 0, 4);
}
$nomina_id = $_GET['codnom'];
$codt = $_GET['codtip'];
$total_cuotas = $isr_total=0;

class PDF extends FPDF {
    var $codnom, $tipnom;
    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }

    function header()
    {
        $nomina_id     = $_GET['nomina_id'];
        $codt          = $_GET['codt'];
        $Conn          = conexion();
        $var_sql       = "SELECT * from nomempresa";
        $rs            = query($var_sql, $Conn);
        $row_rs        = fetch_array($rs);
        $var_izquierda = '../imagenes/' . $row_rs['imagen_izq'];
         $consulta ='SELECT date_format( nnp.periodo_ini,"%d-%m-%Y") periodo_ini, date_format( nnp.periodo_fin,"%d-%m-%Y") periodo_fin, ntn.descrip 
                    FROM nom_nominas_pago nnp INNER JOIN nomtipos_nomina ntn ON (ntn.codtip = nnp.codtip)  
                    WHERE nnp.codnom = '.$this->codnom.' AND nnp.codtip = '.$this->tipnom;
        $resultado = query($consulta,$Conn);
        $fetch     = fetch_array($resultado);
        $fecha_ini = $fetch['periodo_ini'];
        $fecha_fin = $fetch['periodo_fin'];
        $descrip   = $fetch['descrip'];
        $this->SetFont("Arial", "B", 10);
        $this->Cell(0, 5, $row_rs['nom_emp'], 0, 1, 'C');        
        $this->SetFont("Arial", "B", 8);
        $this->Cell(89, 5, 'Fecha: '.date('d-m-Y'), 0, 0, 'C');
        $this->SetFont("Arial", "B", 10);
        $this->Cell(100, 5, 'DETALLE DE LA PLANILLA', 0, 0, 'C');
        $this->SetFont("Arial", "B", 8);
        $this->Cell(89, 5,' ', 0, 1, 'C');
        $this->Cell(89, 5,$descrip, 0, 0, 'C');
        $this->Cell(100, 5, $fecha_ini.' al '.$fecha_fin, 0, 0, 'C');
       // $this->Cell(89, 5, utf8_decode('Página ') . $this->PageNo(), 0, 1, 'C');
        $this->Cell(89, 5,' ', 0, 1, 'C');
        $this->Ln(5);
        $this->SetFont("Arial", "B", 6);
        $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
        $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('TLB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TB','TBR','TBR'));
        $this->Setancho(array(8,4,8,8,4,4,8,8,4,8,8,8,8,8,8,4,4,4));
        $this->Row(array('Nombre del Empleado', 'Salario Base','Gastos R.', 'Otros Ing.', 'Tardanzas/ Ausencias','Horas Extras', 'Domingos','Feriado','Salario Bruto', 'S.S.', 'S.S./G.R.', 'S. E.', 'I.S.R.', 'I.S.R./G.R.', 'Deducciones', 'Sueldo Neto'));
        
        $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
        $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
        $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
        //$this->Ln(4);
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
    function getTotalAcreedorFuncionario($acreedores_group,$codnom,$tipnom,$conexion,$nivel)
    {
        $acreedores = explode(',', $acreedores_group);
        $totalAcreedorFuncionario = 0;
        for ($i=0; $i < count($acreedores); $i++) {         
            $query  = "SELECT ifnull( SUM( a.monto ) ,0) as monto FROM nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            WHERE a.tipnom = '".$tipnom."' AND a.codnom = '".$codnom."' and b.codorg = '$nivel' AND a.codcon='{$acreedores[$i]}'";
            $result = query($query, $conexion);
            $fetch2 = mysqli_fetch_assoc($result);
            $monto  = $fetch2['monto'];
            $totalAcreedorFuncionario += $monto;
        }
        return $totalAcreedorFuncionario;
    }
    function totalPartida($codnom,$tipnom,$conexion,$nivel){
        //$bd     = new bd($_SESSION['bd_nomina']);

        $sql_montos = "SELECT 
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon in (100,115) and b.codorg = '$nivel') as sueldo,
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon in ('150','198') and b.codorg = '$nivel') as lic,
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon = 200 and b.codorg = '$nivel') as ss,
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon = 201 and b.codorg = '$nivel') as se,
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon = 202 and b.codorg = '$nivel') as isr,
        (SELECT ifnull( SUM( a.monto ) ,0)  monto from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where a.codnom='{$codnom}' and a.tipnom='{$tipnom}' and a.codcon = 209 and b.codorg = '$nivel') as siacap,
        (SELECT group_concat(DISTINCT codcon) as ot from nom_movimientos_nomina a 
            INNER JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg 
            where codnom='{$codnom}' and tipnom='{$tipnom}' and (codcon >= 500 OR codcon in (210,211)) and b.codorg = '$nivel') as ot";
        $txt = $sql_montos."\n";
        $file = "./barra_aquery.txt";
            
        file_put_contents($file, $txt);
        $result   = query($sql_montos, $conexion);
        return mysqli_fetch_assoc($result);
         
    }
    function personas($nomina_id, $codt, $pdf)
    {
        $conexion = conexion();
        $sql = "SELECT MAX(codorg) AS codorg FROM nomnivel1";
        $result1 = query($sql, $conexion);
        $max = fetch_array($result1);  
        $query2 = "SELECT * FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codt."'";
        $result = query($query2, $conexion);
        $nomina = fetch_array($result);
        $fecha_ini= $nomina['periodo_ini'];
        $fecha_fin= $nomina['periodo_fin'];
        $query2 = "SELECT b.ficha,b.apenom,b.cedula,b.seguro_social,b.nomposicion_id,c.codorg,c.descrip, a.observacion, b.forcob,c.markar
            FROM nom_movimientos_nomina AS a 
            LEFT JOIN nompersonal AS b ON a.ficha = b.ficha 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id'
            GROUP BY b.ficha ORDER BY b.ficha";
        $result = query($query2, $conexion);
        while($row = fetch_array($result)){
            $fila[] = $row;
        }
        $cant_funcionario = $result->num_rows;
        $contador = 1;
        $num = 1;
        $posicion= "";
        $persona = 0;
        $totalpersonas = 0;
        $totalbd = num_rows($result);
        $pers = 0;
        $i=0;
        $sub_i=0;
        $j="";
        $lineas=0;
        $l=0;
        $tmp="";
        $descrip="";
        $z=0;
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
        $sub_total_sueldo = 0;
        if($cant_funcionario > 0)
        {

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
            $sub_total_sueldo = 0;
            for($wxx = 0;$wxx<count($fila);$wxx++) 
            {
                //echo $fila[$wxx]['markar']," ",$j,"<br>";
                if ($fila[$wxx]['descrip']!=$tmp) 
                {
                    $descrip=$tmp;
                    $tmp=$fila[$wxx]['descrip'];
                    $sw = false;
                    if($tmp != "" AND $j!= $fila[$wxx][markar])
                        $sw = true;
                }else
                {
                    $tmp=$fila[$wxx]['descrip'];
                    $sw = false;
                }

                /****         SUBTOTALES DE PLANILLA           ****/

                /*if ( $sw )
                {
                    $this->SetFont("Arial", "", 6);
                    $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
                    $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
                    $this->Setceldas(array('1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1'));
                    $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
                    $z++;
                    $este_sql      = "SELECT descrip from nomnivel1 where markar  = '{$j}'";
                    $conection     = new bd($_SESSION['bd_nomina']);
                    $res_nivel1    = $conection->query($este_sql);
                    $descrip_nv1 = $res_nivel1->fetch_assoc();
                    $total_partida = $this->totalPartida($nomina_id,$codt,$conexion,$j);
                    $total_acreedores_partida = $this->getTotalAcreedorFuncionario($total_partida['ot'],$nomina_id,$codt,$conexion,$j);
                    $descuentos_partida = $total_partida['ss']+$total_partida['se']+$total_partida['isr']+$total_partida['siacap']+$total_acreedores_partida;
                    $neto_partida = $total_partida['sueldo'] - $total_partida['lic'] -$descuentos_partida ;
                    $this->Row(array($descrip_nv1[descrip], $sub_total_per, $sub_total_per, $sub_total_per, $sub_total_per, '','', number_format(($total_partida['sueldo']-$total_partida['lic']), 2,'.',','), number_format($total_partida['ss'], 2,'.',','), number_format($total_partida['se'], 2,'.',','), number_format($total_partida['siacap'], 2,'.',','), number_format($total_partida['isr'], 2,'.',','), number_format($total_acreedores_partida, 2,'.',','), number_format($descuentos_partida, 2,'.',','), number_format($neto_partida, 2,'.',',')));
                    //$this->Row(array($descrip_nv1[descrip], $sub_total_per, '','', number_format(($sub_total_devengado), 2,'.',','), number_format($sub_total_ss, 2,'.',','), number_format($sub_total_se, 2,'.',','), number_format($sub_total_siacap, 2,'.',','), number_format($sub_total_isr, 2,'.',','), number_format($sub_total_ot, 2,'.',','), number_format($sub_total_desc, 2,'.',','),number_format($sub_total_neto, 2,'.',',')));
                    $j=$fila[$wxx][markar];
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
                    $sub_total_sueldo = 0;
                    $sub_total_devengado = 0;
                    $lineas=$lineas-1;
                }*/
                $lsp=0;
                //$this->Ln();
                $query = "SELECT codcon, descrip, monto from nom_movimientos_nomina where ficha = '".$sficha."' and tipnom='".$codt."' and codnom = '" . $nomina_id . "' AND tipcon='D' and codcon not in (200,201,202,204,205)";
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
                $sficha = $fila[$wxx]['ficha'];
                $sposicion = str_replace("000000", "", $fila[$wxx]['nomposicion_id']);
                $sueldo_bruto=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (100) ";
                $result2            = query($query, $conexion);
                $fetch2             =fetch_array($result2);
                $sueldo_bruto       =$fetch2['monto'];
                $sub_total_sueldo += $sueldo_bruto;
                $total_sueldo_bruto += $sueldo_bruto;

                $sueldo_gr=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (145) ";
                $result2            = query($query, $conexion);
                $fetch2             =fetch_array($result2);
                $sueldo_gr       =$fetch2['monto'];
                $sub_total_gr += $sueldo_gr;
                $total_sueldo_gr += $sueldo_gr;

                $sueldo_otros_ing=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (147,169,173,175,176,177,180,181,192,193) ";
                $result2            = query($query, $conexion);
                $fetch2             =fetch_array($result2);
                $sueldo_otros_ing       =$fetch2['monto'];
                $sub_total_otros_ing += $sueldo_otros_ing;
                $total_sueldo_otros_ing += $sueldo_otros_ing;

                $sueldo_aus_tard=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (198,199) ";
                $result2            = query($query, $conexion);
                $fetch2             =fetch_array($result2);
                $sueldo_aus_tard       =$fetch2['monto'];
                $sub_total_aus_tard += $sueldo_aus_tard;
                $total_sueldo_aus_tard += $sueldo_aus_tard;

                $sueldo_hr_ext=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (103,106,107,108,109,123,158,159,160,161) ";
                $result2            = query($query, $conexion);
                $fetch2             =fetch_array($result2);
                $sueldo_hr_ext       =$fetch2['monto'];
                $sub_total_hr_ext += $sueldo_hr_ext;
                $total_sueldo_hr_ext += $sueldo_hr_ext;

                $sueldo_add=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (115) ";
                $result3            = query($query, $conexion);
                $fetch3             =fetch_array($result3);
                $sueldo_add       =$fetch3['monto'];
                $sub_total_add += $sueldo_add;
                $total_sueldo_add += $sueldo_add;

                $sueldo_domingo=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (111,112,113,62,163,164,167) ";
                $result3            = query($query, $conexion);
                $fetch3             =fetch_array($result3);
                $sueldo_domingo       =$fetch3['monto'];
                $sub_total_domingo += $sueldo_domingo;
                $total_sueldo_domingo += $sueldo_domingo;

                $sueldo_feriado=0;

                $query = "SELECT ROUND((monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (122,165) ";
                $result3            = query($query, $conexion);
                $fetch3             =fetch_array($result3);
                $sueldo_feriado       =$fetch3['monto'];
                $sub_total_feriado += $sueldo_feriado;
                $total_sueldo_feriado += $sueldo_feriado;

                $dias=0;
                $lic=0;
                $query = "SELECT ROUND(SUM(monto),2) as monto,valor 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (150,199,198)";
                $result4   = query($query, $conexion);                
                $fetch4    =fetch_array($result4);
                $dias      ='- '.number_format($fetch4['valor'], 0).' DIAS LIC';
                $lic       =$fetch4['monto'];
                $sub_total_lic += $lic;
                $lic_total += $lic;

                $ss=0;
                $query = "SELECT ROUND(SUM(monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (200,205)";
                $result4 = query($query, $conexion);                
                $fetch4=fetch_array($result4);
                $ss=$fetch4['monto'];
                $sub_total_ss += $ss;
                $ss_total +=round($ss,2);

                $ss_gr=0;
                $query = "SELECT ROUND(SUM(monto),2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon in (210)";
                $result4 = query($query, $conexion);                
                $fetch4=fetch_array($result4);
                $ss_gr=$fetch4['monto'];
                $sub_total_ss_gr += $ss_gr;
                $ss_gr_total +=round($ss_gr,2);

                $se=0;
                $query = "SELECT ROUND(monto,2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=201";
                $result5  = query($query, $conexion);
                $fetch5   =fetch_array($result5);
                $se       =$fetch5['monto'];
                $sub_total_se += $se;
                $se_total +=round($se,2);

                $isr=0;
                $query = "SELECT ROUND(monto,2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=202";
                $result3   = query($query, $conexion);                
                $fetch3    =fetch_array($result3);
                $isr       =$fetch3['monto'];
                $sub_total_isr += $isr;
                $isr_total += $isr;
                $isr_gr=0;
                $query = "SELECT ROUND(monto,2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=208";
                $result3   = query($query, $conexion);                
                $fetch3    =fetch_array($result3);
                $isr_gr       =$fetch3['monto'];
                $sub_total_isr_gr += $isr_gr;
                $isr_gr_total += $isr_gr;
                
                $siacap=0;
                $query = "SELECT ROUND(monto,2) as monto 
                FROM nom_movimientos_nomina a
                LEFT JOIN  nomnivel1 nv1 on (a.codnivel1 = nv1.codorg)
                WHERE ficha = '$sficha' AND tipnom = '$codt' AND codnom = '$nomina_id' AND codcon=209";
                $result6      = query($query, $conexion);
                $fetch6       =fetch_array($result6);
                $siacap       +=$fetch6['monto'];
                $siacap_total +=$siacap;
                $sub_total_siacap += $siacap;
                $ot=0;
                 $query = "SELECT ifnull( SUM( monto ) ,0) AS monto 
                FROM nom_movimientos_nomina AS a 
                LEFT JOIN nom_nominas_pago D  ON (D.codnom = A.codnom AND D.tipnom = A.tipnom)   
                WHERE A.codcon >= 500 AND A.codcon <= 599
                AND A.tipcon = 'D' AND A.ficha = '$sficha' AND A.tipnom = '$codt' AND A.codnom = '$nomina_id'";
                $result7 = query($query, $conexion);
                $fetch7  =fetch_array($result7);
                $ot      =$fetch7['monto'];

                $sub_total_ot += $ot;
                $total_ot += $ot;
                
                $devengado       = $sueldo_bruto+$sueldo_gr+$sueldo_add+$sueldo_domingo+$sueldo_otros_ing+$sueldo_hr_ext+$sueldo_feriado-$lic;
                $sub_total_devengado  += $devengado;

                $totales_deducciones = round($isr,2)+round($ss,2)+round($se,2)+round($siacap,2)+round($ot,2);

                $total_deduccion = $isr+$ss+$se+$siacap+$ot;
                $sub_total_desc  += $total_deduccion;

                $sueldo_neto     = $devengado-$totales_deducciones;
                $sub_total_neto  += $sueldo_neto;
            	$suma_netos += $sueldo_neto;
            	$suma_deducciones += $totales_deducciones ;
                if ($fila[$wxx]['markar'] >= $j)
                {

                    $sub_i=$sub_i+1;
                    $this->SetFont('Arial','', 6);
                    $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
                    $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
                    $this->Setceldas(array('B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
                    
                    $this->SetLineWidth(0.1);
                    $this->SetDash(0.1,1);
                    $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
                    $this->SetFont('Arial','', 6);
                    $nombre="".$fila[$wxx]['apenom']."";
                    /*if($fila[$wxx]['forcob'] == "Cheque"){
                        $forcob = "CH";
                    }
                    else
                    {
                        $forcob = strtoupper($fila[$wxx]['forcob']);
                    }*/
                    $this->Row(array(($nombre),number_format($sueldo_bruto, 2, '.',','),number_format($sueldo_gr, 2, '.',','),number_format($sueldo_otros_ing+$sueldo_add, 2, '.',','),number_format($sueldo_aus_tard, 2, '.',','),number_format($sueldo_hr_ext, 2, '.',','),number_format($sueldo_domingo, 2, '.',','),number_format($sueldo_feriado, 2, '.',','),number_format($devengado, 2, '.',','),number_format($ss, 2, '.',','),number_format($ss_gr, 2, '.',','),number_format($se, 2, '.',','),number_format($isr, 2, '.',','),number_format($isr_gr, 2, '.',','),number_format($ot, 2, '.',','),number_format($sueldo_neto, 2, '.',',')));
                    //$lineas=$lineas-1;
                    $this->SetFont('Arial','', 6);
                    $this->SetWidths(array(30,30));
                    //$this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','C'));
                    $this->Setceldas(array('0','0'));
                    $this->Setancho(array(5,5));
                    
                    $lsp=0;
                    //---------------------------------------------------------------------------------------------
                    $sub_total_per      = $sub_i;
                    $sub_total_lic      = $sub_total_lic+$lic;
                    $sub_total_sq       = $sub_total_sq+$devengado;
                    $sub_total_sq       = round($sub_total_sq,2); 
                    $sub_total_siacap   = $sub_total_siacap+$siacap;
                    $sub_total_isr      = $sub_total_isr+$isr;
                    $sub_total_se       = $sub_total_se+$se;
                    $sub_total_ss       = $sub_total_ss+round($ss,2);
                    $sub_total_ot       = $sub_total_ot+$cuot;
                    $sub_total_desc     = $sub_total_desc+$totales_deducciones;
                    $sub_total_neto     = $sub_total_neto+$sueldo_neto;
                    //---------------------------------------------------------------------------------------------
                    $total_per      = $i;
                    $ss_totales +=round($ss,2);
                    $se_totales +=round($se,2);
                    $isr_totales +=round($isr,2);
                    $siacap_totales +=round($siacap,2);
                    $total_desc +=round($totales_deducciones,2);
                }
            }
            $this->SetFont("Arial", "B", 10);
            $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
            $this->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Setceldas(array('0','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
            $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
            $z++;
            $este_sql      = "SELECT descrip FROM nomnivel1 WHERE markar  = '{$j}'";
            $conection     = new bd($_SESSION['bd_nomina']);
            $res_nivel1    = $conection->query($este_sql);
            $nombre_nivel1 = $res_nivel1->fetch_assoc();
            $nombre_nivel1[descrip] = str_replace("/", "-", $nombre_nivel1[descrip]);
            
            $total_partida = $this->totalPartida($nomina_id,$codt,$conexion,$j);
            $total_acreedores_partida = $this->getTotalAcreedorFuncionario($total_partida['ot'],$nomina_id,$codt,$conexion,$j);
            $descuentos_partida = $total_partida['ss']+$total_partida['se']+$total_partida['isr']+$total_partida['siacap']+$total_acreedores_partida;
            $neto_partida = $total_partida['sueldo'] - $total_partida['lic'] -$descuentos_partida ;
            //$this->Row(array($nombre_nivel1['descrip'], $sub_total_per, '','', number_format(($total_partida['sueldo']-$total_partida['lic']), 2,'.',','), number_format($total_partida['ss'], 2,'.',','), number_format($total_partida['se'], 2,'.',','), number_format($total_partida['siacap'], 2,'.',','), number_format($total_partida['isr'], 2,'.',','), number_format($total_acreedores_partida, 2,'.',','), number_format($descuentos_partida, 2,'.',','), number_format($neto_partida, 2,'.',',')));
            
            //$this->Row(array("-".$nombre_nivel1['descrip'], $sub_total_per, '', number_format($sub_total_sq, 2, '.',','), number_format($sub_total_ss, 2, '.',','), number_format($sub_total_se, 2, '.',','), number_format($sub_total_siacap, 2, '.',','), number_format($sub_total_isr, 2, '.',','), number_format($sub_total_ot, 2, '.',','), number_format($sub_total_desc, 2, '.',','), number_format($sub_total_neto, 2, '.',',')
            //));
            //-------------------------------------------------------------------------------
            $total_per      = $i;
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon in (100,115) ";            
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_bruto = ($fetch2['monto'] != 0) ? round($fetch2['monto'],2) : 0 ;
            //-------------------------------------------------------------------------------
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon in (150,198)";            
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_lic = ($fetch2['monto'] != 0) ? round($fetch2['monto'],2) : 0 ;
            //-------------------------------------------------------------------------------
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=200";
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_ss = ($fetch2['monto'] != 0) ? $fetch2['monto'] : 0 ;
            //-------------------------------------------------------------------------------

            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=201";            
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_se = ($fetch2['monto'] != 0) ? $fetch2['monto'] : 0 ;
            //-------------------------------------------------------------------------------
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=202";            
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_isr = ($fetch2['monto'] != 0) ? $fetch2['monto'] : 0 ;
            //-------------------------------------------------------------------------------
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codcon=209";
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_siacap = ($fetch2['monto'] != 0) ? $fetch2['monto'] : 0 ;
            //-------------------------------------------------------------------------------
            $sql="SELECT SUM(monto) as monto 
            FROM nom_movimientos_nomina AS a 
            INNER JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg   
            WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.tipcon = 'D' AND A.codcon >= 500 AND A.codcon <= 599";            
            $result2 = query($sql, $conexion);
            $fetch2=fetch_array($result2);
            $total_ot = ($fetch2['monto'] != 0) ? $fetch2['monto'] : 0 ;
            //-------------------------------------------------------------------------------
            $total_desc      = $ss_total + $ss_gr_total + $isr_totales + $se_total + $siacap_totales + $total_ot+$isr_gr_total;
            $total_devengado = $total_bruto-$lic_total;
            $total_neto      = $total_devengado+$total_sueldo_gr+$total_sueldo_otros_ing - $total_desc;
            $this->SetFont("Arial", "B", 6);
            $this->SetWidths(array(70,14,14,14,14,14,14,14,14,13,13,13,15,15,16,14));
            $this->SetAligns(array('C','C','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
            $this->Setceldas(array('0','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B','B'));
            $this->Setancho(array(4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4));
            $this->Row(array($sub_total_per, number_format($total_sueldo_bruto, 2, '.',','), number_format($total_sueldo_gr, 2, '.',','), number_format($total_sueldo_otros_ing, 2, '.',','), 
            number_format($total_sueldo_aus_tard, 2, '.',','), number_format($total_sueldo_hr_ext, 2, '.',','),number_format($total_sueldo_domingo, 2, '.',','), number_format($total_sueldo_feriado, 2, '.',','), 
            number_format($total_sueldo_bruto+$total_sueldo_gr+$total_sueldo_otros_ing, 2, '.',','), number_format($ss_total, 2, '.',','), number_format($ss_gr_total, 2, '.',','), number_format($se_total, 2, '.',','), 
            number_format($isr_total, 2, '.',','),             number_format($isr_gr_total, 2, '.',','), number_format($total_ot, 2, '.',','), number_format($total_neto, 2, '.',',')));
            $this->Ln(10);
            $this->SetFont("Arial", "B", 8);
            $this->SetWidths(array(55, 40, 50, 40, 52, 40));
            $this->SetAligns(array('C','L','C','L','C','L'));
            $this->Setceldas(array('0', 'B', '0', 'B', '0', 'B'));
            $this->Setancho(array(5, 5, 5, 5, 5, 5));
            $this->Row(array('Revisado Por','', 'Confeccionado Por:','', 'Director(a) de Recursos Humanos:',''));
        }
        else
        {
            if ($lineas < 6)
            {
                $this->AddPage('L', 'A4');
                $lineas=24;
            }
            $query2 = "SELECT * FROM nomtipos_nomina WHERE  codtip = '".$codt."'";
            $result = query($query2, $conexion);
            $nomina = fetch_array($result);
            $descrip= $nomina[descrip];
            $this->Ln();
            $this->SetFont('Arial','', 8);
            $this->SetWidths(array(50,40,20,18,18,18,18,18,18,18,18,18,18));
            $this->SetAligns(array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','C'));
            $this->Setceldas(array('0','0','0','0','0','0','0','0','0','0','0','0','0','0','0','0'));
            $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5));
            $this->Row(array($descrip, '----', '0.00', '0.00', '0.00','0.00', '0.00', '0.00','0.00','0.00','0.00','0.00'));
        }
    }
}
$pdf = new PDF();
$pdf->codnom = $nomina_id;
$pdf->tipnom = $codt;
$pdf->AddFont('Sanserif', '', 'sanserif.php');
$pdf->SetFont('Sanserif', '', 10);
$pdf->personas($nomina_id, $codt, $pdf);
$pdf->Output();
?>
