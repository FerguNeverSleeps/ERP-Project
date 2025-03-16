<?php 
/*session_start();
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


function getIntegrantes($request)
{
    $query = "SELECT cedula,nombres, apellidos, ficha,foto,estado,descrip 
    FROM nomvis_integrantes 
    WHERE descrip = '".$_SESSION['nomina']."' and estado <> 'Egresado' 
    limit ".$request['start'].",".$request['limit'];

    $result = sql_ejecutar($query);
    // Realizar una consulta MySQL
    $datos = array();
    $i=0;
    while($fila = fetch_array($result))
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
$result = "";

switch ($method) {

    case 'GET':
    
        $result = getIntegrantes($_REQUEST);
        header('Cache-Control: no-cache, must-revalidate');
        header("content-type:application/json");
        echo(json_encode($result));
        break;

    case 'POST':
    break;
   
}*/

?>
