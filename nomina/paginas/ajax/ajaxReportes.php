<?php
require_once '../../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../../lib/common.php';

if( isset($_POST['accion']) AND ($_POST['accion'] == "AgregarReporte"))
{
    $bd = new bd($_SESSION['bd_nomina']);
    $cod_modulo  = $_POST['cod_modulo'] ? $_POST['cod_modulo'] : '';
    $descripcion = $_POST['descripcion'] ? $_POST['descripcion'] : '';
    $archivo     = $_POST['archivo'] ? $_POST['archivo'] : '';
    $activo      = $_POST['activo'] ? $_POST['activo'] : '';
    $icono       = $_POST['icono'] ? $_POST['icono'] : '';
    $orden       = $_POST['orden'] ? $_POST['orden'] : '';
    $SQL = "INSERT into nom_paginas (descripcion, archivo, activo, icono, orden, cod_modulo) values ('{$descripcion}', '{$archivo}', '{$activo}', '{$icono}', '{$orden}',  '{$cod_modulo}');";   
    try{
        $result = $bd->query($SQL);
        if($result){
            $array = array("success" => 1, "mensaje" => "Reporte Agregado con éxito");
            echo json_encode($array);
        }
    }
    catch(Exception $th){
        $mensaje = $th->getMessage();
        $array = array("success" => 0, "mensaje" => $mensaje);
        echo json_encode($array);
    }
}
if( isset($_POST['accion']) AND ($_POST['accion'] == "EditarReporte")){
    $bd = new bd($_SESSION['bd_nomina']);
    $cod_modulo  = $_POST['cod_modulo'] ? $_POST['cod_modulo'] : '';
    $descripcion = $_POST['descripcion'] ? $_POST['descripcion'] : '';
    $archivo     = $_POST['archivo'] ? $_POST['archivo'] : '';
    $activo      = $_POST['activo'] ? $_POST['activo'] : '';
    $icono       = $_POST['icono'] ? $_POST['icono'] : '';
    $orden       = $_POST['orden'] ? $_POST['orden'] : '';
    $id_pagina       = $_POST['id_pagina'] ? $_POST['id_pagina'] : '';
    $SQL = "UPDATE 
                nom_paginas 
            SET 
                descripcion = '{$descripcion}', 
                archivo = '{$archivo}', 
                activo = '{$activo}',
                icono= '{$icono}', 
                orden = '{$orden}',
                cod_modulo = '{$cod_modulo}')
            WHERE 
                id_pagina = '{$id_pagina}'";   
    try{
        $result = $bd->query($SQL);
        if($result){
            $array = array("success" => 1, "mensaje" => "Reporte Agregado con éxito");
            echo json_encode($array);
        }
    }
    catch(Exception $th){
        $mensaje = $th->getMessage();
        $array = array("success" => 0, "mensaje" => $mensaje);
        echo json_encode($array);
    }

}