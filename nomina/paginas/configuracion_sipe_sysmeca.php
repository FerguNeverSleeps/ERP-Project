<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
?>
<script>
    function ActivarNivel(chkNivel,txtNivel){
    
        if (chkNivel.checked==false)
        {
            txtNivel.disabled="disabled"
        }
        else
        {
            txtNivel.disabled=""
        }
    }
    function Enviar(){                  

        //var fecha=new Date();
        //var ano_actual=fecha.getYear();                               
    
        //alert(document.frmAgregarIntegrantes.registro_id.value);          
        if (document.frmEmpresas.txtnombre.value==0)
        {
            document.frmEmpresas.op_tp.value=-1
            alert("Debe ingresar un nombre valido. Verifique...");              
        }   
        else
        {
            document.frmEmpresas.op_tp.value=2
        }
    }
</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">
    //Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
    //Script featured on JavaScript Kit (http://www.javascriptkit.com)
    //For this script, visit http://www.javascriptkit.com
</script>

<?php
//$registro_id=$_POST[registro_id];
$op_tp = $_POST['op_tp'];
//$fecha_actual=date("Y-m-d");
//$validacion=0;

if ($op_tp == 2) {

    if (isset($_POST['chkReportoNetosNevativos'])) {
        $NetosNevativos = "1";
    } else {
        $NetosNevativos = "0";
    }
    if (isset($_POST['chkSueldosCero'])) {
        $SueldosCero = "1";
    } else {
        $SueldosCero = "0";
    }
    if (isset($_POST['chkMediaJornada'])) {
        $MediaJornada = "1";
    } else {
        $MediaJornada = "0";
    }
    if (isset($_POST['chkIncluirNuevasSit'])) {
        $NuevasSituaciones = "1";
    } else {
        $NuevasSituaciones = "0";
    }
    if (isset($_POST['chkContratos'])) {
        $Contratos = "1";
    } else {
        $Contratos = "0";
    }
    if (isset($_POST['chkValidadPorcDeduccion'])) {
        $ValidarPorcDeducc = "1";
    } else {
        $ValidarPorcDeducc = "0";
    }

    if (isset($_POST['chkNivel1'])) {
        $nivel1 = 1;
    } else {
        $nivel1 = 0;
    }
    if (isset($_POST['chkNivel2'])) {
        $nivel2 = 1;
    } else {
        $nivel2 = 0;
    }
    if (isset($_POST['chkNivel3'])) {
        $nivel3 = 1;
    } else {
        $nivel3 = 0;
    }
    if (isset($_POST['chkNivel4'])) {
        $nivel4 = 1;
    } else {
        $nivel4 = 0;
    }
    if (isset($_POST['chkNivel5'])) {
        $nivel5 = 1;
    } else {
        $nivel5 = 0;
    }
    if (isset($_POST['chkNivel6'])) {
        $nivel6 = 1;
    } else {
        $nivel6 = 0;
    }
    if (isset($_POST['chkNivel7'])) {
        $nivel7 = 1;
    } else {
        $nivel7 = 0;
    }
    $sql     = "select * from nomempresa";
    $resulta = sql_ejecutar($sql);
    $fila    = mysqli_fetch_array($resulta);
    $archivo = $_FILES['imagen_izq']['name']; #HTTP_POST_FILES
    //echo $archivo;exit;
    #echo "nombre de archivo:" . $archivo;
    //exit(0);
    if ($archivo != "") {// || $archivo != " "
        $nombre_archivo1 = $_FILES['imagen_izq']['name']; #HTTP_POST_FILES
        $tipo_archivo = $_FILES['imagen_izq']['type']; #HTTP_POST_FILES
        $tamano_archivo = $_FILES['imagen_izq']['size']; #HTTP_POST_FILES
        ##HTTP_POST_FILES
        if (copy($_FILES['imagen_izq']['tmp_name'], "../imagenes/" . $nombre_archivo1)) {

            chmod("../imagenes/" . $nombre_archivo1, 0777);
        } else {
            echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurri&oacute; un problema cargando el archivo 1</div>";
        }
    }
    else
    {
        $nombre_archivo1 = $fila[imagen_izq];
    }

    $archivo = $_FILES['imagen_der']['name']; #HTTP_POST_FILES
    if ($archivo != "") {
        $nombre_archivo2 = $_FILES['imagen_der']['name']; #HTTP_POST_FILES
        $tipo_archivo = $_FILES['imagen_der']['type']; #HTTP_POST_FILES
        $tamano_archivo = $_FILES['imagen_der']['size']; #HTTP_POST_FILES

        if (copy($_FILES['imagen_der']['tmp_name'], "../imagenes/" . $nombre_archivo2)) {

            chmod("../imagenes/" . $nombre_archivo2, 0777);
        } else {
            echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Ocurri&oacute; un problema cargando el archivo 2</div>";
        }
    }
    else
    {
        $nombre_archivo2 = $fila[imagen_der];
    }
   
    $query = "UPDATE nomempresa set 
                nom_emp             ='".utf8_decode($_POST[txtnombre])."',
                rif                 ='".utf8_decode($_POST[txtidentificador1])."',
                nit                 ='".utf8_decode($_POST[txtidentificador2])."',
                dir_emp             ='".utf8_decode($_POST[txtdireccion])."',
                ciu_emp             ='".utf8_decode($_POST[txtciudad])."',
                edo_emp             ='".utf8_decode($_POST[txtestado])."',
                zon_emp             ='".utf8_decode($_POST[txtzonapostal])."',
                tel_emp             ='".utf8_decode($_POST[txttelefonos])."',
                pre_sid             ='".utf8_decode($_POST[txtrepresentante])."',
                ger_rrhh            ='".utf8_decode($_POST[txtencargadoRRHH])."',
                ced_rrhh            ='".utf8_decode($_POST[txtcedulaRRHH])."',
                contralor           ='".utf8_decode($_POST[contralor])."',
                ced_contralor       ='".utf8_decode($_POST[ced_contralor])."',
                jefe_planilla       ='".utf8_decode($_POST[jefe_planilla])."',
                ced_jefe_planilla   ='".utf8_decode($_POST[ced_jefe_planilla])."',
                jefe_registro       ='".utf8_decode($_POST[jefe_registro])."',
                ced_jefe_registro   ='".utf8_decode($_POST[ced_jefe_registro])."',
                cargo_jefe_registro ='".utf8_decode($_POST[cargo_jefe_registro])."',
                depto_registro      ='".utf8_decode($_POST[depto_registro])."',
                correo_emp          ='$_POST[correo_emp]',
                correo_rrhh         ='$_POST[correo_rrhh]',
                correo_planilla     ='$_POST[correo_planilla]',
                correo_sistemas1    ='$_POST[correo_sistemas]',
                nivel1              ='".utf8_decode($nivel1)."',
                nivel2              ='".utf8_decode($nivel2)."',
                nivel3              ='".utf8_decode($nivel3)."',
                nivel4              ='".utf8_decode($nivel4)."',
                nivel5              ='".utf8_decode($nivel5)."',
                nivel6              ='".utf8_decode($nivel6)."',
                nivel7              ='".utf8_decode($nivel7)."',               
                nomniv1             ='$_POST[txtnivel1]',
                nomniv2             ='$_POST[txtnivel2]',
                nomniv3             ='$_POST[txtnivel3]',
                nomniv4             ='$_POST[txtnivel4]',
                nomniv5             ='$_POST[txtnivel5]',
                nomniv6             ='$_POST[txtnivel6]',
                nomniv7             ='$_POST[txtnivel7]',
                imagen_izq          ='$nombre_archivo1',
                imagen_der          ='$nombre_archivo2',
                monsalmin           ='$_POST[salariominimo]',
                moneda              ='".$_POST["moneda"]."',
                recibonom           ='$_POST[recibonom]',
                conficha            ='$_POST[conficha]',
                actividad_economica ='$_POST[actividad_economica]',
                cedula_juridica     ='$_POST[cedula_juridica]',
                cedula_natural      ='$_POST[cedula_natural]',
                representante_licencia ='$_POST[representante_licencia]',
                representante_telef ='$_POST[representante_telef]',
                numero_patronal     ='$_POST[numero_patronal]',
                tipo_empresa        ='$_POST[opttipo_empresa]'";


    $result = sql_ejecutar($query);

    echo '<font color="#FF0000"><strong> Se actualizo correctamente el registro. </strong></font>';
}

$query               = "select * from nomempresa";
$result              = sql_ejecutar($query);
$row                 = mysqli_fetch_array($result);
$nompre_empresa      = utf8_encode($row["nom_emp"]);
$codigo_empresa      = utf8_encode($row["cod_emp"]);
$direccion           = utf8_encode($row["dir_emp"]);
$ciudad              = utf8_encode($row["ciu_emp"]);
$estado              = utf8_encode($row["edo_emp"]);
$zona_postal         = utf8_encode($row["zon_emp"]);
$telefono            = utf8_encode($row["tel_emp"]);
$rif                 = utf8_encode($row["rif"]);
$nit                 = utf8_encode($row["nit"]);
$edad_guarderia      = $row[edadmax];
$representante       = utf8_encode($row["pre_sid"]);
$ced_presid          = utf8_encode($row["ced_presid"]);
$encargadoRRHH       = utf8_encode($row["ger_rrhh"]);
$cedulaRRHH          = utf8_encode($row[ced_rrhh]);
$contralor           = utf8_encode($row[contralor]);
$ced_contralor       = utf8_encode($row[ced_contralor]);
$jefe_planilla       = utf8_encode($row[jefe_planilla]);
$ced_jefe_planilla   = utf8_encode($row[ced_jefe_planilla]);
$jefe_registro       = utf8_encode($row[jefe_registro]);
$ced_jefe_registro   = utf8_encode($row[ced_jefe_registro]);
$cargo_jefe_registro = utf8_encode($row[cargo_jefe_registro]);
$depto_registro      = utf8_encode($row[depto_registro]);
$correo_emp          = utf8_encode($row[correo_emp]);
$contratos           = $row[contratos];
$serial              = $row[serial];
$nosueldocero        = $row[nosueldocero];
$netonegativo        = $row[netoneg];
$MediaJornada        = $row[mediajornada];
$NuevasSituaciones   = $row[nuevassituaciones];
$TipoFicha           = $row[tipoficha];
$for_recibo_vac      = $row[recibovac];
$for_recibo_liq      = $row[reciboliq];
$for_recibo_pago     = $row[recibopago];
$por_diff            = $row[porcdiff];
$validar_porc_deducc = $row[reportdiff];
$monsalmin           = $row[monsalmin];
$recibonom           = $row[recibonom];
$material            = $row[cod_material];
$unidad              = $row[unidad];
$ccosto              = $row[ccosto];
$proveedor           = $row[proveedor];
$moneda              = $row[moneda];
$conficha            = $row[conficha];
$actividad_economica = $row[actividad_economica];
$cedula_juridica     = $row[cedula_juridica];
$cedula_natural     = $row[cedula_natural];
$representante_licencia     = $row[representante_licencia];
$representante_telef     = $row[representante_telef];
$numero_patronal     = $row[numero_patronal];
$correo_rrhh     = $row[correo_rrhh];
$correo_planilla     = $row[correo_planilla];
$correo_sistemas     = $row[correo_sistemas1];
//if(empty($imagen_derecha))
//{$imagen_derecha     = "../../includes/imagenes/".$fila[imagen_der];}
//else
//{$imagen_derecha     ="../../includes/imagenes/".$row[imagen_der];}
//if(empty($imagen_izquierda))
//{$imagen_izquierda   = "../../includes/imagenes/".$fila[imagen_izq];}
//else
//{$imagen_izquierda   ="../../includes/imagenes/".$row[imagen_izq];}


$imagen_derecha     ="../../includes/imagenes/".$row[imagen_der];

$imagen_izquierda   ="../../includes/imagenes/".$row[imagen_izq];
$tipo_empresa        = $row[tipo_empresa];

//msgbox($for_recibo_liq);
?>

<!-- BEGIN GLOBAL MANDATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/css/components.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .tile-icon {
    color: white;
    line-height: 125px; 
    font-size: 80px;
}
</style>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">
<meta lang="es">
<meta charset="utf-8">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <h4>Empresa</h4>
            </div>
            <div class="portlet-body">
            <form action="" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <div class="row">                   
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label>Código:</label>  
                        </div>    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" value="<?php echo $codigo_empresa; ?>" >

                        </div>

                        
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Nombre:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="txtnombre" type="text" id="txtnombre" value="<?php echo $nompre_empresa; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtidentificador1"><? if ($termino == "Planilla") { ?>RUC:<? } else { ?>RIF:<? } ?></label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                                <input class="form-control" name="txtidentificador1" type="text" id="txtidentificador1" value="<?php echo $rif; ?>">

                        </div>                        
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <label for="txtidentificador2">NIT:</label>
                        </div>     

                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <input  class="form-control" name="txtidentificador2" type="text" id="txtidentificador2" style="width:170px" value="<?php echo $nit; ?>">
                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtdireccion">Dirección:</label>
                        </div>                    
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">                                                 
                            <input class="form-control" name="txtdireccion" type="text" id="txtdireccion" size="100" value="<?php echo $direccion; ?>">
                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtciudad">Ciudad:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="txtciudad" type="text" id="txtciudad" value="<?php echo $ciudad; ?>">

                        </div> 
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtestado">Corregimiento:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                             <input class="form-control" name="txtestado" type="text" id="txtestado" value="<?php echo $estado; ?>">
                        </div>                
                    </div>
                    <BR>

                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtzonapostal">Zona Postal:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="txtzonapostal" type="text" id="txtzonapostal" value="<?php echo $telefono; ?>">

                        </div> 
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txttelefonos">Teléfonos:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                             <input class="form-control" name="txttelefonos" type="text" id="txttelefonos" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php echo $zona_postal; ?>">
                        </div>                
                    </div>
                    <BR>

                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtrepresentante">Representante Legal:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="txtrepresentante" type="text" id="txtrepresentante" value="<?php echo $representante; ?>">

                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="ced_presid">Cedula del Representante:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="ced_presid" type="text" id="ced_presid" value="<?php echo $ced_presid; ?>">
                        </div>                
                    </div>
                    <BR>
                    
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="representante_licencia">Licencia Representante:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="representante_licencia" type="text" id="representante_licencia" value="<?php echo $representante_licencia; ?>">

                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="representante_telef">Teléfono Representante:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="representante_telef" type="text" id="representante_telef" value="<?php echo $representante_telef; ?>">
                        </div>                
                    </div>
                    <BR>

                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtencargadoRRHH">Encargado RRHH:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                             <input class="form-control" name="txtencargadoRRHH" type="text" id="txtencargadoRRHH" value="<?php echo $encargadoRRHH; ?>">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtcedulaRRHH">Cédula RRHH:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                             <input class="form-control" name="txtcedulaRRHH" type="text" id="txtcedulaRRHH" value="<?php echo $cedulaRRHH; ?>">
                        </div>                 
                    </div>
                    <BR>

                   <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="jefe_planilla">Jefe de Planillas:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                             <input class="form-control" name="jefe_planilla" type="text" id="jefe_planilla" value="<?php echo $jefe_planilla; ?>">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="ced_jefe_planilla">Cédula Jefe Planillas:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                             <input class="form-control" name="ced_jefe_planilla" type="text" id="ced_jefe_planilla" value="<?php echo $ced_jefe_planilla; ?>">
                        </div>                 
                    </div>
                    <BR>

                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="correo">Correo:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                             <input class="form-control" name="correo_emp" type="email" id="correo_emp" value="<?php echo $correo_emp; ?>">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtidentificador1">Tipo de Empresa</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <div class="radio-list">
                            <?php
                                $sql = "SELECT codigo, descripcion FROM nomtipos_empresa";
                                $res = sql_ejecutar_utf8($sql);

                                $i=1;
                                while($fila = mysqli_fetch_array($res))
                                {
                                ?>
                                    
                                    <label for="opttipo_empresa<?php echo $i; ?>" >
                                    <?php
                                        if($tipo_empresa == $fila['codigo'])
                                        { ?>
                                                <input type="radio" name="opttipo_empresa" id="opttipo_empresa<?php echo $i; ?>" value="<?php echo $fila['codigo']; ?>" checked="checked">
                                          <?php
                                        }
                                        else
                                        { ?>
                                                <input type="radio" name="opttipo_empresa" id="opttipo_empresa<?php echo $i; ?>" value="<?php echo $fila['codigo']; ?>">
                                          <?php
                                        }

                                        echo $fila['descripcion'];
                                    ?>                                                    
                                    </label>
                                    
                                <?php
                                $i++;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <BR>
                    
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="cedula_natural">Cédula Natural:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="cedula_natural" type="text" id="cedula_natural" value="<?php echo $cedula_natural; ?>">

                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="cedula_juridica">Cédula Juridica:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="cedula_juridica" type="text" id="cedula_juridica" value="<?php echo $cedula_juridica; ?>">
                        </div>                
                    </div>
                    <BR>
                    
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="actividad_economica">Actividad Económica:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            
                            <input class="form-control" name="actividad_economica" type="text" id="actividad_economica" value="<?php echo $actividad_economica; ?>">

                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="numero_patronal">Número Patronal:</label>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="numero_patronal" type="text" id="numero_patronal" value="<?php echo $numero_patronal; ?>">
                        </div>                
                    </div>
                    <BR>

                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                             <label for="recibonom">Nota en recibos:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <INPUT class="form-control" type="text" name="recibonom" value="<? echo $recibonom ?>">
                        </div>                            
                    </div>
                    <br>
                    
                    <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <div class="row">
                                <div class="portlet box blue-madison">
                                    <div class="portlet-title">
                                        <h5>Parámetros</h5>
                                    </div>
                                    <div class="portlet-body">
                                         Imagen Izquierda&nbsp;&nbsp;<INPUT type="file" name="imagen_izq">
                                         <img class="media-object" src="<? echo $imagen_izquierda; ?>" alt="Logo Izquierda" style="height: 64px; width: 64px; display: block;">
                                        <BR>
                                        Imagen Derecha&nbsp;&nbsp;<INPUT type="file" name="imagen_der">
                                        <img class="media-object" src="<? echo $imagen_derecha; ?>" alt="Logo Derecha"  style="height: 64px; width: 64px; display: block;">
                                        <BR>
                                        Salario M&iacute;nimo&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="salariominimo" value="<? echo $monsalmin; ?>">
                                        <BR>
                                        <BR>
                                         Moneda&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="moneda" value="<? echo $moneda; ?>">
                                         <BR>
                                        <BR>
                                         Última Ficha:&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="conficha" value="<? echo $conficha; ?>">
                    <br>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            &nbsp;
                        </div> 
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                           <div class="row">
                            <div class="portlet box blue-hoki">
                                <div class="portlet-title"><h5>Estructura Organizacional</h5></div>
                                <div class="portlet-body">
                                    <label for="txtnivel1"><input name="chkNivel1" type="checkbox" id="chkNivel1" value="checkbox" onClick="ActivarNivel(this,document.frmEmpresas.txtnivel1);"
                                               <?php if ($row[nivel1] == 1) { ?> checked="checked" <?php } ?>>Nivel 1</label>
                                                          
                                     <input name="txtnivel1" type="text" style="width:200px" id="txtnivel1"
                                               value=<?php
                                       if ($row[nivel1] == 1) {
                                           echo $row[nomniv1];
                                       }
                                       ?>>
                                       <BR>
                                    <input name="chkNivel2" type="checkbox" id="chkNivel2" value="checkbox"
                                       <?php if ($row[nivel2] == 1) { ?> checked="checked" <?php } ?>
                                       onClick="ActivarNivel(this,document.frmEmpresas.txtnivel2);">
                                            Nivel 2
                                                                   
                                             <input name="txtnivel2" type="text" style="width:200px" id="txtnivel2"
                                           value=<?php
                                           if ($row[nivel2] == 1) {
                                               echo $row[nomniv2];
                                           }
                                           ?>>
                                    <BR>
                                    <input name="chkNivel3" type="checkbox" id="chkNivel3" value="checkbox"
                                   <?php if ($row[nivel3] == 1) { ?> checked="checked" <?php } ?>
                                   onClick="ActivarNivel(this,document.frmEmpresas.txtnivel3);">
                                            Nivel 3 
                                                               
                                        <input name="txtnivel3" type="text" style="width:200px" id="txtnivel3"
                                               value=<?php
                                               if ($row[nivel3] == 1) {
                                                   echo $row[nomniv3];
                                               }
                                               ?>>
                                    <BR>
                                        <input name="chkNivel4" type="checkbox" id="chkNivel4" value="checkbox"
                                       <?php if ($row[nivel4] == 1) { ?> checked="checked" <?php } ?>
                                       onClick="ActivarNivel(this,document.frmEmpresas.txtnivel4);">
                                        Nivel 4 
                                        <input name="txtnivel4" type="text" style="width:200px" id="txtnivel4"
                                           value=<?php
                                           if ($row[nivel4] == 1) {
                                               echo $row[nomniv4];
                                       }
                                       ?>>
                                    <BR>
                                        <input name="chkNivel5" type="checkbox" id="chkNivel5" value="checkbox"
                                       <?php if ($row[nivel5] == 1) { ?> checked="checked" <?php } ?>
                                           onClick="ActivarNivel(this,document.frmEmpresas.txtnivel5);">
                                            Nivel 5
                                           <input name="txtnivel5" type="text" style="width:200px" id="txtnivel5"
                                           value=<?php
                                           if ($row[nivel5] == 1) {
                                               echo $row[nomniv5];
                                       }
                                       ?>>
                                       <BR>
                                       <input name="chkNivel6" type="checkbox" id="chkNivel6" value="checkbox"
                                           <?php if ($row[nivel6] == 1) { ?> checked="checked" <?php } ?>
                                           onClick="ActivarNivel(this,document.frmEmpresas.txtnivel6);">
                                                    Nivel 6

                                                <input name="txtnivel6" type="text" style="width:200px" id="txtnivel6"
                                               value=<?php
                                               if ($row[nivel6] == 1) {
                                                   echo $row[nomniv6];
                                               }
                                           ?>>
                                       <BR>
                                       <input name="chkNivel7" type="checkbox" id="chkNivel7" value="checkbox"
                                       <?php if ($row[nivel7] == 1) { ?> checked="checked" <?php } ?>
                                       onClick="ActivarNivel(this,document.frmEmpresas.txtnivel7);">
                                            Nivel 7
                                        
                                        <input name="txtnivel7" type="text" style="width:200px" id="txtnivel7"
                                           value=<?php
                                       if ($row[nivel7] == 1) {
                                           echo $row[nomniv7];
                                       }
                                       ?>> 
                                </div>
                            </div>
                           </div>
                        </div>                    
              
                    </div>
                    <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                            <div class="row">
                                <div class="portlet box blue-madison">
                                    <div class="portlet-title">
                                        <h5>Correos Electrónico (Mensajeria)</h5>
                                    </div>
                                    <div class="portlet-body">
                                        <BR>
                                        Correo RRHH:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="correo_rrhh" value="<? echo $correo_rrhh; ?>">
                                        <BR>
                                        <BR>
                                         Correo Planilla:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="correo_planilla" value="<? echo $correo_planilla; ?>">
                                         <BR>
                                        <BR>
                                         Correo Sistemas (1):&nbsp;&nbsp;&nbsp;&nbsp;<INPUT type="text" name="correo_sistemas" value="<? echo $correo_sistemas; ?>">
                                        <br>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                   <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        </div>                    
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">                            
                                 <?php boton_metronic('ok', 'Enviar(); document.frmEmpresas.submit();', 2) ?>
                        </div>                    

                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <?php boton_metronic('cancel', 'history.back();', 2) ?>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
                        </div>                
                    </div>
                    
                    
                </div>
            <!-- END PORTLET BODY-->            
            </form>
            </div>
        <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT-->
    </div>
  </div>
    <!-- END CONTENT -->
</div>



<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<p>&nbsp;</p>  
</form>
<p>&nbsp;</p>
</body>
</html>
