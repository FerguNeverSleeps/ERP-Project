<?php
//=================================================================================================================
// Subir foto del integrante

 $sql_workflow        = "SELECT workflow,db_host,db_name,db_user,db_pass FROM param_ws";
 $res_workflow        = $db->query($sql_workflow);
 $res_workflow        = $res_workflow->fetch_array();
 $usuario_creacion = $_SESSION['usuario'];
 $fecha = date("Y-m-d");
 $ultimo_id = '';

 //echo $res_workflow["workflow"].'zxzczc';
                    
if($res_workflow["workflow"]==1)
{
    $workflow       = $res_workflow["workflow"];
    $db_host        = $res_workflow["db_host"];
    $db_name        = $res_workflow["db_name"];
    $db_user        = $res_workflow["db_user"];
    $db_pass        = $res_workflow["db_pass"];    
    $conexion       = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die('No se puede conectar con el servidor Workflow');
    mysqli_query($conexion, 'SET CHARACTER SET utf8');
}

$archivo_foto = isset($_FILES['foto']['name']) ? $_FILES['foto']['name'] : '';
$setFoto      = "";
if($archivo_foto!='')
{
    $nombre_foto  = $_POST['ficha']."_".$archivo_foto;
    $dir_fotos    = "fotos/";
    if (copy($_FILES['foto']['tmp_name'], $dir_fotos . $nombre_foto)) 
    {
        chmod( $dir_fotos . $nombre_foto, 0777);
        $insertFoto = $dir_fotos . $nombre_foto;
        $setFoto    = "foto='". $dir_fotos . $nombre_foto."', ";
    } 
    else
        throw new Exception("Error al subir la foto", 1);            				
}

// Imagen cedula del integrante
$archivo_cedula  = isset($_FILES['imagen_cedula']['name']) ? $_FILES['imagen_cedula']['name'] : '';
$setImagenCedula = "";

if($archivo_cedula!='')
{
    $nombre_imagen  = $_POST['ficha']."_".$archivo_cedula;
    $dir_imagen     = "fotos/";    
    if (copy($_FILES['imagen_cedula']['tmp_name'], $dir_imagen . $nombre_imagen)) 
    {
        chmod( $dir_imagen  . $nombre_imagen, 0777);
        $insertImagenCedula = $dir_imagen . $nombre_imagen;
        $setImagenCedula    = "imagen_cedula='fotos/".$nombre_imagen."', ";
    } 
    else
        throw new Exception("Error al subir la imagen de la cedula", 1);            				
}

$niveles = array('codnivel1', 'codnivel2', 'codnivel3', 'codnivel4', 'codnivel5', 'codnivel6', 'codnivel7');

foreach ($niveles as $nivel) {
	$$nivel = 0;

	if(isset($_POST[$nivel])  &&  $_POST[$nivel] != '')
	{
		$$nivel = $_POST[$nivel];
	}
}

$suesal             = (isset($_POST['suesal']) ? $_POST['suesal'] : 0);
$sueldopro          = (isset($_POST['suesal']) ? $_POST['suesal'] : 0);
$sueldo_original    = (isset($_POST['sueldo_original'])) ? $_POST['sueldo_original'] : 0 ;

$fecnac             = DateTime::createFromFormat('d-m-Y', $_POST['fecnac']); // 16-01-1962
$fecnac             = ($fecnac !== false) ? $fecnac->format('Y-m-d') : '';	

$fecing             = DateTime::createFromFormat('d-m-Y', $_POST['fecing']);
$fecing             = ($fecing !== false) ? $fecing->format('Y-m-d') : '';

$fechainicio             = DateTime::createFromFormat('d-m-Y', $_POST['fechainicio_estructura']);
$fechainicio             = ($fechainicio !== false) ? $fechainicio->format('Y-m-d') : '';

$fecha_decreto      = DateTime::createFromFormat('d-m-Y', $_POST['fecha_decreto']);
$fecha_decreto      = ($fecha_decreto !== false) ? $fecha_decreto->format('Y-m-d') : '';

$fecha_resolucion      = DateTime::createFromFormat('d-m-Y', $_POST['fecha_resolucion']);
$fecha_resolucion      = ($fecha_resolucion !== false) ? $fecha_resolucion->format('Y-m-d') : '';	

$fecha_decreto_baja = DateTime::createFromFormat('d-m-Y', $_POST['fecha_decreto_baja']);
$fecha_decreto_baja = ($fecha_decreto_baja !== false) ? $fecha_decreto_baja->format('Y-m-d') : '';

$fecha_resolucion_baja = DateTime::createFromFormat('d-m-Y', $_POST['fecha_resolucion_baja']);
$fecha_resolucion_baja = ($fecha_resolucion_baja !== false) ? $fecha_resolucion_baja->format('Y-m-d') : '';

$inicio_periodo     = (isset($_POST['inicio_periodo'])) ? $_POST['inicio_periodo']  : '';
$inicio_periodo     = DateTime::createFromFormat('d-m-Y', $inicio_periodo);
$inicio_periodo     = ($inicio_periodo !== false) ? $inicio_periodo->format('Y-m-d') : '';	

$fin_periodo        = (isset($_POST['fin_periodo'])) ? $_POST['fin_periodo']  : '';
$fin_periodo        = DateTime::createFromFormat('d-m-Y', $fin_periodo);
$fin_periodo        = ($fin_periodo !== false) ? $fin_periodo->format('Y-m-d') : '';

$fecha_permanencia        = (isset($_POST['fecha_permanencia'])) ? $_POST['fecha_permanencia']  : '';
$fecha_permanencia        = DateTime::createFromFormat('d-m-Y', $fecha_permanencia);
$fecha_permanencia         = ($fecha_permanencia !== false) ? $fecha_permanencia->format('Y-m-d') : '';

$fecha_creacion        = (isset($_POST['fecha_creacion'])) ? $_POST['fecha_creacion']  : '';
$fecha_creacion        = DateTime::createFromFormat('d-m-Y', $fecha_creacion);
$fecha_creacion         = ($fecha_creacion !== false) ? $fecha_creacion->format('Y-m-d') : '';

$fechajubipensi        = (isset($_POST['fecha_jubilacion'])) ? $_POST['fecha_jubilacion']  : '';
$fechajubipensi        = DateTime::createFromFormat('d-m-Y', $fechajubipensi);
$fechajubipensi         = ($fechajubipensi !== false) ? $fechajubipensi->format('Y-m-d') : '';

//lm
$tipo_empleado      = $_POST['tipo_empleado'];
$clave_ir           = $_POST['clave_ir'];
$apellido_materno   = $_POST['apellido_materno'];
$apellido_casada    = $_POST['apellido_casada'];
$observaciones      = $_POST['observaciones'];
$direccion2         = $_POST['direccion2'];	
$usuario            = $_POST['usuario'];	
//lm
//=================================================================================================================

if($_POST['solicitud']=='2'){
	$uid_user_aprueba=NULL;
}
else
{
    $uid_user_aprueba=$_POST['usuario_aprueba'];//$_POST['solicitud'];
}
if($operacion=='agregar')
{
	//Para validar el usuario

	$sqlNompersonal ="INSERT INTO nompersonal (foto, nomposicion_id, nacionalidad, cedula, sigla, provincia, tomo, folio, apellidos, nombres, nombres2, 
            apenom,sexo, estado_civil,fecnac, lugarnac, codpro, direccion, telefonos, email, estado, fecing, ficha, tipopres, forcob, codbancob, cuentacob, 
            codbanlph, cuentalph, tipemp, suesal, tipnom, codcat, codcargo,codnivel1, codnivel2, codnivel3, codnivel4, codnivel5, codnivel6, codnivel7,
            inicio_periodo, fin_periodo,turno_id, seguro_social, hora_base, segurosocial_sipe, dv, num_decreto, fecha_decreto, num_resolucion, fecha_resolucion,
            num_decreto_baja,fecha_decreto_baja, num_resolucion_baja, fecha_resolucion_baja, causal_despido, siacap, puesto_id, imagen_cedula,sueldopro, fecharetiro, 
            tipo_empleado, clave_ir, apellido_materno,apellido_casada, observaciones, direccion2, dir_provincia, dir_distrito, dir_corregimiento, correo_alternativo,
             uid_user_aprueba, 
             fecha_permanencia,
              TelefonoCelular, 
              IdTipoSangre,
              estatura,
              peso,
              IdNivelEducativo, 
              Hijos,
              ContactoEmergencia, 
              TelefonoContactoEmergencia,
              extension,
              tipo_contribuyente,
              carrera_legislativa,
              sabe_leer,
              sabe_escribir,
              titulo_profesional,
              institucion,
              tiene_discapacidad, 
              tiene_familiar_disca,
              marca_reloj,
               EnfermedadesYAlergias,
               ctacontab,gastos_representacion,
               otros,dieta,combustible,
               IdDepartamento,
               nomfuncion_id,
               tipo_funcionario,
               personal_externo,
               numero_carnet,
               codigo_carnet,
               numero_marcar,
               codigo_diputado,
               piso,
               oficina,
               unidad,
               condicion,
               comentario,
               sfecing,
               paso,
               id_promo,
               fechajubipensi
               ) 
                    VALUES 
                    (NULLIF('". (isset($insertFoto) ? $insertFoto : '') ."',''),
                     NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' ) ."',''),
                     '". $_POST['nacionalidad'] ."',
                     '". $_POST['cedula'] ."',
                     '". $_POST['sigla'] ."',
                     '". $_POST['provincia'] ."',
                     '". $_POST['tomo'] ."',
                     '". $_POST['folio'] ."',
                     '". $_POST['apellidos'] ."',
                     '". $_POST['nombres'] ."',
                     '". $_POST['segundo_nombre'] ."',
                     '". $_POST['apellidos'] . ", " . $_POST['nombres'] ."',
                     '". $_POST['sexo'] ."',
                     '". $_POST['estado_civil'] ."',
                     '". $fecnac ."',
                     '". $_POST['lugarnac'] ."',
                     '". $_POST['codpro'] ."',
                     '". $_POST['direccion'] ."',
                     '". $_POST['telefonos'] ."',
                     NULLIF('". $_POST['email'] ."',''),
                     '". $_POST['estado'] ."',
                     '". $fecing ."',
                     '". $_POST['ficha'] ."',
                     NULLIF('". (isset($_POST['tipopres']) ? $_POST['tipopres'] : '' ) ."',''),
                     '". $_POST['forcob'] ."',
                     NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
                     NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
                     NULLIF('". (isset($_POST['codbanlph']) ? $_POST['codbanlph'] : '') ."',''),
                     NULLIF('". (isset($_POST['cuentalph']) ? $_POST['cuentalph'] : '') ."',''),
                     '". $_POST['tipemp'] ."',
                     '". $suesal ."',
                     '". $_POST['tipnom'] ."',
                     '". $_POST['codcat'] ."',
                     '". $_POST['codcargo'] ."',
                     '". $codnivel1 ."',
                     '". $codnivel2 ."',
                     '". $codnivel3 ."',
                     '". $codnivel4 ."',
                     '". $codnivel5 ."',
                     '". $codnivel6 ."',
                     '". $codnivel7 ."',
                     NULLIF('". $inicio_periodo ."',''),
                     NULLIF('". $fin_periodo ."',''),
                     NULLIF('". $_POST['turno_id'] ."',''),
                     NULLIF('". $_POST['seguro_social'] ."',''),
                     '". $_POST['hora_base'] ."',
                     NULLIF('". $_POST['segurosocial_sipe'] ."',''),
                     NULLIF('". $_POST['dv'] ."',''),
                     NULLIF('". $_POST['num_decreto'] ."',''),
                     NULLIF('{$fecha_decreto}',''),
                     NULLIF('". $_POST['num_resolucion'] ."',''),
                     NULLIF('{$fecha_resolucion}',''),
                     NULLIF('". $_POST['num_decreto_baja'] ."',''),
                     NULLIF('{$fecha_decreto_baja}',''),
                     NULLIF('". $_POST['num_resolucion_baja'] ."',''),
                     NULLIF('{$fecha_resolucion_baja}',''),
                     '". $_POST['causal_baja'] ."',
                     NULLIF('". $_POST['siacap'] ."',''),
                     NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '') ."',''),
                     NULLIF('". (isset($insertImagenCedula) ? $insertImagenCedula : '') ."',''), 
                     '". $sueldopro ."',
                     '". $fecharetiro ."',
                     '". $tipo_empleado ."',
                     '". $clave_ir ."',
                     '". $apellido_materno ."',
                     '". $apellido_casada ."',
                     '". $observaciones ."',
                     '". $direccion2 ."',
                     '".$_POST['direccion_provincia']."',
                     '".$_POST['direccion_distrito']."',
                     '".$_POST['direccion_corregimiento']."',
                     '".$_POST['correo_alternativo']."',
                     '". $uid_user_aprueba."',
                     '".$fecha_permanencia."',
                     '".$_POST['telefono_celular']."',
                     '".$_POST['tipo_sangre']."',
                     '".$_POST['estatura']."',
                     '".$_POST['peso']."',
                     '".$_POST['nivel_educativo']."',
                     '".$_POST['hijos']."',
                     '".$_POST['contacto_emergencia']."',
                     '".$_POST['telefono_contacto']."',
                     '".$_POST['extension']."',
                     '".$_POST['tipo_contribuyente']."',
                     '".$_POST['carrera_legislativa']."',
                     '".$_POST['sabe_leer']."',
                     '".$_POST['sabe_escribir']."',
                     '".$_POST['titulo_profesional']."',
                     '".$_POST['institucion_educativa']."',
                     '".$_POST['discapacidad']."',
                     '".$_POST['fam_discapacidad']."',
                     '".$_POST['marca_reloj']."',
                     '".$_POST['enfermedades_alergias']."',
                     '".$_POST['cuentacontable_estructura']."',
                     '".$_POST['gastos_repre']."', 
                     '".$_POST['otros']."',
                     '".$_POST['dieta']."',
                     '".$_POST['combustible']."',
                     '".$_POST['departamento_estructura']."',
                     '".$_POST['funcion_estructura']."',
                     '".$_POST['tipoempleado_estructura']."',
                     '".$_POST['personalexterno']."',
                     '".$_POST['numero_carnet']."',
                     '".$_POST['codigo_carnet']."',
                     '".$_POST['numero_marcar']."',
                     '".$_POST['codigo_diputado']."',
                     '".$_POST['piso']."',
                     '".$_POST['oficina']."',
                     '".$_POST['f_unidad']."',
                     '".$_POST['f_tipoempleado_estructura']."',
                     '".$usuario_creacion."',
                     '".$fecha."',
                     '".$_POST['grado_etapa']."',
                     '".$_POST['promocion']."',   
                     '". $fechajubipensi ."'
                    )";
    //echo $sql;
    //
    $res_insert = $db->query($sqlNompersonal);
    
    $sql_id_empleado    = "SELECT max(personal_id) as personal_id  FROM nompersonal";
    $res_select         = $db->query($sql_id_empleado);
    $array_id_empleado  = $res_select->fetch_array();
    $id_personal        = $array_id_empleado['personal_id'];


   

	
//insert  posicionempleado
	 $sqlinsertarposicionempleado="INSERT INTO `posicionempleado`
	(`IdEmpleado`,
	 `FechaInicio`,
	`Posicion`, 
	`FechaFin`,
	`IdFuncion`,
	`Planilla`,
	`CuentaContable`,
	`IdDepartamento`,
	`IdTituloInstitucional`,
	`IdTipoEmpleado`,
	`Salario`,
	`gastos_repre`,
        `Resolucion`,
        `fecha_decre`,
        `otros`,
        `decre_nombra`)
        VALUES ('".$id_personal."',
            '".$fechainicio."',
            '".$_POST['nomposicion_id']."',
            '".$fecharetiro."',
            '".$_POST['funcion_estructura']."',
            '".$_POST['tipnom']."',
            '".$_POST['cuentacontable_estructura']."',
            '".$_POST['departamento_estructura']."',
            '".$_POST['codcargo']."',
            '".$_POST['tipoempleado_estructura']."',
            '".$_POST['suesal']."',
            '".$_POST['gastos_repre']."',
            NULLIF('".$_POST['num_decreto'] ."',''),
            NULLIF('".$fecha_decreto."',''),
            '".$_POST['otros']."',
            '".$_POST['num_decreto']."' 
    )"; 
         
    //ACTUALIZACION ESTADO POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$_POST['nomposicion_id']}'";
    
    
    
	
//echo $sqlinsertar,"<br> ";
        if($workflow==1)
        {
            if (strlen(trim($usuario))== 0)
            {
                echo "El usuario no puede estar en blanco. Por favor coloque la información de este campo.";
                exit();
            }
            else
            {
                    $sql = "SELECT pm_ver FROM param_ws";

                    $res = $db->query($sql);
                    $res = $res->fetch_array();

                    $rs  = $res["pm_ver"];   
                if(count($rs) > 0)
                {
                    $pm_ver = $res["pm_ver"];
                }

                /*VERIFICAR QUE EL USUARIO NO EXISTA*/
                if ($pm_ver == '2.5')
                {             
                            $sql = "SELECT USR_USERNAME
                            FROM USERS
                            WHERE USR_STATUS = 1
                            AND USR_USERNAME  = '". $usuario. "'";
                }
                else
                {
                            $sql1 = "SELECT USR_USERNAME
                            FROM USERS
                            WHERE USR_STATUS = 'ACTIVE'
                            AND USR_USERNAME  = '". $usuario. "'";
                }
               /* $rResult = mysqli_query( $conexion, $sql ) or die(mysqli_error($conexion));
                $fila = mysqli_fetch_array($rResult);
                //echo $fila["USR_USERNAME"],"<br>";

               // $rResult1 = mysqli_query( $conexion, $sql1 ) or die(mysqli_error($conexion));


                if(count($fila["USR_USERNAME"]) > 0)
                {
                    echo "El usuario ya existe. Por favor modifique el usuario. 1";
                    exit();
                }
                if(count($rResult1) > 0)
                {
                    echo "El usuario ya existe. Por favor modifique el usuario.";
                    exit();
                }*/
            //Para Crear el usuario
                    $sql        = "SELECT usuario, user_p, dir_ws, id_g_empleados FROM param_ws";
                    $res        = $db->query($sql);       
                    $res        = $res->fetch_array();

                    $userproc   = $res["usuario"];
                    $pass       = 'md5:'. $res["user_p"];
                    $dir_ws     = $res["dir_ws"];
                    $id_g_emple = $res["id_g_empleados"];

                    $client     = new SoapClient($dir_ws);
                    $params     = array(array('userid'=>$userproc, 'password'=>$pass));
                    $result     = $client->__SoapCall('login', $params);

                    //echo $result->status_code;
                    if ($result->status_code == 0)
                    {
                            $sessionId  = $result->message;
                            //echo "busqueda usuario: ",$result->status_code,"<br>";
                            $params     = array(array('sessionId'=>$sessionId, 'userId' => $usuario,
                            'firstname' =>$_POST['nombres'], 'lastname'=>$_POST['apellidos'], 'email'=>$_POST['email'],
                            'role'      =>'PROCESSMAKER_OPERATOR', 'password'=>'12345'));
                            //echo print_r($params),"<br>";

                            $result     = $client->__SoapCall('createUser', $params);
                            if ($result->status_code == 0)
                            { 
                                    //echo "crear usuario: ",$result->status_code,"<br>";

                                    $uid    = $result->userUID;
                                    $params = array(array('sessionId'=>$sessionId, 'userId'=>$uid, 'groupId' =>$id_g_emple));
                                    $result = $client->__SoapCall('assignUserToGroup', $params);
                            if ($result->status_code == 0)
                                    {
                                            //ACTUALIZAR EMPLEADO CON USR_UID
                                            //echo "ACTUALIZAR EMPLEADO CON USR_UID: ",$result->status_code,"<br>";

                                            $update_sql = " UPDATE nompersonal SET useruid='".$uid."', usuario_workflow = '".$usuario."',usr_password = MD5('12345')"
                                            . " WHERE cedula='".$_POST['cedula']."'";

                                            //echo $update_sql,"<br>";
                                            //$insert_empleado_posicion;

                                            $res2        = $db->query($update_sql); 
                                            
                                            
                                            $db->query($sqlinsertarposicionempleado);
                                            $db->query($consulta_posicion);
                                            //$res2        = $res2->fetch_array();
                                            //sc_exec_sql($update_sql);       
                                            /*if ({personal_externo} == 0)
                                            { 
                                                    sc_alert("Funcionario Creado. Por favor coloque la informaci�n del cargo.");               
                                            }
                                            else
                                            {
                                                    sc_alert("Persona Creada.");    
                                            }   */
                                    }
                                    else
                                    {

                                            //echo "Error intentando asignar el grupo al usuario";
                                            echo "<script>alert('Error intentando asignar el grupo al usuario.');</script>";
                                            echo "<script>document.location.href = 'ag_integrantes2.php';</script>";
                                            exit();
                                    }

                    }
                    else
                    {
                                    echo "<script>alert('Error intentando crear el usuario.');</script>";
                                    echo "<script>document.location.href = 'ag_integrantes2.php';</script>";
                                    exit();
                    }
               }
               else
               {
                            //echo "Error intentando conectarse a ProcessMaker.";
                            echo "<script>alert('Error intentando conectarse a ProcessMaker.');</script>";
                            echo "<script>document.location.href = 'ag_integrantes2.php';</script>";
                            exit();

               }	
            }  
        } 
        
        
        if($workflow==0)
        {
            if (strlen(trim($usuario))== 0)
            {
                echo "El usuario no puede estar en blanco. Por favor coloque la información de este campo.";
                exit();
            }
            else
            {
                    $update_sql = "UPDATE nompersonal SET useruid=NULL  WHERE personal_id='".$id_personal."'";

                    $res2        = $db->query($update_sql); 
                    $db->query($sqlinsertarposicionempleado);                                                                                
            }  
        } 
	
        if($res_insert)
	{
		
                //ACCION FUNCIONARIO
                if($_POST['tipoempleado_estructura']==6)
                {
                    $tipo_accion=1;
                }
                else if($_POST['tipoempleado_estructura']==3)
                {

                    $tipo_accion=2;
                }
                else if($_POST['tipoempleado_estructura']==1)
                {

                    $tipo_accion=3;
                }
                
//                echo "\n TIPO ACCION: "; echo $tipo_accion;
                

                $sql_corelativo = "SELECT correlativo FROM accion_funcionario_tipo "
                                . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
                $res_correlativo = $db->query($sql_corelativo);
                $res_correlativo = $res_correlativo->fetch_array();
                $correlativo = $res_correlativo["correlativo"];
                $correlativo = $correlativo+1;
                
//                echo "\n CORRELATIVO: "; echo $correlativo;
                
//                $sql_id = "SELECT MAX(personal_id) AS id FROM nompersonal";
//                $rs = $db->query($sql_id);
//                if ($row = mysql_fetch_row($rs)) 
//                {
//                    $id = trim($row[0]);
//                }
//
//                $ultimo_id = $id;
                
               $sql_accion="INSERT INTO accion_funcionario
                        (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
                        VALUES  
                        ('','{$tipo_accion}','{$correlativo}', '{$id_personal}')";
                
//                echo "\n SQL ACCION : "; echo $sql_accion;

                $res_accion = $db->query($sql_accion);               

                $sql_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                                    . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
                
//                echo "\n SQL ACCION TIPO  : "; echo $sql_accion_tipo;
                
               
                $res_accion_tipo = $db->query($sql_accion_tipo);
                
                //LOG TRANSACCIONES - AGREGAR FUNCIONARIO
                
                $descripcion_transaccion = 'Agregado Funcionario: ' . $_POST['apellidos'] . ', ' . $_POST['nombres']  . ' Cédula: '. $_POST['cedula'] .' Posición: '. $_POST['nomposicion_id'];

                $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
		            VALUES ('', '".$descripcion_transaccion."', now(), 'Datos-Integrantes-Contraloria', 'ag_integrantes2.php', 'Agregar','".$_POST['cedula']."','".$usuario_creacion."')";

		 $res_transaccion = $db->query($sql_transaccion);
                 
                 $conficha = $_POST['ficha'];
                
                $sql_ficha ="UPDATE nomempresa SET conficha='".$conficha."' ";
                $res_ficha = $db->query($sql_ficha);
                
		echo "<script>document.location.href = 'datos_integrantes/listado_integrantes_contraloria.php';</script>";
	}
	else
		echo "<script>alert('¡Hay errores en el proceso!');</script>";
	
}
else
{
        $sqlupdate = "UPDATE nompersonal SET 
                                ".$setFoto."
                                ".$setImagenCedula."
                                nomposicion_id=NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' ) ."',''),
                                nacionalidad = '". $_POST['nacionalidad'] ."',
                                cedula =  '". $_POST['cedula'] ."',
                                sigla =  '". $_POST['sigla'] ."',
                                provincia =  '". $_POST['provincia'] ."',
                                tomo =  '". $_POST['tomo'] ."',
                                folio =  '". $_POST['folio'] ."',
                                apellidos = '". $_POST['apellidos'] ."',                               
                                nombres = '". $_POST['nombres'] ."',
                                nombres2 = '". $_POST['segundo_nombre'] ."',
                                apenom = '". $_POST['apellidos'] . ", " . $_POST['nombres'] ."',
                                sexo = '". $_POST['sexo'] ."',               
                                estado_civil = '". $_POST['estado_civil'] ."',
                                fecnac =  '". $fecnac ."',
                                lugarnac = '". $_POST['lugarnac'] ."',
                                codpro = '". $_POST['codpro'] ."',
                                direccion = '". $_POST['direccion'] ."',
                                dir_provincia = '". $_POST['direccion_provincia'] ."',
                                dir_distrito = '". $_POST['direccion_distrito'] ."',
                                dir_corregimiento = '". $_POST['direccion_corregimiento'] ."',
                                telefonos = '". $_POST['telefonos'] ."',
                                email = NULLIF('". $_POST['email'] ."',''),
                                correo_alternativo = NULLIF('". $_POST['correo_alternativo'] ."',''),
                                ficha = '". $_POST['ficha'] ."',
                                tipopres = NULLIF('". (isset($_POST['tipopres']) ? $_POST['tipopres'] : '' ) ."',''),
                                forcob = '". $_POST['forcob'] ."',
                                codbancob = NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
                                cuentacob = NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
                                codbanlph =  NULLIF('". (isset($_POST['codbanlph']) ? $_POST['codbanlph'] : '') ."',''), 
                                cuentalph = NULLIF('". (isset($_POST['cuentalph']) ? $_POST['cuentalph'] : '') ."',''), 
                                tipemp = '". $_POST['tipemp'] ."',
                                suesal ='". $suesal ."',
                                tipnom = '". $_POST['tipnom'] ."',
                                codcat = '". $_POST['codcat'] ."',
                                codcargo = '". $_POST['codcargo'] ."',            
                                codnivel1 = '". $codnivel1 ."',
                                codnivel2 = '". $codnivel2 ."',
                                codnivel3 = '". $codnivel3 ."',
                                codnivel4 = '". $codnivel4 ."',
                                codnivel5 =  '". $codnivel5 ."',
                                codnivel6 = '". $codnivel6 ."',
                                codnivel7 =  '". $codnivel7 ."',
                                turno_id = NULLIF('". $_POST['turno_id'] ."',''),
                                seguro_social = NULLIF('". $_POST['seguro_social'] ."',''), 
                                hora_base = '". $_POST['hora_base'] ."',
                                segurosocial_sipe = NULLIF('". $_POST['segurosocial_sipe'] ."',''),
                                dv = NULLIF('". $_POST['dv'] ."',''),
                                num_decreto = NULLIF('". $_POST['num_decreto'] ."',''),
                                fecha_decreto = NULLIF('{$fecha_decreto}',''),
                                num_resolucion = NULLIF('". $_POST['num_resolucion'] ."',''),
                                fecha_resolucion = NULLIF('{$fecha_resolucion}',''),
                                num_decreto_baja =  NULLIF('". $_POST['num_decreto_baja'] ."',''),
                                fecha_decreto_baja = NULLIF('{$fecha_decreto_baja}',''),
                                num_resolucion_baja =  NULLIF('". $_POST['num_resolucion_baja'] ."',''),
                                fecha_resolucion_baja = NULLIF('{$fecha_resolucion_baja}',''),
                                siacap = NULLIF('". $_POST['siacap'] ."',''),
                                puesto_id = NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '') ."',''),
                                imagen_cedula = NULLIF('". (isset($insertImagenCedula) ? $insertImagenCedula : '') ."',''), 
                                sueldopro = '". $sueldopro ."',
                                fecharetiro =  '". $fecharetiro ."',
                                tipo_empleado = '". $tipo_empleado ."',
                                clave_ir = '". $clave_ir ."',
                                apellido_materno = '". $apellido_materno ."',
                                apellido_casada =  '". $apellido_casada ."',
                                observaciones = '". $observaciones ."',
                                direccion2 = '". $direccion2 ."',
                                uid_user_aprueba = '".$uid_user_aprueba."',
                                fecha_permanencia =  '".$fecha_permanencia."',
                                TelefonoCelular = '".$_POST['telefono_celular']."',
                                IdTipoSangre = '".$_POST['tipo_sangre']."',
                                IdNivelEducativo = '".$_POST['nivel_educativo']."',
                                titulo_profesional = '".$_POST['titulo_profesional']."',
                                institucion = '".$_POST['institucion_educativa']."',
                                Hijos = '".$_POST['hijos']."',
                                estatura = '".$_POST['estatura']."',
                                peso = '".$_POST['peso']."',
                                ContactoEmergencia = '".$_POST['contacto_emergencia']."',
                                TelefonoContactoEmergencia = '".$_POST['telefono_contacto']."',
                                tiene_discapacidad = '".$_POST['discapacidad']."',
                                tiene_familiar_disca = '".$_POST['fam_discapacidad']."',
                                EnfermedadesYAlergias = '".$_POST['enfermedades_alergias']."',
                                ctacontab = '".$_POST['cuentacontable_estructura']."',
                                gastos_representacion =  '".$_POST['gastos_repre']."', 
                                otros = '".$_POST['otros']."',
                                dieta =  '".$_POST['dieta']."', 
                                combustible = '".$_POST['combustible']."',
                                IdDepartamento = '".$_POST['departamento_estructura']."',
                                nomfuncion_id = '".$_POST['funcion_estructura']."',
                                tipo_funcionario = '".$_POST['tipoempleado_estructura']."',
                                personal_externo = '".$_POST['personalexterno']."' ,
                                numero_carnet = '".$_POST['numero_carnet']."' ,
                                codigo_carnet = '".$_POST['codigo_carnet']."' ,
                                numero_marcar = '".$_POST['numero_marcar']."' ,
                                extension = '".$_POST['extension']."' ,
                                tipo_contribuyente = '".$_POST['tipo_contribuyente']."' ,
                                carrera_legislativa = '".$_POST['carrera_legislativa']."' ,
                                sabe_leer = '".$_POST['sabe_leer']."' ,
                                sabe_escribir = '".$_POST['sabe_escribir']."' ,
                                unidad ='".$_POST['f_unidad']."',
                                condicion='".$_POST['f_tipoempleado_estructura']."',
                                marca_reloj='".$_POST['marca_reloj']."',
                                codigo_diputado='".$_POST['codigo_diputado']."',
                                piso='".$_POST['piso']."',
                                oficina='".$_POST['oficina']."',
                                usuario_workflow = '".$usuario."',
                                comentario = '".$usuario_creacion."',
                                sfecing = '".$fecha_creacion."',
                                paso='".$_POST['grado_etapa']."',
                                id_promo='".$_POST['promocion']."',
                                fechajubipensi = '".$fechajubipensi."'
            WHERE  personal_id = '".$_POST['personal_id']."'";
                                
         $sqlUpdatePosicionempleado="UPDATE posicionempleado SET
                                    FechaInicio = '".$fechainicio."',
                                    Posicion = '".$_POST['nomposicion_id']."', 
                                    FechaFin= '".$fecharetiro."',
                                    IdFuncion = '".$_POST['funcion_estructura']."',
                                    Planilla = '".$_POST['tipnom']."',
                                    CuentaContable= '".$_POST['cuentacontable_estructura']."',
                                    IdDepartamento= '".$_POST['departamento_estructura']."',
                                    IdTituloInstitucional = '".$_POST['codcargo']."',
                                    IdTipoEmpleado='".$_POST['tipoempleado_estructura']."',
                                    Salario =  '".$_POST['suesal']."',
                                    gastos_repre = '".$_POST['gastos_repre']."',
                                    Resolucion =   NULLIF('".$_POST['num_decreto'] ."',''),
                                    fecha_decre = NULLIF('".$fecha_decreto."',''),                                    
                                    otros='".$_POST['otros']."',
                                    decre_nombra='".$_POST['num_decreto']."'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         
                                 WHERE IdEmpleado= '".$_POST['personal_id']."'";
                   			 	
         
         $sqlEmpleadoCartgo ="UPDATE empleado_cargo SET IdDepartamento='".$_POST['departamento_estructura']."', FechaInicio='".$fechainicio."', FechaFinal='".$fecharetiro."', TipoMovimiento ='N' WHERE IdEmpleado='".$_POST['personal_id']."'";
         
         
        /*INSERT INTO empleado_cargo
	(IdEmpleado, IdDepartamento, FechaInicio, FechaFinal, TipoMovimiento, fecha_creacion)
	VALUES(NEW.IdEmpleado, NEW.IdDepartamento, NEW.FechaInicio, new.FechaFin, 'N', CURDATE());*/
                    
	/*$sql = "UPDATE nompersonal SET
				".$setFoto."
				".$setImagenCedula."
	            nacionalidad       = '".$_POST['nacionalidad']."',
	            nomposicion_id     = NULLIF('". (isset($_POST['nomposicion_id']) ? $_POST['nomposicion_id'] : '' )."',''),
	            cedula             = '".$_POST['cedula']."',
	            apellidos          = '".$_POST['apellidos']."',
	            nombres            = '".$_POST['nombres']."',
	            apenom             = '".$_POST['apellidos'].", ".$_POST['nombres']."',
	            sexo               = '".$_POST['sexo']."',
	            estado_civil       = '".$_POST['estado_civil']."',
	            fecnac             = '".$fecnac."',
	            lugarnac           = '".$_POST['lugarnac']."',
	            codpro             = '".$_POST['codpro']."',
	            direccion          = '".$_POST['direccion']."',
	            telefonos          = '".$_POST['telefonos']."',
	            email              = NULLIF('".$_POST['email']."',''),
	            estado             = '".$_POST['estado']."',
	            fecing             = '".$fecing."',
	            ficha              = '".$_POST['ficha']."',
	            tipopres           = NULLIF('". (isset($_POST['tipopres']) ? $_POST['tipopres'] : '' ) ."',''),
	            forcob             = '".$_POST['forcob']."',
	            codbancob          = NULLIF('". (isset($_POST['codbancob']) ? $_POST['codbancob'] : '') ."',''),
	            cuentacob          = NULLIF('". (isset($_POST['cuentacob']) ? $_POST['cuentacob'] : '') ."',''),
	            codbanlph          = NULLIF('". (isset($_POST['codbanlph']) ? $_POST['codbanlph'] : '' ) ."',''),
	            cuentalph          = NULLIF('". (isset($_POST['cuentalph']) ? $_POST['cuentalph'] : '' ) ."',''),
	            tipemp             = '".$_POST['tipemp']."',
	            suesal             = '".$suesal."',
	            tipnom             = '".$_POST['tipnom']."',
	            codcat             = '".$_POST['codcat']."',
	            codcargo           = '".$_POST['codcargo']."',
	            codnivel1	       = '".$codnivel1."',
	            codnivel2	       = '".$codnivel2."',
	            codnivel3	       = '".$codnivel3."',
	            codnivel4          = '".$codnivel4."',
	            codnivel5          = '".$codnivel5."',
	            codnivel6          = '".$codnivel6."',
	            codnivel7          = '".$codnivel7."',
	            inicio_periodo     = NULLIF('".$inicio_periodo."',''),
	            fin_periodo        = NULLIF('".$fin_periodo."',''), 
	            turno_id           = NULLIF('".$_POST['turno_id']."',''),
	            seguro_social      = NULLIF('".$_POST['seguro_social']."',''),
	            hora_base          = '".$_POST['hora_base']."',
	            segurosocial_sipe  = NULLIF('".$_POST['segurosocial_sipe']."',''),
	            dv                 = NULLIF('".$_POST['dv']."',''),
	            num_decreto        = NULLIF('".$_POST['num_decreto']."',''),
	            fecha_decreto      = NULLIF('{$fecha_decreto}','') ,
	            num_decreto_baja   = NULLIF('".$_POST['num_decreto_baja']."',''),
	            fecha_decreto_baja = NULLIF('{$fecha_decreto_baja}',''),
	            siacap             = NULLIF('".$_POST['siacap']."',''),
	            puesto_id          = NULLIF('". (isset($_POST['puesto_id']) ? $_POST['puesto_id'] : '')."',''),
	            sueldopro          = '".$suesal."',
	            tipo_empleado	   = '".$tipo_empleado."',
	            clave_ir		   = '".$clave_ir."',
	            apellido_materno   = '".$apellido_materno."',
                apellido_casada    = '".$apellido_casada."',
                observaciones 	   = '".$observaciones."',
                direccion2         = '".$_POST['direccion2']."'	                    
		   WHERE  personal_id = '".$_POST['personal_id']."'";*/

	$res = $db->query($sqlupdate);

	if($res)
	{
                $db->query($sqlUpdatePosicionempleado);
                $db->query($sqlEmpleadoCartgo);
                
                $sql_workflow        = "SELECT workflow,db_host,db_name,db_user,db_pass FROM param_ws";
                $res_workflow        = $db->query($sql_workflow);
                $res_workflow        = $res_workflow->fetch_array();
                //echo 'asdas'.$res_workflow["workflow"];
                if($res_workflow["workflow"]==1){
                    $workflow       = $res_workflow["workflow"];
                    $db_host        = $res_workflow["db_host"];
                    $db_name        = $res_workflow["db_name"];
                    $db_user        = $res_workflow["db_user"];
                    $db_pass        = $res_workflow["db_pass"];    
                    $conexion       = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die('No se puede conectar con el servidor Workflow');
                    mysqli_query($conexion, 'SET CHARACTER SET utf8');

                    $_sql = "SELECT useruid FROM nompersonal WHERE personal_id = '".$_POST['personal_id']."'";
                    $_res = $db->query($_sql);
                    while($fila = mysqli_fetch_array($_res))
                    {
                       $sql = "UPDATE USERS SET USR_USERNAME='$usuario' WHERE USR_UID ='".$fila['useruid']."'";            
                       $res         = $conexion->query($sql);
                    }

                    
                }
            
                
		if($suesal != $sueldo_original)
		{
			$descripcion = 'Modificacion de sueldo a ficha ' . $_POST['ficha'] . '. Sueldo anterior '. $sueldo_original;

			$sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes-Contraloria', 'ag_integrantes2.php', 'editar','".$suesal."','".$_SESSION['nombre'] ."')";

		    $res = $db->query($sql);
		}

		if($_POST['codcargo'] != $_POST['cargo_original'])
		{
			$descripcion = 'Modificacion de cargo a ficha '.$_POST['ficha'].'. Cargo anterior '.$_POST["cargo_original"];

			$sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes-Contraloria', 'ag_integrantes2.php', 'editar','".$_POST['codcargo']."','".$_SESSION['nombre'] ."')";

		    $res = $db->query($sql);	
		}

		if(isset($_POST['nomposicion_id']) && $_POST['nomposicion_id'] != $_POST['posicion_original'])
		{
			$descripcion = 'Modificacion de posicion a ficha '.$_POST['ficha'].'. Posicion anterior '.$_POST["posicion_original"];

			$sql = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
		            VALUES (NULL, '".$descripcion."', now(), 'Datos-Integrantes-Contraloria', 'ag_integrantes2.php', 'editar','".$_POST['nomposicion_id']."','".$_SESSION['nombre'] ."')";

		    $res = $db->query($sql);		
		}
                
                //LOG TRANSACCIONES - EDITAR FUNCIONARIO
                
                $descripcion_transaccion = 'Editado Funcionario: ' . $_POST['apellidos'] . ', ' . $_POST['nombres']  . ' Cédula: '. $_POST['cedula'] .' Posición: '. $_POST['nomposicion_id'];

                $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
		            VALUES ('', '".$descripcion_transaccion."', now(), 'Datos-Integrantes-Contraloria', 'ag_integrantes2.php', 'Editar','".$_POST['cedula']."','".$usuario_creacion."')";

		 $res_transaccion = $db->query($sql_transaccion);

		echo "<script>document.location.href = 'datos_integrantes/listado_integrantes_contraloria.php';</script>";
	}
	else
		echo "<script>alert('¡Hay errores en el proceso!');</script>";
}