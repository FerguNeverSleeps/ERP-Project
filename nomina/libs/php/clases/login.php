<?php

class Login extends ConexionAdmin {

    function __construct() {
        parent::__construct();
    }

    function validarAcceso($usuario, $contrasena, $id_empresa) {

        $this->usuario = $usuario;
        $this->contrasena = hash("sha256",$contrasena);
        //$this->instruccion = "SELECT * FROM usuarios  where cod_empresa = '" . $id_empresa . "' and usuario = '" . $this->usuario . "'  and clave   = '" . $this->contrasena . "'";
        $this->instruccion = "SELECT * FROM ".SELECTRA_CONF_PYME.".nomusuarios  where  login_usuario = '" . $this->usuario . "'  and clave   = '" . $this->contrasena . "'";
        //echo $this->instruccion;exit;".$id_empresa.".
        $this->rs = $this->ObtenerFilasBySqlSelect($this->instruccion);

        if (!$this->rs[0]) {
            //$this->logout();
            //return false;
            return array("error" =>false, "mensaje" => "Error al autenticar el usuario" );
        } else {
            if($this->rs[0]['estado'] == 1)
            {            
                $this->rCampos = $this->rs;
                $this->runSession();
                $_SESSION['bd'] = $id_empresa;
                //return true;
                return array("error" =>true, "mensaje" => "Usuario autenticado" );
            }
            else
            {
                return array("error" =>false, "mensaje" => "Usuario Inactivo." );
            }
        }
        $this->db->Close();
        $rs->Close();
    }

    private function runSession() {
        $_SESSION["idSession"] = session_id();
        $_SESSION['islogin'] = 1;
        foreach ($this->rCampos as $clave => $valor) {
            $_SESSION['cod_usuario'] = $valor['coduser'];
            $_SESSION['usuario'] = $valor['login_usuario'];
            $_SESSION['clave'] = $valor['clave'];
            $_SESSION['nombreyapellido'] = $valor['descrip'];
            $_SESSION['nombre'] = $valor['nombreyapellido'];
            $_SESSION['ultimo_login'] = $valor['ultima_sesion'];       
            $_SESSION['perfil'] = $valor['perfil']; 
            $_SESSION['foto'] = $valor['img']; 
            $_SESSION['nivel_usuario'] = $valor['nivel_id']; 
            $_SESSION['rol'] = $valor['id_rol']; 
        }
        /*
        $this->instruccion = "update usuarios set ultima_sesion = CURRENT_TIMESTAMP where cod_usuario = " . $_SESSION['cod_usuario'];
        $this->Execute2($this->instruccion);*/
    }

    function validarLoginON() {
        if ($_SESSION['islogin'] == 1) {
            return true;
        } else {
            return false;
        }
    }

    function getIdUsuario() {
        return $_SESSION['cod_usuario'];
    }

    function getUsuario() {
        return $_SESSION['usuario'];
    }

    function getNombreApellidoUSuario() {
        return $_SESSION['nombreyapellido'];
    }

    function getClaveUsuario() {
        return $_SESSION['clave'];
    }

    function getUltimoLogin() {
        return $_SESSION['ultimo_login'];
    }

    function getIdSessionActual() {
        return $_SESSION["idSession"];
    }

    function getRol() {
        return $_SESSION['rol'];
    }

    function logout() {
        session_unset();
        session_destroy();
    }
    
    function getIdEmpresa($empresa) {
        
        $query = "SELECT * FROM nomempresa  WHERE bd='$empresa' LIMIT 1;";
        $result = $this->ObtenerFilasBySqlSelect($query);
  
        $_SESSION['id_empresa'] = $result[0]['codigo'];        
   
        return $result[0]['codigo'];
    }
}

?>
