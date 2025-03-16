<?php
//include_once("nomina_electronica.class.php");
session_start();
ob_start();
$termino = $_SESSION['termino'];
require_once "./modelo/conexion.php";
//$conexion = new db($_SESSION['bd']);
$id_cabecera=$_REQUEST["id_cabecera"];
if(!$id_cabecera){
	exit;
}

$stmt = Conexion::conectar();
$sql="SELECT estatus from emision_col_cabecera where id_cabecera='$id_cabecera'";

$select = $stmt->prepare("$sql");        

$select -> execute();
$row=$select ->fetchAll(PDO::FETCH_ASSOC);
if(isset($row["estatus"]) and $row["estatus"]=="2"){
	exit;
}

$stmt = Conexion::conectar();
echo $sql="UPDATE emision_col_cabecera 
	set 
		estatus=2,
		usuario_anulacion='".$_SESSION['usuario']."',
		fecha_anulacion='".date("Y-m-d H:i:s")."'
	WHERE id_cabecera='$id_cabecera'
";
$update = $stmt->prepare("$sql");        

$update -> execute();
exit;
//header("Location: ./nomina_individual.php?tab_active=tab_anuladas");
/*
$sql= "SELECT 
		a.*, 
		b.*,
		c.* 
    FROM emision_col_cabecera as a 
	    LEFT JOIN  nomina_electronica_detalle_request  as b ON a.id_cabecera=b.id_cabecera 
	    LEFT JOIN  nomina_electronica_detalle_response as c ON b.id_detalle_request=c.id_detalle_request
    WHERE 
    	a.id_cabecera='$id_cabecera' 
    ORDER BY 
    	b.id_detalle_request ASC
";

$nomina=[];
$i=0;

function td_decode($str){
	$tmp=json_decode(base64_decode($str),true);
	return $tmp;
}

$res=$conexion->query($sql);
while($row=$res->fetch_array()){
    
	if($row["codigo"]!="200"){
		continue;
	}

	$nomina[$i]=new AMX_NominaElectronica([
    	"tokenEnterprise"=> $token_enterprise,
    	"tokenPassword"  => $token_password,
    	"idSoftware"     => $id_software,
    	"nitEmpleador"   => $nit_empleador
    ]);

	$consecutivoDocumentoNom=substr($correlativo_anulados_prefijo, 0, 4).$correlativo_anulados_contador;

	$nomina[$i]->Asignar("consecutivoDocumentoNom", $consecutivoDocumentoNom);
	$nomina[$i]->Asignar("fechaEmisionNom",         td_decode($row["fechaEmisionNom"]));
	$nomina[$i]->Asignar("lugarGeneracionXML",      td_decode($row["lugarGeneracionXML"]));
	$nomina[$i]->Asignar("periodoNomina",           td_decode($row["periodoNomina"]));
	$nomina[$i]->Asignar("rangoNumeracionNom",      substr($correlativo_anulados_prefijo, 0, 4)."-".$correlativo_anulados_contador);
	$nomina[$i]->Asignar("redondeo",                td_decode($row["redondeo"]));
	$nomina[$i]->Asignar("tipoDocumentoNom",        "103");
	$nomina[$i]->Asignar("tipoMonedaNom",           td_decode($row["tipoMonedaNom"]));
	$nomina[$i]->Asignar("tipoNota",                "2");
	$nomina[$i]->Asignar("totalComprobante",        td_decode($row["totalComprobante"]));
	$nomina[$i]->Asignar("totalDeducciones",        td_decode($row["totalDeducciones"]));
	$nomina[$i]->Asignar("totalDevengados",         td_decode($row["totalDevengados"]));
	$nomina[$i]->Asignar("trm",                     td_decode($row["trm"]));
	$nomina[$i]->Asignar("pagos",                   td_decode($row["pagos"]));
	$nomina[$i]->Asignar("trabajador",              NULL);
	$nomina[$i]->Asignar("deducciones",             NULL);
	$nomina[$i]->Asignar("devengados",              NULL);
	$nomina[$i]->Asignar("periodos",                NULL);
	//$nomina[$i]->Asignar("novedad",                 NULL);

	$nomina[$i]->Notas(["descripcion"=>$row["descripcion"]]);

	unset($nomina[$i]->data["notas"][0]["extras"]);
	$nomina[$i]->data["notas"][0]["extrasNom"]=NULL;

	unset($nomina[$i]->data["lugarGeneracionXML"]["extras"]);
	$nomina[$i]->data["lugarGeneracionXML"]["extrasNom"]=NULL;

	unset($nomina[$i]->data["pagos"][0]["extras"]);
	$nomina[$i]->data["pagos"][0]["extrasNom"]=NULL;

	unset($nomina[$i]->data["deducciones"]);
	unset($nomina[$i]->data["devengados"]);

	$nomina[$i]->data["pagos"][0]["fechasPagos"][0]["extrasNom"]=NULL;

	$nomina[$i]->data["pagos"][0]["medioPago"]="1";
	$nomina[$i]->data["pagos"][0]["nombreBanco"]=NULL;
	$nomina[$i]->data["pagos"][0]["tipoCuenta"]=NULL;
	$nomina[$i]->data["pagos"][0]["numeroCuenta"]=NULL;

	$fechaGenPred=td_decode($row["fechaEmisionNom"]);
	$fechaGenPred=date("Y-m-d",strtotime($fechaGenPred));

	$nomina[$i]->DocumentosReferenciados([
		"extrasNom"=>NULL,
		"cunePred"=>$row["cune"],
		"fechaGenPred"=>$fechaGenPred,
		"numeroPred"=>$row["consecutivoDocumentoNom"]
	]);

	$respuesta=$nomina[$i]->Enviar();    
    //print $nomina[$i]->json();
    //print_r($respuesta);
    //exit;

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
			trabajador,
			cunePred
		) 
		VALUES (
			'$id_cabecera',
			'".$row["ficha"]."',
			'".$row["cedula"]."',
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
			'".$nomina[$i]->get_base64("trabajador")."',
			'".$row["cune"]."'
		)
	";
	$conexion->query($sql);

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
			xml,
			cunePred
		)
		VALUES (
			'$id_detalle_request',
			'$id_cabecera',
			'".$row["ficha"]."',
			'".$row["cedula"]."',
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
			'".$nomina[$i]->Respuesta()->get_base64("xml")."',
			'".$row["cune"]."'
		)
	";
	$conexion->query($sql);


    $correlativo_anulados_contador++;
	$sql="update nomina_electronica_parametros set correlativo_anulados_contador='".$correlativo_anulados_contador."'";
	$conexion->query($sql);

	$i++;
}

ob_clean();*/
exit;
//header("Location: ../nomina_electronica_proceso_nomina_general_periodos_global.php?tab_active=anulados");
?>