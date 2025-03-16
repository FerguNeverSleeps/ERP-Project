<?php
require_once("../../lib/common.php");
require_once('../../lib/database.php');
//require_once('../utilidades/funciones_reportes.php');
require_once '../phpexcel/Classes/PHPExcel.php';
$db = new Database($_SESSION['bd']);
//------------------------------------------------------------
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("../plantillas/resumen_asistencias_individuales.xlsx");
//------------------------------------------------------------
$ficha  = $_GET['ficha'];
$fecini = date("Y-m-d", strtotime($_GET['fecha1']));
$fecfin = date("Y-m-d", strtotime($_GET['fecha2']));
//------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql);
$empresa = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT a.*,b.descrip AS gerencia,c.descrip AS dpto FROM nompersonal AS a
            LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
            LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
            WHERE ficha = '$ficha'";
$res_emp = $db->query($sql);
$emp     = mysqli_fetch_array($res_emp);
$per_dat = $emp['apenom'].", Cedula: ".$emp['cedula'].",  Numero: ".$emp['ficha'].", Gerencia: ".$emp['gerencia'].", Dpto: ".$emp['dpto'];
$cedula  = $emp['cedula'];
//------------------------------------------------------------
$sql = "SELECT * FROM nomturnos";
$res = $db->query($sql);
while ($tur = mysqli_fetch_array($res)) {
	$turno[$tur['turno_id']] = $tur['entrada']." - ".$tur['salida'];
}
//------------------------------------------------------------
// AND (tardanza != '00:00:00' OR entrada='00:00:00' OR salida='00:00:00' OR h_extra!='00:00:00')
$sql     = "SELECT * FROM `caa_resumen` WHERE `ficha` = '$ficha' AND `fecha_registro` >= '$fecini' AND `fecha_registro` <= '$fecfin' ORDER BY `fecha` ASC";
$res_caa = $db->query($sql);
//------------------------------------------------------------
$dias = array('Lun','Mar','Mie','Jue','Vie','Sab','Dom');
//------------------------------------------------------------
//Estilos para filas
$borders1 = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FF000000'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
        )
    ),
    'font'  => array(
    'color' => array('rgb' => 'FF000000'),
    'size'  => 8,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('C1', $empresa['nom_emp']);
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "DIRECCION DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('C3', "DEPARTAMENTO DE REGISTRO Y CONTROL DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('C4', $per_dat);
$objPHPExcel->getActiveSheet()->SetCellValue('J1', $_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('J2', date('d-m-Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('J3', $_GET['fecha1']." al ".$_GET['fecha2']);
//Fin encabezado tabla
$rowCount = 6;

while($datos = mysqli_fetch_array($res_caa))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $dias[date('N', strtotime($datos['fecha']))-1]." ".$datos['fecha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $datos['entrada']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $datos['salida']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $datos['tiempo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $datos['tardanza']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $datos['h_ausencia'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $datos['recargo_50']);

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$rowCount.':I'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $turno[$datos['turno_id']]);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, cargaIncidencias( $datos['ficha'], $datos['fecha'], $db ) );

    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."J".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}

$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':J'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Permisos Registrados en Expediente" );
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."J".$rowCount)->applyFromArray($borders1);

$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Descripcion" );
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "Inicio" );
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$rowCount.':I'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "Fin" );
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "Duracion" );
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."J".$rowCount)->applyFromArray($borders1);
$rowCount++;
//------------------------------------------------------------
$sql     = "SELECT b.nombre_tipo,c.nombre_subtipo, a.* FROM expediente a 
			LEFT JOIN expediente_tipo b ON a.tipo=b.id_expediente_tipo 
            LEFT JOIN expediente_subtipo c ON a.subtipo=c.id_expediente_subtipo
            WHERE ( 
            (a.fecha_inicio BETWEEN '$fecini' AND '$fecfin') 
            OR (a.fecha_fin BETWEEN '$fecini' AND '$fecfin') 
            OR ( a.fecha_inicio < '$fecini' AND a.fecha_fin > '$fecfin' )
            ) 
            AND a.cedula='$cedula'
            AND a.estatus=1
            AND a.tipo IN( 4, 6, 11, 12, 15, 16, 17, 28 )
            AND a.subtipo != 113 AND a.subtipo != 159
            ORDER BY a.fecha_inicio ASC";
//echo $sql;exit;
$res_caa = $db->query($sql);
//------------------------------------------------------------
$tot_perm=0;
$tot_susp=0;
$tot_vac=0;
$tot_tca=0;
$tot_tcd=0;
$tot_lic=0;
$tot_mo=0;
//colsultas de tipos diferentes a licencia
while($datos = mysqli_fetch_array($res_caa))
{
    switch ($datos['tipo']) {
        case 4:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_perm = $tot_perm+$datos['duracion']; 
            $horas = $datos['horas']+($datos['dias']*8);
            $mins = $datos['minutos'];
            $tot_perm_horas = $tot_perm_horas+$horas; 
            $tot_perm_mins = $tot_perm_mins+$mins; 
            if($tot_perm_mins>=60)
            {
                $tot_perm_horas+=1;
                $tot_perm_mins=$tot_perm_mins-60;
            }
//            $hora  = intval(abs($datos['duracion']));
//            $min   = intval(round((abs($datos['duracion']) - $hora),4) * 60);
            $duracion = ( ($datos['duracion'] !=0) ? $horas." (Horas) ".$mins." (Minutos)":"" );
            $desc     = $datos['nombre_tipo']."/".$datos['nombre_subtipo'];
            break;
        case 6:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_susp = $tot_susp+$datos['dias'];
            $duracion = ( ($datos['dias'] !=0) ? $datos['dias']." (Dias)":"" );
            $desc     = $datos['nombre_tipo'];
            break;
        case 11:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_vac  = $tot_vac+$datos['dias'];
            $duracion = ( ($datos['dias'] !=0) ? $datos['dias']." (Dias)":"" );
            $desc     = $datos['nombre_tipo'];
            break;
        case 12:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            //$horas = $datos['horas']+($datos['dias']*8);
            $horas = $datos['horas'];
            $mins = $datos['minutos'];
            
            if ($datos['subtipo']==38) {
                $tot_tcd = $tot_tcd+$datos['duracion'];
                $tot_tcd_horas = $tot_tcd_horas+$horas; 
                $tot_tcd_mins = $tot_tcd_mins+$mins; 
                if($tot_tcd_mins>=60)
                {
                    $tot_tcd_horas+=1;
                    $tot_tcd_mins=$tot_tcd_mins-60;
                }
            }else{
                $tot_tca = $tot_tca+$datos['duracion'];
                $tot_tca_horas = $tot_tca_horas+$horas; 
                $tot_tca_mins = $tot_tca_mins+$mins; 
                if($tot_tca_mins>=60)
                {
                    $tot_tca_horas+=1;
                    $tot_tca_mins=$tot_tca_mins-60;
                }
            }
//            $hora  = intval(abs($datos['duracion']));
//            $min   = intval(round((abs($datos['duracion']) - $hora),2) * 60);
            $duracion = ( ($datos['duracion'] !=0) ? $horas." (Horas) ".$mins." (Minutos)":"" );
            $desc     = $datos['nombre_tipo']."/".$datos['nombre_subtipo'];;
            break;
        case 15:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_lic  = $tot_lic+$datos['dias'];
            $duracion = ( ($datos['dias'] !=0) ? $datos['dias']." (Dias)":"" );
            $desc     = $datos['nombre_tipo']."/".$datos['nombre_subtipo'];;
            break;
        case 16:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_lic  = $tot_lic+$datos['dias'];
            $duracion = ( ($datos['dias'] !=0) ? $datos['dias']." (Dias)":"" );
            $desc     = $datos['nombre_tipo']."/".$datos['nombre_subtipo'];;
            break;
        case 17:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $tot_lic  = $tot_lic+$datos['dias'];
            $duracion = ( ($datos['dias'] !=0) ? $datos['dias']." (Dias)":"" );
            $desc     = $datos['nombre_tipo']."/".$datos['nombre_subtipo'];;
            break;
        case 28:
            $desde    = $datos['fecha_hora_inicio'];
            $hasta    = $datos['fecha_hora_fin'];
            $tot_mo   = $tot_mo+$datos['duracion'];
            $horas = $datos['horas']+($datos['dias']*8);
            $mins = $datos['minutos'];
            $tot_mo_horas = $tot_mo_horas+$horas; 
            $tot_mo_mins = $tot_mo_mins+$mins; 
            if($tot_mo_mins>=60)
            {
                $tot_mo_horas+=1;
                $tot_mo_mins=$tot_mo_mins-60;
            }
            $duracion = ( ($datos['duracion'] !=0) ? $horas." (Horas) ".$mins." (Minutos)":"" );
            $desc     = $datos['nombre_tipo'];
            break;
        
        default:
            $desde    = $datos['fecha_inicio']." ".$datos['desde'];
            $hasta    = $datos['fecha_fin']." ".$datos['hasta'];
            $duracion = $datos['duracion'];
            $desc     = $datos['nombre_tipo'];
            break;
    }
    
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount,$desc);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $desde);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$rowCount.':I'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $hasta);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $duracion);

    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."J".$rowCount)->applyFromArray($borders1);
    $rowCount++;
    
}

//TOTALES
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':G'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Resumen de tiempos registrados" );
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
$rowCount++;

$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Descripcion" );
$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "Total" );
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
$rowCount++;

//------------------------------------------------------------
if ( $tot_perm!=0 ) {
//    $hora  = intval(abs($tot_perm));
//    $min   = intval(round((abs($tot_perm) - $hora),2) * 60);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Permisos" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_perm_horas." (Horas) ".$tot_perm_mins." (Minutos)" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_susp!=0 ) {
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Suspensiones" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_susp." Dias" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_vac!=0 ) {
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Vacaciones" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_vac." Dias" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_tca!=0 ) {
//    $hora  = intval(abs($tot_tca));
//    $min   = intval(round((abs($tot_tca) - $hora),2) * 60);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Tiempo Compensatorio Aumenta" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_tca_horas." (Horas) ".$tot_tca_mins." (Minutos)" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_tcd!=0 ) {
//    $hora  = intval(abs($tot_tcd));
//    $min   = intval(round((abs($tot_tcd) - $hora),2) * 60);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Tiempo Compensatorio Disminuye" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_tcd_horas." (Horas) ".$tot_tcd_mins." (Minutos)" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_lic!=0 ) {
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Licencias" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_lic." Dias" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
if ( $tot_mo!=0 ) {
//    $hora  = intval(abs($tot_mo));
//    $min   = intval(round((abs($tot_mo) - $hora),2) * 60);
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Mision Oficial" );
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.$rowCount.':G'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_mo_horas." (Horas) ".$tot_mo_mins." (Minutos)" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}

$objPHPExcel->getActiveSheet()->setTitle('Asistencias Individuales');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="asistencias_individuales_diarias.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>