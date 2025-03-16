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
    $aColumns = array('foto', 'descrip', 'ficha', 'cedula', 'apenom', 'fecha_ingreso', 'estado', 'Descripcion', 'nomposicion_id');

    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "personal_id";

    /* DB table to use */
    $sTable = "nomvis_integrantes";

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


    /*
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    //if (isset($_SESSION['ver_regiones'])) {
        $sWhere = "WHERE tipnom IN 
                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
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
            $sWhere .= ") AND tipnom IN 
                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
        /*}else{
            $sWhere .= ") AND codnivel1 =".$_SESSION['region']." AND tipnom IN 
                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=".$id_usuario." AND  acceso=1)";
        }*/
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


    /*
     * SQL queries
     * Get data to display
     */
    
    //$aColumns = array('foto', 'descrip', 'ficha', 'cedula', 'apenom', 'estado', 'nomposicion_id');
//    $sQuery = "SELECT i.descrip as descrip, i.personal_id as personal_id, i.ficha as ficha, i.cedula as cedula, i.apenom as apenom, i.estado as estado,
//               i.nomposicion_id as nomposicion_id,i.tipnom as tipnom
//		FROM   nomvis_integrantes i
//                WHERE  i.tipnom IN 
//                (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=$id_usuario AND  acceso=1)"
//                . "$sOrder
//                   $sLimit";
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS personal_id, `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
        ";
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
            if($aColumns[$i]=='foto')
            {
                $dir = dirname(dirname(dirname(__FILE__)));
                $dir = str_replace('\\', '/', $dir);

                $foto = $aRow[ $aColumns[$i] ];
                $foto_dir = $dir . '/'. $foto;

                //echo "Foto = " . $foto_dir . " file_exists = " . file_exists($foto_dir) . "<br>";

                $foto = ( $foto!=''  && file_exists($foto_dir) ) ? '../'.$foto : '../fotos/silueta.gif' ;

                $row[] = "<img width=\"48\" height=\"48\" src=\"".$foto."\" />";
            }
            else
                $row[] = $aRow[ $aColumns[$i] ];
        }
        
        //==============================================================================================================
        // Permitir editar si el usuario tiene acceso

        if($_SESSION['acceso_editar'] == 1)
        {
            $row[] = "<a href=\"../ag_integrantes2.php?ficha=".$aRow['ficha']."&edit&back_listado_integrantes_contraloria\" title=\"Editar\">" .
                 "<img src=\"../../../includes/imagenes/icons/pencil.png\" alt=\"Editar\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir imprimir si el usuario tiene acceso

        if($_SESSION['acceso_imprimir'] == 1)
        {
            $row[] = "<a rel=\"../../../nomina/fpdf/datos_personal.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" rel2=\"../../../reportes/pdf/datos_personal2.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" name=\"../../../reportes/excel/excel_datos_personal.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" class=\"modalreporte\" href=\"#\" title=\"Imprimir\">" .
                 "<img src=\"../../../includes/imagenes/icons/printer.png\" alt=\"Imprimir\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir cargas familiares si el usuario tiene acceso

        if($_SESSION['acceso_c_familiares'] == 1)
        {
            $row[] = "<a href=\"../familiares.php?cedula=".$aRow['cedula']."&txtficha=".$aRow['ficha']."\" title=\"Cargas Familiares\">" .
                 "<img src=\"../../../includes/imagenes/icons/group.png\" alt=\"Cargas Familiares\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir ver expedientes si el usuario tiene acceso

        if($_SESSION['acceso_expedientes'] == 1)
        {
            $row[] = "<a href=\"../../expediente/expediente_list.php?cedula=".$aRow['cedula']."\" title=\"Ver Expediente\">" .
                 "<img src=\"../../../includes/imagenes/icons/folder_page.png\" alt=\"Ver Expediente\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        //==============================================================================================================
        // Permitir ver adjuntos si el usuario tiene acceso

        if($_SESSION['acceso_expedientes'] == 1)
        {
            $row[] = "<a href=\"../../expediente/navegador_archivos/index.php?nombre=".$aRow['apenom']."&ci=".$aRow['cedula']."\" title=\"Ver Adjuntos\">" .
                 "<img src=\"../../../includes/imagenes/icons/briefcase.png\" alt=\"Ver Expediente\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        
        // Permitir contraloria si el usuario tiene acceso

        if($_SESSION['acceso_contraloria'] == 1)
        {
            $row[] = "<a href=\"../../../reporte_pub/colaborador.php?id=".$aRow['personal_id']."\" title=\"Movimientos Contraloría\">" .
                "<img src=\"../images/contraloria.png\" alt=\"Movimientos Contraloría\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================
        // Permitir dejar sin efecto a un colaborador si el usuario tiene acceso

        if($_SESSION['acceso_s_efecto'] == 1)
        {
            $row[] = "<a href=\"javascript:enviar(".$aRow['personal_id'].");\" title=\"Dejar sin Efecto\">" .
                 "<img src=\"../images/b_drop.png\" alt=\"Movimientos Contraloría\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        //==============================================================================================================

        $output['aaData'][] = $row;
    }

    echo json_encode( $output );
?>
