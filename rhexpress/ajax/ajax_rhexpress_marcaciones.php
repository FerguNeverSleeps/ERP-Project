<?php
  //-------------------------------------------------
  session_start();
  //-------------------------------------------------
  require_once "../config/rhexpress_config.php";
  //-------------------------------------------------
  $ficha = $_SESSION['ficha_rhexpress'];
  //-------------------------------------------------
  $sql = "SELECT a.*,b.descripcion
        FROM caa_resumen AS a
        LEFT JOIN nomturnos AS b ON a.turno_id = b.turno_id
        LEFT JOIN nompersonal AS c ON a.ficha = c.ficha
        WHERE a.ficha='$ficha'
        ORDER BY a.fecha DESC";
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
      ( ($fila['fecha']=="0000-00-00") ? "<strong>0000-00-00</strong>" : $fila['fecha'] ),
      ( ($fila['descripcion']==NULL) ? "" : $fila['descripcion'] ),
      ( ($fila['entrada']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['entrada'] ),
      ( ($fila['salida']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['salida'] ),
      ( ($fila['tiempo']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['tiempo'] ),
      ( ($fila['tardanza']=="00:00:00") ? "00:00:00" : "<strong>".$fila['tardanza']."</strong>" ),
      ( ($fila['h_extra']=="00:00:00") ? "00:00:00" : "<strong>".$fila['h_extra']."</strong>" ),
      ( ($fila['ausencia']==0) ? "NO" : "<strong>SI</strong>" ),
      ( ($fila['h_ausencia']=="00:00:00") ? "00:00:00" : "<strong>".$fila['h_ausencia']."</strong>" ),
      '<button type="button" id="botdetalles" class="btn btn-sm blue" data-toggle="modal" data-target="#incidencias" data-ficha="'.$fila['ficha'].'" data-fecha="'.$fila['fecha'].'">
      <i class="fa fa-clock-o" aria-hidden="true"></i> Detalles de Incidencias
      </button>',
    );
    $id++;
  }
  $iTotalRecords = $id;

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "El grupo de acciónes se ha completado con éxito. ¡Bien hecho!";
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;
  
  echo json_encode($records);
?>