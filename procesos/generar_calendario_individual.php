<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

require_once('../ginteven/conexion.php');

//$ano = date("Y");
$ano=$_GET['ano'];
$aux = 0 ;
$consultay = "SELECT DISTINCT(YEAR(fecha)) as ano FROM nomcalendarios_tiposnomina";
$resultd = $conexion->query($consultay);

while($fetchv = mysqli_fetch_array($resultd))
{
    if($ano == $fetchv[0]) $aux = 1 ; 
}
if($aux == 1)
{
    $sql = "DELETE FROM nomcalendarios_personal WHERE YEAR(fecha)='".$ano."'";
    $res = $conexion->query($sql);

    $consultay = "SELECT ficha,turno_id FROM nompersonal";
    $resultd = $conexion->query($consultay);
    while($empleado = mysqli_fetch_array($resultd))
    {
        $consulta = "INSERT INTO nomcalendarios_personal (cod_empresa,ficha,fecha,dia_fiesta,turno_id) VALUES";
        for($i=1;$i<=12;$i++)
        { 
            $ultimodia = date("d",(mktime(0,0,0,$i+1,1,$ano)-1));
            for($j=1;$j <= $ultimodia; $j++ )
            { 
                $fecha = $ano."-".$i."-".$j;
                $sql = "SELECT cod_empresa,fecha,dia_fiesta FROM nomcalendarios_tiposnomina WHERE fecha = '$fecha'";
                $res = $conexion->query($sql);
                $data=mysqli_fetch_array($res);
                $consulta  .=  "('1','".$empleado['ficha']."','".$data['fecha']."','".$data['dia_fiesta']."','".$empleado['turno_id']."'),";
            }
        }
        $consulta .= '****';
        $consulta = str_replace(',****', ';', $consulta);
        if ($resultado = $conexion->query($consulta)) 
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