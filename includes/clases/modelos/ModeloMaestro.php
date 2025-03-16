<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'BDControlador.php');
class ModeloMaestro{
	private $bdControlador = null;

 	public function savePosicion($request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["op"] == 'add'){
				$saveQuery = "insert into nomposicion (nomposicion_id,descripcion_posicion,sueldo_propuesto,sueldo_anual, partida, gastos_representacion, paga_gr,categoria_id,cargo_id,cod_nivel1,cod_nivel2,cod_nivel3,cod_nivel4,cod_nivel5,cod_nivel6,cod_nivel7) values (NULL,'".$request['descripcion_posicion']."','".$request['sueldo_propuesto']."','".$request['sueldo_anual']."','".$request['partida']."','".$request['gastos_representacion']."','".$request['paga_gr']."','".$request['categoria_id']."','".$request['cargo_id']."','".$request['cod_nivel1']."','".$request['cod_nivel2']."','".$request['cod_nivel3']."','".$request['cod_nivel4']."','".$request['cod_nivel5']."','".$request['cod_nivel6']."','".$request['cod_nivel7']."')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update nomposicion set descripcion_posicion='".$request['descripcion_posicion']."',sueldo_propuesto='".$request['sueldo_propuesto']."',sueldo_anual='".$request['sueldo_anual']."', partida='".$request['partida']."', gastos_representacion='".$request['gastos_representacion']."', paga_gr='".$request['paga_gr']."',categoria_id='".$request['categoria_id']."',cargo_id='".$request['cargo_id']."',cod_nivel1='".$request['cod_nivel1']."',cod_nivel2='".$request['cod_nivel2']."',cod_nivel3='".$request['cod_nivel3']."',cod_nivel4='".$request['cod_nivel4']."',cod_nivel5='".$request['cod_nivel5']."',cod_nivel6='".$request['cod_nivel6']."',cod_nivel7='".$request['cod_nivel7']."'  where nomposicion_id='".$request['nomposicion_id']."'";
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

	public function saveProfesion($request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["op"] == 'add'){
				$saveQuery = "insert into nomprofesiones (descrip) values ('".$request['descrip']."')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update nomprofesiones set descrip='".$request['descrip']."' where codorg='".$request['codorg']."'";
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
	public function saveFuncion($request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = '';
			if($request["op"] == 'add'){
				$saveQuery = "insert into nomfuncion (descripcion_funcion) values ('".$request['descripcion_funcion']."')";
			}
			else if($request["op"] == 'edit'){
				$saveQuery = "update nomfuncion set descripcion_funcion='".$request['descripcion_funcion']."' where nomfuncion_id='".$request['nomfuncion_id']."'";
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

	public function getListPais($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select id,iso,nombre from paises where 1";
			if($request['patronbuscar'] != ''){
				$query .= " and (nombre like '%".$request['patronbuscar']."%' or iso like '%".$request['patronbuscar']."%' ) ";
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

	public function getListPlanilla($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomtipos_nomina where 1";
			if($request['query'] != ''){
				$query .= " and (descrip like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
			}
			if(isset($request['cod_session']) && $request['operacion'] == "add"){
				$query .= " and (codtip = '".($_SESSION['codigo_nomina'])."') ";
			}
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);			

			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
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

	public function getListProfesion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomprofesiones where 1";
			if($request['query'] != ''){
				$query .= " and (descrip like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
			}
			$query .= " order by descrip asc ";
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

	public function getListFuncion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomfuncion where 1";
			if($request['query'] != ''){
				$query .= " and (descripcion_funcion like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
			}
			$query .= " order by descripcion_funcion asc ";
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

	public function getListSituacion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomsituaciones where 1";
			if($request['query'] != ''){
				$query .= " and (situacion like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListBanco($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nombancos where 1";
			if($request['query'] != ''){
				$query .= " and (des_ban like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListTurno($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select turno_id, date_format(entrada,'%h:%i:%s %p') as entrada, date_format(salida,'%h:%i:%s %p') as salida, 
			concat(turno_id,': Entrada: ',date_format(entrada,'%h:%i:%s %p'),' / Salida: ',date_format(salida,'%h:%i:%s %p')) descripcion1,
			concat('Turno ', descripcion) as descripcion from nomturnos where 1";
			if($request['query'] != ''){
				$query .= " and (descripcion like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListPuesto($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "SELECT v.*, CONCAT_WS(', ', codigo, cliente, ubicacion, descripcion) as desc_puesto	
			          FROM   vig_puestos v WHERE 1 ";
			if($request['query'] != ''){
				$query .= " AND (descripcion like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListCategoria($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomcategorias where 1";
			if($request['query'] != ''){
				$query .= " and (descrip like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListCargo($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomcargos where 1";
			if($request['query'] != ''){
				$query .= " and (des_car like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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

	public function getListNivel($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "SELECT codorg, concat_ws(' ', codorg, descrip, markar) as descrip, markar, ee, gerencia 
			from nomnivel".$request['nivel']." where 1";
			if($request['cascade'] != ''){
				$query .= " and gerencia = '".$this->bdControlador->real_escape_string($request['cascade'])."') ";
			}
			if($request['query'] != ''){
				$query .= " and (descrip like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
			}
			if($request['cascada_partida'] != ''){
				$query .= " and markar = '".$request['cascada_partida']."'";
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

	public function getListPosicion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = 'SELECT nomposicion_id, descripcion_posicion, sueldo_propuesto, categoria_id, cargo_id, partida FROM nomposicion';
			if($request['query'] != ''){
				$query  = "select * from nomposicion where 1";
				$query .= " and (descripcion_posicion like '%".$this->bdControlador->real_escape_string($request['query'])."%') ";
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
	public function getPosicion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
			$query = "select * from nomposicion where nomposicion_id = '".$request['id'] ."'";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<posicion>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</posicion></message>';

			$this->bdControlador->desconectar();;
			return  $xml;//Array('success' => true,'data' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getProfesion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select * from nomprofesiones where codorg = '".$request['id'] ."'";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<profesion>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</profesion></message>';

			$this->bdControlador->desconectar();;
			return  $xml;//Array('success' => true,'data' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}

	public function getFuncion($request){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($_SESSION['bd']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select * from nomfuncion where nomfuncion_id = '".$request['id'] ."'";
			
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);
			$item = $this->bdControlador->fetch($result);
//			$matches = Array();
			$xml = '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>';
			$xml .= '<message success="true">';
			$xml .= '<funcion>';
			
			

			$info_campo = $this->bdControlador->fetchFields($result);

		    foreach ($info_campo as $valor) {
		        //printf("Nombre:        %s\n", $valor->name);
		        $xml .= '<'.$valor->name.'>';
				$xml .= $item[$valor->name];
				$xml .= '</'.$valor->name.'>';
		    }
			$xml .= '</funcion></message>';

			$this->bdControlador->desconectar();;
			return  $xml;//Array('success' => true,'data' => $matches);
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	
}
?>