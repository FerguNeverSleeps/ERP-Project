<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');


if(!isset($_REQUEST["id_bisemana"]) or !$_REQUEST["id_bisemana"])
	exit;

$id_bisemana=$_REQUEST["id_bisemana"];

$conexion=conexion();


$sql="SELECT *, year(fechaInicio) anio FROM  bisemanas where idBisemanas='$id_bisemana'";												
$res=query($sql, $conexion);
$bisemana = fetch_array($res);


if(!$bisemana)
	exit;


require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Horizontal de Planilla");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=query($sql, $conexion);
$fila=fetch_array($res);
$logo=$fila['logo'];
$empresa = $fila['empresa'];

//buscar el encabezado
$sql2 = "SELECT cod_enca, fecha_ini, fecha_fin
		 FROM reloj_encabezado
         WHERE fecha_ini='".$bisemana["fechaInicio"]."' and fecha_fin='".$bisemana["fechaFin"]."'";
$res2=query($sql2, $conexion);
$reloj_encabezado=fetch_array($res2);

$sql3="SELECT * FROM nom_nominas_pago where periodo_ini='".$reloj_encabezado["fecha_ini"]."' and periodo_fin='".$reloj_encabezado["fecha_fin"]."'";
$res3=query($sql3, $conexion);
$nom_nominas_pago=fetch_array($res3);
$codnom=$nom_nominas_pago["codnom"];
$tipnom=$nom_nominas_pago["tipnom"];
$status=$nom_nominas_pago["status"];

$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

//buscar los proyectos
$sql="SELECT id_dispositivo, cod_dispositivo, nombre FROM reloj_info WHERE id_dispositivo IN (select distinct marcacion_disp_id from reloj_detalle where id_encabezado=".$reloj_encabezado["cod_enca"].") ORDER BY cod_dispositivo";
//$sql="SELECT id_dispositivo, cod_dispositivo, nombre FROM reloj_info";
$res=query($sql, $conexion);
$data=array();
while($row = mysqli_fetch_array($res,MYSQL_ASSOC))
  $data[]=$row;




//para cada proyecto encontrado, buscar las personas
for($i=0;$i<count($data);$i++){
	$sql="select 
			rd.ficha, 
			p.cedula,
			p.apenom,
			p.suesal,		
			p.codcargo,	
			ROUND(sum(TIME_TO_SEC(rd.ordinaria))/3600,2) as regulares,

			ROUND(sum(IF(WEEKDAY(rd.fecha)<=4,TIME_TO_SEC(rd.extra),0))/3600,2) as extra,
			ROUND(sum(IF(WEEKDAY(rd.fecha)>=5,TIME_TO_SEC(rd.extra),0))/3600,2) as extra_finsem,

			ROUND(sum(IF(WEEKDAY(rd.fecha)<=4,TIME_TO_SEC(rd.extraext),0))/3600,2) as extraext,
			ROUND(sum(IF(WEEKDAY(rd.fecha)>=5,TIME_TO_SEC(rd.extraext),0))/3600,2) as extraext_finsem,
			ROUND(sum(TIME_TO_SEC(rd.domingo))/3600,2) as domingo,

			ROUND(sum(TIME_TO_SEC(rd.extranoc))/3600,2) as extranoc,
			ROUND(sum(TIME_TO_SEC(rd.extraextnoc))/3600,2) as extraextnoc,

			ROUND(sum(IF(rd.capataz='1',TIME_TO_SEC(rd.ordinaria)+TIME_TO_SEC(rd.extra)+TIME_TO_SEC(rd.extraext)+TIME_TO_SEC(rd.extranoc)+TIME_TO_SEC(rd.extraextnoc),0))/3600,2) as horas_capataz,

			ROUND(sum(TIME_TO_SEC(rd.lluvia))/3600,2) as lluvia,
			ROUND(sum(TIME_TO_SEC(rd.altura_menor))/3600,2) as altura_menor,
			ROUND(sum(TIME_TO_SEC(rd.altura_mayor))/3600,2) as altura_mayor,
			ROUND(sum(TIME_TO_SEC(rd.otras))/3600,2) as otras,

			ROUND(sum(TIME_TO_SEC(rd.tarea))/3600,2) as tarea,
			ROUND(sum(TIME_TO_SEC(rd.paralizacion_lluvia))/3600,2) as paralizacion_lluvia,
			ROUND(sum(TIME_TO_SEC(rd.profundidad))/3600,2) as profundidad,
			ROUND(sum(TIME_TO_SEC(rd.tunel))/3600,2) as tunel,
			ROUND(sum(TIME_TO_SEC(rd.martillo))/3600,2) as martillo,
			ROUND(sum(TIME_TO_SEC(rd.rastrilleo))/3600,2) as rastrilleo
		from 
			reloj_detalle as rd,
			nompersonal as p
		where 
			p.ficha=rd.ficha and
			rd.id_encabezado='".$reloj_encabezado["cod_enca"]."' and 
			rd.marcacion_disp_id='".$data[$i]["id_dispositivo"]."' 
		group by 
			rd.ficha,
			p.cedula,
			p.apenom,
			p.suesal,
			p.codcargo";	

	$res=query($sql, $conexion);

	$data[$i]["detalle"]=[];
	$fichas_dispositivo="";
	while($row = mysqli_fetch_array($res,MYSQL_ASSOC)){
		if(strtoupper($status)=='C'){
			//buscar sueldo de la persona en el periodo correspondiente
			$sql="select suesal from nom_nomina_netos where codnom='$codnom' and tipnom='$tipnom' and ficha='".$row["ficha"]."'";
			$res2=query($sql, $conexion);
			$row2 = mysqli_fetch_array($res2,MYSQL_ASSOC);
			if(isset($row2["suesal"]))
				$row["suesal"]=$row2["suesal"];
		}
		$fichas_dispositivo.=$row["ficha"].",";
		$data[$i]["detalle"][]=$row;		
	}

	$sql="select 
			p.ficha, 
			p.cedula,
			p.apenom,
			p.suesal,		
			p.codcargo,	
			0 regulares,

			0 extra,
			0 extra_finsem,

			0 extraext,
			0 extraext_finsem,
			0 domingo,

			0 extranoc,
			0 extraextnoc,

			0 horas_capataz,

			0 lluvia,
			0 altura_menor,
			0 altura_mayor,
			0 otras,

			0 tarea,
			0 paralizacion_lluvia,
			0 profundidad,
			0 tunel,
			0 martillo,
			0 rastrilleo
		from 
			nompersonal as p,
			proyectos proy
		where 
			p.proyecto=proy.idProyecto and
			proy.idDispositivo='".$data[$i]["id_dispositivo"]."' and
			not p.ficha in (".trim($fichas_dispositivo,",").") and
			p.estado='Activo' 
		";	

	$res=query($sql, $conexion);
	while($row = mysqli_fetch_array($res,MYSQL_ASSOC)){
		if(strtoupper($status)=='C'){
			//buscar sueldo de la persona en el periodo correspondiente
			$sql="select suesal from nom_nomina_netos where codnom='$codnom' and tipnom='$tipnom' and ficha='".$row["ficha"]."'";
			$res2=query($sql, $conexion);
			$row2 = mysqli_fetch_array($res2,MYSQL_ASSOC);
			if(isset($row2["suesal"]))
				$row["suesal"]=$row2["suesal"];
		}
		$data[$i]["detalle"][]=$row;
	}

}

//print_r($data);exit;
//exit;


$desde=$reloj_encabezado["fecha_ini"];
$hasta=$reloj_encabezado["fecha_fin"];



//print_r($reloj_encabezado);print_r($data);exit;

/*
$desde=$fila2['desde'];
$hasta=$fila2['hasta'];
$dia_ini = $fila2['dia_ini'];
$dia_fin = $fila2['dia_fin']; 
$mes_numero = $fila2['mes'];
$mes_letras = $meses[$mes_numero - 1];
$anio = $fila2['anio'];*/

/*
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setWidth(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objDrawing->setHeight(36);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
*/

$estilo_totales=array(
  	'borders' => array(
    	'outline' => array(
      		'style' => PHPExcel_Style_Border::BORDER_THIN,
      		'color' => array('rgb' => '000000')
    	)
  	),
  	'fill' => array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'color' => array('rgb' => 'c0c0c0')
	),
	'font'  => array(
        'bold'  => true
    )
);


$activeSheet=$objPHPExcel->getActiveSheet();

//nombre de la empresa
$activeSheet->getStyle('A2')->getFont()->setSize(14);
$activeSheet->getStyle('A2')->getFont()->setBold(true);
$activeSheet->setCellValue('A2', strtoupper($empresa) );
$activeSheet->getRowDimension(2)->setRowHeight(25);
$activeSheet->mergeCells('A2:P2');

//titulo del reporte
$activeSheet->getStyle('A4')->getFont()->setSize(12);
$activeSheet->getStyle('A4')->getFont()->setBold(true);
$activeSheet->getRowDimension(4)->setRowHeight(20);
$activeSheet->setCellValue('A4', 'Planilla Bisemanal No. '.$bisemana["numBisemana"].': Del '. date("d/m/Y",strtotime($reloj_encabezado["fecha_ini"])) .' al '. date("d/m/Y",strtotime($reloj_encabezado["fecha_fin"])));
$activeSheet->mergeCells('A4:P4');

//titulo de columnas
$activeSheet->setCellValue("D7", "REGULARES");
$activeSheet->mergeCells('D7:E7');

$activeSheet->setCellValue("F7", "SOBRETIEMPO");
$activeSheet->mergeCells('F7:G7');

$activeSheet->setCellValue("H7", "Pagos por Enf.");
$activeSheet->mergeCells('H7:I7');

$activeSheet->setCellValue("J7", "Horas\nAus.\nPag.");
$activeSheet->mergeCells('J7:J8');

$activeSheet->setCellValue("K7", "Otros Ing.\n(**)");
$activeSheet->mergeCells('K7:K8');

$activeSheet->setCellValue("L7", "14%");
$activeSheet->mergeCells('L7:L8');

$activeSheet->setCellValue("M7", "SUB-TOTAL");
$activeSheet->mergeCells('M7:M8');

$activeSheet->setCellValue("N7", "Deducciones Legales");
$activeSheet->mergeCells('N7:P7');

$activeSheet->setCellValue("Q7", "ANTES DE\nDED ACREE");
$activeSheet->mergeCells('Q7:Q8');


$activeSheet->setCellValue("D8", "Horas");
$activeSheet->setCellValue("E8", "Monto");
$activeSheet->setCellValue("F8", "Horas");
$activeSheet->setCellValue("G8", "Monto");
$activeSheet->setCellValue("H8", "Horas");
$activeSheet->setCellValue("I8", "Monto");



$activeSheet->setCellValue("N8", "Seg Soc");
$activeSheet->setCellValue("O8", "Seg Edu");
$activeSheet->setCellValue("P8", "I.S.R.");


$activeSheet->getRowDimension(7)->setRowHeight(30);
$activeSheet->getStyle('A1:Q8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$activeSheet->getStyle('A1:Q8')->getFont()->setBold(true);

$activeSheet->getColumnDimension("D")->setWidth(12);
$activeSheet->getColumnDimension("E")->setWidth(12);
$activeSheet->getColumnDimension("F")->setWidth(12);
$activeSheet->getColumnDimension("G")->setWidth(12);
$activeSheet->getColumnDimension("H")->setWidth(12);
$activeSheet->getColumnDimension("I")->setWidth(12);
$activeSheet->getColumnDimension("J")->setWidth(12);
$activeSheet->getColumnDimension("K")->setWidth(12);
$activeSheet->getColumnDimension("L")->setWidth(12);
$activeSheet->getColumnDimension("M")->setWidth(12);
$activeSheet->getColumnDimension("N")->setWidth(12);
$activeSheet->getColumnDimension("O")->setWidth(12);
$activeSheet->getColumnDimension("P")->setWidth(12);
$activeSheet->getColumnDimension("Q")->setWidth(12);


$activeSheet->freezePane("A9");

$factor=[];
$factor["extra"]=1.25;
$factor["extra_finsem"]=1.50;

$factor["extraext"]=2.1875;
$factor["extraext_finsem"]=2.625;
$factor["domingo"]=2.0;

$factor["extranoc"]=1.5;
$factor["extraextnoc"]=2.625;
$factor["lluvia"]=0.50;
$factor["altura_menor"]=0.16;
$factor["altura_mayor"]=0.20;
$factor["otras"]=1.0;

$factor["tarea"]=1.0;
$factor["paralizacion_lluvia"]=0;
$factor["profundidad"]=0;
$factor["tunel"]=0;
$factor["martillo"]=0;
$factor["rastrilleo"]=0;

$encontro_horas_enf_persona=[];
$encontro_horas_ausen_persona=[];

$ln=10;
$linea_totales=[];
$n=1;
for($i=0;$i<count($data);$i++):

	for($j=0;$j<count($data[$i]["detalle"]);$j++):
		$n++;
		$ficha=$data[$i]["detalle"][$j]["ficha"];
		$cedula=$data[$i]["detalle"][$j]["cedula"];
		$nombre=$data[$i]["detalle"][$j]["apenom"];
		$horas_regulares=
			$data[$i]["detalle"][$j]["regulares"];
		$monto_horas_regulares=
			$data[$i]["detalle"][$j]["regulares"]*$data[$i]["detalle"][$j]["suesal"];
		$horas_sobretiempo=
			$data[$i]["detalle"][$j]["extra"]+
			$data[$i]["detalle"][$j]["extra_finsem"]+
			$data[$i]["detalle"][$j]["extraext"]+
			$data[$i]["detalle"][$j]["extraext_finsem"]+
			$data[$i]["detalle"][$j]["extranoc"]+
			$data[$i]["detalle"][$j]["extraextnoc"]+
			$data[$i]["detalle"][$j]["domingo"];
		$monto_horas_sobretiempo=
			$data[$i]["detalle"][$j]["extra"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extra"]+
			$data[$i]["detalle"][$j]["extra_finsem"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extra_finsem"]+
			$data[$i]["detalle"][$j]["extraext"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extraext"]+
			$data[$i]["detalle"][$j]["extraext_finsem"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extraext_finsem"]+
			$data[$i]["detalle"][$j]["extranoc"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extranoc"]+
			$data[$i]["detalle"][$j]["extraextnoc"]*$data[$i]["detalle"][$j]["suesal"]*$factor["extraextnoc"]+
			$data[$i]["detalle"][$j]["domingo"]*$data[$i]["detalle"][$j]["suesal"]*$factor["domingo"];
		$horas_capataz=$data[$i]["detalle"][$j]["horas_capataz"];
		$horas_enf=0;
		$monto_horas_enf=0;
		$horas_aus=0;
		$monto_horas_aus=0;	

		$otros_ing=0;
		$valor_14=0;

		//horas de lluvia
		$otros_ing+=$data[$i]["detalle"][$j]["lluvia"]*$data[$i]["detalle"][$j]["suesal"]*$factor["lluvia"];
		//horas de altura
		$otros_ing+=$data[$i]["detalle"][$j]["altura_menor"]*$data[$i]["detalle"][$j]["suesal"]*$factor["altura_menor"];
		$otros_ing+=$data[$i]["detalle"][$j]["altura_mayor"]*$data[$i]["detalle"][$j]["suesal"]*$factor["altura_mayor"];
		//otras horas
		$otros_ing+=$data[$i]["detalle"][$j]["otras"]*$data[$i]["detalle"][$j]["suesal"]*$factor["otras"];
		//otras tarea
		$otros_ing+=$data[$i]["detalle"][$j]["tarea"]*$data[$i]["detalle"][$j]["suesal"]*$factor["tarea"];
		
		//hora de enfermedad pagadas
		$sql="select sum(duracion) as duracion from expediente where cedula='$cedula' and tipo='4' and subtipo='21' and estatus='1' and fecha between '".$reloj_encabezado["fecha_ini"]."' and '".$reloj_encabezado["fecha_fin"]."'";
		//$sql="select sum(duracion) as duracion from expediente E, proyectos P where E.cedula='$cedula' and E.tipo='4' and E.subtipo='21' and E.fecha between '".$reloj_encabezado["fecha_ini"]."' and '".$reloj_encabezado["fecha_fin"]."' and E.proyecto=P.idProyecto and P.idDispositivo='".$data[$i]["id_dispositivo"]."'";//forma vieja, si se activa comentar $encontro_horas_enf_persona["$cedula"]=true;
	
		$res3=query($sql, $conexion);
		$enfermedad=fetch_array($res3);
		if(!isset($encontro_horas_enf_persona["$cedula"]) and $enfermedad["duracion"]>0){
			$encontro_horas_enf_persona["$cedula"]=true;
			$horas_enf=$enfermedad["duracion"];
			$monto_horas_enf=$horas_enf*$data[$i]["detalle"][$j]["suesal"];
		}
		//otras horas de ausencia pagadas
		$sql="select sum(duracion) as duracion from expediente where cedula='$cedula' and tipo='4' and subtipo<>'21' and estatus='1' and fecha between '".$reloj_encabezado["fecha_ini"]."' and '".$reloj_encabezado["fecha_fin"]."'";
		//$sql="select sum(duracion) as duracion from expediente E, proyectos P where E.cedula='$cedula' and E.tipo='4' and E.subtipo<>'21' and E.fecha between '".$reloj_encabezado["fecha_ini"]."' and '".$reloj_encabezado["fecha_fin"]."' and E.proyecto=P.idProyecto and P.idDispositivo='".$data[$i]["id_dispositivo"]."'";//forma vieja, si se activa comentar $encontro_horas_ausen_persona["$cedula"]=true;
	

		$res3=query($sql, $conexion);
		$ausencia=fetch_array($res3);
		if(!isset($encontro_horas_ausen_persona["$cedula"]) and $ausencia["duracion"]>0){
			$encontro_horas_ausen_persona["$cedula"]=true;
			$horas_aus=$ausencia["duracion"];
			$monto_horas_aus=$horas_aus*$data[$i]["detalle"][$j]["suesal"];
		}
		//$horas_regulares+=$horas_aus;
		//$monto_horas_regulares+=$monto_horas_aus;

		//buscar si la persona es capataz
		//$sql="select des_car from nomcargos where cod_car like '".$data[$i]["detalle"][$j]["codcargo"]."'";
		/*$sql="SELECT nap.valor FROM nomcampos_adic_personal as nap, nomcampos_adicionales as na WHERE nap.id=na.id and upper(descrip) like '%CAPATAZ%' AND upper(nap.valor) LIKE '%SI%' and nap.ficha='$ficha'";
		$res3=query($sql, $conexion);
		$capataz=fetch_array($res3);
		//14% a capataz
		if(isset($capataz["valor"])){
			$valor_14+=($monto_horas_regulares+$monto_horas_sobretiempo+$monto_horas_enf)*0.14;	
		}*/

		if($horas_capataz>0){
//			$valor_14+=($horas_capataz*$data[$i]["detalle"][$j]["suesal"]+$monto_horas_enf)*0.14;
        $valor_14+=($monto_horas_regulares+$monto_horas_sobretiempo+$monto_horas_enf)*0.14;
		}


		$subtotal=round($monto_horas_regulares,2)+round($monto_horas_sobretiempo,2)+round($monto_horas_enf,2)+round($otros_ing,2)+round($valor_14,2)+round($monto_horas_aus,2);
		//$seg_soc=$subtotal*0.1025;
		$seg_soc=$subtotal*0.0975;
		$seg_edu=$subtotal*0.0125;
		$isl=0;

		

		$neto=$subtotal-$seg_soc-$seg_edu-$isl;

		$data[$i]["detalle"][$j]["horas_regulares"]=$horas_regulares;
		$data[$i]["detalle"][$j]["monto_horas_regulares"]=$monto_horas_regulares;
		$data[$i]["detalle"][$j]["horas_sobretiempo"]=$horas_sobretiempo;
		$data[$i]["detalle"][$j]["monto_horas_sobretiempo"]=$monto_horas_sobretiempo;
		$data[$i]["detalle"][$j]["horas_enf"]=$horas_enf;
		$data[$i]["detalle"][$j]["monto_horas_enf"]=$monto_horas_enf;
		$data[$i]["detalle"][$j]["monto_horas_aus"]=$monto_horas_aus;
		$data[$i]["detalle"][$j]["otros_ing"]=$otros_ing;
		$data[$i]["detalle"][$j]["valor_14"]=$valor_14;
		$data[$i]["detalle"][$j]["subtotal"]=$subtotal;
		$data[$i]["detalle"][$j]["seg_soc"]=$seg_soc;
		$data[$i]["detalle"][$j]["seg_edu"]=$seg_edu;
		$data[$i]["detalle"][$j]["isr"]=$isr;
		$data[$i]["detalle"][$j]["neto"]=$neto;

	endfor;

endfor;



for($i=0;$i<count($data);$i++):


	$activeSheet->setCellValue("A$ln", strtoupper("PROYECTO: ".trim($data[$i]["cod_dispositivo"])));
	$activeSheet->setCellValue("C$ln", strtoupper("- ".trim($data[$i]["nombre"])));
	$activeSheet->getStyle("A$ln:C$ln")->getFont()->setBold(true);
	$activeSheet->getStyle("A$ln:C$ln")->getFont()->setItalic(true);
	$activeSheet->getStyle("A$ln:C$ln")->getFont()->setSize(12);
	$activeSheet->getRowDimension($ln)->setRowHeight(15);
	$ln+=2;

	for($j=0;$j<count($data[$i]["detalle"]);$j++):

		$ficha=$data[$i]["detalle"][$j]["ficha"];
		$cedula=$data[$i]["detalle"][$j]["cedula"];
		$nombre=$data[$i]["detalle"][$j]["apenom"];
		$horas_regulares=$data[$i]["detalle"][$j]["horas_regulares"];
		$monto_horas_regulares=$data[$i]["detalle"][$j]["monto_horas_regulares"];
		$horas_sobretiempo=$data[$i]["detalle"][$j]["horas_sobretiempo"];
		$monto_horas_sobretiempo=$data[$i]["detalle"][$j]["monto_horas_sobretiempo"];
		$horas_enf=$data[$i]["detalle"][$j]["horas_enf"];
		$monto_horas_enf=$data[$i]["detalle"][$j]["monto_horas_enf"];
		$monto_horas_aus=$data[$i]["detalle"][$j]["monto_horas_aus"];
		$otros_ing=$data[$i]["detalle"][$j]["otros_ing"];
		$valor_14=$data[$i]["detalle"][$j]["valor_14"];
		$subtotal=$data[$i]["detalle"][$j]["subtotal"];
		$seg_soc=$data[$i]["detalle"][$j]["seg_soc"];
		$seg_edu=$data[$i]["detalle"][$j]["seg_edu"];
		$isr=$data[$i]["detalle"][$j]["isr"];
		$neto=$data[$i]["detalle"][$j]["neto"];

		//buscar el ISRL
		$sql="select monto from nom_movimientos_nomina where codnom='$codnom' and ficha='$ficha' and (codcon='202' or codcon='215')";
		//print $sql;exit;
		$res4=query($sql, $conexion);
		$monto_isl=fetch_array($res4);
		if(isset($monto_isl["monto"])) {
			//buscar el total total de la ficha
			$total_subtotal_ficha=0;
			for($ii=0;$ii<count($data);$ii++):
				for($jj=0;$jj<count($data[$ii]["detalle"]);$jj++):
					if($ficha==$data[$ii]["detalle"][$jj]["ficha"])
						$total_subtotal_ficha+=$data[$ii]["detalle"][$jj]["subtotal"];
				endfor;
			endfor;
			//aplicar la regla de 3
			//si $monto_isl["monto"]   ->   $total_subtotal_ficha
			//  X                      ->   $subtotal
			$isr=0;
			if($total_subtotal_ficha>0)
				$isr=round(($monto_isl["monto"]*$subtotal)/$total_subtotal_ficha,2);
		}


		$activeSheet->setCellValueExplicit("A$ln", "Empleado:", PHPExcel_Cell_DataType::TYPE_STRING);
		//Codigo del trabajador
		$activeSheet->setCellValueExplicit("B$ln", trim($ficha), PHPExcel_Cell_DataType::TYPE_STRING);
		//Nombre del trabajador
		$activeSheet->setCellValueExplicit("C$ln", mb_convert_case(trim($nombre),MB_CASE_UPPER,'UTF-8'), PHPExcel_Cell_DataType::TYPE_STRING);
		$activeSheet->setCellValueExplicit("D$ln", number_format($horas_regulares,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("E$ln", number_format($monto_horas_regulares,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("F$ln", number_format($horas_sobretiempo,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("G$ln", number_format($monto_horas_sobretiempo,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("H$ln", number_format($horas_enf,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("I$ln", number_format($monto_horas_enf,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("J$ln", number_format($monto_horas_aus,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("K$ln", number_format($otros_ing,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);

		$activeSheet->setCellValueExplicit("L$ln", number_format($valor_14,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("M$ln", number_format($subtotal,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("N$ln", number_format($seg_soc,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("O$ln", number_format($seg_edu,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("P$ln", number_format($isr,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);
		$activeSheet->setCellValueExplicit("Q$ln", number_format($neto,2,".",""), PHPExcel_Cell_DataType::TYPE_NUMERIC);



		$activeSheet->getStyle("D$ln:Q$ln")->getNumberFormat()->setFormatCode('#,##0.00');

		$ln++;
	endfor;

	$activeSheet->getStyle("D$ln:P$ln")->getNumberFormat()->setFormatCode('#,##0.00');
	$activeSheet->setCellValue("D$ln","=SUM(D".($ln-$j).":D".($ln-1).")");
	$activeSheet->setCellValue("E$ln","=SUM(E".($ln-$j).":E".($ln-1).")");
	$activeSheet->setCellValue("F$ln","=SUM(F".($ln-$j).":F".($ln-1).")");
	$activeSheet->setCellValue("G$ln","=SUM(G".($ln-$j).":G".($ln-1).")");
	$activeSheet->setCellValue("H$ln","=SUM(H".($ln-$j).":H".($ln-1).")");
	$activeSheet->setCellValue("I$ln","=SUM(I".($ln-$j).":I".($ln-1).")");
	$activeSheet->setCellValue("J$ln","=SUM(J".($ln-$j).":J".($ln-1).")");
	$activeSheet->setCellValue("K$ln","=SUM(K".($ln-$j).":K".($ln-1).")");
	$activeSheet->setCellValue("L$ln","=SUM(L".($ln-$j).":L".($ln-1).")");
	$activeSheet->setCellValue("M$ln","=SUM(M".($ln-$j).":M".($ln-1).")");
	$activeSheet->setCellValue("N$ln","=SUM(N".($ln-$j).":N".($ln-1).")");
	$activeSheet->setCellValue("O$ln","=SUM(O".($ln-$j).":O".($ln-1).")");
	$activeSheet->setCellValue("P$ln","=SUM(P".($ln-$j).":P".($ln-1).")");
	$activeSheet->setCellValue("Q$ln","=SUM(Q".($ln-$j).":Q".($ln-1).")");

	
	$activeSheet->getStyle("A$ln:Q$ln")->applyFromArray($estilo_totales);
	$linea_totales[]=$ln;


	$ln+=2;
endfor;




$activeSheet->getColumnDimension("C")->setWidth(30);

$activeSheet->setCellValueExplicit("A$ln", "TOTALES GENERALES:\nCant. de Empleados: $n", PHPExcel_Cell_DataType::TYPE_STRING);
$activeSheet->getRowDimension($ln)->setRowHeight(30);

$activeSheet->getStyle("D$ln:Q$ln")->getNumberFormat()->setFormatCode('#,##0.00');
$activeSheet->getStyle("A$ln:Q$ln")->applyFromArray($estilo_totales);
foreach(array("D","E","F","G","H","I","J","K","L","M","N","O","P","Q") as $key => $value) {
  	$tmp="";
	for($t=0;$t<count($linea_totales);$t++)
		$tmp.="+".$value.$linea_totales[$t];	
	$activeSheet->setCellValue("$value$ln","=".trim($tmp,"+"));
}


$ln+=2;
//$activeSheet->setCellValueExplicit("A$ln", "Factores usados para el calculo de Cuota Patronal de la CSS: | SS Pat. de Salario: 12.25 | SS Pat. de XIII mes: 10.75 | SE Patronal: 1.50 | RP Patronal: 3.01", PHPExcel_Cell_DataType::TYPE_STRING);

$ln++;
//$activeSheet->setCellValueExplicit("A$ln", "NOTA:  * Recuerde que si usted registra en PayDay Salarios en Especie, esos montos tambien estan sujetos a los descuentos legales y por ende a la Cuota Patronal.\n** En la columna de Otros Ingresos se suman los siguientes conceptos: Otros Ingresos,Comisiones, Primas de Produccion, Bonificaciones y Gratificaciones", PHPExcel_Cell_DataType::TYPE_STRING);


$filename = "Horizontal_Planilla_Bisemana_".$bisemana["numBisemana"]."_del_".fecha($desde).'_hasta_'.fecha($hasta);

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

