<?php

session_start();

include ("../../includes/phpexcel/Classes/PHPExcel/IOFactory.php");
//include ("../lib/common.php");
//include ("../paginas/func_bd.php");
//include ("../header.php");
error_reporting(E_ALL);

$db_user   = root;
$db_pass   = haribol;
$db_name   = asamblea_rrhh;
$db_host   = localhost;


$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

  mysqli_query($conexion, 'SET CHARACTER SET utf8');

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
//$objReader->setReadDataOnly(true);
//==== load forms tashkil where the file exists
$objPHPExcel = $objReader->load("exportaciones/rrhh_empleados_movimientos.xlsx");
echo $db_host;
//==== set active sheet to read data
$worksheet  = $objPHPExcel->getSheet(0);


$highestRow         = $worksheet->getHighestRow(); // e.g. 10
$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
$nrColumns          = ord($highestColumn) - 64;
$worksheetTitle     = $worksheet->getTitle();

echo "<br>La Hoja de Cálculo ".$worksheetTitle." tiene ";
echo $nrColumns . ' columnas (A-' . $highestColumn . ') ';
echo ' y ' . $highestRow . ' filas.';
echo '<br><br>Datos: <table border="1"><tr>';
//----- loop from all rows -----
for ($row = 1; $row <= $highestRow; ++ $row) 
{
    $tipo_tiporegistro=NULL;
    $baja=0;
    echo '<tr>';
    echo "<td>".$row."</td>";
    //--- read each excel column for each row ----
    for ($col = 0; $col < $highestColumnIndex; ++ $col) 
    {
        if($row == 1)
        {
            // show column name with the title
             //----- get value ----
            $cell = $worksheet->getCellByColumnAndRow($col, $row);
            $val = $cell->getValue();
            //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
            echo '<td>' . $val .'</td>';
        }
        else
        {
            if($col == 1)
            {
                //----- get value ----
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                $id_empleado = $val;
                $consultap="SELECT * FROM nompersonal WHERE personal_id='$id_empleado'";               
                $resultadop=mysqli_query($conexion,$consultap);
                $fetchCon=mysqli_fetch_array($resultadop);                
                $cedula=$fetchCon['cedula'];
               
                //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                echo '<td>' . $cedula .'</td>';
            }
            else if($col == 2)
            {
                $date = PHPExcel_Shared_Date::ExcelToPHPObject($worksheet->getCellByColumnAndRow($col, $row)->getValue())->format('Y-m-d');
                echo '<td>'. $date .'</td>';
                $fecha=$date;
            }
            else if($col == 3)
            {
                //----- get value ----
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                if($val=="Traslado")
                {    
                    $tipo_registro=9;
                    $tipo_tiporegistro=30;
                }
                if($val=="Ingreso")
                {    
                    $tipo_registro=55;                    
                }
                if($val=="Baja")
                {    
                    $baja=1;
                    $consulta_baja="SELECT * FROM causal_baja WHERE id_causal_baja='$causal_baja'";               
                    $resultado_baja=mysqli_query($conexion,$consulta_baja);
                    $fetch_baja=mysqli_fetch_array($resultado_baja);                
                    $tipo_registro=$fetch_baja['id_expediente_tipo'];
                }
                
                //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                echo '<td>' . $val .'</td>';
            }
            else if($col == 9)
            {
                //----- get value ----
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                if($baja==1)
                {    
                    $baja=0;
                    $causal_baja=$val;
                    $consulta_baja="SELECT * FROM causal_baja WHERE id_causal_baja='$causal_baja'";               
                    $resultado_baja=mysqli_query($conexion,$consulta_baja);
                    $fetch_baja=mysqli_fetch_array($resultado_baja);                
                    $tipo_registro=$fetch_baja['id_expediente_tipo'];
                }
                
                //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                echo '<td>' . $val .'</td>';
            }
            else if($col == 12)
            {
                //----- get value ----
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                if($val=="")
                    $descripcion='IMPORTADO DESDE SISTEMA ANTERIOR: '.$val;
                else
                    $descripcion='IMPORTADO DESDE SISTEMA ANTERIOR';
                
                //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                echo '<td>' . $val .'</td>';
            }
            else
            {
                 //----- get value ----
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $val = $cell->getValue();
                //$dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                echo '<td>' . $val .'</td>';
            }
        }
    }
    if($row != 1)
    {
        //INSERCIÓN EXPEDIENTE
        $fecha_creacion = date("Y-m-d");
        $usuario = "importacion";
       $consulta_expediente="INSERT INTO expediente
                   (cod_expediente_det, cedula, descripcion, fecha, tipo, subtipo, fecha_creacion, usuario_creacion )
                   VALUES  
                   ('','{$cedula}','{$descripcion}','{$fecha}',"
                   . "'{$tipo_registro}','{$tipo_tiporegistro}','{$fecha_creacion}','{$usuario}')";
       //echo $consulta_expediente;
       $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    }
    echo '</tr>';
}
echo '</table>';



?>