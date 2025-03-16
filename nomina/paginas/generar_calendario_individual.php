<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

require_once '../lib/common.php';
$conexion=conexion();
//$ano = date("Y");
$ano=$_GET['ano'];
$aux = 0 ;
$consulta_calendarios_nomina = "SELECT DISTINCT(YEAR(fecha)) as ano FROM nomcalendarios_tiposnomina";

$resultado_calendarios_nomina=query($consulta_calendarios_nomina,$conexion);
//echo $consulta_calendarios_nomina;
//exit;

while($fetch_calendarios_nomina=fetch_array($resultado_calendarios_nomina))
{
    if($ano == $fetch_calendarios_nomina[0]) $aux = 1 ; 
}

if($aux == 1)
{
    $sql_delete = "DELETE FROM nomcalendarios_personal WHERE YEAR(fecha)='".$ano."'";
 
    $resultado_delete=query($sql_delete,$conexion);

    $consulta_personal = "SELECT ficha,turno_id FROM nompersonal "
                        . " WHERE `estado` NOT LIKE '%Egresado%'";
    $resultado_personal=query($consulta_personal,$conexion);
//    echo $consulta_personal;
    while($empleado = fetch_array($resultado_personal))
    {
        $consulta = "INSERT INTO nomcalendarios_personal (cod_empresa,ficha,fecha,dia_fiesta,turno_id) VALUES";
        for($i=1;$i<=12;$i++)
        { 
            $ultimodia = date("d",(mktime(0,0,0,$i+1,1,$ano)-1));
            for($j=1;$j <= $ultimodia; $j++ )
            { 
                $fecha = $ano."-".$i."-".$j;
                $sql = "SELECT cod_empresa,fecha,dia_fiesta FROM nomcalendarios_tiposnomina WHERE fecha = '$fecha'";
                $resultado_calendario=query($sql,$conexion);
                $data=fetch_array($resultado_calendario);
                $consulta  .=  "('".$data['cod_empresa']."','".$empleado['ficha']."','".$data['fecha']."','".$data['dia_fiesta']."','".$empleado['turno_id']."'),";
            }
        }
        $consulta .= '****';
        $consulta = str_replace(',****', ';', $consulta);
        if ($resultado = query($consulta,$conexion)) 
        {
            echo '<script type="text/javascript">
                alert("Calendario Personal generado Exitosamente.");
                document.location.href = "calendarios.php?ano='.$ano.'";</script>';
        }else{
            echo '<script type="text/javascript">
                alert("Eror al generar Calendario Personal.");
                document.location.href = "calendarios.php?ano='.$ano.'";</script>';
        }
    }
}else{
    echo '<script type="text/javascript">
                alert("No ha sido Generado calendario Empresa.");
                document.location.href = "calendarios.php?ano='.$ano.'";</script>';
}
?>