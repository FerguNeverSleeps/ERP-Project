<?php
    $ruta = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($ruta.'/lib/database.php');

    $db             = new Database($_SESSION['bd']);
    $id_funcionario = isset($_REQUEST['id_funcionario']) ? $_REQUEST['id_funcionario']:NULL;

    $sql_accion = "SELECT id_accion_funcionario, tipo_accion, numero_accion, fecha
            FROM accion_funcionario
            WHERE  id_funcionario='".$id_funcionario."'";
    
    //echo $sql;

    $res_accion = $db->query($sql_accion);

   ?>	
    <div class="panel-group">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                  <a id="btn-toggle2">Acciones Funcionario</a>
                </h4>
            </div>
            <div id="collapse2"  class="collapse" >

                <div class="panel-body"> 
                    
                    <div class="form-group" style="position: relative; left: 5%;" >
                        <table class="table table-striped table-hover" style="width: 90%">
                            <thead>
                              <tr>                                
                                <th>ID&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Tipo&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Número&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                <th>Fecha&nbsp;&nbsp;&nbsp;&nbsp;</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php                                                          
                                                                
                                        while($fila_accion = $res_accion->fetch_array())										
					{ 										 						
                                        ?>
                                            <tr>
                                                <td><?php echo $fila_accion['id_accion_funcionario']; ?></td>
                                                <td><?php echo utf8_encode($fila_accion['tipo_accion']);  ?></td>                                                
                                                <td><?php echo $fila_accion['numero_accion']; ?></td>
                                                <td><?php $fecha = explode('-',$fila_accion['fecha']);
                                                           echo $fecha[2].'/'.$fecha[1].'/'.$fecha[0]; 
                                                    ?></td>
                                            </tr>
                                          <?php									
                                        }
                                ?>
                            </tbody>
                        </table>
                    </div>                     
                </div>                    
            </div>
        </div>
    </div>
            