<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'modelos/ModeloInterfaz.php');
class AccionInterfaz{
	private $modelo = null;
	private $objAuditoria = null;

 // FUNCTIONS
	public function txt_retail_bos_producto($request){
		$this->modelo = new ModeloIterfaz();
		$response = $this->modelo->txtRetailBosProducto($request);
		return $response;
	}
	public function get_list_txt_productos_almacen($request){
		$this->modelo = new ModeloIterfaz();
		$response = $this->modelo->getListTxtProductosAlmacen($request);
		return $response;
	}
	public function txt_retail_bos_rebaja($request){
		$this->modelo = new ModeloIterfaz();
		$response = $this->modelo->txtRetailBosRebaja($request,$op);
		return $response;
	}
	public function get_list_txt_rebajas_almacen($request){
		$this->modelo = new ModeloIterfaz();
		$response = $this->modelo->getListTxtRebajasAlmacen($request);
		return $response;
	}
}
?>