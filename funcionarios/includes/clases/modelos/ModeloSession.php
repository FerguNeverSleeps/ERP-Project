<?php

require_once '../BDControlador.php';
//if(!isset($_SESSION)){session_start();} 
class ModeloSession {
	
	private $bd = null;
	
	public function solicitaAutorizacion($request){
		$this->bd = new BDControlador();
		try{
			$this->bd->conectar();
			
			$login = $request["usuario"];
			$password = md5($request["password"]);
			
			$consulta = "select *
                         	from selectra_conf_pyme.usuarios u 
						where u.usuario='".$login."' and u.clave='".$password."' and nivel_id <= '".$request["nivel_operacion"]."'";
			

			$this->bd->setQuery($consulta);	
			$resultado = $this->bd->ejecutaInstruccion();
			$numero = $this->bd->numero_filas($resultado);
			$fila = $this->bd->fetch($resultado);

			if($numero == 0){
				return  Array('success' => true,'login' => false,'mensaje' => "Usuario No Autorizado");	
			}
			return  Array('success' => true,'login' => true,'mensaje' => "Usuario Autorizado ");
		} 
		catch(Exception $e ) {
			$this->bd->rollback();
			$this->bd->desconectar();
			$arrayError = Array('Error No:' => $e->getCode(),'Error Message:' => $e->getMessage(),'Stack Trace:' => nl2br($e->getTraceAsString()));
			//$arrayError = 'Error No: ' . $e->getCode().' Error Message: ' . $e->getMessage().' Stack Trace: ' . nl2br($e->getTraceAsString());
			return  Array('success' => false,'mensaje' => "Error durante la operacion",'error' => $arrayError);
		}
		
	}
}

?>
