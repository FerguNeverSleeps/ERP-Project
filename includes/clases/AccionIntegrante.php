<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'modelos/ModeloIntegrante.php');
class AccionIntegrante{
	private $modelo = null;
	private $objAuditoria = null;

	public function save($request,$files){
		$this->modelo = new ModeloIntegrante();
		$response = $this->modelo->save($request,$files);
		return $response;
	}

	public function get_integrante($request){
		$this->modelo = new ModeloIntegrante();
		$response = $this->modelo->getIntegrante($request);
		return $response;
	}

	public function get_list($request){
		$this->modelo = new ModeloIntegrante();
		$response = $this->modelo->getList($request);
		return $response;
	}

	public function get_planilla_horizontal($request){
		$this->modelo = new ModeloIntegrante();
		$response = $this->modelo->getPlanillaHorizontal($request);
		return $response;
	}
}
?>