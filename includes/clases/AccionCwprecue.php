<?php
require_once('Cwconcue.php');
require_once('modelos/ModeloCwprecue.php');
class AccionCwprecue{
	private $modelo = null;
	private $objCwconcue = null;

 // FUNCTIONS
	public function save($request){
		$this->modelo = new ModeloCwprecue();
		$this->objCwconcue = new Cwconcue();
		$this->objCwconcue->setCwconcueId((isset($request["id"]))?$request["id"]:'');
		$this->objCwconcue->setCuenta((isset($request["Cuenta"]))?$request["Cuenta"]:'');
		$this->objCwconcue->setDescrip((isset($request["Descrip"]))?$request["Descrip"]:'');
		/*$this->objCwconcue->setNivel((isset($request["Nivel"]))?$request["Nivel"]:'');
		$this->objCwconcue->setTipo((isset($request["Tipo"]))?$request["Tipo"]:'');
		$this->objCwconcue->setDescrip((isset($request["Descrip"]))?$request["Descrip"]:'');
		$this->objCwconcue->setBancos((isset($request["Bancos"]))?$request["Bancos"]:'');
		$this->objCwconcue->setMonPre((isset($request["MonPre"]))?$request["MonPre"]:'');
		$this->objCwconcue->setMonModif((isset($request["MonModif"]))?$request["MonModif"]:'');
		$this->objCwconcue->setFechaNuevo((isset($request["FechaNuevo"]))?$request["FechaNuevo"]:'');
		$this->objCwconcue->setCtaNueva((isset($request["CtaNueva"]))?$request["CtaNueva"]:'');
		$this->objCwconcue->setAuxunico((isset($request["Auxunico"]))?$request["Auxunico"]:'');
		$this->objCwconcue->setMonetaria((isset($request["Monetaria"]))?$request["Monetaria"]:'');
		$this->objCwconcue->setCtaajuste((isset($request["Ctaajuste"]))?$request["Ctaajuste"]:'');
		$this->objCwconcue->setMarca((isset($request["Marca"]))?$request["Marca"]:'');
		$this->objCwconcue->setMonPreu((isset($request["MonPreu"]))?$request["MonPreu"]:'');
		$this->objCwconcue->setMonModify((isset($request["MonModify"]))?$request["MonModify"]:'');
		$this->objCwconcue->setCcostos((isset($request["Ccostos"]))?$request["Ccostos"]:'');
		$this->objCwconcue->setTerceros((isset($request["Terceros"]))?$request["Terceros"]:'');
		$this->objCwconcue->setCuentalt((isset($request["Cuentalt"]))?$request["Cuentalt"]:'');
		$this->objCwconcue->setDescripalt((isset($request["Descripalt"]))?$request["Descripalt"]:'');
		$this->objCwconcue->setFiscaltipo((isset($request["Fiscaltipo"]))?$request["Fiscaltipo"]:'');
		$this->objCwconcue->setTipocosto((isset($request["Tipocosto"]))?$request["Tipocosto"]:'');*/
		$response = $this->modelo->save($this->objCwconcue,$request);
		return $response;
	}
	public function get_cuenta($request){
		$this->modelo = new ModeloCwprecue();
		$this->objCwconcue = new Cwconcue();
		$this->objCwconcue->setCwconcueId($request["id"]);
		$response = $this->modelo->getCuenta($this->objCwconcue,$request);
		return $response;
	}
	public function get_list($request){
		$this->modelo = new ModeloCwprecue();
		$this->objCwconcue = new Cwconcue();
		$response = $this->modelo->getList($request);
		return $response;
	}
	public function get_list_combo($request){
		$this->modelo = new ModeloCwprecue();
		$this->objCwconcue = new Cwconcue();
		$response = $this->modelo->getListCombo($request);
		return $response;
	}
}
?>