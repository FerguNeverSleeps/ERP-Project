<?php 
$url="consulta_cheques";
$modulo="Consulta de Cheques";
$tabla="nomcheques";
$titulos=array("Status","Cheque","Beneficiario","C.I. / R.I.F","Fecha","Monto","Chequera");
$indices=array("1","2","3","5","6","4","9");
$filtro="Ac";

session_start();
ob_start();

require_once '../lib/config.php';
require_once '../lib/common.php';
//include ('../header.php');

$conexion=conexion();
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$pagina=@$_GET['pagina'];
$aa=$_GET['busqueda'];
//$des=$_GET['buscar'];

if(isset($_POST['buscar']) || $tipob!=NULL){
    if(!$tipob){
        $tipob=$_POST['palabra'];
        $des=$_POST['buscar'];
        $aa=$_POST['sel_busqueda'];
    }
    switch($tipob){
        case "exacta": 
            //$aa=$_POST['sel_busqueda'];
            $consulta=buscar_exacta($tabla,$des,$aa);
            break;
        case "todas":
            $consulta=buscar_todas($tabla,$des,$_POST['sel_busqueda']);
            break;
        case "cualquiera":
            $consulta=buscar_cualquiera($tabla,$des,$_POST['sel_busqueda']);
            break;
    }
    //$consulta=$consulta." where status='".$filtro."'";
$consulta.=" and status<>'A' and status<>'D' order by chequera,cheque";
}else{

$consulta="select * from ".$tabla;//." where status='".$filtro."'";

$consulta.=" where status <> 'A' and status <> 'D'  order by chequera,cheque";
}

//echo $consulta." este es el valor quemuestra ";
$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);
?>

<SCRIPT language="JavaScript" type="text/javascript" src="mostrar_cuentas.js"></SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="mostrar_impresion.js"></SCRIPT>
<?



/*require_once '../../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();

$sql = "SELECT * FROM nombancos";
$res = query($sql, $conexion);*/

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
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Consulta de Cheques
                            </div>
                            <div class="actions">
                                <!--<a class="btn btn-sm blue"  onclick="javascript: window.location='../paginas/menu_int.php?cod=282'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>-->
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="table_datatable">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Cheque</th>
                                        <th>Beneficiario</th>
                                        <th>C.I. / R.I.F</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Chequera</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if($num_paginas!=0)
                                    {
                                        $i=0; 
                                        while($fila=mysqli_fetch_array($resultado))
                                        {
                                            $i++;
                                            if($i%2==0)
                                            {
                                                ?>
                                                <tr class="tb-fila" onclick="javascript:cuentas()">
                                                <?
                                            }
                                            else
                                            {
                                                echo "<tr>";
                                            }
                                            foreach($indices as $campo)
                                            {
                                                $nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;
                                                $var=$fila[$nom_tabla];
                                                if ($nom_tabla=="fecha")
                                                {
                                                    if($fila['status']=='An'|| $fila['status']=='Da')
                                                    {
                                                        //echo "<td>".fecha($fila['anulacion'])."</td>";
                                                        echo "<td>".$fila['anulacion']."</td>";
                                                    }
                                                    else
                                                    {
                                                        //echo "<td>".fecha($var)."</td>";
                                                        echo "<td>".$var."</td>";
                                                    }
                                                }
                                                elseif ($nom_tabla=="monto")
                                                {
                                                    echo "<td align=right>".number_format($var, 2, ',', '.')."</td>";
                                                }
                                                else
                                                {
                                                    echo "<td title='".$fila['concepto']."'>$var</td>";
                                                }
                                            }
                                            $codigo=$fila['cheque'];
                                        
                                            $chequera=$fila['chequera'];
                                        

                                        /*icono("javascript:cuentas('codigo=".$codigo."&banco=".$banco."&chequera=".$chequera."&cuenta=".$cuenta."&pagina=".$pagina."')", "Mostrar", "view.gif");
                                        if($fila['status']=='Im')
                                            icono("javascript:impresion('codigo=".$codigo."&banco=".$banco."&chequera=".$chequera."&cuenta=".$cuenta."')", "Menu de Impresion", "imprimir.png");
                                        else
                                            echo "<td></td>";
                                        icono("javascript:conceptos('codigo=".$codigo."&banco=".$banco."&chequera=".$chequera."&cuenta=".$cuenta."')", "Mostrar concepto", "view.gif");
                                        */
                                        

                                        echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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