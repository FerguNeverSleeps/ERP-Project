<?php
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";


$conexion=conexion();

$consulta="SELECT codigopr,descrip from nomprestamos ";
$result=query($consulta,$conexion);
 
$cantidad=501;
while($fila=fetch_array($result)){   
 $codigo=$fila['codigopr'];
 $formulax='$T01=CUOTAPRETIP($FICHA,$FECHANOMINA,$FECHAFINNOM,'.$codigo.');
$MONTO=$T01;';
 
	$update="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos` (
		`codcon` ,
		`descrip` ,
		`tipcon` ,
		`unidad` ,
		`ctacon` ,
		`contractual` ,
		`impdet` ,
		`proratea` ,
		`usaalter` ,
		`descalter` ,
		`formula` ,
		`modifdef` ,
		`markar` ,
		`tercero` ,
		`ccosto` ,
		`codccosto` ,
		`debcre` ,
		`bonificable` ,
		`htiempo` ,
		`valdefecto` ,
		`con_cu_cc` ,
		`con_mcun_cc` ,
		`con_mcuc_cc` ,
		`con_cu_mccn` ,
		`con_cu_mccc` ,
		`con_mcun_mccn` ,
		`con_mcuc_mccc` ,
		`con_mcun_mccc` ,
		`con_mcuc_mccn` ,
		`nivelescuenta` ,
		`nivelesccosto` ,
		`semodifica` ,
		`verref` ,
		`vermonto` ,
		`particular` ,
		`montocero` ,
		`ee` ,
		`fmodif` ,
		`aplicaexcel` ,
		`descripexcel` ,
		`ctacon1` ,
		`carga_masiva` ,
		`orden_reporte_contable`
		)
		VALUES (
		'".$cantidad."',  '".$fila['descrip']."',  'D',  'M',  '',  '1',  'S',  '0',  '0',  '',  '".$formulax."',  '0',  '0',  '0',  '0',  '0',  '',  '0',  '0',  '0.00',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '0',  '1',  '1',  '0',  '1',  '0',  '0',  '0', 'HSBC',  '578010010101001',  'N',  '0'
		);";

## Tipo de Planilla
$update1="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_tiponomina` (
`codcon` ,
`codtip` ,
`ee`
)
VALUES (
'".$cantidad."',  '1', NULL
);";
$update2="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_tiponomina` (
`codcon` ,
`codtip` ,
`ee`
)
VALUES (
'".$cantidad."',  '2', NULL
);";
$update3="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_tiponomina` (
`codcon` ,
`codtip` ,
`ee`
)
VALUES (
'".$cantidad."',  '3', NULL
);";
$update4="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_tiponomina` (
`codcon` ,
`codtip` ,
`ee`
)
VALUES (
'".$cantidad."',  '4', NULL
);";

# Frecuencia de Planilla
$update5="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_frecuencias` (
`codcon` ,
`codfre` ,
`ee`
)
VALUES (
'".$cantidad."',  '2', NULL
);";
$update6="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_frecuencias` (
`codcon` ,
`codfre` ,
`ee`
)
VALUES (
'".$cantidad."',  '3', NULL
);";

#Situaciones de Planilla
$update7="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_situaciones` (
`codcon` ,
`estado` ,
`ee`
)
VALUES (
'".$cantidad."',  'Activo', NULL
);";

$update8="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_situaciones` (
`codcon` ,
`estado` ,
`ee`
)
VALUES (
'".$cantidad."',  'Nuevo', NULL
);";

#Acumulado
$update9="INSERT INTO  `alcaldiasanmiguelito_planilla`.`nomconceptos_acumulados` (
`codcon` ,
`cod_tac` ,
`operacion` ,
`ee`
)
VALUES (
'".$cantidad."',  'CON', NULL , NULL
);";


#Insert de Todo
	$result_up=query($update,$conexion);
	$result_up=query($update1,$conexion);
	$result_up=query($update2,$conexion);
	$result_up=query($update3,$conexion);
	$result_up=query($update4,$conexion);
	$result_up=query($update5,$conexion);
	$result_up=query($update6,$conexion);
	$result_up=query($update7,$conexion);
	$result_up=query($update8,$conexion);
	$result_up=query($update9,$conexion);
	$cantidad++;
}
echo $cantidad;


?>
