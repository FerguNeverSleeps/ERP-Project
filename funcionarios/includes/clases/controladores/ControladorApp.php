<?php
set_time_limit(0);
require_once '../../../generalp.config.inc.php';
//require_once '../AccionSession.php';
require_once '../AccionEventoUsuario.php';
require_once '../AccionEmpresa.php';
require_once '../AccionMaestro.php';
require_once '../AccionIntegrante.php';
require_once '../AccionCwconcue.php';
require_once '../AccionCwprecue.php';
require_once '../AccionLog_transacciones.php';
require_once '../AccionCiclo.php';

if(!isset($_SESSION)){session_start();} 


class ControladorApp {
	
	private $request = "";
	private $filesUploaded = "";
	private $accion = "";
	private $clase = "";
	private $operacion = "";
	private $obj = "";
	private $splitRequest = "";
    private $session = null;


        /*
	 * 
	 * 
	 */
	function __construct($filesUploaded,$request){//,Session &$session
                //$this->session = $session;
		$this->request   = $request;
		$this->filesUploaded   = $filesUploaded;
		if(!isset($this->request['file_get_contents'])){
			$this->splitRequest = explode("/",$this->request['accion']);
			 
			$this->clase     =  $this->splitRequest[0].$this->splitRequest[1];
			$this->accion    =  $this->splitRequest[2];
			//$this->accion    =  $request['accion'];
            if(isset($request['operacion']))
            	$this->operacion =  $request['operacion'];
            if(isset($request['start']))
            	$this->start     =  $request['start'];
            if(isset($request['limit']))
                $this->limit     =  $request['limit'];
        }
	}
	
	function validarOperacion(){
		//@TODO  validar si $request['operacion'] es una operacion valida o permitida
		
		return true;
	}
	
	
	function sistemaBloqueado(){
		//@TODO validar si el sistema ha sido bloqueado
		return false;
	}
	
		
	function sessionValida(){
           /*if(!isset($_SESSION['session_erp_user']) || empty ($_SESSION['session_erp_user']))
		return false;	 
           else*/
               return true;
	}
	
	function ejecutar(){
            
            if($this->validarOperacion() && !$this->sistemaBloqueado() && $this->sessionValida()){
                
                //
                if(isset($this->request['file_get_contents'])){
                	$resultado = file_get_contents($this->request['ruta']);
                }
                else{
                	$this->obj = new $this->clase();
                	$resultado = call_user_func_array(array($this->obj, strtolower($this->accion)), array("_request" => $this->request,"_files" => $this->filesUploaded));
				}
				
                if (isset($this->request['output']) && $this->request['output']=="json"){
                    $this->outPutJson($resultado);
				}
                elseif (isset($this->request['output']) && $this->request['output']=="excel"){
                    header("Content-type: application/vnd.ms-excel; charset=utf-8");
                    header("Content-type: application/x-msexcel; charset=utf-8");
                    header("Content-Disposition: attachment; filename=".$resultado['file_name'].".xls");
                    header("<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />");
                    echo $resultado['report'];
				}
                elseif (isset($this->request['output']) && $this->request['output']=="xml"){
                    header('Content-Type: application/xml; charset=utf-8');
                    echo $resultado;
				}
                elseif (isset($this->request['output']) && $this->request['output']=="txt" && isset($this->request['file_get_contents'])){
                    //header("Content-type: application/octet-stream");
                    header("Content-Disposition: attachment; filename=".$this->request['archivo']);
                    header("Content-type: application/force-download");
                    echo $resultado;
				}
                elseif (isset($this->request['output']) && $this->request['output']=="txt"){
                    echo $resultado;
				}
                elseif (!isset($this->request['output'])){
                    $this->outPutJson($resultado);
				}
				else{
                    $this->outPutJson($resultado);
				}
            }
            elseif( !$this->sessionValida() ){
                echo json_encode(array('success' => false,'mensaje' => 'La sesión ha caducado.','reload' => true));
            }
            
	}
	
	function outPutJson($respuesta){
		echo json_encode($respuesta);
	}	
}

/*$session = new Session();
if(isset($_SESSION['session_erp_user'])){
    $session = $_SESSION['session_erp_user'];
}*/
$obj = new ControladorApp($_FILES,$_REQUEST);//,$session
$obj->ejecutar();
/*foreach ($_FILES['archivos']['error'] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $carga_file = $_FILES['archivos'];
        $nombre_archivo_tmp = $_FILES['archivos']['tmp_name'][$key];
        $nombre_archivo = $_FILES['archivos']['name'][$key];
        echo $nombre_archivo_tmp. "<br>".$nombre_archivo_tmp;
        
    }
}*/
?>