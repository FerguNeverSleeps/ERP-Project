<?php
session_start();
ob_start();
//$termino=$_SESSION['termino'];
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");
$conexion=conexion();


$ficha = $_GET['ficha'] ; 
$encabezado = "SELECT  descrip FROM `nomtipos_nomina`  where codtip ='".$_SESSION['codigo_nomina']."'" ;

$resultado_encabezado=query($encabezado,$conexion);
$encabezado_final = fetch_array($resultado_encabezado) ;
$encabezado_f = $encabezado_final[0] ;
$hoy = date("Y-m-d"); 

$consulta="select   A.ficha ,concat(C.nombres,' ',C.apellidos),H.descrip, D.fechapago ,sum(A.monto) from nom_movimientos_nomina A join nomconceptos_acumulados B on (A.codcon = B.codcon and B.cod_tac = 'SI')  join nompersonal C on (C.ficha =A.ficha)  join nom_nominas_pago D on (A.codnom = D.codnom)
 join nomfrecuencias H on (H.codfre = D.frecuencia)
where A.ficha = '".$ficha."' and A.tipnom='".$_SESSION['codigo_nomina']."' and D.tipnom='".$_SESSION['codigo_nomina']."' GROUP BY D.fechapago
";
$resultado_tiping=query($consulta,$conexion);
$aux = 0 ;
$total = 0 ;
while($fetch=fetch_array($resultado_tiping)) {
	if($aux == 0)
	{
		$aux = 1 ;

	$tbHtml = '<table border=1>
             <header>
         
             <tr><td colspan ="4" ></td></tr>
             <tr><td colspan ="4" align="center" >'.$encabezado_f.'</td></tr>
             <tr><td colspan ="4" align="center"  >FECHA '.$hoy.'</td></tr>
             <tr><td colspan ="4" align="center"><strong>Ficha</strong> : '.$fetch[0].' <strong>Nombre : </strong>'.$fetch[1].'</td></tr>
               <tr>
                  <th></th>
                  <th>Frecuencia</th>
                  <th style="width:100px">Fecha</th>
                  <th style="width:100px">Monto</th>
                </tr>
            </header>';




	}	
	 
  $tbHtml .= "<tr><td></td><td>".$fetch[2].'</td><td>'.$fetch[3].'</td><td align="right">'.number_format($fetch[4],2)."</td></tr>";
$total += $fetch[4] ; 

}  
$tbHtml .= '<tr><td></td><td></td><td><strong>Total</strong></td><td align="right">$'.number_format ($total,2)."</td></tr>";

$tbHtml .= "</html>";

header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=acumulados.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $tbHtml;