<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'modelos/ModeloMaestro.php');
class AccionMaestro{
	private $modelo = null;
	private $objAuditoria = null;

	public function save_posicion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->savePosicion($request);
		return $response;
	}

	public function save_funcion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->saveFuncion($request);
		return $response;
	}

	public function save_profesion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->saveProfesion($request);
		return $response;
	}

	public function get_list_pais($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListPais($request);
		return $response;
	}

	public function get_list_planilla($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListPlanilla($request);
		return $response;
	}

	public function get_list_profesion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListProfesion($request);
		return $response;
	}

	public function get_list_funcion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListFuncion($request);
		return $response;
	}

	public function get_list_situacion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListSituacion($request);
		return $response;
	}

	public function get_list_banco($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListBanco($request);
		return $response;
	}

	public function get_list_turno($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListTurno($request);
		return $response;
	}

	public function get_list_puesto($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListPuesto($request);
		return $response;
	}

	public function get_list_categoria($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListCategoria($request);
		return $response;
	}

	public function get_list_cargo($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListCargo($request);
		return $response;
	}

	public function get_list_nivel($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListNivel($request);
		return $response;
	}

	public function get_list_posicion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getListPosicion($request);
		return $response;
	}

	public function get_posicion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getPosicion($request);
		return $response;
	}

	public function get_profesion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getProfesion($request);
		return $response;
	}

	public function get_funcion($request){
		$this->modelo = new ModeloMaestro();
		$response = $this->modelo->getFuncion($request);
		return $response;
	}
}
?>