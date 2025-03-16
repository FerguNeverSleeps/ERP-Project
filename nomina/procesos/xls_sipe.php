<?
session_start();
ob_start();
$termino= $_SESSION['termino'];


 header("Content-type: application/vnd.ms-excel");
 header("Content-Disposition: attachment; filename=SIPE.xls");


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



$totales=new bd($_SESSION['bd']);

list($mes,$ano)=explode('/',$_POST[mesano]);
$frecuencia=$_POST[frecuencia];
$codcon1="200";
$nomina=$_POST['nomina'];

$consulta="select * from nomfrecuencias";
$resultado=$totales->query($consulta);
$arrafre = array() ;
$arredes = array() ;
 

while($fila=$resultado->fetch_assoc())	
{ 
  $arrafre[] = $fila['codfre'] ;
  $arredes[] = $fila['descrip'] ;

}

$consulta="select * from nomconceptos";
$resultado=$totales->query($consulta);
$arrafrec = array() ;
$arredesc = array() ;
 

while($fila=$resultado->fetch_assoc())	
{ 
  $arrafrec[] = $fila['codcon'] ;
  $arredesc[] = $fila['descrip'] ;

}







$fres = explode(',', $frecuencia) ;
$valsuma = 0 ;
for($ix = 0 ; $ix < count($fres) ; $ix++)
{
	$valorcampos = '' ;
	for($lx=0;$lx < count($arrafre) ; $lx++)
	{
		if($arrafre[$lx] == $fres[$ix] ) $valorcampos = $arredes[$lx] ;

	}		

	 $cadens .= "sum(if(nmp.frecuencia =".$fres[$ix]." and nmn.tipcon='A',nmn.monto,0)) as '".$valorcampos."', " ; 
	 $valsuma++ ;
}	

  
 	 $valsuma++ ;










$acum = array() ;
$acum =  $_POST['acum']  ;


for($ix = 0 ; $ix < count($acum) ; $ix++)
{

	$valorcampos = '' ;
	for($lx=0;$lx < count($arrafrec) ; $lx++)
	{
		if($arrafrec[$lx] == $acum[$ix] ) $valorcampos = $arredesc[$lx] ;

	}	

 $cadensc .= "sum(if(nmn.codcon =".$acum[$ix].",nmn.monto,0)) as '".$valorcampos."', " ; 	
}













$consulta="SELECT nmn.ficha as FICHA, np.apenom as NOMBRE ,nmn.cedula as CEDULA,  $cadens  $cadensc sum(if(nmn.tipcon='A',nmn.monto,0)) as 'TOTAL'  FROM nom_movimientos_nomina nmn join nom_nominas_pago nmp on (nmn.codnom=nmp.codnom and nmn.tipnom=nmp.tipnom) JOIN nompersonal np on (np.cedula=nmn.cedula and np.ficha=nmn.ficha) join nomcargos cc on(np.codcargo=cc.cod_car) WHERE   nmp.frecuencia in ($frecuencia) and nmn.mes='$mes' and nmn.anio='$ano' and nmn.tipnom in ($nomina) group by nmn.ficha order by np.apenom";

  

$resultado2=$totales->query($consulta);
//print_r($nombreca) ; 



?>
<table>

<?php
$aux1 = 0 ;
$vectortotales = array() ;
while($fila=$resultado2->fetch_assoc())
{
if($aux1 == 0 )
{
$aux1 = 1;
?><tr><?php
 foreach($fila as $key => $value) {

      echo "<td nowrap><strong>$key</strong></td>"; 
   } ?>
</tr>
<?php }	?>
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
<?php

}
  
?>
<tr><td></td><td></td><td></td> <?php

for($ix = 0 ; $ix < count($vectortotales) ; $ix++)
{
	?><td><?php echo $vectortotales[$ix]   ;?></td>

<?php  } ?>	
</tr>

</table>