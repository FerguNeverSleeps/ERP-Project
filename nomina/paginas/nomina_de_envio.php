<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
ob_start();
require_once '../lib/common.php';
require_once '../libs/gcon4/soap_tmp/autoload.php';

include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=new bd($_SESSION['bd']);
$db = new bd(SELECTRA_CONF_PYME);
/* Permisos*/
$sql_user = "SELECT COUNT(*)
FROM nomusuarios as a LEFT JOIN roles as b on (b.id = a.id_rol)
WHERE a.coduser = '{$_SESSION[cod_usuario]}' and b.estado ='1' and a.id_rol='1'";
// echo $sql_user;
$es_admin    = $db->query($sql_user)->num_rows;
$es_admin    = ($es_admin == 1) ? 1 : 0 ;
$termino     = (isset($_SESSION['termino'])) ? $_SESSION['termino'] : '';
$registro_id = (isset($_POST['registro_id'])) ? $_POST['registro_id'] : '';
$op          = (isset($_POST['op'])) ? $_POST['op'] : '';
function get_client_ip() {
  $ipaddress = '';
  if (getenv('HTTP_CLIENT_IP'))
	  $ipaddress = getenv('HTTP_CLIENT_IP');
  else if(getenv('HTTP_X_FORWARDED_FOR'))
	  $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
  else if(getenv('HTTP_X_FORWARDED'))
	  $ipaddress = getenv('HTTP_X_FORWARDED');
  else if(getenv('HTTP_FORWARDED_FOR'))
	  $ipaddress = getenv('HTTP_FORWARDED_FOR');
  else if(getenv('HTTP_FORWARDED'))
	  $ipaddress = getenv('HTTP_FORWARDED');
  else if(getenv('REMOTE_ADDR'))
	  $ipaddress = getenv('REMOTE_ADDR');
  else
	  $ipaddress = 'UNKNOWN';
  return $ipaddress;
}


$add="";
if(isset($_REQUEST["search_anio"]) && $_REQUEST["search_anio"] != "Todos") {
    $add="WHERE (npc.fecha_inicio like '".$_REQUEST["search_anio"]."-%' or npc.fecha_fin like '".$_REQUEST["search_anio"]."-%')";
}


if(isset($_REQUEST["codnomina"])) {

	$sql = "SELECT np.ficha, npc.cedula, np.apenom, npc.quincena AS valor_base, npc.porcentaje, npc.cuenta_contable, npc.concepto, npc.monto AS valor_real, npc.fecha_inicio, 
							npc.fecha_fin, npc.codigo_proyecto, pc.descripcion, nmp.anio,  nmp.anio, nmp.fecha, nmp.fecha_aprobacion, nmp.fechapago, npc.tipo_cuenta, npc.codigo_concepto,
							npc.acreedor, n.codigo_proveedor AS proveedor
					FROM nomina_proyecto_cc npc
					 	INNER JOIN nompersonal np ON np.cedula = npc.cedula
					 	INNER JOIN nom_nominas_pago nmp ON nmp.codnom = npc.cod_nomina
					 	LEFT JOIN proyecto_cc pc ON pc.codigo = npc.codigo_proyecto
            LEFT JOIN nomprestamos n ON n.codigopr = npc.acreedor
				 	WHERE npc.cod_nomina = ".$_REQUEST['codnomina']."
					ORDER BY npc.cod_nomina DESC, npc.cedula, npc.ID ASC";
	        // echo $sql;
	        // exit;
	$res = $conexion->query($sql, "utf8");
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

  
  // URL del servidor SOAP - Produccion
  $wsdl = 'https://ubw.unit4cloud.com/co_ses_prod_webservices/service.svc?BatchInputService/BatchInput';

  // URL del servidor SOAP - Pruebas
  // $wsdl = 'https://ubw-accept01.unit4cloud.com/co_ses_acpt_webservices/service.svc?BatchInputService/BatchInput';
  
  // Crear el cliente SOAP
  $client = new SoapClient($wsdl, array('trace' => 1, 'exception' => 0));

  // Crea una instancia del cliente BatchInput
  // $batchInputClient = new BatchInput();

  // Ahora, crea un array de objetos BatchInputDTO y añádelo a batchInput
  $batchInputDTOs = array();

	// $num_filas = $res->num_rows;
	$anioMes = '';
	$anioMesDia = '';
	$i = 0;
	// for ($i = 0; $i < $num_filas; $i++) {
	while ($fila = $res->fetch_assoc()) {

		// Validar que el código de colaborador tenga exactamente 5 dígitos
		if (strlen($fila['ficha']) < 5) {
		  $colaborador_codigo = '1' . str_pad($fila['ficha'], 4, '0', STR_PAD_LEFT);
		}
		else {
			$colaborador_codigo = $fila['ficha'];
		}

		$aparType = "P";
		if ($fila['acreedor'] == 0) {
			$aparId = "PAN".$colaborador_codigo;
		}
		else {
			$aparId = $fila['proveedor'];
		}


		$VoucherType = "NM";
		if (in_array($fila['codigo_concepto'], [26, 27, 28, 29, 30, 31, 32, 33, 3000, 3001, 3002, 9005, 9006, 9007, 9008, 9009])) {
			$VoucherType = "PR";
		}

		$cuenta = substr($fila['cuenta_contable'], 0, 1);
		$dim2 = null;
		$transType = "AP";

		if ($cuenta == '7' || $cuenta == '5' || (in_array($fila['cuenta_contable'], [25200102, 25100102, 25250102])) ) {
			$dim4 = $fila['codigo_proyecto'];
			$transType = "GL";
			if ($cuenta == '5') {
		  	$dim2 = $fila['codigo_proyecto'];
				$dim4 = null;
			}

			$dim5 = "P".$aparId;
		}
		else {
		  $dim4 = "PAN".$colaborador_codigo;
			$dim5 = null;
		}
		
	  $dim3 = "PAN".$colaborador_codigo;
		$dim7 = 1;

		if ($fila['tipo_cuenta'] == 'A' || $fila['tipo_cuenta'] == 'P') {
			$tipoCuenta = 1; //Debido
		}
		else {
			$tipoCuenta = -1; //Credito
		}

		$anioMes = date("Ym", strtotime($fila['fecha']));
		$anioMesDia = date("Ymd", strtotime($fila['fecha']));

    // Crea el primer objeto BatchInputDTO con los datos del primer bloque de XML
    $batchInputDTO = new BatchInputDTO(
			$fila['cuenta_contable'],
			0,
			$fila['valor_real'],
			$aparId,
			$aparType,
			new \DateTime($fila['fecha'].'T'.date("H:i:s")),
			$i,
			$fila['valor_real'],
			$fila['valor_real'],
			"PAN",
			false,
			new \DateTime($fila['fecha'].'T'.date("H:i:s")),
			$fila['valor_real'],
			"PAB",
			$tipoCuenta,
			$fila['concepto'],
			false,
			$dim2,
			$dim3,
			$dim4,
			$dim5,
			substr($fila['cuenta_contable'], 0, 4),
			$dim7,
			new \DateTime($fila['fecha'].'T'.date("H:i:s")),
			0,
			new \DateTime($fila['fechapago'].'T'.date("H:i:s")),
			0,
			0.00,
			0.00,
			0.00,
			"NM-".$anioMes,
			"BI",
			0,
			0,
			false,
			0,
			0,
			0,
			"NOMINA",
			$i,
			$i,
			0,
			false,
			new \DateTime($fila['fecha'].'T'.date("H:i:s")),
			$transType,
			0.00,
			0.00,
			0.00,
			new \DateTime($fila['fecha'].'T'.date("H:i:s")),
			0,
			0,
			$VoucherType);

	    // Agrega el BatchInputDTO al array de batchInputDTOs
	    $batchInputDTOs[] = $batchInputDTO;
	    $i++;
	}

	// var_dump($batchInputDTOs);
  // Crear el objeto ArrayOfBatchInputDTO y asignarle el array de objetos BatchInputDTO
  $arrayOfBatchInputDTO = new ArrayOfBatchInputDTO();
  $arrayOfBatchInputDTO->setBatchInputDTO($batchInputDTOs);

  // Crear el objeto BatchInputProcessType
  $batchInputProcessType = new BatchInputProcessType('98');
  // Crear la lista de ProcessParameters
  $parameterList = new ArrayOfProcessParameters();
  //Creamos un numero random
  $numberRand = rand(1, 99);

  // Agregar los parámetros a la lista
  $parameter = new ProcessParameters();
  $parameter->setName('batch_id');
  $parameter->setValue('NM-'.$anioMesDia.'-'.$numberRand);
  $parameterList->setProcessParameters([$parameter]);

  // Asignar la lista de parámetros al objeto BatchInputProcessType
  $batchInputProcessType->setParameterList($parameterList);

  // Crear el objeto WSCredentials y configurar las credenciales
  $wsCredentials = new WSCredentials();
  $wsCredentials->setUsername('nomina');
  $wsCredentials->setClient('PAN');
  $wsCredentials->setPassword('hqusZCC4gC!iW2');

  // Crear el objeto SaveTransactions y asignarle los datos
  $saveTransactions = new SaveTransactions(
      $arrayOfBatchInputDTO,
      $batchInputProcessType,
      $anioMes,
      'NM-'.$anioMesDia.'-'.$numberRand,
      $wsCredentials
  );

	// echo '<pre>';
	// print_r($saveTransactions);
	// echo '</pre>';
  // return false;
  // Crear una instancia de la clase BatchInput pasando las opciones
  $batchInput = new BatchInput();
  try {
      // Código para llamar al método del Web Service
      // Por ejemplo:
      $response = $batchInput->SaveTransactions($saveTransactions);
      // return $response;

      $saveTransactionsResponse = new SaveTransactionsResponse($response);

			$batchInputSaveResponse = $response->getSaveTransactionsResult();

			// Accede al valor de ReturnCode en BatchInputSaveResponse y lo imprime
			$returnCode = $batchInputSaveResponse->getReturnCode();

      // // Verifica si la conversión fue exitosa y si ReturnCode es igual a 200
      if ($returnCode == 200) {
          // Mostrar OrderNo si ReturnCode es 200
          echo '<div class="alert alert-success" role="alert">
			  					<strong> Operación exitosa, número de order generado: ' . $batchInputSaveResponse->getOrderNo() . '</strong>
								</div>';
      } 
      else if ($returnCode == 400) {
          echo "La Nomina ya fue cargada con anterioridad.";
      }
      else {
          // Manejar el caso en que ReturnCode no sea igual a 200
          echo "La operación no tuvo éxito.";
      }

      echo "<pre>";
      print_r($response);
      echo "</pre>";
      // $metodoResultado = json_decode($respuesta);
      // echo "<script>console.log('".$metodoResultado."')</script>";
	 	
			// var_dump($saveTransactionsResponse);

  } catch (SoapFault $e) {
      $errorMessage = $e->getMessage();
      $errorCode = $e->getCode();
      $errorFile = $e->getFile();
      $errorLine = $e->getLine();
      $errorTrace = $e->getTraceAsString();

      echo "Error en la llamada al Web Service: $errorMessage (Código: $errorCode)\n";
      echo "Archivo: $errorFile\n";
      echo "Línea: $errorLine\n";
      echo "Traza de la pila de llamadas:\n$errorTrace\n";
  }


}


?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .text-middle{
  	vertical-align: middle !important
  }

  .ajustar-texto
  {
  	white-space: normal !important;
  }

  td.icono
  {
  	padding-left: 0px !important;
  	padding-right: 0px !important;
  	width: 10px !important;
  }
</style>
<script type="text/javascript" src="../lib/common.js"></script>
<script type="text/javascript">
function GenerarNomina()
{
	AbrirVentana('barraprogreso_1.php', 150, 500, 0);
}

function CerrarVentana()
{
	javascript:window.close();
}

function showProcesando(){/*App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
	});*/
	
	$('#blockui_portlet_body').block({ 
		boxed: true,
        message: 'Procesando'
	});
}
var fnGet = function (url, data) {
	var objeto;
	$.ajax({
		url: url,
		data: data,
		dataType: "JSON",
		success: function (resultado) {
			objeto = resultado;
		},
		"type": "GET",
		"cache": false,
		"async": false,
		"error": function (resultado) {
			alert("Error");
		}
	});
	return objeto;
};

function enviar(op,id,nomina,codtip)
{
	if (op==1){		// Opcion de Agregar
		//document.frmAgregar.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="ag_nomina_pago.php";
		document.frmPrincipal.submit();
	}
	if (op==2){	 	// Opcion de Modificar
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.op.value=op;
		document.frmPrincipal.action="ag_nomina_pago.php";
		document.frmPrincipal.submit();
	}
	if (op==3){		// Opcion de Eliminar
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
		{
			document.frmPrincipal.registro_id.value=id;
			document.frmPrincipal.op.value=op;
  			document.frmPrincipal.submit();
		}
	}

	if (op==4){		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('barraprogreso_1.php?registro_id='+id+'&codigo_nomina='+nomina,250,900,0);
	}

	if (op==5){	// Movimiento de Nómina
		document.location.href="movimientos_nomina_pago.php?codigo_nomina="+id+"&codt="+codtip

	}
	if (op==6){	//CERRAR NÓMINA
			showProcesando();
		if (confirm("\u00BFEst\u00E1 seguro que desea cerrar esta <?php echo $termino; ?>?"))
		{
			var data = {};
			data.codigo_nomina = id;
			var Fichas = fnGet("cerrar_nomina.php",data);
			console.log(Fichas);
			if (Fichas.Estado === "0") {
				//ModalListaFichas();
				var lista = $('#listarFichasNegativo');
				var tablaFichas = '<table class="table table-striped table-bordered table-hover">';
				tablaFichas += '<thead><tr><td>FICHA</td><td>NOMBRE</td><td>NETO</td><td>NETO 3</td></tr><thead><tbody>';

				$('#listarFichasNegativo').empty();
				Fichas.data.forEach(function (item, index, array) {
					tablaFichas += '<tr> <td>' + item.ficha + '</td><td>' + item.apenom + '</td><td>' + item.neto + '</td><td>' + item.neto3 + '</td></tr>';
				});
				tablaFichas += '</tbdoy></table>';
				$('#listarFichasNegativo').html(tablaFichas);
            	$('#ModalFichas').modal('show');
				$('#blockui_portlet_body').unblock(); 
				$("#botonCerrar").on("click",function(){
					$("#blockui_portlet_body").unblock();
					location.href="nomina_de_pago.php";

				});
			}
			else {
//				window.open("../procesos/zoho_contable/index.php?codnom="+id+"&codtip="+codtip);
				location.href="nomina_de_pago.php";
			}
			$("#blockui_portlet_body").unblock();

			/*var cerrar_nomina=abrirAjax()
			cerrar_nomina.open("GET", "cerrar_nomina.php?codigo_nomina="+id, true)
			cerrar_nomina.onreadystatechange=function()
			{
				if (cerrar_nomina.readyState==4)
				{
					//municipio.parentNode.innerHTML =
					//alert(cerrar_nomina.responseText)
					document.location.href="nomina_de_pago.php"
				}
			}
			cerrar_nomina.send(null);*/
		}
			$("#blockui_portlet_body").unblock();

	}
	if (op==7){	//CERRAR NÓMINA
		var nomina=abrirAjax()
		nomina.open("GET", "abrir_nomina.php?codigo_nomina="+id, true)
		nomina.onreadystatechange=function()
		{
			if (nomina.readyState==4)
			{
					//municipio.parentNode.innerHTML =
						//alert(cerrar_nomina.responseText)
				document.location.href="nomina_de_pago.php"
			}
		}
		nomina.send(null);

	}
	if (op==8){		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo.php?nomina='+id,250,950,0);
	}
	if(op==9)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom.php?nomina='+id,500,950,0);
	}
	if(op==10)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom_desc.php?nomina='+id,300,710,0);
	}
	if(op==11)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom2.php?nomina='+id,500,580,0);
	}
	if(op==12)
	{		// Masivos Conceptos
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom3.php?nomina='+id,500,580,0);
	}
        if(op==13)
	{		// GENERAR TXT
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_generar_nom_txt.php?nomina='+id,500,580,0);
	}
        if(op==14)
	{		// GENERAR PDF
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_generar_nom_pdf.php?nomina='+id,500,580,0);
	}
        if(op==15)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_agregar_masivo_nom_excel.php?nomina='+id,600,800,0);
	}
        if(op==100)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		AbrirVentana('movimientos_nomina_patronales_recalcular.php?registro_id='+id+'&codigo_nomina='+nomina,250,900,0);
	}
}
function accion(opcion, cod)
{
	var nomina=abrirAjax()
	nomina.open("GET", "ajax/preaprobar_nomina.php?codigo_nomina="+cod, true)
	nomina.onreadystatechange=function()
	{
		//console.log(nomina);
		if (nomina.readyState==4)
		{
				//municipio.parentNode.innerHTML =
			alert(nomina.responseText)
			document.location.href="nomina_de_pago.php"
		}
	}
	nomina.send(null);
}
</script>

<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="col-lg-11 col-md-11 portlet box blue">
						<div class="col-lg-12 portlet-title">
							<div class="caption">
								<a href="nomina_de_envio.php"></a> Envío de Nomina GCON4
							</div>
							<div class="actions">
								<a class="btn btn-sm blue" target="_blank" href="generar_csv_todo_personal.php" style="box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3) !important;">
									<i class="fa fa-paper-plane"></i>
									Actualizar Colaboradores en GCON4
								</a>
							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
								<form action="nomina_de_envio_excel.php" method="get" name="frmPrincipal" id="frmPrincipal" target="_blank">
									<div class="col-lg-12 input-group" style="margin-top: 25px; font-size: 18px !important;">
							      <div class="col-md-1">
							        <label for="codnom" class="form-check-label" style="margin-right: -20px;">Nomina</label>
							      </div>
							      <div class="col-md-5">
							        <select class="form-control" name="codnom" id="codnom" required>
								      	<option value="">SELECCIONE</option>
			                  <?php
			                    $sql_anio = "SELECT *
																			 FROM nom_nominas_pago
																			 WHERE status = 'C' AND envio_GCON4 IS NULL
																			 ORDER BY codnom DESC";
			                    $res_anio = $conexion->query($sql_anio, "utf8");

			                    while($fila = $res_anio->fetch_assoc())
			                    {
			                    	?>
			                        <option value="<?php echo $fila['codnom']; ?>" ><?php echo $fila['descrip']; ?></option>
			                      <?php
			                    }
			                  ?>
							        </select>
							      </div>
							      <div class="col-md-6">
											<button type="submit" id="bt_filtra" class="btn btn-sm blue">Exportar</button>
				              <a class="btn btn-sm blue" onclick="enviarNomina();">
												<i class="fa fa-paper-plane"></i>
												Procesar Nomina
											</a>
										</div>
							    </div>
	              </form>

						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
			</div>

			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>

<?php include("../footer4.php"); ?>
<script type="text/javascript">

  function enviarNomina() {
   	var codnom=$("#codnom").val();
   	if (codnom == "") {
   		alert("¡Para realizar el envio debe seleccionar una nomina!");
   	}
   	else {
   		var respuesta = confirm("¿Desea enviar la nomina?");
   		if (respuesta == true) {
		    // El usuario hizo clic en "Aceptar", procede con la acción
		   	window.location.href='?codnomina='+codnom;
			}
   	}
  }
	$(document).ready(function() {
   	$('#table_datatable').DataTable({
    	"iDisplayLength": 25,
    	"bStateSave" : true,
    	"sPaginationType": "bootstrap_extended",
        "aaSorting": [[0, 'desc']],
        "oLanguage": {
        	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
  		    "sEmptyTable":  "No hay datos disponibles", // No hay datos para mostrar
            "sZeroRecords": "No se encontraron registros",
            "oPaginate": {
                "sPrevious": "P&aacute;gina Anterior",//"Prev",
                "sNext": "P&aacute;gina Siguiente",//"Next",
                "sPage": "P&aacute;gina",//"Page",
                "sPageOf": "de",//"of"
            }
        },
        "aLengthMenu": [ // set available records per page
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],
        // "aoColumnDefs": [
        // 	{ 'bSortable': false, 'aTargets': [1, 3] }, // 'bVisible': false,
        //     { 'bSortable': false, 'bSearchable': false, 'aTargets': [7, 8, 9, 10, 11, 12, 13, 14,15] }
        // ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		}
	});

  $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");
	$('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
	$('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
});
</script>
<script type="text/javascript">
$(document).ready(function() {

});
</script>
</body>
</html>
