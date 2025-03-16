<?php
require_once '../../generalp.config.inc.php';
include("../lib/common.php") ;
include("func_bd.php");	

$personal=$_GET['personal'];
$SQL = "SELECT b.des_car, c.descrip 
		FROM nompersonal as a left join nomnivel1 as c on (a.codnivel1=c.codorg) 
		LEFT JOIN nomcargos as b on (a.codcargo=b.cod_car) 
		WHERE a.ficha='".$personal."'";
		
$result=sql_ejecutar($SQL);	
$filas=mysqli_fetch_array($result);
echo '<label for="des_car"><h4>Cargo</h4></label><input  class="form-control" type="text" name="des_car" id="des_car" value="'.$filas[des_car].'" disabled>';
echo '<label for="descip"><h4>Departamento</h4></label><input  class="form-control" type="text" name="descip" id="descip" value="'.$filas[descrip].'" disabled>
';

?>