<?php
//TIENEN DOS BLOQUES DE PHP EN EL MISMO PHP
/* comprobamos que el usuario nos viene como un parametro */
require("../generalp.config.inc.php");

$constantes = get_defined_constants(true);

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

    public function create_database($bdnueva) {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            return;
        }
        $sentencia = "create database " . $bdnueva;
        $resultado = $conexion->query($sentencia);
    }

    private function connect() {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->base,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Fallo de conexion: %s\n", mysqli_connect_error());
        }
        return $conexion;
    }

    public function query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function multi_query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->multi_query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }
    
    public function fetch($resultado){
        return $resultado->fetch_array();
    }

}
if(isset($_GET['user'])) {

        /* utilizar la variable que nos viene o establecerla nosotros */
        $number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 es por defecto
        $format = strtolower($_GET['format']) == 'xml' ? 'xml' : 'json'; //xml es por defecto
        $user_id = ($_GET['user']); 
        $pass = ($_GET['pass']); 
        $ficha = ($_GET['ficha']); 

        /* conectamos a la bd */
        $link = new bd($constantes['user']['SELECTRA_CONF_PYME']);
        $clave =  hash("sha256",$pass);
        $query = "SELECT * from ".$constantes['user']['SELECTRA_CONF_PYME'].".nomusuarios where login_usuario = '{$user_id}' AND clave = '{$clave}'";
        $res=$link->query($query);
        if($res->num_rows)
        {        

            $link2 = new bd($constantes['user']['DB_SELECTRA_NOM']);

            $query = "SELECT * from reloj_detalle where ficha = '{$ficha}' LIMIT 5";
            $result=$link2->query($query);
            $marcaciones = array();
            /* creamos el array con los datos */
            if($result->num_rows>0) {
                    while($marcacion = $result->fetch_assoc()) {
                            $marcaciones[] = $marcacion;
                    }
            }
            /* formateamos el resultado */
            if($format == 'xml') {
                header('Content-type: text/xml');
                echo '';
                foreach($marcaciones as $index => $marcacion) {
                        if(is_array($marcacion)) {
                                foreach($marcacion as $key => $value) {
                                        echo '<',$key,'>';
                                        if(is_array($value)) {
                                                foreach($value as $tag => $val) {
                                                        echo '<',$tag,'>',htmlentities($val),'';
                                                }
                                        }
                                        echo '';
                                }
                        }
                }
                echo '';
            }
            else {
                header('Content-type: application/json');
                //$marcaciones = trim($marcaciones, '[]');
                    $i=0; 
                    do
                    {
                        $coma = (($i+1)<count($marcaciones)) ? ",":"";
                        echo json_encode($marcaciones[$i], JSON_FORCE_OBJECT ).$coma;
                        $i++;
                    }
                    while($i < count($marcaciones));

            }
    
            /* nos desconectamos de la bd */
            @mysql_close($link2);

        }
?>

<?php
//ACA INICIA EL SEGUNDO BLOQUE
/* comprobamos que el usuario nos viene como un parametro */
require("../generalp.config.inc.php");

$constantes = get_defined_constants(true);

class bd1 {

    private $servidor = DB_HOST;
    private $usuario = DB_USUARIO;
    private $clave = DB_CLAVE;
    private $puerto = DB_PUERTO;
    private $base;
    private $conn;

    public function bd($var) {
        $this->base = $var;
    }

    public function create_database($bdnueva) {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            return;
        }
        $sentencia = "create database " . $bdnueva;
        $resultado = $conexion->query($sentencia);
    }

    private function connect() {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->base,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Fallo de conexion: %s\n", mysqli_connect_error());
        }
        return $conexion;
    }

    public function query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function multi_query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->multi_query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }
    
    public function fetch($resultado){
        return $resultado->fetch_array();
    }


}
if(isset($_GET['user'])) {

        /* utilizar la variable que nos viene o establecerla nosotros */
        $number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 es por defecto
        $format = strtolower($_GET['format']) == 'xml' ? 'xml' : 'json'; //xml es por defecto
        $user_id = ($_GET['user']); 
        $pass = ($_GET['pass']); 
        $ficha = ($_GET['ficha']); 

        /* conectamos a la bd */
        $link = new bd($constantes['user']['SELECTRA_CONF_PYME']);
        $clave =  hash("sha256",$pass);
        $query = "SELECT * from ".$constantes['user']['SELECTRA_CONF_PYME'].".nomusuarios where login_usuario = '{$user_id}' AND clave = '{$clave}'";
        $res=$link->query($query);
        if($res->num_rows)
        {        

            $link2 = new bd($constantes['user']['DB_SELECTRA_NOM']);

            $query = "SELECT * from reloj_detalle where ficha = '{$ficha}' LIMIT 5";
            $result=$link2->query($query);

            /* creamos el array con los datos */
            $marcacion = array();
            if($result->num_rows>0) {
                    while($marcacion = $result->fetch_assoc()) {
                            $marcaciones[] = array('marcacion'=>$marcacion);
                    }
            }
            /* formateamos el resultado */
            if($format == 'xml') {
                header('Content-type: text/xml');
                echo '';
                foreach($marcaciones as $index => $marcacion) {
                        if(is_array($marcacion)) {
                                foreach($marcacion as $key => $value) {
                                        echo '<',$key,'>';
                                        if(is_array($value)) {
                                                foreach($value as $tag => $val) {
                                                        echo '<',$tag,'>',htmlentities($val),'';
                                                }
                                        }
                                        echo '';
                                }
                        }
                }
                echo '';
            }
            else {
                header('Content-type: application/json');
                echo json_encode(array('marcaciones'=>$marcaciones));
            }
    
            /* nos desconectamos de la bd */
            @mysql_close($link2);

        }

}


