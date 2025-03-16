<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
//include ("../paginas/funciones_nomina.php");

//include('../lib/common_excel.php');
//include('../paginas/lib/php_excel.php');
//require_once '../paginas/func_bd.php';
//include ("../paginas/funciones_nomina.php");

$conexion=conexion();

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("excel_sipe_css.xlsx");
//------------------------------------------------------------
//echo $id;
//exit; 




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
$rowCount =2;
$count    = 1;
/* Se buscan los campos que tuvieron movimientos en planilla */

$codnom= $_GET['codnom'];
$codtip= $_GET['codtip'];

query("set names utf8",$conexion);

$consulta="SELECT 
            SUM(nm.monto) AS suma, nm.codcon, 
            CASE WHEN p.ee = 1 THEN np.apenom ELSE p.descrip END descrip_acreedor, 
            nm.tipcon, 
            pc.codigopr, 
            p.descrip, 
            p.formula, 
            pc.ruta_destino, 
            pc.cuenta_destino, 
            pc.producto_destino, 
            nm.ficha,
            np.apenom, 
            p.ee
        FROM nom_movimientos_nomina AS nm 
            LEFT JOIN nompersonal AS np ON (np.ficha = nm.ficha)
            LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) 
            LEFT JOIN nomprestamos AS p ON (c.ccosto=p.codigopr)
            LEFT JOIN nomprestamos_cabecera AS pc ON (nm.numpre=pc.numpre) 
        WHERE nm.codnom='{$codnom}' AND nm.tipnom='{$codtip}' 
            AND (nm.codcon>500 AND nm.codcon<600) AND nm.codcon not in ('508','518','520', '523')  
        GROUP BY pc.cuenta_destino 
        ORDER BY descrip_acreedor ASC";

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

    $neto=$fila['suma'];
    $cuenta_bancaria=trim($fila['cuenta_destino']);
    $cedula=$fila['ficha'];
    
    // Si es cuenta corriente o cuenta ahorros
    if ($fila['producto_destino'] == "Cuenta Corriente") {
        $forcob = "03";
    } elseif ($fila['producto_destino'] == "Cuenta Ahorro"){
        $forcob = "04";
    }
    
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowCount", $cedula, PHPExcel_Cell_DataType::TYPE_STRING);


    $nombre=$proceso->eliminar_acentos(($fila['descrip_acreedor']));
    $nombre=$proceso->eliminar_comas($nombre);
    $nombre=$proceso->mayusculas($nombre);

    /*if(strlen($nombre)>=25)
        $nombre=substr($nombre,0,25);
    else
        $nombre=str_pad($nombre, 25, " ", STR_PAD_RIGHT);*/
    $nombre = trim($nombre);

    $apenom=$proceso->eliminar_acentos($fila['apenom']);
    $apenom=$proceso->eliminar_comas($apenom);
    $apenom=$proceso->mayusculas($apenom);

    if($fila['ee'] == 2){
        if(!mb_detect_encoding($apenom,["UTF-8"],true))
            $apenom=utf8_decode($apenom);

        $adenda = "REF*TXT**{$apenom}\\";
    }
    else
    {
        $adenda = "REF*TXT**ACREEDORES\\";
    }

    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowCount", $nombre, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $fila['ruta_destino']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rowCount", str_pad($fila['cuenta_destino'], 17, " ", STR_PAD_RIGHT), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rowCount", $fila['producto_destino'], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, trim($neto));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("G$rowCount", "C", PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("H$rowCount", $adenda , PHPExcel_Cell_DataType::TYPE_STRING);

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

}

$final = $rowCount-1;

$objPHPExcel->getActiveSheet()->setTitle('ACH Banco General');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$nombre_archivo = "txt_ach_acreedores_banco_general.xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
ob_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>