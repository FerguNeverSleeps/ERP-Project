<?php
session_start();
ob_start();

$termino = $_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
include("../header.php");
?>






<?php
$registro_id = $_POST[registro_id];
$op_tp = $_POST[op_tp];
$fecha_actual = date("Y-m-d");
$validacion = 0;


/*if ($filaempresa['tipoficha'] == 1) {
    $consulta = "select max(ficha) as valor from nompersonal where tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $result = sql_ejecutar($consulta);

    $fila = mysqli_fetch_array($result);
    $actual = $fila['valor'] + 1;
} else {

    $consulta = "select max(ficha) as valor from nompersonal";
    $result = sql_ejecutar($consulta);

    $fila = mysqli_fetch_array($result);
    $actual = $fila['valor'] + 1;
}*/
if(isset($_GET['id'])){$actual = $_GET['id'];}
if(isset($_GET['edit'])){$add_param_url="?edit&id=".$actual;$editarFicha = 1;$tipo_operacion="edit";}else{$editarFicha=0;$tipo_operacion="add";$add_param_url="";}

$query = "select * from nomempresa";
$result = sql_ejecutar($query);
$row_emp = mysqli_fetch_array($result);

?>
<!DOCTYPE HTML >
<html>
<head>
    <title></title>
    <link href="../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/resources/ext-theme-neptune-all.css" rel="stylesheet" />
    <style type="text/css">
	        
            .editar{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/pencil.png);
            }
            .calendario{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/calendar.png);
            }
            .cargas_familiares{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/group.png);
            }
            .campos_adicionales{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/table_multiple.png);
            }
            .imprimir{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/printer.png);
            }
            .expediente{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/folder_page.png);
            }
            .eliminar{
		        height: 16px;
		        width: 16px;
		    	background-image: url(../../includes/imagenes/icons/delete.png);
            }
		</style>


    <script src="../../includes/js/ext-5.0.0/build/ext-all-debug.js"></script>
    <script src="../../includes/js/ext-5.0.0/build/packages/ext-theme-neptune/build/ext-theme-neptune.js"></script>
    <script type="text/javascript" src="../../includes/js/ext-5.0.0/build/packages/ext-locale/build/ext-locale-es.js"></script>
    <script>
    	var TIPO_VIEW = "funcion-form";
        var EDITAR_CONCUE = "<? echo $editarFicha?>";
    	var ACTUAL = "<? echo $actual?>";
        var OPERACION = "<? echo $tipo_operacion?>";
        var RELOAD_URL_ADD = "<? echo $add_param_url?>";
        var CEDULA_ACTUAL = "";
        var EDITAR_PERSONAL = "";
        var TIPO_EMPRESA = "";
        var USUARIO_ACCESO_SUELDO = "";
        var nivel1 = true;
        var nomNivel1 = "";
        var nivel2 = true;
        var nomNivel2 = "";
        var nivel3 = -true;
        var nomNivel3 = "";
        var nivel4 = true;
        var nomNivel4 = "";
        var nivel5 = true;
        var nomNivel5 = "";
        var nivel6 = true;
        var nomNivel6 = "";
        var nivel7 = true;
        var nomNivel7 = "";

    </script>
    <script type="text/javascript">
    
    </script>
    <script src="../../includes/js/gui/Personal/Personal.js"></script>
</head>
<body>

</body>
</html>

