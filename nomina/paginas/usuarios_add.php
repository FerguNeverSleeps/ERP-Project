<?php
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
include ("../header4.php");
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
//-----------------------------------------------------

$consulta_emp  = "SELECT nomniv1,nomniv2,nomniv3,nomniv4,nomniv5,nomniv6,nomniv7 FROM nomempresa";
$nomemp    = $db1->query($consulta_emp)->fetch_assoc();
$consulta  = "SELECT codorg, descrip FROM nomnivel1";
$nivel1    = $db1->query($consulta);
$consulta  = "SELECT codorg, descrip FROM nomnivel2";
$nivel2    = $db1->query($consulta);
$consulta  = "SELECT id,descripcion FROM roles WHERE estado = 1";
$res_roles = $db->query($consulta);
$usuario_creacion = $_SESSION['usuario'];
$host = $_SERVER['SERVER_ADDR'];

//-----------------------------------------------------
$modulos = cargarModulos($db1);
//-----------------------------------------------------
$res_emp = $db->query("SELECT codigo, nombre FROM nomempresa WHERE 1 ORDER BY nombre");
$i=0;
while ($empresa = mysqli_fetch_array($res_emp)){
  $empresas[$i] = array( "nombre" => $empresa['nombre'], "codigo" => $empresa['codigo'] );
  $i++;
}
//-----------------------------------------------------
$res_pla = $db1->query("SELECT codtip, descrip FROM nomtipos_nomina WHERE 1 ORDER BY descrip");
$i=0;
while ($planilla = mysqli_fetch_array($res_pla)){
  $planillas[$i] = array( "descripcion" => $planilla['descrip'], "codigo" => $planilla['codtip'] );
  $i++;
}
//-----------------------------------------------------
$res_per = $db->query("SELECT * FROM permisos ORDER BY id_modulo");
$i=0;
while ($permiso = mysqli_fetch_array($res_per)){
  $permisos[$i] = array( "id" => $permiso['id'], "nombre" => $permiso['nombre'], "id_modulo" => $permiso['id_modulo'] );
  $i++;
}
//-----------------------------------------------------
$res_rpt = $db1->query("SELECT a.* FROM nom_paginas a 
  LEFT JOIN nom_modulos b ON a.cod_modulo = b.cod_modulo
  WHERE nom_menu='Planilla'");
$i=0;
while ($reporte = mysqli_fetch_array($res_rpt)){
  $reportes[$i] = array( "descripcion" => $reporte['descripcion'], "codigo" => $reporte['id_pagina'] );
  $i++;
}

//Obtiene la IP del cliente
function get_client_ip() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
      $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
      $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
      $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
      $ipaddress = getenv('REMOTE_ADDR');
  else
      $ipaddress = 'UNKNOWN';
  return $ipaddress;
}
//-----------------------------------------------------
$accion = (isset($_GET['accion']))?$_GET['accion']:$_POST['accion'];
if(isset($_POST['aceptar']))
{
  
  $select_usuario = "SELECT * from nomusuarios WHERE login_usuario = '{$_POST[login_usuario]}'";
  $result1 = $db->query($select_usuario);
  if($result1->num_rows <1)
  {
    function make_thumb($src, $dest, $desired_width) {
      /* read the source image */
      $source_image = imagecreatefromjpeg($src);
      $width = imagesx($source_image);
      $height = imagesy($source_image);
      /* find the “desired height” of this thumbnail, relative to the desired width  */
      $desired_height = floor($height * ($desired_width / $width));
      /* create a new, “virtual” image */
      $virtual_image = imagecreatetruecolor($desired_width, $desired_height);
      /* copy source image at a resized size */
      imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
      /* create the physical thumbnail image to its destination */
      imagejpeg($virtual_image, $dest);
    }

    $fileName = "";
    
    if($_FILES['img']['name'] != ""){
        $uploaddir = '../img_sis/avatars/';//<----This is all I changed
        $uploadfile = $uploaddir .$_POST["login_usuario"]."_".basename($_FILES['img']['name']);
        $fileName = $_POST["login_usuario"]."_".$_FILES['img']['name'];

    }

    if($_FILES['img']['name'] != ""){
        $uploaddir = '../img_sis/avatars/';//<----This is all I changed
        $uploadfile = $uploaddir .$_POST["login_usuario"]."_".basename($_FILES['img']['name']);
        $fileName = $_POST["login_usuario"]."_".$_FILES['img']['name'];

        if (move_uploaded_file($_FILES['img']['tmp_name'], $uploadfile)) {
            make_thumb($uploadfile, $uploaddir."thumbs/".$_POST["login_usuario"]."_".basename($_FILES['img']['name']), 40);
        }
    }

    $sql = "INSERT INTO `nomusuarios`( 
    `descrip`,
    `clave`,
    `correo`,
    `login_usuario`,
    `img`,
    `acceso_sueldo`,
    `acceso_contraloria`,
    `acceso_s_efecto`,
    `acceso_editar`,
    `acceso_imprimir`,
    `acceso_c_familiares`,
    `acceso_expedientes`,
    `region`,
    `departamento`,
    `acceso_dir`,
    `acceso_dep`,
    `id_rol`
    ) VALUES ( 
    '".$_POST['descrip']."',
    '".hash("sha256",$_POST['clave'])."',
    '".$_POST['correo']."',
    '".$_POST['login_usuario']."',
    '".$fileName."',
    '".((isset($_POST['acceso_sueldo']))?1:0)."',
    '".((isset($_POST['acceso_contraloria']))?1:0)."',
    '".((isset($_POST['acceso_s_efecto']))?1:0)."',
    '".((isset($_POST['acceso_editar']))?1:0)."',
    '".((isset($_POST['acceso_imprimir']))?1:0)."',
    '".((isset($_POST['acceso_c_familiares']))?1:0)."',
    '".((isset($_POST['acceso_expedientes']))?1:0)."',
    '".$_POST['region']."',
    '".$_POST['departamento']."',
    '".$_POST['acceso_dir']."',
    '".$_POST['acceso_dep']."',
    '".$_POST['id_rol']."' )";

    $db->query("SET AUTOCOMMIT=0");
    $db->query("START TRANSACTION");
    $a1 = $db->query($sql);
    $codigo = $db->insert_id();
    $id_msj = "codigo=".$codigo;
    $a2 = cargaAccesosEmpresa( $codigo,$_POST['id_rol'], $db );
    $a3 = cargaAccesosPlanilla( $codigo,$_POST['id_rol'], $db );
    $a4 = cargaAccesosUsuarios( $codigo,$_POST['id_rol'], $db );
    $a5 = cargaAccesosModulos( $codigo, $db );
    $a6 = cargaAccesosPaginas( $codigo,$_POST['id_rol'], $db );
    if ( $a1 and $a2 and $a3 and $a4 and $a5 and $a6) {
        $db->query("COMMIT");
      
        //LOG TRANSACCIONES - AGREGAR USUARIO              
        $descripcion_transaccion = 'AGREGAR USUARIO: ' . $_POST['login_usuario'] . ', ' . $_POST['descrip']  . ', Cod Usuario: '. $codigo .', Correo: '. $_POST['correo'].', Region: '. $_POST['region'].', Departamento: '. $_POST['departamento'].', Acceso: '. $_POST['acceso_dir'].' - '. $_POST['acceso_dep'].',  Rol: '. $_POST['id_rol'];
        $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
        VALUES ('', '".$descripcion_transaccion."', now(), 'Agregar Usuario', 'usuarios_add.php', 'Agregar','".$_POST['login_usuario']."','".$usuario_creacion."','".get_client_ip()."')";
        $res_transaccion = $db1->query($sql_transaccion);
        $msj = "&msj=success";
        $data = "&data=Usuario creado con exito..!!";
        if ($_POST['pregunta'] == "no") {
          header("location:usuarios_list.php?accion=".$accion."&$id_msj$msj$data");
        }else{
          header("location:usuarios_permisos.php?accion=".$accion."&$id_msj$msj$data");
        }
    } else {        
        $db->query("ROLLBACK");
        $msj = "&msj=danger";
        $data = "&data=Error al crear al Usuario..!!";
        header("location:usuarios_add.php?accion=".$accion."&$id_msj$msj$data");
    }
  }
  else{
    $msj = "&msj=danger";
    $data = "&data=Seleccione otro nombre de usuario";
    header("location:usuarios_add.php?accion=".$accion."&$id_msj$msj$data");
  
  }
}
else{
  $usuario['descrip'] = $_POST['descrip'];
  $usuario['clave'] = $_POST['clave'];
  $usuario['correo'] = $_POST['correo'];
  $usuario['login_usuario'] = $_POST['login_usuario'];
  $usuario['img'] = $_POST['img'];
  $usuario['acceso_sueldo'] = $_POST['acceso_sueldo'];
  $usuario['acceso_contraloria'] = $_POST['acceso_contraloria'];
  $usuario['acceso_s_efecto'] = $_POST['acceso_s_efecto'];
  $usuario['acceso_editar'] = $_POST['acceso_editar'];
  $usuario['acceso_imprimir'] = $_POST['acceso_imprimir'];
  $usuario['acceso_c_familiares'] = $_POST['acceso_c_familiares'];
  $usuario['acceso_expedientes'] = $_POST['acceso_expedientes'];
  $usuario['region'] = $_POST['region'];
  $usuario['departamento'] = $_POST['departamento'];
  $usuario['acceso_dir'] = $_POST['acceso_dir'];
  $usuario['acceso_dep'] = $_POST['acceso_dep'];
  $usuario['id_rol'] = $_POST['id_rol'];
  $dpto = mysqli_fetch_array( $db1->query( "SELECT codorg,descrip FROM nomnivel2 WHERE codorg = '".$usuario['departamento']."'" ) );
}


?>

<form name="sampleform" method="post" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="page-container">
  <div class="page-content-wrapper">

    <div class="row">
      <div class="col-md-12">
        <?php if (isset($_GET['msj'])): ?>
        <div class="alert alert-<?= $_GET['msj'] ?>">
            <button class="close" data-close="alert"></button>
            <span><?php echo $_GET['data'] ?></span>
        </div>
        <?php endif ?>
        <div class="portlet box blue">
          <div class="portlet-title">
            <div class="caption">
              <img src="../imagenes/21.png" width="22" height="22" class="icon"> Usuarios <?= $accion ?>
            </div>
            <div class="actions">
              <a class="btn btn-sm blue"  onclick="javascript: window.location='usuarios_list.php'">
                <i class="fa fa-arrow-left"></i> Regresar
              </a>
            </div>
          </div>
          <div class="portlet-body">
            <div class="row">
              <div class="col-md-4">
                <label>Agregar Nuevo Registro de Usuarios</label>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-lg-12">
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <input type="hidden" name="accion" value="<?= $accion ?>">
                    <label for="usr">Nombre:</label>
                    <input type="text" name="descrip" maxlength="60" class="form-control" placeholder="Inserte su nombre" pattern="[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?(( |\-)[a-zA-ZÀ-ÖØ-öø-ÿ]+\.?)*" title="El nombre debe tener por lo menos 1 caracter al inicio (Numeros no permitidos)" required>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Usuario:</label>
                    <input type="text" name="login_usuario" maxlength="20" class="form-control" pattern="[A-Za-z0-9]{4,20}" placeholder="Inserte el Nombre de Usuario" title="El nombre de usuario debe tener por lo menos 4 caracteres" required>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Correo Electronico:</label>
                    <input type="email" name="correo" maxlength="50" class="form-control" placeholder="Inserte su Correo Electronico" required>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Contrase&#241;a:</label>
                    <input type="password" name="clave" maxlength="20" class="form-control" placeholder="Ingrese Contraseña" pattern="[A-Za-z0-9!?-]{5,20}" title="La contraseña debe tener por lo menos 5 caracteres alfanumericos" required>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr"><?= $nomemp['nomniv1'] ?></label>
                    <select name="region" id="region" class="form-control" required>
                      <option value="0">Seleccione <?= $nomemp['nomniv1'] ?></option>
                    <?php while ($region=mysqli_fetch_array($nivel1)): ?>
                      <option value="<?php echo $region['codorg'] ?>" <?=($usuario['region']==$region['codorg'])?"selected":""?> ><?php echo $region['descrip'] ?></option>
                    <?php endwhile ?>
                    </select>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Confirmar Contrase&#241;a:</label>
                    <input type="password" name="comprobar" maxlength="20" class="form-control" placeholder="Ingrese su Contraseña nuevamente" required>
                  </div>
                </div>
                                <?php
                if ($nomemp['nivel2'] != '') {
                  ?>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Departamento:</label>
                    <select name="departamento" id="departamento" class="form-control">
                      <option value="<?= $dpto['codorg'] ?>" selected><?= (isset($dpto['descrip']))?$dpto['descrip']:"Seleccione un Departamento" ?></option>
                    </select>
                  </div>
                </div>
                                     <?php
                }?>             
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Rol:</label>
                    <select name="id_rol" id="id_rol" class="form-control" required>
                      <option value="0">Seleccione un Rol</option>
                      <?php while ($rol=mysqli_fetch_array($res_roles)): ?>
                      <option value="<?= $rol['id'] ?>"><?= $rol['descripcion'] ?></option>
                      <?php endwhile ?>
                    </select>
                  </div>
                </div>
                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Foto:</label>
                    <input type="file" name="img">
                  </div>
                </div>
                                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Desea Extender los permisos del Rol:</label>
                    <select name="pregunta" id="pregunta" class="form-control" required>
                      <option value="0">Seleccione</option>
                      <option value="si">Si</option>
                      <option value="no">No</option>
                    </select>
                  </div>
                </div>
                                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Ver multiples Direcciones:</label>
                    <select name="acceso_dir" id="acceso_dir" class="form-control" required>
                      <option value="0">Seleccione</option>
                      <option value="1">Si</option>
                      <option value="0">No</option>
                    </select>
                  </div>
                </div>
                                
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="usr">Ver multiples Departamentos:</label>
                    <select name="acceso_dep" id="acceso_dep" class="form-control" required>
                      <option value="0">Seleccione</option>
                      <option value="1">Si</option>
                      <option value="0">No</option>
                    </select>
                  </div>
                </div>

              </div>
            </div>
            <hr>

            <div class="row">
              <div class="col-md-2">
                <label>Acceso a Sueldo</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_sueldo']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_sueldo']; ?>" name="acceso_sueldo">
              </div>
              <div class="col-md-2">
                  <label>Acceso a Contraloria</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_contraloria']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_contraloria']; ?>" name="acceso_contraloria">
              </div>
              <div class="col-md-2">
                <label>Acceso a dejar sin Efecto</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_s_efecto']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_s_efecto']; ?>" name="acceso_s_efecto">
              </div>
              <div class="col-md-2">
                <label>Acceso a Editar</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_editar']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_editar']; ?>" name="acceso_editar">
              </div>    
            </div>
            <br>

            <div class="row">
              <div class="col-md-2">
                <label>Acceso a imprimir</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_imprimir']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_imprimir']; ?>" name="acceso_imprimir">
              </div>
              <div class="col-md-2">
                <label>Acceso a cargas familiares</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_c_familiares']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_c_familiares']; ?>" name="acceso_c_familiares">
              </div>
              <div class="col-md-2">
                <label>Acceso a ver expedientes</label>
              </div>
              <div class="col-md-1">
                <input type="checkbox" <?php echo(($usuario['acceso_expedientes']==1) ? "checked" : ""); ?> value="<?php echo $usuario['acceso_expedientes']; ?>" name="acceso_expedientes">
                </div>
            </div>
            <hr>
            <div align="right">
              <input class="btn btn-primary" type="submit" id="aceptar" name="aceptar" value="<?= $accion ?>">&nbsp;<input type="button" name="cancelar" class="btn btn-primary" value="Cancelar" onclick="javascript: window.location='usuarios_list.php'">
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</form>
<script type="text/javascript" src="../../includes/js/jquery.min.js"></script>
<script>
$(document).ready(function(){

  $("input[name=admin]").change(function(){
    $("input[type='checkbox']").each( function() {
      if($("input[name=admin]:checked").length == 1){
        this.checked = true;
      } else {
        this.checked = false;
      }
    });
  });

  $( "#region" ).change(function(event){
    var gerencia = $(this);
    filtrarSelect( gerencia.val() );
  });
  
  function fillSelect(data) {
    $.each(data, function(i, item) {
      $("#departamento").append('<option value="'+item.codorg+'">'+item.descrip+'</option>');
    });
  }
  
  function filtrarSelect(id){
    $.ajax({
      url  : 'ajax/filtro_region.php',
      type : 'POST',
      data : {
          id:id,
          ajax    : true
      },
      dataType : 'json',
      success : function(response) {
          // Limpiamos el select
          $("#departamento").find('option').remove();
          fillSelect(response['respuesta']);
      },
      error : function(xhr, status) {
          console.log(xhr);
      },
      complete : function(xhr, status) {
      }
    });
  }

});
</script>
</body>
</html>
