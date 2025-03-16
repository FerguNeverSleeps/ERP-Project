<?php
    $ruta = dirname(dirname(dirname(dirname(__FILE__))));
    $ruta = str_replace('\\', '/', $ruta);
    require_once $ruta.'/generalp.config.inc.php';
    require_once('../../lib/database.php');

    $db = new Database($_SESSION['bd']);
    $sql = "SELECT acceso_sueldo FROM ".SELECTRA_CONF_PYME.".nomusuarios WHERE login_usuario='".$_SESSION['usuario']."'";
    $res = $db->query($sql);
    $usuario = $res->fetch_object();
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    if($usuario->acceso_sueldo==1)
    {
   
        $aColumns = array( 'foto', 'ficha', 'cedula', 'apenom', 'estado',  'sueldo', 'fecha_ingreso');
    }
    
    if($usuario->acceso_sueldo==0)
    {
   
        $aColumns = array( 'foto', 'ficha', 'cedula', 'apenom', 'estado', 'fecha_ingreso');
    }
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "personal_id";
    
    /* DB table to use */
    $sTable = "nomvis_integrantes";
    
    /* Database connection information */
    $db_user  = DB_USUARIO;
    $db_pass  = DB_CLAVE;
    $db_name  = $_SESSION['bd'];
    $db_host  = DB_HOST;
    
    
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

    mysqli_query($conexion, 'SET CHARACTER SET utf8');

    // Consultar primero el tipo de empresa
    $res = mysqli_fetch_array(mysqli_query($conexion, "SELECT tipo_empresa FROM nomempresa"), MYSQLI_ASSOC);
    $tipo_empresa = $res['tipo_empresa'];
    
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
    $sWhere = "WHERE descrip = '{$_SESSION['nomina']}' ";
    if(isset($_SESSION['region']) AND $_SESSION['region']!= '0')
    {
        $sWhere .= "AND codnivel1 = '{$_SESSION['region']}' ";
    }
    if(isset($_SESSION['departamento']) AND $_SESSION['departamento']!= '0')
    {
        $sWhere .= "AND codnivel2 = '{$_SESSION['departamento']}' ";
    }
    if(isset($_SESSION['nivel3']) AND $_SESSION['nivel3']!= '0')
    {
        $sWhere .= "AND codnivel3 = '{$_SESSION['nivel3']}' ";
    }
    if(isset($_SESSION['nivel4']) AND $_SESSION['nivel4']!= '0')
    {
        $sWhere .= "AND codnivel4 = '{$_SESSION['nivel4']}' ";
    }
    if(isset($_SESSION['nivel5']) AND $_SESSION['nivel5']!= '0')
    {
        $sWhere .= "AND codnivel5 = '{$_SESSION['nivel5']}' ";
    }
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        $sWhere .= " AND (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            $sWhere .= "`".$aColumns[$i]."` LIKE '%".mysqli_real_escape_string($conexion, $_GET['sSearch'])."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
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
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS personal_id, `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
        "; //echo $sQuery;
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
                $dir = dirname(dirname(__FILE__));
                $dir = str_replace('\\', '/', $dir);

                $foto = $aRow[ $aColumns[$i] ];
                $foto_dir = $dir . '/'. $foto;

                //echo "Foto = " . $foto_dir . " file_exists = " . file_exists($foto_dir) . "<br>";

                // Si existe la foto del usuario se muestra esa. De lo contrario se muestra la foto por defecto
                $foto = ( $foto!=''  && file_exists($foto_dir) ) ? $foto : 'fotos/silueta.gif' ;

                $row[] = "<img width=\"48\" height=\"48\" src=\"".$foto."\" />";
            }
            else if($aColumns[$i]=='nomposicion_id')
            {
                if($tipo_empresa==1) $row[] = $aRow[ $aColumns[$i] ]; 
            }
            else if($aColumns[$i]=='apenom'){
                $caracter="<br>";
                $apenom = wordwrap($aRow[ $aColumns[$i] ], 15, $caracter, false);
                $row[] = $apenom;
            }
            else
                $row[] = $aRow[ $aColumns[$i] ]; 
        }
        
         $row[] = "<a href=\"ver_integrantes.php?ficha=".$aRow['ficha']."&edit\" title=\"Ver\">" .
                 "<img src=\"../../includes/imagenes/icons/magnifier.png\" alt=\"Ver\" width=\"16\" height=\"16\"></a>";
        
        if($_SESSION['acceso_editar'] == 1)
        {
                $row[] = "<a href=\"ag_integrantes.php?ficha=".$aRow['ficha']."&edit\" title=\"Editar\">" . 
                 "<img src=\"../../includes/imagenes/icons/pencil.png\" alt=\"Editar\" width=\"16\" height=\"16\"></a>";
        }else
        {
            $row[] = "";
        }
        
        if($_SESSION['acceso_calendarios'] == 1)
        {
                 $row[] = "<a href=\"calendarios_personal.php?ficha=".$aRow['ficha']."&anio\" title=\"Ver Calendario\">" . 
                 "<img src=\"../../includes/imagenes/icons/calendar.png\" width=\"16\" height=\"16\"></a>";

        }else
        {
            $row[] = "";
        }
        
       
        if($_SESSION['acceso_c_familiares'] == 1)
        {    
            $row[] = "<a href=\"familiares.php?cedula=".$aRow['cedula']."&txtficha=".$aRow['ficha']."\" title=\"Cargas Familiares\">" . 
                 "<img src=\"../../includes/imagenes/icons/group.png\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        
        if($_SESSION['acceso_editar'] == 1)
        {
                 $row[] = "<a href=\"otrosdatos_integrantes.php?txtficha=".$aRow['ficha']."\" title=\"Campos Adicionales\">" . 
                 "<img src=\"../../includes/imagenes/icons/table_multiple.png\" width=\"16\" height=\"16\"></a>";
        
        }else
        {
            $row[] = "";
        }
        
            
        if($_SESSION['acceso_imprimir'] == 1)
        {  
            $row[] = "<a rel=\"../../nomina/fpdf/datos_personal.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" rel2=\"../../reportes/pdf/datos_personal2.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" name=\"../../reportes/excel/excel_datos_personal.php?cedula=".$aRow['cedula']."&ficha=".$aRow['ficha']."\" class=\"modalreporte\" href=\"#\" title=\"Imprimir\">" .
                 "<img src=\"../../includes/imagenes/icons/printer.png\" alt=\"Imprimir\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
        
        if($_SESSION['acceso_expedientes'] == 1)
        {
            $row[] = "<a href=\"../expediente/expediente_list.php?cedula=".$aRow['cedula']."\" title=\"Ver Expediente\">" .
                 "<img src=\"../../includes/imagenes/icons/folder_page.png\" alt=\"Ver Expediente\" width=\"16\" height=\"16\"></a>";
         }else{
            $row[] = "";
        }
        //==============================================================================================================
        //==============================================================================================================
        // Permitir ver adjuntos si el usuario tiene acceso

        if($_SESSION['acceso_expedientes'] == 1)
        {
            $row[] = "<a href=\"../expediente/navegador_archivos/index.php?nombre=".$aRow['apenom']."&ci=".$aRow['cedula']."\" title=\"Ver Adjuntos\">" .
                 "<img src=\"../../includes/imagenes/icons/briefcase.png\" alt=\"Ver Expediente\" width=\"16\" height=\"16\"></a>";
        }else{
            $row[] = "";
        }
            
         //$row[] = "<a href=\"../../reporte_pub/colaborador.php?id=".$aRow['personal_id']."\" title=\"Movimientos Contraloría\">" .
          //       "<img src=\"../paginas/images/contraloria.png\" alt=\"Movimientos Contraloría\" width=\"16\" height=\"16\"></a>";         
        
        //==============================================================================================================
        // Permitir eliminar un colaborador si no está en ninguna planilla
        $sql = "SELECT COUNT(*) as contar FROM nom_movimientos_nomina WHERE ficha='{$aRow['ficha']}'";
        $res = mysqli_query($conexion, $sql);

        $registros = mysqli_fetch_object($res)->contar;

        if($registros==0)
        {
            $row[] = "<a href=\"javascript:enviar(3,".$aRow['personal_id'].");\"  title=\"Eliminar\" style=\"cursor: pointer\">" .
                     "<img src=\"../../includes/imagenes/icons/delete.png\" alt=\"Eliminar\" width=\"16\" height=\"16\"></a>"; 
        }
        else
        {
            $row[] = ""; //"<a title=\"Eliminar\" style=\"cursor: pointer\">" .
                         //"<img src=\"../../includes/imagenes/icons/delete.png\" alt=\"Eliminar\" width=\"16\" height=\"16\"></a>";            
        }
        //==============================================================================================================

        $output['aaData'][] = $row;
    }
    
    echo json_encode( $output );
?>