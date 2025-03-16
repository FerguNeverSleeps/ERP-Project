<?php

require_once('../lib/config.php'); // general.config.inc.php
require_once('../lib/common.php'); // funciones
include('../fpdf/numerosALetras.class.php');
include ("../header.php");
include ("../paginas/func_bd.php");
include ("../paginas/funciones_nomina.php"); 

$conexion = conexion();


$sql = "SELECT nc.codcon, nc.descrip, nc.tipcon, nc.formula 
        FROM nomconceptos nc WHERE nc.descrip LIKE '%COMPLEMENTO%'";

$sql = "SELECT 
(SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%COMPLEMENTO DE SUELDO%') AS complemento_sueldo,
(SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%PRIMA POR HOGAR%') AS prima_hogar,
 (SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%PRIMA POR ANTIGUEDAD%') AS prima_antiguedad,
 (SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%PRIMA PROFESIONAL%') AS prima_profesional,
 (SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%BONO ALIMENTICIO%') AS cesta_ticket,
  (SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip='SUELDO') AS sueldo,
  (SELECT formula FROM nomconceptos c, nomconceptos_tiponomina ct 
 WHERE  c.codcon=ct.codcon AND ct.codtip=tn.codtip AND descrip LIKE '%SUELDO BASICO%') AS sueldo_basico  
FROM   nomtipos_nomina tn
WHERE  tn.codtip=1";

$res = query($sql, $conexion);

if($fila = fetch_array($res)){
#    echo "//=============================================================================================<br>";
#    $complemento_sueldo = str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['complemento_sueldo']);
#    echo "<br><b>COMPLEMENTO DE SUELDO:</b><br><br>".$complemento_sueldo."<br>";
#    
#    echo "//=============================================================================================<br>";
#    $prima_hogar =  str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['prima_hogar']);
#    echo "<br><b>PRIMA HOGAR:</b><br><br>".$complemento_sueldo."<br>";
#
#    echo "//=============================================================================================<br>";
#    $prima_antiguedad =  str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['prima_antiguedad']);
#    echo "<br><b>PRIMA ANTIGUEDAD:</b><br><br>".$prima_antiguedad."<br>";
#
#    echo "//=============================================================================================<br>";
#    $prima_profesional =  str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['prima_profesional']);
#    echo "<br><b>PRIMA PROFESIONALIZACION:</b><br><br>".$prima_profesional."<br>";
#
#    echo "//=============================================================================================<br>";
#    $cesta_ticket =  str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['cesta_ticket']);
#    echo "<br><b>CESTA TICKET:</b><br><br>".$cesta_ticket."<br>";
#
#    echo "//=============================================================================================<br>";
#    $sueldo =  str_replace(array(';','*/', '/*') , array(';<br><br>','*/<br><br>','/*<br><br>'), $fila['sueldo']);
#    echo "<br><b>SUELDO:</b><br><br>".$sueldo."<br>";
}

echo "<br><br><br>";
echo "/*=============================================================================================*/<br>";
/*
$sql = "SELECT c.codcon, c.descrip, c.tipcon, c.formula
		FROM   nomconceptos_tiponomina ct, nomconceptos c
		WHERE  ct.codcon=c.codcon AND ct.codtip=1
		AND    (   c.descrip LIKE '%BONO ALIMENTICIO%'
			    OR c.descrip LIKE '%POR ANTIGUEDAD%' 
			    OR c.descrip LIKE '%PROFESIONAL%' 
			    OR c.descrip IN ('COMPLEMENTO DE SUELDO', 'PRIMA POR HOGAR', 'SUELDO', 'SUELDO BASICO')
                OR c.descrip LIKE '%DE SERVICIO%')
		ORDER BY c.codcon";*/

$sql = "SELECT c.codcon, c.descrip, c.tipcon, c.formula
		FROM   nomconceptos c
		WHERE  c.descrip LIKE '%SERVICIO%'
		ORDER BY c.codcon
		LIMIT 1";

//echo $sql;

$res =  query($sql, $conexion);
//$fila = fetch_array($res);

$SUELDO=1000;

while ($fila = fetch_array($res)) {
   echo "//===============================================================================<br>";
   echo "<b>".$fila['codcon']." - ".$fila['descrip'].'</b><br><br>';
   eval($fila['formula']);
   $formula = str_replace(';', ";<br><br>", $fila['formula']);
   echo $formula.'<br>';
   echo "MONTO => ".$MONTO."<br>";
}
