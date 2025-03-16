<?php
session_start();
ob_start();
require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=new bd($_SESSION['bd']);

//var_dump($_REQUEST);exit;
//-------------------------------------------------------------------------------------------------------------------
function fecha_formato($fecha)
{
    if ( $fecha == "00-00-0000")
    {
        $fecha=date("Y-m-d");
    }else{
        $e = explode('-', $fecha);
        $fecha = $e[2]."-".$e[1]."-".$e[0];
    }
    return $fecha;
}
$fechar=date("Y-m-d"); 
//-------------------------------------------------------------------------------------------------------------------
//Cargando los datos del arhivo a una tabla temporal
if (isset($_POST['importar']))
{    

    //-------------------------------------------------------------------------------------------------------------------
    /*if($_FILES['archivo']["error"] > 0)
    { echo 1;exit;
        header("elegibles_list.php?msj=2");
    }else
    {   */

        $foto = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';
        if($foto!='')
        {
            $nombre_foto  = $_POST['cedula']."_".$foto;
            $dir_fotos    =  "../../includes/curriculos/";
            if (copy($_FILES['foto']['tmp_name'], $dir_fotos . $nombre_foto)) 
            {
                chmod( $dir_fotos . $nombre_foto, 0777);
                $insertFoto = $dir_fotos . $nombre_foto;
                //$setFoto    = "foto='". $dir_fotos . $nombre_foto."', ";
            } 
            else
                throw new Exception("Error al subir la foto", 1);                   
        }
        $archivo = isset($_FILES['archivo']['name']) ? $_FILES['archivo']['name'] : '';
        if($archivo!='')
        {
            $nombre_archivo  = "doc_".$_POST['cedula']."_".$archivo;
            $dir_archivo    =  "../../includes/curriculos/";
            if (copy($_FILES['archivo']['tmp_name'], $dir_archivo . $nombre_archivo)) 
            {
                chmod( $dir_archivo . $nombre_archivo, 0777);
                $insertarchivo = $dir_archivo . $nombre_archivo;
                //$setFoto    = "foto='". $dir_fotos . $nombre_foto."', ";
            } 
            else
                throw new Exception("Error al subir la foto", 1);                   
        }
        //---------------------------------------------------
        if ($_POST['importar'] == 'Aceptar')
        {
           /* $sql="INSERT INTO nomelegibles (
            cedula,nombres,nombres2,apellidos,apellidos_materno,apellidos_casada,sexo,fecnac,lugarnac,telefono,email,cod_profesion,grado_instruccion,area_desempenoanios_exp,observacion,fecha_reg,direccion,foto, archivo,tipo_sang,discapacidad,discapacidad_escifica,alergias_afecciones,nombre_padre,nombre_madre,nombre_conyugue,nombre_dep1,parentesco1,fecnac1,discapacidad1,nombre_dep2,parentesco2,fecnac2,discapacidad2,nacionalidad,seguro_social,
        ) VALUES (,'".$_POST['nacionalidad']."','".$_POST['seguro_social']."',,'".$_POST['nombres2']."','".$_POST['apellidos']."','".$_POST['apellidos_materno']."','".$_POST['apellidos_casada']."','".$_POST['sexo']."','".fecha_formato($_POST['fecnac'])."','".$_POST['lugarnac']."','".$_POST['telefono']."','".$_POST['email']."','".$_POST['cod_profesion']."','".$_POST['grado_instruccion']."','".$_POST['area_desempeno']."','".$_POST['anios_exp']."','".$_POST['observacion']."','".date('Y-m-d')."','".$_POST['direccion']."','".$foto."','".$archivo."','".$_POST['tipo_sang']."','".$_POST['discapacidad']."','".$_POST['discapacidad_escifica']."','".$_POST['alergias_afecciones']."','".$_POST['nombre_padre']."','".$_POST['nombre_madre']."','".$_POST['nombre_conyugue']."'    )";*/

        $sql="INSERT INTO nomelegibles(cedula,
        seguro_social,
        nombres,
        nombres2,
        apellidos,
        apellidos_materno,
        apellidos_casada,
        tipo_sang,
        sexo,
        fecnac,
        lugarnac,
        telefono,
        email,
        cod_profesion,
        grado_instruccion,
        area_desempeno,
        anios_exp,
        observacion,
        fecha_reg,
        direccion,
        foto,
        archivo,
        discapacidad,
        discapacidad_escifica,
        alergias_afecciones,
        nombre_padre,
        nombre_madre,
        nombre_conyugue,
        nombre_dep1,
        parentesco1,
        fecnac1,
        discapacidad1,
        nombre_dep2,
        parentesco2,
        fecnac2,
        discapacidad2,
        nombre_dep3,
        parentesco3,
        fecnac3,
        discapacidad3,
        nombre_dep4,
        parentesco4,
        fecnac4,
        discapacidad4,
        nombre_dep5,
        parentesco5,
        fecnac5,
        discapacidad5,
        nacionalidad,
        estado_civil,
        email_institucional,
        telefono_ext,
        fecha_registro,
        referencia1,
        tel_referencia1,
        email_referencia1,
        referencia2,
        tel_referencia2,
        email_referencia2,
        referencia3,
        tel_referencia3,
        email_referencia3) 
        VALUES ('".$_POST['cedula']."',
        '".$_POST['seguro_social']."',
        '".$_POST['nombres']."',
        '".$_POST['nombres2']."',
        '".$_POST['apellidos']."',
        '".$_POST['apellido2']."',
        '".$_POST['apellido3']."',
        '".$_POST['tip_sang']."',
        '".$_POST['sexo']."',
        '".fecha_formato($_POST['fecnac'])."',
        '".$_POST['lugarnac']."',        
        '".$_POST['telefono']."',
        '".$_POST['email']."',
        '".$_POST['cod_profesion']."',
        '".$_POST['grado_instruccion']."',
        '".$_POST['area_desempeno']."',
        '".$_POST['anios_exp']."',
        '".$_POST['observacion']."',
        '".$fechar."',
        '".$_POST['direccion']."',
        '".$insertFoto."',
        '".$insertarchivo."',
        '".$_POST['_incapacidad']."',
        '".$_POST['incapacidad_esp']."',
        '".$_POST['alergias_afecciones']."',
        '".$_POST['nombre_padre']."',
        '".$_POST['nombre_madre']."',
        '".$_POST['nombre_conyugue']."',
        '".$_POST['nombre_dep1']."',
        '".$_POST['parentesco1']."',
        '".fecha_formato($_POST['fecnac1'])."',
        '".$_POST['discapacidad1']."',
        '".$_POST['nombre_dep2']."',
        '".$_POST['parentesco2']."',
        '".fecha_formato($_POST['fecnac2'])."',
        '".$_POST['discapacidad2']."',
        '".$_POST['nombre_dep3']."',
        '".$_POST['parentesco3']."',
        '".fecha_formato($_POST['fecnac3'])."',
        '".$_POST['discapacidad3']."',
        '".$_POST['nombre_dep4']."',
        '".$_POST['parentesco4']."',
        '".fecha_formato($_POST['fecnac4'])."',
        '".$_POST['discapacidad4']."',
        '".$_POST['nombre_dep5']."',
        '".$_POST['parentesco5']."',
        '".fecha_formato($_POST['fecnac5'])."',
        '".$_POST['discapacidad5']."',
        '".$_POST['nacionalidad']."',
        '".$_POST['estado_civil']."',
        '".$_POST['email_institucional']."',
        '".$_POST['telefono_ext']."',
        '".$fechar."',
        '".$_POST['referencia1']."',
        '".$_POST['tel_referencia1']."',
        '".$_POST['email_referencia1']."',
        '".$_POST['referencia2']."',
        '".$_POST['tel_referencia2']."',
        '".$_POST['email_referencia2']."',
        '".$_POST['referencia3']."',
        '".$_POST['tel_referencia3']."',
        '".$_POST['email_referencia3']."')";

        }else{
           /* $sql="UPDATE nomelegibles SET cedula='".$_POST['cedula']."',apellidos='".$_POST['apellidos']."',nombres='".$_POST['nombres']."',sexo='".$_POST['sexo']."',fecnac='".fecha_formato($_POST['fecnac'])."',lugarnac='".$_POST['lugarnac']."',telefono='".$_POST['telefono']."',email='".$_POST['email']."',cod_profesion='".$_POST['cod_profesion']."',grado_instruccion='".$_POST['grado_instruccion']."',area_desempeno='".$_POST['area_desempeno']."',anios_exp='".$_POST['anios_exp']."',observacion='".$_POST['observacion']."',fecha_reg='".date('Y-m-d')."',direccion='".$_POST['direccion']."',foto='".$foto."',archivo='".$archivo."' WHERE cedula = '".$_POST['cedula_ant']."'";*/
           $sql="UPDATE nomelegibles SET 
           cedula                ='".$_POST['cedula']."',
           seguro_social         ='".$_POST['seguro_social']."',
           nombres               ='".$_POST['nombres']."',
           nombres2              ='".$_POST['nombres2']."',
           apellidos             ='".$_POST['apellidos']."',
           apellidos_materno     ='".$_POST['apellidos2']."',
           apellidos_casada      ='".$_POST['apellidos3']."',
           tipo_sang             ='".$_POST['tipo_sang']."',
           sexo                  ='".$_POST['sexo']."',
           fecnac                ='".$_POST['fecnac']."',
           lugarnac              ='".$_POST['lugarnac']."',
           telefono              ='".$_POST['telefono']."',
           email                 ='".$_POST['email']."',
           cod_profesion         ='".$_POST['cod_profesion']."',
           grado_instruccion     ='".$_POST['grado_instruccion']."',
           area_desempeno        ='".$_POST['area_desempeno']."',
           anios_exp             ='".$_POST['anios_exp']."',
           observacion           ='".$_POST['observacion']."',
           fecha_reg             ='".$_POST['fecha_reg']."',
           direccion             ='".$_POST['direccion']."',
           foto                  ='".$insertFoto."',
           archivo               ='".$insertarchivo."',
           discapacidad          ='".$_POST['discapacidad']."',
           discapacidad_escifica ='".$_POST['discapacidad_escifica']."',
           alergias_afecciones   ='".$_POST['alergias_afecciones']."',
           nombre_padre          ='".$_POST['nombre_padre']."',
           nombre_madre          ='".$_POST['nombre_madre']."',
           nombre_conyugue       ='".$_POST['nombre_conyugue']."',
           nombre_dep1           ='".$_POST['nombre_dep1']."',
           parentesco1           ='".$_POST['parentesco1']."',
           fecnac1               ='".$_POST['fecnac1']."',
           discapacidad1         ='".$_POST['discapacidad1']."',
           nombre_dep2           ='".$_POST['nombre_dep2 ']."',
           parentesco2           ='".$_POST['parentesco2']."',
           fecnac2               ='".$_POST['fecnac2']."',
           discapacidad2         ='".$_POST['discapacidad2']."',
           nombre_dep3           ='".$_POST['nombre_dep3']."',
           parentesco3           ='".$_POST['parentesco3']."',
           fecnac3               ='".$_POST['fecnac3']."',
           discapacidad3         ='".$_POST['discapacidad3']."',
           nombre_dep4           ='".$_POST['nombre_dep4']."',
           parentesco4           ='".$_POST['parentesco4']."',
           fecnac4               ='".$_POST['fecnac4']."',
           discapacidad4         ='".$_POST['discapacidad4']."',
           nombre_dep5           ='".$_POST['nombre_dep5']."',
           parentesco5           ='".$_POST['parentesco5']."',
           fecnac5               ='".$_POST['fecnac5']."',
           discapacidad5         ='".$_POST['discapacidad5']."',
           referencia1           ='".$_POST['referencia1']."',
           tel_referencia1       ='".$_POST['tel_referencia1']."',
           email_referencia1     ='".$_POST['email_referencia1']."',
           referencia2           ='".$_POST['referencia2']."',
           tel_referencia2       ='".$_POST['tel_referencia2']."',
           email_referencia2     ='".$_POST['email_referencia2']."',
           referencia3           ='".$_POST['referencia3']."',
           tel_referencia3       ='".$_POST['tel_referencia3']."',
           email_referencia3     ='".$_POST['email_referencia3']."' 
           WHERE cedula = '".$_POST['cedula_ant']."'";
        }
        if ($res = $conexion->query($sql, "utf8"))
        {
                header("location:elegibles_list.php?msj=5");

            /*if ( (move_uploaded_file($_FILES['archivo']['tmp_name'],$ubicacion) ) || (move_uploaded_file($_FILES['foto']['tmp_name'],$ubicacion_foto) ) )
            {
                header("location:elegibles_list.php?msj=5");
            }else{
                header("location:elegibles_list.php?msj=4");
            }*/
        }else{
            header("location:elegibles_list.php?msj=4");
        }
    //}
}else{
    header("location:elegibles_list.php?msj=1");
}
?>
