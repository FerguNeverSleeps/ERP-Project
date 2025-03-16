<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	
         $sql_workflow        = "SELECT workflow,db_host,db_name,db_user,db_pass FROM param_ws";
         $res_workflow        = $db->query($sql_workflow);
         $res_workflow        = $res_workflow->fetch_array();
         //echo 'asdas'.$res_workflow["workflow"];
        if($res_workflow["workflow"]==1){
            $workflow       = $res_workflow["workflow"];
            $db_host        = $res_workflow["db_host"];
            $db_name        = $res_workflow["db_name"];
            $db_user        = $res_workflow["db_user"];
            $db_pass        = $res_workflow["db_pass"];    
            $conexion       = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die('No se puede conectar con el servidor Workflow');
            mysqli_query($conexion, 'SET CHARACTER SET utf8');
            
         
	
            $sql = "SELECT * FROM  USERS WHERE USR_USERNAME ='".$_REQUEST['usuario']."'";            
            $res         = $conexion->query($sql);
            $user = $res->fetch_assoc();
            //echo 'asdasd'.count($user);
            if(count($user)==0)
            {
                 $resp ='<div class="control-label form-group">
                        
                        <div class="col-md-6">                            
                            <label class="label label-success">Usuario Disponible</label>
                        </div>
                    </div>';
            }
            else
            {
                 $resp ='<div class="control-label form-group">
                        
                        <div class="col-md-6">                            
                            <label class="label label-danger">Usuario no Disponible</label>
                        </div>
                    </div>';
            }
                                                                                 
        }
        else{
            $resp ='<div class="control-label form-group">
                        
                        <div class="col-md-6">                            
                            <label class="label label-danger">Error de conección</label>
                        </div>
                    </div>';
        }
        echo $resp;
	       
?>
