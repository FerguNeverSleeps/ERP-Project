<?php
 class Clientes{
	private $idCliente;
	public $primaryKeyField = 'idCliente';
	public $primaryKeyFieldInDb = 'id_cliente';
	private $codCliente;
	private $nombre;
	private $fnacimiento;
	private $representante;
	private $direccion;
	private $altena;
	private $alterna2;
	private $telefonos;
	private $fax;
	private $email;
	private $permitecredito;
	private $limite;
	private $dias;
	private $tolerancia;
	private $porcParcial;
	private $porcDescuentoGlobal;
	private $calcRetenImpuestoIslr;
	private $calcRetenImpuestoIva;
	private $codVendedor;
	private $codZona;
	private $rif;
	private $nit;
	private $contribuyenteEspecial;
	private $retenidoPorCliente;
	private $codTipoCliente;
	private $codEntidad;
	private $codTipoPrecio;
	private $clase;
	private $calcRetenImpuesto1x1000;
	private $estado;
	private $cuentaContable;
	private $codMediq;
	private $foto;
	private $pais;
	private $localidad;
	private $lugarEntrega;
	private $empresaZonaLibre;
	private $grupoEconomico;
	private $marca;
	private $neto;
	private $total;
	private $cuotaMensual;

 // FUNCTIONS
	public function getIdCliente(){
		return $this->idCliente;
	}
	public function setIdCliente($idCliente){
		$this->idCliente = $idCliente;
	}
	public function getCodCliente(){
		return $this->codCliente;
	}
	public function setCodCliente($codCliente){
		$this->codCliente = $codCliente;
	}
	public function getNombre(){
		return $this->nombre;
	}
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}
	public function getFnacimiento(){
		return $this->fnacimiento;
	}
	public function setFnacimiento($fnacimiento){
		$this->fnacimiento = $fnacimiento;
	}
	public function getRepresentante(){
		return $this->representante;
	}
	public function setRepresentante($representante){
		$this->representante = $representante;
	}
	public function getDireccion(){
		return $this->direccion;
	}
	public function setDireccion($direccion){
		$this->direccion = $direccion;
	}
	public function getAltena(){
		return $this->altena;
	}
	public function setAltena($altena){
		$this->altena = $altena;
	}
	public function getAlterna2(){
		return $this->alterna2;
	}
	public function setAlterna2($alterna2){
		$this->alterna2 = $alterna2;
	}
	public function getTelefonos(){
		return $this->telefonos;
	}
	public function setTelefonos($telefonos){
		$this->telefonos = $telefonos;
	}
	public function getFax(){
		return $this->fax;
	}
	public function setFax($fax){
		$this->fax = $fax;
	}
	public function getEmail(){
		return $this->email;
	}
	public function setEmail($email){
		$this->email = $email;
	}
	public function getPermitecredito(){
		return $this->permitecredito;
	}
	public function setPermitecredito($permitecredito){
		$this->permitecredito = $permitecredito;
	}
	public function getLimite(){
		return $this->limite;
	}
	public function setLimite($limite){
		$this->limite = $limite;
	}
	public function getDias(){
		return $this->dias;
	}
	public function setDias($dias){
		$this->dias = $dias;
	}
	public function getTolerancia(){
		return $this->tolerancia;
	}
	public function setTolerancia($tolerancia){
		$this->tolerancia = $tolerancia;
	}
	public function getPorcParcial(){
		return $this->porcParcial;
	}
	public function setPorcParcial($porcParcial){
		$this->porcParcial = $porcParcial;
	}
	public function getPorcDescuentoGlobal(){
		return $this->porcDescuentoGlobal;
	}
	public function setPorcDescuentoGlobal($porcDescuentoGlobal){
		$this->porcDescuentoGlobal = $porcDescuentoGlobal;
	}
	public function getCalcRetenImpuestoIslr(){
		return $this->calcRetenImpuestoIslr;
	}
	public function setCalcRetenImpuestoIslr($calcRetenImpuestoIslr){
		$this->calcRetenImpuestoIslr = $calcRetenImpuestoIslr;
	}
	public function getCalcRetenImpuestoIva(){
		return $this->calcRetenImpuestoIva;
	}
	public function setCalcRetenImpuestoIva($calcRetenImpuestoIva){
		$this->calcRetenImpuestoIva = $calcRetenImpuestoIva;
	}
	public function getCodVendedor(){
		return $this->codVendedor;
	}
	public function setCodVendedor($codVendedor){
		$this->codVendedor = $codVendedor;
	}
	public function getCodZona(){
		return $this->codZona;
	}
	public function setCodZona($codZona){
		$this->codZona = $codZona;
	}
	public function getRif(){
		return $this->rif;
	}
	public function setRif($rif){
		$this->rif = $rif;
	}
	public function getNit(){
		return $this->nit;
	}
	public function setNit($nit){
		$this->nit = $nit;
	}
	public function getContribuyenteEspecial(){
		return $this->contribuyenteEspecial;
	}
	public function setContribuyenteEspecial($contribuyenteEspecial){
		$this->contribuyenteEspecial = $contribuyenteEspecial;
	}
	public function getRetenidoPorCliente(){
		return $this->retenidoPorCliente;
	}
	public function setRetenidoPorCliente($retenidoPorCliente){
		$this->retenidoPorCliente = $retenidoPorCliente;
	}
	public function getCodTipoCliente(){
		return $this->codTipoCliente;
	}
	public function setCodTipoCliente($codTipoCliente){
		$this->codTipoCliente = $codTipoCliente;
	}
	public function getCodEntidad(){
		return $this->codEntidad;
	}
	public function setCodEntidad($codEntidad){
		$this->codEntidad = $codEntidad;
	}
	public function getCodTipoPrecio(){
		return $this->codTipoPrecio;
	}
	public function setCodTipoPrecio($codTipoPrecio){
		$this->codTipoPrecio = $codTipoPrecio;
	}
	public function getClase(){
		return $this->clase;
	}
	public function setClase($clase){
		$this->clase = $clase;
	}
	public function getCalcRetenImpuesto1x1000(){
		return $this->calcRetenImpuesto1x1000;
	}
	public function setCalcRetenImpuesto1x1000($calcRetenImpuesto1x1000){
		$this->calcRetenImpuesto1x1000 = $calcRetenImpuesto1x1000;
	}
	public function getEstado(){
		return $this->estado;
	}
	public function setEstado($estado){
		$this->estado = $estado;
	}
	public function getCuentaContable(){
		return $this->cuentaContable;
	}
	public function setCuentaContable($cuentaContable){
		$this->cuentaContable = $cuentaContable;
	}
	public function getCodMediq(){
		return $this->codMediq;
	}
	public function setCodMediq($codMediq){
		$this->codMediq = $codMediq;
	}
	public function getFoto(){
		return $this->foto;
	}
	public function setFoto($foto){
		$this->foto = $foto;
	}
	public function getPais(){
		return $this->pais;
	}
	public function setPais($pais){
		$this->pais = $pais;
	}
	public function getLocalidad(){
		return $this->localidad;
	}
	public function setLocalidad($localidad){
		$this->localidad = $localidad;
	}
	public function getLugarEntrega(){
		return $this->lugarEntrega;
	}
	public function setLugarEntrega($lugarEntrega){
		$this->lugarEntrega = $lugarEntrega;
	}
	public function getEmpresaZonaLibre(){
		return $this->empresaZonaLibre;
	}
	public function setEmpresaZonaLibre($empresaZonaLibre){
		$this->empresaZonaLibre = $empresaZonaLibre;
	}
	public function getGrupoEconomico(){
		return $this->grupoEconomico;
	}
	public function setGrupoEconomico($grupoEconomico){
		$this->grupoEconomico = $grupoEconomico;
	}
	public function getMarca(){
		return $this->marca;
	}
	public function setMarca($marca){
		$this->marca = $marca;
	}
	public function getNeto(){
		return $this->neto;
	}
	public function setNeto($neto){
		$this->neto = $neto;
	}
	public function getTotal(){
		return $this->total;
	}
	public function setTotal($total){
		$this->total = $total;
	}
	public function getCuotaMensual(){
		return $this->cuotaMensual;
	}
	public function setCuotaMensual($cuotaMensual){
		$this->cuotaMensual = $cuotaMensual;
	}
}
?>