<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$db = new Database($_SESSION['bd']);
$db->query("TRUNCATE salarios_netos");
$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("AMAXONIA")
                             ->setLastModifiedBy("AMAXONIA");

//------------------------------------------------------------

$mes = $_GET['mes'] ? $_GET['mes'] : "";
$anio = $_GET['anio'] ? $_GET['anio'] : "";

//--------------------------------------------------------------

$borde_simple = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        )
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
        'size'  => 12,
        'name'  => 'Verdana'
    )
);
$border2 = array(
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
        'size'  => 9,
        'name'  => 'Verdana'
    )
);
$border3 = array(
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
        'size'  => 8,
        'name'  => 'Verdana'
    )
);
$b_total = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 10,
        'name'  => 'Verdana'
    )
);
$negrita = array(
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 9,
        'name'  => 'Verdana'
    )
);

function cellColor($cells,$color)
{
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)));
}
//Fin encabezado tabla
$count=0;

$sql_tipnom = "SELECT * FROM nomtipos_nomina";
$result_tipnom = $db->query($sql_tipnom, $conexion);
//================================LISTADO DE TOTALES===========================================  
function obtener_conceptos($cod)
{
    switch ($cod) {
        case '1': 
        return array('1' => "SUELDO",'2' => "SS",'3' => "SE",'4' => "ISLR",'5' => "FALTAS",'6' => "PREST.",'7' => "ANTICIPOS",'8' => "NETO.",'9' => "EFECTIVO",'10' => "RETARDOS",'11' => "CELULAR",'12' => "OTROS DCTOS",'13' => "ANTICIPOS",'14' => "NETO",'15' => "TOTAL",'16' => "SUELDO",'17' => "SS",'18' => "SE",'19' => "ISRL",'20' => "FALTAS",'21' => "PREST",'22' => "ANTICIPOS",'23' => "NETO",'24' => "EFECTIVO.",'25' => "RETARDOS",'26' => "CELULAR",'27' => "OTROS DCTOS",'28' => "ANTICIPOS",'29' => "NETO",'30' => "");
        case '2':
        return array('1' => "CANTIDAD",'2' => "SUELDO",'3' => "ABONOS",'4' => "TOTAL BRUTO",'5' => "FALTAS",'6' => "AMONESTACIONES",'7' => "SS.",'8' => "SE",'9' => "ANTICIPOS",'10' => "LOGISTICA",'11' => "DAÑOS Y SERV.",'12' => "CELULAR",'13' => "PANTALLA",'14' => "DATA",'15' => "DOTACIÓN",'16' => "SEG. VIDA",'17' => "OTROS DCTOS",'18' => "DESC. DIR.",'19' => "TOTAL DESC.",'20' => "NETO",'21' => "CANT",'22' => "SUELDO",'23' => "ABONOS",'24' => "SUBTOTAL",'25' => "FALTAS",'26' => "SS.",'27' => "ANTICIPOS",'28' => "SE",'29' => "ANTICIPOS",'30' => "LOGISTICA",'31' => "DAÑOS Y SERV.",'32' => "CELULAR",'33' => "PANTALLA",'34' => "DATA",'35' => "DOTACIÓN",'36' => "SEG. VIDA",'37' => "OTROS DCTOS",'38' => "DESC. DIR.",'39' => "SUBTOTAL",'40' => "NETO",'41' => "TOTAL");
        case '3':
        return array('1' => "SUELDO",'2' => "SS",'3' => "SE",'4' => "ISLR",'5' => "FALTAS",'6' => "PREST.",'7' => "VALES",'8' => "NETO",'9' => "SUELDO",'10' => "BONO",'11' => "ALQUILER",'12' => "MANTENIMIENTO",'13' => "TOTAL BRUTO",'14' => "RETARDOS",'15' => "AMONESTACION",'16' => "DOTACION",'17' => "COMBUSTIBLE",'18' => "PANTALLA",'19' => "CELULAR",'20' => "ANTICIPOS",'21' => "OTROS DCTOS",'22' => "NETO",'23' => "TOTAL",'24' => "SUELDO",'25' => "SS",'26' => "SE",'27' => "ISLR",'28' => "FALTAS",'29' => "PREST.",'30' => "VALES",'31' => "NETO",'32' => "SUELDO",'33' => "BONO",'34' => "ALQUILER",'35' => "MANTENIMIENTO",'36' => "TOTAL BRUTO",'37' => "RETARDOS",'38' => "AMONESTACION",'39' => "DOTACION",'40' => "COMBVSTIBLE",'41' => "PANTALLA",'42' => "CELULAR",'43' => "ANTICIPOS",'44' => "OTROS DCTOS",'45' => "NETO",'46' => 'TOTAL','47' => 'SUELDO','48' => 'BONO','49' => 'ALQUILER','50' => 'MANTENIMIENTO','51' => 'TOTAL BRUTO','52' => 'FALTAS','53' => 'AMONESTACION','54' => 'DOTACION','55' => 'CELULAR','56' => 'ANTICIPOS','57' => 'OTROS DCTOS','58' => 'NETO','59' => '','60' => 'SUELDO','61' => 'BONO','62' => 'ALQUILER','63' => 'MANTENIMIENTO','64' => 'TOTAL BRUTO','65' => 'FALTAS','66' => 'AMONESTACION','67' => 'DOTACION','68' => 'CELULAR','69' => 'ANTICIPOS','70' => 'OTROS DCTOS','71' => 'NETO','72' => 'TOTAL');

        case '4':
        return array('1' => "SUELDO",'2' => "SS",'3' => "SE",'4' => "ISLR",'5' => "FALTAS",'6' => "PREST.",'7' => "VALES",'8' => "NETO",'9' => "SUELDO",'10' => "BONO",'11' => "ALQUILER",'12' => "MANTENIMIENTO",'13' => "TOTAL BRUTO",'14' => "RETARDOS",'15' => "AMONESTACION",'16' => "DOTACION",'17' => "COMBUSTIBLE",'18' => "PANTALLA",'19' => "CELULAR",'20' => "ANTICIPOS",'21' => "OTROS DCTOS",'22' => "NETO",'23' => "TOTAL",'24' => "SUELDO",'25' => "SS",'26' => "SE",'27' => "ISLR",'28' => "FALTAS",'29' => "PREST.",'30' => "VALES",'31' => "NETO",'32' => "SUELDO",'33' => "BONO",'34' => "ALQUILER",'35' => "MANTENIMIENTO",'36' => "TOTAL BRUTO",'37' => "RETARDOS",'38' => "AMONESTACION",'39' => "DOTACION",'40' => "COMBUSTIBLE",'41' => "PANTALLA",'42' => "CELULAR",'43' => "ANTICIPOS",'44' => "OTROS DCTOS",'45' => "NETO",'46' => "");

        default:
            # code...
            break;
    }

}    
$array_meses = array("1"=>"Enero","2"=>"Febrero","3"=>"Marzo","4"=>"Abril","5"=>"Mayo","6"=>"Junio","7"=>"Julio","8"=>"Agosto","9"=>"Septiembre","10"=>"Octubre","11"=>"Noviembre","12"=>"Diciembre");
//================================LISTADO DE NIVELES===========================================   
function obtener_niveles()
{
    $db = new Database($_SESSION['bd']);
    $sql = "SELECT * from parametros where codigo = '1'";
    $result = $db->query($sql);
    $data = array();
    while($fila = $result->fetch_assoc()){
        $data[]=$fila;
    }
    return $data;
}
$indice_nivel=0;
$titulos = array('1'=> "ADMINIST",'2'=> "AGENTES",'3'=> "SUPERVIS",'4'=> "ADMINIST 1",'5'=> "TOTALES");
while($row_tip = mysqli_fetch_array($result_tipnom))
{
    
    $rowCount = 3;
    $codtip = $row_tip['codtip'];
    $nomtip = explode(" ", $row_tip['descrip']);

    //Encabezado de la tabla
    //================================OPCIONES DE LA TABLA===========================================
    $objPHPExcel->createSheet($count);
    $objPHPExcel->setActiveSheetIndex($count);
    $objPHPExcel->getActiveSheet()->setTitle(substr($titulos[$codtip],0,8));
    //titulo de la tabla
    $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Reporte Salario Neto");
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);

    $letra = "B"; 
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
    $nivel = 0;
    
    //================================LISTADOD DE COLABORADORES===========================================   
    $sql_personal= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
    SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
    CASE WHEN np.apellidos LIKE 'De %' THEN 
            SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
    ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
    END as primer_apellido, nv1.descrip as nivel1, np.codnivel1,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151 ) AND nnp.frecuencia=2),0) as salario1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151 ) AND nnp.frecuencia=3),0) as salario2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=200 AND nnp.frecuencia=2),0) as seg_soc1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=200 AND nnp.frecuencia=3),0) as seg_soc2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=201 AND nnp.frecuencia=2),0) as seg_educ1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=201 AND nnp.frecuencia=3),0) as seg_educ2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=202 AND nnp.frecuencia=2),0) as islr1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon=202 AND nnp.frecuencia=3),0) as islr2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (194, 195,196,  198, 199 ) AND nnp.frecuencia=2),0) as faltas1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (194, 195,196,  198, 199 ) AND nnp.frecuencia=3),0) as faltas2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (197 ) AND nnp.frecuencia=2),0) as anticipos1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (197 ) AND nnp.frecuencia=3),0) as anticipos2, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND (n.codcon>=500 AND n.codcon<=599) AND nnp.frecuencia=2),0) as prest1, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND (n.codcon>=500 AND n.codcon<=599) AND nnp.frecuencia=3),0) as prest2,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (300, 310, 315, 316, 321, 322, 323, 324  ) AND nnp.frecuencia=2),0) as salario31, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (300, 310, 315, 316, 321, 322, 323, 324  ) AND nnp.frecuencia=3),0) as salario32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (317 ,320 ) AND nnp.frecuencia=2),0) as bono31, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (317 ,320 ) AND nnp.frecuencia=3),0) as bono32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (319) AND nnp.frecuencia=2),0) as alquiler31, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (319) AND nnp.frecuencia=3),0) as alquiler32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (318) AND nnp.frecuencia=2),0) as mantenimiento31, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (318) AND nnp.frecuencia=3),0) as mantenimiento32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (340,341,342, 343, 344, 346, 347, 348) AND nnp.frecuencia=2),0) as retardos31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (340,341,342, 343, 344, 346, 347, 348) AND nnp.frecuencia=3),0) as retardos32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (345) AND nnp.frecuencia=2),0) as combustible31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (345) AND nnp.frecuencia=3),0) as combustible32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (382) AND nnp.frecuencia=2),0) as celular31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (382) AND nnp.frecuencia=3),0) as celular32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (381,398) AND nnp.frecuencia=2),0) as anticipos31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (381,398) AND nnp.frecuencia=3),0) as anticipos32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (386) AND nnp.frecuencia=2),0) as dotacion31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (386) AND nnp.frecuencia=3),0) as dotacion32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (383) AND nnp.frecuencia=2),0) as pantalla31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (383) AND nnp.frecuencia=3),0) as pantalla32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (384, 385, 387, 397, 399) AND nnp.frecuencia=2),0) as amonestacion31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (384, 385, 387, 397, 399) AND nnp.frecuencia=3),0) as amonestacion32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (384, 385, 387, 397, 399) AND nnp.frecuencia=2),0) as dctoss31,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (384, 385, 387, 397, 399) AND nnp.frecuencia=3),0) as dctoss32,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 221, 222 ) AND nnp.frecuencia=2),0) as salario21, 
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 221, 222 ) AND nnp.frecuencia=3),0) as salario22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (240, 241, 242, 244, 245) AND nnp.frecuencia=2),0) as faltas21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (240, 241, 242, 244, 245) AND nnp.frecuencia=3),0) as faltas22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (243, 248) AND nnp.frecuencia=2),0) as amonestacion21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (243, 248) AND nnp.frecuencia=3),0) as amonestacion22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (286) AND nnp.frecuencia=2),0) as dotacion21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (286) AND nnp.frecuencia=3),0) as dotacion22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (280,289) AND nnp.frecuencia=2),0) as celular21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (280,289) AND nnp.frecuencia=3),0) as celular22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (298, 281) AND nnp.frecuencia=2),0) as anticipos21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (298, 281) AND nnp.frecuencia=3),0) as anticipos22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (220, 217) AND nnp.frecuencia=2),0) as bono21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (220, 217) AND nnp.frecuencia=3),0) as bono22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (219) AND nnp.frecuencia=2),0) as alquiler21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (219) AND nnp.frecuencia=3),0) as alquiler22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (218) AND nnp.frecuencia=2),0) as mantenimiento21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (218) AND nnp.frecuencia=3),0) as mantenimiento22,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (282, 283, 284, 285, 287, 297, 299) AND nnp.frecuencia=2),0) as dctoss21,
        COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
        INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
        WHERE n.ficha=np.ficha AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio}  AND n.codcon in (282, 283, 284, 285, 287, 297, 299) AND nnp.frecuencia=3),0) as dctoss22
     FROM nompersonal np
        INNER JOIN  nomnivel1 nv1 on (nv1.codorg = np.codnivel1)
    WHERE np.tipnom='$codtip'
    GROUP BY np.ficha 
    ORDER BY  np.codnivel1 asc, np.ficha";
    $result_personal = $db->query($sql_personal);
    $sw=$nivel=$nivel_ant=0;
    $total = $result_personal->num_rows;
    $cant=0;
    $total_cell="";
    $ixx=0;
    $bandera = 0;
    while($row_personal = mysqli_fetch_array($result_personal))
    {
        $nivel = $row_personal['codnivel1'];
        $nombre_nivel = $row_personal['nivel1'];
        $conceptos = obtener_conceptos($codtip);
        /* TOTALES */
        if($nivel_ant!=$nivel && $nivel_ant>0)
        {
            $conex = new Database($_SESSION[bd]);
            if($codtip == 1)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AG'.$rowCount)->applyFromArray($b_total);
            }
            if($codtip == 2)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTAL');
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AQ'.$rowCount)->applyFromArray($b_total);
            }
            if($codtip == 3 )
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':BW'.$rowCount)->applyFromArray($b_total);
            }
            if($codtip == 4)
            {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AW'.$rowCount)->applyFromArray($b_total);
            }            
            $letra = "D";
            $final = ($rowCount-1);
            if($codtip==1 OR $codtip == 3 OR $codtip == 4)
            {
                for ($i=1; $i <= count($conceptos) ; $i++) 
                {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
                    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, "=SUM(".$letra.$inicio.":".$letra.$final.")");
                    $cell = $objPHPExcel->getActiveSheet()->getCell($letra.$rowCount);
                    $total_cell[$i].=$letra.$rowCount.";";
                    $celda =$letra.$rowCount;
                    $ixx++;
                    $cellValue = $cell->getCalculatedValue();
                    $total_nivel[$indice_nivel][$conceptos[$i]]=$cellValue;
                    $letra++;
                }
                if ($codtip == 1)
                {
                    $conex = new Database($_SESSION[bd]);
                    $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','1','{$codtip}','D".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','8','{$codtip}','K".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("L".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','9','{$codtip}','L".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("Q".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','14','{$codtip}','Q".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("S".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','16','{$codtip}','S".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("Z".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','23','{$codtip}','Z".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','24','{$codtip}','AA".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AF".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','29','{$codtip}','AF".$rowCount."'),";
                    $insert .= '****';
                    $insert = str_replace(',****', ';', $insert);
                    $conex->query($insert);                    
                }
                if ($codtip == 3)
                {
                    $conex = new Database($_SESSION[bd]);
                    $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','1','3','D".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','8','3','K".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("P".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','13','3','P".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("Y".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','22','3','Y".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','24','3','AA".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AH".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','31','3','AH".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AM".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','36','3','AM".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AV".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','45','3','AV".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("BB".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','51','3','BB".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("BI".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','58','3','BI".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("BO".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','64','3','BO".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("BV".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."-','".$cellValue."','71','3','BV".$rowCount."'),";
                    /*$cell = $objPHPExcel->getActiveSheet()->getCell("BU".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel."','".$row_personal['nivel1']."','".$cellValue."','23','3','BU".$rowCount."'),"*/
                    $insert .= '****';
                    $insert = str_replace(',****', ';', $insert);
                    $conex->query($insert);
                }
                if ($codtip == 4)
                {
                    $conex = new Database($_SESSION[bd]);
                    $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','1','{$codtip}','D".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','8','{$codtip}','K".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("L".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','9','{$codtip}','L".$rowCount."'),"; 
                    $cell = $objPHPExcel->getActiveSheet()->getCell("P".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','13','{$codtip}','P".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("Y".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','22','{$codtip}','Y".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
                    $cellValue = $cell->getCalculatedValue();
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','24','{$codtip}','AA".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AH".$rowCount);
                    $cellValue = $cell->getCalculatedValue();  
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','31','{$codtip}','AH".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AM".$rowCount);
                    $cellValue = $cell->getCalculatedValue();  
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','36','{$codtip}','AM".$rowCount."'),";
                    $cell = $objPHPExcel->getActiveSheet()->getCell("AV".$rowCount);
                    $cellValue = $cell->getCalculatedValue();  
                    $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','45','{$codtip}','AV".$rowCount."'),";  
                    $insert .= '****';
                    $insert = str_replace(',****', ';', $insert);
                    $conex->query($insert);
                }
            }
            $rowCount++;
            $indice_nivel++;
        }
        
        if($nivel_ant!=$nivel)
        {
            $nivel_ant = $nivel;
            $sw=1;
        }
        else{
            $sw = 0;
        }
        
        if($sw ==1){
            $rowCount++;
            if($codtip == 1)
            {
                if ($bandera == 0) {
                    $bandera =1;
                    $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':R'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "PRIMERA QUINCENA");
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':R'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->mergeCells('S'.$rowCount.':AG'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, "SEGUNDA QUINCENA");
                    $objPHPExcel->getActiveSheet()->getStyle('S'.$rowCount.':AG'.$rowCount)->applyFromArray($borders);                
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':K'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "NOMINA I");
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':K'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->mergeCells('L'.$rowCount.':Q'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "NOMINA III");
                    $objPHPExcel->getActiveSheet()->getStyle('L'.$rowCount.':Q'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, "TOTAL");
                    $objPHPExcel->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->mergeCells('S'.$rowCount.':Z'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, "NOMINA I");
                    $objPHPExcel->getActiveSheet()->getStyle('S'.$rowCount.':Z'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->mergeCells('AA'.$rowCount.':AF'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, "NOMINA III");
                    $objPHPExcel->getActiveSheet()->getStyle('AA'.$rowCount.':AF'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, "TOTAL");
                    $objPHPExcel->getActiveSheet()->getStyle('AG'.$rowCount)->applyFromArray($border2);
                    $rowCount++;
                    //Se hace la cabecera de la planilla
                    $letra = "D";
                    for ($i=1; $i <= count($conceptos) ; $i++) 
                    {
                        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
                        $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $conceptos[$i]);
                        $letra++;
                    }
                }
                $objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':C'.$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre_nivel);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AG'.$rowCount)->applyFromArray($border3);
                $rowCount++;

            }
            if($codtip == 3 OR $codtip ==4)
            {
                if ($bandera == 0) 
                {
                    $bandera =1;
                    $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':Z'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "PRIMERA QUINCENA");
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':Z'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->mergeCells('AA'.$rowCount.':AW'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, "SEGUNDA QUINCENA");
                    $objPHPExcel->getActiveSheet()->getStyle('AA'.$rowCount.':AW'.$rowCount)->applyFromArray($borders);
                    if($codtip==3)
                    {
                    $objPHPExcel->getActiveSheet()->mergeCells('AX'.$rowCount.':BI'.$rowCount);
                        $objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowCount, "PRIMERA QUINCENA");
                        $objPHPExcel->getActiveSheet()->getStyle('AX'.$rowCount.':BI'.$rowCount)->applyFromArray($borders);
                        $objPHPExcel->getActiveSheet()->mergeCells('BK'.$rowCount.':BW'.$rowCount);
                        $objPHPExcel->getActiveSheet()->SetCellValue('BK'.$rowCount, "SEGUNDA QUINCENA");
                        $objPHPExcel->getActiveSheet()->getStyle('BK'.$rowCount.':BW'.$rowCount)->applyFromArray($borders);

                    }
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':K'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "NOMINA I");
                    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':K'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->mergeCells('L'.$rowCount.':Y'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "NOMINA III");
                    $objPHPExcel->getActiveSheet()->getStyle('L'.$rowCount.':Y'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, "TOTAL");
                    $objPHPExcel->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($border2);
                    $objPHPExcel->getActiveSheet()->mergeCells('AA'.$rowCount.':AH'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, "NOMINA I");
                    $objPHPExcel->getActiveSheet()->getStyle('AA'.$rowCount.':AH'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->mergeCells('AI'.$rowCount.':AU'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, "NOMINA III");
                    $objPHPExcel->getActiveSheet()->getStyle('AI'.$rowCount.':AU'.$rowCount)->applyFromArray($borders);
                    $objPHPExcel->getActiveSheet()->mergeCells('AW'.$rowCount.':AW'.$rowCount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('AW'.$rowCount, "TOTAL");
                    $objPHPExcel->getActiveSheet()->getStyle('AW'.$rowCount.':AW'.$rowCount)->applyFromArray($border2);
                    if($codtip == 3)
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells('AX'.$rowCount.':BI'.$rowCount);
                        $objPHPExcel->getActiveSheet()->SetCellValue('AX'.$rowCount, "NOMINA II");
                        $objPHPExcel->getActiveSheet()->getStyle('AX'.$rowCount.':BI'.$rowCount)->applyFromArray($border2);
                        $objPHPExcel->getActiveSheet()->mergeCells('BJ'.$rowCount.':BJ'.$rowCount);
                        $objPHPExcel->getActiveSheet()->SetCellValue('BJ'.$rowCount, "TOTAL");
                        $objPHPExcel->getActiveSheet()->getStyle('BJ'.$rowCount)->applyFromArray($border2);
                        $objPHPExcel->getActiveSheet()->mergeCells('BK'.$rowCount.':BW'.$rowCount);
                        $objPHPExcel->getActiveSheet()->SetCellValue('BK'.$rowCount, "NOMINA II");
                        $objPHPExcel->getActiveSheet()->getStyle('BK'.$rowCount.':BW'.$rowCount)->applyFromArray($border2);
                        $objPHPExcel->getActiveSheet()->SetCellValue('BW'.$rowCount, "TOTAL");
                        $objPHPExcel->getActiveSheet()->getStyle('BW'.$rowCount)->applyFromArray($border2);
                    }
                    $rowCount++;
                    //Se hace la cabecera de la planilla
                    $letra = "D";
                    for ($i=1; $i <= count($conceptos) ; $i++) 
                    {
                        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
                        $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $conceptos[$i]);
                        $letra++;
                        if($codtip == 4 AND $i > 46)
                        {
                            break;
                        }
                    }
                }
                //$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':C'.$rowCount);
                if ($codtip == 3) {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre_nivel);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':BW'.$rowCount)->applyFromArray($border3);
                } elseif($codtip == 4) {
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre_nivel);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AW'.$rowCount)->applyFromArray($border3);
                }
                
                $rowCount++;
            }
            if($codtip == 2)
            {
                $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':V'.$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "PRIMERA QUINCENA");
                $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':V'.$rowCount)->applyFromArray($borders);
                $objPHPExcel->getActiveSheet()->mergeCells('W'.$rowCount.':AQ'.$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, "SEGUNDA QUINCENA");
                $objPHPExcel->getActiveSheet()->getStyle('W'.$rowCount.':AQ'.$rowCount)->applyFromArray($borders);
                $rowCount++;
                //Se hace la cabecera de la planilla
                $letra = "C";
                for ($i=1; $i <= count($conceptos) ; $i++) 
                {
                    $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
                    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $conceptos[$i]);
                    $letra++;
                }
                //$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':C'.$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $nombre_nivel);
                $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AQ'.$rowCount)->applyFromArray($border3);
                $rowCount++;
            }
            $indice_nivel++;
            $inicio = $rowCount;

            $sw=1;
        }
        $lista = $rowCount;
        /* */
        //-------------------PRIMERA QUINCENA---------------------
        $total_sueldo1=$total_ss1=$total_se1=$total_prest1=$total_efect1=$neto_planilla11=$neto_planilla31=$total_prim_quinc = 0;    
        $total_sueldo2=$total_ss2=$total_se2=$total_prest2=$total_efect2=$neto_planilla12=$neto_planilla32=$total_prim_quinc = 0;    
        if($codtip == 1)
        {
            $ficha = $row_personal['ficha']; 
            $salario1 = $row_personal['salario1'];
            $seg_soc1 = $row_personal['seg_soc1'];
            $seg_educ1 = $row_personal['seg_educ1'];
            $islr1 = $row_personal['islr1'];
            $faltas1 = $row_personal['faltas1'];
            $prest1 = $row_personal['prest1'];
            $anticipos1 = $row_personal['anticipos1'];
            $Efectivo1 = $row_personal['salario31'];
            $descto31 = $row_personal['dctoss31'];
            $anticipos31 = $row_personal['anticipos31'];
            $retardo31 = $row_personal['retardos31'];
            $celular31 = $row_personal['celular31'];
            $neto_planilla1 = $salario1 - ($seg_soc1+$seg_educ1+$faltas1+$prest1);
            $neto_planilla3 = $Efectivo1;
            $neto_prim_quinc = $neto_planilla1+$neto_planilla3;
            $letra = "B";
            $objPHPExcel->getActiveSheet()->SetCellValue($letra.$lista, $row_personal['ficha']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->SetCellValue("C".$lista, utf8_decode($row_personal['nombre']));
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    
            $objPHPExcel->getActiveSheet()->SetCellValue("D".$rowCount, $salario1);
            $objPHPExcel->getActiveSheet()->SetCellValue("E".$rowCount, $seg_soc1);
            $objPHPExcel->getActiveSheet()->SetCellValue("F".$rowCount, $seg_educ1);
            $objPHPExcel->getActiveSheet()->SetCellValue("G".$rowCount, $islr1);
            $objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount, $faltas1);
            $objPHPExcel->getActiveSheet()->SetCellValue("I".$rowCount, $prest1);
            $objPHPExcel->getActiveSheet()->SetCellValue("J".$rowCount, $anticipos1);
            $objPHPExcel->getActiveSheet()->SetCellValue("K".$rowCount, "=D".$rowCount."-E".$rowCount."-F".$rowCount."-G".$rowCount."-H".$rowCount."-I".$rowCount."-J".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("L".$rowCount, $Efectivo1);
            $objPHPExcel->getActiveSheet()->SetCellValue("M".$rowCount, $anticipos31);
            $objPHPExcel->getActiveSheet()->SetCellValue("N".$rowCount, $celular31);
            $objPHPExcel->getActiveSheet()->SetCellValue("O".$rowCount, $descto31);
            $objPHPExcel->getActiveSheet()->SetCellValue("P".$rowCount, $retardo31);
            $objPHPExcel->getActiveSheet()->SetCellValue("Q".$rowCount, "=L".$rowCount."-M".$rowCount."-N".$rowCount."-O".$rowCount."-P".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("R".$rowCount, "=K".$rowCount."+Q".$rowCount);
    
            //-------------------SEGUNDA QUINCENA---------------------   
            $ficha = $row_personal['ficha']; 
            $salario2 = $row_personal['salario2'];
            $seg_soc2 = $row_personal['seg_soc2'];
            $seg_educ2 = $row_personal['seg_educ2'];
            $islr2 = $row_personal['islr2'];
            $faltas2 = $row_personal['faltas2'];
            $prest2 = $row_personal['prest2'];
            $Efectivo2 = $row_personal['salario32'];
            $anticipos32 = $row_personal['anticipos32'];            

            $descto32 = $row_personal['dctoss32'];
            $retardo32 = $row_personal['retardos32'];
            $celular32 = $row_personal['celular32'];
            $neto_planilla1_quinc2 = $salario2 - ($seg_soc2+$seg_educ2+$prest2);
            $neto_planilla3_quinc2 = $Efectivo2;
            $neto_seg_quinc = $neto_planilla1_quinc2+$neto_planilla3_quinc2;

            $objPHPExcel->getActiveSheet()->SetCellValue("S".$rowCount, $salario2);
            $objPHPExcel->getActiveSheet()->SetCellValue("T".$rowCount, $seg_soc2);
            $objPHPExcel->getActiveSheet()->SetCellValue("U".$rowCount, $seg_educ2);
            $objPHPExcel->getActiveSheet()->SetCellValue("V".$rowCount, $islr2);
            $objPHPExcel->getActiveSheet()->SetCellValue("W".$rowCount, $faltas2);
            $objPHPExcel->getActiveSheet()->SetCellValue("X".$rowCount, $prest2);
            $objPHPExcel->getActiveSheet()->SetCellValue("Y".$rowCount, $anticipos2);
            $objPHPExcel->getActiveSheet()->SetCellValue("Z".$rowCount, "=S".$rowCount."-Y".$rowCount."-T".$rowCount."-U".$rowCount."-V".$rowCount."-W".$rowCount."-X".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AA".$rowCount, $Efectivo2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AB".$rowCount, $retardo32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AC".$rowCount, $descto32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AD".$rowCount, $celular32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AE".$rowCount, $anticipos32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AF".$rowCount, "=AA".$rowCount."-AB".$rowCount."-AC".$rowCount."-AD".$rowCount."-AE".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AG".$rowCount, "=Z".$rowCount."+AF".$rowCount);
            $rowCount++;
        }        
        if($codtip == 3 OR $codtip == 4)
        {
            $ficha = $row_personal['ficha']; 
            $salario1 = $row_personal['salario1'];
            $seg_soc1 = $row_personal['seg_soc1'];
            $seg_educ1 = $row_personal['seg_educ1'];
            $islr1 = $row_personal['islr1'];
            $faltas1 = $row_personal['faltas1'];
            $prest1 = $row_personal['prest1'];
            $anticipos1 = $row_personal['anticipos1'];
            $Efectivo1 = $row_personal['salario31'];
            $descto31 = $row_personal['dctoss31'];
            $retardo31 = $row_personal['retardos31'];
            $bono31 = $row_personal['bono31'];
            $alquiler31 = $row_personal['alquiler31'];
            $combustible31 = $row_personal['combustible31'];
            $anticipos31 = $row_personal['anticipos31'];
            $celular31 = $row_personal['celular31'];
            $dotacion31 = $row_personal['dotacion31'];
            $mantenimiento31 = $row_personal['mantenimiento31'];
            $amonestacion31 = $row_personal['amonestacion31'];
            $pantalla31 = $row_personal['pantalla31'];
            $neto_planilla1 = $salario1 - ($seg_soc1+$seg_educ1+$faltas1+$prest1);
            $neto_planilla3 = $Efectivo1;
            $neto_prim_quinc = $neto_planilla1+$neto_planilla3;
            $letra = "B";
            $objPHPExcel->getActiveSheet()->SetCellValue($letra.$lista, $row_personal['ficha']);
            $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
            $objPHPExcel->getActiveSheet()->SetCellValue("C".$lista, utf8_decode($row_personal['nombre']));
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    
            $objPHPExcel->getActiveSheet()->SetCellValue("D".$rowCount, $salario1);
            $objPHPExcel->getActiveSheet()->SetCellValue("E".$rowCount, $seg_soc1);
            $objPHPExcel->getActiveSheet()->SetCellValue("F".$rowCount, $seg_educ1);
            $objPHPExcel->getActiveSheet()->SetCellValue("G".$rowCount, $islr1);
            $objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount, $faltas1);
            $objPHPExcel->getActiveSheet()->SetCellValue("I".$rowCount, $prest1);
            $objPHPExcel->getActiveSheet()->SetCellValue("J".$rowCount, $anticipos1);
            $objPHPExcel->getActiveSheet()->SetCellValue("K".$rowCount, "=D".$rowCount."-E".$rowCount."-F".$rowCount."-G".$rowCount."-H".$rowCount."-I".$rowCount."-J".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("L".$rowCount, $Efectivo1);
            $objPHPExcel->getActiveSheet()->SetCellValue("M".$rowCount, $bono31);
            $objPHPExcel->getActiveSheet()->SetCellValue("N".$rowCount, $alquiler31);
            $objPHPExcel->getActiveSheet()->SetCellValue("O".$rowCount, $mantenimiento31);
            $objPHPExcel->getActiveSheet()->SetCellValue("P".$rowCount, "=L".$rowCount."+M".$rowCount."+N".$rowCount."+O".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("Q".$rowCount, $retardo31);
            $objPHPExcel->getActiveSheet()->SetCellValue("R".$rowCount, $amonestacion31);
            $objPHPExcel->getActiveSheet()->SetCellValue("S".$rowCount, $dotacion31);
            $objPHPExcel->getActiveSheet()->SetCellValue("T".$rowCount, $combustible31);
            $objPHPExcel->getActiveSheet()->SetCellValue("U".$rowCount, $pantalla31);
            $objPHPExcel->getActiveSheet()->SetCellValue("V".$rowCount, $celular31);
            $objPHPExcel->getActiveSheet()->SetCellValue("W".$rowCount, $anticipos31);
            $objPHPExcel->getActiveSheet()->SetCellValue("X".$rowCount, $descto31);
            $objPHPExcel->getActiveSheet()->SetCellValue("Y".$rowCount, "=P".$rowCount."-Q".$rowCount."-R".$rowCount."-S".$rowCount."-T".$rowCount."-U".$rowCount."-V".$rowCount."-W".$rowCount."-X".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("Z".$rowCount, "=K".$rowCount."+Y".$rowCount);
    
            //-------------------SEGUNDA QUINCENA---------------------    
            $ficha = $row_personal['ficha']; 
            $salario2 = $row_personal['salario2'];
            $seg_soc2 = $row_personal['seg_soc2'];
            $seg_educ2 = $row_personal['seg_educ2'];
            $islr2 = $row_personal['islr2'];
            $faltas2 = $row_personal['faltas2'];
            $prest2 = $row_personal['prest2'];
            $Efectivo2 = $row_personal['salario32'];
            $anticipos32 = $row_personal['anticipos32'];
            $celular32 = $row_personal['celular32'];
            $descto32 = $row_personal['dctoss32'];
            $retardo32 = $row_personal['retardos32'];
            $bono32 = $row_personal['bono32'];
            $alquiler32 = $row_personal['alquiler32'];
            $combustible32 = $row_personal['combustible32'];
            $anticipos32 = $row_personal['anticipos32'];
            $celular32 = $row_personal['celular32'];
            $dotacion32 = $row_personal['dotacion32'];
            $mantenimiento32 = $row_personal['mantenimiento32'];
            $amonestacion32 = $row_personal['amonestacion32'];
            $pantalla32 = $row_personal['pantalla32'];
            $neto_planilla1_quinc2 = $salario2 - ($seg_soc2+$seg_educ2+$prest2);
            $neto_planilla3_quinc2 = $Efectivo2;
            $neto_seg_quinc = $neto_planilla1_quinc2+$neto_planilla3_quinc2;

            $objPHPExcel->getActiveSheet()->SetCellValue("AA".$rowCount, $salario2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AB".$rowCount, $seg_soc2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AC".$rowCount, $seg_educ2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AD".$rowCount, $islr2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AE".$rowCount, $faltas2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AF".$rowCount, $prest2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AG".$rowCount, $anticipos32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AH".$rowCount, "=AA".$rowCount."-AB".$rowCount."-AC".$rowCount."-AD".$rowCount."-AE".$rowCount."-AF".$rowCount."-AG".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AI".$rowCount, $Efectivo2);
            $objPHPExcel->getActiveSheet()->SetCellValue("AJ".$rowCount, $bono32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AK".$rowCount, $alquiler32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AL".$rowCount, $mantenimiento32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AM".$rowCount, "=AI".$rowCount."+AJ".$rowCount."+AK".$rowCount."+AL".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AN".$rowCount, $dotacion32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AO".$rowCount, $amonestacion32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AP".$rowCount, $dotacion32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AQ".$rowCount, $combustible32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AR".$rowCount, $pantalla32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AS".$rowCount, $celular32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AT".$rowCount, $anticipos32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AU".$rowCount, $descto32);
            $objPHPExcel->getActiveSheet()->SetCellValue("AV".$rowCount, "=AM".$rowCount."-AN".$rowCount."-AO".$rowCount."-AP".$rowCount."-AQ".$rowCount."-AR".$rowCount."-AS".$rowCount."-AT".$rowCount."-AU".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AW".$rowCount, "=AV".$rowCount."+Z".$rowCount);
            if($codtip == 3)
            {
                //-------------------NOMINA II---------------------    
                //-------------------PRIMERA QUINCENA---------------------    
                $ficha = $row_personal['ficha']; 
                $salario21 = $row_personal['salario21'];
                $salario22 = $row_personal['salario22'];
                $bono21 = $row_personal['bono21'];
                $bono22 = $row_personal['bono22'];
                $alquiler21 = $row_personal['alquiler21'];
                $alquiler22 = $row_personal['alquiler22'];
                $mantenimiento21 = $row_personal['mantenimiento21'];
                $mantenimiento22 = $row_personal['mantenimiento22'];
                $faltas21 = $row_personal['faltas21'];
                $faltas22 = $row_personal['faltas22'];
                $amonestacion21 = $row_personal['amonestacion21'];
                $amonestacion22 = $row_personal['amonestacion22'];
                $dotacion21 = $row_personal['dotacion21'];
                $dotacion22 = $row_personal['dotacion22'];
                $celular21 = $row_personal['celular21'];
                $celular22 = $row_personal['celular22'];
                $anticipos21 = $row_personal['anticipos21'];
                $anticipos22 = $row_personal['anticipos22'];
                $dctoss21 = $row_personal['dctoss21'];
                $dctoss22 = $row_personal['dctoss22'];
                $neto_planilla1_quinc2 = $salario2 - ($seg_soc2+$seg_educ2+$prest2);
                $neto_planilla3_quinc2 = $Efectivo2;
                $neto_seg_quinc = $neto_planilla1_quinc2+$neto_planilla3_quinc2;
    
                $objPHPExcel->getActiveSheet()->SetCellValue("AX".$rowCount, $salario21);
                $objPHPExcel->getActiveSheet()->SetCellValue("AY".$rowCount, $bono21);
                $objPHPExcel->getActiveSheet()->SetCellValue("AZ".$rowCount, $alquiler21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BA".$rowCount, $mantenimiento21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BB".$rowCount, "=AX".$rowCount."+AY".$rowCount."+AZ".$rowCount."+BA".$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue("BC".$rowCount, $faltas21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BD".$rowCount, $amonestacion21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BE".$rowCount, $dotacion21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BF".$rowCount, $celular21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BG".$rowCount, $anticipos21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BH".$rowCount, $dctoss21);
                $objPHPExcel->getActiveSheet()->SetCellValue("BI".$rowCount, "=BB".$rowCount."-BC".$rowCount."-BD".$rowCount."-BE".$rowCount."-BF".$rowCount."-BG".$rowCount."-BH".$rowCount);
    
                $objPHPExcel->getActiveSheet()->SetCellValue("BJ".$rowCount, "=BI".$rowCount."+Z".$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue("BK".$rowCount, $salario22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BL".$rowCount, $bono22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BM".$rowCount, $alquiler22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BN".$rowCount, $mantenimiento22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BO".$rowCount, "=BK".$rowCount."+BL".$rowCount."+BM".$rowCount."+BN".$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue("BP".$rowCount, $faltas22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BQ".$rowCount, $amonestacion22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BR".$rowCount, $dotacion22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BS".$rowCount, $celular22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BT".$rowCount, $anticipos22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BU".$rowCount, $dctoss22);
                $objPHPExcel->getActiveSheet()->SetCellValue("BV".$rowCount, "=BO".$rowCount."-BP".$rowCount."-BQ".$rowCount."-BR".$rowCount."-BS".$rowCount."-BT".$rowCount."-BU".$rowCount);
                $objPHPExcel->getActiveSheet()->SetCellValue("BW".$rowCount, "=BV".$rowCount."+BJ".$rowCount);
            }
            $rowCount++;
        }
        $cant++;
    }

    
    if(($codtip == 2) OR ($codtip == 5))
    {
        $conceptos = obtener_conceptos($codtip);
        $nominas_agentes = array('1' => "AGENTES I", '2' => "AGENTES II", '3' => "AGENTES III");
        $letra = "B";
        $tipo_planilla = [2,5];
        for ($i=1; $i <= 3; $i++) { 
            if ($i==1) {
               $sql_agentes = "SELECT 
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip in ({$tipo_planilla}) AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as salario".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=2),0) as Bonos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (194, 195, 196, 198, 199) AND nnp.frecuencia=2),0) as Faltas".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as amonestaciones".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as danios".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as celular".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as pantalla".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as data".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (200) AND nnp.frecuencia=2),0) as seg_soc".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (201) AND nnp.frecuencia=2),0) as seg_educ".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (197) AND nnp.frecuencia=2),0) as anticipos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=2),0) as logistica".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=2),0) as dotacion".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=2),0) as otros_descto".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND (n.codcon>=500 AND n.codcon<=599) AND nnp.frecuencia=2),0) as descto_dir".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=2),0) as seg_vida".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151) 
                AND n.tipcon='A' AND nnp.frecuencia=3),0) as salario".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=3),0) as Bonos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (194, 195, 196, 198, 199) AND nnp.frecuencia=3),0) as Faltas".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=3),0) as amonestaciones".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=3),0) as danios".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=3),0) as celular".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=3),0) as pantalla".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=3),0) as data".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (200) AND nnp.frecuencia=3),0) as seg_soc".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (201) AND nnp.frecuencia=3),0) as seg_educ".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (197) AND nnp.frecuencia=3),0) as anticipos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=3),0) as logistica".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=3),0) as dotacion".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=3),0) as otros_descto".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND (n.codcon>=500 AND n.codcon<=599) AND nnp.frecuencia=3),0) as descto_dir".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100000) AND nnp.frecuencia=3),0) as seg_vida".$i."_2,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as cant".$i."_1,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (100, 105, 111, 112, 113, 115, 150, 151) 
                AND n.tipcon='A' AND nnp.frecuencia=3),0) as cant".$i."_2";
            }
            if ($i==2) {
               $sql_agentes = "SELECT 
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 218, 219, 221, 222) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as salario".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (217, 220) AND nnp.frecuencia=2),0) as Bonos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (240, 241, 242, 244, 245) AND nnp.frecuencia=2),0) as Faltas".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (243,248) AND nnp.frecuencia=2),0) as amonestaciones".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (287) AND nnp.frecuencia=2),0) as danios".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (1000000) AND nnp.frecuencia=2),0) as celular".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (283) AND nnp.frecuencia=2),0) as pantalla".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (282) AND nnp.frecuencia=2),0) as data".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as seg_soc".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as seg_educ".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (298, 281) AND nnp.frecuencia=2),0) as anticipos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (285) AND nnp.frecuencia=2),0) as logistica".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (286) AND nnp.frecuencia=2),0) as dotacion".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (280, 284, 289, 299) AND nnp.frecuencia=2),0) as otros_descto".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as descto_dir".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (297) AND nnp.frecuencia=2),0) as seg_vida".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 218, 219, 221, 222) 
                AND n.tipcon='A' AND nnp.frecuencia=3),0) as salario".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (217, 220) AND nnp.frecuencia=3),0) as Bonos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (240, 241, 242, 244, 245) AND nnp.frecuencia=3),0) as Faltas".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (243,248) AND nnp.frecuencia=3),0) as amonestaciones".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (287) AND nnp.frecuencia=3),0) as danios".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (280,289) AND nnp.frecuencia=3),0) as celular".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (283) AND nnp.frecuencia=3),0) as pantalla".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (282) AND nnp.frecuencia=3),0) as data".$i."_3,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as seg_soc".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as seg_educ".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (298, 281) AND nnp.frecuencia=3),0) as anticipos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (285) AND nnp.frecuencia=3),0) as logistica".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (286) AND nnp.frecuencia=3),0) as dotacion".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in ( 284, 299) AND nnp.frecuencia=3),0) as otros_descto".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as descto_dir".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (297) AND nnp.frecuencia=3),0) as seg_vida".$i."_2,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 218, 219, 221, 222) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as cant".$i."_1,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (210, 211, 212, 213, 214, 215, 216, 218, 219, 221, 222) 
                AND n.tipcon='A' AND nnp.frecuencia=3),0) as cant".$i."_2";
            }
            if ($i==3) {
               $sql_agentes = "SELECT 
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (300, 310, 316, 318, 319, 321, 322, 323, 324,315) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as salario".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (317, 320) AND nnp.frecuencia=2),0) as Bonos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (340,341,342, 344, 346) AND nnp.frecuencia=2),0) as Faltas".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (343,348) AND nnp.frecuencia=2),0) as amonestaciones".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (387) AND nnp.frecuencia=2),0) as danios".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (380,382) AND nnp.frecuencia=2),0) as celular".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (383) AND nnp.frecuencia=2),0) as pantalla".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as data".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as seg_soc".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as seg_educ".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (381, 398) AND nnp.frecuencia=2),0) as anticipos".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (385) AND nnp.frecuencia=2),0) as logistica".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (386) AND nnp.frecuencia=2),0) as dotacion".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (384, 399, 345) AND nnp.frecuencia=2),0) as otros_descto".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=2),0) as descto_dir".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (397) AND nnp.frecuencia=2),0) as seg_vida".$i."_1,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (300, 310, 316, 318, 319, 321, 322, 323, 324,315) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as salario".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (317, 320) AND nnp.frecuencia=3),0) as Bonos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (340,341,342, 344, 346) AND nnp.frecuencia=3),0) as Faltas".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (343,348) AND nnp.frecuencia=3),0) as amonestaciones".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (387) AND nnp.frecuencia=3),0) as danios".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (380,382) AND nnp.frecuencia=3),0) as celular".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (383) AND nnp.frecuencia=3),0) as pantalla".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as data".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as seg_soc".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as seg_educ".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (381, 398) AND nnp.frecuencia=3),0) as anticipos".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (385) AND nnp.frecuencia=3),0) as logistica".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (386) AND nnp.frecuencia=3),0) as dotacion".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (384, 399, 345) AND nnp.frecuencia=3),0) as otros_descto".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (10000) AND nnp.frecuencia=3),0) as descto_dir".$i."_2,
                COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom)
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (397) AND nnp.frecuencia=3),0) as seg_vida".$i."_2,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (300, 310, 316, 318, 319, 321, 322, 323, 324,315) 
                AND n.tipcon='A' AND nnp.frecuencia=2),0) as cant".$i."_1,
                COALESCE((SELECT distinct count(n.ficha)
                FROM nom_movimientos_nomina n 
                INNER JOIN nom_nominas_pago nnp ON (nnp.codtip = n.tipnom AND nnp.codnom=n.codnom) 
                INNER JOIN nompersonal np ON (np.ficha = n.ficha) 
                WHERE nnp.codtip={$codtip} AND MONTH(nnp.periodo_fin)={$mes} AND YEAR(nnp.periodo_fin)={$anio} AND n.codcon in (300, 310, 316, 318, 319, 321, 322, 323, 324,315) 
                AND n.tipcon='A' AND nnp.frecuencia=3),0) as cant".$i."_2";
            }
            $resul_agentes     = $db->query($sql_agentes);
            $agentes           = $resul_agentes->fetch_assoc();
            $ind_salario1      = "salario".$i."_1";
            $ind_bonos1        = "Bonos".$i."_1";
            $ind_faltas1       = "Faltas".$i."_1";
            $ind_seg_soc1      = "seg_soc".$i."_1";
            $ind_amonestacion1 = "amonestaciones".$i."_1";
            $ind_seg_educ1     = "seg_educ".$i."_1";
            $ind_anticipos1    = "anticipos".$i."_1";
            $ind_logistica1    = "logistica".$i."_1";
            $ind_danios1       = "danios".$i."_1";
            $ind_celular1      = "celular".$i."_1";
            $ind_pantalla1     = "pantalla".$i."_1";
            $ind_data1         = "data".$i."_1";
            $ind_dotacion1     = "dotacion".$i."_1";
            $ind_otros_descto1 = "otros_descto".$i."_1";
            $ind_descto_dir1   = "descto_dir".$i."_1";
            $ind_seg_vida1     = "seg_vida".$i."_1";
            $ind_cant1         = "cant".$i."_1";
            $ind_salario2      = "salario".$i."_2";
            $ind_bonos2        = "Bonos".$i."_2";
            $ind_faltas2       = "Faltas".$i."_2";
            $ind_seg_soc2      = "seg_soc".$i."_2";
            $ind_amonestacion2 = "amonestaciones".$i."_2";
            $ind_seg_educ2     = "seg_educ".$i."_2";
            $ind_anticipos2    = "anticipos".$i."_2";
            $ind_logistica2    = "logistica".$i."_2";
            $ind_danios2       = "danios".$i."_2";
            $ind_celular2      = "celular".$i."_2";
            $ind_pantalla2     = "pantalla".$i."_2";
            $ind_data2         = "data".$i."_2";
            $ind_dotacion2     = "dotacion".$i."_2";
            $ind_otros_descto2 = "otros_descto".$i."_2";
            $ind_descto_dir2   = "descto_dir".$i."_2";
            $ind_seg_vida2     = "seg_vida".$i."_2";
            $ind_cant2         = "cant".$i."_2";
            $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $nominas_agentes[$i]);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    
            $objPHPExcel->getActiveSheet()->SetCellValue("C".$rowCount, $agentes[$ind_cant1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("D".$rowCount, $agentes[$ind_salario1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("E".$rowCount, $agentes[$ind_bonos1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("F".$rowCount, "=D".$rowCount."+E".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("G".$rowCount, $agentes[$ind_faltas1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("H".$rowCount, $agentes[$ind_amonestacion1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("I".$rowCount, $agentes[$ind_seg_soc1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("J".$rowCount, $agentes[$ind_seg_educ1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("K".$rowCount, $agentes[$ind_anticipos1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("L".$rowCount, $agentes[$ind_logistica1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("M".$rowCount, $agentes[$ind_danios1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("N".$rowCount, $agentes[$ind_celular1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("O".$rowCount, $agentes[$ind_pantalla1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("P".$rowCount, $agentes[$ind_data1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("Q".$rowCount, $agentes[$ind_dotacion1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("R".$rowCount, $agentes[$ind_seg_vida1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("S".$rowCount, $agentes[$ind_otros_descto1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("T".$rowCount, $agentes[$ind_descto_dir1]);
            $objPHPExcel->getActiveSheet()->SetCellValue("U".$rowCount, "=G".$rowCount."+H".$rowCount."+I".$rowCount."+J".$rowCount."+K".$rowCount."+L".$rowCount."+M".$rowCount."+N".$rowCount."+O".$rowCount."+P".$rowCount."+Q".$rowCount."+R".$rowCount."+S".$rowCount."+T".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("V".$rowCount, "=F".$rowCount."-U".$rowCount);
    
            $objPHPExcel->getActiveSheet()->SetCellValue("W".$rowCount, $agentes[$ind_cant2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("X".$rowCount, $agentes[$ind_salario2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("Y".$rowCount, $agentes[$ind_bonos2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("Z".$rowCount, "=X".$rowCount."+Y".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AA".$rowCount, $agentes[$ind_faltas2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AB".$rowCount, $agentes[$ind_amonestacion2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AC".$rowCount, $agentes[$ind_seg_soc2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AD".$rowCount, $agentes[$ind_seg_educ2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AE".$rowCount, $agentes[$ind_anticipos2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AF".$rowCount, $agentes[$ind_logistica2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AG".$rowCount, $agentes[$ind_danios2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AH".$rowCount, $agentes[$ind_celular2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AI".$rowCount, $agentes[$ind_pantalla2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AJ".$rowCount, $agentes[$ind_data2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AK".$rowCount, $agentes[$ind_dotacion2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AL".$rowCount, $agentes[$ind_seg_vida2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AM".$rowCount, $agentes[$ind_otros_descto2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AN".$rowCount, $agentes[$ind_descto_dir2]);
            $objPHPExcel->getActiveSheet()->SetCellValue("AO".$rowCount, "=AA".$rowCount."+AB".$rowCount."+AC".$rowCount."+AD".$rowCount."+AE".$rowCount."+AF".$rowCount."+AG".$rowCount."+AH".$rowCount."+AI".$rowCount."+AJ".$rowCount."+AK".$rowCount."+AL".$rowCount."+AM".$rowCount."+AN".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AP".$rowCount, "=Z".$rowCount."-AO".$rowCount);
            $objPHPExcel->getActiveSheet()->SetCellValue("AQ".$rowCount, "=AP".$rowCount."+V".$rowCount);
            $neto21 =($agentes[$ind_salario1]+$agentes[$ind_bonos1]) - ($agentes[$ind_faltas1]+$agentes[$ind_amonestacion1]+$agentes[$ind_seg_soc1]+$agentes[$ind_seg_educ1]+$agentes[$ind_anticipos1]+$agentes[$ind_logistica1]+$agentes[$ind_danios1]+$agentes[$ind_celular1]+$agentes[$ind_pantalla1]+$agentes[$ind_data1]+$agentes[$ind_dotacion1]+$agentes[$ind_seg_vida1]+$agentes[$ind_otros_descto1]+$agentes[$ind_descto_dir1]);
            $neto22 =  ($agentes[$ind_salario2]+$agentes[$ind_bonos2]) - ($agentes[$ind_faltas2]+$agentes[$ind_amonestacion2]+$agentes[$ind_seg_soc2]+$agentes[$ind_seg_educ2]+$agentes[$ind_anticipos2]+$agentes[$ind_logistica2]+$agentes[$ind_danios2]+$agentes[$ind_celular2]+$agentes[$ind_pantalla2]+$agentes[$ind_data2]+$agentes[$ind_dotacion2]+$agentes[$ind_seg_vida2]+$agentes[$ind_otros_descto2]+$agentes[$ind_descto_dir2]);
            $conex = new Database($_SESSION[bd]);
            $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
            $insert .= "('".$nivel."','AGENTES ".$i."','".($agentes[$ind_salario1])."','4','{$codtip}','D".$rowCount."'),";
            $insert .= "('".$nivel."','AGENTES ".$i."','".($agentes[$ind_salario1]+$agentes[$ind_bonos1])."','4','{$codtip}','F".$rowCount."'),";
            $insert .= "('".$nivel."','AGENTES ".$i."','".$neto21."','20','{$codtip}','V".$rowCount."'),";
            $insert .= "('".$nivel."','AGENTES ".$i."','".($agentes[$ind_salario2])."','24','{$codtip}','X".$rowCount."'),";
            $insert .= "('".$nivel."','AGENTES ".$i."','".($agentes[$ind_salario2]+$agentes[$ind_bonos2])."','14','{$codtip}','Z".$rowCount."'),";
            $insert .= "('".$nivel."','AGENTES ".$i."','".$neto22."','40','{$codtip}','AP".$rowCount."'),";
            $insert .= '****';
            $insert = str_replace(',****', ';', $insert);
            $conex->query($insert);
            $rowCount++;
        }

    }
    if($nivel_ant==$nivel && $nivel_ant>0)
    {

        if($codtip == 1)
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AG'.$rowCount)->applyFromArray($b_total);
        }
        if($codtip == 2)
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTAL:');
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AP'.$rowCount)->applyFromArray($b_total);
        }
        if($codtip == 3 )
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':BV'.$rowCount)->applyFromArray($b_total);
        }
        if($codtip == 4)
        {
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'SUBTOTAL');
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AW'.$rowCount)->applyFromArray($b_total);
        }           
        $letra = "D";
        $final = ($rowCount-1);
        $fila_ins = $rowCount;
        for ($i=1; $i <= count($conceptos) ; $i++) 
        {
            $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
            $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, "=SUM(".$letra.$inicio.":".$letra.$final.")");
            $total_cell[$i].=$letra.$rowCount.";";
            $cellValue = $cell->getCalculatedValue();
            $total_nivel[$indice_nivel][$conceptos[$i]]=$cellValue;
            $ixx++;
            $letra++;
        }
        if ($codtip == 1)
        {
            $conex = new Database($_SESSION[bd]);
            $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
            $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','1','{$codtip}','D".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','8','{$codtip}','K".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("L".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','9','{$codtip}','L".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("Q".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','14','{$codtip}','Q".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("S".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','16','{$codtip}','S".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("Z".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','23','{$codtip}','Z".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','24','{$codtip}','AA".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AF".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."--','".$cellValue."','29','{$codtip}','AF".$rowCount."'),";
            $insert .= '****';
            $insert = str_replace(',****', ';', $insert);
            $conex->query($insert);                    
        }
        if ($codtip == 3)
        {
            $conex = new Database($_SESSION[bd]);
            $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
            $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','1','3','D".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','8','3','K".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("P".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','13','3','P".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("Y".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','22','3','Y".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','24','3','AA".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AH".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','31','3','AH".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AM".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','36','3','AM".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AV".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','45','3','AV".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("BB".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','51','3','BB".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("BI".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','58','3','BI".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("BO".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','64','3','BO".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("BV".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."-','".$cellValue."','71','3','BV".$rowCount."'),";
            /*$cell = $objPHPExcel->getActiveSheet()->getCell("BU".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel."','".$row_personal['nivel1']."','".$cellValue."','23','3','BU".$rowCount."'),"*/
            $insert .= '****';
            $insert = str_replace(',****', ';', $insert);
            $conex->query($insert);
        }
        if ($codtip == 4)
        {
            $conex = new Database($_SESSION[bd]);
            $insert = "INSERT INTO salarios_netos (nivel,descrip_nivel,monto, concepto, tipnom, celda) VALUES ";
            $cell = $objPHPExcel->getActiveSheet()->getCell("D".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','1','{$codtip}','D".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("K".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','8','{$codtip}','K".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("L".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','9','{$codtip}','L".$rowCount."'),"; 
            $cell = $objPHPExcel->getActiveSheet()->getCell("P".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','13','{$codtip}','P".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("Y".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','22','{$codtip}','Y".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AA".$rowCount);
            $cellValue = $cell->getCalculatedValue();
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','24','{$codtip}','AA".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AH".$rowCount);
            $cellValue = $cell->getCalculatedValue();  
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','31','{$codtip}','AH".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AM".$rowCount);
            $cellValue = $cell->getCalculatedValue();  
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','36','{$codtip}','AM".$rowCount."'),";
            $cell = $objPHPExcel->getActiveSheet()->getCell("AV".$rowCount);
            $cellValue = $cell->getCalculatedValue();  
            $insert .= "('".$nivel_ant."','".$row_personal['nivel1']."','".$cellValue."','45','{$codtip}','AV".$rowCount."'),";  
            $insert .= '****';
            $insert = str_replace(',****', ';', $insert);
            $conex->query($insert);
        }
        $rowCount++;
    }
    $count++;
    //----------TOTAL POR NIVEL------------------
    if($codtip == 1)
    {
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTALES');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AG'.$rowCount)->applyFromArray($b_total);
    }
   /* if($codtip == 2)
    {
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTALES');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AQ'.$rowCount)->applyFromArray($b_total);
    }*/
    if($codtip == 3)
    {
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTALES');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':BV'.$rowCount)->applyFromArray($b_total);
    }
    if($codtip == 4)
    {
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, 'TOTALES');
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':AW'.$rowCount)->applyFromArray($b_total);
    }
    
    
    $letra = "D";
    $letra_inicio = $letra_inicio;
    $letra_final = "";
    if($codtip != 2)
    {
        for ($i=1; $i <= count($conceptos) ; $i++) 
        {
            $total_consolidado = "=";
            $letra_final=$letra;
            $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
            $totales=explode(";", $total_cell[$i]);
            foreach ($totales as $total => $valor) {
                if ($valor != "") {
                    $total_consolidado .="+".$valor; 
                }
            }
            $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $total_consolidado);
            $letra++;
        }
    }
}
$objPHPExcel->createSheet($count);
$objPHPExcel->setActiveSheetIndex($count);
$objPHPExcel->getActiveSheet()->setTitle("TOTALES");
$letra = "B";
$row = 3;
$niveles = obtener_niveles();
$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
for ($i=0; $i < count($niveles) ; $i++) 
{
    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$row, $niveles[$i]['txt_valor']);
    $row++;
}
$rowCount = 2;
$objPHPExcel->getActiveSheet()->mergeCells('D1:F1');
$objPHPExcel->getActiveSheet()->mergeCells('H1:J1');
$objPHPExcel->getActiveSheet()->mergeCells('L1:N1');
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "BRUTOS");
$objPHPExcel->getActiveSheet()->SetCellValue('H1', "NETOS");
$objPHPExcel->getActiveSheet()->SetCellValue('L1', "DIFERENCIA");

$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "Q1");
$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "Q2");
$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($borders); 
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "TOTAL");
$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($borders);

$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "Q1");
$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "Q2");
$objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($borders); 
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "TOTAL");
$objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($borders);

$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, "Q1");
$objPHPExcel->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, "Q2");
$objPHPExcel->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($borders); 
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, "TOTAL");
$objPHPExcel->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($borders);

/****************************************************************/
$conex = new Database($_SESSION[bd]);
$sql_totales = "SELECT * FROM salarios_netos";
$result = $conex->query($sql_totales);
while($filas = $result->fetch_assoc())
{
    /**********PRIMERA HOJA***********/
    /**********PRESIDENCIA ***********/
    /** BRUTOS */
    if($filas['nivel']=="108" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D4", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E4", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */
    if($filas['nivel']=="108" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H4", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I4", "=ADMINIST!".$filas['celda']);
    }


    /**********GERENCIA ADMON ***********/
    /** BRUTOS */
    if($filas['nivel']=="102" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D6", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E6", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */
    if($filas['nivel']=="102" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H6", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I6", "=ADMINIST!".$filas['celda']);
    }

/*    $objPHPExcel->getActiveSheet()->SetCellValue("E5", "=ADMINIST!Z8");
    $objPHPExcel->getActiveSheet()->SetCellValue("D6", "=ADMINIST!Q8");
    $objPHPExcel->getActiveSheet()->SetCellValue("E6", "=ADMINIST!AF8"); */
    
    /**********GERENCIA GTIC ***********/
    /** BRUTOS */
    if($filas['nivel']=="111" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D8", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E8", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */
    if($filas['nivel']=="111" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H8", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I8", "=ADMINIST!".$filas['celda']);
    }

    /*$objPHPExcel->getActiveSheet()->SetCellValue("D7", "=ADMINIST!D2");
    $objPHPExcel->getActiveSheet()->SetCellValue("E7", "=ADMINIST!D2");
    $objPHPExcel->getActiveSheet()->SetCellValue("D8", "=ADMINIST!D2");
    $objPHPExcel->getActiveSheet()->SetCellValue("E8", "=ADMINIST!D2");*/

    /**********GERENCIA DE LA CALIDAD ***********/
    /** BRUTOS */
    if($filas['nivel']=="105" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D10", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E10", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */

    if($filas['nivel']=="105" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H10", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I10", "=ADMINIST!".$filas['celda']);
    }
    /*$objPHPExcel->getActiveSheet()->SetCellValue("E9", "=ADMINIST!Z18");
    $objPHPExcel->getActiveSheet()->SetCellValue("D10", "=ADMINIST!Q18");
    $objPHPExcel->getActiveSheet()->SetCellValue("E10", "=ADMINIST!AF18");*/

    /**********GERENCIA COMERCIAL ***********/
    /** BRUTOS */
    if($filas['nivel']=="106" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D12", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E12", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */

    if($filas['nivel']=="106" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H12", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I12", "=ADMINIST!".$filas['celda']);
    }
    
    //$objPHPExcel->getActiveSheet()->SetCellValue("D11", "=ADMINIST!K23");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E11", "=ADMINIST!Z23");
    //$objPHPExcel->getActiveSheet()->SetCellValue("D12", "=ADMINIST!Q23");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E12", "=ADMINIST!AF23");
    
    /**********GERENCIA RR.HH. ***********/
    /** BRUTOS */
    if($filas['nivel']=="103" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D14", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E14", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */

    if($filas['nivel']=="103" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H14", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I14", "=ADMINIST!".$filas['celda']);
    }
    
    //$objPHPExcel->getActiveSheet()->SetCellValue("D13", "=ADMINIST!K14");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E13", "=ADMINIST!Z14");
    //$objPHPExcel->getActiveSheet()->SetCellValue("D14", "=ADMINIST!Q14");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E14", "=ADMINIST!AF14");
    
    /**********GERENCIA OPERACIONES ***********/
    /** BRUTOS */
    if($filas['nivel']=="104" AND $filas['concepto']=="1" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="13" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D16", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="24" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="36" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E16", "='ADMINIST 1'!".$filas['celda']);
    }
    /** NETOS */

    if($filas['nivel']=="104" AND $filas['concepto']=="8" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="31" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="22" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H16", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="45" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I16", "='ADMINIST 1'!".$filas['celda']);
    }
    //$objPHPExcel->getActiveSheet()->SetCellValue("D15", "='ADMINIST 1'!K15");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E15", "='ADMINIST 1'!AH15");
    //$objPHPExcel->getActiveSheet()->SetCellValue("D16", "='ADMINIST 1'!Y15");
    //$objPHPExcel->getActiveSheet()->SetCellValue("E16", "='ADMINIST 1'!AV15");

    /**********SUPERVISORES RECORRIDA***********/
    /** BRUTOS */

    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="1" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D17", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="24" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E17", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="13" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D19", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="36" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E19", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="51" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D18", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="64" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E18", "=SUPERVIS!".$filas['celda']);
    }
    /** NETOS */

    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="8" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H17", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="31" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I17", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="22" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H19", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="45" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I19", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="58" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H18", "=SUPERVIS!".$filas['celda']);
    }
    if(($filas['nivel']=="112" or $filas['nivel']=="104") AND $filas['concepto']=="71" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I18", "=SUPERVIS!".$filas['celda']);
    }
    /**********CENTRO DE CONTROL***********/
    /** BRUTOS */

    if($filas['nivel']=="113" AND $filas['concepto']=="1" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D20", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="24" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E20", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="13" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D22", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="36" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E22", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="51" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D21", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="64" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E21", "=SUPERVIS!".$filas['celda']);
    }
    /** NETOS */

    if($filas['nivel']=="113" AND $filas['concepto']=="8" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H20", "='SUPERVIS'!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="31" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I20", "='SUPERVIS'!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="22" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H22", "='SUPERVIS'!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="45" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I22", "='SUPERVIS'!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="58" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H21", "='SUPERVIS'!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="71" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I21", "='SUPERVIS'!".$filas['celda']);
    }
    
    /*
        $objPHPExcel->getActiveSheet()->SetCellValue("D20", "='ADMINIST 1'!K15");
        $objPHPExcel->getActiveSheet()->SetCellValue("E20", "='ADMINIST 1'!Y15");
        $objPHPExcel->getActiveSheet()->SetCellValue("D21", "='ADMINIST 1'!AH15");
        $objPHPExcel->getActiveSheet()->SetCellValue("E21", "='ADMINIST 1'!AV15");
        $objPHPExcel->getActiveSheet()->SetCellValue("D22", "='ADMINIST 1'!BI15");
        $objPHPExcel->getActiveSheet()->SetCellValue("E22", "='ADMINIST 1'!BV15");*/

    /**********SEGUNDA HOJA***********/
    /** BRUTOS */
    if($filas['tipnom']==2 AND $filas['celda']=="F6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="F7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="F8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D25", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E25", $filas['monto']);
    }
    /** NETOS */
    if($filas['tipnom']==2 AND $filas['celda']=="V6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("H23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="AP6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("I23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="V7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("H24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="AP7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("I24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="V8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("H25", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="AP8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("I25", $filas['monto']);
    }
}
$contRow=3;
while($contRow < 26)
{
    $objPHPExcel->getActiveSheet()->SetCellValue("F".$contRow, "=D".$contRow."+E".$contRow);
    $objPHPExcel->getActiveSheet()->SetCellValue("J".$contRow, "=H".$contRow."+I".$contRow);
    $objPHPExcel->getActiveSheet()->SetCellValue("N".$contRow, "=L".$contRow."+M".$contRow);
    $objPHPExcel->getActiveSheet()->SetCellValue("L".$contRow, "=D".$contRow."-H".$contRow);
    $objPHPExcel->getActiveSheet()->SetCellValue("M".$contRow, "=E".$contRow."-I".$contRow);
    $contRow++;
}
$count++;

/****************************************************************/
$objPHPExcel->createSheet($count);
$objPHPExcel->setActiveSheetIndex($count);
$objPHPExcel->getActiveSheet()->setTitle("PROVISIONES");

$objPHPExcel->getActiveSheet()->mergeCells('D1:F1');
$objPHPExcel->getActiveSheet()->mergeCells('G1:J1');

$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "PROVISIONES");
$objPHPExcel->getActiveSheet()->getStyle('D1')->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('D1', $array_meses[$mes]." ".$anio." BRUTOS");
$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "PROVISIONES");

$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "Q1");
$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "Q2");
$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($negrita); 
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "TOTAL");
$objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($negrita);

$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "PRIMA");
$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "VAC");
$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($negrita); 
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "DECIMO");
$objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "TOTAL PROV");
$objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, "TOTAL GRAL");
$objPHPExcel->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($negrita);

$letra = "C";
$row = 3;
$niveles = obtener_niveles();
$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);

for ($i=0; $i < count($niveles) ; $i++) 
{
    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$row, $niveles[$i]['txt_valor']);
    $row++;
}

$objPHPExcel->getActiveSheet()->getStyle("C3:C25")->applyFromArray($borde_simple);

$objPHPExcel->getActiveSheet()->getStyle("D1:K25")->applyFromArray($borde_simple);
$rowCount = 2;

$conex = new Database($_SESSION[bd]);
$sql_totales = "SELECT * FROM salarios_netos";
$result = $conex->query($sql_totales);
while($filas = $result->fetch_assoc())
{
    /**********PRIMERA HOJA***********/
    /**********PRESIDENCIA ***********/
    /** BRUTOS */
    if($filas['nivel']=="108" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D4", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E4", "=ADMINIST!".$filas['celda']);
    }
    /** NETOS */
    if($filas['nivel']=="108" AND $filas['concepto']=="8" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="23" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I3", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="14" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("H4", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="108" AND $filas['concepto']=="29" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("I4", "=ADMINIST!".$filas['celda']);
    }


    /**********GERENCIA ADMON ***********/
    /** BRUTOS */
    if($filas['nivel']=="102" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E5", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D6", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="102" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E6", "=ADMINIST!".$filas['celda']);
    }
    
    /**********GERENCIA GTIC ***********/
    /** BRUTOS */
    if($filas['nivel']=="111" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E7", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D8", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="111" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E8", "=ADMINIST!".$filas['celda']);
    }
    
    /**********GERENCIA DE LA CALIDAD ***********/
    /** BRUTOS */
    if($filas['nivel']=="105" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E9", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D10", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="105" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E10", "=ADMINIST!".$filas['celda']);
    }
    
    /**********GERENCIA COMERCIAL ***********/
    /** BRUTOS */
    if($filas['nivel']=="106" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E11", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D12", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="106" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E12", "=ADMINIST!".$filas['celda']);
    }
    
    /**********GERENCIA RR.HH. ***********/
    /** BRUTOS */
    if($filas['nivel']=="103" AND $filas['concepto']=="1" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="16" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E13", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="9" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D14", "=ADMINIST!".$filas['celda']);
    }
    if($filas['nivel']=="103" AND $filas['concepto']=="24" AND $filas['tipnom']=='1')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E14", "=ADMINIST!".$filas['celda']);
    }
    
    /**********GERENCIA OPERACIONES ***********/
    /** BRUTOS */
    if($filas['nivel']=="104" AND $filas['concepto']=="1" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="13" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D16", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="24" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E15", "='ADMINIST 1'!".$filas['celda']);
    }
    if($filas['nivel']=="104" AND $filas['concepto']=="36" AND $filas['tipnom']=='4')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E16", "='ADMINIST 1'!".$filas['celda']);
    }
    
    /**********SUPERVISORES RECORRIDA***********/
    /** BRUTOS */

    if($filas['nivel']=="112" AND $filas['concepto']=="1" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D17", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="112" AND $filas['concepto']=="24" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E17", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="112" AND $filas['concepto']=="13" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D19", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="112" AND $filas['concepto']=="36" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E19", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="112" AND $filas['concepto']=="51" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D18", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="112" AND $filas['concepto']=="64" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E18", "=SUPERVIS!".$filas['celda']);
    }
    
    /**********CENTRO DE CONTROL***********/
    /** BRUTOS */

    if($filas['nivel']=="113" AND $filas['concepto']=="1" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D20", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="24" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E20", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="13" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D22", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="36" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E22", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="51" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("D21", "=SUPERVIS!".$filas['celda']);
    }
    if($filas['nivel']=="113" AND $filas['concepto']=="64" AND $filas['tipnom']=='3')
    {
        $objPHPExcel->getActiveSheet()->SetCellValue("E21", "=SUPERVIS!".$filas['celda']);
    }
    
    /**********AGENTES***********/
    /** BRUTOS */
    if($filas['tipnom']==2 AND $filas['celda']=="F6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z6"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E23", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="F7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z7"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E24", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="F8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("D25", $filas['monto']);
    }
    if($filas['tipnom']==2 AND $filas['celda']=="Z8"){
        $objPHPExcel->getActiveSheet()->SetCellValue("E25", $filas['monto']);
    }
}

$contRow=3;
while($contRow < 26)
{
    $objPHPExcel->getActiveSheet()->SetCellValue("F".$contRow, "=D".$contRow."+E".$contRow);
    $objPHPExcel->getActiveSheet()->SetCellValue("G".$contRow, "=F".$contRow."*1.9234%");
    $objPHPExcel->getActiveSheet()->SetCellValue("H".$contRow, "=F".$contRow."*0.090909");
    $objPHPExcel->getActiveSheet()->SetCellValue("I".$contRow, "=F".$contRow."*8.3333%");
    $objPHPExcel->getActiveSheet()->SetCellValue("J".$contRow, "=SUM(G".$contRow.":I".$contRow.")");
    $objPHPExcel->getActiveSheet()->SetCellValue("K".$contRow, "=F".$contRow."+J".$contRow);
    $objPHPExcel->getActiveSheet()->getStyle("F$contRow:K$contRow")->getNumberFormat()->setFormatCode('#,##0.00');
    $objPHPExcel->getActiveSheet()->getStyle('J'.$contRow)->applyFromArray($negrita);
    $objPHPExcel->getActiveSheet()->getStyle('K'.$contRow)->applyFromArray($negrita);
    $contRow++;
}

cellColor('J2:J'.($contRow-1), '95b3d7');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="salarios_netos.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>