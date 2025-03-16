<?php
session_start();
ob_start();

require('fpdf.php');
include("../lib/common.php");

$nomina_id = $_GET['nomina_id'];
$codt = $_GET['codt'];

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Consultar datos de cabecera
        $nomina_id = $_GET['nomina_id'];
        $codt      = $_GET['codt'];
        $conexion = conexion();
        //cargo la empresa
        //Array de meses
        $mes = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        $sql = "SELECT * FROM nomempresa";
        $result = query($sql, $conexion);
        $empresa = fetch_array($result);
        //cargo la planilla
        $sql = "SELECT * FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codt."'";
        $result   = query($sql, $conexion);
        $planilla = fetch_array($result);
        $num_pla  = $planilla['codnom']."-".$planilla['anio'];
        //Cargando datos
        $frecuencia = ( $planilla['frecuencia'] == 2 ) ? "Primera Quincena de ".$mes[$planilla['mes']]." de ".$planilla['anio'] : "Segunda Quincena de".$mes[$planilla['mes']]." de ".$planilla['anio'];
        // Título
        $this->SetFont("Arial", "B", 14);
        $this->Cell(0, 9, utf8_decode($empresa['nom_emp']), 0, 1, 'C');
        
        $this->SetFont("Arial", "B", 10);
        $this->Cell(100, 9, "Fecha ".date('d/m/Y'), 0, 0, 'C');
        
        $this->SetFont("Arial", "B", 14);
        $this->Cell(128, 9, 'RECAPITULACION DE LA PLANILLA DEL PERIODO', 0, 0, 'C');
        
        $this->SetFont("Arial", "B", 10);
        $this->Cell(100, 9, 'Pagina '.$this->PageNo().'/{nb}', 0, 1, 'C');

        $this->SetFont("Arial", "B", 14);
        $this->Cell(0, 9, date ( 'd-m-Y' , strtotime ( $planilla['periodo_ini'] ) )." al ".date ( 'd-m-Y' , strtotime ( $planilla['periodo_fin'] ) ), 0, 1, 'C');
        //Serapador
        $this->Cell(0, 3, "", 0, 1, 'C');
        //titulos de columnas
        $this->SetFont("Arial", "B", 8);
        $this->Cell(40, 10, 'Partidas', "TB", 0, 'L');
        $this->Cell(30, 10, 'Sueldo Quinc.', "TB", 0, 'L');
        $this->Cell(30, 10, 'Total. Devengado', "TB", 0, 'L');
        $this->Cell(30, 10, 'Total. Desc Lic', "TB", 0, 'L');
        $this->Cell(30, 10, 'Fondo Comp', "TB", 0, 'L');
        $this->Cell(30, 10, 'Seguro Educ', "TB", 0, 'L');
        $this->Cell(30, 10, 'Impuesto S/Renta', "TB", 0, 'L');
        $this->Cell(30, 10, 'Seguro Social', "TB", 0, 'L');
        $this->Cell(30, 10, 'Otros Descuentos', "TB", 0, 'L');
        $this->Cell(30, 10, 'Total Descuentos', "TB", 0, 'L');
        $this->Cell(30, 10, 'Sueldo Neto', "TB", 1, 'L');
    }

    function recapitulacion($nomina_id, $codt, $pdf)
    {
        $conexion = conexion();
        $sql = "SELECT * FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codt."'";
        $result   = query($sql, $conexion);
        $planilla = fetch_array($result);
        $num_pla  = $planilla['codnom']."-".$planilla['anio'];
        
        $query2 = "SELECT b.codorg,b.markar AS partida FROM nom_movimientos_nomina AS a,nomnivel1 AS b WHERE a.tipnom = '$codt' AND codnom = '$nomina_id' AND a.codnivel1 = b.codorg GROUP BY a.codnivel1 ORDER BY a.codnivel1";
        
        $result = query($query2, $conexion);
        /* determinar el número de filas del resultado */
        $total_filas = mysqli_num_rows($result);

        $total_quinc=0;
        $i=0;
        $lineas=1;
        while ($fila = fetch_array($result))
        {
            $lineas = ( $lineas > 18 ) ? 1 : $lineas;
            $i++;
            $nump = ( $i < 10 ) ? "0".$i : $i;
            $codnivel1=$fila['codorg'];
            //calcular subtotales
            //subtotal Sueldos
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=100";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_quinc=$fetch2['monto'];
            //--------------------------------------------------------------------
            $query = "SELECT ifnull(SUM(monto),0) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=150";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_lic=$fetch2['monto'];
            //--------------------------------------------------------------------
            //subtotal SIACAP
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=204";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_siacap=$fetch2['monto'];
            //--------------------------------------------------------------------
            //subtotal Seguro educativo
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=201";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_se=$fetch2['monto'];
            //--------------------------------------------------------------------
            //subtotal Impuesto Sobre la renta
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=202";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_isr=$fetch2['monto'];
            //--------------------------------------------------------------------
            //subtotal Seguro social
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.codcon=200";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_ss=$fetch2['monto'];
            //--------------------------------------------------------------------
            //subtotal OBLIGACIONES (Otros descuentos)
            $query = "SELECT SUM(a.monto) AS monto FROM nom_movimientos_nomina AS a WHERE a.tipnom = '$codt' AND a.codnom = '$nomina_id' AND a.codnivel1 = '$codnivel1' AND a.tipcon='D' AND a.codcon NOT IN (100,150,200,201,202,204)";
            $result2 = query($query, $conexion);
            $fetch2=fetch_array($result2);
            $subtotal_obligaciones=$fetch2['monto'];
            //--------------------------------------------------------------------
            //Subtotal_devengado
            $subtotal_devengado = $subtotal_quinc - $subtotal_lic;
            $subtotal_desc=$subtotal_siacap+$subtotal_se+$subtotal_isr+$subtotal_ss+$subtotal_obligaciones+$subtotal_lic;

            $subtotal_neto   = $subtotal_devengado - $subtotal_desc;
            //--------------------------------------------------------------------
            //Subtotales a imprimir
            $this->SetFont("Arial", "B", 8);
            $this->Cell(40, 8, $nump."   ".$fila['partida'], "B", 0, 'L');
            $this->Cell(30, 8, number_format($subtotal_quinc, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_devengado, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_lic, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_siacap, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_se, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_isr, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_ss, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_obligaciones, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_desc, 2, '.',','), "B", 0, "L");
            $this->Cell(30, 8, number_format($subtotal_neto, 2, '.',','), "B", 1, "L");
            //Cuento la cantidad de lineas para poder cuadrar la ubicacion de las firmas
            $lineas++;
            //total general
            $total_quinc        = $total_quinc + $subtotal_quinc;
            $total_devengado    = $total_devengado + $subtotal_devengado;
            $total_lic          = $total_lic + $subtotal_lic;
            $total_siacap       = $total_siacap + $subtotal_siacap;
            $total_se           = $total_se + $subtotal_se;
            $total_isr          = $total_isr + $subtotal_isr;
            $total_ss           = $total_ss + $subtotal_ss;
            $total_obligaciones = $total_obligaciones + $subtotal_obligaciones;
            $total_desc         = $total_desc + $subtotal_desc;//12,25 %
            $total_neto         = $total_neto + $subtotal_neto;
        }
            //Imprimiendo totales
            $this->SetFont("Arial", "B", 10);
            $this->Cell(40, 10, "Totales     ", "TB", 0, 'R');
            $this->Cell(30, 10, number_format($total_quinc, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_devengado, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_lic, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_siacap, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_se, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_isr, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_ss, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_obligaciones, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_desc, 2, '.',','), "TB", 0, "L");
            $this->Cell(30, 10, number_format($total_neto, 2, '.',','), "TB", 1, "L");
            /*
            //Seccion de firmas
            $resto=18-$lineas;
            $mm=(8*$resto)-2;
            $this->SetFont("Arial", "B", 10);
            $this->Cell(0, 10, " Seccion de Tramites (Sellos)", 0, 1, 'L');
            $this->SetFont("Arial", "B", 8);
            //Primera fila de firmas
            $this->ln(8);
            $this->Cell(60, 9, " Confeccionado por:", "T", 0, 'C');
            $this->Cell(40, 9, "", 0, 0, 'C');
            $this->Cell(60, 9, " Alcalde Municipal", "T", 0, 'C');
            $this->Cell(10, 9, "", 0, 0, 'C');
            $this->Cell(30, 9, " Fecha de Firma", "T", 0, 'C');
            $this->Cell(10, 9, "", 0, 0, 'C');
            $this->Cell(60, 9, " Jefe de Contabilidad", "T", 0, 'C');
            $this->Cell(10, 9, "", 0, 0, 'C');
            $this->Cell(30, 9, " Fecha de Firma", "T", 1, 'C');
            //Segunda fila de firmas
            $this->ln(8);
            $this->Cell(60, 9, " Dir. De Rec. Humanos:", "T", 0, 'C');
            $this->Cell(40, 9, "", 0, 0, 'C');
            $this->Cell(60, 9, " Jefe de Fiscalizacion", "T", 0, 'C');
            $this->Cell(10, 9, "", 0, 0, 'C');
            $this->Cell(30, 9, " Fecha de Firma", "T", 1, 'C');
            //Tercera fila de firmas
            $this->ln(8);
            $this->Cell(20, 9, " Monto:", 0, 0, 'R');
            $this->Cell(20, 6, number_format($total_patrono, 2, '.',','), "B", 0, 'L');
            $this->Cell(20, 9, " Fecha:", 0, 0, 'R');
            $this->Cell(20, 6, date('d/m/Y'), "B", 0, 'L');
            $this->Cell(20, 9, "", 0, 0, 'C');
            $this->Cell(25, 9, " Autorizado Por:", 0, 0, 'C');
            $this->Cell(60, 6, "", "B", 1, 'L');
            //Cuarta fila de firmas
            $this->Cell(20, 9, " Registro No:", 0, 0, 'R');
            $this->Cell(20, 6, $num_pla, "B", 0, 'L');
            $this->Cell(85, 9, "", 0, 0, 'C');
            $this->Cell(60, 6, " Tesorero Municipal", 0, 1, 'C');*/
    }
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');
$pdf->AliasNbPages();
$pdf->AddPage('L','Legal');
$pdf->SetFont('Times','',8);

$pdf->recapitulacion($nomina_id, $codt, $pdf);

$pdf->Output();
?>