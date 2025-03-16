<?php
$rutaFile = dirname(dirname(dirname(__FILE__)));
$rutaFile = str_replace('\\', '/' , $rutaFile);
require_once($rutaFile . '/generalp.config.inc.php');
class Conexion {

    private $servidor = DB_HOST;
    private $usuario = DB_USUARIO;
    private $clave = DB_CLAVE;
    private $base;

    public function Conexion($var) {
        $this->base = $var;
    }
 
	public function conectar(){
		$link = new PDO("mysql:host={$this->sevidor};dbname={$this->base}",
						$this->usuario,
						$this->clave);

		$link->exec("set names utf8");

		return $link;

	}

}
		
