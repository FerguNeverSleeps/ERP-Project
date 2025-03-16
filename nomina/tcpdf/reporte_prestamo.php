<?php
//============================================================+
// File name   : reporte_pretamo.php
// Begin       : 2014-09-13
// Last Update : 2014-09-13
//
// Description : Reporte de estado de cuenta de préstamos
//
// Author: Marianna Pessolano
//============================================================+

if(isset($_SESSION)){
	session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');
require('tcpdf.php');
require_once('../lib/common.php'); // funciones
//include ("../paginas/func_bd.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

$conexion=conexion();

if(isset($_GET['numpre'])){
	$numpre = (int) $_GET['numpre'];

	// create new PDF document
	$pdf = new TCPDF('P', PDF_UNIT, 'LETTER', true, 'UTF-8', false);

	$pdf->SetAuthor('Selectra');
	$pdf->SetTitle('Reporte');

	// remove default header/footer
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();

	// Consultar datos del prestamo
	$consulta="SELECT * FROM nomprestamos_cabecera where numpre=$_GET[numpre]";
	$resultado=query($consulta,$conexion);
	$fetch=fetch_array($resultado);

	$consulta="SELECT * FROM nomprestamos_detalles where numpre=$_GET[numpre]";
	$resultado2=query($consulta,$conexion);

	$html = '<table width="100%" border="0"  cellspacing="0" cellpadding="3">
				<tr style="font-weight : bold; background-color: #5084A9; color: #EEEEEE;">
					<td width="40%">Numero de prestamo: '.$fetch[numpre].'</td>
					<td width="30%">Ficha: '.$fetch[ficha].'&nbsp;</td>
					<td width="30%" align="center">Tipo: '.$fetch[codigopr].'&nbsp;</td>
				</tr>
			 </table>';

	$html .= '<br><br>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<strong>Monto del prestamo:</strong> '.$fetch[monto].'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<strong>Cuotas a monto fijo:</strong> '.$fetch[mtocuota].'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<strong>Número de cuotas:</strong> '.$fetch[cuotas].'
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<strong>Descripción del prestamo:</strong> '.$fetch[detalle].'
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td><strong>Frecuencia de deducción:</strong> Quincenal</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		<tr>
			<td>
				<strong>Fecha de Aprobación:</strong> '.fecha($fetch[fechaapro]).'
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<strong>Fecha de vencimiento 1era cuota:</strong> '.fecha($fetch[fecpricup]).'
			</td>
		</tr>
		<tr><td>&nbsp;</td></tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="40%"><strong>Monto de cuotas:</strong> &nbsp;&nbsp;&nbsp;'.$fetch[mtocuota].'</td>
		<td width="60%"></td>
		</tr>
		</table>
		<br><br><br>';

	$html.= '
	<table width="100%" border="0">
	<tr>
	<td>
	<div id="divcuo">
	<table width="100%" border="0" cellpadding="3">
	<tr class="tb-head"  style="font-weight : bold; background-color: #5084A9; color: #EEEEEE;">
	<td width="13%"># Cuota</td>
	<td width="15%" align="center">Vence</td>
	<td width="14%" align="right">Saldo inicio</td>
	<td width="15%" align="right">Amortizado</td>
	<td width="14%" align="right">Cuota</td>
	<td width="14%" align="right">Saldo fin</td>
	<td width="15%" align="center">Status</td>
	</tr>';

	while($fetch2=fetch_array($resultado2))
	{
		$html .= '<tr>
					<td width="13%">'.$fetch2[numcuo].'</td>
					<td width="15%" align="center">'.fecha($fetch2[fechaven]).'</td>
					<td width="14%" align="right">'.$fetch2[salinicial].'</td>
					<td width="15%" align="right">'.$fetch2[montocuo].'</td>
					<td width="14%" align="right">'.$fetch2[montocuo].'</td>
					<td width="14%" align="right">'.$fetch2[salfinal].'</td>
					<td width="15%" align="center">'.$fetch2[estadopre].'</td>
				  </tr>';
	}

	$html.= '</table>
</div>
</td>
</tr>
</table>';

	$pdf->writeHTML($html, true, false, true, false, '');

	$pdf->Output('reporte_estado_prestamo.pdf', 'I');

}
//============================================================+
// END OF FILE
//============================================================+



