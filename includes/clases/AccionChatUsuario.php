<?php
require_once('ChatUsuario.php');
require_once('modelos/ModeloChatUsuario.php');
class AccionChatUsuario{
	private $modelo = null;
	private $objChatUsuario = null;

 // FUNCTIONS
	public function save($request){
		$this->modelo = new ModeloChatUsuario();
		$this->objChatUsuario = new ChatUsuario();
		$this->objChatUsuario->setChatUsuarioId((isset($request["chat_usuario_id"]))?$request["chat_usuario_id"]:'');
		$this->objChatUsuario->setUsuarioOrigen((isset($request["usuario_origen"]))?$request["usuario_origen"]:$_SESSION['usuario']);
		$this->objChatUsuario->setUsuarioDestino((isset($request["usuario_destino"]))?$request["usuario_destino"]:'');
		$this->objChatUsuario->setMensaje((isset($request["mensaje"]))?$request["mensaje"]:'');
		$this->objChatUsuario->setFechaCreacion((isset($request["fecha_creacion"]))?$request["fecha_creacion"]:'');
		$response = $this->modelo->save($this->objChatUsuario,$request);
		return $response;
	}
	public function get_one($request){
		$this->modelo = new ModeloChatUsuario();
		$this->objChatUsuario = new ChatUsuario();
		$this->objChatUsuario->setChatUsuarioId($request["chat_usuario_id"]);
		$response = $this->modelo->getOne($this->objChatUsuario);
		return $response;
	}
	public function get_list($request){
		$this->modelo = new ModeloChatUsuario();
		$this->objChatUsuario = new ChatUsuario();
		$response = $this->modelo->getList($request);
		return $response;
	}
	public function get_list_usuarios($request){
		$this->modelo = new ModeloChatUsuario();
		$this->objChatUsuario = new ChatUsuario();
		$response = $this->modelo->getListUsuarios($request);
		return $response;
	}
}
?>