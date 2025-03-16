<?php
session_start();
ob_start();
//Si realizas otra modificacion colocarla arriba para hacer un historico de modificaciones
//Fecha Ultima Modificacion: 19-02-2008 Hora: 12:09 pm
//Realizada por Topo en el aeropuerto
//Modificacion: Estados de los Cheques

$url="cheques";
$modulo="cheques";
$tabla="nomcheques";

require_once '../lib/config.php';
require_once '../lib/common.php';
//include('../header.php');

$conexion=conexion();
$tipob=@$_GET['tipo'];
$des=@$_GET['des'];
$pagina=@$_GET['pagina'];
$codigo_banco=@$_GET['banco'];
$chequera=@$_GET['chequera'];
$situacion=@$_GET['status'];
$rsac=@$_GET['rsac'];
$opcion=@$_GET['opcion'];
$cheque = @$_GET['cheque'];
$odp = @$_GET['odp'];
$monto = @$_GET['monto'];

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
  if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
  }
if(isset($_POST['buscar']) || $tipob!=NULL){
	if(!$tipob){
		$tipob=$_POST['palabra'];
		$des=$_POST['buscar'];
		$codigo_banco=$_POST['banco'];
		$chequera=$_POST['chequera'];
	}
	
	switch($tipob){
		case "exacta": 
			$consulta=buscar_exacta($tabla,$des,"cheque");
			break;
		case "todas":
			$consulta=buscar_todas($tabla,$des,"cheque");
			break;
		case "cualquiera":
			$consulta=buscar_cualquiera($tabla,$des,"cheque");
			break;
	}
	$consulta=$consulta." AND banco='".$codigo_banco."' AND chequera='".$chequera."'";
}else{
	if ($rsac == '1') 
	{
		
	}
	$consulta="select * from ".$tabla." where chequera='".$chequera."' ORDER BY cheque";
}

$consulta_banco="select * from nombancos where cod_ban='".$codigo_banco."'";
$resultado_banco=mysqli_query($conexion,$consulta_banco) or die("No se puede obtener el banco");
$banco_fila=mysqli_fetch_array($resultado_banco);
$cuenta= $banco_fila['cuentacob'];
$descripcion=$banco_fila['des_ban'];

$consultaCheque = "select * from nomcheques where  chequera='".$chequera."' AND status='A' ORDER BY cheque";
$resultadoCheque = query($consultaCheque, $conexion);

$num_paginas=obtener_num_paginas($consulta);
$pagina=obtener_pagina_actual($pagina, $num_paginas);
$resultado=paginacion($pagina, $consulta);
include("../header4.php");
?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
	                <FORM name="tcheques" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self">
	                    <div class="portlet box blue">

	                        <div class="portlet-title">
	                        	<?
	                        		switch($situacion){
	                        			case 'D':
	                        				$sit="Deposito";
	                        				break;
	                        			case 'A':
	                        				$sit="Activa";
	                        				break;
	                        			case 'C':
	                        				$sit="Consumida";
	                        				break;
	                        			case 'D';
	                        		}
	                        	?>
	                            <div class="caption">
	                                <img src="../imagenes/21.png" width="22" height="22" class="icon"><?php echo $descripcion;?>, Nro. Cuenta: <?php echo $cuenta;?>, Chequera Nro.: <?php echo $chequera;?>, Situacion: ("<?php echo $sit;?>"). Relaci&oacute;n de Cheques
	                            </div>
	                            <div class="actions">
	                                <a class="btn btn-sm blue"  onclick="javascript: window.location='chequeras.php?pagina=1&codigo=<?php echo $codigo_banco;?>'">
	                                    <i class="fa fa-arrow-left"></i> Regresar
	                                </a>
	                            </div>
	                        </div>
	                        <div class="portlet-body">
	                            <table class="table table-striped table-bordered table-hover" id="table_datatable">
	                                <thead>
	                                    <tr>
	                                        <th>Situaci&oacute;n</th>
	                                        <th>N&uacute;mero</th>
	                                        <th>Beneficiario</th>
	                                        <th>Monto</th>
	                                        <th>Fecha Cheque</th>
	                                        <th>Fecha Anulaci&oacute;n</th>
	                                        <th>&nbsp;</th>
	                                        <th>&nbsp;</th>
	                                        <th>&nbsp;</th>
	                                        <th>&nbsp;</th>
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                	<?
                                		if($num_paginas!=0)
                            			{
	                                		$w=0; 
	                                		while($fila=mysqli_fetch_array($resultado))
	                                		{
	                                			$w++;
	                                			$status=$fila['status'];
	                                			$numero=$fila['cheque'];
	                                			$chequex=$fila['cheque'];
	                                			$beneficiario=$fila['beneficiario'];
	                                			$monto=$fila['monto'];
	                                			$fecha_cheque=$fila['fecha'];
	                                			$fecha_anulacion=$fila['anulacion'];
	                                			switch($status)
	                                			{
	                                				case 'D':
		                                				echo"<td>D</td>";
                                					break;
	                                				case 'A':
		                                				echo"<td>A</td>";
                                					break;
	                                				case 'Ac':
	                                					echo"<td>Ac</td>";
                                					break;
	                                				case 'Im':
	                                					echo"<td>Im</td>";
	                                				break;
	                                				case 'En':
	                                					echo"<td>En</td>";
	                                				break;
	                                				case 'Da':
	                                					echo"<td>Da</td>";
	                                				break;
	                                				case 'An':
	                                					echo"<td>An</td>";
	                                				break;
	                                				default:
	                                					echo"<td>Ninguna</td>";
	                                				break;
	                                			}
	                                			$k=strlen($numero);
	                                			if($k<8)
	                                			{
	                                				for($i=$k;$i<=8;$i++)
	                                				{
	                                					$numero="0".$numero;
		                                			}
	                                			}
	                                			echo "<td>$numero</td>";
	                                		
	                                			echo "<td>$beneficiario</td>";
	                                			echo "<td align=right>".number_format($monto, 2, ',', '.')."</td>";
	                                			echo "<td align=center>".fecha($fecha_cheque)."</td>";
	                                			echo "<td align=center>".fecha($fecha_anulacion)."</td>";
	                                			$filaCq = num_rows($resultadoCheque);
	                                		
	                                			if($_SESSION[CONSULTA]==1)
	                                			{
	                                				echo "<td></td><td></td><td></td><td></td><td></td>";
	                                			}
	                                			ELSE
	                                			{
	                                				/*if ($filaCq == 0)
	                                				{
	                                					echo "<td><a href=\"cheques_editar.php?banco=$codigo_banco&cheque=$chequex&chequera=$chequera&inicio=$inicio&fin=$final&valor=A\"><IMG title=\"Editar\" src=\"../imagenes/edit.gif\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                				}
	                                				else
	                                				{
	                                					echo "<td></td>";
	                                				}*/
	                                				if ($status<>'Da' && $status<>'An')
	                                				{
	                                					echo "<td><a href=\"opcion_chequera.php?opcion=5&banco=$codigo_banco&chequera=$chequera&valor=A&cheque=$chequex&status=$status\"><IMG title=\"Activar\" src=\"../imagenes/activar.png\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                			
	                                					echo "<td><a href=\"opcion_chequera.php?opcion=2&banco=$codigo_banco&chequera=$chequera&valor=D&cheque=$chequex&status=$status\"><IMG title=\"Depósito\" src=\"../imagenes/deposito.png\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                			
	                                					echo "<td><a href=\"opcion_chequera.php?opcion=3&banco=$codigo_banco&chequera=$chequera&cheque=$chequex&monto=$monto&valor=D&status=$status&rsac=1&odp=$odp\"><IMG title=\"Dañado\" src=\"../imagenes/cheque_danado.png\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                					echo "<td><a href=\"opcion_chequera.php?opcion=4&banco=$codigo_banco&chequera=$chequera&cheque=$chequex&monto=$monto&valor=A&rsac=1&status=$status\"><IMG title=\"Anulado\" src=\"../imagenes/cheque_anular.png\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                				}
	                                				else
	                                				{
	                                					echo "<td><a href=\"opcion_chequera.php?opcion=7&banco=$codigo_banco&chequera=$chequera&valor=A&cheque=$chequex&status=$status\"><IMG title=\"Activar\" src=\"../imagenes/activar.png\" width=\"16\" height=\"16\" align=\"left\" border=\"0\"></a></td>";
	                                					echo "<td></td>";
	                                					echo "<td></td>";
		                                				echo "<td></td>";
	                                				}
	                                			}
	                                	   	 	echo "</tr>";
                                			}
	                                	}
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