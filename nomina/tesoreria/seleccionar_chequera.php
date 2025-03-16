<?php
$url="seleccionar_chequera";
$modulo="terceros";
$tabla="nomchequera";

require_once '../lib/config.php';
require_once '../lib/common.php';


$conexion=conexion();
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$codigo_banco=@$_GET['codigo'];
$pagina=@$_GET['pagina'];
$entrada=$_GET[entrada];

if(isset($_POST['buscar']) || $tipob!=NULL){
	if(!$tipob){
		$tipob=$_POST['palabra'];
		$des=$_POST['buscar'];
		$codigo_banco=$_POST['banco'];
	}
	
	switch($tipob){
		case "exacta": 
			$consulta=buscar_exacta($tabla,$des,"numero");
			break;
		case "todas":
			$consulta=buscar_todas($tabla,$des,"numero");
			break;
		case "cualquiera":
			$consulta=buscar_cualquiera($tabla,$des,"numero");
			break;
	}
	$consulta=$consulta." AND banco='".$codigo_banco."' and situacion='A";

}else{

$consulta="select * from ".$tabla." where banco='".$codigo_banco."' and situacion='A'";

}
$consulta_banco="select * from nombancos where cod_ban='".$codigo_banco."'";
$resultado_banco=mysqli_query($conexion, $consulta_banco) or die("No se puede obtener el banco");
$banco_fila=mysqli_fetch_array($resultado_banco);
$cuenta= $banco_fila['cuentacob'];
$descripcion=$banco_fila['des_ban'];



$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);
include("../header4.php");
?>
<HEAD>
<TITLE></TITLE>
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
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"><?php echo $descripcion;?>, Nro_cuenta: <?php echo $cuenta;?>. Seleccione una Chequera
                            </div>
                            <div class="actions">
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='seleccionar_banco.php?entrada=<?php echo $entrada;?>'">
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
	                        		    <th>Situacion</th>
	                        		    <th>Inicio</th>
	                        		    <th>Fin</th>
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
                        					$numero=$fila["chequera_id"];
                        					$cantidad=$fila["cantidad"];
                        					$inicio=$fila["inicio"];
                        					$situacion=$fila["situacion"];
                        					$final=($inicio+$cantidad)-1;

                        					echo"<td>$numero</td>";
	                        				echo"<td>$cantidad</td>";
	                        				switch($situacion)
	                        				{
	                        					case 'D':
	                        						echo"<td>Deposito</td>";
	                        					break;
	                        					case 'A':
	                        						echo"<td>Activa</td>";
	                        					break;
	                        					case 'C':
	                        						echo"<td>Consumida</td>";
	                        					break;
	                        					default:
	                        						echo"<td>Ninguna</td>";
	                        					break;
	                        				}
	                        				echo"<td>$inicio</td>";
	                        				echo"<td>$final</td>";

	                        				if($entrada==1)
	                        					echo "<td><a href=\"seleccionar_nomina.php?codigo=$numero&banco=$codigo_banco\"><IMG title=\"Seleccionar Nomina\" src=\"../imagenes/add.gif\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";	
	                        				else
	                        					echo "<td><a href=\"cheques_trabajador.php?codigo=$numero&banco=$codigo_banco\"><IMG title=\"Agregar Orden\" src=\"../imagenes/add.gif\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                        		    	echo"</tr>";
	                        			}
	                        		}
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