<?php
  //-------------------------------------------------
  session_start();
  //-------------------------------------------------
  require_once "../config/rhexpress_config.php";
  //-------------------------------------------------
  $ficha = $_SESSION['ficha_rhexpress'];
  $fecha = $_REQUEST['fecha'];
  //-------------------------------------------------
  $sql = "SELECT a.*,b.*
          FROM caa_incidencias_empleados a
          LEFT JOIN caa_incidencias b ON a.id_incidencia = b.id
          WHERE a.ficha = '$ficha' AND a.fecha = '$fecha'";
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
  while ($fila=mysqli_fetch_array($res)) {
    $records["data"][] = array(
      ( ($fila['fecha']==NULL) ? "0000-00-00" : $fila['fecha'] ),
      ( ($fila['descripcion']==NULL) ? "" : $fila['descripcion'] ),
      ( ($fila['acronimo']==NULL) ? "" : $fila['acronimo'] ),
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