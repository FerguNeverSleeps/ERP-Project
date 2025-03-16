<?php
require_once '../../generalp.config.inc.php';

class Database
{
	private $db_host = DB_HOST;	
	private $db_user = DB_USUARIO;
	private $db_pass = DB_CLAVE;
	// private $db_name;
	private $_mysqli;
	
	public function __construct($db_name)
	{
		// $this->db_name = $db_name;
		$this->_mysqli = @new mysqli($this->db_host, $this->db_user, $this->db_pass, $db_name);

		if($this->_mysqli->connect_errno) {
		    exit("Falló la conexión a MySQL: (" . $this->_mysqli->connect_errno . ") " . $this->_mysqli->connect_error);
		}
		$this->_mysqli->set_charset('utf8');
	}

	public function __destruct()
	{
		if(!$this->_mysqli->connect_errno)
			@$this->_mysqli->close();
	}

	public function errno()
	{
		return $this->_mysqli->errno;
	}

	public function error()
	{
		return $this->_mysqli->error;
	}

	public function escape_string($string)
	{
		return $this->_mysqli->real_escape_string($string);
	}

	// public function get_connect()
	// {
	// 	return $this->_mysqli;
	// }

	public function insert_id()
	{
		return $this->_mysqli->insert_id;
	}

	public function query($query)
	{
		$result = $this->_mysqli->query($query);

		if(!$result)
			echo "Falló la consulta: (" . $this->_mysqli->errno . ") " . $this->_mysqli->error;

		return $result;		
	}

	public function set_charset($charset)
	{
		$this->_mysqli->set_charset($charset);
	}
}
?>