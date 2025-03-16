<?php
  //-------------------------------------------------
  session_start();
  //-------------------------------------------------
  require_once "../config/rhexpress_config.php";
  //-------------------------------------------------
  //$useruid = $_SESSION['useruid_rhexpress'];
  $cedula=$_SESSION['cedula_rhexpress'];
  $tipo     = intval($_REQUEST['tipo']);
  //-------------------------------------------------
//  $sql = "SELECT fecha,tiempo,observacion,dias,horas,minutos 
//          FROM dias_incapacidad
//          WHERE usr_uid='$useruid' AND tipo_justificacion='$tipo'";
  
  $sql = "SELECT * FROM dias_incapacidad WHERE cedula='$cedula' AND tipo_justificacion='$tipo'";
  $res = $conexion->query($sql);
  //-------------------------------------------------
  $row_cnt = mysqli_num_rows($res);
  //-------------------------------------------------
  $iTotalRecords = $row_cnt;
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
  $iDisplayStart = intval($_REQUEST['start']);
  $sEcho = intval($_REQUEST['draw']);
  
  $records = array();
  $records["data"] = array(); 

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  $id=1;
  while ($fila=mysqli_fetch_array($res))
  {
      if($fila['tipo_justificacion']==3)
      {
          if($fila['dias_restante']!=NULL)
              $dias = $fila['dias_restante'];
          else
              $dias =0;
          if($fila['horas_restante']!=NULL)
              $horas = $fila['horas_restante'];
          else
              $horas = 0;
          if($fila['minutos_restante']!=NULL)
              $minutos = $fila['minutos_restante'];
          else
              $minutos = 0;

      }
      else
      {
          if($fila['dias']!=NULL)
              $dias = $fila['dias'];
          else
              $dias = 0;
          if($fila['horas']!=NULL)
              $horas = $fila['horas'];
          else
              $horas = 0;
          if($fila['minutos']!=NULL)
              $minutos = $fila['minutos'];
          else
             $minutos = 0;
      }
    $tiempo = ($tipo != 7) ? $fila['tiempo']:$fila['tiempo']/8;
    $records["data"][] = array(
      ( $dias ),
      ( $horas ),
      ( $minutos ),
      ( ($fila['fecha']==NULL) ? "00-00-0000" : $fila['fecha'] ),
      ( ($fila['observacion']==NULL) ? "Sin Observacion2" : $fila['observacion'] ),
     
    );
    $id++;
  }
  $iTotalRecords = $id;

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "El grupo de acciónes se ha completado con éxito. ¡Bien hecho!"; // pass custom message(useful for getting status of group actions)
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;
  
  echo json_encode($records);
?>