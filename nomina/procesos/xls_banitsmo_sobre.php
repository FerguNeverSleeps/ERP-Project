<?
session_start();
ob_start();
$termino= $_SESSION['termino'];

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=BANITSMO.xls");


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



$_SESSION['bd'];
$totales=new bd($_SESSION['bd']);

$proceso=new txt();
//creamos la cabecera del txt



unset($totales);
//construimos el detalle
$movimientos=new bd($_SESSION['bd']);

$consulta="select np.ficha as FICHA,np.apenom as Nombre,sum(if(nmn.tipcon = 'A',  nmn.monto,0)) as 'SALARIO BRUTO', sum(if(nmn.codcon = '200',  nmn.monto,0)) as 'SEGURO SOCIAL',sum(if(nmn.codcon = '201',  nmn.monto,0)) as 'SEGURO EDUCATIVO', (sum(if(nmn.tipcon = 'A',  nmn.monto,0)) - sum(if(nmn.codcon = '200',  nmn.monto,0)) - sum(if(nmn.codcon = '201',  nmn.monto,0))) as NETO  from nom_nomina_netos nnn join nom_movimientos_nomina nmn on(nmn.codnom = nnn.codnom and nmn.ficha = nnn.ficha) join nompersonal   np on (np.cedula=nnn.cedula and np.ficha=nnn.ficha) where nnn.codnom='".$_GET['codigo_nomina']."' and nnn.tipnom='".$_SESSION['codigo_nomina']."' and np.forcob = 'Efectivo' and  np.codbancob='1' GROUP BY np.ficha order by nnn.ficha";
$resultado=$movimientos->query($consulta);
//FICHA  |  NOMBRE  | SALARIO BRUTO | SEGURO SOCIAL |  SEGURO EDUCATIVO |  NETO |

 



?>
<table>




<?php
$aux1 = 0 ;
while($fila=$resultado->fetch_assoc())
{

if($aux1 == 0 )
{
$aux1 = 1;
?><tr><?php
 foreach($fila as $key => $value) {

      echo "<td nowrap><strong>$key</strong></td>"; 
   } ?>
</tr>
<?php }	 






	if($fila['cta_ban']=="")
	{	
?>
<tr>
<?php
	//$detalles="";
	$neto_empleado=$fila['neto'];
	$cuenta_bancaria=$fila['cta_ban'];
	$cedula=$fila['cedula'];
	?>


<tr>
<?php
	 
	$comenzaracumular = 0 ; 
    foreach($fila as $key => $value) {
		echo "<td nowrap>$value</td>"; 
		if($comenzaracumular >= 3 && $comenzaracumular < ($valsuma +3))
		{
			$vectortotales[$comenzaracumular-3] +=$value ;
		}
		$comenzaracumular ++ ;



   }
?>
</tr>


	 
	<!--$nombre=str_replace(",","",$fila['apenom']);
 
	echo "<td>".$cuenta_bancaria."</td><td>".$neto_empleado."</td><td>DEPOSITO DIRECTO DE PLANILLA</td><td>".$nombre."</td>";
-->

</tr>

<?php
}
}
?>
</table>