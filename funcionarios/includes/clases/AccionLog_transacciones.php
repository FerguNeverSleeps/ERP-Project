<?php
require_once('Log_transacciones.php');
require_once('modelos/ModeloLog_transacciones.php');
class AccionLog_transacciones{
	private $modelo = null;
	private $objLog_transacciones = null;

 // FUNCTIONS
	public function save($request){
		$this->modelo = new ModeloLog_transacciones();
		$this->objLog_transacciones = new Log_transacciones();
		$this->objLog_transacciones->setcod_logid((isset($request["cod_log"]))?$request["cod_log"]:'');
		$this->objLog_transacciones->setdescripcion((isset($request["descripcion"]))?$request["descripcion"]:'');
		$this->objLog_transacciones->setfecha_hora((isset($request["fecha_hora"]))?$request["fecha_hora"]:'');
		$this->objLog_transacciones->setmodulo((isset($request["modulo"]))?$request["modulo"]:'');
		$this->objLog_transacciones->seturl((isset($request["url"]))?$request["url"]:'');
		$this->objLog_transacciones->setaccion((isset($request["accion"]))?$request["accion"]:'');
		$this->objLog_transacciones->setvalor((isset($request["valor"]))?$request["valor"]:''); 
		$this->objLog_transacciones->setusuario((isset($request["usuario"]))?$request["usuario"]:'');
		$response = $this->modelo->save($this->objLog_transacciones,$request);
		return $response;
	}
	public function get_log_transacciones($request){
		$this->modelo = new ModeloLog_transacciones();
		$this->objLog_transacciones = new Log_transacciones();
		//$this->objLog_transacciones->setcod_logid($request["cod_log"]);
		$response = $this->modelo->getLog_transacciones($this->objLog_transacciones,$request);
		return $response;
	}
	public function get_list_log_transacciones($request){
		$this->modelo = new ModeloLog_transacciones();
		$this->objLog_transacciones = new Log_transacciones();
		$response = $this->modelo->getList($request);
		return $response;
	}
	public function get_list_combo($request){
		$this->modelo = new ModeloLog_transacciones();
		$this->objLog_transacciones = new Log_transacciones();
		$response = $this->modelo->getListCombo($request);
		return $response;
	}
}
?>