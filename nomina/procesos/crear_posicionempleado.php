<?php 

require_once "../lib/common.php";
$conexion=conexion();
 
 
// Leo todos los Funcionarios para crearles su posicion empleado en la tabla y que se ejecute el Trigger
$consulta="SELECT * FROM nompersonal";
$result_per=query($consulta,$conexion);
 
while($fetch_per=fetch_array($result_per))
{

//insert  posicionempleado
	$sqlinsertarposicionempleado="INSERT INTO `posicionempleado`
	(`IdEmpleado`,
	 `FechaInicio`,
	`Posicion`, 
	`FechaFin`,
	`IdFuncion`,
	`Planilla`,
	`CuentaContable`,
	`IdDepartamento`,
	`IdTituloInstitucional`,
	`IdTipoEmpleado`,
	`Salario`,
	`gastos_repre`,
     `Resolucion`,
     `fecha_decre`,
     `antiguedad`,
     `zona_apartada`,
     `jefaturas`,
     `especialidad`,
     `otros`,
     `decre_nombra`)
        VALUES ('".$fetch_per['ficha']."',
            '".$fetch_per['fecing']."',
            '".$fetch_per['nomposicion_id']."',
            '".$fecharetiro."',
            '".$fetch_per['nomfuncion_id']."',
            '".$fetch_per['tipnom']."',
            '".$fetch_per['cuentacontable_estructura']."',
            '".$fetch_per['IdDepartamento']."',
            '".$fetch_per['codcargo']."',
            '".$fetch_per['tipo_empleado']."',
            '".$fetch_per['suesal']."',
            '".$fetch_per['gastos_representacion']."',
            NULLIF('". $fetch_per['num_decreto'] ."',''),
            NULLIF('".$fecha_decreto."',''),
            '".$fetch_per['antiguedad']."',
            '".$fetch_per['zona_apartada']."',
            '".$fetch_per['jefaturas']."',
            '".$fetch_per['especialidad']."',
            '".$fetch_per['otros']."',
            '".$fetch_per['num_decreto']."' 
    )";
    $resultado=query($sqlinsertarposicionempleado,$conexion); 
	
	 
}

?>