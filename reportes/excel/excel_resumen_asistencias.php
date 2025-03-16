<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
//------------------------------------------------------------
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/resumen_asistencias.xlsx");
//------------------------------------------------------------
$fecini = getDia( $_POST['mes'] , 1 );
$fecfin = getDia( $_POST['mes'] , 2 );
$gerencia = $_POST['gerencia'];
$dpto   = $_POST['dpto'];
//------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql);
$empresa = mysqli_fetch_array($res);
//------------------------------------------------------------
if ( $dpto == "all" ) {
    $sql     = "SELECT * FROM nomnivel2 WHERE gerencia = '$gerencia'";
    $res_uni = $db->query($sql);
}elseif($dpto == "ger") {
    $sql     = "SELECT * FROM nomnivel1 WHERE codorg = '$gerencia'";
    $res_uni = $db->query($sql);
}else{
    $sql     = "SELECT * FROM nomnivel2 WHERE gerencia = '$gerencia' AND codorg = '$dpto'";
    $res_uni = $db->query($sql);
}
//------------------------------------------------------------
$sql = "SELECT a.ficha,a.fecha,b.acronimo,b.codigo FROM caa_incidencias_empleados AS a 
        LEFT JOIN caa_incidencias AS b ON a.id_incidencia = b.id 
        WHERE a.fecha BETWEEN '$fecini'  AND '$fecfin' AND b.codigo IN (1,2,3,4,9)";
$res    = $db->query($sql);
$i=0;
while($inci = mysqli_fetch_array($res))
{
    $incidencias[$i] = array(
        'ficha'    => $inci['ficha'],
        'fecha'    => $inci['fecha'],
        'acronimo' => $inci['acronimo'],
        'codigo'   => $inci['codigo']
        );
    $i++;
}
//------------------------------------------------------------
$col_dias = array('0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
    'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL');
//------------------------------------------------------------
//Estilos para filas
$encab1 = array(
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
    'bold'  => true,
    'color' => array('rgb' => 'FF000000'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
$encab2 = array(
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
    'bold'  => true,
    'color' => array('rgb' => '7f7f7f'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------
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
    'bold'  => true,
    'color' => array('rgb' => 'FF000000'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
$borders2 = array(
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
    'bold'  => true,
    'color' => array('rgb' => '7f7f7f'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', $empresa['nom_emp']);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "DIRECCION DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('B3', "DEPARTAMENTO DE REGISTRO Y CONTROL DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('AF1', $_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('AF2', date('d-m-Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('AF3', $fecini." al ".$fecfin);
//Fin encabezado tabla
$rowCount = 4;
if ( ( $dpto == "all" )||($dpto == "ger") )
{
    //-----------------------------------------------
    // Gerencia
    //-----------------------------------------------
    while($unidad = mysqli_fetch_array($res_uni))
    {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "UNIDAD ADMINISTRATIVA:");
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $unidad['descrip']);
        $rowCount++;
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Nombre del Funcionario");
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "Funcionario");
        $encd = count($col_dias) - 5;
        $j=1;
        for ($i=3; $i < $encd; $i++)
        {
            $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, $j );
            $j++;
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, "AI" );
        $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, "AJ" );
        $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, "TI" );
        $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, "TJ" );
        $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, "P" );
        $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($encab1);
        $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($encab2);
        $rowCount++;
        //------------------------------------------------------------
        if( $dpto == "ger" ) {
            $codigo = $unidad['codorg'];
        }else{
            $codigo = $unidad['gerencia'];
        }
        $sql = "SELECT * FROM nompersonal WHERE codnivel1 = '".$codigo."' AND codnivel2 = '".$unidad['codorg']."' AND marca_reloj = 1 AND estado != 'Egresado' AND estado != 'De Baja' ORDER BY ficha ASC";
        $res_emp = $db->query($sql);
        //------------------------------------------------------------
        if ( mysqli_num_rows($res_emp) != 0 )
        {
            $INICIO = $rowCount;
            while($empleados = mysqli_fetch_array($res_emp))
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $empleados['apenom']);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $empleados['ficha']);
                $fecdia = $fecini;
                $dias = count($col_dias) - 5;
                for ($i=1; $i < 6; $i++) { 
                    $cuenta[$i] = 0;
                }
                for ($i=3; $i < $dias; $i++)
                {
                    $fecdia = sumeDiaFecha($fecdia,'+1 day');
                    $inc    = tipo_incidencia($empleados['ficha'],$fecdia,$incidencias);
                    $cuenta = cuentaInc($cuenta,$inc);
                    $d=date('N',strtotime($fecdia));
                    if ( ($empleados['estado'] == 'Licencia con Sueldo') && ($d<6) ) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, "LS");
                    }elseif ( ($empleados['estado'] == 'Vacaciones') && ($d<6) ) {
                        $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, "LS");
                    }else{
                        $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, $inc[0]);
                    }   
                }
                $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $cuenta[3]);
                $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $cuenta[4]);
                $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $cuenta[1]);
                $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $cuenta[2]);
                $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $cuenta[5]);

                $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($borders1);
                $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($borders2);
                $rowCount++;
            }
            $final = $rowCount-1;
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':AF'.$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AG".$rowCount,"Total:");
            $objPHPExcel->getActiveSheet()->SetCellValue("AH".$rowCount,"=SUM(AH".$INICIO.":AH".$final.")");
            $objPHPExcel->getActiveSheet()->SetCellValue("AI".$rowCount,"=SUM(AI".$INICIO.":AI".$final.")");
            $objPHPExcel->getActiveSheet()->SetCellValue("AJ".$rowCount,"=SUM(AJ".$INICIO.":AJ".$final.")");
            $objPHPExcel->getActiveSheet()->SetCellValue("AK".$rowCount,"=SUM(AK".$INICIO.":AK".$final.")");
            $objPHPExcel->getActiveSheet()->SetCellValue("AL".$rowCount,"=SUM(AL".$INICIO.":AL".$final.")");

            $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($borders1);
            $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($borders2);
            $rowCount++;
        }
    }
}    
//-----------------------------------------------
// DEpartamentos asignados a la gerencia
//-----------------------------------------------
while($unidad = mysqli_fetch_array($res_uni))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "UNIDAD ADMINISTRATIVA:");
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $unidad['descrip']);
    $rowCount++;
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Nombre del Funcionario");
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "Funcionario");
    $encd = count($col_dias) - 5;
    $j=1;
    for ($i=3; $i < $encd; $i++)
    {
        $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, $j );
        $j++;
    }
    $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, "AI" );
    $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, "AJ" );
    $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, "TI" );
    $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, "TJ" );
    $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, "P" );
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($encab1);
    $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($encab2);
    $rowCount++;
    //------------------------------------------------------------
    $sql = "SELECT * FROM nompersonal WHERE codnivel1 = '".$unidad['gerencia']."' AND codnivel2 = '".$unidad['codorg']."' AND marca_reloj = 1 AND estado != 'Egresado' AND estado != 'De Baja' ORDER BY ficha ASC";
    $res_emp = $db->query($sql);
    //------------------------------------------------------------
    if ( mysqli_num_rows($res_emp) != 0 )
    {
        $INICIO = $rowCount;
        while($empleados = mysqli_fetch_array($res_emp))
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $empleados['apenom']);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $empleados['ficha']);
            $fecdia = $fecini;
            $dias = count($col_dias) - 5;
            for ($i=1; $i < 6; $i++) { 
                $cuenta[$i] = 0;
            }
            for ($i=3; $i < $dias; $i++)
            {
                $fecdia = sumeDiaFecha($fecdia,'+1 day');
                $inc    = tipo_incidencia($empleados['ficha'],$fecdia,$incidencias);
                $cuenta = cuentaInc($cuenta,$inc);
                $d=date('N',strtotime($fecdia));
                if ( ($empleados['estado'] == 'Licencia con Sueldo') && ($d<6) ) {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, "LS");
                }elseif ( ($empleados['estado'] == 'Vacaciones') && ($d<6) ) {
                    $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, "LS");
                }else{
                    $objPHPExcel->getActiveSheet()->SetCellValue($col_dias[$i].$rowCount, $inc[0]);
                }   
            }
            $objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, $cuenta[3]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, $cuenta[4]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, $cuenta[1]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, $cuenta[2]);
            $objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, $cuenta[5]);

            $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($borders1);
            $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($borders2);
            $rowCount++;
        }
        $final = $rowCount-1;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$rowCount.':AF'.$rowCount);
        $objPHPExcel->getActiveSheet()->SetCellValue("AG".$rowCount,"Total:");
        $objPHPExcel->getActiveSheet()->SetCellValue("AH".$rowCount,"=SUM(AH".$INICIO.":AH".$final.")");
        $objPHPExcel->getActiveSheet()->SetCellValue("AI".$rowCount,"=SUM(AI".$INICIO.":AI".$final.")");
        $objPHPExcel->getActiveSheet()->SetCellValue("AJ".$rowCount,"=SUM(AJ".$INICIO.":AJ".$final.")");
        $objPHPExcel->getActiveSheet()->SetCellValue("AK".$rowCount,"=SUM(AK".$INICIO.":AK".$final.")");
        $objPHPExcel->getActiveSheet()->SetCellValue("AL".$rowCount,"=SUM(AL".$INICIO.":AL".$final.")");
        $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AG".$rowCount)->applyFromArray($borders1);
        $objPHPExcel->getActiveSheet()->getStyle("AH".$rowCount.':'."AL".$rowCount)->applyFromArray($borders2);
        $rowCount++;
    }
}
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount, "OBSERVACIONES:");
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B".$rowCount.":AL".$rowCount);
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AL".$rowCount)->applyFromArray($borders1);
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":AL".$rowCount);
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."AL".$rowCount)->applyFromArray($borders1);
$rowCount++;
$rowCount++;
//---------------------------------------------------------------------------------------------------------------
//Descripciones
//Fila 1
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        TJ.   Tardanza Justificada");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        TC.   Tiempo Compensatorio");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);
//Fila 2
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        TI.   Tardanza Injustificada");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        V.      Vacaciones");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);
//Fila 3
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        AJ.   Ausencia Justificada");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        L.G.   Licencia por Gravidez");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("S".($rowCount-2).":AL".$rowCount);
$objPHPExcel->getActiveSheet()->getStyle("S".($rowCount-2).':'."AL".$rowCount)->applyFromArray($borders1);
//Fila 4
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        AJ.   Ausencia Injustificada");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        L.B.   Licencia por Estudios ( Becas,Conferencias,Cursos )");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("S".$rowCount.":AL".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("S".$rowCount,"    ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->getStyle("S".$rowCount.':'."AL".$rowCount)->applyFromArray($borders1);
//Fila 5
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        A.E.  Ausencia por Enfermedad [Hasta por 3 (tres) días]");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        M.O.  Mision Oficial");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("S".$rowCount.":AB".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("S".$rowCount,"    PREPARADO POR");
$objPHPExcel->getActiveSheet()->getStyle("S".$rowCount.':'."AB".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("AC".$rowCount.":AL".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("AC".$rowCount,"   FIRMA DEL JEFE DE UNIDAD");
$objPHPExcel->getActiveSheet()->getStyle("AC".$rowCount.':'."AL".$rowCount)->applyFromArray($borders1);
//Fila 6
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        P.     Permiso ( Cuando el empleado debe retirarse de la oficina )");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        A.T.   Salida antes de Tiempo");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);
//Fila 7
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        L.E.  Licencia por Enfermedad [ Más de 3 (tres) días]");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        S.E.   Sin marcación en la Entrada");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);
//Fila 8
$rowCount++;
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A".$rowCount.":G".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("A".$rowCount,"        L.J.  Libre por Jurado");
$objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."G".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("H".$rowCount.":R".$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount,"        S.S.   Sin marcación de salida");
$objPHPExcel->getActiveSheet()->getStyle("H".$rowCount.':'."R".$rowCount)->applyFromArray($borders1);

$objPHPExcel->setActiveSheetIndex(0)->mergeCells("S".($rowCount-2).":AL".$rowCount);
$objPHPExcel->getActiveSheet()->getStyle("S".($rowCount-2).':'."AL".$rowCount)->applyFromArray($borders1);

$objPHPExcel->getActiveSheet()->setTitle('Resumen de Asistencias');
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="resumen_asistencias.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>