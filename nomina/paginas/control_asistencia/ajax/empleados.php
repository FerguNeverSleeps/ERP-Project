<?php
    session_start();
    $ruta = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
    $ruta = str_replace('\\', '/', $ruta);
    require_once $ruta.'/generalp.config.inc.php';
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */

    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array( 'apenom', 'cedula', 'ficha','numero_marcar','dpto', 'dpto_id');

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "personal_id";

    /* DB table to use */
    $sTable = "infomarcaciones";

    /* Database connection information */
    $db_user   = DB_USUARIO;
    $db_pass   = DB_CLAVE;
    $db_name   = $_SESSION['bd'];
    $db_host   = DB_HOST;


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
    error_reporting(E_ALL);
    /*
     * MySQL connection
     */
    
    $id_usuario=$_SESSION['id_usuario'];
    
    $conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
        die( 'Could not open connection to server' );

    mysqli_query($conexion, 'SET CHARACTER SET utf8');

    /*
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
            intval( $_GET['iDisplayLength'] );
    }


    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
                    ($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
            }
        }

        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }

    $sWhere = " WHERE 1 ";
    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    //if (isset($_SESSION['ver_regiones'])) {
       // $sWhere = "WHERE tipnom IN 
       //         (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
    /*}else{
        $sWhere = "WHERE codnivel1 =".$_SESSION['region']." AND tipnom IN 
                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
    }*/
    
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysqli_real_escape_string($conexion, $_GET['sSearch'])."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        //if (isset($_SESSION['ver_regiones'])) {
           // $sWhere .= ") AND tipnom IN 
           //     (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
        /*}else{
            $sWhere .= ") AND codnivel1 =".$_SESSION['region']." AND tipnom IN 
                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
        }*/
        $sWhere .= " ) ";
    }

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysqli_real_escape_string($conexion, $_GET['sSearch_'.$i])."%' ";
        }
    }

    //VERIFICAR SI TIENE ACCESO A VARIOS NIVELES
//    if( isset($_SESSION['acceso_dir']) AND $_SESSION['acceso_dir']==0 )
//    {
//        $sWhere .= " AND codnivel1 = ".$_SESSION['region']." ";
//    }
//    elseif( isset($_SESSION['acceso_dir']) AND $_SESSION['acceso_dir']==1 )
//    {
//        if( isset($_SESSION['acceso_dep']) AND $_SESSION['acceso_dep']==0 )
//        {
//            $sWhere .= " AND IdDepartamento = ".$_SESSION['departamento']." ";
//        }
//    }
    
 /*   if( $_SESSION['acceso_dep']==0 && $_SESSION['departamento']!=0)
    {
        $sWhere .= " AND dpto_id = ".$_SESSION['departamento']."";
        
    }
    
    if( $_SESSION['acceso_dep']==1)
    {
        $sWhere .= " AND dpto_id IN (SELECT  id_departamento from usuario_departamento WHERE id_usuario=".$id_usuario.")";
        
    }
    
    //VERIFICAR SI TIENE ACCESO A VARIOS NIVELES
    $dpto_id = ( ( isset( $_GET['dpto_id'] ) ) ? $_GET['dpto_id'] : "all" );
    if( isset($dpto_id) AND $dpto_id=="all" )
    {
        $sWhereDpto = " ";
    }else
    {
        $sWhereDpto =" AND dpto_id = '$dpto_id' ";
    }
    */
    /*
     * SQL queries
     * Get data to display
     */
    //$sWhere = " WHERE 1 ";
    $sWhereDpto = " ";
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS personal_id, `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
        FROM   $sTable
        $sWhere
        AND estado NOT LIKE '%Baja%'
        $sWhereDpto
        $sOrder
        $sLimit
        ";
//    echo $sQuery;   
    $rResult = mysqli_query( $conexion, $sQuery ) or die(mysqli_error($conexion));    
    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = mysqli_query( $conexion, $sQuery ) or die(mysqli_error($conexion));
    $aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];

    /* Total data set length */
    $sQuery = "
        SELECT COUNT(`".$sIndexColumn."`)
        FROM   $sTable
    ";
    $rResultTotal = mysqli_query( $conexion, $sQuery ) or die(mysql_error($conexion));
    $aResultTotal = mysqli_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];


    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']),
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
    while ( $aRow = mysqli_fetch_array( $rResult ) )
    {
        $row = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if($aColumns[$i]=='apenom'){
                $caracter="<br>";
                $apenom = wordwrap($aRow[ $aColumns[$i] ], 30, $caracter, false);
                $row[] = $apenom;
            }
            else if($aColumns[$i]=='dpto'){
                $caracter="<br>";
                $departamento = wordwrap($aRow[ $aColumns[$i] ], 30, $caracter, false);
                $row[] = $departamento;
            }
            else
                $row[] = $aRow[ $aColumns[$i] ];
        }
        //==============================================================================================================
        // Permitir ver_resumen_caa
        if( isset($_SESSION['ver_resumen_caa']) )
        {
            $row[] = "<a class='btn btn-primary' href='resumen_asistencias.php?ficha=".$aRow['ficha']."' title='Detalles de Asistencia'>
            <i class='fa fa-pie-chart' aria-hidden='true'></i>
            </a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir ver_rpt_individual_caa
       // if( isset($_SESSION['ver_rpt_individual_caa']) )
       // {
            $row[] = "<button type='button' class='btn btn-success' title='Reporte de Asistencias' data-toggle='modal' data-target='#exampleModal2' data-ficha='".$aRow['ficha']."'>
            <i class='fa fa-print' aria-hidden='true'></i></button>";
       /* }else{
            $row[] = "";
        }*/
        //==============================================================================================================
        // Permitir ver_rpt_marcaciones_personales_caa
        if( isset($_SESSION['ver_rpt_marcaciones_personales_caa']) )
        {
            $row[] = "<button type='button' class='btn btn-primary' title='Reporte de Marcaciones' data-toggle='modal' data-target='#exampleModal3' data-ficha='".$aRow['ficha']."'><i class='fa fa-print' aria-hidden='true'></i></button>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir ver_calendario_personal_caa
        if( isset($_SESSION['ver_calendario_personal_caa']) )
        {
            $row[] = "<a class='btn btn-primary' href='../calendario_personal_asistencias.php?anio=".date('Y')."&ficha=".$aRow['ficha']."' title='Calendario de personal'>
            <i class='fa fa-calendar' aria-hidden='true'></i>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        $output['aaData'][] = $row;
    }

    //print_r($output['aaData']);
    echo json_encode( $output );
?>
