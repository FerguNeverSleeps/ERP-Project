<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';
require_once '../../paginas/func_bd.php';

$conexion = conexion();

$cedula = $_GET['cedula'];
$editar = $_GET['editar'];
$codigo = $_GET['codigo'];
$subtipo = $_GET['subtipo'];
$aprobado = $_GET['aprobado'];

$sql_emp = "SELECT  *
    FROM   nompersonal 
    WHERE  cedula='{$cedula}'";

$resultado_emp=query($sql_emp,$conexion);
$fetch_emp=fetch_array($resultado_emp,$conexion); 


$sql_subtipo = "SELECT  *
    FROM   expediente_subtipo 
    WHERE  id_expediente_subtipo='{$subtipo}'";
//echo $sql_subtipo;
$resultado_subtipo=query($sql_subtipo,$conexion);
$fetch_subtipo=fetch_array($resultado_subtipo,$conexion); 

if($editar==1)
{
    $sql_expediente = "SELECT  *
        FROM   expediente 
        WHERE  cod_expediente_det='{$codigo}'";
    //echo $sql_expediente;

    $resultado_expediente=query($sql_expediente,$conexion);
    $fetch_expediente=fetch_array($resultado_expediente,$conexion); 

    $periodo_vacacion = $fetch_expediente['periodo_vacacion'];
    $resuelto = $fetch_expediente['resuelto'];

}

if($subtipo!=150)
{
    if ($editar ==1 and $aprobado) {
        include '../prestamos_editar.php';
    } else {
        include '../prestamos_agregar.php';
    }
    
}else{
    echo ' 
        <h2>NO SE APLICARA NINGUN PRESTAMO</h2>
            
    ';
}
/*
echo ' 
    <div class="form-group">
        <div class="col-md-4"></div>
        <div class="col-md-6">
            <label> Disfrute
                <input type="radio" value="1" name="tipo" id="tipo1" />
                <span></span>
            </label>
            <label> Disfrute y Pagados
                <input type="radio" value="2" name="tipo" id="tipo2" checked />
                <span></span>
            </label>
            <label> Pagados
                <input type="radio" value="3" name="tipo" id="tipo3" />
                <span></span>
            </label>
                <label> Vac. Acumuladas
                <input type="radio" value="4" name="tipo" id="tipo4" />
                <span></span>
            </label>
        </div>
    </div>
';*/
?>

