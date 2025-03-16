<?php 



$sql_detalle = "
	SELECT 
		R.*,
		P.apenom
    FROM   nomina_electronica_detalle_response R
    	left join nompersonal P on P.cedula=R.cedula and P.ficha=R.ficha
    WHERE  R.id_cabecera='$id_cabecera'
	ORDER BY id_detalle_response ASC";
		

$res_procesadas=$conexion->query($sql_detalle);


$total = 0;
$total2 = 0;
$id=array();
$fecha=array();
$fecha_inicio=array();
$fecha_fin=array();
$estatus=array();

$id2=array();
$fecha2=array();
$fecha_inicio2=array();
$fecha_fin2=array();
$estatus2=array();
?>

<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .table_datatable {

  }
  #table_datatable td.text-normal {
  	white-space: normal;
  }


</style>
<script type="text/javascript">

</script>
<div class="page-container-x">
	
	
	<table class="table table-striped table-bordered table-hover" id="table_datatable_procesadas">
	<thead>
	<tr>
		<th>Documento</th>
        <th>Código</th>
        <th>Mensaje</th>
        <th>Resultado</th>
        <th>CUNE</th>
        <th>Cédula</th>                                                                        
		<th>Ficha</th>
		<th>Nombre / Apellido</th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<?php								
		while( $fila_procesadas=$res_procesadas->fetch_array())
		{ 	

			if(!mb_detect_encoding($fila_procesadas['apenom'],["UTF-8"],true))
				$fila_procesadas['apenom']=utf8_encode($fila_procesadas['apenom']);
			if(!mb_detect_encoding($fila_procesadas['mensaje'],["UTF-8"],true))
				$fila_procesadas['mensaje']=utf8_encode($fila_procesadas['mensaje']);
			if(!mb_detect_encoding($fila_procesadas['resultado'],["UTF-8"],true))
				$fila_procesadas['resultado']=utf8_encode($fila_procesadas['resultado']);
			if(!mb_detect_encoding($fila_procesadas['codigo'],["UTF-8"],true))
				$fila_procesadas['codigo']=utf8_encode($fila_procesadas['codigo']);

			$title="";
			$reglasRechazoTFHKA="";
			if($fila_procesadas['reglasRechazoTFHKA']){
				$reglasRechazoTFHKA=base64_decode($fila_procesadas['reglasRechazoTFHKA']);	
				if(!mb_detect_encoding($reglasRechazoTFHKA,["UTF-8"],true))
					$reglasRechazoTFHKA=utf8_encode($reglasRechazoTFHKA);
				$reglasRechazoTFHKA=json_decode($reglasRechazoTFHKA,true);
				if(is_array($reglasRechazoTFHKA) and count($reglasRechazoTFHKA)>0){
					$reglasRechazoTFHKA=implode("\n-", $reglasRechazoTFHKA);
					$title.="Rechazo TFHKA:\n$reglasRechazoTFHKA";			
				}	
			}

			$reglasRechazoDIAN="";
			if($fila_procesadas['reglasRechazoDIAN']){
				$reglasRechazoDIAN=base64_decode($fila_procesadas['reglasRechazoDIAN']);	
				if(!mb_detect_encoding($reglasRechazoDIAN,["UTF-8"],true))
					$reglasRechazoDIAN=utf8_encode($reglasRechazoDIAN);
				$reglasRechazoDIAN=json_decode($reglasRechazoDIAN,true);
				if(is_array($reglasRechazoDIAN) and count($reglasRechazoDIAN)>0){
					$reglasRechazoDIAN=implode("\n-", $reglasRechazoDIAN);
					$title.="\nRechazo DIAN:\n$reglasRechazoDIAN";			
				}	
			}

			$title=str_replace("'", "", $title);
			$title=str_replace('"', "", $title);
			
			$add_cls="background-color: #ffcdd2; color: #d32f2f;";
			if($fila_procesadas['codigo']=="200")
				$add_cls="background-color: #c8e6c9; color: #004d40;";
		?>
			<tr class="odd gradeX" id="num" opcion="<?php echo $fila_procesadas['id_detalle_response'];?>" style="<?php print $add_cls?>" title="<?php print $title?>">
				<td><?php echo $fila_procesadas['consecutivoDocumento'];?></td>
                <td class="text-normal"><?php echo $fila_procesadas['codigo'];?></td>
                <td><?php echo $fila_procesadas['mensaje'];?></td>
                <td><?php echo $fila_procesadas['resultado'];?></td>
                <td><?php echo $fila_procesadas['cune'];?></td>
                <td><?php echo $fila_procesadas['cedula'];?></td>
                <td><?php echo $fila_procesadas['ficha'];?></td>
                <td><?php echo $fila_procesadas['apenom'];?></td>
                <td>                 	                                       	
                	<a target="_blank" href="https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=<?php echo $fila_procesadas['cune']; ?>" title="Ver">
						<img src="../../includes/imagenes/icons/bar-code-16.png" width="16" height="16">
					</a>
                </td>                                           
			</tr>
		<?php							
		}
	?>
	</tbody>
	</table>
	<br><br>	
	<center>
		<a class="btn btn-lg blue"  onclick="javascript: window.location='nomina_electronica_proceso_nomina_general_periodos_global.php'">
			<i class="fa fa-arrow-left"></i> Regresar
		</a>
	</center>
</div>			


<script type="text/javascript">
	 $(document).ready(function() {      
                      
            
            
            $('#table_datatable_procesadas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 0, "desc" ]], 
                "oLanguage": {
                	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
          		    "sEmptyTable":  "No hay datos disponibles", // No hay datos para mostrar
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [6] },
                    { "bSearchable": false, "aTargets": [6] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_procesadas_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_procesadas').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable_procesadas_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_procesadas_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
            
            
            
	 });
</script>
</body>
</html>