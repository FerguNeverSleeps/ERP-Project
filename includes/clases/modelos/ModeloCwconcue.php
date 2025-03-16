<?php
if(!isset($DIR)){
	$DIR = "../";
}
 require_once('../BDControlador.php');
 require_once('../Cwconcue.php');
class ModeloCwconcue{
	private $bdControlador = null;

 // FUNCTIONS
	public function save(Cwconcue &$Cwconcue,$request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["op"] == 'add'){
				$saveQuery = "insert into cwconcue (id,Cuenta,Nivel,Tipo,Descrip,Bancos,MonPre,MonModif,FechaNuevo,CtaNueva,Auxunico,Monetaria,Ctaajuste,Marca,MonPreu,MonModify,Ccostos,Terceros,Cuentalt,Descripalt,Fiscaltipo,Tipocosto) values (NULL,'{$Cwconcue->getCuenta()}','{$Cwconcue->getNivel()}','{$Cwconcue->getTipo()}','{$Cwconcue->getDescrip()}','{$Cwconcue->getBancos()}','{$Cwconcue->getMonPre()}','{$Cwconcue->getMonModif()}','{$Cwconcue->getFechaNuevo()}','{$Cwconcue->getCtaNueva()}','{$Cwconcue->getAuxunico()}','{$Cwconcue->getMonetaria()}','{$Cwconcue->getCtaajuste()}','{$Cwconcue->getMarca()}','{$Cwconcue->getMonPreu()}','{$Cwconcue->getMonModify()}','{$Cwconcue->getCcostos()}','{$Cwconcue->getTerceros()}','{$Cwconcue->getCuentalt()}','{$Cwconcue->getDescripalt()}','{$Cwconcue->getFiscaltipo()}','{$Cwconcue->getTipocosto()}')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update cwconcue  set Cuenta = '{$Cwconcue->getCuenta()}',Nivel = '{$Cwconcue->getNivel()}',Tipo = '{$Cwconcue->getTipo()}',Descrip = '{$Cwconcue->getDescrip()}',Bancos = '{$Cwconcue->getBancos()}',MonPre = '{$Cwconcue->getMonPre()}',MonModif = '{$Cwconcue->getMonModif()}',FechaNuevo = '{$Cwconcue->getFechaNuevo()}',CtaNueva = '{$Cwconcue->getCtaNueva()}',Auxunico = '{$Cwconcue->getAuxunico()}',Monetaria = '{$Cwconcue->getMonetaria()}',Ctaajuste = '{$Cwconcue->getCtaajuste()}',Marca = '{$Cwconcue->getMarca()}',MonPreu = '{$Cwconcue->getMonPreu()}',MonModify = '{$Cwconcue->getMonModify()}',Ccostos = '{$Cwconcue->getCcostos()}',Terceros = '{$Cwconcue->getTerceros()}',Cuentalt = '{$Cwconcue->getCuentalt()}',Descripalt = '{$Cwconcue->getDescripalt()}',Fiscaltipo = '{$Cwconcue->getFiscaltipo()}',Tipocosto = '{$Cwconcue->getTipocosto()}' where id = '{$Cwconcue->getCwconcueId()}'";
			}
			$this->bdControlador->setQuery($saveQuery);
			$this->bdControlador->ejecutaInstruccion();
			if($request["op"] == 'add'){
				$lastId =$this->bdControlador->lastId();
			}
			$this->bdControlador->commit();
			return  Array('success' => true,'mensaje' =>'OK','id' => $lastId);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getCuenta(Cwconcue &$Cwconcue,$request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select id,Cuenta,Nivel,Tipo,Descrip,Bancos,MonPre,MonModif,FechaNuevo,CtaNueva,Auxunico,Monetaria,Ctaajuste,Marca,MonPreu,MonModify,Ccostos,Terceros,Cuentalt,Descripalt,Fiscaltipo,Tipocosto from cwconcue where id = '".$request['id'] ."'";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<cuenta>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</cuenta></message>';

			$this->bdControlador->desconectar();;
			return  $xml;//Array('success' => true,'data' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getList($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select id,Cuenta,Nivel,Tipo,Descrip,Bancos,MonPre,MonModif,FechaNuevo,CtaNueva,Auxunico,Monetaria,Ctaajuste,Marca,MonPreu,MonModify,Ccostos,Terceros,Cuentalt,Descripalt,Fiscaltipo,Tipocosto from cwconcue where 1";
			if($request['texto_buscar'] != ''){
				$texto = $this->bdControlador->real_escape_string($request['texto_buscar']);
				$query .= " and (Cuenta like '%".$texto."%' or  Descrip like '%".$texto."%')";
			}
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$matches = Array();
			while ($item = $this->bdControlador->fetch($result)){
				$matches[] = $item;
			}
			$this->bdControlador->desconectar();;
			return  Array('success' => true,'totalCount' => $num,'matches' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getListCombo($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select concat(Cuenta,' - ',Descrip) CuentaDescrip,cwconcue_id,Cuenta,Nivel,Tipo,Descrip,Bancos,MonPre,MonModif,FechaNuevo,CtaNueva,Auxunico,Monetaria,Ctaajuste,Marca,MonPreu,MonModify,Ccostos,Terceros,Cuentalt,Descripalt,Fiscaltipo,Tipocosto from cwconcue where 1";
			if($request['patronbuscar'] != ''){
				$query .= " and (cwconcue_id like '%{$request}%') or  and (Cuenta like '%{$request}%') or  and (Nivel like '%{$request}%') or  and (Tipo like '%{$request}%') or  and (Descrip like '%{$request}%') or  and (Bancos like '%{$request}%') or  and (MonPre like '%{$request}%') or  and (MonModif like '%{$request}%') or  and (FechaNuevo like '%{$request}%') or  and (CtaNueva like '%{$request}%') or  and (Auxunico like '%{$request}%') or  and (Monetaria like '%{$request}%') or  and (Ctaajuste like '%{$request}%') or  and (Marca like '%{$request}%') or  and (MonPreu like '%{$request}%') or  and (MonModify like '%{$request}%') or  and (Ccostos like '%{$request}%') or  and (Terceros like '%{$request}%') or  and (Cuentalt like '%{$request}%') or  and (Descripalt like '%{$request}%') or  and (Fiscaltipo like '%{$request}%') or  and (Tipocosto like '%{$request}%') ";
			}
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$matches = Array();
			while ($item = $this->bdControlador->fetch($result)){
				$matches[] = $item;
			}
			$this->bdControlador->desconectar();;
			return  ($matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
}
?>