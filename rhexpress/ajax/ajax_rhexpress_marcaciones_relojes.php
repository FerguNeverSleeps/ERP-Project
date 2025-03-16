<?php
  //-------------------------------------------------
  session_start();
  //-------------------------------------------------
  require_once "../config/rhexpress_config.php";
  $conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
  //-------------------------------------------------
  $ficha = $_SESSION['ficha_rhexpress'];
  //echo $_SESSION['bd'];
  if ($_REQUEST["search"]['value']!="") {
    $sql_and=" AND a.fecha like '%".$_REQUEST["search"]['value']."%'         
    ";
  }else{
    $sql_and='';
  }
  //-------------------------------------------------
  $sql = "SELECT *
        FROM reloj_detalle AS a
        LEFT JOIN nompersonal AS c ON a.ficha = c.ficha 
        WHERE a.ficha='{$ficha}' $sql_and
        ORDER BY a.fecha DESC limit ".$_REQUEST['start']." ," .$_REQUEST['length']."";
  $res = $conexion->query($sql);
  //-------------------------------------------------
  $row_cnt = mysqli_num_rows($res);
  //-------------------------------------------------
  $sql2 = "SELECT *
        FROM reloj_detalle AS a
        LEFT JOIN nompersonal AS c ON a.ficha = c.ficha 
        WHERE a.ficha='{$ficha}' $sql_and
        ORDER BY a.fecha DESC";
  $res2 = $conexion->query($sql2);
  //-------------------------------------------------
  $row_cnt2 = mysqli_num_rows($res2);

  $iTotalRecords   = $row_cnt2;
  $iDisplayLength  = intval($_REQUEST['length']);
  $iDisplayLength  = $iDisplayLength < 0 ? $iTotalRecords: $iDisplayLength; 
  $iDisplayStart   = intval($_REQUEST['start']);
  $sEcho           = intval($_REQUEST['draw']);
  
  $records         = array();
  $records["data"] = array(); 

  $end             = $iDisplayStart + $iDisplayLength;
  $end             = $end > $iTotalRecords ? $iTotalRecords : $end;

  $id=1;
  while ($fila=mysqli_fetch_array($res)) {
    $records["data"][] = array(
      ( ($fila['fecha']=="0000-00-00") ? "<strong>0000-00-00</strong>" : $fila['fecha'] ),
      ( ($fila['entrada']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['entrada'] ),
      ( ($fila['salmuerzo']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['salmuerzo'] ),
      ( ($fila['ealmuerzo']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['ealmuerzo'] ),
      ( ($fila['salida']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['salida'] ),
      // '<button type="button" id="botMarcaciones" class="btn btn-sm blue" data-toggle="modal" data-target="#marcaciones" data-ficha="'.$fila['ficha'].'" data-fecha="'.$fila['fecha'].'">
      // <i class="fa fa-clock-o" aria-hidden="true"></i> Registrar marcacion
      // </button>'
      // '<button type="button" id="botdetalles" class="btn btn-sm blue" data-toggle="modal" data-target="#incidencias" data-ficha="'.$fila['ficha'].'" data-fecha="'.$fila['fecha'].'">
      // <i class="fa fa-clock-o" aria-hidden="true"></i> Detalles de Incidencias
      // </button>',
      //( ($fila['h_ausencia']=="00:00:00") ? "00:00:00" : "<strong>".$fila['h_ausencia']."</strong>" ),
      // ( ($fila['tiempo']=="00:00:00") ? "<strong>00:00:00</strong>" : $fila['tiempo'] ),
      // ( ($fila['tardanza']=="00:00:00") ? "00:00:00" : "<strong>".$fila['tardanza']."</strong>" ),
      // ( ($fila['h_extra']=="00:00:00") ? "00:00:00" : "<strong>".$fila['h_extra']."</strong>" ),
      // ( ($fila['ausencia']==0) ? "NO" : "<strong>SI</strong>" ),
    );
    $id++;
  }
  $iTotalRecords = $id;

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "El grupo de acciónes se ha completado con éxito. ¡Bien hecho!";
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $row_cnt2;
  $records["recordsFiltered"] = $row_cnt2;
  
  echo json_encode($records);
?>