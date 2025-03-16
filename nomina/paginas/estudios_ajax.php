<?php
session_start();
ob_start();
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>

<?php
include("../lib/common.php");
include ("func_bd.php") ;
//codigo para pagina;
$TAMANO_PAGINA = 20;
$pagina = $_GET["pagina"];
$bandera=$_GET['bandera'];
if(isset($_POST['bandera']))
  $bandera=$_POST['bandera'];
if (!$pagina) {
    $inicio = 1;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $TAMANO_PAGINA+1;
} 
$limit=$inicio-1;
?>

<script>

function CerrarVentana(){
  javascript:window.close();
}

function enviar(op,id){
  
  if (op==1){
    // Opcion de Agregar
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="estudios_ajax.php";
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    document.frmPrincipal.registro_id.value=id;
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="estudios_ajax.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("Esta seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.submit();
    }   
  }
}
</script>
  <?php 


$criterio=$_POST['optOpcion'];
$cadena=$_POST['textfield'];
$registro_id=$_POST[registro_id];

if (isset($_GET[cedula]))
{
  
   $cedula=$_GET[cedula];
}
else
{
  
  $cedula=$_POST[cedula];
}

$op=$_POST['op'];

if ($op==3) {//Se presiono el boton de Eliminar     
  $query="delete from expediente where correl='$registro_id'";     
  $result=sql_ejecutar($query);
  
  activar_pagina("estudios_ajax.php?cedula=$cedula&bandera=$bandera");     
  }     
elseif ($cadena <> ""){ // Condicion para filtrado
    
    // para obtener la cantidad de registros
    $strsql="select COUNT(*) from expediente WHERE tipo IN ( 1,2 )";
   
    $strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombre");    
    $result =sql_ejecutar($strsql);     
    $fila = fetch_array($result); 
    $num_total_registros = $fila[0];  
  
    $strsql="select * from expediente WHERE tipo IN ( 1,2 )";
    $strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombre");  
    
      
    
    $strsql= "$strsql LIMIT $TAMANO_PAGINA OFFSET $limit";
    $result =sql_ejecutar($strsql);     
      $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA); 
     }
else{// No se filtra y se muestran todos los datos

  $strsql= "select COUNT(*) from expediente where cedula='$cedula' AND tipo IN ( 1,2 )";
  $result =sql_ejecutar($strsql); 
  $fila = fetch_array($result); 
  $num_total_registros = $fila[0];
  
  $strsql= "select * from expediente where cedula='$cedula' AND tipo IN ( 1,2 ) LIMIT $TAMANO_PAGINA OFFSET $limit";
  
  $result =sql_ejecutar($strsql);   
    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
}
include ("../header4.php");
?>
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
  <div class="page-content-wrapper">
    <div class="page-content">
      <div class="row">
        <div class="col-md-12">
           <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                Estudios
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  href="javascript:enviar(1,0)">
                  <i class="fa fa-plus"></i>
                      Agregar
                  </a>
              </div>
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      <th>ID</th>                      
                      <th>Título</th>
                      <th>Institución</th>
                      <th>Tiene Idoneidad</th>
                      <th>Ejerce Actualmente</th>
                    </tr>
                  </thead>
                  <tbody>
                      <?php
                          while ($fila = fetch_array($result))
                          { 
                          ?>
                            <tr>
                              <td><?php echo $fila['cod_expediente_det']; ?></td>                             
                              
                              <td>
                                  <?php 
                                    $consulta="select * from nomprofesiones where codorg='".$fila['titulo_profesional']."'";
                                    $resultado_titulo=sql_ejecutar($consulta);
                                    $fila_titulo=fetch_array($resultado_titulo);
                                    echo utf8_encode($fila_titulo['descrip']); 
                                  ?>
                              </td>                              
                              <td><?php echo $fila['institucion']; ?></td>
                              <td><?php echo $fila['idoneidad']; ?></td>
                              <td><?php echo $fila['ejerce']; ?></td>                              
                            </tr>
                            <?php
                            }
                            ?>
                  </tbody>
                </table>
              </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    
    <input name="cedula" id="cedula" type="hidden" value="<?php echo $cedula; ?>">
    <input name="registro_id" id="registro_id" type="hidden" value="">  
    <input name="op" type="hidden" value="">  
  </form>
  <?php include("../footer4.php"); ?>
<script type="text/javascript">
   $(document).ready(function() { 
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended", 
              //"sPaginationType": "full_numbers",
                "oLanguage": {
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
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [ 4 ] },
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [ 3 ] },
                    { "sWidth": "8%", "aTargets": [3] },
                    { "sWidth": "8%", "aTargets": [4] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
                /*
              "aoColumns": [
            null,
            null,
            { "sWidth": "10px" },
            { "sWidth": "10px" },
        ]*/
              /*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });
</script>
</body>
</html>