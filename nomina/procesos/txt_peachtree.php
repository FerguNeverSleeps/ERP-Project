<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../paginas/funciones_nomina.php";


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
		$this->temp=number_format($monto,2,'','');
		//$this->retorno=$this->temp;
		//$this->retorno.=$this->temp[1];
		return $this->temp;
	}

}

$proceso=new txt();

$nomina=$_GET['codigo_nomina'];

if($_SESSION['codigo_nomina']=='3')
{
	$codcon=2065;
	//$codcon_pat=3542;
}


$directorio="txt/nomina".date("Y_m_d_H_i_s");
if(mkdir($directorio)){
$ruta=$directorio."/nomina.txt";
$archivo= fopen($ruta,"w");
chmod($directorio,0777);
chmod($ruta,0777);
}else{
echo "No se pudo crear el directrorio";

}
$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

//buscamos los datos del tipo de nomina
/*
$consulta="select * from nomtipos_nomina where codtip='".$_SESSION['codigo_nomina']."'";
$resultado=$totales->query($consulta);
$fila=$resultado->fetch_assoc();
$codigo_empresa=$fila['codigo_banco'];
//buscamos los datos de la nomina
$consulta="select * from nom_nominas_pago where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado=$totales->query($consulta);
$fila=$resultado->fetch_assoc();


$consulta_temp="select count(*) as num,sum(neto) as total from nom_nomina_netos where codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";

$resultado_temp=$totales->query($consulta_temp);
$fila_temp=$resultado_temp->fetch_assoc();
$num_registros=$fila_temp['num'];//$resultado_temp->num_rows;
$num_registros+=1;
*/
//llamamos a la clase de proceso

$proceso=new txt();



//creamos la cabecera del txt



unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta="select * from nom_nominas_pago where  codnom='".$_GET['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."' ";
$resultado=$movimientos->query($consulta);
$fetchh=$resultado->fetch_assoc();

//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO ASIGNACION QUE EXISTEN EN ESTA NOMINA
$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."'  AND tipnom='".$_SESSION['codigo_nomina']."' AND tipcon='A' ORDER BY codcon";	
$result10=$movimientos->query($consulta,$conexion);

while($fetch_con=$result10->fetch_assoc())
{
	$conexion=conexion();
	$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina join nompersonal on nom_movimientos_nomina.cedula=nompersonal.cedula  WHERE nom_movimientos_nomina.codnom='".$_GET['codigo_nomina']."'  AND nom_movimientos_nomina.tipnom='".$_SESSION['codigo_nomina']."' AND nom_movimientos_nomina.codcon=".$fetch_con['codcon']." ";
	$result_suma=$movimientos->query($consulta,$conexion);
	$fetch_suma=$result_suma->fetch_assoc();

	
	
	$consulta="SELECT ctacon, descrip FROM nomconceptos WHERE codcon=".$fetch_con['codcon']."";
	$result_cta=$movimientos->query($consulta,$conexion);
	$fetch_cta=$result_cta->fetch_assoc();
	
	
	$detalles="";	
	$detalles.=date("d/m/Y");
	$detalles.="	";
	$detalles.=$fetchh[descrip];
	$detalles.="	";
	$detalles.='10';
	$detalles.="	";
	$detalles.=$fetch_cta[ctacon];
	$detalles.="	";
	$detalles.=trim($fetch_cta[descrip]);
	$detalles.="	";
	$detalles.="-".$fetch_suma[suma];
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	$i++;

}

//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO DEDUCCION QUE EXISTEN EN ESTA NOMINA
$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' AND tipnom='".$_SESSION['codigo_nomina']."' AND tipcon='D' ORDER BY codcon";
$result_cond=$movimientos->query($consulta,$conexion);

$total_deduc;
while($fetch_cond=$result_cond->fetch_assoc())
{	
	$conexion=conexion();
	$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' AND tipnom='".$_SESSION['codigo_nomina']."' AND codcon=".$fetch_cond['codcon']." ";
	$result_suma2=$movimientos->query($consulta,$conexion);
	$fetch_suma2=$result_suma2->fetch_assoc();
		
	$consulta="SELECT ctacon, descrip FROM nomconceptos WHERE codcon='$fetch_cond[codcon]'";
	$result_cta2=$movimientos->query($consulta,$conexion);
	$fetch_cta2=$result_cta2->fetch_assoc();
		//$descripcion=$descripcion." ".$fila_cheque['concepto'];
	
	$detalles="";	
	$detalles.=date("d/m/Y");
	$detalles.="	";
	$detalles.=$fetchh[descrip];
	$detalles.="	";
	$detalles.='10';
	$detalles.="	";
	$detalles.=$fetch_cta2[ctacon];
	$detalles.="	";
	$detalles.=trim($fetch_cta2[descrip]);
	$detalles.="	";
	$detalles.="-".$fetch_suma2[suma];
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	$i++;
	
}
$conexion=conexion();
//SE CONSULTAN LOS DISTINTOS CONCEPTOS TIPO PATRONAL QUE EXISTEN EN ESTA NOMINA
$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' AND tipnom='".$_SESSION['codigo_nomina']."' AND tipcon='P' ORDER BY codcon";	
$result31=$movimientos->query($consulta,$conexion);

while($fetch_con=$result31->fetch_assoc())
{
	$conexion=conexion();
	$consulta="SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$_GET['codigo_nomina']."' AND tipnom='".$_SESSION['codigo_nomina']."' AND codcon=".$fetch_con['codcon']."";
	$result_suma=$movimientos->query($consulta,$conexion);
	$fetch_suma=$result_suma->fetch_assoc();
	
	$consulta="SELECT ctacon, descrip FROM nomconceptos WHERE codcon=".$fetch_con['codcon']."";
	$result_cta=$movimientos->query($consulta,$conexion);
	$fetch_cta=$result_cta->fetch_assoc();
		

	//$descripcion=$descripcion." ".$fila_cheque['concepto'];
	
	$detalles="";	
	$detalles.=date("d/m/Y");
	$detalles.="	";
	$detalles.=$fetchh[descrip];
	$detalles.="	";
	$detalles.='10';
	$detalles.="	";
	$detalles.=$fetch_cta2[ctacon];
	$detalles.="	";
	$detalles.=trim($fetch_cta2[descrip]);
	$detalles.="	";
	$detalles.=$fetch_suma2[suma];
	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	$i++;
	
}

/*
while($fila=$resultado->fetch_assoc())
{
	$detalles="";
	$neto_empleado=$fila['neto'];
	$cuenta_bancaria=$fila['cta_ban'];
	$cedula=$fila['cedula'];
	
	//$identificacion="770";
	//$detalles.=$identificacion;
	//$cuenta_bancaria="010800".$cuenta_bancaria;
	//$numero_cuenta=$proceso->completar($cuenta_bancaria,20);
	$detalles.=$cedula;
	$detalles.=',';
	$nombre=str_replace(",","",$fila['apenom']);
	$detalles.=$nombre;
	$detalles.=',000000071';
	$detalles.=',';
	$detalles.=$fila[cta_ban];
	$detalles.=',04';
	$detalles.=',';
	//$campo_libre=$proceso->completar("",4);
	//$detalles.=$campo_libre;

	//$detalles.=$mes_pago;
	if(strlen($neto_empleado)==6)
		$detalles.=$proceso->completar($neto_empleado,5);
	elseif(strlen($neto_empleado)==5)
		$detalles.=$proceso->completar($neto_empleado,6);
	elseif(strlen($neto_empleado)==7)
		$detalles.=$proceso->completar($neto_empleado,4);
	
	$detalles.=",C,REF*TXT**DEPOSITO DIRECTO DE PLANILLA\ ";

	$detalles.="\r\n";
	fwrite($archivo,$detalles);
	//echo $detalles;
}
*/

fclose($archivo);
//echo "Content-Disposition: attachment; filename=".$ruta;
header("Content-type: application/octet-stream");
readfile($ruta); 
header('Content-Disposition: attachment; filename="peachtree.txt"');

?>
