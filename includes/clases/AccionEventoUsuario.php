<?php
require_once('EventoUsuario.php');
require_once('modelos/ModeloEventoUsuario.php');
class AccionEventoUsuario{
	private $modelo = null;
	private $objEventoUsuario = null;

 // FUNCTIONS
	public function save($request){
		$this->modelo = new ModeloEventoUsuario();
		$this->objEventoUsuario = new EventoUsuario();
		$this->objEventoUsuario->setEventoUsuarioId((isset($request["evento_usuario_id"]))?$request["evento_usuario_id"]:'');
		$this->objEventoUsuario->setEvento((isset($request["evento"]))?$request["evento"]:'');
		$request["fecha_inicio"] = substr($request["fecha_inicio"], 6, 4)."-".substr($request["fecha_inicio"], 3, 2)."-".substr($request["fecha_inicio"], 0, 2);
		if($request["fecha_fin"] != ""){
			$request["fecha_fin"] = substr($request["fecha_fin"], 6, 4)."-".substr($request["fecha_fin"], 3, 2)."-".substr($request["fecha_fin"], 0, 2);
		}
		
		$this->objEventoUsuario->setFechaInicio((isset($request["fecha_inicio"]))?$request["fecha_inicio"]:'');
		$this->objEventoUsuario->setFechaFin(($request["fecha_fin"] != "")?$request["fecha_fin"]:$request["fecha_inicio"]);
		$this->objEventoUsuario->setHoraInicio((isset($request["hora_inicio"]))?$request["hora_inicio"]:'');
		$this->objEventoUsuario->setHoraFin(($request["hora_fin"] != ":00")?$request["hora_fin"]:$request["hora_inicio"]);
		$this->objEventoUsuario->setUsuario((isset($request["usuario"]))?$request["usuario"]:$_SESSION['usuario']);
		$this->objEventoUsuario->setColor((isset($request["color"]))?$request["color"]:'');
		$response = $this->modelo->save($this->objEventoUsuario,$request);
		return $response;
	}
	public function get_one($request){
		$this->modelo = new ModeloEventoUsuario();
		$this->objEventoUsuario = new EventoUsuario();
		$this->objEventoUsuario->setEventoUsuarioId($request["evento_usuario_id"]);
		$response = $this->modelo->getOne($this->objEventoUsuario);
		return $response;
	}
	public function get_list_month($request){
		$this->modelo = new ModeloEventoUsuario();
		$this->objEventoUsuario = new EventoUsuario();
		$response = $this->modelo->getListMonth($request);
		return $response;
	}
	public function get_list_week($request){
		$this->modelo = new ModeloEventoUsuario();
		$this->objEventoUsuario = new EventoUsuario();
		$response = $this->modelo->getListWeek($request);
		return $response;
	}
	public function get_list_month_day($request){
		$this->modelo = new ModeloEventoUsuario();
		$this->objEventoUsuario = new EventoUsuario();
		$response = $this->modelo->getListDay($request);
		return $response;
	}
}
?>