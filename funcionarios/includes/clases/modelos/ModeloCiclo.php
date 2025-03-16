<?php
if(!isset($DIR)){
	$DIR = "../";
}
require_once($DIR.'BDControlador.php');
class ModeloCiclo{
	private $bdControlador = null;

 	public function saveCiclo($request)
 	{
		
	}
	public function listCiclo($request)
 	{
		$this->bdControlador = new BDControlador();
		try{
			$this->bdControlador->setBd($request['db_connect']);
			$this->bdControlador->conectar();
			$query = '';
				$query = "select codnom,codtip,descrip,periodo_ini,periodo_fin,fechapago,status from nom_nominas_pago where 1";
			if($request['patronbuscar'] != ''){
				$query .= "  ";
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