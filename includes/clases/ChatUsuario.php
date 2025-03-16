<?php
 class ChatUsuario{
	private $chatUsuarioId;
	public $primaryKeyField = 'chatUsuarioId';
	public $primaryKeyFieldInDb = 'chat_usuario_id';
	private $usuarioOrigen;
	private $usuarioDestino;
	private $mensaje;
	private $fechaCreacion;

 // FUNCTIONS
	public function getChatUsuarioId(){
		return $this->chatUsuarioId;
	}
	public function setChatUsuarioId($chatUsuarioId){
		$this->chatUsuarioId = $chatUsuarioId;
	}
	public function getUsuarioOrigen(){
		return $this->usuarioOrigen;
	}
	public function setUsuarioOrigen($usuarioOrigen){
		$this->usuarioOrigen = $usuarioOrigen;
	}
	public function getUsuarioDestino(){
		return $this->usuarioDestino;
	}
	public function setUsuarioDestino($usuarioDestino){
		$this->usuarioDestino = $usuarioDestino;
	}
	public function getMensaje(){
		return $this->mensaje;
	}
	public function setMensaje($mensaje){
		$this->mensaje = $mensaje;
	}
	public function getFechaCreacion(){
		return $this->fechaCreacion;
	}
	public function setFechaCreacion($fechaCreacion){
		$this->fechaCreacion = $fechaCreacion;
	}
}
?>