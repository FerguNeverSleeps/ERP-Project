<?php

session_start();
ob_start();
$termino= $_SESSION['termino'];
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$conexion=conexion();

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("excel_sipe_css.xlsx");
//------------------------------------------------------------

$meses1      = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
$mes_letras  = $meses1[date('m') - 1];
//--------------------------------------------------------------
//titulo de la tabla
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
        'name'  => 'Calibri'
    )
);

class txt{
    private $temp,$retorno,$cad;
    public function completar($cad,$long)
    {
        $this->temp=$cad;
        for($i=1;$i<=$long;$i++){
            $this->temp="0".$this->temp;    
        }
        return $this->temp;
        
    }
    public function completar2($LARGO,$CADENA)
    {
        $this->temp=$cad;
        for($i=1;$i<=$long;$i++){
            $this->temp="0".$this->temp;
        }
        return $this->temp;
        
    }
    public function formatear_monto($monto){
        $this->temp=number_format($monto,2,'','');
        //$this->retorno=$this->temp;
        //$this->retorno.=$this->temp[1];
        return $this->temp;
    }
        
    public function eliminar_acentos($cadena){
        
        //Reemplazamos la A y a
        $cadena = str_replace(
        array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
        array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
        $cadena
        );
 
        //Reemplazamos la E y e
        $cadena = str_replace(
        array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
        array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
        $cadena );
 
        //Reemplazamos la I y i
        $cadena = str_replace(
        array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
        array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
        $cadena );
 
        //Reemplazamos la O y o
        $cadena = str_replace(
        array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
        array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
        $cadena );
 
        //Reemplazamos la U y u
        $cadena = str_replace(
        array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
        array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
        $cadena );
 
        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
        array('Ñ', 'ñ', 'Ç', 'ç'),
        array('N', 'n', 'C', 'c'),
        $cadena);
                
        $cadena = str_replace(
        array("\\", "¨", "º", "-", "~",
            "#", "@", "|", "!", "\"",
            "·", "$", "%", "&", "/",
            "(", ")", "?", "'", "¡",
            "¿", "[", "^", "<code>", "]",
            "+", "}", "{", "¨", "´",
            ">", "< ", ";", ",", ":",
            "."),
            '',
            $cadena
        );
                
        $this->cad=$cadena;
        return $this->cad;
    }
        
    public function eliminar_espacios($cadena){
        
        //Reemplazamos la A y a
        $cad=str_replace(" ", "", $cadena);
        $this->cad=$cad;                        
        return $this->cad;
    }
        
    public function eliminar_comas($cadena){
        
        //Reemplazamos la A y a
        $cad=str_replace(",", "", $cadena);
        $this->cad=$cad;
        return $this->cad;
    }
        
    public function mayusculas($cadena){
        
        //Reemplazamos la A y a
        $cad=strtoupper($cadena);
        $this->cad=$cad;
        return $this->cad;
    }
}

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "ID BENEFICIARIO");
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "NOMBRE BENEFICIARIO");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "RUTA BANCO");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "CUENTA BANCO");
$objPHPExcel->getActiveSheet()->SetCellValue('E1', "TIPO CUENTA");
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "MONTO");
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "TIPO TRANS.");
$objPHPExcel->getActiveSheet()->SetCellValue('H1', "ADENDA");
/*$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Deducc.");*/
//Fin encabezado tabla
$rowCount = 2;
$count    = 1;
/* Se buscan los campos que tuvieron movimientos en planilla */

$consulta="SELECT nnn.*, np.apenom, np.nombres, np.nombres2, np.apellidos, np.telefonos,nnn.forcob, nnp.descrip 
            FROM nom_nomina_netos nnn 
            JOIN nompersonal np ON (np.cedula=nnn.cedula AND np.ficha=nnn.ficha) 
            LEFT JOIN nomnivel1 n1 on (n1.codorg=nnn.codnivel1) 
            LEFT JOIN nom_nominas_pago nnp ON nnp.codnom = nnn.codnom AND nnp.tipnom = nnn.tipnom
            WHERE nnn.codnom='".$_GET['codnom']."' AND nnn.tipnom='".$_GET['codtip']."' 
            AND nnn.forcob<>'Efectivo' AND np.codbancob = '1'  
            ORDER BY n1.markar,nnn.cedula";
$resultado=query($consulta,$conexion);
while ($fila=fetch_array($resultado,$conexion))
{ 
    //----------------------------------------
    $fonts = array(
        'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('argb' => 'FF000000')
            )
        ),
        'font'  => array(
            'bold'   => false,
            'color'  => array('rgb' => '000000'),
            'size'   => 10,
            'name'   => 'Arial',
            'center' => true
        )
    );
  
    $proceso=new txt();
    $neto_empleado=$fila['neto'];
    $cuenta_bancaria=trim($fila['cta_ban']);
    $cedula=$fila['cedula'];
    // Si es cuenta corriente o cuenta ahorros
    if ($fila['forcob'] == "Cuenta Corriente") {
        $forcob = "03";
    } elseif ($fila['forcob'] == "Cuenta Ahorro"){
        $forcob = "04";
    }
    
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowCount", $cedula, PHPExcel_Cell_DataType::TYPE_STRING);

    $nombre=$proceso->eliminar_acentos(utf8_encode($fila['nombres']." ".$fila['nombres2']." ".$fila['apellidos']));
    $nombre=$proceso->eliminar_comas($nombre);
    $nombre=$proceso->mayusculas($nombre);
    if(strlen($nombre)>=25)
        $nombre=substr($nombre,0,25);
    else
        $nombre=str_pad($nombre, 25, " ", STR_PAD_RIGHT);

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowCount", $nombre, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, '71');
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rowCount", str_pad(trim($fila['cta_ban']), 17, " ", STR_PAD_RIGHT), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rowCount", $forcob, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, str_pad(trim($neto_empleado), 9, " ", STR_PAD_RIGHT));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G$rowCount", "C", PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("H$rowCount", "REF*TXT**DEPOSITO DIRECTO DE PLANILLA\\", PHPExcel_Cell_DataType::TYPE_STRING);

    $borders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            )
        ),
    );
    $rowCount++;
    $count++;
    $descrip = $proceso->eliminar_acentos(utf8_encode($fila['descrip']));
}
$descrip = str_replace(" ","_", $descrip);
$final = $rowCount-1;

$objPHPExcel->getActiveSheet()->setTitle('ACH Banco General');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$nombre_archivo = "ACH_BANCO_GENERAL_".$descrip.".xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
ob_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>