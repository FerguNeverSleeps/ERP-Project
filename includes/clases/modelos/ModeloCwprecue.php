<?php
if(!isset($DIR)){
	$DIR = "../";
}
 require_once('../BDControlador.php');
 require_once('../Cwconcue.php');
class ModeloCwprecue{
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
				$saveQuery = "insert into cwprecue (id,CodCue,Denominacion) values (NULL,'{$Cwconcue->getCuenta()}','{$Cwconcue->getDescrip()}')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update cwprecue  set CodCue = '{$Cwconcue->getCuenta()}',Denominacion = '{$Cwconcue->getDescrip()}' where id = '{$Cwconcue->getCwconcueId()}'";
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
				$query = "select id,CodCue Cuenta,Tipocta,Denominacion Descrip,Tipopuc from cwprecue where id = '".$request['id'] ."'";
			
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
				$query = "select id,CodCue Cuenta,Tipocta,Denominacion Descrip,Tipopuc from cwprecue where 1";
			if($request['texto_buscar'] != ''){
				$texto = $this->bdControlador->real_escape_string($request['texto_buscar']);
				$query .= " and (CodCue like '%".$texto."%' or  Denominacion like '%".$texto."%')";
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
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select concat(CodCue,' - ',Denominacion) CuentaDescrip,id,CodCue Cuenta,Tipocta,Denominacion Descrip,Tipopuc from cwprecue where 1";
			
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