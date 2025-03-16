<?php
session_start();
/** Error reporting */
error_reporting(E_ALL);
date_default_timezone_set('America/Panama');
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once('../../nomina/lib/database.php');
require_once("../../nomina/lib/common.php");
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
require_once("../../includes/phpexcel/Classes/PHPExcel/IOFactory.php");

function fechas($fecha,$formato)
{
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    if (strlen($fecha)<2) {
        return "";
    }
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
         case 3:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia."-".$meses[$mes-1]. "-".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}

  $conexion = new Database($_SESSION['bd']); 

if(isset($_POST['codnivel1']))
{
  if( $_POST['codnivel1'] == '-1')
  {
    $sql2 = "";
    $codnivel1 ="Todas las regiones";
    $nivel = "TODAS_LAS_REGIONES";
    $retitu= "TODAS LAS REGIONES";
  }
  else
  {
    $codnivel1 = $_POST['codnivel1'];
    $sql2 = "WHERE codnivel1=$codnivel1 and n.estado<>'De Baja'";

    $sql = "SELECT descrip FROM nomnivel1 WHERE codorg='{$codnivel1}'";

    $res = $conexion->query($sql);
    $obj = $res->fetch_object();

    $nivel = str_replace(array(' ',"\"", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
               array('' , '' , "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
               $obj->descrip);
    $retitu= $obj->descrip;

  }



  if (!file_exists("plantillas/exportar_funcionarios_region.xlsx")) 
  {
    exit("Plantilla de excel no encontrada");
  }



  $objPHPExcel = new PHPExcel();

  // Leemos un archivo Excel

  // gastos_representacion+antiguedad+zona_apartada+zona_apartada+jefaturas+especialidad+otros
  $objReader   = PHPExcel_IOFactory::createReader('Excel2007');
  $objPHPExcel = $objReader->load("plantillas/exportar_funcionarios_region.xlsx");

  $objPHPExcel->setActiveSheetIndex(0);

  $sql = "SELECT n.tipnom as planilla,n.estado,n.gastos_representacion, n.antiguedad, n.zona_apartada, n.jefaturas, n.especialidad, n.otros, n.ficha, n.apellidos, n.nombres, n.cedula, 
           CASE WHEN n.nacionalidad = 1 THEN 'Panameño'
              WHEN n.nacionalidad = 2 THEN 'Extranjero'
              WHEN n.nacionalidad = 3 THEN 'Nacionalizado'
           END as nacionalidad, n.sexo, n.estado_civil, 
           DATE_FORMAT(n.fecnac, '%Y-%m-%d') as fecha_nacimiento, n.lugarnac, p.descrip as profesion, f.descripcion_funcion as funcion, n.estado as situacion,
           DATE_FORMAT(n.fecing, '%Y-%m-%d') as fecha_ingreso, n.forcob as forma_pago, n.seguro_social, c.des_car as cargo,
           ca.descrip as categoria, n.nomposicion_id as posicion, n.tipemp as tipo_contrato,
           DATE_FORMAT(n.inicio_periodo, '%d/%m/%Y') as fecha_inicio, DATE_FORMAT(n.fin_periodo, '%d/%m/%Y') as fecha_fin,
           t.descripcion as turno, n.num_decreto, n.num_decreto_baja, n.telefonos, n.hora_base, n.suesal,
           n1.descrip as region, dep.Descripcion as departamento        
        FROM   nompersonal n
        LEFT JOIN nomprofesiones p ON n.codpro    = p.codorg
        LEFT JOIN nomcargos c      ON n.codcargo  = c.cod_car
        LEFT JOIN nomfuncion f     ON n.nomfuncion_id  = f.nomfuncion_id
        LEFT JOIN nomcategorias ca ON n.codcat    = ca.codorg
        LEFT JOIN nomturnos t      ON n.turno_id  = t.turno_id
        LEFT JOIN nomnivel1 n1     ON n.codnivel1 = n1.codorg 
        LEFT JOIN departamento dep     ON n.IdDepartamento = dep.IdDepartamento
       ".$sql2." ORDER BY codnivel1"; 

  $res = $conexion->query($sql);

  $sql7 = "SELECT nom_emp FROM nomempresa";
  $empresa= $conexion->query($sql7);
  $emp=$empresa->fetch_object();

  $objPHPExcel->getActiveSheet()->setCellValue("H1", $emp->nom_emp);     
  $objPHPExcel->getActiveSheet()->setCellValue("H4", $retitu);     

  $region_fun = "";

  $i=6;
  while($data = $res->fetch_object())
  {
    
    if ($region_fun != $data->region || $i == 6) {
        $region_fun = $data->region;
         $i++;
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "DISTRITO DE ".$data->region);
        $objPHPExcel->getActiveSheet()->getStyle("B$i")->getFont()->setBold(true);
        $i++;
         
        $objPHPExcel->getActiveSheet()->getStyle("A$i:M$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "Ubicacion");
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "Nombre completo");
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "Cedula");
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "Seg soc");
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "Plan");
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, "pos");
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, "Cargo seg estrutura");
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, "Cargo seg Funciones");
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, "Salario");
        $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, "ss");
        $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, "Ini lab");
        $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, "F nac");
        $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, "# Marcar");
        $objPHPExcel->getActiveSheet()->getStyle("A$i:M$i")->getFont()->setBold(true);
        $i++;
    }

    $objPHPExcel->getActiveSheet()->setCellValue("A".$i, $data->departamento);     
    $objPHPExcel->getActiveSheet()->setCellValue("B".$i, $data->nombres." ".$data->apellidos);     
    $objPHPExcel->getActiveSheet()->setCellValue("C".$i, $data->cedula);    
    $objPHPExcel->getActiveSheet()->setCellValue("D".$i, $data->seguro_social);      
    $objPHPExcel->getActiveSheet()->setCellValue("E".$i, $data->planilla);       
    $objPHPExcel->getActiveSheet()->setCellValue("F".$i, $data->posicion);   
    $objPHPExcel->getActiveSheet()->setCellValue("G".$i, $data->cargo);      
    $objPHPExcel->getActiveSheet()->setCellValue("H".$i, $data->funcion);    
    $objPHPExcel->getActiveSheet()->setCellValue("I".$i, $data->suesal); 
    $objPHPExcel->getActiveSheet()->setCellValue("J".$i, $data->gastos_representacion+$data->antiguedad+$data->zona_apartada+$data->jefaturas+$data->especialidad+$data->otros);     
    $objPHPExcel->getActiveSheet()->setCellValue("K".$i, fechas($data->fecha_ingreso,3));    
    $objPHPExcel->getActiveSheet()->setCellValue("L".$i, fechas($data->fecha_nacimiento,3)); 
    $objPHPExcel->getActiveSheet()->setCellValue("M".$i, $data->ficha); 
  // gastos_representacion+antiguedad+zona_apartada+zona_apartada+jefaturas+especialidad+otros

    $i++;
  }

  $letra='A';
  while($letra <= 'Z')
  {
    $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
    $letra++;
  }
  // $objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);
  // $objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setAutoSize(true);
  // $objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

  $objPHPExcel->getActiveSheet()->setSelectedCells('B500');

  $objPHPExcel->setActiveSheetIndex(0);

  

  $filename = 'Empleados_por_region_'.$nivel.'.xlsx';

  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007'); 

  $objWriter->save($filename);

  echo $filename;
}
else
{
  echo "acceso denegado";
}
