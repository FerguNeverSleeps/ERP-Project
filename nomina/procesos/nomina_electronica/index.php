<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

include('../../lib/common.php');

global $conexion;
$conexion = new bd($_SESSION['bd']);
$sql = "
	SELECT 
		id_configuracion,
		token_enterprise,
		token_password,
		nit_empleador,
		id_software,
		correlativo_prefijo,
		correlativo_contador,
        correlativo_anulados_prefijo,
        correlativo_anulados_contador
    FROM nomina_electronica_parametros 
";

$res=$conexion->query($sql);
$configuracion=$res->fetch_array();

if(!isset($configuracion["id_configuracion"])){
	print "<br>Debe establecer los parametros de configuración de nomina electrónica.";
	exit;
}

global $token_enterprise;
global $token_password;
global $nit_empleador;
global $id_software;
global $correlativo_prefijo;
global $correlativo_contador;
global $correlativo_anulados_prefijo;
global $correlativo_anulados_contador;

$token_enterprise              = "";
$token_password                = "";
$nit_empleador                 = "";
$id_software                   = "";
$correlativo_prefijo           = "";
$correlativo_contador          = "";
$correlativo_anulados_prefijo  = "";
$correlativo_anulados_contador = "";

if(isset($configuracion["token_enterprise"]) and $configuracion["token_enterprise"])
	$token_enterprise     = trim($configuracion["token_enterprise"]);
if(isset($configuracion["token_password"]) and $configuracion["token_password"])
	$token_password       = trim($configuracion["token_password"]);
if(isset($configuracion["nit_empleador"]) and $configuracion["nit_empleador"])
	$nit_empleador        = trim($configuracion["nit_empleador"]);
if(isset($configuracion["id_software"]) and $configuracion["id_software"])
	$id_software          = trim($configuracion["id_software"]);
if(isset($configuracion["correlativo_prefijo"]) and $configuracion["correlativo_prefijo"])
	$correlativo_prefijo  = trim($configuracion["correlativo_prefijo"]);
if(isset($configuracion["correlativo_contador"]) and $configuracion["correlativo_contador"])
	$correlativo_contador = trim($configuracion["correlativo_contador"]);
if(isset($configuracion["correlativo_anulados_prefijo"]) and $configuracion["correlativo_anulados_prefijo"])
    $correlativo_anulados_prefijo  = trim($configuracion["correlativo_anulados_prefijo"]);
if(isset($configuracion["correlativo_anulados_contador"]) and $configuracion["correlativo_anulados_contador"])
    $correlativo_anulados_contador = trim($configuracion["correlativo_anulados_contador"]);

if(!$token_enterprise){
	print "<br>Debe definir el parametro nomina electrónica 'TOKEN ENTERPRISE'.";
	exit;
}
if(!$token_password){
	print "<br>Debe definir el parametro nomina electrónica 'TOKEN PASSWORD'.";
	exit;
}
if(!$nit_empleador){
	print "<br>Debe definir el parametro nomina electrónica 'NIT EMPLEADOR'.";
	exit;
}
if(!$id_software){
	print "<br>Debe definir el parametro nomina electrónica 'ID SOFTWARE'.";
	exit;
}
if(!$correlativo_prefijo){
	print "<br>Debe definir el parametro nomina electrónica 'CORRELATIVO PREFIJO'.";
	exit;
}
if(!$correlativo_contador){
	print "<br>Debe definir el parametro nomina electrónica 'CORRELATIVO CONTADOR'.";
	exit;
}
if(!$correlativo_anulados_prefijo){
    print "<br>Debe definir el parametro nomina electrónica 'CORRELATIVO ANULADOS PREFIJO'.";
    exit;
}
if(!$correlativo_anulados_contador){
    print "<br>Debe definir el parametro nomina electrónica 'CORRELATIVO ANULADOS CONTADOR'.";
    exit;
}


if($_REQUEST["accion"]=="anular"){
	include_once("nomina_electronica_anular.php");
	exit;
}

//INICIO DEL PROCESO INVOCAR FUNCIONES PARA EL LLENADO DE LAS TABLAS TEMPORALES CON LOS CALCULOS DE LA NOMINA A PROCESAR

$anio        = $_REQUEST["anio"];
$mes         = $_REQUEST["mes"];
$fecha_ini   = $_REQUEST["fecha_inicio"];
$fecha_fin   = $_REQUEST["fecha_fin"];
$codtip      = $_REQUEST["codtip"];
$descripcion = $_REQUEST["descripcion"];

$fecha_ini=explode("/", $fecha_ini);
$fecha_ini=$fecha_ini[2]."-".$fecha_ini[1]."-".$fecha_ini[0];

$fecha_fin=explode("/", $fecha_fin);
$fecha_fin=$fecha_fin[2]."-".$fecha_fin[1]."-".$fecha_fin[0];


include_once("nomina_electronica_planilla.php");
$RETORNO=nomina_electronica_proceso_nomina_general_periodos_global_procesar($anio,$mes,$fecha_ini,$fecha_fin,$codtip,$descripcion);

//print_r($RETORNO);
//exit;


//INICIO DEL PROCESO DE ARMADO DEL REQUEST POR CADA TRABAJADOR Y SU ENVIO
include_once("nomina_electronica.class.php");
global $id_cabecera;
$id_cabecera=$RETORNO["id_cabecera"];//el proceso anterior debe generar el id_cabecera, colorcarlo aca en la variable $id_cabecera

$exitoso=0;
$fallido=0;

//$id_cabecera=2;

//buscar parametros de configuracion de los conceptos
$sql="select * from nomina_electronica_configuracion";
$res=$conexion->query($sql);
$configuracion_conceptos=[];
while($row=$res->fetch_array()){
    $configuracion_conceptos[]=$row;
}

//buscar informacion del id_cabecera en proceso
$sql="select * from nomina_electronica_cabecera where id_cabecera='$id_cabecera'";
$res=$conexion->query($sql);
$cabecera=$res->fetch_array();


//buscar los trabajadores contenidos en el periodo a procesar
$sql="select distinct ficha, cedula from nomina_electronica_detalle_trabajador where id_cabecera='$id_cabecera'";
$res=$conexion->query($sql);
$trabajador=[];
while($row=$res->fetch_array()){
    $trabajador[]=$row;
}



$nomina=[];
//para cada trabajador armar el request
for($i=0; $i<count($trabajador); $i++) { 
    //crear una instancia del objeto nomina para el trabajador en curso
    $nomina[$i]=new AMX_NominaElectronica([
    	"tokenEnterprise"=> $token_enterprise,
    	"tokenPassword"  => $token_password,
    	"idSoftware"     => $id_software,
    	"nitEmpleador"   => $nit_empleador
    ]);

    //buscar los datos del trabajador
    $sql="select * from nomina_electronica_detalle_trabajador where id_cabecera=$id_cabecera and ficha='".$trabajador[$i]["ficha"]."' and cedula='".$trabajador[$i]["cedula"]."'";

    //print $sql;exit;
    $res=$conexion->query($sql);

    $trj=[];
    while($row=$res->fetch_array()){
        $indice = $row["campo"];
        $valor  = $row["valor"];
        if(!mb_detect_encoding($valor,["UTF-8"],true))
			$valor=utf8_encode($valor);
		if($indice=="numeroDocumento"){
			$valor=str_replace("-", "", $valor);
			$valor=str_replace("_", "", $valor);
			$valor=str_replace(" ", "", $valor);
		}
        $trj["$indice"]=$valor;
    }
    $trj["lugarTrabajoDepartamentoEstado"]="11";
    $trj["lugarTrabajoMunicipioCiudad"]="11001";
    $trj["salarioIntegral"]="0";

    //print_r( $trj);exit;
    $nomina[$i]->Trabajador($trj);
    $nomina[$i]->Notas(["descripcion"=>$cabecera["descripcion"]]);

    $totalDevengados=0;
    $totalDeducciones=0;
    //print_r($configuracion_conceptos);
    //por cada concepto de la configuracion, hallar los del trabajador
    //print "<br>ficha: ".$trabajador[$i]["ficha"]." - ".$trabajador[$i]["cedula"]."<br>    ";
    for($j=0; $j<count($configuracion_conceptos); $j++) {
        $sql_ejecutar="
            select      
                COALESCE(( SUM(IF(codcon in ({conceptos_suman}),monto,0)) ),0)  - COALESCE(( SUM(IF(codcon in ({conceptos_restan}),monto,0)) ),0)  monto
            from 
                nomina_electronica_detalle_conceptos 
            where 
                id_cabecera='$id_cabecera' and
                ficha='".$trabajador[$i]["ficha"]."' and 
                cedula='".$trabajador[$i]["cedula"]."'            
        ";

        $tmp=conceptos_suman_restan($configuracion_conceptos[$j]["conceptos"]);
        //print "<br>j: $j   ";
        //print_r($tmp);
        $conceptos_suman  = $tmp["conceptos_suman"];
        $conceptos_restan = $tmp["conceptos_restan"];
        if(!$conceptos_suman)  $conceptos_suman  ="-1";
        if(!$conceptos_restan) $conceptos_restan ="-1";

        $sql_ejecutar = str_replace("{conceptos_suman}",  "$conceptos_suman",  $sql_ejecutar);
        $sql_ejecutar = str_replace("{conceptos_restan}", "$conceptos_restan", $sql_ejecutar);



        $res=$conexion->query($sql_ejecutar);
        $row=$res->fetch_array();
        //print "<br>". $sql_ejecutar;
        
        $monto="";
        $atributo=$configuracion_conceptos[$j]["nombre_corto"];
        if(isset($row["monto"]) and $row["monto"]>0){
        	$monto=number_format($row["monto"],2,".","");
            switch($configuracion_conceptos[$j]["tipo"]){
                case '1':
                case 'deducciones':
                    $totalDeducciones+=$row["monto"];
                    if(in_array($atributo, ["afc","cooperativa","deuda","educacion","embargoFiscal","pensionVoluntaria","planComplementarios","reintegro","retencionFuente"])){
                        $nomina[$i]->Deducciones($atributo,$monto);
                    }
                    else if($atributo=="anticiposNom"){
                        $nomina[$i]->Deducciones__Agregar_Anticipos(["montoanticipo"=>"$monto"]);
                    }
                    else if($atributo=="fondosPensiones"){
                        $nomina[$i]->Deducciones__Agregar_FondosPensiones(["deduccion"=>"$monto","porcentaje"=>"0.00"]);
                    }
                    else if($atributo=="fondosSP"){
                        $nomina[$i]->Deducciones__Agregar_FondosSP(["deduccionSP"=>"$monto","deduccionSub"=>"0.00","porcentaje"=>"0.00","porcentajeSub"=>"0.00"]);
                    }
                    else if($atributo=="libranzas"){
                        $nomina[$i]->Deducciones__Agregar_Libranzas(["deduccion"=>"$monto","descripcion"=>"Descripcion General de Libranza"]);
                    }
                    else if($atributo=="otrasDeducciones"){
                        $nomina[$i]->Deducciones__Agregar_OtrasDeducciones(["montootraDeduccion"=>"$monto"]);
                    }
                    else if($atributo=="pagosTerceros"){
                        $nomina[$i]->Deducciones__Agregar_PagosTerceros(["montopagotercero"=>"$monto"]);
                    }
                    else if($atributo=="salud"){
                        $nomina[$i]->Deducciones__Agregar_Salud(["deduccion"=>"$monto","porcentaje"=>"0.00"]);
                    }
                    else if($atributo=="sanciones"){
                        $nomina[$i]->Deducciones__Agregar_Sanciones(["sancionPublic"=>"$monto","sancionPriv"=>"$monto"]);
                    }
                    else if($atributo=="sindicatos"){
                        $nomina[$i]->Deducciones__Agregar_Sindicatos(["deduccion"=>"$monto","porcentaje"=>"0.00"]);
                    }
                break;                
                case '2':
                case 'devengados':
                    $totalDevengados+=$row["monto"];
                    if(in_array($atributo, ["apoyoSost","bonifRetiro","dotacion","reintegro","teletrabajo"])){
                        $nomina[$i]->Devengados($atributo,$monto);
                    }
                    else if($atributo=="anticiposNom"){
                        $nomina[$i]->Devengados__Agregar_Anticipos(["montoanticipo"=>"$monto"]);
                    }
                    else if($atributo=="auxilios"){
                        $nomina[$i]->Devengados__Agregar_Auxilios(["auxilioNS"=>"$monto","auxilioS"=>"$monto"]);
                    }
                    else if($atributo=="basico"){
                        $nomina[$i]->Devengados__Agregar_Basico(["diasTrabajados"=>"15","sueldoTrabajado"=>"$monto"]);
                    }
                    else if($atributo=="bonificaciones"){
                        $nomina[$i]->Devengados__Agregar_Bonificaciones(["bonificacionNS"=>"$monto","bonificacionS"=>"$monto"]);
                    }
                    else if($atributo=="bonoEPCTVs"){
                        $nomina[$i]->Devengados__Agregar_BonoEPCTVs(["pagoAlimentacionNS"=>"$monto","pagoAlimentacionS"=>"$monto","pagoNS"=>"$monto","pagoS"=>"$monto"]);
                    }
                    else if($atributo=="cesantias"){
                        $nomina[$i]->Devengados__Agregar_Cesantias(["montocomision"=>"$monto"]);
                    }
                    else if($atributo=="compensaciones"){
                        $nomina[$i]->Devengados__Agregar_Compensaciones(["compensacionE"=>"$monto","compensacionO"=>"$monto"]);
                    }
                    else if($atributo=="horasExtras"){
                        $nomina[$i]->Devengados__Agregar_HorasExtras(["cantidad"=>"$monto","horaInicio"=>"2021-03-02 12:00:00","horaFin"=>"2021-03-02 12:00:00","pago"=>"$monto","porcentaje"=>"25.00","tipoHorasExtra"=>"0"]);
                    }
                    else if($atributo=="huelgasLegales"){
                        $nomina[$i]->Devengados__Agregar_HuelgasLegales(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16"]);
                    }
                    else if($atributo=="incapacidades"){
                        $nomina[$i]->Devengados__Agregar_Incapacidades(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto","tipo"=>"2"]);
                    }
                    else if($atributo=="licenciaMP"){
                        $nomina[$i]->Devengados__Agregar_LicenciaMP(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto"]);
                    }
                    else if($atributo=="licenciaNR"){
                        $nomina[$i]->Devengados__Agregar_LicenciaNR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto"]);
                    }
                    else if($atributo=="licenciaR"){
                        $nomina[$i]->Devengados__Agregar_LicenciaR(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto"]);
                    }
                    else if($atributo=="otrosConceptos"){
                        $nomina[$i]->Devengados__Agregar_OtrosConceptos(["conceptoNS"=>"$monto","conceptoS"=>"$monto","descripcionConcepto"=>"Descipcion Generica"]);
                    }
                    else if($atributo=="pagosTerceros"){
                        $nomina[$i]->Devengados__Agregar_PagosTerceros(["montopagotercero"=>"$monto"]);
                    }
                    else if($atributo=="primas"){
                        $nomina[$i]->Devengados__Agregar_Primas(["cantidad"=>"30","pago"=>"$monto","pagoNS"=>"$monto"]);
                    }
                    else if($atributo=="transporte"){
                        $nomina[$i]->Devengados__Agregar_Transporte(["auxilioTransporte"=>"$monto","sueldoTrabajado"=>"$monto","viaticoManuAlojNS"=>"$monto"]);
                    }
                    else if($atributo=="vacacionesComunes"){
                        $nomina[$i]->Devengados__Agregar_VacacionesComunes(["cantidad"=>"30","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto"]);
                    }
                    else if($atributo=="vacacionesCompensadas"){
                        $nomina[$i]->Devengados__Agregar_VacacionesCompensadas(["cantidad"=>"20","fechaInicio"=>"2021-03-15","fechaFin"=>"2021-03-16","pago"=>"$monto"]);
                    }
                break;
            }
        }
    }

    //print "<br>paso"; exit;

    $totalComprobante=$totalDevengados-$totalDeducciones;


    $consecutivoDocumentoNom=substr($correlativo_prefijo, 0, 4).$correlativo_contador;
    $nomina[$i]->Asignar("totalDeducciones",number_format($totalDeducciones,2,".",""));
    $nomina[$i]->Asignar("totalDevengados",number_format($totalDevengados,2,".",""));
    $nomina[$i]->Asignar("totalComprobante",number_format($totalComprobante,2,".",""));
    $nomina[$i]->Asignar("consecutivoDocumentoNom",$consecutivoDocumentoNom);
    $nomina[$i]->Asignar("rangoNumeracionNom",substr($correlativo_prefijo, 0, 4)."-1");
	$nomina[$i]->Asignar("periodoNomina","4");
	$nomina[$i]->Asignar("fechaEmisionNom",date("Y-m-d H:i:s"));
	$nomina[$i]->Asignar("tipoNota","1");
	$nomina[$i]->Asignar("tipoMonedaNom","COP");
	$nomina[$i]->Asignar("novedad","0");
	//$nomina[$i]->Asignar("novedadCUNE","d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e");
	//$nomina[$i]->DocumentosReferenciados(["cunePred"=>"d91deada982e658a3f69bf8eb2d173c7c10142fe34dee71f9c3c75b3d066b2f535083f02657a79369efdd6d9dc11240e","fechaGenPred"=>"2021-03-04","numeroPred"=>"DSNE1"]);

	$nomina[$i]->Asignar("tipoDocumentoNom","102");
	$nomina[$i]->LugarGeneracionXML(["departamentoEstado"=>"11","idioma"=>"es","municipioCiudad"=>"11001","pais"=>"CO"]);
	$nomina[$i]->Pagos(["fechasPagos"=>[["fechapagonomina"=>date("Y-m-d")]],"metodoDePago"=>"1","medioPago"=>"1","nombreBanco"=>NULL,"tipoCuenta"=>NULL,"numeroCuenta"=>NULL]);
	//$nomina[$i]->Periodos(["fechaIngreso"=>"2021-03-02","fechaLiquidacionInicio"=>"2021-03-03","fechaLiquidacionFin"=>"2021-03-04","fechaRetiro"=>"2021-03-02","tiempoLaborado"=>"3"]);

	$datetime1 = date_create("$fecha_fin");
	$datetime2 = date_create("$fecha_ini");
	$interval = date_diff($datetime1, $datetime2, true);

	$dias=$interval->d+1;
	if($dias>30)
		$dias=30;

	$total_dias=$interval->y*360+$interval->m*30+$dias;

	$nomina[$i]->Periodos(["fechaIngreso"=>"$fecha_ini","fechaLiquidacionInicio"=>"$fecha_ini","fechaLiquidacionFin"=>"$fecha_fin","fechaRetiro"=>"$fecha_fin","tiempoLaborado"=>"$total_dias"]);
	$nomina[$i]->Asignar("redondeo","0.00");
	$nomina[$i]->Asignar("trm","0.00");
	
	


	//incrementar el correlativo del documento a enviar
	$correlativo_contador++;
	$sql="update nomina_electronica_parametros set correlativo_contador='".$correlativo_contador."'";
	$conexion->query($sql);

    $respuesta=$nomina[$i]->Enviar();
    //print_r($nomina[$i]->data);
    //print_r($respuesta);

    //llenar tablas tablas de envio y respuesta [nomina_electronica_detalle_request,nomina_electronica_detalle_response]
    $sql="
    	insert into nomina_electronica_detalle_request(
	    	id_cabecera,
	    	ficha,
	    	cedula,
	    	consecutivoDocumentoNom,
			deducciones,
			devengados,
			documentosReferenciadosNom,
			extrasNom,
			fechaEmisionNom,
			notas,
			novedad,
			novedadCUNE,
			lugarGeneracionXML,
			pagos,
			periodoNomina,
			periodos,
			rangoNumeracionNom,
			redondeo,
			tipoDocumentoNom,
			tipoMonedaNom,
			tipoNota,
			totalComprobante,
			totalDeducciones,
			totalDevengados,
			trm,
			trabajador
		) 
		VALUES (
			'$id_cabecera',
			'".$trabajador[$i]["ficha"]."',
			'".$trabajador[$i]["cedula"]."',
			'".$consecutivoDocumentoNom."',
			'".$nomina[$i]->get_base64("deducciones")."',
			'".$nomina[$i]->get_base64("devengados")."',
			'".$nomina[$i]->get_base64("documentosReferenciadosNom")."',
			'".$nomina[$i]->get_base64("extrasNom")."',
			'".$nomina[$i]->get_base64("fechaEmisionNom")."',
			'".$nomina[$i]->get_base64("notas")."',
			'".$nomina[$i]->get_base64("novedad")."',
			'".$nomina[$i]->get_base64("novedadCUNE")."',
			'".$nomina[$i]->get_base64("lugarGeneracionXML")."',
			'".$nomina[$i]->get_base64("pagos")."',
			'".$nomina[$i]->get_base64("periodoNomina")."',
			'".$nomina[$i]->get_base64("periodos")."',
			'".$nomina[$i]->get_base64("rangoNumeracionNom")."',
			'".$nomina[$i]->get_base64("redondeo")."',
			'".$nomina[$i]->get_base64("tipoDocumentoNom")."',
			'".$nomina[$i]->get_base64("tipoMonedaNom")."',
			'".$nomina[$i]->get_base64("tipoNota")."',
			'".$nomina[$i]->get_base64("totalComprobante")."',
			'".$nomina[$i]->get_base64("totalDeducciones")."',
			'".$nomina[$i]->get_base64("totalDevengados")."',
			'".$nomina[$i]->get_base64("trm")."',
			'".$nomina[$i]->get_base64("trabajador")."'
		)
	";
	$conexion->query($sql);
	//$id_detalle_request=$conexion->insert_id;
	//$id_detalle_request = mysqli_insert_id($conexion);
	$res2=$conexion->query("select max(id_detalle_request) id from nomina_electronica_detalle_request");
	$tmp=$res2->fetch_array();
	$id_detalle_request=$tmp["id"];

	$sql="
		insert into nomina_electronica_detalle_response(
			id_detalle_request,
			id_cabecera,
			ficha,
	    	cedula,
			codigo,
			mensaje,
			resultado,
			consecutivoDocumento,
			cune,
			trackId,
			reglasNotificacionesTFHKA,
			reglasNotificacionesDIAN,
			reglasRechazoTFHKA,
			reglasRechazoDIAN,
			nitEmpleador,
			nitEmpleado,
			idSoftware,
			qr,
			esvalidoDIAN,
			xml
		)
		VALUES (
			'$id_detalle_request',
			'$id_cabecera',
			'".$trabajador[$i]["ficha"]."',
			'".$trabajador[$i]["cedula"]."',
			'".$nomina[$i]->Respuesta()->get("codigo")."',
			'".utf8_decode($nomina[$i]->Respuesta()->get("mensaje"))."',
			'".utf8_decode($nomina[$i]->Respuesta()->get("resultado"))."',
			'".$consecutivoDocumentoNom."',
			'".$nomina[$i]->Respuesta()->get("cune")."',
			'".$nomina[$i]->Respuesta()->get("trackId")."',
			'".$nomina[$i]->Respuesta()->get_base64("reglasNotificacionesTFHKA")."',
			'".$nomina[$i]->Respuesta()->get_base64("reglasNotificacionesDIAN")."',
			'".$nomina[$i]->Respuesta()->get_base64("reglasRechazoTFHKA")."',
			'".$nomina[$i]->Respuesta()->get_base64("reglasRechazoDIAN")."',
			'".$nomina[$i]->Respuesta()->get("nitEmpleador")."',
			'".$nomina[$i]->Respuesta()->get("nitEmpleado")."',
			'".$nomina[$i]->Respuesta()->get("idSoftware")."',
			'".$nomina[$i]->Respuesta()->get_base64("qr")."',
			'".$nomina[$i]->Respuesta()->get_base64("esvalidoDIAN")."',
			'".$nomina[$i]->Respuesta()->get_base64("xml")."'
		)
	";
	$conexion->query($sql);

	if($nomina[$i]->Respuesta()->get("codigo")=="200"){
		$exitoso++;
	}
	else {
		$fallido++;
	}
	//break;
	//exit;
}

//exit;

if($exitoso==count($trabajador)){
	$sql="update nomina_electronica_cabecera set estatus=1 where id_cabecera='$id_cabecera'";
}
else{
	$sql="update nomina_electronica_cabecera set estatus=0 where id_cabecera='$id_cabecera'";
}
$conexion->query($sql);


if($id_cabecera and !isset($_REQUEST["accion_culminar"])){
	header("Location: ../nomina_electronica_detalle_response.php?id=$id_cabecera");
}

//una vez culminado el proceso, cargar la tabla que se mostrara en el modal, si el parametro accion_culminar es 2
if(isset($_REQUEST["accion_culminar"]) and $_REQUEST["accion_culminar"]=="2"){	
	//print "accion culminar ".$_REQUEST["accion_culminar"];
	ob_clean();
	include("../nomina_electronica_detalle_response_modal.php");
}

?>