<?php

namespace Selectra_planilla\Http\Controllers;

use Illuminate\Http\Request;

use Selectra_planilla\Http\Requests;
use Selectra_planilla\Http\Controllers\Controller;
use Selectra_planilla\Empresa;
use Selectra_planilla\Datosempresa;
use Selectra_planilla\Pais;
use Input,Session,Redirect;

class ConfigController extends Controller
{
    public function index()
    {
        return view('config.index');
    }
    public function fin()
    {
        header('location:localhost/Selectra_planilla');
    }
    public function store(Request $request)
    {
        if ($request->etapa == '1') 
        {
            $host = [
                'host' => $request->host,
                'usuario' => $request->usuario,
                'password' => $request->s_password,
                'database1' => $request->database1,
                'database2' => 'prueba_planillaexpress_conf',
                    ];
            $res1=$this->crearDatabase($host,false);
            $res2=$this->crearDatabase($host,true);
            $res3=$this->cargaDatos($host,1);
            $res4=$this->cargaDatos($host,2);
            return view('config.usuario',compact('host'));
        }elseif ($request->etapa == '2') 
        {
            $host = [
                'host' => $request->host,
                'usuario' => $request->usuario,
                'password' => $request->s_password,
                'database1' => $request->database1,
                'database2' => $request->database2,
                    ];
            $usuario = [
                'usuario_admin' => $request->usuario_admin,
                'correo' => $request->correo,
                'a_password' => $request->a_password,
                    ];
            if ($this->validar($host,$usuario,1)){
                $res=$this->cargar_usuario($host,$usuario);
            }
            $paises = $this->carga_paises($host);
            return view('config.empresa',compact('host','paises'));
        }
        elseif ($request->etapa == '3') 
        {
            //obtenemos el campo file definido en el formulario
            $file = $request->file('archivo');
     
            //obtenemos el nombre del archivo
            $nombre = $file->getClientOriginalName();
     
            //indicamos que queremos guardar un nuevo archivo en el disco local
            \Storage::disk('public')->put($nombre,  \File::get($file));
            $empresa = [
                'nombre' => $request->nombre,
                'pais_id' => $request->pais_id,
                'img_izq' => $nombre,
                'nombre_sistema' => $request->nombre_sistema,
                'bd_nomina' => $request->database2,
                    ];
            
            $host = [
                'host' => $request->host,
                'usuario' => $request->usuario,
                'password' => $request->s_password,
                'database1' => $request->database1,
                'database2' => $request->database2,
                    ];
            if ($this->validar($host,$empresa,2)){
                $res2=$this->cargar_emp($host,$empresa);
            }
            $modulos = $this->carga_modulos($host);
            return view('config.modulos',compact('host','modulos'));
        }
        elseif ($request->etapa == '4') 
        {
            $host = [
                'host' => $request->host,
                'usuario' => $request->usuario,
                'password' => $request->s_password,
                'database1' => $request->database1,
                'database2' => $request->database2,
                    ];
            $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database1']);
            $sql1="SELECT cod_modulo, nom_menu FROM nom_modulos WHERE cod_modulo_padre IS NULL";
            $res = mysqli_query($conexion,$sql1) or die(mysqli_error());
            mysqli_close($conexion);
            $conexion= mysqli_connect('localhost','root','','prueba_planillaexpress_conf');
            while ($modulos = mysqli_fetch_array($res))
            {
                if(isset($_POST[$modulos['cod_modulo']]) && $_POST[$modulos['cod_modulo']] != "")
                {
                    $sql2="INSERT INTO nom_modulos_usuario (coduser, cod_modulo) VALUES ('2','".$modulos['cod_modulo']."')";
                    mysqli_query($conexion,$sql2) or die(mysqli_error());
                }
            }
        }
        $url= $this->dameURL();
        $arc= $this->crea_archivo_c($host);
        return view('config.fin',compact('url'));
    }
    public function crearDatabase($parametros,$opc = false)
    {
    	$conexion= mysqli_connect($parametros['host'],$parametros['usuario'],$parametros['password']);
        if ($opc == false) {
            $sql="CREATE DATABASE IF NOT EXISTS ".$parametros['database2'];
        }else{
            $sql="CREATE DATABASE IF NOT EXISTS ".$parametros['database1'];
        }

        if (mysqli_query($conexion, $sql))
        {
            mysqli_close($conexion);
            return true;
        }else{
            mysqli_close($conexion);
            return false;
        }
    }
    public function cargaDatos($parametros,$db)
    {
    	if ($db == 2)
        {
            $archivo = 'estructura_planillaexpress_conf.sql';
            $database = $parametros['database2'];
        }else{
            $archivo = 'estructura_rrhh_ginteven.sql';
            $database = $parametros['database1'];
        }
        $conexion= mysqli_connect($parametros['host'],$parametros['usuario'],$parametros['password'],$database);
		if (!$conexion)
		{ 
			return redirect('/');
		} 
		else
		{
			$sqlSource = file_get_contents("../db/".$archivo);
			mysqli_multi_query($conexion,$sqlSource);
            mysqli_close($conexion);
            return true;
		}
    }
    public function cargar_emp($host,$empresa)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database2']);
        $sql1 = "INSERT INTO nomempresa (nombre,bd_nomina) VALUES ('".$empresa['nombre']."','".$empresa['bd_nomina']."')";
        $sql2 = "SELECT codigo FROM nomempresa WHERE NOMBRE = '".$empresa['nombre']."'";
    
        mysqli_query($conexion,$sql1) or die(mysqli_error());
        $res=mysqli_query($conexion,$sql2) or die(mysqli_error());
        $codigo = mysqli_fetch_array($res);
        $sql3 = "INSERT INTO datos_empresa (cod_empresa, nombre_empresa, img_izq, nombre_sistema, pais_id) VALUES ('".$codigo['codigo']."','".$empresa['nombre']."','".$empresa['img_izq']."','".$empresa['nombre_sistema']."','".$empresa['pais_id']."')";

        mysqli_query($conexion,$sql3) or die(mysqli_error());
        mysqli_close($conexion);
        return true;
    }
    public function cargar_usuario($host,$usuario)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],'prueba_planillaexpress_conf');
        $sql1 = "INSERT INTO `nomusuarios` (`coduser`, `descrip`, `nivel`, `fecha`, `clave`, `correo`, `acce_usuarios`, `acce_configuracion`, `acce_elegibles`, `acce_personal`, `acce_prestamos`, `acce_consultas`, `acce_transacciones`, `acce_procesos`, `acce_reportes`, `acce_estuaca`, `acce_xestuaca`, `acce_permisos`, `acce_logros`, `acce_penalizacion`, `acce_movpe`, `acce_evalde`, `acce_experiencia`, `acce_antic`, `acce_uniforme`, `contadorvence`, `fecclave`, `encript`, `pregunta`, `respuesta`, `acctwind`, `borraper`, `dfecha`, `dfecclave`, `login_usuario`, `acce_autorizar_nom`, `acce_enviar_nom`, `acce_generarordennomina`, `acce_validar_constancias`, `acce_editar_constancias`, `img`, `acceso_sueldo`) VALUES
            ('2', 'Administrador', NULL, NULL, '".$usuario['a_password']."', '".$usuario['correo']."', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1,NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '".$usuario['usuario_admin']."', 1, 1, 1, 1, 1, '', 1)";
        mysqli_query($conexion,$sql1) or die(mysqli_error());
        mysqli_close($conexion);
        return true;
    }
    public function actualiza_config($host,$etapa,$archivo)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database2']);
        $sql1 = "INSERT INTO config_install (fecha, etapa,arch_cliente) VALUES (NOW(), '".$etapa."', '".$archivo."')";
        mysqli_query($conexion,$sql1) or die(mysqli_error());
        mysqli_close($conexion);
        return true;
    }
    public function carga_paises($host)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database2']);
        $sql="SELECT * FROM `paises`";
        $resultado=mysqli_query($conexion,$sql);
        mysqli_close($conexion);
        return $resultado;
    }
    public function carga_modulos($host)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database1']);
        $sql="SELECT cod_modulo, nom_menu FROM `nom_modulos` WHERE cod_modulo_padre IS NULL AND activo = 1 ORDER BY orden";
        $resultado=mysqli_query($conexion,$sql);
        mysqli_close($conexion);
        return $resultado;
    }
    public function dameURL()
    {
        $url_base=$_SERVER['REQUEST_URI'];
        $partes=explode("/",$url_base);
        $url='';
        $i=0;
        $x=0;
        while ($x==0)
        {
            if ($partes[$i] != 'configuracion') {
                $url=$url.$partes[$i]."/";
            }elseif ($partes[$i] == 'configuracion') {
                $x=1;
            }
            $i=$i+1;
        }
        return $url;
    }
    public function crea_archivo_c($host)
    {
        $file = fopen("../../archivo_config_prueba1.php", "w");
        fwrite($file,"<?php". PHP_EOL);
        fwrite($file,"error_reporting(E_ALL^E_NOTICE);". PHP_EOL);
        fwrite($file,"define('DB_USUARIO','root', true);". PHP_EOL);
        fwrite($file,"define('DB_CLAVE', '', true);". PHP_EOL);
        fwrite($file,"define('DB_HOST', 'localhost', true);". PHP_EOL);
        fwrite($file,"define('DB_PUERTO', '3306', true);". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_CONF', 'selectra_planilla_conf', true);#sisalud_selectraconf". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_CONT', '', true);". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_NOM', 'express_planilla', true);". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_FAC', '', true);". PHP_EOL);
        fwrite($file,"define('SUGARCRM', 'sugarcrm',true);". PHP_EOL);
        fwrite($file,"define('TOMCAT', 'http://localhost:8080/JavaBridge/java/Java.inc', true);". PHP_EOL);
        fwrite($file,"//CONSTANTES UTILIZADAS POR LA INTERFAZ DE REGISTRO DE EVENTOS (LOG)". PHP_EOL);
        fwrite($file,"define('REG_INFO',0, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGIN_OK',1, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGIN_FAIL',2, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGOUT',3, true);". PHP_EOL);
        fwrite($file,"define('REG_SESSION_INVALIDATE',4, true);". PHP_EOL);
        fwrite($file,"define('REG_SESSION_READ_ERROR',5, true);". PHP_EOL);
        fwrite($file,"define('REG_SQL_OK',6, true);". PHP_EOL);
        fwrite($file,"define('REG_SQL_FAIL',7, true);". PHP_EOL);
        fwrite($file,"define('REG_ILLEGAL_ACCESS',8, true);". PHP_EOL);
        fwrite($file,"define('REG_ALL',9, true);". PHP_EOL);
        fwrite($file,"$"."config['bd']='mysql';". PHP_EOL);
        fwrite($file,"require_once('funciones.inc.php');". PHP_EOL);
        fwrite($file,"?>". PHP_EOL);
        fclose($file);
        //-------------------------------------------------------------------------------------------------------------
        $file = fopen("../../archivo_config_prueba2.php", "w");
        fwrite($file,"<?php". PHP_EOL);
        fwrite($file,"if (!isset("."$"."_SESSION)) {". PHP_EOL);
        fwrite($file,"session_start();". PHP_EOL);
        fwrite($file,"ob_start();". PHP_EOL);
        fwrite($file,"}". PHP_EOL);
        fwrite($file,"error_reporting(E_ALL^E_NOTICE);". PHP_EOL);
        fwrite($file,"define('DB_USUARIO','root', true);". PHP_EOL);
        fwrite($file,"define('DB_CLAVE', '', true);". PHP_EOL);
        fwrite($file,"define('DB_HOST', 'localhost', true);". PHP_EOL);
        fwrite($file,"define('DB_PUERTO', '3306', true);". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_BIE', '', true);". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_DEFAULT', '', true);". PHP_EOL);
        fwrite($file,"define('SELECTRA_CONF_PYME', 'selectra_planilla_conf',true);". PHP_EOL);
        fwrite($file,"define('SUGARCRM', 'sugarcrm',true);". PHP_EOL);
        fwrite($file,"if (isset("."$"."_SESSION['EmpresaContabilidad']))". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_CONT',"."$"."_SESSION['EmpresaContabilidad'], true);". PHP_EOL);
        fwrite($file,"if (isset("."$"."_SESSION['Empresa_Nomina']))". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_NOM',"."$"."_SESSION['EmpresaNomina'], true);". PHP_EOL);
        fwrite($file,"else". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_NOM', 'express_planilla', true);". PHP_EOL);
        fwrite($file,"if (isset("."$"."_SESSION['EmpresaFacturacion']))". PHP_EOL);
        fwrite($file,"define('DB_SELECTRA_FAC',"."$"."_SESSION['EmpresaFacturacion'], true);". PHP_EOL);
        fwrite($file,"$"."_SESSION['ROOT_PROYECTO']= str_replace('\\', '/' , dirname(__FILE__) );  ". PHP_EOL);
        fwrite($file,"$"."_SESSION['LIVEURL']= 'http://localhost/selectra_planilla'; // CAMBIAR EN PRODUCCION". PHP_EOL);
        fwrite($file,"define('PATH_SF','{"."$"."_SESSION['LIVEURL']}/solucion/web/pyme.php/', true);". PHP_EOL);
        fwrite($file,"define('REG_INFO',0, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGIN_OK',1, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGIN_FAIL',2, true);". PHP_EOL);
        fwrite($file,"define('REG_LOGOUT',3, true);". PHP_EOL);
        fwrite($file,"define('REG_SESSION_INVALIDATE',4, true);". PHP_EOL);
        fwrite($file,"define('REG_SESSION_READ_ERROR',5, true);". PHP_EOL);
        fwrite($file,"define('REG_SQL_OK',6, true);". PHP_EOL);
        fwrite($file,"define('REG_SQL_FAIL',7, true);". PHP_EOL);
        fwrite($file,"define('REG_ILLEGAL_ACCESS',8, true);". PHP_EOL);
        fwrite($file,"define('REG_ALL',9, true);". PHP_EOL);
        fwrite($file,"$"."ConnSys = array('server' => DB_HOST, 'user' => DB_USUARIO, 'pass' => DB_CLAVE, 'db' => DB_SELECTRA_DEFAULT);". PHP_EOL);
        fwrite($file,"$"."config['bd']='mysql';". PHP_EOL);
        fwrite($file,"require_once('funciones.inc.php');". PHP_EOL);
        fwrite($file,"?>". PHP_EOL);        
        fclose($file);
    return true;
    }
    public function validar($host,$data,$opc)
    {
        $conexion= mysqli_connect($host['host'],$host['usuario'],$host['password'],$host['database2']);
        if ($opc == 1)
        {
            $sql="SELECT descrip, correo, login_usuario FROM `nomusuarios` WHERE descrip = 'Administrador' OR correo = '".$data['correo']."' OR login_usuario = '".$data['usuario_admin']."'";
        }else{
            $sql="SELECT nombre, bd_nomina FROM `nomempresa` WHERE nombre = '".$data['nombre']."' OR bd_nomina = '".$data['bd_nomina']."'";
        }
        if (mysqli_query($conexion,$sql))
        {
            $res = true;
        }else{
            $res = false;
        }
        mysqli_close($conexion);
        return $res;
    }
}
