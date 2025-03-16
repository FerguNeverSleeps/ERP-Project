<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'BDControlador.php');
require_once($DIR.'Auditoria.php');
class ModeloAuditoria{
	private $bdControlador = null;

 // FUNCTIONS
	public function save(Auditoria &$Auditoria,$request){
		$lastId = 0;
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$this->bdControlador->autocommit(FALSE);
			$saveQuery = "insert into auditoria (auditoria_id,mensaje,auditoria_mod_id,operacion,id_afectado,codigo_afectado,tabla_afectada,usuario) values (NULL,'{$Auditoria->getMensaje()}','{$Auditoria->getAuditoriaModId()}','{$Auditoria->getOperacion()}','{$Auditoria->getIdAfectado()}','{$Auditoria->getCodigoAfectado()}','{$Auditoria->getTablaAfectada()}','{$Auditoria->getUsuario()}')";
			$this->bdControlador->setQuery($saveQuery);
			$this->bdControlador->ejecutaInstruccion();
			$lastId =$this->bdControlador->lastId();
			
			$this->bdControlador->commit();
			return  Array('success' => true,'mensaje' =>'OK','auditoria_id' => $lastId);
		}
		catch(Exception $ex){
			$this->bdControlador->rollback();
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
	public function getOne(Auditoria &$Auditoria){
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->conectar();
			$query = '';
				$query = "select auditoria_id,mensaje,auditoria_mod_id,operacion,id_afectado,codigo_afectado,tabla_afectada,usuario,fecha_operacion from auditoria where auditoria_id = '{$Auditoria->primaryKeyFieldInDb}'";
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
			$query = "select auditoria_id,mensaje,auditoria_mod_id,operacion,id_afectado,codigo_afectado,tabla_afectada,usuario,date_format(fecha_operacion,'%d/%m/%Y %h:%i:%s $p') fecha_operacion from auditoria where 1 ";
			if($request['usuario'] != ''){
				$query .= " and usuario like '%".$request['usuario']."%'";
			}
			if($request['mensaje'] != ''){
				$query .= " and mensaje like '%".$request['usuario']."%'";
			}
			if($request['operacion'] != ''){
				$query .= " and operacion = '".$request['operacion']."'";
			}
			if($request['fecha_desde'] != '' && $request['creacio_hasta'] != ''){
				$query .= " and fecha_operacion between '".$request['fecha_desde']."' and '".$request['fecha_hasta']."'";
			}
			else if($request['fecha_desde'] != '' && $request['fecha_hasta'] == ''){
				$query .= " and fecha_operacion >= '".$request['fecha_desde']."'";
			}
			else if($request['fecha_desde'] == '' && $request['fecha_hasta'] != ''){
				$query .= " and fecha_operacion <= '".$request['fecha_hasta']."'";
			}
			$this->bdControlador->setQuery($query);
			$result = $this->bdControlador->ejecutaInstruccion();
			$num = $this->bdControlador->numero_filas($result);

			$this->bdControlador->setQuery($query." limit ".$request['iDisplayStart'].",".$request['iDisplayLength']);
			$result = $this->bdControlador->ejecutaInstruccion();

			$iTotalRecords = $num;
  			$iDisplayLength = intval($request['iDisplayLength']);
  			$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
  			$iDisplayStart = intval($request['iDisplayStart']);
  			$sEcho = intval($request['sEcho']);
  
  			$records = array();
  			$records["aaData"] = array(); 

  			$end = $iDisplayStart + $iDisplayLength;
		  	$end = $end > $iTotalRecords ? $iTotalRecords : $end;

			while ($item = $this->bdControlador->fetch($result)){
				$records["aaData"][] = array(
					$item['mensaje'],
		      		$item['fecha_operacion'],
		      		$item['usuario'],
		      		''
		      		//,'<a href="#ver-escala" data-toggle="modal" class="btn btn-xs default" onclick="javascript:ListadoEscala.cargarVerEscala('.$item['escala_id'].')"><i class="fa fa-search"></i> Ver</a>'
	   			);
			}

  			if (isset($request["sAction"]) && $request["sAction"] == "group_action") {
    			$records["sStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    			$records["sMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
  			}

			$records["sEcho"] = $sEcho;
  			$records["iTotalRecords"] = $iTotalRecords;
  			$records["iTotalDisplayRecords"] = $iTotalRecords;

			$this->bdControlador->desconectar();;
			return  $records;
		}
		catch(Exception $ex){
			$this->bdControlador->desconectar();
			return  Array('success' => false,'mensaje' =>'NO_OK','error'=>$ex->getMessage());
		}
	}
}
?>