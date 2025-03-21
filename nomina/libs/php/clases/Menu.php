<?php

class Menu extends ConexionAdmin{

    var $sql;
    
    function __construct() {
        parent::__construct();
    }

    public function getArchivosPHPTPL($opt_menu, $opt_seccion) {
       $this->sql = "
            SELECT
                opt_menu.cod_modulo AS id_optmenu,
                opt_seccion.cod_modulo AS id_optseccion,
                opt_menu.nom_menu AS descripcion_optmenu,
                opt_seccion.nom_menu AS descripcion_optseccion,
                opt_seccion.archivo_php,
                opt_seccion.archivo_tpl,
                opt_seccion.orden orden2,
                opt_seccion.img_ruta
            FROM
                modulos opt_menu 
                INNER JOIN modulos opt_seccion ON opt_menu.cod_modulo = opt_seccion.cod_modulo_padre
            WHERE
                opt_menu.cod_modulo = " . $opt_menu . " AND opt_seccion.cod_modulo  = " . $opt_seccion;
 
        $this->rCampos = $this->ObtenerFilasBySqlSelect($this->sql);

        if ($this->rCampos != -1) {
            return $this->rCampos;
        } else {
            return -1;
        }
    }

//public function getArchivosPHPTPL($opt_menu, $opt_seccion){

    public function getCabeceraSeccionesByOptMenu($opt_menu) {
        $this->sql = "SELECT *
                FROM `modulos`
               WHERE `cod_modulo` = " . $opt_menu . " and visible = 1 order by orden";

        return $this->ObtenerFilasBySqlSelect($this->sql);
       
    }

    public function getSeccionesByOptMenu($opt_menu) {
  
        $this->sql = "SELECT *
                FROM `modulos` a
                INNER JOIN `modulo_usuario` c ON c.cod_modulo = a.cod_modulo                
                WHERE `cod_modulo_padre` = " . $opt_menu . "  and visible = 1 and permitido = 1 "
             . " and cod_usuario = " . $_SESSION['cod_usuario'] . " "
             . " ORDER BY orden";

        return $this->ObtenerFilasBySqlSelect($this->sql);
    }

    public function getMenu($codUsuario) {
        $this->sql = "
            SELECT
                m.cod_modulo,
                m.cod_modulo_padre,
                m.orden,
                m.nom_menu,m.archivo_php, m.archivo_tpl, m.img_ruta,
                permitido
                FROM modulos m 
                INNER JOIN modulo_usuario mu ON mu.cod_modulo = m.cod_modulo
                WHERE mu.cod_usuario = " . $codUsuario . " and m.visible = 1  and permitido = 1  
                    and (cod_modulo_padre = 0 or cod_modulo_padre is null)
                ORDER BY m.orden     ";
    
        return $this->ObtenerFilasBySqlSelect($this->sql);
    }
}

?>