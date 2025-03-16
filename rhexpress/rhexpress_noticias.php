<?php

$sSql =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql-' );

$hoy = date("Y-m-d H:i:s");
 //Noticias
$sqlNoticias = "SELECT * from noticias where estatus = '1' AND '{$hoy}' >= fecha_inicio  AND '{$hoy}' <= fecha_vencimiento  ORDER BY fecha_vencimiento DESC;";
 
 $resultNoticias = $sSql->query($sqlNoticias);


?>
<div class="row">
    <div class="col-md-12">
         <div class="portlet box blue">
             <div class="portlet-title">
                 <div class="caption">
                     <i class="fa fa-clock"></i>Noticias
                 </div>
                 <!--<div class="actions" style="margin-top: 10px;">

                     <a class="btn btn-default green"  onclick="javascript: window.location='../../reportes/excel/excel_cumpleanios.php'">
                         <i class="fa fa-print"></i> IMPRIMIR LISTADO
                     </a>
                 </div>-->
             </div>
             <div class="portlet-body">
                 <div class="table-responsive">
                    <?php while($fila = $resultNoticias->fetch_assoc()){ 
                        ?>
                        
                        <div class="alert alert-info">
                        <b>Titulo:</b>
                        <?php echo $fila["titulo"];
                        ?> <br>
                        <?php echo $fila["descripcion"];?>
                        </div>
                    <?php } ?>
                 </div>
             </div>
         </div>
     </div>
 </div>