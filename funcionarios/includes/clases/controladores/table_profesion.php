<?php
  /* 
   * Paging
   */
  include_once("../modelos/MySQL.php");
  
  include_once("../objetoConexion.php");
    //print_r($_REQUEST);
    //print_r($_REQUEST['order']);
    $buscar=$_REQUEST['search']['value'];
    $dir = $_REQUEST['order'][0]['dir'];
    $column = $_REQUEST['order'][0]['column'];
    //echo $dir." ".$column." ".$buscar;

   if ($buscar!='' AND $column == '1') 
   {
    $consulta2="
    SELECT *
    FROM nomprofesiones
    WHERE descrip LIKE  '%".$buscar."%' OR codorg='".$buscar."'
    ORDER BY descrip ".$dir;

    $consulta="
    SELECT COUNT(*) as REGISTROS_CONTADOS
    FROM nomprofesiones
    WHERE descrip LIKE  '%".$buscar."%' OR codorg='".$buscar."'
    ORDER BY descrip ".$dir;
  }
  elseif ($buscar!='' AND $column == '0') 
   {
    $consulta2="
    SELECT *
    FROM nomprofesiones
    WHERE descrip LIKE  '%".$buscar."%' OR codorg='".$buscar."
    ORDER BY codorg ".$dir;

    $consulta="
    SELECT COUNT(*) as REGISTROS_CONTADOS
    FROM nomprofesiones
    WHERE descrip LIKE  '%".$buscar."%' OR codorg='".$buscar."'        
    ORDER BY codorg ".$dir;

  }
  elseif ($buscar=='' AND $column == '0') 
  {
    $consulta="
    SELECT COUNT(*) as REGISTROS_CONTADOS
    FROM nomprofesiones ORDER BY codorg ".$dir;
    $consulta2="
    SELECT *
    FROM nomprofesiones ORDER BY codorg ".$dir;
  }
  elseif ($buscar=='' AND $column == '1') 
  {
    $consulta="
    SELECT COUNT(*) as REGISTROS_CONTADOS
    FROM nomprofesiones ORDER BY descrip ".$dir;
    $consulta2="
    SELECT *
    FROM nomprofesiones ORDER BY descrip ".$dir;
  }

  $objConexion->ejecutarQuery($consulta);
  $datos = $objConexion->getMatrizCompleta();
  $iTotalRecords = $datos[0]['REGISTROS_CONTADOS']; 

  $iDisplayLength = intval($_REQUEST['length']);
  $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
  $iDisplayStart = intval($_REQUEST['start']);

  $sEcho = intval($_REQUEST['draw']);
  
  $records = array();
  $records["data"] = array(); 

  $end = $iDisplayStart + $iDisplayLength;
  $end = $end > $iTotalRecords ? $iTotalRecords : $end;

 
  

    $objConexion->ejecutarQuery($consulta2);
    $datos2 = $objConexion->getMatrizCompleta();

  for($i = $iDisplayStart; $i < $end; $i++) {


    $id = ($i + 1);
    $records["data"][] = array(
      $datos2[$i]['codorg'],
      $datos2[$i]['descrip'],

      "<a id='editar' href='profesion-edit.php?codorg=".$datos2[$i]['codorg']."&descrip=".$datos2[$i]['descrip']."'>
            <div class='glyphicon glyphicon-edit' aria-hidden='true'></div>
          </a>

          <div id='eliminar' class='glyphicon glyphicon-trash' aria-hidden='true'></div>"
   );

  }

  if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
  }
  $records["draw"] = $sEcho;
  $records["recordsTotal"] = $iTotalRecords;
  $records["recordsFiltered"] = $iTotalRecords;
  
  echo json_encode($records);
?>