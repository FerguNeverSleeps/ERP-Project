<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
/*Se reciben los parámetros*/

$reg         = $_GET["reg"];

/*Se inicializa la plantilla de excel*/
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla_blanco.xlsx");

/*Se buscan los parámetros del turno*/
$sql_turno   = "SELECT * FROM reloj_encabezado WHERE cod_enca = ".$reg;
$res_turno   = $db->query($sql_turno);
$fila_turno  = $res_turno->fetch_assoc();


$fecha_ini=$fila_turno['fecha_ini'];
$fecha_fin=$fila_turno['fecha_fin'];
$dias = (strtotime($fecha_ini)-strtotime($fecha_fin))/86400;
$dias = abs($dias); 
$dias = floor($dias); 
$sql = "SELECT rd.ficha, np.cedula, np.estado, np.fecing, np.apenom, np.suesal, SUM(  ordinaria +  domingo +  nacional ) AS horas , sum(tardanza) as tardanzas 
     FROM  reloj_detalle as rd LEFT JOIN  nompersonal as np on (rd.ficha=np.ficha)
     WHERE   rd.id_encabezado ='{$reg}'
     GROUP BY  ficha";

$i=11;


$res_turnos   = $db->query($sql);
;

$num_filas = $fila_turnos -> num_rows;
$registro= "";
$contador=3;
$fecha=$fecha_ini;
for($i=0; $i < $dias; $i++)
{
                
    $fecha = strtotime ( 'next day' , strtotime ( $fecha ) ) ;
    $fecha = date ( 'Y-m-j' , $fecha );
    $registro[$i]=$fecha;
}
while($fila  = $res_turnos->fetch_assoc())
{
    for($i=0; $i < $dias; $i++)
    {
        /**/
            $nuevafecha=explode("-", $registro[$i]);
            $turnolibre = '6';
            $SQL2 = "SELECT str_to_date(rd.entrada,'%H:%i') as entrada, rd.tardanza,ncp.dia_fiesta, np.ficha,ncp.turno_id
                FROM  reloj_detalle as rd
                INNER join nompersonal as np on (rd.ficha=np.ficha)
                INNER join nomcalendarios_personal as ncp on (np.ficha = ncp.ficha)
                WHERE  rd.id_encabezado =".$reg."
                and DAY(rd.fecha)=".$nuevafecha[2]."
                and MONTH(rd.fecha)=".$nuevafecha[1]."
                AND YEAR(rd.fecha)=".$nuevafecha[0]."
                AND  ncp.fecha='".$registro[$i]."' 
                AND rd.ficha=".$fila['ficha']."
                GROUP BY  rd.ficha";
        $resultado=$db->query($SQL2);
        $columnas = $resultado->fetch_array();
        $arrayName = array($registro[$i] => $columnas);
        /* $ficha            =$fila['ficha'];
            
            $suesal           =$fila['suesal'];
            $nombre           = utf8_decode( $fila['apenom']);
            $horas_ordinarias =$fila['horas'];
            $tardanzas        =0;
            $ausencias        =0;
            $rataxhora        =(($suesal/4.33)/44);
            $rataxhora        =round($rataxhora,2);
            
            $fecha            =$fecha_ini;

            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$contador, $horas['ficha']);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$contador, $horas['suesal']);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$contador, $horas['cedula']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$contador, $horas['apenom']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$contador, $horas['horas']);*/
            $contador++;

            print_r($arrayName);
        
    }
}exit;
/*
echo $consulta ="select ifnull(count(fecha),0) as cantidad 
from nomcalendarios_personal 
where turno_id<>'$turnoLibre' and fecha not in ( 
    SELECT reloj_detalle.fecha 
    FROM reloj_detalle 
    INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca  WHERE reloj_encabezado.cod_enca='$reg' AND reloj_detalle.ficha='".$ficha."' )
     and fecha between '$fecha_ini' and '$fecha_fin' AND ficha='".$ficha."'";
exit;*/


//$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "TOTALES");
$i=$i+3;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
/*Nombre del archivo*/
$nombre = "horas_extras_xls_".$fila_turno[fecha_ini]."_".$fila_turno[fecha_fin].".xlsx";
header('Content-Disposition: attachment;filename='.$nombre);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>