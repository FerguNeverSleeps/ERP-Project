<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

include('../../lib/common.php');

//DATOS ACCESO ZOHO PRUEBAS CARLOS PINTO
//$organizacion_id="743814609";
//$tag_id="2583548000000000333";
//$client_id="1000.GP1SJURFLIY5GQZGCX1ZK52T5NZ3GE";
//$client_secret="5bffc28f8287461649ed2ba08cb778422f187e3620";
//$redirect_uri="http://roja/amaxonia/amaxonia_planilla/nomina/procesos/zoho_contable/";

//DATOS ACCESO MMD Servicios Integrales SA
//$organizacion_id="737781820";
//$tag_id="2510338000000000333";
//$client_id="1000.YE5AR2PHVTYMRJMFNDUW6QL4NPE6HF";
//$client_secret="67c98a6b58e1334d3ffafa1711334bbb886a8a1b6e";
//$redirect_uri="http://planilla.mmdapp.net:8080/nomina/procesos/zoho_contable/";

$conexion = new bd($_SESSION['bd']);
$sql = "
	SELECT 
		id_configuracion,
		id_organizacion,
		tags_id,
		cliente_id,
		cliente_secreto,
		url_autorizada
    FROM zoho_contable_parametros 
";

$res=$conexion->query($sql);
$configuracion=$res->fetch_array();

if(!isset($configuracion["id_configuracion"])){
	print "<br>Debe establecer los parametros de configuración de zoho.";
	exit;
}

$organizacion_id = "";
$tag_id          = "";
$client_id       = "";
$client_secret   = "";
$redirect_uri    = "";

if(isset($configuracion["id_organizacion"]) and $configuracion["id_organizacion"])
	$organizacion_id    = trim($configuracion["id_organizacion"]);
if(isset($configuracion["tags_id"]) and $configuracion["tags_id"])
	$tag_id             = trim($configuracion["tags_id"]);
if(isset($configuracion["cliente_id"]) and $configuracion["cliente_id"])
	$client_id          = trim($configuracion["cliente_id"]);
if(isset($configuracion["cliente_secreto"]) and $configuracion["cliente_secreto"])
	$client_secret      = trim($configuracion["cliente_secreto"]);
if(isset($configuracion["url_autorizada"]) and $configuracion["url_autorizada"])
	$redirect_uri       = trim($configuracion["url_autorizada"]);

if(!$organizacion_id){
	print "<br>Debe definir el parametro zoho 'ID ORGANIZACION'.";
	exit;
}
if(!$tag_id){
	print "<br>Debe definir el parametro zoho 'TAGS ID'.";
	exit;
}
if(!$client_id){
	print "<br>Debe definir el parametro zoho 'CLIENTE ID'.";
	exit;
}
if(!$client_secret){
	print "<br>Debe definir el parametro zoho 'CLIENTE SECRETO'.";
	exit;
}
if(!$redirect_uri){
	print "<br>Debe definir el parametro zoho 'URL AUTORIZADA'.";
	exit;
}


//si recibe codnom y codtip
//buscar los datos de la nomina

//$_GET["codnom"]=46;
//$_GET["codtip"]=1;

//OPCION PARA REENVIAR EL PDF ADJUNTO CUANDO FALLA (setea las variables de session y redirecciona para la obtencion del token de acceso, posteriormente cae en el condicional $_GET["code"])
if(isset($_GET["accion"]) and $_GET["accion"]=="adjuntar" and $_GET["journal_id"] and $_GET["pdf_generado"]){

	$_SESSION["sw_adjuntar"]=[
		"journal_id"   => $_GET["journal_id"],
		"pdf_generado" => $_GET["pdf_generado"]
	];

	header("Location: https://accounts.zoho.com/oauth/v2/auth?scope=ZohoBooks.fullaccess.all&client_id={$client_id}&state=testing&response_type=code&redirect_uri={$redirect_uri}&access_type=offline");
	exit;
}

//OPCION PARA REENVIAR EL XLS ADJUNTO CUANDO FALLA (setea las variables de session y redirecciona para la obtencion del token de acceso, posteriormente cae en el condicional $_GET["code"])
if(isset($_GET["accion"]) and $_GET["accion"]=="adjuntar" and $_GET["journal_id"] and $_GET["xls_generado"]){

	$_SESSION["sw_adjuntar"]=[
		"journal_id"   => $_GET["journal_id"],
		"xls_generado" => $_GET["xls_generado"]
	];

	header("Location: https://accounts.zoho.com/oauth/v2/auth?scope=ZohoBooks.fullaccess.all&client_id={$client_id}&state=testing&response_type=code&redirect_uri={$redirect_uri}&access_type=offline");
	exit;
}


//OPCION PARA ANULAR/ELIMNAR EL COMPROBANTE  (setea las variables de session y redirecciona para la obtencion del token de acceso, posteriormente cae en el condicional $_GET["code"])
if(isset($_GET["accion"]) and $_GET["accion"]=="eliminar" and $_GET["id_zoho_contable_cabecera"]){

	$_SESSION["sw_eliminar"]=[
		"id_zoho_contable_cabecera"   =>$_GET["id_zoho_contable_cabecera"]
	];

	header("Location: https://accounts.zoho.com/oauth/v2/auth?scope=ZohoBooks.fullaccess.all&client_id={$client_id}&state=testing&response_type=code&redirect_uri={$redirect_uri}&access_type=offline");
	exit;
}


///CUANDO RECIBE EL CODNOM Y CODTIP, SE GENERA EL COMPROBANTE PARA ENVIARLO A ZOHO, UNA VEZ GENERADO SE HACE LA REDIRECCION PARA OBTENER EL TOKEN DE ACCESO A ZOHO Y CAE EN EL CONDICIONAL $_GET["code"]
if(isset($_GET['codnom']) && isset($_GET['codtip'])){
	include_once("zoho_contable_comprobante.php");

	$tipo_comprobante="1";//default 1     Valores [ 1=Planilla | 2=ACH | 3=Acreedores ]
	if(isset($_GET["tipo_comprobante"]) and $_GET["tipo_comprobante"]){
		if($_GET["tipo_comprobante"]=="1")
			$tipo_comprobante="1";
		else if($_GET["tipo_comprobante"]=="2")
			$tipo_comprobante="2";
		else if($_GET["tipo_comprobante"]=="3"){
			$tipo_comprobante="3";
			print "Quitar cuando se defina el caso tipo=3";exit;
		}
		else {
			print "Parametro tipo_comprobante inválido. Valores [ 1=Planilla | 2=ACH | 3=Acreedores ]";
			exit;
		}
	}

	$_SESSION["ZOHO_JSON_DATA"]="";

	if($tipo_comprobante=="1"){
		include_once("generar_planilla_xls.php");
		$codnom=$_GET['codnom'];
		$codtip=$_GET['codtip'];	

		$sql2 = "
			SELECT 
				np.codnom
	        FROM nom_nominas_pago np 
	        WHERE  
	        	np.codnom=".$codnom." AND 
	        	np.tipnom=".$codtip;

		$res2=$conexion->query($sql2);
		$fila2=$res2->fetch_array();

		if(!isset($fila2["codnom"])){
			print "Registro no encontrado en nom_nominas_pago.";
			exit;
		}

		//print_r($fila2);

		//ARMAR AQUI EL COPMPROBANTE A ENVIAR
		$tmp=comprobante_contable_planilla($codnom,$codtip);
		//print_r($tmp["detalle"]);exit;	

		//generar pdf del asiento
		$pdf_generado=comprobante_contable_pdf($tmp);
		$xls_generado=generar_planilla_xls($codnom,$codtip);		
	}
	else if($tipo_comprobante=="2"){
		$codnom=$_GET['codnom'];
		$codtip=$_GET['codtip'];	

		$sql2 = "
			SELECT 
				np.codnom
	        FROM nom_nominas_pago np 
	        WHERE  
	        	np.codnom=".$codnom." AND 
	        	np.tipnom=".$codtip;

		$res2=$conexion->query($sql2);
		$fila2=$res2->fetch_array();

		if(!isset($fila2["codnom"])){
			print "Registro no encontrado en nom_nominas_pago.";
			exit;
		}

		$tmp=comprobante_contable_ach($codnom,$codtip);		
		$pdf_generado=comprobante_contable_pdf($tmp);
		$xls_generado="";
	}
	else {//si es 3=Acreedores

	}



	if(isset($_REQUEST["id_zoho_contable_cabecera"]) and $_REQUEST["id_zoho_contable_cabecera"]){
		$id_zoho_contable_cabecera=$_REQUEST["id_zoho_contable_cabecera"];

		$sql="select estatus from zoho_contable_cabecera where id='$id_zoho_contable_cabecera'";
		$res2=$conexion->query($sql);
		$fila2=$res2->fetch_array();
		if($fila2["estatus"]!="0"){
			print "<br>El comprobante debe encontrarse en estatus pendiente.";
			exit;
		}

		//borrar registro anterior, si existe
		$sql="delete from zoho_contable_detalle where id_cabecera='$id_zoho_contable_cabecera'";
		$conexion->query($sql);

		//$sql="delete from zoho_contable_cabecera where id='$id_zoho_contable_cabecera'";
		//$conexion->query($sql);
		$sql="
			update zoho_contable_cabecera 
			set 
				descripcion='".$tmp["descripcion"]."',
				estatus='0',
				estatus_zoho=NULL
			where
				id='$id_zoho_contable_cabecera'
		";
		$conexion->query($sql);
	}
	else{
		//si es nuevo, verificar que no exista un registro previo segun el tipo
		if($tipo_comprobante=="1" or $tipo_comprobante=="2"){
			$sql="select id, estatus from zoho_contable_cabecera where tipo_planilla='$codtip' and cod_planilla='$codnom' and tipo='$tipo_comprobante' and estatus!=2";
			$res3=$conexion->query($sql);
			$existe=$res3->fetch_array();
			if(isset($existe["id"]) and $existe["id"]){
				print "La planilla ya posee comprobante.<br>Puede ubicarlo en la sección de '".($existe["estatus"]=="0"?"fallidas":"procesadas")."' bajo el id '".$existe["id"]."'.";
				exit;
			}
		}
		else{//para el tipo=3 (acreedores)

		}



		$res2=$conexion->query("select ifnull(max(id)+1,1) id from zoho_contable_cabecera");
		$fila2=$res2->fetch_array();
		$id_zoho_contable_cabecera=$fila2["id"];	

		//insertar la cabecera
		$sql="
			INSERT INTO zoho_contable_cabecera(
				id,
				id_planilla,
				cod_planilla,
				tipo_planilla,
				id_zoho,
				cod_zoho,
				descripcion,
				estatus,
				estatus_zoho,
				mensaje_zoho,
				tipo_asiento,
				fecha_creacion,
				usuario_creacion,
				tipo
			) 
			VALUES (
				'$id_zoho_contable_cabecera',
				'$codnom',
				'$codnom',
				'$codtip',
				'',
				'',
				'".$tmp["descripcion"]."',
				'0',
				NULL,
				'',
				'',
				'".date("Y-m-d H:i:s")."',
				'".$_SESSION["usuario"]."',
				'$tipo_comprobante'
			)
		";

		$conexion->query($sql);
	}




	//$res2=$conexion->query("select LAST_INSERT_ID() id");
	//$fila2=$res2->fetch_array();
	//$id_zoho_contable_cabecera=$conexion->insert_id;
	//$id_zoho_contable_cabecera = mysqli_insert_id($conexion);



	//print "paso";
	//exit;
	$suma_debito=0;
	$suma_credito=0;

	$line_items=[];
	//for($i=0; $i < count($tmp["detalle"]); $i++){ 
	foreach($tmp["detalle"] as $i => $value) {
		$monto=0;
		$operacion="";
		if(isset($tmp["detalle"][$i]["credito"]) and $tmp["detalle"][$i]["credito"]*1!==0){
			$operacion="credit";
			$monto=$tmp["detalle"][$i]["credito"];
		}
		else if(isset($tmp["detalle"][$i]["debito"]) and $tmp["detalle"][$i]["debito"]*1!==0){
			$operacion="debit";
			$monto=$tmp["detalle"][$i]["debito"];
		}
		else {
			if(isset($tmp["detalle"][$i]["credito"])){
				$operacion="credit";
				$monto=$tmp["detalle"][$i]["credito"];
			}
			else if(isset($tmp["detalle"][$i]["debito"])){
				$operacion="debit";
				$monto=$tmp["detalle"][$i]["debito"];
			}
		}

		if($monto==0 and !$operacion and !$tmp["detalle"][$i]["cuenta"]){
			continue;
		}

		if($operacion=="debit")
			$suma_debito+=$monto;
		if($operacion=="credit")
			$suma_credito+=$monto;

		$line_items[]=[
			"account_id"      => $tmp["detalle"][$i]["cuenta"],
			"amount"          => $monto,
			"debit_or_credit" => "$operacion",
			"description"     => $tmp["detalle"][$i]["concepto"],
			"markar"          => $tmp["detalle"][$i]["markar"],
			"departamento_id" => $tmp["detalle"][$i]["departamento_id"]
		];

		$sql="
			INSERT INTO zoho_contable_detalle (
				id_cabecera,
				id_cuenta,
				cod_cuenta,
				nombre_cuenta,
				concepto,
				monto,
				tipo,
				id_centro_costo,
				cod_centro_costo,
				nombre_centro_costo,
				cod_centro_costo_zoho
			)
			VALUES (
				'$id_zoho_contable_cabecera',
				'".$tmp["detalle"][$i]["cuenta_id"]."',
				'".$tmp["detalle"][$i]["cuenta"]."',
				'".$tmp["detalle"][$i]["cuenta_descripcion"]."',
				'".$tmp["detalle"][$i]["concepto"]."',
				'$monto',
				'".($operacion=="debit"?"D":"C")."',
				'".$tmp["detalle"][$i]["departamento_id"]."',
				'".$tmp["detalle"][$i]["markar"]."',
				'".$tmp["detalle"][$i]["departamento"]."',
				''
			)
		";
		$conexion->query($sql);
	}

	$_SESSION["ZOHO_JSON_DATA"]=[
		"id_zoho_contable_cabecera" => "$id_zoho_contable_cabecera",
		"journal_date"              => $tmp["fecha_pago"],
		"journal_type"              => "both",
		"notes"                     => $tmp["descripcion"],
		"reference_number"          => substr($tmp["descripcion"],0,100),
		"line_items"                => $line_items,
		//"status"                    => "draft",
		"status"                    => "published",
		"pdf_generado"              => "$pdf_generado",
		"xls_generado"              => "$xls_generado"
	];



	//print("Debito=$suma_debito   Credito=$suma_credito<BR>");
	//print_r($_SESSION["ZOHO_JSON_DATA"]);exit;

	//OBTENER EL CODIGO DE ACCESO A LA APLICACIÓN
	header("Location: https://accounts.zoho.com/oauth/v2/auth?scope=ZohoBooks.fullaccess.all&client_id={$client_id}&state=testing&response_type=code&redirect_uri={$redirect_uri}&access_type=offline");
	exit;
}



//CONDICIONAL  $_GET["code"]
//UNA VEZ VERIFICADAS LAS CREDENCIALES ZOHO, SE HACE LA REDIRECCION CON  $_GET["code"], codigo de acceso, el cual sera usado para realizar todas la peticiones a zoho.
if(isset($_GET["code"]) and $_GET["code"]){
	include_once("zoho_contable_funciones.php");
	$access_code=$_GET["code"];

	//OBTENER EL TOKEN DE ACCESO, a partir del codigo de acceso.
	$retorno=zoho_access_token($access_code, $client_id, $client_secret, $redirect_uri);
	$access_token  = $retorno["access_token"];
	$refresh_token = $retorno["refresh_token"];

	if(!$access_token){
		print "<br>No pudo obtener el token de acceso / codigo de acceso inválido.<br>";
		//print_r($retorno);
		exit;
	}

	//OBTENER EL ID DE LA ORGANIZACION, SIN NO SE ESPECIFICA
	if(!$organizacion_id){
		$retorno=zoho_get_organization($access_token);
		$organizacion_id="";
		if(isset($retorno["code"]) and $retorno["code"]=="0" and isset($retorno["organizations"][0]["organization_id"]) and $retorno["organizations"][0]["organization_id"]){
			$organizacion_id=$retorno["organizations"][0]["organization_id"];
		}
		else{
			print "<br>No pudo obtener el identificador de la organizacion.<br>";
			//print_r($retorno);
			exit;
		}
	}	

	//SI ES REENVIAR EL ADJUNTO
	if($_SESSION["sw_adjuntar"]){
		print "<br>Asiento Id #".$_SESSION["sw_adjuntar"]["journal_id"];

		if($_SESSION["sw_adjuntar"]["pdf_generado"]){
			$retorno=zoho_journals_attachment($access_token,$organizacion_id,$_SESSION["sw_adjuntar"]["journal_id"],$_SESSION["sw_adjuntar"]["pdf_generado"],"pdf");
			if(isset($retorno["code"]) and $retorno["code"]=="0"){
				print "<br>Documento PDF adjuntado correctamente.";
			}
			else{
				//print_r($retorno);			
				print "<br><br>Error al adjuntar el archivo PDF: <a href='index.php?accion=adjuntar&journal_id=".$_SESSION["sw_adjuntar"]["journal_id"]."&pdf_generado=".$_SESSION["sw_adjuntar"]["pdf_generado"]."'>Intentar Adjuntar Nuevamente</a>";
			}			
		}
		if($_SESSION["sw_adjuntar"]["xls_generado"]){
			$retorno=zoho_journals_attachment($access_token,$organizacion_id,$_SESSION["sw_adjuntar"]["journal_id"],$_SESSION["sw_adjuntar"]["xls_generado"],"xls");
			if(isset($retorno["code"]) and $retorno["code"]=="0"){
				print "<br>Documento XLS adjuntado correctamente.";
			}
			else{
				//print_r($retorno);			
				print "<br><br>Error al adjuntar el archivo XLS: <a href='index.php?accion=adjuntar&journal_id=".$_SESSION["sw_adjuntar"]["journal_id"]."&xls_generado=".$_SESSION["sw_adjuntar"]["xls_generado"]."'>Intentar Adjuntar Nuevamente</a>";
			}			
		}

		$_SESSION["sw_adjuntar"]=NULL;
		exit;
	}

	$_SESSION["sw_adjuntar"]=NULL;

	//SI ES ELIMINAR/ANULAR COMPROBANTE
	if($_SESSION["sw_eliminar"]){
		//buscar journal_id ($id_zoho)
		//buscar si el campo id_zoho, se encuentra lleno para borrar el comprabante en zoho
		$id_zoho_contable_cabecera=$_SESSION["sw_eliminar"]["id_zoho_contable_cabecera"];
		$sql="select id_zoho from zoho_contable_cabecera where id='$id_zoho_contable_cabecera'";
		$res=$conexion->query($sql);
		$tmp=$res->fetch_array();
		if(isset($tmp["id_zoho"]) and $tmp["id_zoho"]){
			zoho_journals_delete($access_token,$organizacion_id,$tmp["id_zoho"]);
		}
		$sql="
			update 
				zoho_contable_cabecera 
			set 
				estatus='2', 
				fecha_anulacion='".date("Y-m-d H:i:s")."',
				usuario_anulacion='".$_SESSION["usuario"]."' 
			where 
				id='$id_zoho_contable_cabecera'
		";
		$conexion->query($sql);

		print "<br>Asiento Anulado con Éxito";
		$_SESSION["sw_eliminar"]=NULL;
		exit;
	}

	//OBTENER EL LISTADO DE CUENTAS CONTABLES ZOHO PARA HACER EL PASE CON LA CTA DE AMAXONIA
	$cuentas_contables=[];
	$retorno=zoho_get_chartofaccounts($access_token,$organizacion_id);
	if(isset($retorno["code"]) and $retorno["code"]=="0" and isset($retorno["chartofaccounts"])){
		$cuentas_contables=$retorno["chartofaccounts"];
	}
	else{
		print "<br>No pudo obtener el listado de cuentas contables [$organizacion_id].<br>";
		print_r($retorno);
		exit;
	}

	//OBTENER LOS TAGS
	$retorno=zoho_get_tags($access_token,$organizacion_id,$tag_id);
	if(isset($retorno["code"]) and $retorno["code"]=="0" and isset($retorno["reporting_tag"]["tag_options"])){
		$tags=$retorno["reporting_tag"]["tag_options"];
	}
	else{
		print "<br>No pudo obtener el listado de tags para los centros de costos.<br>";
		//print_r($retorno);
		exit;
	}


	//DATA OBTENIDA EN EL PRIMER LLAMADO, CUANDO SE ENVIA codnom y tipnom
	$json_data=$_SESSION["ZOHO_JSON_DATA"];	
	$pdf_generado=$json_data["pdf_generado"];
	unset($json_data["pdf_generado"]);//quitar la variable pdf_generado del json de envio al servidor del zoho

	$xls_generado=$json_data["xls_generado"];
	unset($json_data["xls_generado"]);//quitar la variable xls_generado del json de envio al servidor del zoho

	$id_zoho_contable_cabecera=$json_data["id_zoho_contable_cabecera"];
	unset($json_data["id_zoho_contable_cabecera"]);//quitar la variable pdf_generado del json de envio al servidor del zoho

	//buscar si el campo id_zoho, se encuentra lleno para borrar el comprabante en zoho
	$sql="select id_zoho from zoho_contable_cabecera where id='$id_zoho_contable_cabecera'";
	$res=$conexion->query($sql);
	$tmp=$res->fetch_array();
	if(isset($tmp["id_zoho"]) and $tmp["id_zoho"]){
		zoho_journals_delete($access_token,$organizacion_id,$tmp["id_zoho"]);
	}

	//array para llenar con la cuenta contable amaxonia y el account_id de zoho, para luego hacer update en el detalle del comprobante amaxonia
	$cuenta_coincidencias=[];
	//buscar el account_id en $cuentas_contables a partir del codigo
	for($i=0; $i<count($json_data["line_items"]); $i++) { 
		$encontro=false;
		for($j=0; $j<count($cuentas_contables) and $encontro==false; $j++) { 
			if(trim($json_data["line_items"][$i]["account_id"])==trim($cuentas_contables[$j]["account_code"])){	
				if(!isset($cuenta_coincidencias[$cuentas_contables[$j]["account_code"]]))
					$cuenta_coincidencias[$cuentas_contables[$j]["account_code"]]=$cuentas_contables[$j]["account_id"];
				$json_data["line_items"][$i]["account_id"]=$cuentas_contables[$j]["account_id"];
				$encontro=true;
			}
		}

		if(!$encontro){
			print "No encontro la cuenta contable con el codigo ".$json_data["line_items"][$i]["account_id"]." en zoho.";
			exit;
		}
	}

	//array para llenar con los centros de costos/departamentos de amaxonia y el tag_option_id de zoho, para luego hacer update en el detalle del comprobante amaxonia
	$tags_coincidencias=[];
	//asignarle el tag a cada linea
	for($i=0; $i<count($json_data["line_items"]); $i++) { 
		$encontro=false;
		for($j=0; $j<count($tags) and $encontro==false; $j++) { 
			if(strtoupper(trim($json_data["line_items"][$i]["markar"]))!="" and strtoupper(trim($json_data["line_items"][$i]["markar"]))==strtoupper(trim($tags[$j]["tag_option_name"]))){
				$json_data["line_items"][$i]["tags"]=[];
				$json_data["line_items"][$i]["tags"][]=[
					"tag_id"          => "$tag_id",
					"tag_option_id"   => $tags[$j]["tag_option_id"]
				];
				$encontro=true;
				if(!isset($tags_coincidencias[$json_data["line_items"][$i]["departamento_id"]])){
					$tags_coincidencias[$json_data["line_items"][$i]["departamento_id"]]=$tags[$j]["tag_option_id"];
				}
			}
		}
		unset($json_data["line_items"][$i]["markar"]);//quitar la variable markar del json de envio
		unset($json_data["line_items"][$i]["departamento_id"]);//quitar la variable departamento_id del json de envio
	}

	//hacer update en el campo cod_centro_costo_zoho de la tabla zoho_contable_detalle, con el tag_option_id con el equivalente al centro de costo encontrado en zoho
	foreach($tags_coincidencias as $key => $value) {
		$sql="update zoho_contable_detalle set cod_centro_costo_zoho='$value' where id_centro_costo='$key' and id_cabecera='$id_zoho_contable_cabecera'";
		$conexion->query($sql);
	}

	//hacer update en el campo id_cuenta_zoho de la tabla zoho_contable_detalle, con el account_id del equivalente a la cuenta contable de amaxonia
	foreach($cuenta_coincidencias as $key => $value) {
		$sql="update zoho_contable_detalle set id_cuenta_zoho='$value' where cod_cuenta='$key' and id_cabecera='$id_zoho_contable_cabecera'";
		$conexion->query($sql);
	}


	/*
	//asiento estatico para pruebas
	$json_data=[
		"journal_date"            => "2021-03-05",
	    //"reference_number"        => "20210305000002",
	    //"notes"                   => "Loan repayment",
	    "journal_type"            => "both",
	    //"currency_id"             => "460000000000097",
	    //"exchange_rate"           => 1,
	    "line_items" => [
	        [
	            "account_id"      => "2583548000000000367",
	            //"account_id"      => "1230000000",
	            //"line_id"         => "202103050000021",
	            "amount"          => 500,
	            "debit_or_credit" => "credit",
	            //"project_id"      => "460000000898001",	            
	        ],
	        [
	            "account_id"      => "2583548000000000367",
	            //"line_id"         => "20210305000002",
	            //"account_id"      => "1230000000",
	            "amount"          => 500,
	            "debit_or_credit" => "debit",
	            //"project_id"      => "460000000898001",	            
	        ]
	    ]	    
	];
	*/

	//CREAR EL ASIENTO EN ZOHO
	$retorno = zoho_journals_create($access_token, $organizacion_id, $json_data);
	$_SESSION["ZOHO_JSON_DATA"]="";
	if(isset($retorno["code"]) and $retorno["code"]=="0"){
		print "<br>Asiento registrado bajo el Id #".$retorno["journal"]["journal_id"];

		$sql="
			update zoho_contable_cabecera 
			set 
				estatus='1',
				estatus_zoho='".$retorno["code"]."',  
				mensaje_zoho='".$retorno["message"]."',
				id_zoho='".$retorno["journal"]["journal_id"]."',  
				cod_zoho='".$retorno["journal"]["entry_number"]."',  
				tipo_asiento='".$retorno["journal"]["journal_type"]."'
			where 
				id='$id_zoho_contable_cabecera'
		";
		$conexion->query($sql);
		
		//ADJUNTAR ARCHIVO
		if($pdf_generado){
			$retorno_pdf=zoho_journals_attachment($access_token,$organizacion_id,$retorno["journal"]["journal_id"],$pdf_generado,"pdf");
			if(isset($retorno_pdf["code"]) and $retorno_pdf["code"]=="0"){
				print "<br>Documento PDF adjuntado correctamente.";
			}
			else{
				//print_r($retorno);			
				print "<br><br>Error al adjuntar el archivo PDF: <a href='index.php?accion=adjuntar&journal_id=".$retorno["journal"]["journal_id"]."&pdf_generado=".$pdf_generado."' target='_blank'>Intentar Adjuntar Nuevamente</a>";
			}			
		}

		if($xls_generado){
			$retorno_xls=zoho_journals_attachment($access_token,$organizacion_id,$retorno["journal"]["journal_id"],$xls_generado,"xls");
			if(isset($retorno_xls["code"]) and $retorno_xls["code"]=="0"){
				print "<br>Documento XLS adjuntado correctamente.";
			}
			else{
				//print_r($retorno);			
				print "<br><br>Error al adjuntar el archivo XLS: <a href='index.php?accion=adjuntar&journal_id=".$retorno["journal"]["journal_id"]."&xls_generado=".$xls_generado."' target='_blank'>Intentar Adjuntar Nuevamente</a>";
			}
		}
		
	}
	else{
		print "<br>Registro del asiento fallido.<br>";
		print "<br>Codigo: ".$retorno["code"];
		print "<br>Mensaje: ".$retorno["message"];
		//print_r($retorno);

		$sql="
			update zoho_contable_cabecera 
			set 				
				estatus_zoho='".$retorno["code"]."',  
				mensaje_zoho='".$retorno["message"]."'
			where 
				id='$id_zoho_contable_cabecera'
		";
		$conexion->query($sql);
	}	


	//revokar acceso
	$retorno=zoho_revoke_token($refresh_token);
	print_r($retorno);
	exit;
}



?>