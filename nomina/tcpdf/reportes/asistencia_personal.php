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
	public $fechainicio, $fechafinal, $fecharegistro;
	//Page header
	public function Header() {
		// Logo
		$Conn=conexion();
		$var_sql="select * from nomempresa";
		$rs = query($var_sql,$Conn);
		$row_rs = fetch_array($rs);
		$var_encabezado1=$row_rs['nom_emp'];
		$var_izquierda='../../imagenes/'.$row_rs[imagen_izq];
		$var_derecha='../../imagenes/'.$row_rs[imagen_der];

		$image_file = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('helvetica', 8);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$html=
		'<table width="1024" border="0" cellpadding="1" cellspacing="0">
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					<img  width="100" height="40" src="'.$var_izquierda.'" />
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:18px">
					INFORME DE ASISTENCIA DE PERSONAL
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					<img width="150" height="40" src="'.$var_derecha.'" />
				</td>
			</tr>
  		
		<tr>
			<td width="190" style="border-left-color:#FFF;border-bottom-color:#FFF;font-size:12x" align="center">
				<div>FECHA EMISION<br />
				<b style="font-size:12px;">'.$fecha.'</b></div>
			</td>
			<td width="190" style="border-left-color:#FFF;border-bottom-color:#FFF;font-size:12x" align="center">
				<div>FECHA INICIO<br />
				<b style="font-size:12px;">'.$this->CambiarFecha($this->fechainicio).'</b></div>
			</td>
			<td width="190" style="border-left-color:#FFF;border-bottom-color:#FFF;font-size:12x" align="center">
				<div>FECHA FIN<br />
				<b style="font-size:12px;">'.$this->CambiarFecha($this->fechafinal).'</b></div>
			</td>
			<td width="190" style="border-left-color:#FFF;border-bottom-color:#FFF;font-size:12x" align="center">
				<div>FECHA REGISTRO<br />
				<b style="font-size:12px;">'.$this->CambiarFecha($this->fecharegistro).'</b></div>
			</td>
			<td width="200" style="border-right-color:#FFF;border-left-color:#FFF;border-bottom-color:#FFF;font-size:12px">
				<div align="right">HOJA  N/ TOTAL DE HOJAS<br />
					<b style="font-size:12px">'.$this->getAliasNumPage().' de '.$this-> getAliasNbPages().'</b>
				</div>
			</td>
		</tr>
		
		</table>
		<BR />
';
$this->writeHTML($html);
		// Set font
		// Page number
		
	}
public function ColoredTable($reg) 
{
		
	$conexion=conexion();
    if($this->getAliasNumPage() <= 1)
	{
		$this->SetY(45);
	}
    
	// Data
	$fill = 1;
	$border='B';
	$ln=0;
	$fill = 0;
	$align='C';
	$link=0;
	$stretch=1;
	$ignore_min_height=0;
	$calign='T';
	$valign='T';
	$height=5.75;//alto de cada columna
	$Y=47;
	$this->SetY($Y);
	$YMax=260;
    $sql1 = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento
         FROM   nompersonal np
         INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1
         ORDER BY np.codnivel1";

    $res1=query($sql1, $conexion);
    
	    while($row=fetch_array($res1))
	    {		   
            $codnivel1=$row['codnivel1'];
            $departamento=$row['departamento'];


			// Color and font restoration
			$this->SetFont('helvetica', '', 10);

			$this->SetFillColor(224, 235, 255);
			$this->SetTextColor(0);
			$this->SetFont('');
		
			
			
			$sql = "SELECT DISTINCT np.ficha, np.apenom as nombre, nc.des_car as cargo
                    FROM   reloj_detalle rd, nompersonal np, nomcargos nc
                    WHERE   rd.ficha=np.ficha AND nc.cod_car=np.codcargo AND rd.id_encabezado=".$reg." 
                    AND np.codnivel1=".$codnivel1." ORDER BY np.apenom";
            $res=query($sql, $conexion);
            
            while($fila=fetch_array($res))
            {
            	$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

				$this->Cell(50, 7, $row['departamento'], 1, 0, 'C', 1);
				$this->Ln();

				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				// Header
				$w = array(20, 13, 13,13,13,13,13,13,13,13,13,13,13,13,13,17,17,13);
				$num_headers = count($header);
				
				for($i = 0; $i < $num_headers; ++$i) {
					$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
				}
				$this->Ln();
                $ficha=$fila['ficha'];
                $cargo=$fila['cargo'];
                $consulta="SELECT
                 reloj_encabezado.cod_enca,
                 reloj_encabezado.fecha_reg,
                 reloj_encabezado.fecha_ini,
                 reloj_encabezado.fecha_fin,
                 reloj_detalle.id,
                 reloj_detalle.id_encabezado,
                 reloj_detalle.ficha,
                 reloj_detalle.fecha,
                 nomturnos.turno_id,
                 nomturnos.descripcion,
                 nomturnos.entrada,
                 nomturnos.tolerancia_entrada,
                 nomturnos.inicio_descanso,
                 nomturnos.salida_descanso,
                 nomturnos.tolerancia_descanso,
                 nomturnos.salida,
                 nomturnos.tolerancia_salida,
                 nompersonal.apenom,
                 reloj_detalle.entrada,
                 reloj_detalle.salmuerzo,
                 reloj_detalle.ealmuerzo,
                 reloj_detalle.salida,
                 reloj_detalle.ordinaria,
                 reloj_detalle.dialibre,
                 reloj_detalle.ent_emer,
                 reloj_detalle.sal_emer,
                 reloj_detalle.salida_diasiguiente,
                 reloj_detalle.extra,
                 reloj_detalle.extraext,
                 reloj_detalle.extranoc,
                 reloj_detalle.extraextnoc,
                 reloj_detalle.domingo,
                 reloj_detalle.nacional,
                 reloj_detalle.emergencia,
                 reloj_detalle.descansoincompleto,
                 reloj_detalle.tardanza
            	FROM
                 nomturnos INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                 INNER JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                 INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
	            WHERE
	                 reloj_encabezado.cod_enca = '$reg' AND reloj_detalle.ficha ='$ficha'
	            ORDER BY
	                 ficha ASC,
	                 fecha ASC";
	            $query=query($consulta,$conexion);

	            $fichaaux="";
	            $fechaaux="";
	            $apenomaux="";
	            $i=0;
	            $j=0;	

	            while($fila=fetch_array($query))
	            {
	                $date = new DateTime($fila[fecha]);
	                $fecha = $date->format('d-m-Y');
	                $hora = $date->format('H:i');
	                $ficha = $fila[ficha];

	                if($i==0)
	                {	
                        $this->Cell(110, $altura, $fila[ficha]." - ".$fila[apenom]." - ".$cargo, 0,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                  $this->Ln();
						$fichaaux=$fila[ficha];
                        $fechaaux=$fecha;
                        $apenomaux=$fila[apenom];
	                }
	                if($ficha!=$fichaaux)
	                {
	                    //TOTALES
	                	$this->Cell(110, $altura, $fila[ficha]." ".$fila[apenom], $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                	$this->Ln();
	                    $fichaaux=$ficha;

	                    $apenomaux=$fila[apenom];

	                }
	                if($fila[entrada] == '00:00'){
                		$fila[entrada] = '';
                	}
                	if($fila[salmuerzo] == '00:00'){
                		$fila[salmuerzo] = '';
                	}
                    if($fila[ealmuerzo] == '00:00'){
                		$fila[ealmuerzo] = '';
                	}
                	if($fila[salida] == '00:00'){
                		$fila[salida] = '';
                	}
                	if($fila[domingo] == '00:00'){
                		$fila[domingo] = '';
                	}
                	if($fila[nacional] == '00:00'){
                		$fila[nacional] = '';
                	}
                    if($fila[ealmuerzo] == '00:00'){
                		$fila[ealmuerzo] = '';
                	}
                	if($fila[tardanza] == '00:00'){
                		$fila[tardanza] = '';
                	}
                	if($fila[emergencia] == '00:00'){
                		$fila[emergencia] = '';
                	}
                	if($fila[descansoincompleto] == '00:00'){
                		$fila[descansoincompleto] = '';
                	}
                	if($fila[salida_diasiguiente] == '00:00'){
                		$fila[salida_diasiguiente] = '';
                	}
                	if($fila[extra] == '00:00'){
                		$fila[extra] = '';
                	}
                	if($fila[extraext] == '00:00'){
                		$fila[extraext] = '';
                	}
                	if($fila[extranoc] == '00:00'){
                		$fila[extranoc] = '';
                	}
                	if($fila[extraextnoc] == '00:00'){
                		$fila[extraextnoc] = '';
                	}
                	if($fila[ordinaria] == '00:00'){
                		$fila[ordinaria] = '';
                	}
	                //$this->Row(array($fecha,$turno,$fila[entrada],$fila[salmuerzo],$fila[ealmuerzo],$fila[salida],$fila[ordinaria],$fila[domingo],$fila[nacional],$fila[tardanza],$fila[emergencia],$fila[descansoincompleto]));
	                /*13echo $fecha." ".$turno." ".$fila[entrada]." ".
	                $fila[salmuerzo]." ".$fila[ealmuerzo]." ".$fila[salida]." ".$fila[ordinaria]." ".$fila[domingo]." ".
	                $fila[nacional]." ".$fila[tardanza]." ".$fila[emergencia]." ".$fila[descansoincompleto]."<br>";*/
	                
	            }//Fin While
	        }//Fin While
	    }//Fin While
    }//fin function
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
	$border=0;
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
	$altura=7;
	$sql1 = "   SELECT DISTINCT np.codnivel1, nn.descrip as departamento
                FROM   reloj_detalle rd, nompersonal np, nomcargos nc,nomnivel1 nn
                WHERE   rd.ficha=np.ficha AND nc.cod_car=np.codcargo AND nn.codorg=np.codnivel1 AND rd.id_encabezado=".$reg." 
                Group BY nn.descrip";

	$res1=query($sql1, $conexion);
    
		while($row=fetch_array($res1))
		{		   
			$codnivel1=$row['codnivel1'];
		$departamento=$row['departamento'];


			// Color and font restoration
			$this->SetFont('helveticab', '', 8);

			$this->SetFillColor(224, 235, 255);
			$this->SetTextColor(0);
		
			
			
			$sql = "SELECT DISTINCT np.ficha, np.apenom as nombre, nc.des_car as cargo
					FROM   reloj_detalle rd, nompersonal np, nomcargos nc
					WHERE   rd.ficha=np.ficha AND nc.cod_car=np.codcargo AND rd.id_encabezado=".$reg." 
					AND np.codnivel1=".$codnivel1." ORDER BY np.ficha";
            $res=query($sql, $conexion);
            $this->Cell(50, 7, $row['departamento'], 1, 0, 'C', 1);
			$this->Ln();
            while($fila=fetch_array($res))
            {
				//$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

				

				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				// Header
				$w = array(15, 20);
				$num_headers = count($header);
				
				//for($i = 0; $i < $num_headers; ++$i) {
				//	$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
				//}
				//$this->Ln();
				$ficha=$fila['ficha'];
				$cargo=$fila['cargo'];
				$consulta="SELECT
				 reloj_encabezado.cod_enca,
				 reloj_encabezado.fecha_reg,
                 reloj_encabezado.fecha_ini,
                 reloj_encabezado.fecha_fin,
                 reloj_detalle.id,
                 reloj_detalle.id_encabezado,
                 reloj_detalle.ficha,
                 reloj_detalle.fecha,
                 nomturnos.turno_id,
                 nomturnos.descripcion,
                 nomturnos.entrada,
                 nomturnos.tolerancia_entrada,
                 nomturnos.inicio_descanso,
                 nomturnos.salida_descanso,
                 nomturnos.tolerancia_descanso,
                 nomturnos.salida,
                 nomturnos.tolerancia_salida,
                 nompersonal.apenom,
                 reloj_detalle.entrada,
                 reloj_detalle.salmuerzo,
                 reloj_detalle.ealmuerzo,
                 reloj_detalle.salida,
                 reloj_detalle.ordinaria,
                 reloj_detalle.dialibre,
                 reloj_detalle.ent_emer,
                 reloj_detalle.sal_emer,
                 reloj_detalle.salida_diasiguiente,
                 reloj_detalle.extra,
                 reloj_detalle.extraext,
                 reloj_detalle.extranoc,
                 reloj_detalle.extraextnoc,
                 reloj_detalle.mixtodiurna,
                 reloj_detalle.mixtonoc,
                 reloj_detalle.mixtoextdiurna,
                 reloj_detalle.mixtoextnoc,
                 reloj_detalle.observacion,
                 reloj_detalle.domingo,
                 reloj_detalle.descextra1,
                 reloj_detalle.nacional,
                 reloj_detalle.emergencia,
                 reloj_detalle.descansoincompleto,
                 reloj_detalle.tardanza
            	FROM
                 nomturnos INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                 INNER JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                 INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
	            WHERE
	                 reloj_encabezado.cod_enca = '$reg' AND reloj_detalle.ficha ='$ficha'
	            ORDER BY
	                 ficha ASC,
	                 fecha ASC";
	            $query=query($consulta,$conexion);
	            $fichaaux="";
	            $fechaaux="";
	            $apenomaux="";
	            $i=0;
	            $j=0;
            	
	            while($fila=fetch_array($query))
	            {
	                $pos=0;
 					$turno = $fila[descripcion];
	                $date = new DateTime($fila[fecha]);
	                $fecha = $date->format('d-m-Y');
	                $hora = $date->format('H:i');
	                $ficha = $fila[ficha];
	                $cabecera = array('Fecha', 'Turno');
	            	$cuerpo=array($fecha,$turno);
	                //entrada
	            	if($fila[entrada] != '00:00' AND $fila[entrada] != ""){
	            		array_push($cabecera, 'Entr');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[entrada]);
	            	}
	            	//Salida Almuerzo
	            	if($fila[salmuerzo] != '00:00' AND $fila[salmuerzo] != ""){
	            		array_push($cabecera, 'SA');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[salmuerzo]);
	            	}
	            	//Entrada Almuerzo
	            	if($fila[ealmuerzo] != '00:00' AND $fila[ealmuerzo] != ""){
	                    array_push($cabecera, 'EA');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[ealmuerzo]);
	            	}
	            	//Salida
	            	if($fila[salida] != '00:00' AND $fila[salida] != ""){
	            		array_push($cabecera, 'Sal');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[salida]);
	            	}
	            	//Salida al dia siguiente
	            	if($fila[salida_diasiguiente] != '00:00' AND $fila[salida_diasiguiente] != ""){
	            	
	            		array_push($cabecera, 'Sal DS');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[salida_diasiguiente]);
					}
	            	//Descanso
	            	if($fila[dialibre] != '00:00' AND $fila[dialibre] != ""){
	            		array_push($cabecera, 'Desc.');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[dialibre]);
					
	               	}
	            	//Ordinarias
	            	if($fila[ordinaria] != '00:00' AND $fila[ordinaria] != ""){
	            		array_push($cabecera, 'Ord.');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[ordinaria]);
					
	               	}
	            	//domingos
	            	if($fila[domingo] != '00:00' AND $fila[domingo] != ""){
	            		array_push($cabecera, 'Dom');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[domingo]);

	            	}
	            	//Feriados nacionales
	            	if($fila[nacional] != '00:00' AND $fila[nacional] != ""){
	            		array_push($cabecera, 'nac');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[nacional]);
	            	}	            	
	            	//Tardanza
	            	if($fila[tardanza] != '00:00' AND $fila[tardanza] != ""){
	            		array_push($cabecera, 'Tard.');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[tardanza]);
	            	}
	            	//Emergencia
	            	if($fila[emergencia] != '00:00' AND $fila[emergencia] != ""){
	            		array_push($cabecera, 'Emerg.');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[emergencia]);
	            	}
	            	//Descanso incompleto
	            	if($fila[descansoincompleto] != '00:00' AND $fila[descansoincompleto] != ""){
	            		array_push($cabecera, 'DI');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[descansoincompleto]);
	            	}
	            	//Horas Extras
	            	if($fila[extra] != '00:00' AND $fila[extra] != ""){
	            		array_push($cabecera, 'Extra.');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[extra]);
	            	}
	            	//Horas Extras Diurnas Con recargo
	            	if($fila[extraext] != '00:00' AND $fila[extraext] != ""){
	            		array_push($cabecera, 'ExtraExt');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[extraext]);
	            	}
	            	//Horas Extras Nocturnas
	            	if($fila[extranoc] != '00:00' AND $fila[extranoc] != ""){
	            		array_push($cabecera, 'ExtraNoc');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[extranoc]);
	            	}
	            	//Horas extras nocturnas con recargo
	            	if($fila[extraextnoc] != '00:00' AND $fila[extraextnoc] != ""){
	            		array_push($cabecera, 'NocExt');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[extraextnoc]);
	            	}
	            	///Horas Extras Mixto diurna
	            	if($fila[mixtodiurna] != '00:00' AND $fila[mixtodiurna] != ""){
	            		array_push($cabecera, 'ExtMix');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[mixtodiurna]);
	            	}
	            	//Hora Extra Mixto Diurna con Recargo
	            	if($fila[mixtoextdiurna] != '00:00' AND $fila[mixtoextdiurna] != ""){
	            		array_push($cabecera, 'MixExt');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[mixtoextdiurna]);
	            	}
	            	//Hora Extra Mixto Nocturna
	            	if($fila[mixtonoc] != '00:00' AND $fila[mixtonoc] != ""){
	            		array_push($cabecera, 'NocMix');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[mixtonoc]);
	            	}
	            	//Hora Extra Mixto Nocturna con Recargo
	            	if($fila[mixtoextnoc] != '00:00' AND $fila[mixtoextnoc] != ""){
	            		array_push($cabecera, 'MixNoc');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[mixtoextnoc]);
	            	}
	            	//Descanso Extra
	            	if($fila[descextra1] != '00:00' AND $fila[descextra1] != ""){
	            		array_push($cabecera, 'DesExt');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[descextra1]);
	            			            	}
	            	//Llamado de Emergencia
	            	if($fila[ent_emer] != '00:00' AND $fila[ent_emer] != ""){
	            		array_push($cabecera, 'E.Em');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[ent_emer]);
	            	}
	            	//Llamado de Emergencia
	            	if($fila[sal_emer] != '00:00' AND $fila[sal_emer] != ""){
	            		
	            		array_push($cabecera, 'S.Em');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[sal_emer]);
	            	}
	            	//observacion
	            	if($fila[observacion] != '00:00' AND $fila[observacion] != ""){
	            	
	            		array_push($cabecera, 'obs');
	            		array_push($w, 11);
	            		array_push($cuerpo, $fila[observacion]);
	            	}	        

	            if($i==0)
	                {	
	                	$this->SetFont('helvetica', 'B', 9);
						$this->SetTextColor(0);
                        $this->Cell(110, $altura, $fila[ficha]." - ".$fila[apenom]." - ".$cargo, 0,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                  	$this->Ln();
						$fichaaux=$fila[ficha];
                        $fechaaux=$fecha;
                        $apenomaux=$fila[apenom];
	                }
	                if($ficha!=$fichaaux)
	                {
	                	$this->SetFont('helvetica', 'B', 9);
						$this->SetTextColor(0);
	                    //TOTALES
	                	$this->Cell(110, $altura,$fila[ficha]." - ".$fila[apenom]." - ".$cargo, $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                	$this->Ln();
	                    

	                }
	                $this->SetFont('helvetica', '', 8);
						$this->SetTextColor(0);
	            $num_headers = count($cabecera);
					if(	$this->GetY() >172)
					{
						$this->AddPage();
					}
					for($i = 0; $i < $num_headers; ++$i) {
					$this->Cell($w[$i], 8, $cabecera[$i], 0, 0, 'C', 1);
					/*if($i==18)
						{
							$this->Ln();		
						}*/
				}
				$this->Ln();
				 
	            $turno=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
	                $entradaf=$salmuf=$ealmuf=$salidaf="";
	               	
					for ($ixx=0; $ixx <count($cuerpo) ; $ixx++) { 
						$ancho=11;
						if($ixx==0)
						{
							$ancho=16;
						}
						/*if($ixx==18)
						{
							$this->Ln();		
						}*/
						
						$this->Cell($w[$ixx], $altura, $cuerpo[$ixx], $border,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

					}
	                $this->Ln();

	                $fechaaux=$fecha;
	                $j=0;               


	                $j++;
	                $i++;
	             }//fin del while
	             		

	        }// Fin del While		
			
		}//Fin del While
	
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
$pdf->SetTitle('Asistencia Personal');
$pdf->SetSubject('Reporte Asistencia Personal');
$pdf->SetKeywords('TCPDF, PDF, reporte, asistencia, personal');

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
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('L', 'A4');

$reg=$_REQUEST['reg'];


//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Informe_Asistencia_Personal.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();