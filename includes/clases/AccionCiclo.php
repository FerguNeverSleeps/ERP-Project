<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'modelos/ModeloCiclo.php');
class AccionCiclo{
	private $modelo = null;
	private $objAuditoria = null;

	public function save_ciclo($request){
		$this->modelo = new ModeloCiclo();
		$response = $this->modelo->saveCiclo($request);
		return $response;
	}
	public function list_ciclo($request){
		$this->modelo = new ModeloCiclo();
		$response = $this->modelo->listCiclo($request);
		return $response;
	}

}
?>