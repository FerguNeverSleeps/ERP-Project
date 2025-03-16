<?php

class Permisos extends ConexionAdmin {

    function __construct() {
        define('VERCOSTO', 1);
        define('BAJOCOSTO', 2);
        parent::__construct();
    }

    function getPermisosUsuarios($idusuario) {
        $this->id_usuario = $idusuario;
        
        $query = "SELECT * FROM  modulo_usuario  WHERE cod_usuario=".$idusuario ;
        $result = $this->ObtenerFilasBySqlSelect($query);
//        $_SESSION['id_empresa'] = $result[0]['codigo'];
    }
    
     function getPermisosModulo() {

        $query = "SELECT b.cod_sistema, COUNT( * ) AS registros
                        FROM  `modulo_usuario` a
                        INNER JOIN modulos b ON b.cod_modulo = a.cod_modulo
                        WHERE a.cod_usuario = $this->id_usuario
                        AND a.permitido =1
                        GROUP BY b.cod_sistema";
                
        $result = $this->ObtenerFilasBySqlSelect($query);
        foreach ($result[0] as $valor) {
            define('ADMINISTRATIVO',($valor['cod_sistema']==1 and $valor['registros'] > 0) ? 'true' : 'false' ); 
            define('CONTABILIDAD',($valor['cod_sistema']==2 and $valor['registros'] > 0) ? true : false );
            define('PLANILLA',($valor['cod_sistema']==3 and $valor['registros'] > 0) ? true : false );
        }
    }
    
/*    function getPermisosDetalleUsuario($idusuario, $idpermiso) {
        $this->id_usuario = $idusuario;
        
        $query = "SELECT * FROM  permiso_usuario_detalle  WHERE cod_usuario=".$idusuario ." and "
                . " cod_permiso_detalle = ". $idpermiso ;
        $result = $this->ObtenerFilasBySqlSelect($query);
        return $result[0]['permitido'];
        
    }
*/
    function getPermisoRol($idrol, $idpermiso) {
        $this->id_usuario = $idusuario;
        
        $query = "SELECT * FROM  rol_permisos  WHERE id_rol=".$idrol ." and "
                . " id_permiso = ". $idpermiso ;
        $result = $this->ObtenerFilasBySqlSelect($query);
        return $result[0]['id'];
        
    }
}

?>
