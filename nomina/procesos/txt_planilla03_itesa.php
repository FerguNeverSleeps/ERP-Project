<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";
$conexion=conexion();

//include "../header.php";
class txt{
	private $temp,$retorno;
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
		$this->temp=number_format($monto,2,'.',',');
		//$this->retorno=$this->temp;
		//$this->retorno.=$this->temp[1];
		return $this->temp;
	}

}

$proceso=new txt();

$codtip=$_GET['codigo_nomina'];
$mesano1=$_GET['mesano'];
$mesano2=$_GET['mesano1'];
$ma1=explode("/",$mesano1);
$ma2=explode("/",$mesano2);



$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/planilla03_itesa.txt";
$archivo= fopen($ruta,"w");
chmod($directorio,0777);
chmod($ruta,0777);
}else{
echo "No se pudo crear el directrorio";

}

$movimientos=new bd($_SESSION['bd']);

$sql = "SELECT DISTINCT np.ficha, np.cedula as cedula, np.apenom as nombre, np.seguro_social as seguro, np.fecing as fecha_ingreso
					FROM   nom_movimientos_nomina nm, nompersonal np
					WHERE  nm.cedula=np.cedula  AND nm.tipnom='".$codtip."'
					ORDER BY np.apenom";
$resultado=$movimientos->query($sql);


while($fila=$resultado->fetch_assoc())
{
	
    $ficha=$fila['ficha'];
    
    if($fila['fecha_ingreso']<fecha_sql($mesano1)){
        $inicio=fecha_sql($mesano1);
        }else{
                $inicio=$fila['fecha_ingreso'];
        }    
    $meses=floor(antiguedad($inicio,fecha_sql($mesano2),'D')/30);
    
    //SALARIOS INTEGRAL (SALARIOS + XIII MES + VACACIONES)
    $sql_salarios_integral = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=102 OR nm.codcon=114 ) AND nm.anio=".$ma1[2]."";
    
    $res_salarios_integral=query($sql_salarios_integral, $conexion);
    $salarios_integral=0;
    while($fila_salarios_integral=fetch_array($res_salarios_integral))
    {
        $salarios_integral=$salarios_integral+$fila_salarios_integral['monto'];
    }
    
    //GASTOS REPRESENTACION
    $sql_gastosr = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=145) AND nm.anio=".$ma1[2]."";
    
    $res_gastosr=query($sql_gastosr, $conexion);
    $gastosr=0;
    while($fila_gastosr=fetch_array($res_gastosr))
    {
        $gastosr=$gastosr+$fila_gastosr['monto'];
    }
    
    //DEDUCCIONES
    $sql_deducciones = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=200) AND nm.anio=".$ma1[2]."";
    
    $res_deducciones=query($sql_deducciones, $conexion);
    $deducciones=0;
    while($fila_deducciones=fetch_array($res_deducciones))
    {
        $deducciones=$deducciones+$fila_deducciones['monto'];
    }
    
    //SEGURO EDUCATIVO
    $sql_seguro = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=201) AND nm.anio=".$ma1[2]."";
    
    $res_seguro=query($sql_seguro, $conexion);
    $seguro=0;
    while($fila_seguro=fetch_array($res_seguro))
    {
        $seguro=$seguro+$fila_seguro['monto'];
    }
    //SEGURO EDUCATIVO GR
    $sql_segurogr = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=207) AND nm.anio=".$ma1[2]."";
    
    $res_segurogr=query($sql_segurogr, $conexion);
    $segurogr=0;
    while($fila_segurgro=fetch_array($res_segurogr))
    {
        $segurogr=$segurogr+$fila_seguro['monto'];
    }    
    //INTERESES HIPOTECARIOS
    $interes_hipotecarios=0;
    
    //INTERESES EDUCATIVOS
    $interes_educativos=0;
    
    //PRIMAS SEGUROS
    $primas_seguros=0;
    
    //FONDO JUBILACION
    $fondo_jubilacion=0;

    //TOTAL
    $total=$salarios+$gastosr;
    
    //RENTA NETA GRAVABLE
    $renta_neta=$total-$deducciones-$seguro-$interes_hipotecarios-$interes_educativos-$primas_seguros-$fondo_jubilacion;
  
    
    //SALARIOS
    $sql_salarios = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=100) AND nm.anio=".$ma1[2]."";
    
    $res_salarios=query($sql_salarios, $conexion);
    $salarios=0;
    while($fila_salarios=fetch_array($res_salarios))
    {
        $salarios=$salarios+$fila_salarios['monto'];
    }
        $nombre=str_replace(",","",$fila['nombre']);
        $cedula=$fila['cedula'];
        $empleado_declara=1;
        $tipo_id=1;
        $grupo=1;
        $dependientes=0;
        $meses_trabajados=$meses;
        $salario=$salarios_integral;
        $especies=0.00;
        $gastos_representacion=$gastosr;
        $salarios_sin_retencion=0.00;
        $deducciones_basicas=$deducciones;
        $seguro_educativo=$seguro;
        $intereses_hipotecarios=0.00;
        $intereses_educativos=0.00;
        $primas_seguro=0.00;
        $fondo_jubilacion=0.00;
        $total_deducciones=$total;
        $neto_gravable=$renta_neta;
        $impuesto_causado=0.00;
        $impuesto_gastos_representacion=0.00;
        $exencion_ley6=0.00;
        $retenciones_salarios=0.00;
        $retenciones_gastos_representacion=0.00;
        $ajuste=0.00;
        $total_24_28=0.00;
         if(($salarios_integral-$deducciones)>0)
        {
            $fisco=0-0;
            $empleado=$salarios_integral-$deducciones;
        }
        else
        {
            $fisco=$salarios_integral-$deducciones;
            $empleado=0.00;
        }
        
        
        
        
	$detalles=$empleado_declara;
        $detalles.=';';
        $detalles.=$tipo_id;
        $detalles.=';';
        $detalles.=$cedula;
	$detalles.=';';
        $detalles.=$dv;
        $detalles.=';';	
	$detalles.=$nombre;
        $detalles.=';';
	$detalles.=$grupo;
        $detalles.=';';
        $detalles.=$dependientes;
        $detalles.=';';
        $detalles.=$meses_trabajados;
        $detalles.=';';
        $detalles.=$salario;
        $detalles.=';';
        $detalles.=$especies;
        $detalles.=';';
        $detalles.=$gastos_representacion;
        $detalles.=';';
        $detalles.=$salarios_sin_retencion;
        $detalles.=';';
        $detalles.=$deducciones_basicas;
        $detalles.=';';
        $detalles.=$seguro_educativo;
        $detalles.=';';
        $detalles.=$intereses_hipotecarios;
        $detalles.=';';
        $detalles.=$intereses_educativos;
        $detalles.=';';
        $detalles.=$primas_seguro;
        $detalles.=';';
        $detalles.=$fondo_jubilacion;
        $detalles.=';';
        $detalles.=$total_deducciones;
        $detalles.=';';
        $detalles.=$neto_gravable;
        $detalles.=';';
        $detalles.=$impuesto_causado;
        $detalles.=';';
        $detalles.=$impuesto_gastos_representacion;
        $detalles.=';';
        $detalles.=$exencion_ley6;
        $detalles.=';';
        $detalles.=$retenciones_salarios;
        $detalles.=';';
        $detalles.=$retenciones_gastos_representacion;
        $detalles.=';';
        $detalles.=$ajuste;
        $detalles.=';';
        $detalles.=$total_24_28;
        $detalles.=';';
        $detalles.=$fisco;
        $detalles.=';';
        $detalles.=$empleado;
        
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}


fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="planilla03_itesa.txt"');

?>
