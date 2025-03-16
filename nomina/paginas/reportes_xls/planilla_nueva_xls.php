<?php
session_start();
error_reporting(1);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');
include('../../lib/common_excel_pdo.php');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

$conexion = new Conexion($_SESSION["bd_nomina"]);
//Clase Dinámica
Class Planilla{
	var $conexion;
	static public function listadoPlanilla( $conexion, $codnom, $codtip ){
		$sql="SELECT DISTINCT nm.codcon, nm.descrip as descripcion, nm.tipcon 
			  FROM nom_movimientos_nomina nm 
			  WHERE nm.codnom=:codnom AND nm.tipnom=:codtip AND  ((nm.codcon >=100 AND nm.codcon <=202) OR (nm.codcon>=500 AND nm.codcon<=599) 
			  OR (nm.codcon>=3000 AND nm.codcon<=3002)) 
			  ORDER BY  nm.codcon,nm.tipcon";
        $conn = $conexion->conectar();
		$stmt = $conn->prepare($sql);
        $stmt -> bindParam(":codnom" , $codnom , PDO::PARAM_STR);
        $stmt -> bindParam(":codtip" , $codtip , PDO::PARAM_STR);
        $stmt -> execute();
		return $stmt ->fetchAll(PDO::FETCH_ASSOC);


	}
	public function datosEmpresa( $conexion ){
		$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e;";
        $conn = $conexion->conectar();
		$stmt = $conn->prepare($sql);
        $stmt -> execute();
		return $stmt ->fetchAll(PDO::FETCH_ASSOC);

	}
	public function datosPlanilla($conexion, $codnom, $codtip){
		$sql = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta,  ntn.descrip as descrip_planilla
		 FROM nom_nominas_pago np 
		 INNER JOIN nomtipos_nomina ntn on (np.tipnom = ntn.codtip)
		 WHERE  np.codnom= :codnom AND np.tipnom= :codtip;";
        $conn = $conexion->conectar();
		$stmt = $conn->prepare($sql);
        $stmt -> bindParam(":codnom" , $codnom , PDO::PARAM_STR);
        $stmt -> bindParam(":codtip" , $codtip , PDO::PARAM_STR);
        $stmt -> execute();
		$return =  $stmt ->fetchAll(PDO::FETCH_ASSOC);
	}
	public function listadoNivel1($conexion, $codnom, $codtip){
		
		$sql = "SELECT nmn.codnivel1, nv1.descrip as nivel1
			FROM nom_movimientos_nomina nmn
			INNER JOIN nomnivel1 nv1 ON nv1.codorg = nmn.codnivel1
			WHERE  nmn.codnom= :codnom AND nmn.tipnom= :codtip
			GROUP BY nmn.codnivel1
			ORDER BY nmn.codnivel1 ASC;;";
        $conn = $conexion->conectar();
		$stmt = $conn->prepare("$sql");
        $stmt -> bindParam(":codnom" , $codnom , PDO::PARAM_STR);
        $stmt -> bindParam(":codtip" , $codtip , PDO::PARAM_STR);
        $stmt -> execute();
		return $stmt ->fetchAll(PDO::FETCH_ASSOC);
	}
	public function personas_x_nivel($conexion, $nivel1, $codnom, $codtip){
		
		$sql = "SELECT * FROM nompersonal np
		INNER JOIN nom_movimientos_nomina nmn ON np.tipnom = nmn.codtip AND nmn.codnivel1 = np.nom_nivel1
		WHERE nmn.codnom = :codnom AND nmn.codtip = :codtip AND nmn.codnivel1 = :codnivel1 ;";
        $conn = $conexion->conectar();
		$stmt = $conn->prepare("$sql");
        $stmt -> bindParam(":codnom" , $codnom , PDO::PARAM_STR);
        $stmt -> bindParam(":codtip" , $codtip , PDO::PARAM_STR);
        $stmt -> bindParam(":codnivel1" , $nivel1 , PDO::PARAM_STR);
        $stmt -> execute();
		return $stmt ->fetchAll(PDO::FETCH_ASSOC);
	}
}
function renderCabecera( $Hoja )
{

$Hoja->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$Hoja->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$Hoja->getStyle('E9:K9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$Hoja->getStyle('E7:K7')->getFont()->setBold(true);
$Hoja->getStyle('E2')->getFont()->setBold(true);
$Hoja->getStyle('E3')->getFont()->setBold(true);
$Hoja->getStyle('E4')->getFont()->setBold(true);
$Hoja->getStyle('E5')->getFont()->setBold(true);
$Hoja->getStyle('A10:E10')->getFont()->setBold(true);

$Hoja->setCellValue('E5', $fila2['descrip_planilla'])
    ->setCellValue('A10', 'NÚMERO DEL')
    ->setCellValue('G10', 'HORAS')
    ->setCellValue('I10', 'INGRESOS')
    ->setCellValue('K10', 'SEGURO')
    ->setCellValue('L10', 'SEGURO')
    ->setCellValue('M10', 'IMPUESTO')
    ->setCellValue('O10', 'DESCUENTOS')
    ->setCellValue('Q10', 'SUELDO');

$Hoja->setCellValue('E5', $fila2['descrip_planilla'])
    ->setCellValue('A11', 'EMPLEADO')
    ->setCellValue('C11', 'NOMBRE DEL EMPLEADO')
    ->setCellValue('F11', 'TIT.')
    ->setCellValue('G11', 'MONTO')
    ->setCellValue('H11', 'TITULO')
    ->setCellValue('I11', 'MONTO')
    ->setCellValue('K11', 'SOCIAL')
    ->setCellValue('L11', 'EDUCAT.')
    ->setCellValue('M11', 'S/RENTA')
    ->setCellValue('N11', 'I')
    ->setCellValue('O11', 'CÓDIGO')
    ->setCellValue('P11', 'MONTO')
    ->setCellValue('Q11', 'NETO');

	$Hoja->getStyle('A11:Q12')->getFont()->setBold(true);

}
function renderCuerpo( $Hoja, $conexion, $planilla, $codnom, $codtip )
{
	$listadoNivel1 = $planilla->listadoNivel1($conexion, $codnom, $codtip);
	$ixx = 12;
	foreach ($listadoNivel1 as $key => $value) 
	{
		$personal = $planilla -> personas_x_nivel($conexion, $value["codnivel1"], $codnom, $codtip );
		$Hoja->setCellValue('A'.$ixx, "Departamento")
			->setCellValue('C'.$ixx, $value["codnivel1"]." ".$value["nivel1"]);
		$ixx++;
	}

}
function renderTotales()
{

}
$planilla = new Planilla();
$resp_datos_empresa = $planilla->datosEmpresa($conexion);
$datos_empresa = $resp_datos_empresa[0];
$codnom=$_GET['codnom'];
$codtip=$_GET['codtip'];

$res2 = $planilla->datosPlanilla($conexion, $codnom, $codtip);

$fila2=$res2[0];

function allBordersThin(){
	$style = array('borders'=>array(
			'allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_THIN)
	));
	return $style;
}
function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
  if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
  }

require_once '../phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Amaxonia")
							 ->setLastModifiedBy("Amaxonia")
							 ->setTitle("Listado de Planilla")
							 ->setSubject("Office 2007 XLSX Test Document");
$logo=$fila['logo'];

$objDrawing = new PHPExcel_Worksheet_Drawing();

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('E2',  $datos_empresa['empresa'])
    ->setCellValue('E3', 'RUC '.$datos_empresa['rif'].' Telefonos '.$datos_empresa['telefono'])
    ->setCellValue('E4', 'Direccion: '.$datos_empresa['direccion'])
    ->setCellValue('E7', 'RESUMEN DE PLANILLA 1')
    ->setCellValue('E9', 'Desde '. fecha($fila2['desde']).' Hasta '.fecha($fila2['hasta']));

$Hoja = $objPHPExcel->getActiveSheet();

$Hoja->getStyle('E7')->getFont()->setName('Arial');
$Hoja->getStyle('E7')->getFont()->setSize(14);
//$Hoja->mergeCells('C2:D5');
$Hoja->mergeCells('E2:P2');
$Hoja->mergeCells('E3:P3');
$Hoja->mergeCells('E4:P4');
$Hoja->mergeCells('E5:P5');
$Hoja->mergeCells('E7:K7');
$Hoja->mergeCells('E8:K8');
$Hoja->mergeCells('E9:K9');

renderCabecera( $Hoja );
renderCuerpo( $Hoja, $conexion, $planilla , $codnom, $codtip );

$Hoja->setTitle('Reporte');
$Hoja->setSelectedCells('B100');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_Nueva.xls"');
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
exit;






