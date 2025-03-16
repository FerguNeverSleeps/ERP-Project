<?php
 require_once('../BDControlador.php');
 require_once('../EventoUsuario.php');
class ModeloEventoUsuario{
	private $bdControlador = null;

 // FUNCTIONS
	public function save(EventoUsuario &$EventoUsuario,$request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();

		try{
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["operacion"] == 'add'){
				$saveQuery = "insert into evento_usuario (evento_usuario_id,evento,color,fecha_inicio,fecha_fin,hora_inicio,hora_fin,usuario) values (NULL,'{$EventoUsuario->getEvento()}','{$EventoUsuario->getColor()}','{$EventoUsuario->getFechaInicio()}','{$EventoUsuario->getFechaFin()}','{$EventoUsuario->getHoraInicio()}','{$EventoUsuario->getHoraFin()}','{$EventoUsuario->getUsuario()}')";
			}
			else if($request["operacion"] == 'update'){
				$saveQuery = "update evento_usuario  set evento_usuario_id = '{$EventoUsuario->getEventoUsuarioId()}',evento = '{$EventoUsuario->getEvento()}',fecha_inicio = '{$EventoUsuario->getFechaInicio()}',fecha_fin = '{$EventoUsuario->getFechaFin()}',hora_inicio = '{$EventoUsuario->getHoraInicio()}',hora_fin = '{$EventoUsuario->getHoraFin()}',usuario = '{$EventoUsuario->getUsuario()}',fecha_creacion = '{$EventoUsuario->getFechaCreacion()}' where evento_usuario_id = '{$EventoUsuario->getEventoUsuarioId()}'";
			}
			$this->bdControlador->setQuery($saveQuery);
			$this->bdControlador->ejecutaInstruccion();
			if($request["operacion"] == 'add'){
				$lastId =$this->bdControlador->lastId();
			}
			$this->bdControlador->commit();
			return  Array('success' => true,'mensaje' =>'OK','evento_usuario_id' => $lastId);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getOne(EventoUsuario &$EventoUsuario){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$query = '';
				$query = "select evento_usuario_id,evento,fecha_inicio,fecha_fin,hora_inicio,hora_fin,usuario,fecha_creacion from evento_usuario where evento_usuario_id = '{$EventoUsuario->primaryKeyFieldInDb}'";
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
	public function getListMonth($request){
		$this->bdControlador = new BDControlador();
		try{
			//$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';

			if($request['tipo_calendario'] == 0){
				$query = "select evento_usuario_id,evento,color,fecha_inicio,fecha_fin,hora_inicio,hora_fin,usuario,fecha_creacion from evento_usuario ";
				$query .= " where usuario= '".$_SESSION['usuario']."' and month(fecha_inicio) = '".$request['mes']."'";
			}
			elseif($request['tipo_calendario'] == 1){
				//(case when dia_fiesta=1 then 'No Laborable' when dia_fiesta=2 then 'Media Jornada' when dia_fiesta=3 then 'Feriado' else '' end) evento,
				$query = "select id evento_usuario_id,
				(case when DAYOFWEEK(fecha) in (1,7) then 'Fin de Semana' else (case when dia_fiesta=1 then 'No Laborable' when dia_fiesta=2 then 'Media Jornada' when dia_fiesta=3 then 'Feriado' else 'Laborable' end) end) evento,
				(case when DAYOFWEEK(fecha) in (1,7) then 'yellow' else (case when dia_fiesta=1 then 'red' when dia_fiesta=2 then 'gray' when dia_fiesta=3 then 'green' else '' end) end) color,
				fecha fecha_inicio,fecha fecha_fin,'00:00:00' hora_inicio,'23:59:59' hora_fin,'' usuario,'' fecha_creacion 
				from ".$_SESSION['bd'].".nomcalendarios_tiposnomina ";
				$query .= " where cod_tiponomina='".$_SESSION['codigo_nomina']."'";//month(fecha_inicio) = '".$request['mes']."' dia_fiesta!= 0
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
	public function getListWeek($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select evento_usuario_id,evento,color,fecha_inicio,fecha_fin,hora_inicio,hora_fin,usuario,fecha_creacion from evento_usuario ";
			$query .= " where usuario= '".$_SESSION['usuario']."' and month(fecha_inicio) = '".$request['mes']."'";
			
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
	public function getListDay($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select evento_usuario_id,evento,color,fecha_inicio,fecha_fin,hora_inicio,hora_fin,usuario,fecha_creacion from evento_usuario ";
			$query .= " where usuario= '".$_SESSION['usuario']."' and day(fecha_inicio) = '".$request['dia']."'";
			
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