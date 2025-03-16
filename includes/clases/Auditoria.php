<?php
 class Auditoria{
	private $auditoriaId;
	public $primaryKeyField = 'auditoriaId';
	public $primaryKeyFieldInDb = 'auditoria_id';
	private $mensaje;
	private $auditoriaModId;
	private $operacion;
	private $idAfectado;
	private $codigoAfectado;
	private $tablaAfectada;
	private $usuario;
	private $fechaOperacion;

 // FUNCTIONS
	public function getAuditoriaId(){
		return $this->auditoriaId;
	}
	public function setAuditoriaId($auditoriaId){
		$this->auditoriaId = $auditoriaId;
	}
	public function getMensaje(){
		return $this->mensaje;
	}
	public function setMensaje($mensaje){
		$this->mensaje = $mensaje;
	}
	public function getAuditoriaModId(){
		return $this->auditoriaModId;
	}
	public function setAuditoriaModId($auditoriaModId){
		$this->auditoriaModId = $auditoriaModId;
	}
	public function getOperacion(){
		return $this->operacion;
	}
	public function setOperacion($operacion){
		$this->operacion = $operacion;
	}
	public function getIdAfectado(){
		return $this->idAfectado;
	}
	public function setIdAfectado($idAfectado){
		$this->idAfectado = $idAfectado;
	}
	public function getCodigoAfectado(){
		return $this->codigoAfectado;
	}
	public function setCodigoAfectado($codigoAfectado){
		$this->codigoAfectado = $codigoAfectado;
	}
	public function getTablaAfectada(){
		return $this->tablaAfectada;
	}
	public function setTablaAfectada($tablaAfectada){
		$this->tablaAfectada = $tablaAfectada;
	}
	public function getUsuario(){
		return $this->usuario;
	}
	public function setUsuario($usuario){
		$this->usuario = $usuario;
	}
	public function getFechaOperacion(){
		return $this->fechaOperacion;
	}
	public function setFechaOperacion($fechaOperacion){
		$this->fechaOperacion = $fechaOperacion;
	}
}
?>