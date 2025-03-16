<?php
session_start();
ob_start();
require_once "../../config/db.php";
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
    
    //$db_host, $db_user, $db_pass, $db_name
    $conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );

    mysqli_query($conexion, 'SET CHARACTER SET utf8');

    $buscar           = isset ( $_REQUEST['search']['value'] ) ? $_REQUEST['search']['value'] : NULL;
    $dir              = isset ( $_REQUEST['order'][0]['dir'] ) ? $_REQUEST['order'][0]['dir'] : NULL;
    $column           = isset ( $_REQUEST['order'][0]['column'] ) ? $_REQUEST['order'][0]['column'] : NULL;


    if($buscar != "")
    {
        $busqueda = " WHERE apenom LIKE '%$buscar%' OR cedula LIKE '%$buscar%' OR ficha LIKE '%$buscar%' OR nomposicion_id LIKE '%$buscar%' ORDER BY ficha ASC";
    }else{
        $busqueda = " ORDER BY ficha ASC";
    }

    //OR A.ApellidoMaterno LIKE '%".$buscar."%' OR A.PrimerNombre LIKE '%".$buscar."%'

    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */

    $sQuery = "SELECT personal_id, apenom, cedula, ficha, nomposicion_id FROM nompersonal";
        $sQuery .= $busqueda;

        //echo $sQuery,"<br>";
        $rResult = mysqli_query( $conexion, $sQuery ) or die(mysqli_error($conexion));
        //echo $sQuery;
        /* Data set length after filtering */

        $campos = array();
        while ($fila = mysqli_fetch_assoc($rResult))
        {
            $campos[] = $fila;
        }
    //$grupos           = $grupos -> listarGrupos();/* CONSULTA SQL */
    $TOTAL_REGISTROS  = count ( $campos );

    $LONGITUD_LISTADO = intval ( isset ( $_REQUEST['length'] ) ? $_REQUEST['length']:NULL);
    $LONGITUD_LISTADO = $LONGITUD_LISTADO < 0 ? $TOTAL_REGISTROS : $LONGITUD_LISTADO; 
    $iDisplayStart    = intval ( isset($_REQUEST['start'] ) ? $_REQUEST['start']:NULL);

    $sEcho            = intval ( $_REQUEST['draw'] );

    $records          = array();
    $records["data"]  = array();

    $FINAL            = $iDisplayStart + $LONGITUD_LISTADO;


    /* Se arma los datos del arreglo para transformalo en JSON hay que cambiar los datos del arreglo y adaptarlos con los resultados de la búsqueda además de lo que se desea mostrar en la pantalla*/
    
    //print_r($campos);
    for($i = $iDisplayStart; $i < $FINAL; $i++) 
    {
        if(isset($campos[$i]['personal_id']))
        {
            $cadena = '
            <a class="btn btn-primary" href="calendarios_personal.php?ficha='.$campos[$i]['personal_id'].'" title="Calendario de Personal">
            <i class="fa fa-calendar" aria-hidden="true"></i>
            </a>';
            $records["data"][] = array(
            $i+1,
            $campos[$i]['apenom'],
            $campos[$i]['cedula'],
            $campos[$i]['ficha'],
            $campos[$i]['nomposicion_id'],
            $cadena
           );
        }

    }

    if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") 
    {
     $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
     $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
    }
    $records["draw"]            = $sEcho;
    $records["recordsTotal"]    = $TOTAL_REGISTROS;
    $records["recordsFiltered"] = $TOTAL_REGISTROS;
    echo json_encode($records);

?>