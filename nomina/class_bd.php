<?php

class bd {

    private $servidor = DB_HOST;
    private $usuario = DB_USUARIO;
    private $clave = DB_CLAVE;
    private $puerto = DB_PUERTO;
    private $base;
    private $conn;

    public function bd($var) {
        $this->base = $var;
    }

    public function create_database($bdnueva) 
    {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            return;
        }
        $sentencia = "create database " . $bdnueva;
        $resultado = $conexion->query($sentencia);
    }

    private function connect() 
    {
        mysqli_report(MYSQLI_REPORT_STRICT);
        
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->base,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Fallo de conexion: %s\n", mysqli_connect_error());
        }
        return $conexion;
    }

    public function query($sentencia) 
    {
        $conexion = $this->connect();
        $resultado = $conexion->query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function multi_query($sentencia) 
    {
        $conexion = $this->connect();
        $resultado = $conexion->multi_query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }
    
    public function fetch($resultado, $tipo_array=MYSQLI_ASSOC)
    {
        return $resultado->fetch_array($tipo_array);
    }

    public function num_rows($resultado)
    {
        return $resultado->num_rows;
    }

}
?>
