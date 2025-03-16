<?php
    $ruta         = dirname(dirname(dirname(dirname(__FILE__))));
    $ruta         = str_replace('\\', '/', $ruta);
    require_once $ruta.'/generalp.config.inc.php';
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
    * Easy set variables
    */
    
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
    * you want to insert a non-database field (for example a counter or static image)
    */
    $aColumns     = array( 'nomposicion_id',   'sueldo_propuesto', 'sueldo_anual',
    'partida', 'gastos_representacion','cargo_id');
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "nomposicion_id";
    
    /* DB table to use */
    $sTable       = "nomposicion";
    
    /* Database connection information */
    $db_user      = DB_USUARIO;
    $db_pass      = DB_CLAVE;
    $db_name      = $_SESSION['bd'];
    $db_host      = DB_HOST;
    
    $conexion     =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
    die( 'Could not open connection to server' );      
   // echo json_encode( $output );

  /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
    error_reporting(E_ALL);
    /* 
     * MySQL connection
     */
    $conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
    die( 'Could not open connection to server' );    
    $buscar   =isset($_REQUEST['search']['value']) ? $_REQUEST['search']['value']:NULL;
    $dir      = isset($_REQUEST['order'][0]['dir']) ? $_REQUEST['order'][0]['dir']:NULL;
    $column   = isset($_REQUEST['order'][0]['column']) ? $_REQUEST['order'][0]['column']:NULL;
    mysqli_query($conexion, 'SET CHARACTER SET utf8');

    if($buscar!='')
    {
        $sWhere="WHERE  nomposicion_id like '%".$buscar."%'";
    }
    else
    {
        $sWhere='';
    }

    if ($dir=='asc') {
        $sOrder="ORDER BY nomposicion_id ASC";
    }
    else
    {
            $sOrder="ORDER BY nomposicion_id DESC";

    }
    /* SQL queries
     * Get data to display
     */
    $sQuery          = "
    SELECT nomposicion_id,   sueldo_propuesto, sueldo_anual, partida, gastos_representacion,cargo_id 
    FROM $sTable
    $sWhere
    $sOrder
    "; 
        $rResultTotal                          = mysqli_query( $conexion, $sQuery ) or die(mysql_error($conexion));
        $posicion=array();
        while($posiciones= mysqli_fetch_assoc($rResultTotal)){
            $posicion[]=$posiciones;
        }
    
    $iTotalRecords   = count($posicion); 
    
    
    $iDisplayLength  = intval(isset($_REQUEST['length']) ? $_REQUEST['length']:NULL);
    $iDisplayLength  = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength; 
    $iDisplayStart   = intval(isset($_REQUEST['start']) ? $_REQUEST['start']:NULL);
    
    
    $sEcho           = intval($_REQUEST['draw']);
    
    $records         = array();
    $records["data"] = array(); 
    
    $end             = $iDisplayStart + $iDisplayLength;
    $end             = $end > $iTotalRecords ? $iTotalRecords : $end;
  //echo $end," ",$iDisplayLength," ",$iDisplayStart,"<br>";

  //print_r($posicion);

    for($i = $iDisplayStart; $i < $end; $i++) 
    {
        $id = ($i + 1);
        $records["data"][] = array(
        $posicion[$i]["nomposicion_id"],
               
        $posicion[$i]["sueldo_propuesto"],
        $posicion[$i]["sueldo_anual"],
        $posicion[$i]["partida"],
        $posicion[$i]["gastos_representacion"],
        $posicion[$i]["cargo_id"]
        ,' <a href="ag_maestro_posicion.php?edit&id='.$posicion[$i]["nomposicion_id"].'" title="Editar">
        <img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>',
        '<a href="javascript:enviar(3,'.$posicion[$i]["nomposicion_id"].');" title="Eliminar">
        <img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>'
        );

    }
    $records["draw"]            = $sEcho;
    $records["recordsTotal"]    = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;
    echo json_encode( $records );
/*    $output = array(
        "draw" => intval($_GET['sEcho']),
        "recordsTotal" => $iTotal,
        "recordsFiltered" => $iFilteredTotal,
        "aaData" => array()
    );
    
    while ( $aRow = mysqli_fetch_array( $rResult ) )
    {
        $row = array(); 

        $row[] = $aRow['nomposicion_id'];

        $row[] = $aRow['descripcion_posicion'];

        $row[] = $aRow['sueldo_propuesto'];

        $row[] = $aRow['sueldo_anual'];

        $row[] = $aRow['partida'];

        $row[] = $aRow['gastos_representacion'];

        $row[] = $aRow['paga_gr'];

        $row[] =  '<a href="ag_maestro_posicion.php?edit&id='.$aRow['nomposicion_id'].'" title="Editar">
                        <img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>';

        $row[] ='<a href="javascript:enviar(3,'.$aRow['nomposicion_id'].');" title="Eliminar">
                                              <img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>';

        $output['aaData'][] = $row;
    }*/
    
   // echo json_encode( $output );
?>