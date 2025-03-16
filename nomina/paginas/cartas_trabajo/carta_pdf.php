<?php
require('mypdf.php');

if( isset($_GET['ficha']) && isset($_GET['tipnom']) && isset($_GET['carta_id']) )
{
	$ficha    = (int) $_GET['ficha'];
	$tipnom   = (int) $_GET['tipnom'];
	$carta_id = (int) $_GET['carta_id'];

	$db = new Database($_SESSION['bd']);
	
	//===================================================================================================
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Selectra Planilla');
	$pdf->SetTitle('Carta');

	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	$pdf->AddPage();


	$sql = "SELECT nombre, contenido1, contenido2, contenido3, titulo, observaciones, formula
			FROM   nomtipos_constancia
			WHERE  codigo='{$carta_id}'";
	$res = $db->query($sql);
	$carta = $res->fetch_object();

		$sql = "SELECT n.nombres, n.apellidos, n.cedula, c.cod_car, c.des_car as cargo
				FROM   nompersonal n
				LEFT JOIN nomcargos c ON c.cod_cargo=n.codcargo
				WHERE  n.ficha='{$ficha}' AND n.tipnom='{$tipnom}'";
		$res = $db->query($sql);

		$trabajador = $res->fetch_object();

		$fullname = $trabajador->nombres . ' ' . $trabajador->apellidos;

	$mes_letras = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio",
		                "Agosto","Septiembre","Octubre","Noviembre","Diciembre");

	$DIA  = date('d');
	$MES  = strtolower($mes_letras[date('n')-1]);
	$ANIO = date('Y');

	$NOMBRE_COMPLETO = strtoupper($trabajador->nombres .' ' . $trabajador->apellidos);

	$PRIMER_APELLIDO = trim($trabajador->apellidos);
	$apellidos = explode(' ', $PRIMER_APELLIDO);
	if(count($apellidos)>0) $PRIMER_APELLIDO = $apellidos[0];

	$CEDULA = $trabajador->cedula;
	$FICHA  = $ficha;
	$CARGO  = strtoupper($trabajador->cargo);
	// $DIAS_INASISTENTE = 'el día 31 de diciembre de 2015, 2,4,6,7,8,11,12,13,14 y 15 de enero de 2016';
	// $ULTIMO_DIA = '30 de DICIEMBRE de 2015';

	if($carta->formula!='')
	{
		eval($carta->formula);
	}

	$pdf->setCellHeightRatio(1.5);

	if($carta->contenido2!='')
	{
		$pdf->SetMargins(30, 0, 43);

    	$html = str_replace('"', "'", $carta->contenido2);

    	eval("\$html = \"$html\";");

    	$html = str_replace("'", '"', $html);

		$pdf->writeHTML($html, true, false, false, false, 'J');		
	}


	$vocalesConAcentos = array('á','é','í','ó','ú',' ','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;', '(pdf)');
	$vocalesSinAcentos = array('a','e','i','o','u','_','a','e','i','o','u','');
	$nombre_carta = utf8_encode($carta->nombre);
	$nombre_carta = strtolower($nombre_carta);
	$nombre_carta = str_replace($vocalesConAcentos, $vocalesSinAcentos, $nombre_carta);
	
	$fichero = $nombre_carta . '.pdf';

	$pdf->Output($fichero, 'I');
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";
   echo "<script>document.location.href = '../list_personal.php';</script>";	
}
?>