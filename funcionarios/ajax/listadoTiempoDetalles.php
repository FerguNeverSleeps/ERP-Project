<?php
  //-------------------------------------------------
  session_start();  
  require_once('../../lib/database.php'); 
  $db = new Database($_SESSION['bd']);  
 //-------------------------------------------------
    $buscar    = isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value']:NULL;
    $dir       = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir']:NULL;
    $column    = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column']:NULL;
    $cedula   = isset($_REQUEST['cedula']) ? $_REQUEST['cedula'] : NULL ;
    $tipo      = isset($_REQUEST['tipo']) ? $_REQUEST['tipo'] : NULL ;
  //-------------------------------------------------
    //$sql = "SELECT * FROM departamento";

  $sql_columnas = "SELECT A.*,B.descripcion as justificacion, date_format(A.fecha,'%d/%b/%Y') fecha2";

  $sql_FROM = " FROM dias_incapacidad as A 
        LEFT JOIN tipo_justificacion AS B ON A.tipo_justificacion = B.idtipo ";

  $sql_WHERE = " WHERE A.cedula='{$cedula}' AND A.tipo_justificacion='{$tipo}' ";
  
  if ($buscar != "") {
      $fragmento_sql=" AND A.observacion like '%".$buscar."%' ";
  }
  
  $fragmento_sql.= " ORDER BY A.fecha DESC ";

  
//  if ($dir!= "") {
//      $fragmento_sql .= " ORDER BY A.fecha ".$dir;
//  }
 
  
  $sql_LIMIT = " LIMIT ".$_REQUEST['start'].",".$_REQUEST['length']."";
  
  $sql_WHERE .= $fragmento_sql;
  $sql       .= $sql_columnas;
  $sql       .= $sql_FROM;
  $sql       .= $sql_WHERE;
  $sql       .= $sql_LIMIT;
    // echo $sql;
//echo $sql,"<br>";exit;

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
  while ($fila=$res->fetch_assoc()) 
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
    $opcion = "<a href=\"javascript:enviar(".$aRow['personal_id'].");\" title=\"Eliminar\">" .
                 "<img src=\"../includes/imagenes/delete.png\" alt=\"Eliminar\" width=\"16\" height=\"16\"></a>";
    $records["data"][] = array(
      ( is_null($fila['fecha']) ? "-" : $fila['fecha2'] ),
      ( is_null($fila['tiempo']) ? "-" : number_format($fila['tiempo'], 2) ),
      ( is_null($fila['observacion']) ? "-" : $fila['observacion']),
      ( $dias),
      ( $horas),
      ( $minutos),
      ( $opcion )
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