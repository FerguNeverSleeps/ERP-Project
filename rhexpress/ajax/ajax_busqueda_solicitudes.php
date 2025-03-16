<?php
  //-------------------------------------------------
  session_start();
  //-------------------------------------------------
  require_once "../config/rhexpress_config.php"; 
  $conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
         mysqli_query($conexion, 'SET CHARACTER SET utf8');  
  //-------------------------------------------------
  $ficha = $_SESSION['ficha_rhexpress'];
  //-------------------------------------------------
  $find_text = $_REQUEST['search[value]'];
  $cedula=$_REQUEST['por_cedula'];
  $add="";
if($cedula)
  $add.=" sc.cedula like '%$cedula%' AND ";
  $iDisplayLength  = intval($_REQUEST['length']);
 // $iDisplayLength  = $iDisplayLength < 0 ? $iTotalRecords: $iDisplayLength; 
  $iDisplayStart   = intval($_REQUEST['start']);
 // $sEcho           = intval($_REQUEST['draw']);
  $sql2="SELECT COUNT(*) as total FROM solicitudes_casos WHERE $add TRUE";
  $sql2="SELECT COUNT(*) as total FROM solicitudes_casos WHERE $add TRUE";
  $sql = "SELECT sc.id_solicitudes_casos,sc.cedula as cedula,sc.fecha_registro,sc.observacion_jefe,se.descrip_solicitudes_estatus as status,nm.descrip as departamento,st.descrip_solicitudes_tipos as tipo
  FROM solicitudes_casos as sc 
  LEFT JOIN solicitudes_estatus as se ON se.id_solicitudes_estatus=sc.id_solicitudes_casos_status
  LEFT JOIN nompersonal as np ON sc.cedula=np.cedula
  LEFT JOIN nomnivel1 as nm ON nm.codorg=np.codnivel1
  LEFT JOIN solicitudes_tipos as st ON st.id_solicitudes_tipos=sc.id_tipo_caso 
  WHERE $add TRUE limit $iDisplayLength offset $iDisplayStart";
  $res = $conexion->query($sql);
  $res2 = $conexion->query($sql2);
  $total=mysqli_fetch_array($res2);
  //-------------------------------------------------
  $row_cnt = mysqli_num_rows($res);
  //-------------------------------------------------
  // $iTotalRecords   = $row_cnt;
  
  $records         = array();
  $records["data"] = []; 

  // $end             = $iDisplayStart + $iDisplayLength;
  // $end             = $end > $iTotalRecords ? $iTotalRecords : $end;

  //$id=1;
  while ($fila=mysqli_fetch_array($res)) {
    $records["data"][] = array(
    
     (($fila['id_solicitudes_casos']!="0") ? $fila['id_solicitudes_casos'] : $fila['id_solicitudes_casos']),
     (($fila['cedula']!="0") ? $fila['cedula'] : $fila['cedula'] ),
     (($fila['tipo']!="0") ? $fila['tipo'] : $fila['tipo'] ),
     (($fila['status']!="0") ? $fila['status'] : $fila['status'] ),
     (($fila['departamento']!="0") ? $fila['departamento'] : $fila['departamento'] ),
     (($fila['fecha_registro']!="0") ? $fila['fecha_registro'] : $fila['fecha_registro'] ),
     (($fila['observacion_jefe']!="0") ? $fila['observacion_jefe'] : $fila['observacion_jefe'] ),      
     );
    // $id++;
  }
  // $iTotalRecords = $id;

  // if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
  //   $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
  //   $records["customActionMessage"] = "El grupo de acciónes se ha completado con éxito. ¡Bien hecho!";
  // }

  // $records["draw"] = $sEcho;
  // $records["recordsTotal"] = $iTotalRecords;
  // $records["recordsFiltered"] = $iTotalRecords;
  $records["total"]= $records["iTotalRecords"]= $records["iTotalDisplayRecords"]= $total['total'];

  echo json_encode($records);
?>