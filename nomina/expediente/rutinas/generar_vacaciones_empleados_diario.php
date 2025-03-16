<?php
include('/var/www/html/amaxonia_rrhh/general.config.inc.php');
include('funciones_vacaciones.php');
 
error_reporting(E_ALL);

$db_user   = "ginteven";
$db_pass   = "g1nt3v3n";
$db_name   = "asamblea";
$db_host   = "172.17.145.86";

//$db_user   = "root";
//$db_pass   = "haribol";
//$db_name   = "asamblea_rrhh";
//$db_host   = "localhost";


$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

//CASO AUTOMATICO DIARIO
$fecha_acreditacion = strtotime("+1 month", strtotime(date('Y-m-d')));
$fecha_acreditacion = date ( 'Y-m-d' , $fecha_acreditacion );

//CASO MANUAL DIARIO
//$fecha_acreditacion = strtotime("+1 month", strtotime("2017-08-23"));
//$fecha_acreditacion = date ( 'Y-m-d' , $fecha_acreditacion );
    
//CASO MANUAL DIARIO
//    $fecha_acreditacion = "2017-01-01";
    
$dias = $restante = 30;
$horas = $minutos = 0;
$usuario = 'admin';
$anio_acreditacion = date("Y", strtotime($fecha_acreditacion));
$mes_acreditacion = date("m", strtotime($fecha_acreditacion));
$dia_acreditacion = date("d", strtotime($fecha_acreditacion));

//echo $fecha_acreditacion;
//echo "AÑO: "; echo $anio_acreditacion; echo " MES: "; echo $mes_acreditacion; echo " DIA: "; echo $dia_acreditacion; 
//exit;


$consulta_personal="SELECT personal_id, estado, inicio_periodo, fin_periodo, cedula, apenom, tipnom, nomposicion_id, fecha_permanencia, useruid, usuario_workflow "
        . "FROM nompersonal WHERE `estado` NOT LIKE '%Baja%' AND `estado` NOT LIKE '%PENSIONADO POR INVALIDEZ%' "
        . "AND `estado` NOT LIKE '%Licencia sin sueldo%' AND `estado` NOT LIKE '%Licencia sin Sueldo%'"
        . "AND `estado` NOT LIKE '%Licencias sin sueldo%' AND `estado` NOT LIKE '%Licencias sin Sueldo%'"
        . "AND `estado` NOT LIKE '%Licencia Por Riesgos Profesionales%' AND `estado` NOT LIKE '%Licencias Especiales Riesgos Profesionales%' AND `estado` NOT LIKE '%Licencia Por Riesgos Profesion%'" 
        . "AND `tipemp` LIKE '%Fijo%' AND `tipo_funcionario` IN ( 1, 20, 18) "
        . "AND `tipnom` IN ( 1, 2 ) "
        . "AND DAY(fecha_permanencia) = '{$dia_acreditacion}' AND MONTH(fecha_permanencia) = '{$mes_acreditacion}'"
        . "AND TIMESTAMPDIFF(MONTH, fecha_permanencia, '{$fecha_acreditacion}')>=11";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{          
    $personal_id=$fila['personal_id'];
    $cedula=$fila['cedula'];
    $apenom=$fila['apenom'];
    $tipnom=$fila['tipnom'];
    $nomposicion_id=$fila['nomposicion_id'];
    $fecha_permanencia=$fila['fecha_permanencia'];
    $usr_uid=$fila['useruid'];
    $cod_user=$fila['usuario_workflow'];
    $estado=$fila['estado'];
    $inicio_periodo=$fila['inicio_periodo'];
    $fin_periodo=$fila['fin_periodo'];
    $acumula=1;

    if($estado==="Licencia Por Riesgos Profesion" || $estado==="Licencia Por Riesgos Profesionales" || $estado==="Licencias Especiales Riesgos Profesionales")
    {
        $dias=dias_transcurridos($inicio_periodo,$fin_periodo);
        if($dias>120)
        {
            $acumula=0;
        }
    }

    if($acumula==1)
    {
    
        $anio_permanencia = date("Y", strtotime($fecha_permanencia));
        $mes_permanencia = date("m", strtotime($fecha_permanencia));
        $dia_permanencia = date("d", strtotime($fecha_permanencia));  

        if($dia_permanencia>1)
        {
            $dia_fin_periodo = $dia_permanencia-1;
            $mes_fin_periodo = $mes_permanencia-1;
        }
        else
        {
            $mes_fin_periodo = $mes_permanencia-2;
            $dia_fin_periodo=cantidad_dias_mes($mes_fin_periodo,$anio_acreditacion);

        }
        $anio_ini_per=$anio_acreditacion-1;
        //SE GENERA FECHA INICIO PERIODO LEGAL A PARTIR DEL MES Y DIA DE LA FECHA DE PERMANENCIA
        $fecha_ini_per = $anio_ini_per."-".$mes_permanencia."-".$dia_permanencia;
        $fecha_inicio_periodo = date('Y-m-d', strtotime($fecha_ini_per));

        $anio_fin_per=$anio_acreditacion;
        //SE GENERA FECHA FIN PERIODO LEGAL A PARTIR DEL MES Y DIA DE LA FECHA DE PERMANENCIA
        $fecha_fin_per = $anio_fin_per."-".$mes_fin_periodo."-".$dia_fin_periodo;
        $fecha_fin_periodo = date('Y-m-d', strtotime($fecha_fin_per));

        //BUSCAMOS SI EL PERIODO YA FUE GENERADO
        $consulta_periodos="SELECT YEAR(fini_periodo) AS ini, YEAR(ffin_periodo) AS fin FROM periodos_vacaciones "
                            . "WHERE cedula = '{$cedula}' HAVING (ini = '{$anio_ini_per}' AND fin = '{$anio_fin_per}')";

        $resultado_periodos=mysqli_query($conexion,$consulta_periodos);

        $cantidad = mysqli_num_rows($resultado_periodos);

        if($cantidad==0)
        {

            $tipo=11;     
            $subtipo=112;    
            $estatus=1;

            $fecha_creacion = date('Y-m-d');

            echo "ID: "; echo $personal_id;
            echo " - CEDULA: "; echo $cedula;
            echo " - APELLIDOS Y NOMBRE: "; echo $apenom;
            echo " - POSICION: "; echo $nomposicion_id;
            echo " - FECHA INGRESO: "; echo $fecha_permanencia;
            echo " - DIA INGRESO: "; echo $dia_permanencia;
            echo " - MES INGRESO: "; echo $mes_permanencia;
            echo " - AÑO INGRESO: "; echo $anio_permanencia;
            echo " - FECHA REGISTRO: "; echo $fecha_acreditacion;
            echo " - DIA REGISTRO: "; echo $dia_acreditacion;
            echo " - MES REGISTRO: "; echo $mes_acreditacion;
            echo " - AÑO REGISTRO: "; echo $anio_acreditacion;
            echo " - FECHA INICIO: "; echo $fecha_inicio_periodo;
            echo " - FECHA FIN: "; echo $fecha_fin_periodo;
            echo "<br>";
            echo "<br>";

            $descripcion="VACACIONES GENERADAS AUTOMATICAMENTE POR EL SISTEMA - FECHA: ". $fecha_acreditacion . " PERIODO: 2016-2017";   

            //INSERCIÓN EXPEDIENTE
            $consulta_expediente="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha_inicio_periodo, fecha_fin_periodo, fecha,
                        dias, restante, tipo, estatus, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$descripcion}', '{$fecha_inicio_periodo}','{$fecha_fin_periodo}', "
                        . "'{$fecha_acreditacion}','{$dias}','{$restante}','{$tipo}','{$estatus}','{$fecha_creacion}','{$usuario}')";
            $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    //        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
    //        echo "<br>";
    //        echo "<br>";
            $ultimo_id_expediente = mysqli_insert_id($conexion);

            $tipo_justificacion=7;
            //INSERCION DIAS INCAPACIDAD
            $tiempo = 30;//$dias*8;
            $consulta_incapacidad="INSERT INTO dias_incapacidad
                    (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, 
                    dias, horas, minutos, idparent, cedula)
                    VALUES  
                    ('','{$nomposicion_id}','{$tipo_justificacion}', '{$fecha_acreditacion}', '{$tiempo}', '{$descripcion}', '', '', '{$usr_uid}',"
                    . "'{$fecha_fin_periodo}', '{$dias}', '{$horas}','{$minutos}','{$ultimo_id_expediente}', '{$cedula}')";
            $resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
    //        echo "CONSULTA DIAS INCAPACIDAD: "; echo $consulta_incapacidad; 
    //        echo "<br>";
    //        echo "<br>";
            $ultimo_id_incapacidad = mysqli_insert_id($conexion);

            //INSERCION EN PERIODOS VACACIONES
            $consulta_vacaciones="INSERT INTO periodos_vacaciones
                    (id_periodo_vacacion, usr_uid, fini_periodo, ffin_periodo, dias,
                    saldo, fecha_efectivas, fecha_creacion, usuario_creacion, id_dias_incapacidad, cod_expediente_det, cedula)
                    VALUES  
                    ('','{$usr_uid}','{$fecha_inicio_periodo}', '{$fecha_fin_periodo}',"
                    . "'{$dias}', '{$restante}', '{$fecha_inicio}','{$fecha_acreditacion}','{$usuario}','{$ultimo_id_incapacidad}',"
                    . "'{$ultimo_id_expediente}', '{$cedula}')";
            $resultado_vacaciones=mysqli_query($conexion,$consulta_vacaciones);
    //        echo "CONSULTA VACACIONES: "; echo $consulta_vacaciones; 
    //        echo "<br>";
    //        echo "<br>";
    //        
    //        exit;

            $cont++;
        }
    }
}
if($cont==1)
{
    echo "NO SE ENCONTRARON REGISTROS PARA ESTA FECHA";
    exit;
}
?>
