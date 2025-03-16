<?php

require_once '../BDControlador.php';

class ModeloEmpresa {
	
	private $bd = null;


        public function getEmpresas($request){
		$num=0;
		$this->bd = new BDControlador();
		try{
			$this->bd->conectar();

            $sql = "select e.* from ".SELECTRA_CONF_PYME.".nomempresa e 
            inner join ".SELECTRA_CONF_PYME.".nomusuario_empresa ne on ne.id_empresa = e.codigo and ne.acceso=1 
            inner join ".SELECTRA_CONF_PYME.".nomusuarios u on u.coduser = ne.id_usuario 
             where e.bd_nomina != '".$request['db_connect'] ."' and u.login_usuario='".$request['user'] ."' ";
            $this->bd->setQuery($sql);	
			$result = $this->bd->ejecutaInstruccion();
			$num = $this->bd->numero_filas($result);
            $matches = Array();			
			
			
			while ( $item = $this->bd->fetch($result)) {
			    $matches[] = $item;
			}
			
			return Array('success' => true,'matches' => $matches,'totalCount' => $num);
		} 
		catch(Exception $e ) {
			$this->bd->desconectar();
			$arrayError = Array('Error No:' => $e->getCode(),'Error Message:' => $e->getMessage(),'Stack Trace:' => nl2br($e->getTraceAsString()));
			return  Array('success' => false,"error"=> "Ha ocurrido un error: ".$e->getMessage(),'mensaje' => "Error durante la operacion",'error' => $arrayError,'file_name' => 'casos_lista_sin_credito');
		}		
	}	
}

?>