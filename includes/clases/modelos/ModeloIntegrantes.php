<?php 
session_start();
ini_set("display_errors", false);
ini_set("html_errors", false);
    include ("func_bd.php");
    include ("../lib/common.php");
class Integrantes {
    function __construct($cedula, $nombres, $apellidos, $ficha, $foto,$estado,$descrip) {
        $this->cedula = $cedula;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->ficha = $ficha;
        $this->foto = $foto;
        $this->estado = $estado;
        $this->descrip = $descrip;
    }
}


function getIntegrantes()
{
       $link = mysqli_connect('localhost', 'root', '','planillaexpress') or die('No se pudo conectar: ' . mysqli_error($link));

    $query = "SELECT cedula,nombres, apellidos, ficha,foto,estado,descrip FROM nomvis_integrantes WHERE descrip = '".$_SESSION['nomina']."' and estado <> 'Egresado'";

   $result = mysqli_query($link, $query) or die(' Consulta fallida: ' . mysqli_error($link));
    $datos = array();
    $i=0;
    while($fila = mysqli_fetch_array($result))
    {
        $datos[$i]["cedula"] = $fila[cedula];
        $datos[$i]["apelidosynombres"] = $fila[apellidos].' , '.$fila[nombres];
        $datos[$i]["ficha"] = $fila[ficha];
        if(strlen($fila['foto'])>=7)            
        {$datos[$i]["foto"] = $fila[foto];}
        else
        {$datos[$i]["foto"]='';}
        $datos[$i]["estado"] = $fila[estado];
        $datos[$i]["descrip"] = $fila[descrip];
        $i++;
    }
    return  $datos;

}

$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'];
$result = '';

switch ($method) {

    case 'GET':
    
        $result = getIntegrantes();
        header('Cache-Control: no-cache, must-revalidate');
        header("content-type:application/json");
        echo(json_encode($result));
    break;
    case 'POST':
        switch ($accion) {
    
            case 'create':
            
                $modelCar = json_decode($requestBody);
                // TODO: Save $modelCar in the database.
            
                $result = array('success' => true, 'accion' => 'create', 'modelCar' => $modelCar);
                header('Cache-Control: no-cache, must-revalidate');
                header('content-type:application/json');
                echo(json_encode($result));
    
            break;
    
            case 'update':
        
                $updatedData = json_decode($requestBody);
                // TODO: Save $updatedData in the database.
            
                $result = array('success' => true, 'accion' => 'update', 'updatedData' => $updatedData);
                header('Cache-Control: no-cache, must-revalidate');
                header('content-type:application/json');
                echo(json_encode($result));
    
            break;
        
            case 'destroy':
        
                $deletedData = json_decode($requestBody);
                // TODO: Delete $deletedData from the database.
            
                $result = array('success' => true, 'accion' => 'destroy', 'deletedData' => $deletedData);
                header('Cache-Control: no-cache, must-revalidate');
                header('content-type:application/json');
                echo(json_encode($result));
        
            break;
    
        }
   
}

?>
