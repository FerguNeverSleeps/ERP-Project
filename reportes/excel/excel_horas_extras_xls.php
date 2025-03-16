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

/*Estilo de celda*/

$titulo = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 16,
        'name'  => 'Arial',
        'center' => true
    )
);
$borders = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        )
      ),
      'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Verdana'
    )
    );

/*Se busca la fecha agrupada*/
$sql_fichas = "
    SELECT DISTINCT ficha, 
    GROUP_CONCAT( fecha order by fecha) fechas
    FROM reloj_detalle
    WHERE id_encabezado = '{$reg}'
    GROUP BY ficha ASC";
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tiempos por Departamento en detalle");
$objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($titulo);
$objPHPExcel->getActiveSheet()->SetCellValue('I1', date('d/m/Y',strtotime($fila_turno['fecha_reg'])));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);



/*Titulos en encabezado*/
$objPHPExcel->getActiveSheet()->getStyle('B3:I3')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B3', "FECHA");
$objPHPExcel->getActiveSheet()->SetCellValue('C3', "NOMBRE");
$objPHPExcel->getActiveSheet()->SetCellValue('D3', "DÍA");
$objPHPExcel->getActiveSheet()->SetCellValue('E3', "TURNO");
$objPHPExcel->getActiveSheet()->SetCellValue('F3', "TIPO");
$objPHPExcel->getActiveSheet()->SetCellValue('G3', "DESDE");
$objPHPExcel->getActiveSheet()->SetCellValue('H3', "HASTA");
$objPHPExcel->getActiveSheet()->SetCellValue('I3', "HORAS EXTRA");
//Fin encabezado tabla
$rowCount = 4;
/*Se asignan los registros en cada celda*/
$db = new Database($_SESSION['bd']);

$res_fichas = $db->query($sql_fichas);


date_default_timezone_set('America/Panama');
/*El recorrido es por fechas*/
while($row = $res_fichas->fetch_array())
{
  $fichaaux = 0;
  $ficha = $row['ficha'];
  $fechas = explode(",", $row['fechas']);
  /*Se recorren las fichas registradas por fechas*/
  for ($i=0; $i < count($fechas); $i++) 
  { 
    /*Se busca el turno*/

    $sql_turnos = "SELECT * from nomcalendarios_personal ncp 
    LEFT JOIN nomturnos nt on (ncp.turno_id = nt.turno_id)
    LEFT JOIN nompersonal np on (np.ficha=ncp.ficha)
    WHERE ncp.fecha = '".$fechas[$i]."' AND ncp.ficha = '{$ficha}'";

    $res_turnos   = $db->query($sql_turnos);
    $turnos       = $res_turnos->fetch_assoc();
    
    /*Se buscan las fechas*/
    $sql_fechas   = "SELECT * FROM reloj_detalle where ficha = '{$ficha}' AND fecha = '{$fechas[$i]}'";
    $res_fechas   = $db->query($sql_fechas);
    $marcaciones  = $res_fechas->fetch_assoc();
    
    $resta        = strtotime($marcaciones[salida]) - strtotime($turnos[salida]);
    $restadomingo = strtotime($marcaciones[salida]) - strtotime($marcaciones[entrada]);
    $hora_domingo = date('H',strtotime($marcaciones[domingo]));
    $minuto_domingo = date('i',strtotime($marcaciones[domingo]));
    $resta        = round($resta/3600,2);
    $restadomingo = round($hora_domingo+($minuto_domingo/60),2);
    if ($resta != 0 AND $resta>=0.15) {
      /*Se verifica si es la primera vez que se muestra la ficha*/
      if($ficha!=$fichaaux)
      {
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,$ficha);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,($turnos['apenom']));
        $fichaaux  = $ficha;
        $rowCount++;
      }
      /*Se comprueba si la fecha es un lunes*/
      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, date("d-m-Y",strtotime($turnos['fecha'])));

      $dia = date('l', strtotime($turnos['fecha']));
      $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
      $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
      $nombredia = str_replace($dias_EN, $dias_ES, $dia);
      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $nombredia);
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, date('d',strtotime($turnos['fecha'])));
      $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $turnos['turno_id']);
      $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $turnos['descripcion']);

      if ($nombredia!="Domingo") {
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $turnos['salida']);
      }
      else
      {
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $marcaciones['entrada']);
      }
      $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $marcaciones['salida']);
      if ($nombredia!="Domingo") {
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $resta);
      }
      else
      {
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $restadomingo);
      }
      $totales += $resta;
      $rowCount++;
    }
  }
  $rowCount++;  
    /*Se aplican los estilos a las celdas*/
  /*$objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
  $rowCount++;*/
}
$final = $rowCount-1;
/*Se cuenta la cantidad de registros*/
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $totales);
$objPHPExcel->getActiveSheet()->setTitle('Tiempo por Departamento');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
/*Nombre del archivo*/
$nombre = "horas_extras_xls_".$fila_turno[fecha_ini]."_".$fila_turno[fecha_fin].".xlsx";
header('Content-Disposition: attachment;filename='.$nombre);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>