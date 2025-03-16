<?php
 class Cwconcue{
	private $cwconcueId;
	public $primaryKeyField = 'cwconcueId';
	public $primaryKeyFieldInDb = 'cwconcue_id';
	private $cuenta;
	private $nivel;
	private $tipo;
	private $descrip;
	private $bancos;
	private $monPre;
	private $monModif;
	private $fechaNuevo;
	private $ctaNueva;
	private $auxunico;
	private $monetaria;
	private $ctaajuste;
	private $marca;
	private $monPreu;
	private $monModify;
	private $ccostos;
	private $terceros;
	private $cuentalt;
	private $descripalt;
	private $fiscaltipo;
	private $tipocosto;

 // FUNCTIONS
	public function getCwconcueId(){
		return $this->cwconcueId;
	}
	public function setCwconcueId($cwconcueId){
		$this->cwconcueId = $cwconcueId;
	}
	public function getCuenta(){
		return $this->cuenta;
	}
	public function setCuenta($cuenta){
		$this->cuenta = $cuenta;
	}
	public function getNivel(){
		return $this->nivel;
	}
	public function setNivel($nivel){
		$this->nivel = $nivel;
	}
	public function getTipo(){
		return $this->tipo;
	}
	public function setTipo($tipo){
		$this->tipo = $tipo;
	}
	public function getDescrip(){
		return $this->descrip;
	}
	public function setDescrip($descrip){
		$this->descrip = $descrip;
	}
	public function getBancos(){
		return $this->bancos;
	}
	public function setBancos($bancos){
		$this->bancos = $bancos;
	}
	public function getMonPre(){
		return $this->monPre;
	}
	public function setMonPre($monPre){
		$this->monPre = $monPre;
	}
	public function getMonModif(){
		return $this->monModif;
	}
	public function setMonModif($monModif){
		$this->monModif = $monModif;
	}
	public function getFechaNuevo(){
		return $this->fechaNuevo;
	}
	public function setFechaNuevo($fechaNuevo){
		$this->fechaNuevo = $fechaNuevo;
	}
	public function getCtaNueva(){
		return $this->ctaNueva;
	}
	public function setCtaNueva($ctaNueva){
		$this->ctaNueva = $ctaNueva;
	}
	public function getAuxunico(){
		return $this->auxunico;
	}
	public function setAuxunico($auxunico){
		$this->auxunico = $auxunico;
	}
	public function getMonetaria(){
		return $this->monetaria;
	}
	public function setMonetaria($monetaria){
		$this->monetaria = $monetaria;
	}
	public function getCtaajuste(){
		return $this->ctaajuste;
	}
	public function setCtaajuste($ctaajuste){
		$this->ctaajuste = $ctaajuste;
	}
	public function getMarca(){
		return $this->marca;
	}
	public function setMarca($marca){
		$this->marca = $marca;
	}
	public function getMonPreu(){
		return $this->monPreu;
	}
	public function setMonPreu($monPreu){
		$this->monPreu = $monPreu;
	}
	public function getMonModify(){
		return $this->monModify;
	}
	public function setMonModify($monModify){
		$this->monModify = $monModify;
	}
	public function getCcostos(){
		return $this->ccostos;
	}
	public function setCcostos($ccostos){
		$this->ccostos = $ccostos;
	}
	public function getTerceros(){
		return $this->terceros;
	}
	public function setTerceros($terceros){
		$this->terceros = $terceros;
	}
	public function getCuentalt(){
		return $this->cuentalt;
	}
	public function setCuentalt($cuentalt){
		$this->cuentalt = $cuentalt;
	}
	public function getDescripalt(){
		return $this->descripalt;
	}
	public function setDescripalt($descripalt){
		$this->descripalt = $descripalt;
	}
	public function getFiscaltipo(){
		return $this->fiscaltipo;
	}
	public function setFiscaltipo($fiscaltipo){
		$this->fiscaltipo = $fiscaltipo;
	}
	public function getTipocosto(){
		return $this->tipocosto;
	}
	public function setTipocosto($tipocosto){
		$this->tipocosto = $tipocosto;
	}
}
?>