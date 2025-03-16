<?php
    $ruta = dirname(dirname(dirname(dirname(__FILE__))));
    $ruta = str_replace('\\', '/', $ruta);
    require_once $ruta.'/generalp.config.inc.php';
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
    
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array( 'imagen_cedula','cedula', 'ficha', 'nombres', 'apellidos', 'estado', 'descrip');
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "personal_id";
    
    /* DB table to use */
    $sTable = "nomvis_integrantes_documentos";
    
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
        FROM $sTable
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
    // REPORTES 1
    $sqlreporte1 = "SELECT codigo, nombre, tipo_documento, archivo FROM nomtipos_constancia
    WHERE visibilidad='1'";
    $resreporte1 = mysqli_query($conexion,$sqlreporte1);
    $i=0;
    while($rowreport1 = mysqli_fetch_array($resreporte1))
    {
           $codigo[$i]=$rowreport1['codigo'];
           $nombre[$i]=$rowreport1['nombre'];
           $tipo_d[$i]=$rowreport1['tipo_documento'];
           $archivo[$i]=$rowreport1['archivo'];
           $i++;
    }
    $totalreporte1=$i;
    $texto="<option value=''>Seleccione</option>";
    for($j=0;$j<$totalreporte1;$j++) 
    {
          $texto.= "<option value=".$codigo[$j]." data-id='".$tipo_d[$j]."' data-arc='".$archivo[$j]."'>".utf8_encode($nombre[$j])."</option>";
    }
    // FIN REPORTE 1
    //REPORTE 2
          $texto21 = "    <option value=''>Seleccione</option>
                          <option value='Nombramiento'>Nombramiento</option>
                          <option value='Resuelto'>Resuelto</option>
                          <option value='Resuelto_g'>Resuelto General</option> 
                          <option value='ajust_h'>Modelo de toma de ajuste (hombre)</option>
                          <option value='ajust_m'>Modelo de toma de ajuste (mujer)</option>
                          <option value='modelo_h'>Modelo de toma (hombre)</option>
                          <option value='modelo_m'>Modelo de toma (mujer)</option>
                          <option value='decreto'>Decreto</option>
                          <option value='decreto_g'>Decreto General</option>
                          <option value='expediente'>Expediente</option>";
            if ($_SESSION["nombre_empresa_nomina"]=="MMD Nuevo Grupo,S.A.") {
                $texto2 = "     <option value=''>Seleccione</option>   
                <option value='contrato_de_trabajo_administrativo'>Contrato de trabajo administrativo</option>
                <option value='contrato_de_trabajo_anfitrion'>Contrato de trabajo anfitrion</option>
                <option value='contrato_de_trabajo_asesor_de_ventas'>Contrato de trabajo asesor de ventas</option>
                <option value='contrato_de_trabajo_asistente_de_jefe_de_unidad'>Contrato de trabajo asistente de jefe de unidad</option>
                <option value='contrato_de_trabajo_jefe_de_unidad'>Contrato de jefe unidad</option>
                <option value='carta_certificacion_isr'>Carta Certificación ISR</option>
                <option value='carta_ficha_itesa'>Carta Ficha</option>
                <option value='carta_terminacion_obra'>Carta Terminacion Obra</option>
                <option value='carta_estado_gravidez'>Carta Estado de Gravidez</option>
                <option value='carta_de_maternidad'>Carta de Maternidad</option>
                <option value='expediente_asamblea'>Expediente</option>";
            } elseif($_SESSION["nombre_empresa_nomina"]=="DEMO PANAMA"){
                $texto2 = "     <option value=''>Seleccione</option>   
                <option value='contrato_de_trabajo_administrativo_pjv'>Contrato Administrativo</option>
                <option value='contrato_asistente_jefe_de_unidad_pjv'>Contrato de Asistentes de Jefes de Unidad</option>
                <option value='contrato_asesor_de_venta_pjv'>Contrato de Asesores de Ventas</option>
                <option value='contrato_jefe_de_unidad_pjv'>Contrato de Jefes de Unidad</option>
                <option value='contrato_de_agente_de_Atencion_pjv'>Contrato de Agente de Atención</option>
                <option value='carta_certificacion_isr'>Carta Certificación ISR</option>
                <option value='carta_ficha_itesa'>Carta Ficha</option>
                <option value='carta_de_maternidad'>Carta de Maternidad</option>
                <option value='carta_terminacion_obra'>Carta Terminacion Obra</option>
                <!--<option value='carta_estado_gravidez'>Carta Estado de Gravidez</option>-->
                <option value='expediente_asamblea'>Expediente</option>";
            
            }elseif($_SESSION["nombre_empresa_nomina"]=="PANACONSTRUCT S.A."){
                    $texto2 = "     <option value=''>Seleccione</option>   
                    <option value='contrato_de_trabajo_administrativo_pjv'>Contrato Administrativo</option>
                    <option value='contrato_asistente_jefe_de_unidad_pjv'>Contrato de Asistentes de Jefes de Unidad</option>
                    <option value='contrato_asesor_de_venta_pjv'>Contrato de Asesores de Ventas</option>
                    <option value='contrato_jefe_de_unidad_pjv'>Contrato de Jefes de Unidad</option>
                    <option value='contrato_de_agente_de_Atencion_pjv'>Contrato de Agente de Atención</option>
                    <option value='carta_certificacion_isr'>Carta Certificación ISR</option>
                    <option value='carta_ficha_panaconstruct'>Carta Ficha</option>
                    <option value='carta_de_maternidad'>Carta de Maternidad</option>
                    <option value='carta_terminacion_obra'>Carta Terminacion Obra</option>
                    <!--<option value='carta_estado_gravidez'>Carta Estado de Gravidez</option>-->
                    <option value='expediente_asamblea'>Expediente</option>";
                }else{
                {
                    $texto2 = "     <option value=''>Seleccione</option>       
                              <option value='contrato_administrativo'>Contrato Indefinivo Modelo</option>
                              <!--<option value='contrato_ayudante'>Contrato Ayudante/Almacenista</option>-->
                              <!--<option value='contrato_electrico'>Contrato Electrico</option>-->
                              <!--<option value='contrato_principiante'>Contrato Principiante</option>-->
                              <option value='carta_certificacion_isr'>Carta Certificación ISR</option>
                              <option value='carta_ficha_itesa'>Carta Ficha</option>
                              <option value='carta_terminacion_obra'>Carta Terminacion Obra</option>
                              <option value='carta_estado_gravidez'>Carta Estado de Gravidez</option>
                              <option value='expediente_asamblea'>Expediente</option>";
                }
            }
            
        //   $texto2 = "     <option value=''>Seleccione</option>       
        //                   <option value='contrato_administrativo'>Contrato Administrativos</option>
        //                   <option value='contrato_ayudante'>Contrato Ayudante/Almacenista</option>
        //                   <option value='contrato_electrico'>Contrato Electrico</option>
        //                   <option value='contrato_principiante'>Contrato Principiante</option>
        //                   <option value='carta_certificacion_isr'>Carta Certificación ISR</option>
        //                   <option value='carta_ficha_itesa'>Carta Ficha</option>
        //                   <option value='carta_terminacion_obra'>Carta Terminacion Obra</option>
        //                   <option value='carta_estado_gravidez'>Carta Estado de Gravidez</option>
        //                   <option value='expediente_asamblea'>Expediente</option>";
    while ( $aRow = mysqli_fetch_array( $rResult ) )
    {
        $row = array(); 
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if($aColumns[$i]=='imagen_cedula')
            {
                $dir = dirname(dirname(__FILE__));
                $dir = str_replace('\\', '/', $dir);

                $foto = $aRow[ $aColumns[$i] ];
                $foto_dir = $dir . '/'. $foto;

                //echo "Foto = " . $foto_dir . " file_exists = " . file_exists($foto_dir) . "<br>";

                // Si existe la foto del usuario se muestra esa. De lo contrario se muestra la foto por defecto
                $foto = ( $foto!=''  && file_exists($foto_dir) ) ? $foto : 'fotos/silueta.gif' ;

                $row[] = "<img width=\"65\" height=\"48\" src=\"".$foto."\" />";
            }
            else 
            {
                $row[] = utf8_encode($aRow[ $aColumns[$i] ]); 
            }
        }
        $row[] = "<select id=\"tipo_constancia".$aRow['ficha']."\" class=\"form-control\" name=\"tipo_constancia\" onchange=\"javascript:AbrirConstancia(".$aRow['ficha'].",".$_SESSION['codigo_nomina'].",this.value, this);\">".$texto."</select>";

        $row[] = "<select id=\"tipo_contrato\" class=\"form-control\" data-id=\"".$aRow['ficha']."\" name=\"tipo_contrato\" onchange=\"javascript:AbrirContrato(this.value,".$aRow['ficha'].",".$_SESSION['codigo_nomina'].");\">".$texto2."</select>";       
        
        $output['aaData'][] = $row;
    }
    
    echo json_encode( $output );
?>
