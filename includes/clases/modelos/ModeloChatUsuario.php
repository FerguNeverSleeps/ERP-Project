<?php
 require_once('../BDControlador.php');
 require_once('../ChatUsuario.php');
class ModeloChatUsuario{
	private $bdControlador = null;

 // FUNCTIONS
	public function save(ChatUsuario &$ChatUsuario,$request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["operacion"] == 'add'){
				$saveQuery = "insert into chat_usuario (chat_usuario_id,usuario_origen,usuario_destino,mensaje) values (NULL,'{$ChatUsuario->getUsuarioOrigen()}',".(($ChatUsuario->getUsuarioDestino()=='')?"NULL":"'".$ChatUsuario->getUsuarioDestino()."'").",'{$ChatUsuario->getMensaje()}')";
			}
			else if($request["operacion"] == 'update'){
				$saveQuery = "update chat_usuario  set chat_usuario_id = '{$ChatUsuario->getChatUsuarioId()}',usuario_origen = '{$ChatUsuario->getUsuarioOrigen()}',usuario_destino = '{$ChatUsuario->getUsuarioDestino()}',mensaje = '{$ChatUsuario->getMensaje()}',fecha_creacion = '{$ChatUsuario->getFechaCreacion()}' where chat_usuario_id = '{$ChatUsuario->getChatUsuarioId()}'";
			}
			$this->bdControlador->setQuery($saveQuery);
			$this->bdControlador->ejecutaInstruccion();
			if($request["operacion"] == 'add'){
				$lastId =$this->bdControlador->lastId();
			}
			$this->bdControlador->commit();
			return  Array('success' => true,'mensaje' =>'OK','chat_usuario_id' => $lastId);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getOne(ChatUsuario &$ChatUsuario){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$query = '';
				$query = "select chat_usuario_id,usuario_origen,usuario_destino,mensaje,fecha_creacion from chat_usuario where chat_usuario_id = '{$ChatUsuario->primaryKeyFieldInDb}'";
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$matches = Array();
			while ($item = $this->bdControlador->fetch($result)){
				$matches[] = $item;
			}
			return  Array('success' => true,'totalCount' => $num,'matches' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getList($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$query = '';
			$query = "select c.chat_usuario_id,c.usuario_origen,c.usuario_destino,c.mensaje,date_format(c.fecha_creacion,'%d/%m/%Y %H:%i') fecha_creacion,(case when c.usuario_origen='".$_SESSION['usuario']."' then u1.nombreyapellido else u2.nombreyapellido end) nombre,(case when c.usuario_origen='".$_SESSION['usuario']."' then 'out' else 'in' end) tipo_mensaje from chat_usuario c left join usuarios u1 on u1.usuario = c.usuario_origen left join usuarios u2 on u2.usuario = c.usuario_origen ";
			if($request['usuario_destino'] != ''){
				$query .= " where usuario_destino = '".$request['usuario_destino']."' ";
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
	public function getListUsuarios($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$query = '';
				$query = "select nombreyapellido,usuario from usuarios where 1";
			if($request['patronbuscar'] != ''){
				$query .= " and (chat_usuario_id like '%{$request}%') or  and (usuario_origen like '%{$request}%') or  and (usuario_destino like '%{$request}%') or  and (mensaje like '%{$request}%') or  and (fecha_creacion like '%{$request}%') ";
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
}
?>