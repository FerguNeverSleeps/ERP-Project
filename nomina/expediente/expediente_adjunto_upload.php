<?php
 include_once('clases/database.class.php');
include('obj_conexion.php');

if( !isset($_FILES['archivo_expediente']) )
{
  $nombre_pagina = "expediente_agregar_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula']."&adjunto=".$_POST['adjunto'];
    echo"
      <script type='text/javascript'>              
          alert('Debe elegir un archivo');
          window.location.href='$nombre_pagina';
      </script>
      ";
}
else
{
//    echo"
//      <script type='text/javascript'>              
//          alert('Entro Archivo');
//         
//      </script>
//      ";
    if ($_POST['principal']=="on")
              $principal=1;
          else
              $principal=0;
  
  $cedula = $_POST['cedula'];
  if (!file_exists("navegador_archivos/archivos/".$cedula)) 
  {
    mkdir("navegador_archivos/archivos/".$cedula, 0755, true);
  }
  
  $destino =  "navegador_archivos/archivos/".$cedula."/";    
 
  $nombre       = $_FILES['archivo_expediente']['name'];  
  $nombre_tmp   = $_FILES['archivo_expediente']['tmp_name'];
  $tipo         = $_FILES['archivo_expediente']['type'];
  $tamano       = $_FILES['archivo_expediente']['size'];
  
//  echo "tipo: "; echo $tipo; echo " Tamaño: "; echo $tamano;
  
  $ext_permitidas = array('jpg','jpeg','gif','png','pdf');
  $partes_nombre = explode('.', $nombre);
  $extension = end( $partes_nombre );
   
//  echo " Extensión: "; echo $extension;
  
  $ext_correcta = in_array($extension, $ext_permitidas);  
   
  $tipo_correcto = preg_match('/^image\/(pjpeg|jpeg|jpg|gif|png|pdf)$/', $tipo);
 
  $limite = 20000 * 1024;
//  echo " Extensión Correcta: "; echo $ext_correcta; echo " Tipo Correcto: "; echo $tipo_correcto;
//  echo " Limite: "; echo $limite; 
  
  //echo  $tipo_correcto.'</ br>';
  //echo $ext_correcta.'</ br>';
  //echo $limite.'</ br>';
  if( $ext_correcta && $tamano <= $limite )
  {//
    if( $_FILES['archivo_expediente']['error'] > 0 )
    {
      $nombre_pagina = "expediente_agregar_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula']."&adjunto=".$_POST['adjunto'];
          echo"
            <script type='text/javascript'>              
                alert('Error en archivo');
                window.location.href='$nombre_pagina';
            </script>
            ";
    }
    else
    {
//      echo"
//      <script type='text/javascript'>              
//          alert('Entro Archivo Correcto');
//         
//      </script>
//      ";
      if($_POST['adjunto']=='')
      {
//        echo"
//        <script type='text/javascript'>              
//            alert('Entro Guardar Nuevo');
//
//        </script>
//        ";
        if( file_exists('navegador_archivos/archivos/'.$cedula.'/'.$nombre) )
        {
            $nombre_pagina = "expediente_agregar_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula']."&adjunto=".$_POST['adjunto'];
            echo"
              <script type='text/javascript'>              
                  alert('Archivo ya existe');
                  window.location.href='$nombre_pagina';
              </script>
              ";
        }
        else
        {
//          echo"
//            <script type='text/javascript'>              
//                alert('Entro Guardar Nuevo - Archivo No Existe');
//
//            </script>
//            ";
          move_uploaded_file($nombre_tmp,$destino.$nombre); 
          //echo "<br/>Guardado en: " . $destino.$nombre;
          $archivo=$nombre;          
          $adjunto =array(
                              'nombre_adjunto'=>$_POST['tx_nombre'],
                              'descripcion'=>$_POST['tx_observacion'],
                              'cod_expediente_det'=>$_POST['codigo'],
                              'principal'=>$principal,
                              'tamano'=>$tamano,
                              'fecha'=>'NOW()',
                              'archivo'=>$archivo,
                              'tipo'=>$tipo
                          );
          $resp = $db->query_insert('expediente_adjunto',$adjunto);  
        }       
      }
      else
      {
          if( file_exists('navegador_archivos/archivos/'.$cedula.'/'.$nombre) )
          {
            $actualizacion="UPDATE expediente_adjunto SET "
                    . "`nombre_adjunto` = '{$_POST['tx_nombre']}',"
                    . "`descripcion` = '{$_POST['tx_observacion']}',"
                    . "`principal` = '{$principal}'"
                    . " WHERE id_adjunto='{$_POST['adjunto']}'";
            $resultado_actualizacion=$db->query($actualizacion);
          }
          else 
          {
            
            move_uploaded_file($nombre_tmp,$destino.$nombre); 
            $archivo=$nombre;
            $fecha = date("Y-m-d");
            $actualizacion="UPDATE expediente_adjunto SET "
                    . "`nombre_adjunto` = '{$_POST['tx_nombre']}',"
                    . "`descripcion` = '{$_POST['tx_observacion']}',"
                    . "`principal` = '{$principal}',"
                    . "`tamano` = '{$tamano}',"
                    . "`fecha` = '{$fecha}',"
                    . "`archivo` = '{$archivo}',"
                    . "`tipo` = '{$tipo}'"
                    . " WHERE id_adjunto='{$_POST['adjunto']}'";
            $resultado_actualizacion=$db->query($actualizacion);
          }
          
      }
      $nombre_pagina = "expediente_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula'];
      echo "<script language=javascript> window.location.href='$nombre_pagina';  </script>";
    }
  }
  else
  {
    $nombre_pagina = "expediente_agregar_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula']."&adjunto=".$_POST['adjunto'];
    if($_POST['adjunto']=='')
    {
        echo"
          <script type='text/javascript'>              
              alert('Archivo inválido o no seleccionado');
              window.location.href='$nombre_pagina';
          </script>
          ";
    }
    else
    {
        $actualizacion="UPDATE expediente_adjunto SET "
                    . "`nombre_adjunto` = '{$_POST['tx_nombre']}',"
                    . "`descripcion` = '{$_POST['tx_observacion']}',"
                    . "`principal` = '{$principal}'"
                    . " WHERE id_adjunto='{$_POST['adjunto']}'";
        $resultado_actualizacion=$db->query($actualizacion);
    }
    $nombre_pagina = "expediente_adjunto.php?codigo=".$_POST['codigo']."&cedula=".$_POST['cedula'];
      echo "<script language=javascript> alert('Archivo Adjuntado con Éxito'); window.location.href='$nombre_pagina';  </script>";
  }
}

