<?php
  //-------------------------------------------------
  session_start();  
  require_once('../../lib/database.php'); 
  $db = new Database($_SESSION['bd']);  
 //-------------------------------------------------
  $buscar        = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value']:NULL;
  $dir           = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir']:NULL;
  $column        = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column']:NULL;
  $edad1         = isset($_REQUEST['edad1']) ? $_REQUEST['edad1'] : NULL ;
  $edad2         = isset($_REQUEST['edad2']) ? $_REQUEST['edad2'] : NULL ;
  $estado_civil  = isset($_REQUEST['estado_civil']) ? $_REQUEST['estado_civil'] : NULL ;
  $genero        = isset($_REQUEST['genero']) ? $_REQUEST['genero'] : NULL ;
  $cod_profesion = isset($_REQUEST['cod_profesion']) ? $_REQUEST['cod_profesion'] : NULL ;
  $gradInst      = isset($_REQUEST['gradInst']) ? $_REQUEST['gradInst'] : NULL ;
  $areaDesem     = isset($_REQUEST['areaDesem']) ? $_REQUEST['areaDesem'] : NULL ;
  $a_exp         = isset($_REQUEST['a_exp']) ? $_REQUEST['a_exp'] : NULL ;
  //-------------------------------------------------
  $sql_columnas = "SELECT *,date_format(eleg.fecnac,'%d-%m-%Y') fecha_nac,prof.descrip as profesion,ins.descripcion as instruccion, des.descripcion as area_des ";

  $sql_FROM = " FROM nomelegibles eleg
  LEFT JOIN nomdesempeno des on (eleg.area_desempeno = des.codigo)
  LEFT JOIN nominstruccion ins on (eleg.grado_instruccion = ins.codigo)
  LEFT JOIN nomprofesiones prof on (eleg.cod_profesion = prof.codorg)";

  $sql_WHERE = " WHERE cedula <> '' ";
  $fragmento_sql = "";

  if ($estado_civil!="" && $estado_civil!=0 && $estado_civil!=NULL) {
      $fragmento_sql.=" AND eleg.estado_civil like '%".$estado_civil."%' ";
  }
  if ($genero!="" && $genero!=0 && $genero!=NULL) {
      $fragmento_sql.=" AND eleg.sexo like '%".$genero."%' ";
  }
  if ($apellidos!="" && $apellidos!=0 && $apellidos!=NULL) {
      $fragmento_sql.=" AND eleg.apenom like '%".$apellidos."%' ";
  }
  if ($funcion!="" && $funcion!=0 && $funcion!=NULL) {
      $fragmento_sql.=" AND eleg.nf.nomfuncion_id like '%".$funcion."%' ";
  }
  if ($cargo!="" && $cargo!=0 && $cargo!=NULL) {
      $fragmento_sql.=" AND eleg.nc.cod_cargo like '%".$cargo."%' ";
  }
  if ($genero!="" && $genero!=0 && $genero!=NULL) {
      $fragmento_sql.=" AND sexo like '%".$genero."%'";
  }

  
  $sql_LIMIT = " LIMIT ".$_REQUEST['length']." OFFSET ".$_REQUEST['start']."";
  
  $sql_WHERE .= $fragmento_sql;
  $sql       .= $sql_columnas;
  $sql       .= $sql_FROM;
  $sql       .= $sql_WHERE;
  $sql       .= $sql_LIMIT;
	//echo $sql,"<br>";exit(1);


  $res       = $db->query($sql);

  $sql2       .= $sql_columnas;
  $sql2       .= $sql_FROM;
  $sql2       .= $sql_WHERE;
  $res2       = $db->query($sql2);
  $row_cnt   = $res2->num_rows;

  //-------------------------------------------------
  $iTotalRecords  = $row_cnt;
  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
  $iDisplayStart  = intval($_REQUEST['start']);
  $sEcho          = intval($_REQUEST['draw']);
  
  $records = array();
  $records["data"] = array(); 

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

  $id=1;
  while ($fila=$res->fetch_assoc()) {

    $records["data"][] = array(
      ( is_null($fila['cedula']) ? "-" : $fila['cedula'] ),
      ( is_null($fila['nombres']) ? "-" : $fila['nombres'] ),
      ( is_null($fila['apellidos']) ? "-" : $fila['apellidos'] ),
      ( is_null($fila['fecha_nac']) ? "-" : $fila['fecha_nac'] ),
      ( is_null($fila['profesion']) ? "-" : $fila['profesion'] ),
      ( is_null($fila['area_des']) ? "-" : $fila['area_des']  ),
      ( is_null($fila['instruccion']) ? "-" : $fila['instruccion'] ),
      ( is_null($fila['anios_exp']) ? "-" : $fila['anios_exp'] ),
      ( is_null($fila['sexo']) ? "-" : $fila['sexo']   ),
      ( is_null($fila['telefono']) ? "-" : $fila['telefono'] ),
      ( is_null($fila['email']) ? "-" : $fila['email'] )
    );
    $id++;
  }
  //$iTotalRecords = $id;

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "El grupo de acciónes se ha completado con éxito. ¡Bien hecho!"; // pass custom message(useful for getting status of group actions)
  }

  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;
  
  echo json_encode($records);
?>