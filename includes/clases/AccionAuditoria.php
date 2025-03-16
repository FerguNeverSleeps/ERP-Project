<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'Auditoria.php');
require_once($DIR.'modelos/ModeloAuditoria.php');
class AccionAuditoria{
	private $modelo = null;
	private $objAuditoria = null;

 // FUNCTIONS
	public function save($request){
		$this->modelo = new ModeloAuditoria();
		$this->objAuditoria = new Auditoria();
		$this->objAuditoria->setAuditoriaId((isset($request["auditoria_id"]))?$request["auditoria_id"]:'');
		$this->objAuditoria->setMensaje((isset($request["mensaje"]))?$request["mensaje"]:'');
		$this->objAuditoria->setAuditoriaModId((isset($request["auditoria_mod_id"]))?$request["auditoria_mod_id"]:'');
		$this->objAuditoria->setOperacion((isset($request["operacion"]))?$request["operacion"]:'');
		$this->objAuditoria->setIdAfectado((isset($request["id_afectado"]))?$request["id_afectado"]:'');
		$this->objAuditoria->setCodigoAfectado((isset($request["codigo_afectado"]))?$request["codigo_afectado"]:'');
		$this->objAuditoria->setTablaAfectada((isset($request["tabla_afectada"]))?$request["tabla_afectada"]:'');
		$this->objAuditoria->setUsuario((isset($request["usuario"]))?$request["usuario"]:$_SESSION['usuario']);
		$this->objAuditoria->setFechaOperacion((isset($request["fecha_operacion"]))?$request["fecha_operacion"]:'');
		$response = $this->modelo->save($this->objAuditoria,$request);
		return $response;
	}
	public function get_one($request){
		$this->modelo = new ModeloAuditoria();
		$this->objAuditoria = new Auditoria();
		$this->objAuditoria->setAuditoriaId($request["auditoria_id"]);
		$response = $this->modelo->getOne($this->objAuditoria);
		return $response;
	}
	public function get_list($request){
		$this->modelo = new ModeloAuditoria();
		$this->objAuditoria = new Auditoria();
		$response = $this->modelo->getList($request);
		return $response;
	}
}
?>