<?php
 class EventoUsuario{
	private $eventoUsuarioId;
	public $primaryKeyField = 'eventoUsuarioId';
	public $primaryKeyFieldInDb = 'evento_usuario_id';
	private $evento;
	private $fechaInicio;
	private $fechaFin;
	private $horaInicio;
	private $horaFin;
	private $usuario;
	private $fechaCreacion;
	private $color;

 // FUNCTIONS
	public function getEventoUsuarioId(){
		return $this->eventoUsuarioId;
	}
	public function setEventoUsuarioId($eventoUsuarioId){
		$this->eventoUsuarioId = $eventoUsuarioId;
	}
	public function getEvento(){
		return $this->evento;
	}
	public function setEvento($evento){
		$this->evento = $evento;
	}
	public function getFechaInicio(){
		return $this->fechaInicio;
	}
	public function setFechaInicio($fechaInicio){
		$this->fechaInicio = $fechaInicio;
	}
	public function getFechaFin(){
		return $this->fechaFin;
	}
	public function setFechaFin($fechaFin){
		$this->fechaFin = $fechaFin;
	}
	public function getHoraInicio(){
		return $this->horaInicio;
	}
	public function setHoraInicio($horaInicio){
		$this->horaInicio = $horaInicio;
	}
	public function getHoraFin(){
		return $this->horaFin;
	}
	public function setHoraFin($horaFin){
		$this->horaFin = $horaFin;
	}
	public function getUsuario(){
		return $this->usuario;
	}
	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}
	public function getFechaCreacion(){
		return $this->fechaCreacion;
	}
	public function setFechaCreacion($fechaCreacion){
		$this->fechaCreacion = $fechaCreacion;
	}
	public function getColor(){
		return $this->color;
	}
	public function setColor($color){
		$this->color = $color;
	}
}
?>