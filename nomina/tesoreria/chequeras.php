<?php 
//require_once '../../generalp.config.inc.php';
session_start();
ob_start();
$url="chequeras";
$modulo="chequeras";
$tabla="nomchequera";

require_once '../lib/config.php';
require_once '../lib/common.php';
//include('../header.php');

$conexion=conexion();
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$codigo_banco=@$_GET['codigo'];
$pagina=@$_GET['pagina'];
$banco=$_GET['banco'];
$chequera=$_GET['chequera'];
$accion = $_GET['accion'];

if(isset($_POST['buscar']) || $tipob!=NULL){
    if(!$tipob){
        $tipob=$_POST['palabra'];
        $des=$_POST['buscar'];
        $codigo_banco=$_POST['banco'];
    }
    
    switch($tipob){
        case "exacta": 
            $consulta=buscar_exacta($tabla,$des,"chequera_id");
            break;
        case "todas":
            $consulta=buscar_todas($tabla,$des,"chequera_id");
            break;
        case "cualquiera":
            $consulta=buscar_cualquiera($tabla,$des,"chequera_id");
            break;
    }
    $consulta=$consulta." AND banco='".$codigo_banco."'";

}else{

    if($accion=='borrar_chequera')
    {
        $var_sql="delete from nomchequera WHERE banco=".$banco." and chequera_id=".$chequera;
        $rs = query($var_sql,$conexion);

        echo"<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
        alert(\"Se ha eliminado\")
        parent.cont.location.href=\"chequeras.php?codigo=".$banco."\"
        </SCRIPT>";
        $codigo_banco = $banco;

    }
   // $consulta="select * from ".$tabla." where banco='".$codigo_banco."'";
    $consulta="SELECT * FROM ".$tabla." WHERE banco = ".$codigo_banco;
}

$consulta_banco="select * from nombancos where cod_ban='".$codigo_banco."'";
$resultado_banco=query($consulta_banco, $conexion);
$banco_fila=fetch_array($resultado_banco);
$cuenta= $banco_fila['cuentacob'];
$descripcion=$banco_fila['des_ban'];

$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);


// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
/*require_once '../lib/common.php';
$conexion=conexion();
$codigo_banco=@$_GET['codigo'];

$sql = "SELECT * FROM nomchequera WHERE banco = '".$codigo_banco."'";
$res = query($sql, $conexion);


$consulta_banco="SELECT * FROM nombancos WHERE cod_ban = '".$codigo_banco."'";
$resultado_banco = query($consulta_banco, $conexion);
$banco_fila = fetch_array($resultado_banco);
$cuenta = $banco_fila['cuentacob'];
$descripcion = $banco_fila['des_ban'];*/

include("../header4.php"); // <html><head></head><body>
?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                <FORM name="<?echo $url?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
                    <div class="portlet box blue">

                        <div class="portlet-title">
                        
                            <div class="caption">
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"><?php echo $descripcion;?>, Nro. Cuenta: <?php echo $cuenta;?>. Relaci&oacute;n de Chequeras
                            </div>
                            <div class="actions">
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='chequeras_agregar.php?banco=<?php echo $codigo_banco;?>'">
                                    <i class="fa fa-plus"></i>Agregar
                                </a>
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='bancos.php?pagina=1'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="table_datatable">
                                <thead>
                                    <tr>
                                        <th>N&uacute;mero</th>
                                        <th>Cantidad</th>
                                        <th>Situaci&oacute;n</th>
                                        <th>Inicio</th>
                                        <th>Fin</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($num_paginas!=0)
                                        {
                                            $i=0; 
                                            while($fila=mysqli_fetch_array($resultado))
                                            {
                                            
                                        $numero=$fila["chequera_id"];
                                        $cantidad=$fila["cantidad"];
                                        $inicio=$fila["inicio"];
                                        $situacion=$fila["situacion"];
                                        $final=($inicio+$cantidad)-1;

                                        echo "<td>$numero</td>";
                                        echo "<td>$cantidad</td>";
                                        switch($situacion){
                                            case 'D':
                                                echo "<td>Dep&oacute;sito</td>";
                                                break;
                                            case 'A':
                                                echo "<td>Activa</td>";
                                                break;
                                            case 'C':
                                                echo "<td>Consumida</td>";
                                                break;
                                            default:
                                                echo "<td>Ninguna</td>";
                                                break;
                                        }
                                        echo "<td>$inicio</td>";
                                        echo "<td>$final</td>";
                                        $editar = $modulo."_modificar";
                                        $eliminar = $modulo."_eliminar";
                                        
                                        $status = $fila['situacion'];
                                        $conCheques = "SELECT * FROM nomcheques WHERE  chequera='".$numero."'";
                                        //echo $conCheques."<br>";
                                        $resCheques = query($conCheques, $conexion);
                                        $registros = num_rows($resCheques);
                                        //echo $registros;
                                        if($_SESSION[CONSULTA]==1)
                                        {
                                           echo "<td></td><td></td>";
                                        }
                                        ELSE
                                        {
                                            if ($registros==0)
                                            {
                                                icono("chequeras_modificar.php?codigo=".$numero."&banco=".$codigo_banco, "Editar", "edit.gif");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Editar" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                            if ($registros==0)
                                            {
                                                icono("opcion_chequera.php?opcion=0&inicio=".$inicio."&fin=".$final."&status=".$situacion."& banco=".$codigo_banco."&chequera=".$numero, "Generar Cheques", "generar_cheques.png");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Generar" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                        }
                                        icono("cheques.php?pagina=1&banco=".$codigo_banco."&chequera=".$numero."&status=".$situacion, "Ver Cheques", "view.gif");

                                        if($_SESSION[CONSULTA]==1)
                                        {
                                            echo "<td></td><td></td><td></td>";
                                        }
                                        ELSE
                                        {
                                            if (($registros>0)&&($situacion=="D"))
                                            {
                                                icono("opcion_chequera.php?opcion=1&banco=".$codigo_banco."&chequera=".$numero."&valor=A", "Activar", "activar.png");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Activar" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                            if (($registros==0)||($situacion=="A"))
                                            {
                                                icono("opcion_chequera.php?opcion=1&banco=".$codigo_banco."&chequera=".$numero."&valor=C", "Consumir", "consumir.png");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Consumir" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                            if (($registros==0)||($situacion=="A"))
                                            {
                                                icono("opcion_chequera.php?opcion=1&banco=".$codigo_banco."&chequera=".$numero."&valor=D", "Depósito", "deposito.png");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Depositar" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                            if ($registros==0)
                                            {
                                                icono("chequeras.php?accion=borrar_chequera&banco=".$codigo_banco."&chequera=".$numero, "Borrar Chequera", "delete.gif");
                                            }
                                            else
                                            {
                                                ?>
                                                <td><img width="16" height="16" align="left" border="0" title="No puede Borrar" src="../imagenes/ico_est6.gif"/></td>
                                                    <?
                                            }
                                        }
                                        echo "</tr>";
                                        }}
                                        cerrar_conexion($conexion);

                                        ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </FORM>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('#table_datatable').DataTable(
        {
            //"oSearch": {"sSearch": "Escriba frase para buscar"},
            "iDisplayLength": 10,
            //"sPaginationType": "bootstrap",
            "sPaginationType": "bootstrap_extended", 
            //"sPaginationType": "full_numbers",
            "oLanguage":
            {
                "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                "sLengthMenu": "Mostrar _MENU_",
                //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                //"sInfoEmpty": "No hay registros para mostrar",
                "sInfoEmpty": "",
                //"sInfo": "",
                "sInfo":"Total _TOTAL_ registros",
                "sInfoFiltered": "",
                "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                "sZeroRecords": "No se encontraron registros",//"No matching records found",
                /*"oPaginate": {
                "sPrevious": "Página Anterior",
                "sNext": "Página Siguiente"
                }*/
                "oPaginate":
                {
                    "sPrevious": "P&aacute;gina Anterior",//"Prev",
                    "sNext": "P&aacute;gina Siguiente",//"Next",
                    "sPage": "P&aacute;gina",//"Page",
                    "sPageOf": "de",//"of"
                }
            },
            "aLengthMenu":
            [ // set available records per page
                [5, 10, 25, 50,  -1],
                [5, 10, 25, 50, "Todos"]
            ],                
            "aoColumnDefs":
            [
                { 'bSortable': false, 'aTargets': [3] },
                { "bSearchable": false, "aTargets": [ 3 ] },
                { 'bSortable': false, 'aTargets': [2] },
                { "bSearchable": false, "aTargets": [ 2 ] },
                { "sWidth": "8%", "aTargets": [2] },
                { "sWidth": "8%", "aTargets": [3] }
            ],
            "fnDrawCallback": function()
            {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
            }
        });
        $('#table_datatable').on('change', 'tbody tr .checkboxes', function()
        {
            $(this).parents('tr').toggleClass("active");
        });
        $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
        $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
    });
</script>
</body>
</html>