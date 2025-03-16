<?php	
	require_once("../class/Session.class.php");
	$my_ses = new Session();
    include("../config/config.inc.php");
	include("../class/Database.class.php");    
	include("../config/conexion.php");	
    include("../class/Curriculum.class.php");  
    include_once("../class/JSON.php");

        //echo DB_SERVER.' - '.DB_USER.'-'.DB_PASS.'-'.DB_DATABASE;
    $objCurriculum =  new Curriculum($db);
	
	$operacion = $_GET['OPERACION'];
        
	switch($operacion) {		
        case 'REGISTRAR':            
            $atributos = array(
                'cedula'            =>$_POST['tx_cedula'],
                'apellidos'         =>$_POST['tx_apellido'],
                'nombres'           =>$_POST['tx_nombre'], 
                'sexo'              =>$_POST['sel_sexo'], 
                'fecnac'            =>$_POST['tx_fecha'], 
                'lugarnac'          =>$_POST['tx_lugar'], 
                'telefono'          =>$_POST['tx_telefono'],
                'email'             =>$_POST['tx_email'],
                'cod_profesion'     =>$_POST['sel_profesion'],
                'grado_instruccion' =>$_POST['sel_grado'],
                'area_desempeno'    =>$_POST['sel_area_desempeno'], 
                'anios_exp'         =>$_POST['tx_anho'], 
                'observacion'       =>$_POST['tx_observacion'], 
                'fecha_reg'         =>'NOW()', 
                'direccion'         =>$_POST['tx_direccion'],
                'foto'              =>$_FILES['file_foto']['name'], 
                'archivo'           =>$_FILES['file_documento']['name']
            );

            $resp          = $objCurriculum->save_curriculum($atributos);            
            echo $resp;           
            //CREAMOS DIRECTORIO
            // Estructura de la carpeta deseada        
            //echo json_encode($post);
        break;

        case 'GETPORFESION':
            $data = $objCurriculum->get_profesion();
            $json       = new Services_JSON();
            $resp       = $json->encode($data);
            echo $resp  ;
            //echo json_encode($data);
        break;

        case 'GETINSTRUCCION':           
            $data       = $objCurriculum->get_instruccion();
            $json       = new Services_JSON();
            $resp       = $json->encode($data);
            echo $resp;            
        break;

        case 'GETDESEMPENO':           
            $data       = $objCurriculum->get_desempeno();
            $json       = new Services_JSON();
            $resp       = $json->encode($data);
            echo $resp;            
        break;
	}	
?>