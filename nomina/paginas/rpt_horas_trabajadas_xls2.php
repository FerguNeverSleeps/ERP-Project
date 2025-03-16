<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');
function dias_transcurridos($fecha_i,$fecha_f)
{
    $dias   = (strtotime($fecha_i)-strtotime($fecha_f))/86400;
    $dias   = abs($dias); $dias = floor($dias);     
    return $dias+1;
}
// Ejemplo de uso:2015-10-30    2015-11-12

function traducir($fecha)
{
    switch ($fecha) {

        case 'Monday':
            return "Lunes";
            break;
        case 'Tuesday':
            return "Martes";
            break;
        case 'Wednesday':
            return "Miercoles";
            break;
        case 'Thursday':
            return "Jueves";
            break;
        case 'Friday':
            return "Viernes";
            break;
        case 'Saturday':
            return "Sábado";
            break;
        case 'Sunday':
            return "Domingo";
            break;
        default:
            # code...
            break;
    }
}
include('../lib/common_excel.php');
include('lib/php_excel.php');
include("funciones_nomina.php");
$conexion=conexion();

$codtip=$_GET['codtip'];
$id=$_GET['reg'];

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Fondo de Cesantia")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
query("SET names utf8;", $conexion);
$res=query($sql, $conexion);

$fila=fetch_array($res);
$logo=$fila['logo'];

$sql2 = "SELECT fecha_ini,fecha_fin FROM reloj_encabezado WHERE cod_enca=".$id;

$res2=query($sql2, $conexion);

$fila2=fetch_array($res2);

$fecha_ini=$fila2['fecha_ini'];
$fecha_fin=$fila2['fecha_fin'];

//$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta  FROM nom_nominas_pago np 
//         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
//$res2=query($sql2, $conexion);
//$fila2=fetch_array($res2);

/*$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setWidth(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
*/
//$objDrawing->setHeight(36);
//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$objPHPExcel->setActiveSheetIndex(0); 

$objPHPExcel->getActiveSheet()->setTitle('HOJA TARD-AUS');
$objPHPExcel->getActiveSheet()
            ->setCellValue('B2', strtoupper( $fila['empresa']))
            ->setCellValue('B3',  strtoupper(utf8_encode($fila['rif']).' Telefonos '.$fila['telefono']))
            ->setCellValue('B4', strtoupper('Direccion: '.utf8_encode($fila['direccion'])))
            ->setCellValue('B6', 'REPORTE DE TARDANZAS Y AUSENCIAS')
            ->setCellValue('B7', 'FECHA INICIO : '.$fecha_ini.' -  FECHA FIN:'.$fecha_fin)
             ->setCellValue('B8', strtoupper('Fecha: '.date('d/m/Y')));    

$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
$objPHPExcel->getActiveSheet()->mergeCells('B2:H2');
$objPHPExcel->getActiveSheet()->mergeCells('B3:H3');
$objPHPExcel->getActiveSheet()->mergeCells('B4:H4');
$objPHPExcel->getActiveSheet()->mergeCells('B6:H6');
$objPHPExcel->getActiveSheet()->mergeCells('B7:H7');
$objPHPExcel->getActiveSheet()->mergeCells('B8:H8');
//$objPHPExcel->getActiveSheet()->mergeCells('F10:G10');
//$objPHPExcel->getActiveSheet()->mergeCells('F11:G11');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B7:E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B8:E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$dias=dias_transcurridos($fecha_ini,$fecha_fin);
$fecha=$fecha_ini;
$Caracter="D";
for ($i=0; $i < $dias; $i++) {
   
    $objPHPExcel->getActiveSheet()->getStyle($Caracter.'9')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle($Caracter.'9')->getFont()->setSize(8);
    $objPHPExcel->getActiveSheet()->getStyle($Caracter.'9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'9',  traducir (date("l",strtotime($fecha ) )));
    $fecha = strtotime ( 'next day' , strtotime ( $fecha ) ) ;
    $fecha = date ( 'Y-m-j' , $fecha );

    $Caracter++;
    $objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(8);

}
$fecha=$fecha_ini;
$Caracter="A";
$objPHPExcel->getActiveSheet()->mergeCells('B10:C10');
$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(5);
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10', 'FICHA');
$Caracter++;
$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(20);
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10', 'NOMBRE Y APELLIDO');
$Caracter++;

$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(20);
$Caracter++;

for ($i=0; $i < $dias; $i++) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(8);   
    $objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10',  date("d",strtotime($fecha )));            
    $fecha = strtotime ( 'next day' , strtotime ( $fecha ) ) ;
    $fecha = date ( 'Y-m-j' , $fecha );
    $objPHPExcel->getActiveSheet()->getStyle($Caracter.'10')->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getStyle($Caracter.'10')->getFont()->setSize(8);
    $Caracter++;

}

$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(10);
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'9','TARDANZAS');
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10','POR HORA');            
$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setAutoSize(true);
$CaracterTardanza=$Caracter;
$objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.'9:'.$CaracterTardanza.'10')->applyFromArray(allBordersThin());
$Caracter++;

$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(10);
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'9', 'AUSENCIAS');
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10', 'POR HORA');            
$CaracterAusencias=$Caracter;

$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getStyle($Caracter.'9:'.$Caracter.'10')->applyFromArray(allBordersThin());

$Caracter++;

$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setWidth(10);
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'9', 'MONTO');
$objPHPExcel->getActiveSheet()       
            ->setCellValue($Caracter.'10','A DESCONTAR');            
$objPHPExcel->getActiveSheet()->getColumnDimension($Caracter)->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getStyle($Caracter.'9:'.$Caracter.'10')->applyFromArray(allBordersThin());

$CaracterDescontar=$Caracter;

$objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.'9:'.$Caracter.'9')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.'9:'.$Caracter.'9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A10:'.$Caracter.'10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:'.$Caracter.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
cellColor('A10:'.$Caracter.'10', 'FFFF00');
cellColor($CaracterTardanza.'9:'.$Caracter.'9', 'FFFF00');


/*$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.($i+4))->applyFromArray(allBordersThin());*/

$objPHPExcel->getActiveSheet()->getStyle('A10:'.$Caracter.'10')->applyFromArray(allBordersThin());
$sql = "SELECT rd.ficha, np.cedula, np.estado, np.fecing, np.apenom, np.suesal, SUM(  ordinaria +  domingo +  nacional ) AS horas , sum(tardanza) as tardanzas, np.tipnom
 FROM  reloj_detalle as rd INNER JOIN nompersonal as np ON (rd.ficha=np.ficha)
  WHERE   rd.id_encabezado ='{$id}'
GROUP BY  ficha";
$i=11;

$res=query($sql, $conexion);

$num_filas = num_rows($res);

$contador=0;
while($fila=fetch_array($res))
{
    $ficha=$fila['ficha'];
    $suesal=$fila['suesal'];
    $nombre=  $fila['apenom'];
    $horas_ordinarias=$fila['horas'];
    $tardanzas=0;
    $ausencias=0;
    if($fila['tipnom']==2)
    {
        $rataxhora = $suesal;
    }
    elseif($fila['tipnom']==2){
        $rataxhora=(($suesal/4.33)/44);
        $rataxhora=round($rataxhora,2);

    }

    $fecha=$fecha_ini;    

    if($contador<$num_filas-1)
    {    
        $Caracter="A";
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $ficha);
        $Caracter++;
        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $nombre);
        $Caracter++;
        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cedula);        
        $Caracter="D";
        for ($xx=0; $xx < $dias; $xx++) { 

            $nuevafecha=explode("-", $fecha);
            $SQL2 = "SELECT str_to_date(rd.entrada,'%H:%i') as entrada, rd.tardanza
                    FROM  reloj_detalle as rd
                    INNER JOIN nompersonal as np ON (rd.ficha=np.ficha)
                    WHERE  rd.id_encabezado =".$id."
                    and DAY(rd.fecha)=".$nuevafecha[2]."
                    and MONTH(rd.fecha)=".$nuevafecha[1]."
                    AND YEAR(rd.fecha)=".$nuevafecha[0]."
                    AND rd.ficha=".$fila['ficha']."
                    GROUP BY  rd.ficha";
            $resultado=query($SQL2, $conexion);
            $columnas = fetch_array($resultado); 

            $SQL3 = "SELECT ncp.dia_fiesta, np.ficha,ncp.turno_id
                    FROM  nomcalendarios_personal as ncp
                    INNER JOIN nompersonal as np ON (ncp.ficha=np.ficha)
                    WHERE ncp.fecha='".$fecha."'
                    AND np.ficha=".$fila['ficha'];
            $resultado=query($SQL3, $conexion);
            $dias_feriados = fetch_array($resultado); 
            $nuevatardanza=explode(":", $columnas['tardanza']);

            $tardanza=((($nuevatardanza[0]*60)+($nuevatardanza[1]))/60);
            $tardanzas+=$tardanza;
            $tardanzas=round($tardanzas,2);
            /*$consulta ="SELECT ifnull(count(fecha),0) as cantidad 
            from nomcalendarios_personal 
            where turno_id<>'6' and fecha not in ( 
                SELECT reloj_detalle.fecha 
                FROM reloj_detalle 
                INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca 
                WHERE reloj_encabezado.cod_enca='$id' AND reloj_detalle.ficha='".$ficha."' ) 
                and fecha ='{$fecha}' AND ficha='".$ficha."'";
            $resultado=query($consulta, $conexion);
            $result_count = fetch_array($resultado); */
         //$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $tardanza." - ".$dias_feriados['turno_id']." - ".$dias_feriados['dia_fiesta']);
         if($fila['estado']=="Nuevo")
            {
                if ($fila['fecing']>=$fecha) {
                   $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, "-");  
                }
                else
                {
                    if($columnas['entrada']=="" AND ($dias_feriados['turno_id']=="11") )
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "08:00");
                        $ausencias+=8;
                    }
                    else
                    {
                        if($columnas['entrada']!="00:00")
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, "-");    
                        }
                        else
                        {
                            $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, "-");    
                        }
                        
                    }
                }
            }
            else
            {
                if($columnas['entrada']=="" )
                {
                    //ir a nomcalendarios_empleados y buscar si el turno es distinto a 11 (turno libre) 
                    if($columnas['entrada']=="" AND ($dias_feriados['turno_id']=="11") )
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "00:00");
                    }
                    else{
                        
                        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "08:00");
                        $ausencias+=8;
                    }
                }
                elseif($columnas['entrada']!="" AND  $dias_feriados['dia_fiesta']=="0")
                {

                    $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $columnas['tardanza']);
                
                } 
                else
                {
                    if($columnas['entrada']!="00:00")
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, "-");    
                    }
                    else
                    {
                        $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, "-");    
                    }
                    
                }
            }         

            $Caracter++;
            $fecha = strtotime ( 'next day' , strtotime ( $fecha ) ) ;
            $fecha = date ( 'Y-m-j' , $fecha );
        }


        if ($tardanzas=="0") {
           $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "-");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $tardanzas);
        }
        $Caracter++;
        if ($ausencias=="0") {
           $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "-");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $ausencias);
        }
        $Caracter++;
        $montotardanzas=$rataxhora*$tardanzas;
        $montotardanzas=round($montotardanzas,2);
        $montoausencias=$rataxhora*$ausencias;
        $montoausencias=round($montoausencias,2);
        $montototal=$montoausencias+$montotardanzas;
        $total+=$montototal;
        if ($montototal=="0") {
           $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i,  "-");
        }else{
            $objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $montototal);
        }
    }
    else
    {
       $objPHPExcel->getActiveSheet()->mergeCells($CaracterTardanza.$i.':'.$CaracterAusencias.$i);

        $objPHPExcel->getActiveSheet()->setCellValue($CaracterTardanza.$i, 'TOTALES');
        $objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.$i)->getFont()->setName('Arial');
        $objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.$i)->getFont()->setSize(14);  
        $objPHPExcel->getActiveSheet()->getStyle($CaracterTardanza.$i)->getFont()->setBold(true);      
      //  $objPHPExcel->getActiveSheet()->setCellValue($CaracterTotal.$i, $horas_ordinarias);
        $objPHPExcel->getActiveSheet()->setCellValue($CaracterDescontar.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle($CaracterDescontar.$i)->getFont()->setName('Arial');
        $objPHPExcel->getActiveSheet()->getStyle($CaracterDescontar.$i)->getFont()->setSize(14);  
        $objPHPExcel->getActiveSheet()->getStyle($CaracterDescontar.$i)->getFont()->setBold(true);         
        cellColor($CaracterTardanza.$i.':'.$CaracterDescontar.$i, 'D9D9D9');

    }
   
    
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$Caracter.$i)->applyFromArray(allBordersThin());
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':'.$Caracter.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    
        //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
    $contador++;
}
//$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "TOTALES");
$i=$i+3;

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':B'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;

$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//$objPHPExcel->getActiveSheet()->setSelectedCells('B100');


$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
$nombre_archivo="Reporte_Ausencia_Tardanzas_".$fecha_ini."_".$fecha_fin;
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'".xls"');
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

$xlsData = ob_get_contents();
ob_end_clean();
$response =  array(
    'op' => 'ok',
    'file' => "data:application/vnd.ms-Excel;base64,".base64_encode($xlsData)
);

die(json_encode($response));

?>




