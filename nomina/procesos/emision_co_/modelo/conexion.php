<?php

class Conexion {

	static public function conectar( $bd = null){
		if($bd == null)
		{
			if(!isset($_SESSION['bd'])){
				session_start();
				$bd = $_SESSION['bd'];
				
			}else{
				$bd = $_SESSION['bd'];

			}

		}
		$link = new PDO("mysql:host=localhost;dbname=$bd",
		//$link = new PDO("mysql:host=localhost;dbname=repo",
		//						"root","");
							"root","4m4x0n143rp");
		$link ->exec("set names utf8");


		$link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$link->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		
		return $link;
	}			

}