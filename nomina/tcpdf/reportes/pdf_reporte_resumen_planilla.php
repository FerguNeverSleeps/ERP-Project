<?php
//============================================================+
// File name   : pdf_reporte_resumen_planilla.php
// Create      : 14-09-2016
// Last Update : 15-09-2016
//
// Description : Reporte Mensual Resumen de Planilla
//
// Author: Marianna Pessolano
//============================================================+
require_once('../../lib/database.php');

date_default_timezone_set('America/Panama');

if(isset($_GET['nomina']))
{
	$codnom = $_GET['nomina'];
	$tipnom = $_SESSION['codigo_nomina']; // $_GET['codt'];

    $count_codnom = count( explode(',', $codnom) );
    $salto_linea = 0;

	$db = new Database($_SESSION['bd']);

	require '../tcpdf.php';

    class MYPDF extends TCPDF {

        public function Header() {
            global $db, $codnom, $tipnom, $count_codnom, $salto_linea;

            if($count_codnom<=4 || ($this->page == 1 && $count_codnom>4 ) )
            {
                $sql = "SELECT nom_emp AS nombre FROM nomempresa";
                $emp = $db->query($sql)->fetch_object();

                $this->SetFont('helvetica', '', 12);
                $this->Cell(150, 0, $emp->nombre, 0, 0, 'L');
                $this->Cell(0, 0, 'Fecha: ' . date('d/m/Y'), 0, 1, 'R');
                $this->Cell(0, 0, 'Hora:    ' . date('h:i a'), 0, 1, 'R');

                $this->SetFont('helvetica', 'B', 12);
                $this->Cell(0, 10, 'RESUMEN DE CONCEPTOS', 0, 1, 'C');

                $this->SetFont('helvetica', '', 12);
                $sql = "SELECT descrip
                    FROM nom_nominas_pago
                    WHERE codnom IN ({$codnom}) AND tipnom='{$tipnom}'";
                $res = $db->query($sql);

                while($fila = $res->fetch_object())
                {
                    $this->Cell(0, 0, $fila->descrip, 0, 1, 'C');
                    $salto_linea += 6;
                }

                if($count_codnom <= 4)
                    $salto_linea = 0;
                else
                    $salto_linea += 3;
            }
        }

        public function Footer() {
            global $db;

            $this->SetY(-45);

            $this->SetFont('helvetica', '', 10);

            $style = array('width' => 0.2,
                           'cap' => 'butt',
                           'join' => 'miter',
                           'dash' => 0,
                           'color' => array(0, 0, 0));

            $y = $this->GetY();
            $this->Line(82, $y, 130, $y, $style);

            $sql = "SELECT ger_rrhh FROM nomempresa";
            $emp = $db->query($sql)->fetch_object();

            $this->Ln(5);
            $this->Cell(0, 0, 'COORDINADOR DE RRHH', 0, 1, 'C');
            $this->Cell(0, 0, $emp->ger_rrhh, 0, 1, 'C');

            $this->SetY(-15);
            $pagina =  '               Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();
            $this->Cell(0, 0, $pagina, 0, 1, 'C');
        }
    }

	$pdf = new MYPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

	$pdf->SetAuthor('Selectra');
	$pdf->SetTitle('Resumen de Planilla Mensual');

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    $pdf->SetMargins(10, 30, 14);
    $pdf->SetHeaderMargin(12);
    $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, 58);

	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    if($count_codnom <= 4)
        $pdf->SetTopMargin(60);

	// ---------------------------------------------------------
	$pdf->AddPage();

    if($salto_linea > 0)
        $pdf->Ln($salto_linea);

	$pdf->SetFont('helvetica', '', 10);

	$sql = "SELECT n.codcon, c.descrip, c.tipcon, c.ctacon, SUM(monto) AS monto_total
			FROM nom_movimientos_nomina n
			INNER JOIN nomconceptos c ON c.codcon=n.codcon
			WHERE codnom IN ({$codnom}) AND tipnom='{$tipnom}'
			GROUP BY n.codcon, c.descrip, c.tipcon, c.ctacon";
	$res = $db->query($sql);

	$table = '';
	$totalA = $totalD = $totalP = 0;
	while($concepto = $res->fetch_object())
	{
		$monto = number_format($concepto->monto_total, 2, ',', '.');

		$ctacon = $asignaciones = $deducciones = $patronales = '';
		if($concepto->tipcon == 'A')
		{
			$asignaciones = $monto;
            $ctacon = $concepto->ctacon;
			$totalA += $concepto->monto_total;
		}
		elseif($concepto->tipcon == 'D')
		{
			$deducciones = $monto;
			$totalD += $concepto->monto_total;
		}
		elseif($concepto->tipcon == 'P')
		{
			$patronales = $monto;
			$totalP += $concepto->monto_total;
		}

		$table .= 	'<tr style="font-size: 8pt">'.
					'<td style="width:260px;">'.$concepto->codcon.' - '. utf8_decode($concepto->descrip) .'</td>'.
					'<td style="width:120px; text-align: center">'.$ctacon.'</td>'.
				 	'<td style="width:100px; text-align: right">'.$asignaciones.'</td>'.
					'<td style="width:100px; text-align: right">'.$deducciones.'</td>'.
					'<td style="width:100px; text-align: right">'.$patronales.'</td>'.
					'</tr>';
	}

    $html = '<table style="padding: 3px">
                <thead>
				<tr>
					<td style="width:260px; border-top: 1px solid black; border-bottom: 1px solid black">Código y Descripción del Concepto</td>
					<td style="width:120px; text-align: center; border-top: 1px solid black; border-bottom: 1px solid black">Cuenta Contable</td>
					<td style="width:100px; text-align: right; border-top: 1px solid black; border-bottom: 1px solid black">Asignaciones</td>
					<td style="width:100px; text-align: right; border-top: 1px solid black; border-bottom: 1px solid black">Deducciones</td>
					<td style="width:100px; text-align: right; border-top: 1px solid black; border-bottom: 1px solid black">Patronales</td>
				</tr>
				</thead>
				<tbody>' . $table .
                '<tr style="font-size: 12pt">
                    <td colspan="2" style="text-align: right; border-top: 1px solid black;">TOTALES</td>
                    <td style="text-align: right; border-top: 1px solid black;">'. number_format($totalA, 2, ',', '.') .'</td>
                    <td style="text-align: right; border-top: 1px solid black;">'. number_format($totalD, 2, ',', '.') .'</td>
                    <td style="text-align: right; border-top: 1px solid black;">'. number_format($totalP, 2, ',', '.') .'</td>
                 </tr>
                 <tr style="font-size: 12pt">
                    <td colspan="3" style="text-align: right; border-bottom: 1px solid black;">TOTAL NETO:</td>
                    <td colspan="2" style="text-align: right; border-bottom: 1px solid black;">'. number_format($totalA-$totalD, 2, ',', '.') .'</td>
                 </tr>
                 </tbody>
              </table>';

	$pdf->writeHTML($html, true, false, true, false, '');
	// ---------------------------------------------------------
	//Close and output PDF document
	$pdf->Output('resumen_de_planilla.pdf', 'I');
}
else
{
    echo "Acceso Denegado";
}
//============================================================+
// END OF FILE
//============================================================+