<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('lib/php_excel.php');

function formato_fecha($fecha,$formato)
    {
	    if (empty($fecha))
	    {
	    	$fecha = date('Y-m-d');
	    }
	    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $separa = explode("-",$fecha);
        $dia = $separa[2];
	    $mes = $separa[1];
	    $anio = $separa[0];
	    switch ($formato)
	    {
	        case 1:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }

if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];

	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

	require_once 'phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra")
								->setLastModifiedBy("Selectra")
								->setTitle("Horizontal de Planilla");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
			FROM nom_nominas_pago np 
			WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$res2=$conexion->query($sql2);
	$fila2=$res2->fetch_array();

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde=$fila2['desde'];
	$hasta=$fila2['hasta'];
        
        $desde1=formato_fecha($fila2['desde'],1);
	$hasta1=formato_fecha($fila2['hasta'],1);
	$dia_ini = $fila2['dia_ini'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', strtoupper($empresa) )
				->setCellValue('A3', strtoupper($NOMINA))
				->setCellValue('A4', 'DEL '. $desde1 .' AL '. $hasta1 )
                                    ->setCellValue('A5', "Banco General");


	//$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8:Y8')->getAlignment()->setWrapText(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
        $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
//            ->setCellValue('A6', 'SALARIOS')
           
            ->setCellValue('A8', 'CEDULA')
            ->setCellValue('B8', 'NUMERO DE CUENTA')
            ->setCellValue('C8', 'NOMBRE EMPLEADO')
//            ->setCellValue('C8', 'SALARIO REGULAR')
//            ->setCellValue('D8', 'SOBRE TIEMPO')
//            ->setCellValue('E8', 'OTROS INGRESOS')
//                ->setCellValue('F8', 'OTROS')
//            ->setCellValue('G8', 'SALARIO BRUTO')
//            ->setCellValue('H8', 'S.S.')
//            ->setCellValue('I8', 'S.E.')
//            ->setCellValue('J8', 'I.S.R.')
//            ->setCellValue('K8', 'OTRAS DEDUC')
//            ->setCellValue('L8', 'TOTAL DEDUC')
            ->setCellValue('D8', 'TOTAL');

//	cellColor('C8:G8', '92d050');
//	cellColor('H8:L8', 'ffcc00');

	//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	

//
//	$objPHPExcel->getActiveSheet()->mergeCells('C6:R6');
//	$objPHPExcel->getActiveSheet()->mergeCells('S6:U6');
//	$objPHPExcel->getActiveSheet()->mergeCells('D7:I7');
	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->applyFromArray(allBordersThin());

	$objPHPExcel->getActiveSheet()->freezePane('C9'); // Inmovilizar Paneles

	/*$sql = "SELECT nn.codorg, nn.descrip,
				CASE WHEN nn.codorg=1 THEN 'Administrativos'
						WHEN nn.codorg=2 THEN 'Técnicos'
						WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
						WHEN nn.codorg=4 THEN 'Comercialización' 
				END as grupo
			FROM   nomnivel1 nn
			WHERE  nn.descrip NOT IN('Eventual', 'O&M')
			ORDER BY nn.codorg";
	$res=$conexion->query($sql);
	*/
	$i=9; $nivel=1;
	// $monto_salarios=0;
	$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
	$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
	$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
	$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
	$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII=$total_desc_varios="=";
	//while($row=$res->fetch_array())
	//{
		$codorg = $row['codorg'];

		$sql2= "SELECT np.ficha, np.suesal as salario, np.cedula, np.cuentacob,
				CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				CASE WHEN np.apellidos LIKE 'De %' THEN 
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				END as primer_apellido
				FROM   nompersonal np
				WHERE  np.tipnom=".$codtip."
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				AND    (np.estado = 'REGULAR' OR np.estado = 'Activo' OR np.estado = 'Egresado' OR np.estado ='Vacaciones')
                                AND    (np.forcob LIKE '%Cuenta%' AND np.codbancob=1)
				ORDER  BY np.cedula";
		
		$res2=$conexion->query($sql2);

		$ini=$i; $enc=false;$cont=0;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$ficha = $row2['ficha'];
			$primer_nombre = utf8_encode($row2['primer_nombre']);
			$apellido   = utf8_encode($row2['primer_apellido']);
			$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']

			//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getFont()->setSize(8);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['cedula']);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $row2['cuentacob'], PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['cuentacob']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $trabajador);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray(allBordersThin());

			$sql3 = "SELECT
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (100)),0) as regular,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=106), 0) as unopunto25,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (105,107)), 0) as unopunto5,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (111,116,122)), 0) as domingonac,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=108), 0) as dospunto18,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (109,137)), 0) as dospunto625,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (110,112,113,114,120,121,123,124,125,126,127,128,129,130,131,132,133,134,135,136,138,139,142,143,144,149,150,151,152,153)), 0) as lasdemas,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (159,160,161,162)), 0) as lluvia,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (170,171)), 0) as ausencia,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=169), 0) as cm,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (110,141,147,145,157,158)), 0) as otros,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (90,91,92,93,94,95,96,97,100,101,102,115,170,171,169)), 0) as regular_monto,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=189), 0) as com14,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (105,106,107,108,109,111,116122,112,113,114,120,121,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,142,143,144,149,150,151,152,153,163,164,165,166,167,168,172)), 0) as sobretiempo,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (159,160,161,162)), 0) as lluvia_monto,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as sss,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (201,206)), 0) as sse,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (202,208,212,213,215)), 0) as isr,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND ((n.codcon BETWEEN 500 AND 599) OR (n.codcon BETWEEN 216 AND 225) OR (n.codcon BETWEEN 203 AND 204) OR (n.codcon IN (199)))), 0) as descuentos,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=508)  as cxc,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=299)  as descuentos_varios";
			
			$res3 = $conexion->query($sql3);
			$neto=0;
			if($row3=$res3->fetch_array())
			{
				$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
				$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
				$AJUSTE = 0;

				

				$regular = 0;
				$uno25 = 0;
				$uno5 = 0;
				$uno75 = 0;
				$lasdemas = 0;
				$sumaDeHorasExt = 0;

				$regular =  $row3['regular_monto'];
				$uno25 = $row2['salario'] * $row3['unopunto25'] * 1.25;
				$uno5 = $row2['salario'] * $row3['unopunto5'] * 1.5;
				$uno75 = $row2['salario'] * $row3['unopunto75'] * 1.75;
				$lasdemas = $row2['salario'] * $row3['lasdemas'] * 2.625;
				$otrosing = $row3['otros'] + $row3['lluvia_monto'];

				$sumaDeHorasExt = $uno25 + $uno5 + $uno75 + $lasdemas;

				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.$i)->getAlignment()
											->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row2['salario']);
//				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['regular']);
//				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['unopunto25']);
//				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['unopunto5']);
//				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['domingonac']);
//				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['dospunto18']);
//				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['dospunto625']);
//				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['lasdemas']);
//				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['lluvia']);
//				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['nopaga']);
//				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['ausencia']);
//				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['cm']);
//				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $otrosing);
//				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $regular);	
//				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $row3['com14']);
//				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['sobretiempo']);
//				$salBruto = $regular + $row3['com14'] + $row3['sobretiempo'] + $row3['otrosingresos'];
				$salBruto = $regular + $row3['com14'] + $row3['sobretiempo'] + $otrosing;
//				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $salBruto);
//				//DEDUCCIONES
//				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['sss']);
//				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['sse']);
//				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['isr']);
//				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['descuentos']);
				$totalDeducciones = 0;
				$totalDeducciones = $row3['sss'] + $row3['sse'] + $row3['isr'] + $row3['descuentos'];
//				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $totalDeducciones);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, (($salBruto) - $totalDeducciones));
			
				$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO 
					- $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
					- $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos_varios'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;


				$neto = "='".$dia_fin." ".$mes_letras."'!S".$i; 

				$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			}

			$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);

			$i++;$cont++;
		}

		
		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total Emp.:               '.$cont );

//			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	
//			$total_salarios				  	.= "+C".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	
			$total_comision				  	.= "+D".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	
//			$total_extra_salario 		  	.= "+E".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	
//			$total_uso_auto	 			  	.= "+F".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	
//			$total_reembolso			  	.= "+G".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	
//			$total_bono				  		.= "+H".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	
//			$total_tardanza   			  	.= "+I".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	
//			$total_ausencia 				.= "+J".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	
//			$total_salario   				.= "+K".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	
//			$total_segurosocial_salario  	.= "+L".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	
//			$total_seguro_educativo 		.= "+M".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	
//			$total_isr_salario	 		  	.= "+N".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	
//			$total_isr_gastos				.= "+O".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");	
//			$Total_Desc_Ley  			  	.= "+P".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");	
//			$total_desc_varios 				.= "+Q".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=SUM(R".$ini.":R".($i-1).")");	
//			$total_descuentos			  	.= "+R".$i;
//			$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, "=SUM(S".$ini.":S".($i-1).")");
//			$total_neto 				  	.= "+S".$i;
//
//			$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, "=SUM(T".$ini.":T".($i-1).")");
//			$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, "=SUM(U".$ini.":U".($i-1).")");
//			$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, "=SUM(V".$ini.":V".($i-1).")");
//			$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, "=SUM(W".$ini.":W".($i-1).")");
//			$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, "=SUM(X".$ini.":X".($i-1).")");
//			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, "=SUM(Y".$ini.":Y".($i-1).")");


			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.$i)->getNumberFormat()
										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(6);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			if($nivel==1)
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray(allBordersMedium());
			}
			else
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray(allBordersThin());
			}

			$i++;
		
			$i++;	
			$nivel++;
		}
	//}

	$i++;
	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray(allBordersThin());
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
								->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(9);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
	$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));

	$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(18);

	//===========================================================================
	$objPHPExcel->setActiveSheetIndex(0); 
	$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "Empleados_ACH_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}
exit;
