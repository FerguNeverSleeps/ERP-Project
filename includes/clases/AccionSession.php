<?php

require_once 'modelos/ModeloSession.php';
//if(!isset($_SESSION)){session_start();} 

class AccionSession {
	
	private $session = "";
	
	public function solicita_autorizacion($request){
		$sessionModelo = new ModeloSession();
		
		$response = $sessionModelo->solicitaAutorizacion($request);
		
		return $response;
	}
}

?>