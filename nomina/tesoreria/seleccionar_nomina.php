<?php
session_start();
ob_start();
$url="seleccionar_nomina";
$modulo="Seleccione ".$_SESSION['termino'];
$tabla="nom_nominas_pago";
$titulos=array("Codigo","Tipo","Estatus","Descripcion");
$indices=array("0","42","10","1");
$filtro="Ac";
//session_start();
//ob_start();
require_once '../lib/config.php';
require_once '../lib/common.php';
//include ('../header.php');

$conexion=conexion();

$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$pagina=@$_GET['pagina'];
$aa=$_GET['busqueda'];
//$des=$_GET['buscar'];
$banco=$_GET[banco];
$chequera=$_GET[codigo];

$entrada=$_GET[entrada];


$consulta="select np.*, nt.descrip as nominas from ".$tabla." np join nomtipos_nomina nt  on np.codtip=nt.codtip where status='C' and (status_acreedores='0' or status_cheques='0')  order by codtip asc, codnom desc";

//echo $consulta." este es el valor quemuestra ";
$num_paginas=obtener_num_paginas($consulta,100);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta, 100);
include("../header4.php");
?>

<script>
function CerrarVentana(){
	javascript:window.close();
}

/*function enviar(op){
	
	document.frmPrincipal.op.value=op;
	document.frmPrincipal.submit();
}
*/

function enviar(op,id,chequera,banco){
		
	if (op==1){		// Opcion de Agregar
		//document.frmAgregar.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="<?php echo $documento_edit; ?>";
		document.frmPrincipal.submit();	
	}
	if (op==2){	 	// Opcion de Modificar
		document.frmPrincipal.registro_id.value=id;		
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="<?php echo $documento_edit; ?>";
		document.frmPrincipal.submit();		
	}
	if (op==3){		// Opcion de Eliminar
		if (confirm("Esta seguro que desea generar los cheques por acreedores?"))
		{

			var cerrar_nomina=abrirAjax()
			cerrar_nomina.open("GET", "generar_cheques_acreedores.php?nomina="+id+"&chequera="+chequera, true)
			cerrar_nomina.onreadystatechange=function() 
			{
				if (cerrar_nomina.readyState==4)
				{
					//municipio.parentNode.innerHTML = 
					//alert(cerrar_nomina.responseText)
					alert("Cheques Generados exitosamente")
					document.location.href="seleccionar_nomina.php?codigo="+chequera+"&banco="+banco+"&texto=Cheques Generados";
				}
			}
			cerrar_nomina.send(null);
		}
	}
	if (op==4){		// Opcion de Eliminar
		if (confirm("Esta seguro que desea generar los cheques por empleados?"))
		{

			var cerrar_nomina=abrirAjax()
			cerrar_nomina.open("GET", "generar_cheques_empleados.php?nomina="+id+"&chequera="+chequera, true)
			cerrar_nomina.onreadystatechange=function() 
			{
				if (cerrar_nomina.readyState==4)
				{
					//municipio.parentNode.innerHTML = 
					alert("Cheques Generados exitosamente")
					document.location.href="seleccionar_nomina.php?codigo="+chequera+"&banco="+banco+"&texto=Cheques Generados";
				}
			}
			cerrar_nomina.send(null);
		}
	}
}

</script>

<SCRIPT language="JavaScript" type="text/javascript" src="mostrar_cuentas.js"></SCRIPT>
<SCRIPT language="JavaScript" type="text/javascript" src="mostrar_impresion.js"></SCRIPT>

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
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Seleccione <?php echo $_SESSION['termino'];?>
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
                                	    <th>C&oacute;digo</th>
                                	    <th>Tipo</th>
                                	    <th>Estatus</th>
                                	    <th>descripci&oacute;n</th>
                                	</tr>
                                </thead>
                                <tbody>
                                <?
                            		if($num_paginas!=0)
                            		{
                                		$i=0; 
                                		while($fila=mysqli_fetch_array($resultado))
                                		{
                                			foreach($indices as $campo)
                                			{
                                				$nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;
                                				$var=$fila[$nom_tabla];
                                				if ($nom_tabla=="fecha")
                                				{
                                					if($fila['status']=='An'|| $fila['status']=='Da')
                                					{
                                						echo "<td>".fecha($fila['anulacion'])."</td>";
                                					}
                                					else
                            						{
                                						echo "<td>".fecha($var)."</td>";
                                					}
                                				}
                                				elseif ($nom_tabla=="monto")
                                				{
                                					echo "<td align=right>".number_format($var, 2, ',', '.')."</td>";
                                				}
                                				elseif ($nom_tabla=="cuenta")
                                				{
                                					echo "<td align=left>".substr($fila['cuenta'], -10)."</td>";
                                				}
                                				else
                                				{
                                					echo "<td title='".$fila['concepto']."'>$var</td>";
                                				}
                                			}
                                			$codigo=$fila['codnom'];
                                			$tipnom=$fila['codtip'];                                		
                                			$cuenta=$fila['cuenta'];
                                			if($fila[status_acreedores]==0)
                                				icono("javascript:enviar(3,$codigo,$chequera,$banco);", "Generar Cheques a Acreedores", "generar.png");
                                			else
                                				echo "<td></td>";
                                			if($fila[status_cheques]==0)
                                				icono("javascript:enviar(4,$codigo,$chequera,$banco);", "Generar Cheques a Empleados", "familiares.png");
                                			else
                                				echo "<td></td>";
                                			icono("prestamos_edit.php?numpre=".$codigo, "Cerrar Nomina", "cancel.gif");
                                		

                                	    	echo "</tr>";
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