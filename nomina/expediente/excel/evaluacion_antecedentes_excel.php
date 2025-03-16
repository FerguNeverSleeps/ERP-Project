<?php
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}
include("../../../nomina/lib/common.php");
require_once('../../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
require_once '../../../includes/phpexcel/Classes/PHPExcel.php';

error_reporting(E_ALL);
    function formato_fecha($fecha,$formato)
    {
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
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
    


$ced      = (isset($_GET['cedula'])) ? $_GET['cedula'] : '' ;
$codigo      = (isset($_GET['codigo'])) ? $_GET['codigo'] : '' ;
//------------------------------------------------------------

$sql1   = "SELECT A.* , B.des_car, B.cod_cargo, C.descripcion as departamento "
                . "FROM nompersonal AS A "
                . "LEFT JOIN nomcargos AS B ON A.codcargo = B.cod_car "
                . "LEFT JOIN departamento AS C ON A.IdDepartamento = C.IdDepartamento "
                . "WHERE A.cedula =  '".$ced."'";
        $res1 = $db->query($sql1);
	$persona = $res1->fetch_object();
	
	$nombre_apellido  = strtoupper($persona ->apenom);
	$cedula        = $persona ->cedula;
	$cargo         = strtoupper($persona ->des_car);
	$codcargo      = $persona ->cod_cargo;
	$posicion      = $persona ->nomposicion_id;
	$planilla      = $persona ->tipnom;
	$suesal        = $persona ->suesal;
        $departamento        = $persona ->departamento;
        
        $sql2   = "SELECT a.*,b.des_car,c.nombre FROM expediente a "
                . "LEFT JOIN nomcargos AS b ON a.ascenso_cargo_nuevo = b.cod_car "
                . "LEFT JOIN tipo_ascenso AS c ON a.ascenso_nivel_nuevo = c.id "
                . "WHERE a.cod_expediente_det='".$codigo."'";
        $res2 = $db->query($sql2);
	$expediente = $res2->fetch_object();
        $fecha_resolucion = formato_fecha($expediente ->fecha_resolucion,1);
        $numero_resolucion = $expediente ->numero_resolucion;
        $fecha_inicio = formato_fecha($expediente ->fecha_inicio,1);
        $fecha_inicio_periodo = formato_fecha($expediente ->fecha_inicio_periodo,1);
        $fecha_fin_periodo = formato_fecha($expediente ->fecha_fin_periodo,1);
        $dias_vac = $expediente ->dias_vac;

$date_1 = new DateTime($persona ->fecing);
// Todays date
$date_2 = new DateTime(date('Y-m-d'));

$difference = $date_2->diff( $date_1 );

// Echo the as string to display in browser for testing
$antiguedad=(string)$difference->y;




$date_1 = new DateTime($persona ->cm_fecha_notificacion_ingreso);
// Todays date
$date_2 = new DateTime(date('Y-m-d'));

$difference = $date_2->diff( $date_1 );

// Echo the as string to display in browser for testing
$tiempo_cargo=(string)$difference->y;
  
    

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/evaluacion_antecedentes.xlsx");
if($expediente ->dejado==2)
{
   $objPHPExcel = PHPExcel_IOFactory::load("plantillas/evaluacion_antecedentes_intermedio.xlsx");
}
//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('G3', "Fecha: ".date('d-m-Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('C8', $persona ->apenom);
$objPHPExcel->getActiveSheet()->SetCellValue('C9', $persona ->cedula);
$objPHPExcel->getActiveSheet()->SetCellValue('C5', $persona ->departamento);
$objPHPExcel->getActiveSheet()->SetCellValue('C10', $persona ->nomposicion_id);
$objPHPExcel->getActiveSheet()->SetCellValue('D11', formato_fecha($persona ->fecha_permanencia,1));
$objPHPExcel->getActiveSheet()->SetCellValue('C12', $antiguedad. " Año(s)");
$objPHPExcel->getActiveSheet()->SetCellValue('D13', formato_fecha($persona ->cm_fecha_notificacion_ingreso,1));
$objPHPExcel->getActiveSheet()->SetCellValue('B14', $nivel);
$objPHPExcel->getActiveSheet()->SetCellValue('F14', $persona ->des_car);
$objPHPExcel->getActiveSheet()->SetCellValue('C15', $persona ->suesal);
//$objPHPExcel->getActiveSheet()->SetCellValue('H15', $tiempo_cargo. " Año(s)");

$objPHPExcel->getActiveSheet()->SetCellValue('C17', $expediente ->nombre);
$objPHPExcel->getActiveSheet()->SetCellValue('C18', $expediente ->des_car);
$objPHPExcel->getActiveSheet()->SetCellValue('C19', $expediente ->ascenso_salario_nuevo);
$objPHPExcel->getActiveSheet()->SetCellValue('C22', "2016: ".$expediente ->ascenso_desempenio_1_1);
$objPHPExcel->getActiveSheet()->SetCellValue('D22', "2016: ".$expediente ->ascenso_desempenio_1_2);
$objPHPExcel->getActiveSheet()->SetCellValue('E22', "2017: ".$expediente ->ascenso_desempenio_2_1);
$objPHPExcel->getActiveSheet()->SetCellValue('F22', "2017: ".$expediente ->ascenso_desempenio_2_2);
$objPHPExcel->getActiveSheet()->SetCellValue('C23', "2016: ".$expediente ->ascenso_conducta_1);
$objPHPExcel->getActiveSheet()->SetCellValue('D23', "2017: ".$expediente ->ascenso_conducta_2);
$objPHPExcel->getActiveSheet()->SetCellValue('F23', "2018: ".$expediente ->ascenso_conducta_actual);
$objPHPExcel->getActiveSheet()->SetCellValue('F24', "2018: ".$expediente ->ascenso_participativa_actual);
$objPHPExcel->getActiveSheet()->SetCellValue('H21', $expediente ->ascenso_investigacion);
$objPHPExcel->getActiveSheet()->SetCellValue('H22', $expediente ->ascenso_desempenio_porcentaje);
$objPHPExcel->getActiveSheet()->SetCellValue('H23', $expediente ->ascenso_conducta_porcentaje);
$objPHPExcel->getActiveSheet()->SetCellValue('H24', $expediente ->ascenso_participativa_porcentaje);
$objPHPExcel->getActiveSheet()->SetCellValue('H25', $expediente ->ascenso_puntaje_total);
$objPHPExcel
        ->getActiveSheet()
        ->getStyle('A29')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('32CD32');
if($expediente ->ascenso_puntaje_total<71)
{
    $objPHPExcel
        ->getActiveSheet()
        ->getStyle('A29')
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('FF3333');
}
$objPHPExcel->getActiveSheet()->getStyle("A29")->getFont()->setBold(true)
                                ->setSize(11)
                                ->getColor()->setRGB('FFFFFF');
$objPHPExcel->getActiveSheet()->SetCellValue('A29', $expediente ->descripcion);
if($persona ->foto!='fotos/' && $persona ->foto!='' && $persona ->foto!=NULL){
  if(file_exists("../../../nomina/paginas/".$persona ->foto)){
    $foto = "../../../nomina/paginas/".$persona ->foto;
  }else{
    $foto = "../../../nomina/paginas/fotos/silueta.gif";
    //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
  }
}else{
    //$this->Image("../paginas/fotos/silueta.gif" ,160,30,43);
    $foto = '../../../includes/assets/img/profile/profile_black.jpg';
    //$this->Image('../../includes/assets/img/profile/profile.png',160,30,43);
    //$this->Image("../paginas/fotos/girl2.jpg",160,30,43);
  }
// Provide path to your logo file
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');

$objDrawing->setPath($foto);  //setOffsetY has no effect
$objDrawing->setCoordinates('H1');
$objDrawing->setHeight(220); // logo height
$objDrawing->setWidth(120); // logo height
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

$objPHPExcel->getActiveSheet()->setTitle('Evaluacion Antecedentes');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="evaluacion_antecedentes.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>