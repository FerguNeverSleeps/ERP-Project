<?php
if(!isset($DIR)){
	$DIR = "../";
}
 require_once('../BDControlador.php');
 require_once('../Log_transacciones.php');
class ModeloLog_transacciones{
	private $bdControlador = null;

 // FUNCTIONS
	//public function save(Log_transacciones &$Log_transacciones,$request){
	public function save($request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["op"] == 'add'){
				$saveQuery = "insert into log_transacciones (cod_log,descripcion,fecha_hora,modulo,url,accion,valor,usuario) 
				values (NULL,'".$request['descripcion']."',now(),'".$request['modulo']."','".$request['url']."','".$request['accion']."','".$request['valor']."','".$_SESSION['nombre'] ."')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update log_transacciones  set cod_log = '{$cod_log->getcod_log()}'";
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
	public function getLog_transacciones(Log_transacciones &$Log_transacciones,$request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select cod_log,descripcion,fecha_hora,modulo,url,accion,valor,usuario from log_transacciones where cod_log = '".$request['id'] ."'";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<log_transacciones>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</log_transacciones></message>';

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
			//echo	$query = '';
				$query = "select cod_log,descripcion,fecha_hora,modulo,url,accion,valor,usuario from Log_transacciones where 1";
			//exit(0);
			if($request['texto_buscar'] != ''){
				$texto = $this->bdControlador->real_escape_string($request['texto_buscar']);
				$query .= " and (cod_log like '%".$texto."%' or  descripcion like '%".$texto."%')";
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
				$query = "select cod_log,descripcion,fecha_hora,modulo,url,accion,valor,usuario from log_transacciones where 1";
			if($request['patronbuscar'] != ''){
				$query .= " and (cod_log like '%{$request}%') or  and (descripcion like '%{$request}%') or  and (usuario like '%{$request}%') ";
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