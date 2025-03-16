<?php
session_start();
ob_start();

$url="seleccionar_banco";
$modulo="seleccionar_banco";
$tabla="nombancos";

require_once '../lib/config.php';
require_once '../lib/common.php';

/*$conexion5=conexion_conf();
$consulta5 = "SELECT sobregirof FROM parametros WHERE codigo = '1'";
$resultado3 = query($consulta5,$conexion5);
$fetch2=fetch_array($resultado3);
//echo $fetch2['sobregirof'];
cerrar_conexion($conexion5);*/

$conexion=conexion();
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$pagina=@$_GET['pagina'];
$siguiente=@$_GET['siguiente'];

$entrada=$_GET[entrada];

if(isset($_POST['buscar']) || $tipob!=NULL){
	if(!$tipob){
		$tipob=$_POST['palabra'];
		$des=$_POST['buscar'];
		$siguiente=$_POST['sig'];
	}
	
	switch($tipob){
		case "exacta": 
			$consulta=buscar_exacta($tabla,$des,"descripcion");
			break;
		case "todas":
			$consulta=buscar_todas($tabla,$des,"descripcion");
			break;
		case "cualquiera":
			$consulta=buscar_cualquiera($tabla,$des,"descripcion");
			break;
	}

}else{

$consulta="select * from ".$tabla;

}

$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);
include("../header4.php");
?>
<SCRIPT language="JavaScript" type="text/javascript" src="transaccion.js">

</SCRIPT>
</HEAD>

<BODY>
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
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Seleccionar Banco
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
                                	    <th>C&oacute;digo</th>
                                	    <th>Nombre del Banco</th>
                                	    <th>Nro de Cuenta</th>
                                	    <th>Tipo</th>
                                	    <th>&nbsp;</th>
                                	</tr>
                                </thead>
                                <tbody>
                                	<?
                            		if($num_paginas!=0)
                            		{
                                		$i=0; 
                            			while($fila=mysqli_fetch_array($resultado))
                            			{
                            				$codigo=$fila["cod_ban"];
                            				$descripcion=$fila["des_ban"];
                            				$cuenta=$fila["cuentacob"];
                            				$tipo=$fila["tipocuenta"];
                            				$cuenta_contable=$fila["cuenta_contable"];
                            				echo"<td>$codigo</td>";
                            				echo"<td>$descripcion</td>";
                            				echo"<td>$cuenta</td>";
                            				echo"<td>$tipo</td>";
                            				if(($fila["saldo"]<=0)&&($fetch2['sobregirof']=='N'))
                            				{
                        	      				echo "<td><IMG title=\"No tiene fondos\" src=\"../imagenes/ico_cancel.gif\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></td>";
                            				}
                            				else
                            				{
                            					if($_SESSION[CONSULTA]==1)
                            					{
                            						echo "<td></td>";
                            					}
                            					ELSE
                            					{
                            	      				echo "<td><a href=\"seleccionar_chequera.php?pagina=1&codigo=$codigo&entrada=$entrada\"><IMG title=\"Agregar Banco\" src=\"../imagenes/add.gif\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
                        		 				}
                            				}
                            	    		echo"</tr>";
                            			}
                        			}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </FORM>
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