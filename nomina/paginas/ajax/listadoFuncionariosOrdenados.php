<?php
  //-------------------------------------------------
  session_start();  
  require_once('../../lib/database.php'); 
  $db = new Database($_SESSION['bd']);  
 //-------------------------------------------------
    $buscar    = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value']:NULL;
    $dir       = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir']:NULL;
    $column    = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column']:NULL;
    $posicion  = isset($_REQUEST['posicion']) ? $_REQUEST['posicion'] : NULL ;
    $nombres   = isset($_REQUEST['nombre']) ? $_REQUEST['nombre'] : NULL ;
    $apellidos = isset($_REQUEST['apellido']) ? $_REQUEST['apellido'] : NULL ;
    $funcion   = isset($_REQUEST['funcion']) ? $_REQUEST['funcion'] : NULL ;
    $cargo     = isset($_REQUEST['cargo']) ? $_REQUEST['cargo'] : NULL ;
    $genero    = isset($_REQUEST['genero']) ? $_REQUEST['genero'] : NULL ;
    $externo   = isset($_REQUEST['externo']) ? $_REQUEST['externo'] : NULL ;
    $tipo      = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : NULL ;
    $promocion = isset($_REQUEST['promocion']) ? $_REQUEST['promocion'] : NULL ;
  //-------------------------------------------------
  $sql_columnas = "SELECT np.personal_id, np.cedula, np.apenom, np.nomposicion_id,np.seguro_social, np.fecnac,np.fecing,np.sexo, pos.sueldo_1, 
  (pos.sueldo_1 - pos.gastos_representacion) as sueldo_total, nc.des_car as cargo, nf.descripcion_funcion as funcion, 
  nivel1.descrip as departamento,instruc.descripcion as grado_instruccion,  np.personal_externo, prom.descripcion as promocion";

  $sql_FROM = " FROM nompersonal as np LEFT JOIN nomposicion as pos ON (np.nomposicion_id=pos.nomposicion_id)
  LEFT JOIN nomcargos as nc ON (nc.cod_cargo = np.codcargo)
  LEFT JOIN nomfuncion as nf ON (np.nomfuncion_id = nf.nomfuncion_id)
  LEFT JOIN nomnivel1 as nivel1 ON (nivel1.codorg = np.codnivel1)
  LEFT JOIN nominstruccion as instruc ON (instruc.codigo = np.IdNivelEducativo)
  LEFT JOIN promocion as prom ON (prom.id = np.id_promo) ";

  $sql_WHERE = " WHERE np.estado <> 'De Baja' ";
  $fragmento_sql = "";

  if (isset($_REQUEST['posicion'])) {
      $fragmento_sql.=" OR np.nomposicion_id like '%".$posicion."%' ";
  }
  if (isset($_REQUEST['nombre'])) {
      $fragmento_sql.=" OR np.apenom like '%".$nombres."%' ";
  }
  if (isset($_REQUEST['apellido'])) {
      $fragmento_sql.=" OR np.apenom like '%".$apellidos."%' ";
  }
  if (isset($_REQUEST['funcion'])) {
      $fragmento_sql.=" OR nf.nomfuncion_id like '%".$funcion."%' ";
  }
  if (isset($_REQUEST['cargo'])) {
      $fragmento_sql.=" OR nc.cod_cargo like '%".$cargo."%' ";
  }
  if (isset($_REQUEST['genero'])) {
      $fragmento_sql.=" OR np.sexo like '%".$genero."%'";
  }
  if (isset($_REQUEST['promocion'])) {
      $fragmento_sql.=" OR prom.id like '%".$promocion."%'";
  }
  
  $sql_LIMIT = " LIMIT ".$_REQUEST['start'].",".$_REQUEST['length']."";
  
  $sql_WHERE .= $fragmento_sql;
  $sql       .= $sql_columnas;
  $sql       .= $sql_FROM;
  $sql       .= $sql_WHERE;
  $sql       .= $sql_LIMIT;

//echo $sql,"<br>";exit(1);

  $res       = $db->query($sql);
  $row_cnt   = $res->num_rows;

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
      ( is_null($fila['apenom']) ? "-" : $fila['apenom'] ),
      ( is_null($fila['fecnac']) ? "-" : $fila['fecnac'] ),
      ( is_null($fila['nomposicion_id']) ? "-" : $fila['nomposicion_id'] ),
      ( is_null($fila['fecing']) ? "-" : $fila['fecing'] ),
      ( is_null($fila['funcion']) ? "-" : $fila['funcion']  ),
      ( is_null($fila['sueldo_total']) ? "-" : $fila['sueldo_total'] ),
      ( is_null($fila['sexo']) ? "-" : $fila['sexo']   ),
      ( is_null($fila['departamento']) ? "-" : $fila['departamento']   ),
      ( is_null($fila['grado_instruccion']) ? "-" : $fila['grado_instruccion'] ),
      ( is_null($fila['promocion']) ? "-" : $fila['promocion'] ),
      ( "-" ),
      ( "-" ),
      ( is_null($fila['estado']) ? "-" : $fila['estado'] ),
      ( is_null($fila['tipo']) ? "-" : $fila['tipo'] ),
      ( is_null($fila['direcion']) ? "-" : $fila['direcion'] )
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