<?php

session_start();

error_reporting(E_ALL);

$db_user   = root;
$db_pass   = haribol;
$db_name   = asamblea_rrhh;
$db_host   = localhost;


$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

$consulta_familiar="SELECT * FROM rrhh_familiares";               
$resultado_familiar=mysqli_query($conexion,$consulta_familiar);
//$fetch_baja=mysqli_fetch_array($resultado_movimientos); 
//echo '<br><br>Familiares: <table border="1">';
//echo '<tbody>';
//echo '<tr>';
//echo '<td>num</td><td>id empleado</td><td>cedula</td><td>nombre madre</td><td>nombre padre</td><td>nombre conyuge</td><td>vive madre</td><td>vive padre</td><td>conyuge lugar trabajo</td>';
//echo '<td>conyuge telefono</td><td>persona emergencia</td><td>telefono emergencia</td><td>fecha creacion</td><td>creador por</td><td>fecha modificacion</td><td>modificado por</td><td>id familiar</td>';
//echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_familiar))
{
    
    $id_empleado=$fila['id_empleado'];
    $consultap="SELECT * FROM nompersonal WHERE personal_id='$id_empleado'";   
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);                
    $cedula=$fetchCon['cedula'];
    $ficha=$fetchCon['ficha'];
    $tipnom=$fetchCon['tipnom'];
    
    $nombre_madre=utf8_decode($fila['nombre_madre']);
    $nombre_padre=utf8_decode($fila['nombre_padre']);
    $nombre_conyuge=utf8_decode($fila['nombre_conyuge']);
    $vive_madre=$fila['vive_madre'];
    $vive_padre=$fila['vive_padre'];
    $conyuge_lugar_trabajo=$fila['conyuge_lugar_trabajo'];
    $conyuge_telefono=$fila['conyuge_telefono'];
    $persona_emergencia=$fila['persona_emergencia'];
    $telefono_emergencia=$fila['telefono_emergencia'];
    $fecha_creacion=$fila['fecha_creacion'];
    $creado_por=$fila['creado_por'];
    $fecha_modificacion=$fila['fecha_modificacion'];
    $modificado_por=$fila['modificado_por'];
    $id_familiar=$fila['idFamiliar'];
    
//     echo '<tr>';
//    echo "<td>".$cont."</td>";
//    echo "<td>".$id_empleado."</td>";   
//    echo "<td>".$cedula."</td>";
//    echo "<td>".$nombre_madre."</td>";
//    echo "<td>".$nombre_padre."</td>";
//    echo "<td>".$nombre_conyuge."</td>";
//    echo "<td>".$vive_madre."</td>";
//    echo "<td>".$vive_padre."</td>";
//    echo "<td>".$conyuge_lugar_trabajo."</td>";
//    echo "<td>".$conyuge_telefono."</td>";
//    echo "<td>".$persona_emergencia."</td>";
//    echo "<td>".$telefono_emergencia."</td>";    
//    echo "<td>".$fecha_creacion."</td>";
//    echo "<td>".$creado_por."</td>";
//    echo "<td>".$fecha_modificacion."</td>"; 
//    echo "<td>".$modificado_por."</td>";    
//    echo "<td>".$id_familiar."</td>"; 
//    echo '</tr>';
    
    $discapacidad=0;
    
    $longitud_madre = strlen ($nombre_madre );
    //echo "<br>Longitud madre: "; echo $longitud_madre;
    $longitud_padre = strlen ($nombre_padre );
    //echo "<br>Longitud padre: "; echo $longitud_padre;
    $longitud_conyuge = strlen ($nombre_conyuge );
    //echo "<br>Longitud conyuge: "; echo $longitud_conyuge;
    
    if($longitud_madre!=1)
    {
        $parentesco=1;
        $s="Femenino";
        //$nombre_madre = utf8_decode($nombre_madre);
        $consulta_familiar="INSERT INTO nomfamiliares
                    (correl, cedula, ficha, nombre, codpar, sexo, tipnom, cedula_beneficiario, vive, discapacidad )
                    VALUES  
                    ('','{$cedula}','{$ficha}','{$nombre_madre}','{$parentesco}','{$s}','{$tipnom}','{$cedula}','{$vive_madre}','{$discapacidad}')";
        //$resultado_familiar=mysqli_query($conexion,$consulta_familiar);
        echo "<br>"; echo $consulta_familiar; echo ";";
    }
    
    if($longitud_padre!=1)
    {
        $parentesco=2;
        $s="Masculino"; 
        //$nombre_padre = utf8_decode($nombre_padre);
        $consulta_familiar="INSERT INTO nomfamiliares
                    (correl, cedula, ficha, nombre, codpar, sexo, tipnom, cedula_beneficiario, vive, discapacidad )
                    VALUES  
                    ('','{$cedula}','{$ficha}','{$nombre_padre}','{$parentesco}','{$s}','{$tipnom}','{$cedula}','{$vive_padre}','{$discapacidad}')";
        //$resultado_familiar=mysqli_query($conexion,$consulta_familiar);
        echo "<br>"; echo $consulta_familiar; echo ";";
    }
    
    if($longitud_conyuge!=1)
    {
        $parentesco=4;     
        $vive=1;
        //$nombre_conyuge = utf8_decode($nombre_conyuge);
        $consulta_familiar="INSERT INTO nomfamiliares
                    (correl, cedula, ficha, nombre, codpar,tipnom, cedula_beneficiario, vive, discapacidad )
                    VALUES  
                    ('','{$cedula}','{$ficha}','{$nombre_conyuge}','{$parentesco}','{$tipnom}','{$cedula}','{$vive}','{$discapacidad}')";
        //$resultado_familiar=mysqli_query($conexion,$consulta_familiar);
        echo "<br>"; echo $consulta_familiar; echo ";";
    }
       
    $cont++;
}
echo '</tbody>';
echo '</table>';



?>