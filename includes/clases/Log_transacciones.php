<?php
 class Log_transacciones{
	private $cod_logId;
	public $primaryKeyField = 'cod_logId';
	public $primaryKeyFieldInDb = 'cod_log';
	private $cod_log;
	private $descripcion;
	private $fecha_hora;
	private $modulo;
	private $url;
	private $accion;
	private $valor;
	private $usuario;


 // FUNCTIONS
	public function getLog_transacciones(){
		return $this->cod_logId;
	}
	public function setLog_transacciones($cod_logId){
		$this->cod_logId = $cod_logId;
	}
	public function getcod_log(){
		return $this->cod_log;
	}
	public function setdescripcion($descripcion){
		$this->descripcion = $descripcion;
	}
	public function setfecha_hora($fecha_hora){
	$this->fecha_hora = $fecha_hora;
	}
	public function setmodulo($modulo){
	$this->modulo= $modulo;
	}
	public function seturl($url){
	$this->url= $url;
	}
	public function setaccion($accion){
	$this->accion= $accion;
	}
	public function setvalor($valor){
	$this->valor= $valor;
	}
	public function setusuario($usuario){
	$this->usuario= $usuario;
	}
}
?>