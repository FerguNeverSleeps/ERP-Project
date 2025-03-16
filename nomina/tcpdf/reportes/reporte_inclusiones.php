<?php
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

//require_once('../../config/lang/eng.php');
require_once('../tcpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';
//require_once('../../../Clases/Cls_conexion.php');
//$informes = new Cls_conexion();
//$informes->conectar_bd();


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $fechainicio, $fechafinal, $fecharegistro, $id;
	//Page header
	public function Header() {
		// Logo
		$Conn=conexion();
		$var_sql="select * from nomempresa";
		$rs = query($var_sql,$Conn);
		$row_rs = fetch_array($rs);
		$var_encabezado1=$row_rs['nom_emp'];
		$var_izquierda='../../imagenes/'.$row_rs[imagen_der];
		$var_derecha='../../imagenes/'.$row_rs[imagen_izq];

		$image_file = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 12);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$fill = 1;
		$border="LTRB";
		$ln=0;
		$fill = 0;
		$align='C';
		$link=0;
		$stretch=1;
		$ignore_min_height=0;
		$calign='T';
		$valign='T';
		$height=5.75;//alto de cada columna
		$YMax=260;
		$altura=21;
		$membrete="<b>REPÚBLICA DE PANAMÁ<br>
					CONTRALORÍA GENERAL DE LA REPÚBLICA<br>
					DIRECCIÓN GENERAL DE FISCALIZACIÓN<br>
					INCLUSIONES</b>";
        $this->Image($var_izquierda,11,11,19,19); 

		$this->MultiCell(40, $altura,"", $border="LTB", $align='C', $fill=false, $ln=1, $x=10,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(120, $altura,$membrete, $border="TB", $align='C', $fill=false, $ln=1,$x=50,$y=10,  $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);
		$this->MultiCell(40, $altura,"", $border="TRB", $align='C', $fill=false, $ln=1, $x=170,$y=10, $reseth=true, $stretch=0, $ishtml=true, $autopadding=true, $maxh=0, $valign='T', $fitcell=false);

		$this->Ln();
		
		
	}
	 function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
	  //$buena= $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha

		$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
	  }
	}
	public function CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro)
	{
		$conexion=conexion();
	    if($this->getAliasNumPage() <= 1)
		{
			$Y=44;
			$this->SetTopMargin ($Y);
			$this->SetTopMargin ($Y);

		}
		else
		{
			$Y=40;
			$this->SetTopMargin($Y);
			$this->SetTopMargin ($Y);

		}

		$fill = 1;
		$border=1;
		$ln=0;
		$fill = 0;
		$align='C';
		$link=0;
		$stretch=1;
		$ignore_min_height=0;
		$calign='T';
		$valign='M';
		$height=5.75;//alto de cada columna
		$YMax=260;
		$altura=7;

		$sql1 = "   SELECT DISTINCT *
	                FROM  parametro_inclusion
	                WHERE  id_parametro_inclusion=1";
		$sql2 = "   SELECT DISTINCT *, DAY(fecha_decreto) as dia_decreto,MONTH(fecha_decreto) as mes_decreto,YEAR(fecha_decreto) as ano_decreto,
		 			DAY(fecing) as dia_ingreso,MONTH(fecing) as mes_ingreso,YEAR(fecing) as ano_ingreso
	                FROM  inclusion 
	                WHERE  personal_id=".$this->id;
		/*$sql3 = "   SELECT nomposicion_id, descripcion_posicion 
					FROM nomposicion";
		$sql4 = "   SELECT codorg, descrip 
					FROM nomnivel1 
					ORDER BY descrip";	

		$sql5 = "   SELECT cod_cargo, des_car 
					FROM nomcargos 
					ORDER BY des_car";	*/
		$res1=query($sql1, $conexion);
		$fila1=fetch_array($res1);
		$res2=query($sql2, $conexion);
		$fila2=fetch_array($res2);
		/*$res3=query($sql3, $conexion);
		$fila3=fetch_array($res3);
		$res4=query($sql4, $conexion);
		$fila4=fetch_array($res4);
		$res5=query($sql5, $conexion);
		$fila5=fetch_array($res5);	*/	
		$this->SetFont('helveticaB', '',8);
		//Ministerio
		$this->Cell(25, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "AREA", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "ENTIDAD", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(130, $altura, "NOMBRE DE LA ENTIDAD", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->SetFont('helvetica', '',8);

		$this->Cell(25, $altura,"MINISTERIO:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila1['area'], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila1['ministerio'], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(130, $altura, $fila1['nombre_entidad'], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		$this->SetFont('helveticaB', '',8);
		$this->Cell(20, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "1RA", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "2DA", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "MES", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "AÑO", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);		
		$this->Cell(55, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, "DIA", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, "MES", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, "AÑO", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);	
		$this->Ln();
				if($fila2[quincena]==1)
		{
			$quincena1=="XX";
			$quincena2=="";
		}
		else
		{
			$quincena1=="";
			$quincena2=="XX";
		}
		$this->SetFont('helvetica', '',8);
		$this->Cell(20, $altura,"QUINCENA:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $quincena1, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $quincena2, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila2[mes], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila2[ano], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);		
		
		$this->Cell(5, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, "Decreto:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila2[num_decreto], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, $fila2[dia_decreto], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, $fila2[mes_decreto], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(15, $altura, $fila2[ano_decreto], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);	
		$this->Ln();
		$this->Ln();
		$this->Cell(40, $altura,"Numero de Posición:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, $fila2[nomposicion_id],  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, "Cédula", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura,$fila2[cedula], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, "Seguro Social", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, $fila2[seguro_social], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		//Numero Posicion
		$this->SetFont('helveticaB', '',8);

		$this->Cell(65, $altura, "", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "F", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "M", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		if ($fila2[sexo]=='M') {
			$sexo1='';
			$sexo2='XX';
		}
		elseif($fila2[sexo]=='F')
		{
			$sexo1='XX';
			$sexo2='';
		}
		elseif($fila2[sexo]!='M' AND $fila2[sexo]!='F')
		{
			$sexo1='';
			$sexo2='';
		}
		$this->SetFont('helvetica', '',8);
		$this->Cell(20, $altura,"Clave I/R", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura, $fila2[clave_ir],  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura, "", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(20, $altura,"Sexo", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, $sexo1, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, $sexo2, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		//Clave IR
		$this->Cell(30, $altura,"Nombre", $border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, $fila2[nombres],$border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->SetFont('helveticaB', '',8);
		$this->Cell(40, $altura, $fila2[apellido_paterno], $border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, $fila2[apellido_materno], $border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(50, $altura, $fila2[apellido_casada], $border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->SetFont('helvetica', '',8);
		$this->Cell(30, $altura,"", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, "Nombres",$border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, "A. Paterno", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, "A. Materno", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(50, $altura, "A. De Casada", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();		
		$this->Ln();
		$this->Cell(30, $altura,"Fecha De", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "Dia",$border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "Mes", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "Año", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(40, $altura, "Titular", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		if($fila2[titular_interino]==1)
		{
			$titular="X";
			$interino="";
		}else{
			$titular="X";
			$interino="";
		}

		$this->Cell(35, $altura, $titular, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "Condición del", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"Inicio de Lab:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->SetFont('helveticaB', '',8);
		$this->Cell(10, $altura, $fila2[dia_ingreso],$border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, $fila2[mes_ingreso], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, $fila2[ano_ingreso], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->SetFont('helvetica', '',8);
		$this->Cell(40, $altura, "Interino", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $interino, $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura, "", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[tipemp], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();					
		$this->Ln();		
		
		$this->Cell(180, $altura,"", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "Qnas", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura, "Dias", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$dias_pagar= round($fila2[dias_pagar]);
		$this->Cell(30, $altura,"Días a Pagar", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura , $dias_pagar,  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(105, $altura, "", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(30, $altura,$fila2[suesal], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(5, $altura , "",  $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

		$this->Cell(10, $altura, $fila2[quincenas_pagar], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

		$this->Cell(10, $altura, $fila2[dias], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		//Dias a Pagar
		$altura=6;
		$align='L';
		$this->Cell(30, $altura,"Sueldo", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"001",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c001], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura,"", $border="T",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura , "Qnas",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura,"002",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c002], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Diferencia de Salario B/.", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura,$fila2[diferencia], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura,$fila2[diferencia_quincena],  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"003",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c003], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Número de Planilla", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[tipnom], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"Sobresueldo", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"011",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c011], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Centro de Pago Región/Prov.", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[descrip_centro_pago], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();

		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"012",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c012], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Nro. Provincia", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[codnivel1], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"013",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c013], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Cargo Según planilla:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[des_car], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"019",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c019], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Grado o Etapa del Cargo:", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[grado], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"080",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c080], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Código de Cargo", $border="L",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(45, $altura,$fila2[codcargo], $border="LTRB",$ln,"C",$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(30, $altura,"Gastos", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(10, $altura ,"030",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(35, $altura, $fila2[c030], $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(125, $altura,"", $border="BR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		$this->Cell(30, $altura,"Observación", $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(1, $altura , "",  $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(165, $altura,$fila2[observacion], $border="B",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Ln();
		$this->Ln();
		$align="C";
		$this->SetFont('helveticaB', '',8);

		$this->Cell(200, $altura,"AUTORIZACIONES/APROBACIONES", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"Firmas", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Registro de Presupuesto",  $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"Fiscalización de Planillas", $border="LTRB",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"Analista de Planillas", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"SELLO",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"SELLO", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"Jefe de Planillas", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"______________________", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Ln();
		$this->Cell(60, $altura,"General de Fiscalización", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="LR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

		$this->Ln();
		$this->Cell(60, $altura,"", $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"",  $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$this->Cell(70, $altura,"", $border="BLR",$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	
	}

}//fin clase
		




// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio=$_REQUEST['fecha_ini'];
$pdf->fechafinal=$_REQUEST['fecha_fin'];
$pdf->fecharegistro=$_REQUEST['fecha_reg'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Selectra');
$pdf->SetTitle('INCLUSIONES');
$pdf->SetSubject('Reporte de Inclusiones');
$pdf->SetKeywords('TCPDF, PDF, reporte, Inclusiones');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->SetFooterData(false);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10,true);
$pdf->SetLeftMargin(10);
$pdf->SetHeaderMargin(10);
$pdf->SetRightMargin(10);
$pdf->setPrintFooter(false);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15	);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', 'BI', 10);

// add a page
$pdf->AddPage('P', 'legal');

$reg=$_REQUEST['reg'];
$pdf->id=$_REQUEST['id'];

//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('reporte_inclusiones.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();