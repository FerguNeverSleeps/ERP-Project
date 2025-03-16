<?php

require_once 'modelos/ModeloEmpresa.php';
//if(!isset($_SESSION)){session_start();} 

class AccionEmpresa {
	public function get_empresas($request){
		$empresaModelo = new ModeloEmpresa();
		$response = $empresaModelo->getEmpresas($request);
		
		return $response;
	}
}

?>