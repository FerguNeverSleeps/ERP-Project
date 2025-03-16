<?php
session_start();
//session_destroy();
require_once("func_bd.php") ;
$config = parse_ini_file("../lib/selectra.ini");

{
    if ($_SESSION['bd'] == "" || !isset($_GET["seleccion_nomina"])) {
        #echo 'salio';exit;
        $_SESSION['bd'] = $config['bdnombre'];
        $_SESSION['termino'] = $config['termino'];
    }
    require_once "../lib/common.php";
 
    
    $seleccion=$_GET["seleccion_nomina"];
    //echo $_GET["seleccion_nomina"];exit;
    if ($seleccion==1){
            /*foreach($_GET['opt'] as $key => $value){
                    $valuetxt=$_GET['opt'];
            }*/
            ///echo $_SESSION['bd'];exit;
            $strsql= "select codtip,descrip from nomtipos_nomina where codtip = '".$_GET['opt']."'";
            $result = sql_ejecutar($strsql);
            $fila = mysqli_fetch_array($result);
            $_SESSION['codigo_nomina'] = $fila[0];
            $_SESSION['nomina'] = $fila[1];//$valuetxt;
            //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
            activar_pagina("frame.php");
    }
    //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
        $bValidPwd = false;
        $sUsername = $_SESSION['usuario'];
        $sPassword = $_SESSION['clave'];
        //echo $_SESSION['bd'];
        $sSql = new bd($_SESSION['bd']);
        $result = $sSql->query("select bd_nomina,bd_contabilidad,nombre nom_emp from nomempresa where bd_nomina = '".$_SESSION['bd_nomina']."'");
        $filaemp = $result->fetch_assoc();
        $_SESSION["bd"] = $filaemp['bd_nomina'];
        $_SESSION["bdc"] = $filaemp['bd_contabilidad'];
        $_SESSION["nombre_empresa_nomina"] = $filaemp['nom_emp'];

        $sSql = new bd($_SESSION['bd']);
        //echo $_SESSION['bd'];
        //echo "select * from nomusuarios where login_usuario='$sUsername' and clave='".hash("sha256",$sPassword)."'";
        
        $result_usuarios = $sSql->query("select * from ".SELECTRA_CONF_PYME.".nomusuarios where login_usuario='$sUsername'");// and clave='" . ($sPassword) . "'
        //echo "select * from nomusuarios where login_usuario='$sUsername'";
        //@TODO colocar clave en el query.
        //echo $result->num_rows;
        //exit;
        if ($result_usuarios->num_rows > 0) {
            //cerrar_conexion($Conn);
            $fila_usuarios = $result_usuarios->fetch_assoc();
            $_SESSION['ewSessionStatus'] = "login";            
            $_SESSION['ewSessionUserName'] = $sUsername;
            $_SESSION['nombre'] = $fila_usuarios["descrip"];
            $_SESSION['coduser'] = $fila_usuarios["coduser"];
            $_SESSION['id_usuario'] = $fila_usuarios["coduser"];
            $_SESSION['ewSessionSysAdmin'] = 0; // Non system admin
            //Estos Permisos los quitare de aca
            $_SESSION['acce_configuracion'] = $fila_usuarios['acce_configuracion'];
            $_SESSION['acce_usuarios'] = $fila_usuarios['acce_usuarios'];
            $_SESSION['acce_elegibles'] = $fila_usuarios['acce_elegibles'];
            $_SESSION['acce_personal'] = $fila_usuarios['acce_personal'];
            $_SESSION['acce_prestamos'] = $fila_usuarios['acce_prestamos'];
            $_SESSION['acce_consultas'] = $fila_usuarios['acce_consultas'];
            $_SESSION['acce_transacciones'] = $fila_usuarios['acce_transacciones'];
            $_SESSION['acce_procesos'] = $fila_usuarios['acce_procesos'];
            $_SESSION['acce_reportes'] = $fila_usuarios['acce_reportes'];
            $_SESSION['acce_enviar_nom'] = $fila_usuarios['acce_enviar_nom'];
            $_SESSION['acce_autorizar_nom'] = $fila_usuarios['acce_autorizar_nom'];
            $_SESSION['acce_estuaca'] = $fila_usuarios['acce_estuaca'];
            $_SESSION['acce_xestuaca'] = $fila_usuarios['acce_xestuaca'];
            $_SESSION['acce_permisos'] = $fila_usuarios['acce_permisos'];
            $_SESSION['acce_logros'] = $fila_usuarios['acce_logros'];
            $_SESSION['acce_penalizacion'] = $fila_usuarios['acce_penalizacion'];
            $_SESSION['acce_movpe'] = $fila_usuarios['acce_movpe'];
            $_SESSION['acce_evalde'] = $fila_usuarios['acce_evalde'];
            $_SESSION['acce_experiencia'] = $fila_usuarios['acce_experiencia'];
            $_SESSION['acce_antic'] = $fila_usuarios['acce_antic'];
            $_SESSION['acce_uniforme'] = $fila_usuarios['acce_uniforme'];
            $_SESSION['acce_generarordennomina'] = $fila_usuarios['acce_generarordennomina'];
            $_SESSION['acceso_sueldo'] = $fila_usuarios['acceso_sueldo'];
            $_SESSION['acceso_contraloria'] = $fila_usuarios['acceso_contraloria'];
            $_SESSION['acceso_s_efecto'] = $fila_usuarios['acceso_s_efecto'];
            $_SESSION['acceso_editar'] = $fila_usuarios['acceso_editar'];
            $_SESSION['acceso_calendarios'] = $fila_usuarios['acceso_calendarios'];
            $_SESSION['acceso_imprimir'] = $fila_usuarios['acceso_imprimir'];
            $_SESSION['acceso_c_familiares'] = $fila_usuarios['acceso_c_familiares'];
            $_SESSION['acceso_expedientes'] = $fila_usuarios['acceso_expedientes'];
            //===========================================
            $_SESSION['region'] = $fila_usuarios['region'];
            $_SESSION['departamento'] = $fila_usuarios['departamento'];
            $_SESSION['acceso_dir'] = $fila_usuarios['acceso_dir'];
            $_SESSION['acceso_dep'] = $fila_usuarios['acceso_dep'];
            $_SESSION['nivel3'] = $fila_usuarios['nivel3'];
            $_SESSION['nivel4'] = $fila_usuarios['nivel4'];
            $_SESSION['nivel5'] = $fila_usuarios['nivel5'];
            
            $_SESSION["ewSessionStatus"] = "login";
            $_SESSION['termino'] = $config['termino'];
        } else {
            $_SESSION["ewSessionMessage"] = "no";
        }
        $strsql_permisos= "select a.*,b.codigo 
            from ".SELECTRA_CONF_PYME.".usuario_permisos as a,".SELECTRA_CONF_PYME.".permisos as b where a.id_usuario=".$fila_usuarios["coduser"]." and a.id_permiso=b.id";
        $result_permisos = sql_ejecutar($strsql_permisos);
        while ($fila_permisos = mysqli_fetch_array($result_permisos)) {
            $_SESSION[$fila_permisos["codigo"]] = 1;
        }

}
   $strsql= "select n.* from nomtipos_nomina n inner join ".SELECTRA_CONF_PYME.".nomusuario_nomina nu on nu.id_nomina = n.codtip and nu.id_usuario = '".$_SESSION['cod_usuario'] ."' and nu.acceso=1
   order by n.codtip";
    
    $result =sql_ejecutar($strsql);  
    $fila = mysqli_fetch_array($result);
if (REG_ACCESS) {

    //print_r($fila);   
    echo '<script>document.location.href = "seleccionar_nomina.php?seleccion_nomina=1&opt="+'.$fila[codtip].';</script>';
} 
else
{
    echo '<script>document.location.href = "../modulos/principal/aviso.php?seleccion_nomina=1&opt='.$fila[codtip].'"</script>';

}

           
