<?php 

require_once('../lib/database.php');
$db           = new Database($_SESSION['bd']);

$year  = date("Y");
$mes   = date("m");
$dia   = date("d");
$sql_1 = "SELECT gen_tiempo_i FROM param_ws";
$res   = $db->query($sql_1);



$fila  = $res->fetch_assoc();
//echo $fila[gen_tiempo_i],"<br>";       
 
if(count($fila) == 0)
{
                $val = 'A';
}
else {
                $val =  $fila[gen_tiempo_i];
}
 
if ($val == 'A' )
{
                $sql = " Call sp_insert_tiempo_incapacidad(".$year. ")";
}
else
{
                $sql = " Call sp_ins_t_incapacidad_x_fechaini(".$year. ", " . $mes . ", " . $dia .")";          
}
 
//$sql = " Call sp_insert_tiempo_incapacidad()";
//echo $sql, "<br>";

$res   = $db->query($sql);
 
//sc_alert("Proceso Terminado");
?>