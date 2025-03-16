<?php
session_start();
?>
<?php 

	
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
$conexion=conexion();
$cedula=$_GET['cedula'];
if(isset($_POST['cedula']))
	$cedula=$_POST['cedula'];
$codigo=$_GET['codigo'];
if(isset($_GET['opt']))
    $opt=$_GET['opt'];
$aprobado = $_GET['aprobado'] ? 1 : 0;

//echo $_SESSION[codigo_nomina]; echo " ";
//echo $cedula; echo " ";

//CONSULTA DATOS DE FUNCIONARIO
$consultap="SELECT * FROM nompersonal WHERE cedula='$_GET[cedula]'";
//echo $consultap;
$resultadop=query($consultap,$conexion);
$fetchCon=fetch_array($resultadop,$conexion);
$ficha=$fetchCon['ficha'];
$nombre=$fetchCon['apenom'];
$nombres=$fetchCon['nombres'];
$apellido_paterno=$fetchCon['apellidos'];
$apellido_materno=$fetchCon['apellido_materno'];
$apellido_casada=$fetchCon['apellido_casada'];
$user_uid=$fetchCon['useruid'];
$id_empleado=$fetchCon['personal_id'];
$usuario = $_SESSION['usuario'];
$id_tipoempleado_anterior = $fetchCon['tipo_funcionario'];
//CONSULTA POSICION FUNCIONARIO
$consulta_posicion="SELECT * FROM posicionempleado WHERE IdEmpleado='$id_empleado'";
$resultado_posicion=query($consulta_posicion,$conexion);
$fetchCon=fetch_array($resultado_posicion,$conexion);
$posicion=$fetchCon['Posicion'];

$titulo = '-' .$cedula. '-'.$nombre;

    $modulo="Registro de Expediente: ".utf8_encode ($nombre);
    $consultaExpTipo="SELECT * FROM expediente_tipo ORDER BY nombre_tipo";
    
    $resultadoExpTipo=query($consultaExpTipo,$conexion);
?>

<SCRIPT  language="JavaScript" type="text/javascript" src="../lib/common.js?<?php echo time(); ?>"></SCRIPT>
<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.7.2.custom.css" />
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/layout.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" media="all" href="../lib/jscalendar/calendar-blue.css" title="win2k-cold-1" /> 
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css"  />
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL SCRIPTS -->
<!--<script type="text/javascript" src="../lib/jquery.js"></script> -->
<!--<script type="text/javascript" src="../lib/jquery-1.3.2.min.js"></script>-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../lib/jquery-ui.min.js"></script>

<script src="../../lib/jquery.maskedinput.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.es.js"></script>

<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>

<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="../../includes/js/scripts/momentjs/moment-with-locales.js"></script>
<script src="../../includes/js/scripts/momentjs/readable-range-es.js"></script>
<script src="lib/jquery.price_format.2.0.js"></script>
<script src="../lib/moment-with-locales.js"></script>

<script src="../../includes/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>

<script>
        $(document).ready(function() {     
          App.init();
            $("#tipo_registro").select2({
                language: "es",              
                allowClear: true,
            });
        });
</script>

<script type="text/javascript">
var editar = '<?php echo $_POST["editar"]; ?>';
var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png"];    

function confirmar2(msg)
{
	if($('#tipo_tiporegistro').val()=="")
	{
		alert("Debe indicar el Tipo de Registro");
		return false;
	}
	if($('#tipo_registro').val()=="13")
	{
		var nombre_documento  = $("#nombre_documento").val();
		var fecha_vencimiento = $("#fecha_vencimiento").val();
		

		if(nombre_documento=='')
		{
			alert("Debe indicar el nombre del documento");
			return false;
		}

		if(fecha_vencimiento=='')
		{
			alert("Debe indicar la fecha de vencimiento del documento");
			return false;
		}

		if(editar==1)
		{
			var archivo = $("#archivo").val();

			if(archivo=='')
			{
				alert("Debe seleccionar un documento");
				return false;
			}

	        var sFileName = document.getElementById("archivo").value;
	        if (sFileName.length > 0) {
	            var blnValid = false;
	            for (var j = 0; j < _validFileExtensions.length; j++) {
	                var sCurExtension = _validFileExtensions[j];
	                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
	                    blnValid = true;
	                    break;
	                }
	            }
	            
	            if (!blnValid) { // " + sFileName + "
	                alert("\u0021Error! El archivo es inv\u00E1lido, las extensiones permitidas son: " + _validFileExtensions.join(", "));
	                return false;
	            }
	        }
		}
	}

	if(confirm(msg) == true) 
	{
            if( $('#tipo_registro').val()=="999" )
            {
                    var data=$('#implemento_datatable').DataTable().fnGetData();
                    $("#expediente_implemento").val(JSON.stringify(data));
                
                    document.formulario1.opcion.value=1;
                    document.formulario1.submit();
                
            }
            else
            {
                    document.formulario1.opcion.value=1;
                    document.formulario1.submit();
            }
	}      
}
</script>
<script>
var editor_index="";

function onAgregar(){
    editor_index="";
    $("#articulo").val("");
    $("#marca").val("");
    $("#modelo").val("");
    $("#talla").val("");
    $("#color").val("");
    $("#cantidad").val("1");
    $("#entrega").val("");
    $("#color").val("");

    $("#modal_datatable").modal("show");
}

function onEditar(index){
    editor_index=index;
    var row=$('#implemento_datatable').DataTable().fnGetData(index);

    $("#articulo").val(row["articulo"]);
    $("#marca").val(row["marca"]);
    $("#modelo").val(row["modelo"]);
    $("#talla").val(row["talla"]);
    $("#color").val(row["color"]);
    $("#cantidad").val(row["cantidad"]);
    $("#entrega").val(row["entrega"]);
    $("#vencimiento").val(row["vencimiento"]);

    $("#modal_datatable").modal("show");
}        

function onEliminar(index){
    if(!confirm("Se eliminará el registro.\n¿Desea continuar?")) return;
    $('#implemento_datatable').DataTable().fnDeleteRow(index);
    //codigo para forzar refrescamiento de la tabla al borrar
    var data=$('#implemento_datatable').DataTable().fnGetData();
    for(var i=0; i<data.length; i++){
        data[i].accion="";
        $('#implemento_datatable').DataTable().fnUpdate( data[i], i );
    }
}

function onAceptar(){
    var data={
        articulo:           $("#articulo").val(),
        marca:              $("#marca").val(),
        modelo:             $("#modelo").val(),
        talla:              $("#talla").val(),
        color:              $("#color").val(),
        cantidad:           $("#cantidad").val(),
        entrega:            $("#entrega").val(),
        vencimiento:        $("#vencimiento").val(),
        accion:             ""
    };

    if(editor_index==="")//si es agregar
        $('#implemento_datatable').DataTable().fnAddData(data);
    else//si es editar
        $('#implemento_datatable').DataTable().fnUpdate( data, editor_index );
    $("#modal_datatable").modal("hide");
}

function onGuardar(){
    var data=$('#implemento_datatable').DataTable().fnGetData();
    console.log(data);
    //enviar data con ajax para el guardado

}

function onInit(){
    //armar el array con la data inicial de la tabla
    //llamado ajax para obtener la data o json_encode con php
//    var data=[
//        {
//          articulo: "001",
//          fecha: "01/02/2018",
//          tipo: "Grupo A",
//          nombre_apellido: "Pedro Perez",
//          monto: "1000.00",
//          accion: "",
//        },              
//        {
//          codigo: "002",
//          fecha: "16/04/2019",
//          tipo: "Grupo B",
//          nombre_apellido: "Pepito Pregunton",
//          monto: "100.00",
//          accion: "",
//        }
//    ];

//    $('#implemento_datatable').DataTable().fnClearTable();
//    $('#implemento_datatable').DataTable().fnAddData(data);
}
                                        
function inicio(){
    jQuery(function($){                                              
	   $.mask.definitions['T']='[PA]'; 
	   $('#desde').mask('99:99 TM',{placeholder:"_"});
	   $('#hasta').mask('99:99 TM',{placeholder:"_"});

    });                                     
}

function vacacion(){
    jQuery(function($){                                              
	   $.mask.definitions['T']='[PA]'; 
	   $('#desde').mask('99/9999',{placeholder:"_"});
	   $('#horas').mask('99/9999',{placeholder:"_"});
	   $('#hasta').mask('99/9999',{placeholder:"_"});
	   $('#fecha').mask('99/99/9999',{placeholder:"_"});
	   $('#aprobado').mask('99/99/9999',{placeholder:"_"});
	   $('#enterado').mask('99/99/9999',{placeholder:"_"});

    });                                     
}

function mostrar_campo(valor){
	if(valor=="Aumenta"){
		$('#efectividad').hide();
		}else{
		$('#efectividad').show();
		}
}
jQuery(function($){
	$.datepicker.regional['es'] = {
		closeText: 'Cerrar',
		prevText: '&#x3c;Ant',
		nextText: 'Sig&#x3e;',
		currentText: 'Hoy',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		weekHeader: 'Sm',
		dateFormat: 'dd/mm/yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['es']);
});    

 function cargar_tipo_local()
        {
            
                    var tipo=document.getElementById('tipo_registro');                    
                    var cedula=document.getElementById('cedula');
                    var codigo=document.getElementById('codigo');
                    var tipotipo=document.getElementById('registro');
                    //var fechas=document.getElementById('fechas')
                    //alert(frecuencia.value)
                    var contenido_tipotipo=abrirAjax();
                    contenido_tipotipo.open("GET", "procesos.php?opcion="+tipo.value+"&cedula="+cedula.value+"&codigo="+codigo.value, true);
                    contenido_tipotipo.onreadystatechange=function()
                    {
                            if (contenido_tipotipo.readyState==4)
                            {
                               //console.log(contenido_tipotipo.responseText);
                                //tipotipo.parentNode.innerHTML = contenido_tipotipo.responseText;
                                   tipotipo.innerHTML=contenido_tipotipo.responseText;
                                   //App.init();
                                    $("#tipo_tiporegistro").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    var editar_reporte_incidente = $("#editar_reporte_incidente").val();
                                    if(editar_reporte_incidente == "1"){
                                    	cargar_form_prestamo();
                                    }
                                    $("#pagado_por_emp").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#nivel_actual").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#cod_cargo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#cod_cargo_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#departamento_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#gerencia_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#seccion_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#institucion_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#institucion_anterior").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#nomina_anterior").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#nomina_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#planilla_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });                                    
                                                                       
                                    $("#estatus_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                     $("#posicion_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#departamento_nuevo2").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#funcion_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#cargo_estructura").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#cargo_funcion").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                   
                                     $("#departamento_rotacion").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                     $("#institucion_educativa_nueva").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#titulo_profesional").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#periodo_vacacion").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#institucion").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#tipo_comparecencia").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#tipo_investigacion").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#tipo_falta").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                                                         
                                     $("#ascenso_nivel_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                     $("#ascenso_cargo_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                     $("#funcionario_evalua").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                     $("#proyecto").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#proyecto_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $("#cargo_nuevo").select2({
                                        language: "es",              
                                         allowClear: true,
                                    });
                                    
                                    $('#sobresueldo_jefatura').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#sobresueldo_exclusividad').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                      $('#sobresueldo_altoriesgo').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#sobresueldo_especialidad').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                                                                                            
                                    $('#veinte_porciento').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#cuarenta_porciento').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#salario_bienal').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                    $('#monto_mensual_bienal').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#acumulativo_bienal').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#salario_base').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#ajuste').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#ajuste_discrecional').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                     $('#ajuste_otros').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     }); 
                                    
                                    $('#ajuste_salario_minimo').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });    
                                    
                                    $('#salario_devengado').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                    
                                    $('#salario_devengar').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                     $('#diferencia').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                      $('#monto_pagar').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                     $('#porcentaje_sobresueldo').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                      $('#sobresueldo').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                      $('#acuerdo').priceFormat({
                                        prefix: '',
                                        centsSeparator: '.',
                                        thousandsSeparator: ','
                                     });
                                     
                                    $('#hora_incidente').datetimepicker({
//                                        locale: 'es'
                                    });
                                    $('#div_fecha_hora_inicio').datetimepicker({
//                                        locale: 'es'
                                    });
                                    $('#div_fecha_hora_fin').datetimepicker({
//                                        locale: 'es',
                                        useCurrent: false //Important! See issue #1075
                                    });
                                    $("#div_fecha_hora_inicio").on("dp.change", function (e) {
                                        $('#div_fecha_hora_fin').data("DateTimePicker").minDate(e.date);
                                    });
                                    $("#div_fecha_hora_fin").on("dp.change", function (e) {
                                        $('#div_fecha_hora_inicio').data("DateTimePicker").maxDate(e.date);
                                    });
                                    
                                     buscar_correlativo(tipo.value);
                                     if(tipo.value==57 )
                                     {
                                        buscar_datos_posicion_anterior_inicio();                                        
                                    }   
                                    if(tipo.value==58 || tipo.value==9)
                                     {
                                        buscar_datos_posicion_anterior_inicio();
                                        buscar_datos_posicion_nueva_inicio();
                                    } 
                                    if(tipo.value==11)
                                    {
                                       cargar_select_periodo_vacacion_vacacion();
                                       
                                    }
                                    
                                    if(tipo.value==68)
                                    {
                                        $.fn.datepicker.dates.es={"days":["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"],"daysShort":["Dom","Lun","Mar","Mié","Jue","Vie","Sáb"],"daysMin":["Do","Lu","Ma","Mi","Ju","Vi","Sá"],"months":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Deciembre"],"monthsShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],"today":"Hoy","clear":"Limpiar","titleFormat":"MM yyyy"};
                                        $.fn.datepicker.defaults.language="es";
                                        $.fn.datepicker.defaults.todayHighlight=true;
                                        $.fn.datepicker.defaults.autoclose=true;
                                        $.fn.datepicker.defaults.format="dd/mm/yyyy";
                                         		
                                            $("#entrega").datepicker();
                                            $("#vencimiento").datepicker();

                                            $('#implemento_datatable').DataTable({
                                                "aaData": [],            	
                                                "iDisplayLength": 10,            
                                                "sPaginationType": "bootstrap_extended", 
                                                "oLanguage": {
                                                    "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                                                    "sLengthMenu": "Mostrar _MENU_",
                                                    "sInfoEmpty": "",
                                                    "sInfo":"Total _TOTAL_ registros",
                                                    "sInfoFiltered": "",
                                                    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                                                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                                                    "oPaginate": {
                                                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                                                        "sNext": "P&aacute;gina Siguiente",//"Next",
                                                        "sPage": "P&aacute;gina",//"Page",
                                                        "sPageOf": "de"//"of"
                                                    }
                                                }, 
                                                "aoColumns": [{"mDataProp": "articulo"},
                                                                {"mDataProp": "marca"},
                                                                {"mDataProp": "modelo"},
                                                                {"mDataProp": "talla"},
                                                                {"mDataProp": "color"},
                                                                {"mDataProp": "cantidad"},
                                                                {"mDataProp": "entrega"},
                                                                {"mDataProp": "vencimiento"},
                                                                        {
                                                                            "mDataProp": "accion",
                                                                        "bSearchable": false,
                                                                        "bSortable": false,
                                                        "fnRender": function(data, type, full, meta) {
                                                            return "<div class='acciones'>"+
                                                                "<i class='fa fa-pencil' onclick='onEditar("+data["iDataRow"]+")'></i>"+
                                                                "<i class='fa fa-trash'  onclick='onEliminar("+data["iDataRow"]+")'></i>"+
                                                                "</div>";
                                                        }
                                                                        }
                                                                ],  

                                                        "fnDrawCallback": function() {
                                                    $('#implemento_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                                                }
                                            });

                                            $('#implemento_datatable').on('change', 'tbody tr .checkboxes', function(){
                                                $(this).parents('tr').toggleClass("active");
                                            });

                                            $('#implemento_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
                                            $('#implemento_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
                                            
                                            
                                            onInit();
                                            
                                    }
                            }
                    }
                    contenido_tipotipo.send(null);
                   
                    
        }
        
        function validar_moneda(e)
        {
            var key = window.Event ? e.which : e.keyCode
            //alert(key);
            return (key >= 48 && key <= 57 || (key==8 || key==46 || key==44));
        }
        
        function buscar_correlativo(valor)
        {
            var tipo_accion;
            tipo_accion = 0;
            if (valor== 1)
            {//              
                tipo_accion = 38;
            }
            if (valor== 2)
            {//              
                tipo_accion = 39;
            }
            if (valor== 4)
                tipo_accion = 22;
            if (valor== 5)
                tipo_accion = 27;
            if (valor== 6)
                tipo_accion = 28;
            if (valor== 7)
                tipo_accion = 29;
            if (valor== 8)
                tipo_accion = 10;
            if (valor== 9)
                tipo_accion = 20;  
            if (valor== 10)
                tipo_accion = 52;
            if (valor== 11)
                tipo_accion = 19;
            if (valor== 12)
                tipo_accion = 44;
            if (valor== 15)
                tipo_accion = 7;
            if (valor== 16)
                tipo_accion = 8;
            if (valor== 17)
                tipo_accion = 9;
            if (valor== 23)
                tipo_accion = 32;
            if (valor== 24)
                tipo_accion = 48;
            if (valor== 25)
                tipo_accion = 49;
            if (valor== 26)
                tipo_accion = 50;
            if (valor== 27)
                tipo_accion = 44;
            if (valor== 28)
                tipo_accion = 36;
            if (valor== 29)
                tipo_accion = 37;
            if (valor== 30)
                tipo_accion = 5;
            if (valor== 31)
                tipo_accion = 6;
            if (valor== 32)
                tipo_accion = 11;
             if (valor== 33)
                tipo_accion = 12;
             if (valor== 35)
                tipo_accion = 21;
            if (valor== 36)
                tipo_accion = 24;
            if (valor== 37)
                tipo_accion = 25;
            if (valor== 38)
                tipo_accion = 26;
            if (valor== 39)
                tipo_accion = 30;
            if (valor== 40)
                tipo_accion = 31;
            if (valor== 41)
                tipo_accion = 33;
            if (valor== 42)
                tipo_accion = 34;
             if (valor==43)
                tipo_accion = 35;    
            if (valor== 44)
                tipo_accion = 4;
            if (valor== 56)
                tipo_accion = 40;
             if (valor== 57)
                tipo_accion = 41;
             if (valor== 58)
                tipo_accion = 42;
            if (valor== 65)
                tipo_accion = 13;
            if (valor== 66)
                tipo_accion = 14;
             if (valor== 67)
                tipo_accion = 15;
             if (valor== 68)
                tipo_accion = 54;
            if (valor== 59)
                tipo_accion = 43;
             if (valor== 61)
                tipo_accion = 45;   
             if (valor== 62)
                tipo_accion = 46;
            if (valor== 63)
                tipo_accion = 47;
            if (valor== 64)
                tipo_accion = 48;
            if (valor>= 69 && valor<= 78) //para mivimientos de MDD documentos
                tipo_accion = 18;   
            //alert(tipo_accion);
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_correlativo.php?tipo_accion="+tipo_accion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var numero_secuencial=parseInt(datos)+1;
                            document.getElementById('numero_secuencial').value=numero_secuencial;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            //window.location.href="expediente_agregar.php?cedula="+cedula;
        }  
        
        function buscar_datos_posicion_nueva()
        {
                       
            var posicion=document.getElementById('posicion_nueva').value;
            //alert(posicion);
            $.ajax({
                    type: "POST",
                    url:"ajax/validar_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var estado=datos;
                            //alert (estado);
                            if(estado==0)
                            {
                                $.ajax({
                                        type: "POST",
                                        url:"ajax/buscar_cod_car_posicion.php?posicion="+posicion,
                                        async: true,
                                        success: function(datos){
                                            /*
                                            var dataJson = eval(datos);

                                            for(var i in dataJson){
                                                alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                                            }*/
                                            //alert(datos);
                                            if (datos!='')
                                            {   
                                                var cod_car=datos;
                                                document.getElementById('cargo_nuevo').value=cod_car;
                                            }

                                        },
                                        error: function (obj, error, objError){
                                            //avisar que ocurrió un error
                                        }
                                });

                                $.ajax({
                                        type: "POST",
                                        url:"ajax/buscar_des_car_posicion.php?posicion="+posicion,
                                        async: true,
                                        success: function(datos){
                                            /*
                                            var dataJson = eval(datos);

                                            for(var i in dataJson){
                                                alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                                            }*/
                                            //alert(datos);
                                            if (datos!='')
                                            {   
                                                var des_car=datos;
                                                document.getElementById('des_car_posicion').value=des_car;
                                            }

                                        },
                                        error: function (obj, error, objError){
                                            //avisar que ocurrió un error
                                        }
                                });

                                $.ajax({
                                        type: "POST",
                                        url:"ajax/buscar_salario_posicion.php?posicion="+posicion,
                                        async: true,
                                        success: function(datos){
                                            /*
                                            var dataJson = eval(datos);

                                            for(var i in dataJson){
                                                alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                                            }*/
                                            //alert(datos);
                                            if (datos!='')
                                            {   
                                                var salario=datos;
                                                document.getElementById('salario_posicion').value=salario;
                                            }

                                        },
                                        error: function (obj, error, objError){
                                            //avisar que ocurrió un error
                                        }
                                });
                                //document.getElementById('btn-guardar').disabled = false;
                            }
                            else
                            {
                                alert("Posición No Disponible. Verifique y Seleccione otra.");
                                //document.getElementById('btn-guardar').disabled = 'disabled';
                            }
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            //window.location.href="expediente_agregar.php?cedula="+cedula;
        }  
        
        function buscar_datos_posicion_nueva_inicio()
        {
                       
            var posicion=document.getElementById('posicion_nueva').value;
            //alert(posicion);           
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_cod_car_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var cod_car=datos;
                            document.getElementById('cargo_nuevo').value=cod_car;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_des_car_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var des_car=datos;
                            document.getElementById('des_car_posicion').value=des_car;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_salario_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var salario=datos;
                            document.getElementById('salario_posicion').value=salario;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            }); 
        }  
        
        function buscar_datos_posicion_anterior_inicio()
        {
                       
            var posicion=document.getElementById('posicion_anterior').value;
            //alert(posicion);           
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_cod_car_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var cod_car=datos;
                            document.getElementById('cargo_posicion_anterior').value=cod_car;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_des_car_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var des_car=datos;
                            document.getElementById('des_car_posicion_anterior').value=des_car;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_salario_posicion.php?posicion="+posicion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var salario=datos;
                            document.getElementById('salario_posicion_anterior').value=salario;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            }); 
        }  
        
        function calcular_sobresueldo(valor)
        {
            var salario=parseFloat(document.getElementById('salario').value);
            var porcentaje=parseFloat(valor);
            var sobresueldo = salario*(porcentaje/100);
            document.getElementById('sobresueldo').value=sobresueldo;

        }       
        
        function monto_analisis_cambio_etapa()
        {
            //alert(valor);
            var patron=",";
            if(document.getElementById('ajuste').value=='')
                document.getElementById('ajuste').value='0.00';
            if(document.getElementById('ajuste_discrecional').value=='')
                document.getElementById('ajuste_discrecional').value='0.00';
            if(document.getElementById('ajuste_salario_minimo').value=='')
                document.getElementById('ajuste_salario_minimo').value='0.00';
            if(document.getElementById('ajuste_otros').value=='')
                document.getElementById('ajuste_otros').value='0.00';
            if(document.getElementById('acuerdo').value=='')
                document.getElementById('acuerdo').value='0.00';
            if(document.getElementById('etapa_analisis').value=='')
               document.getElementById('etapa_analisis').value='0';
            var salario_porcentaje=parseFloat(document.getElementById('salario_base_porcentaje').value.replace(patron,''));
            var etapa=parseFloat(document.getElementById('etapa_analisis').value);            
            var ajuste=parseFloat(document.getElementById('ajuste').value);
            var ajuste_discrecional=parseFloat(document.getElementById('ajuste_discrecional').value);
            var ajuste_salario_minimo=parseFloat(document.getElementById('ajuste_salario_minimo').value);
            var ajuste_otros=parseFloat(document.getElementById('ajuste_otros').value);
            var acuerdo=parseFloat(document.getElementById('acuerdo').value);
            var monto = salario_porcentaje+(etapa*ajuste)+ajuste_discrecional+ajuste_salario_minimo+ajuste_otros+acuerdo;
            document.getElementById('salario_analisis').value=monto.toFixed(2);

        }  
        
        function mostrar_especial()
        {
            
            var tipo=parseInt(document.getElementById('tipo_tiporegistro').value);
            //alert(tipo);
            document.getElementById("especiales").style.display="none";
            if (tipo==53 || tipo == 82 )
            {
                //alert("Entro Tipo");
                document.getElementById("especiales").style.display="block";
            }
            

        }  
        
        function calcular_porcentaje()
        {
            var patron=",";           
            var salario=parseFloat(document.getElementById('salario_base').value.replace(patron,''));
            var porcentaje=0.00;
            if (salario>=500.00 && salario<=799.00)
            {
                porcentaje=0.14;
            }
            if(salario>=800.00 && salario<=1499.00)
            {
                porcentaje=0.12;
            }
            if(salario>=1500.00)
            {
                porcentaje=0.10;
            }
            //alert (salario);
            //alert (porcentaje);
            var porcentaje_salario = salario*porcentaje;
            //alert (porcentaje_salario);  
            var salario_porcentaje = salario+porcentaje_salario;
            document.getElementById('porcentaje').value=porcentaje_salario.toFixed(2);
            document.getElementById('salario_base_porcentaje').value=salario_porcentaje.toFixed(2);

        }
        
         function calcular_salario_porcentaje()
        {
            var patron=",";           
            var salario=parseFloat(document.getElementById('salario_base').value.replace(patron,''));
            var porcentaje_salario=parseFloat(document.getElementById('porcentaje').value.replace(patron,''));            
            var salario_porcentaje = salario+porcentaje_salario;
            document.getElementById('salario_base_porcentaje').value=salario_porcentaje.toFixed(2);

        }
        
        
        function calcular_duracion_licencia()
        {
            var fecha_inicio=document.getElementById('fecha_inicio').value;
            var fecha_fin=document.getElementById('fecha_fin').value;            
            if (fecha_inicio=='' || fecha_fin=='')
            {
                return false;
            }
            else
            {
//                alert(fecha_inicio);
//                alert(fecha_fin);
                var a = moment(fecha_inicio, 'DD/MM/YYYY');
                var b = moment(fecha_fin, 'DD/MM/YYYY');                
                var anios = b.diff(a, 'years');
                var meses = b.diff(a, 'months');
                var dias = b.diff(a, 'days')+1;
                var horas = dias * 8;
                var minutos = horas * 60;
                document.getElementById('anios').value=anios;
                document.getElementById('meses').value=meses;
                document.getElementById('dias').value=dias;
//                document.getElementById('horas').value=horas;
//                document.getElementById('minutos').value=minutos;
//                alert(meses);
//                alert(dias);
//                alert(horas);
//                alert(minutes);
            }
        }
        
         function calcular_duracion_permiso()
        {
            var fecha_inicio=document.getElementById('fecha_inicio').value;
            var fecha_fin=document.getElementById('fecha_fin').value;            
            if (fecha_inicio=='' || fecha_fin=='')
            {
                return false;
            }
            else
            {
//                alert(fecha_inicio);
//                alert(fecha_fin);
                var a = moment(fecha_inicio, 'DD/MM/YYYY');
                var b = moment(fecha_fin, 'DD/MM/YYYY');                
//                var anios = b.diff(a, 'years');
//                var meses = b.diff(a, 'months');
                var dias = b.diff(a, 'days')+1;
                var horas = dias * 8;
                var minutos = horas * 60;
//                document.getElementById('anios').value=anios;
//                document.getElementById('meses').value=meses;
                document.getElementById('dias').value=dias;
                document.getElementById('horas').value=0;
                document.getElementById('minutos').value=0;
//                alert(meses);
//                alert(dias);
//                alert(horas);
//                alert(minutes);
            }
        }
        
        function dias_suspension()
        {
            var fecha_inicio=document.getElementById('fecha_desde').value;
            var fecha_fin=document.getElementById('fecha_hasta').value;            
            if (fecha_inicio=='' || fecha_fin=='')
            {
                return false;
            }
            else
            {
//                alert(fecha_inicio);
//                alert(fecha_fin);
                var a = moment(fecha_inicio, 'DD/MM/YYYY');
                var b = moment(fecha_fin, 'DD/MM/YYYY');   
                var dias = b.diff(a, 'days')+1;
                document.getElementById('dias').value=dias;
//                alert(meses);
//                alert(dias);
//                alert(horas);
//                alert(minutes);
            }
        }
        
        function buscar_periodo_vacacion(valor)
        {
//            alert(valor);
            var id_periodo_vacacion;
            id_periodo_vacacion=valor;
           
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_fecha_inicio_periodo_vacacion.php?id_periodo_vacacion="+id_periodo_vacacion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var fecha_inicio_periodo=datos;
                            document.getElementById('fecha_inicio_periodo').value=fecha_inicio_periodo;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_fecha_fin_periodo_vacacion.php?id_periodo_vacacion="+id_periodo_vacacion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var fecha_fin_periodo=datos;
                            document.getElementById('fecha_fin_periodo').value=fecha_fin_periodo;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_restante_periodo_vacacion.php?id_periodo_vacacion="+id_periodo_vacacion,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var saldo=datos;
                            document.getElementById('restante').value=saldo;
                            document.getElementById('dias').value=saldo;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            //window.location.href="expediente_agregar.php?cedula="+cedula;
        }  
        
         function reset_campos()
        {
            
            document.getElementById('numero_resolucion').value='';
            document.getElementById('fecha_resolucion').value='';
            document.getElementById('fecha_inicio').value='';
            document.getElementById('fecha_fin').value='';
            if(document.getElementById('periodo_vacacion').value=='')
            {
                document.getElementById('dias').value=30;
                document.getElementById('restante').value=30;
                cargar_periodo_vacaciones_legal();
            }
        }
        
        function calcular_dias()
        {
            var fecha_inicio=document.getElementById('fecha_inicio').value;
            var fecha_fin=document.getElementById('fecha_fin').value;              
            var restante=document.getElementById('restante').value;
            var tipo_registro=document.getElementById('tipo_registro').value;
            var total=0;
            var bandera=0;
            if (fecha_inicio=='' || fecha_fin=='')
            {                
                return false;
            }
            else
            {
                var a = moment(fecha_inicio, 'DD/MM/YYYY');
                var b = moment(fecha_fin, 'DD/MM/YYYY');
                var dias = b.diff(a, 'days')+1;               
                if(tipo_registro==11)
                {
                    var tipo_tiporegistro=document.getElementById('tipo_tiporegistro').value;
//                    if(tipo_tiporegistro==110)
//                    {
//                        if(dias==7 || dias==8 || dias==15 || dias==30)
//                        {
//                            bandera=0;
//                        }
//                        else
//                        {
//                            bandera=3;
//                        }
//                    }
                    if(dias>restante)
                    {
                        bandera=1;                    
                    }
                    if(dias<0)
                    {
                        bandera=2;                   
                    }
                }
                
                if(tipo_registro==27)
                {
                    var tipo_tiporegistro=document.getElementById('tipo_tiporegistro').value;
                    if(tipo_tiporegistro==61)
                    {
                        if(document.getElementById('aumenta').checked)
                        {
                            var total=parseInt(dias)+parseInt(restante);
                            
                            if(total>30)
                            {
                                bandera=4;
                            }
                            if(dias<0)
                            {
                                bandera=5;                   
                            }
                        }
                    }                    
                    
                }
                
                
                if( bandera==1)
                {
                    alert('Cantidad de dias Solicitados excede Saldo Disponible. Verifique');
                    return false;
                }
                if( bandera==2)
                {
                    alert('Fecha Inicio es Mayor a Fecha Fin. Verifique');
                    return false;
                }
                if( bandera==3)
                {
                    alert('Debe introducir un rango de fechas que resulte en 7, 8, 15 o 30 dias. Verifique');
                    return false;
                }
                 if( bandera==4)
                {
                    alert('Cantidad de dias de Ajuste Excede 30 Dias. Verifique');
                    return false;
                }
                if( bandera==5)
                {
                    alert('Cantidad de dias de Ajuste No puede ser Menor a 0. Verifique');
                    return false;
                }
                document.getElementById('dias').value=dias;
                
            }
        }       
        
        function cargar_periodo_vacaciones_legal()
        {
            var fecha_permanencia=document.getElementById('fecha_permanencia').value;
            if(fecha_permanencia=='00/00/0000')
            {                
                return false;
            }
            else
            {
                //alert(fecha_permanencia);
                var fec_perm = moment(fecha_permanencia, 'DD/MM/YYYY');
                var anio_actual = moment().year();
                var dia = fec_perm.date();
                var mes = fec_perm.month()+1;
                if(dia<10)
                    dia="0"+dia;
                if(mes<10)
                    mes="0"+mes;
                var fecha_inicio_periodo = dia+"/"+mes+"/"+anio_actual;
//                alert("fecha permanencia "+fecha1);
//                alert("año actual "+anio_actual);
//                alert("dia "+dia);
//                alert("mes "+mes);
//                alert("fecha2 "+fecha2);
                var fecha_inicio_moment = moment(fecha_inicio_periodo, 'DD/MM/YYYY');                
                var fecha_fin_moment = fecha_inicio_moment.add('months', 11); 
                var fecha_fin_moment = fecha_fin_moment.subtract('days', 1); 
                var fecha_fin_periodo = fecha_fin_moment.format("DD/MM/YYYY");
            }
            document.getElementById('fecha_inicio_periodo').value=fecha_inicio_periodo;
            document.getElementById('fecha_fin_periodo').value=fecha_fin_periodo;
        }        
        
        function cargar_select_periodo_vacacion_vacacion()
        {
            var subtipo = document.getElementById('tipo_tiporegistro').value;
            var cedula = document.getElementById('cedula').value;
            var editar = document.getElementById('editar').value;
            
            if(editar==1)
            {
                var codigo = document.getElementById('codigo').value;
            }
//            alert(subtipo);
//            alert(cedula);
//            alert(editar);
//            alert(codigo);
            $.ajax({
                    type: "POST",
                    url:"ajax/cargar_select_periodo_vacacion_vacacion.php?editar="+editar+"&cedula="+cedula+"&codigo="+codigo+"&subtipo="+subtipo,
                    async: true,
                    success: function(datos){

                        if (datos!='')
                        {   
                            $("#div_periodo_vacacion").empty();
                            $("#div_periodo_vacacion").append(datos); 
                            var edit = document.getElementById('editar').value;
                            if(edit!=1)
                            {
                                cargar_periodo_vacaciones_legal();
                            }
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });
            
            
        }      
        
        function cargar_form_prestamo()
        {
            var subtipo = document.getElementById('tipo_tiporegistro').value;
            var cedula = document.getElementById('cedula').value;
            var editar = document.getElementById('editar').value;
            var aprobado = document.getElementById('aprobado').value;
            
            if(editar==1)
            {
                var codigo = document.getElementById('codigo').value;
            }
            $.ajax({
                type: "POST",
                url:"ajax/cargar_form_prestamo.php?editar="+editar+"&cedula="+cedula+"&codigo="+codigo+"&subtipo="+subtipo+"&aprobado="+aprobado,
                async: true,
                success: function(datos){

                    if (datos!='')
                    {   
                        $("#div_form_prestamo").empty();
                        $("#div_form_prestamo").append(datos); 
                        //aca se generaran las cuotas
                        /*var edit = document.getElementById('editar').value;
                        if(edit!=1)
                        {
                            cargar_periodo_vacaciones_legal();
                        }*/
                    }
                },
                error: function (obj, error, objError){
                    //avisar que ocurrió un error
                }
            });
        }  
        
        function calcular_horas_dias()
        {            
            var horas = document.getElementById('horas').value;
            var minutos = document.getElementById('minutos').value;
            var tiempo = 0;
            if(minutos>59)
            {
                alert('Cantidad de Minutos Excede 59. Verifique');
                return false;
            }
            if (horas=='')
                horas=0;
            else
                horas=parseFloat(horas);
            if (minutos=='')
                minutos=0;
            else
                minutos=parseFloat(minutos);
            tiempo = horas * 60 + minutos;
            if(tiempo>=480)
            {
                dias = tiempo / 480;
            }
            else
                dias = 0;
            document.getElementById('dias').value=dias.toFixed(2);
        }  
        
        function calcular_dias_horas()
        {
            var fecha_hora_inicio=document.getElementById('div_fecha_hora_inicio').value;
            var fecha_hora_fin=document.getElementById('div_fecha_hora_fin').value;            
            if (fecha_hora_inicio=='' || fecha_hora_fin=='')
            {
                return false;
            }
            else
            {
//                alert(fecha_inicio);
//                alert(fecha_fin);
                var a = moment(fecha_hora_inicio, "YYYY-MM-DD HH:mm:ss");
                var b = moment(fecha_hora_fin, "YYYY-MM-DD HH:mm:ss"); 
                var dias = b.diff(a, 'days')+1;
                var horas = dias * 8;
                document.getElementById('dias').value=dias;
                document.getElementById('horas').value=horas;
//                alert(dias);
//                alert(horas);
            }
        }
        
        function buscar_datos_falta()
        {
                       
            var tipo_falta=document.getElementById('tipo_falta').value;           
                    
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_articulo_falta.php?tipo_falta="+tipo_falta,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var articulo=datos;
                            document.getElementById('articulo').value=articulo;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_numeral_falta.php?tipo_falta="+tipo_falta,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var numeral=datos;
                            document.getElementById('numeral').value=numeral;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });

            $.ajax({
                    type: "POST",
                   url:"ajax/buscar_descripcion_falta.php?tipo_falta="+tipo_falta,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var numeral_descripcion=datos;
                            document.getElementById('numeral_descripcion').value=numeral_descripcion;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            });                       
                  
        } 
        
        function cargar_ajuste_tiempo()
        {
                       
            var tipo=document.getElementById('tipo_tiporegistro').value;
            //alert(tipo);
            if(tipo==59 || tipo==60 || tipo==62)
            {

                document.getElementById("tipo_mision").style = "display:none";
                document.getElementById("mision_label").style = "display:none";
                document.getElementById("tipo").style = "display:block";
                document.getElementById("tiempos").style = "display:block";
                document.getElementById("medico").style = "display:block";
                document.getElementById("observacion").style = "display:block";
            }
            if(tipo==58)
            {

                document.getElementById("tipo_mision").style = "display:block";
                document.getElementById("mision_label").style = "display:block";
                document.getElementById("tipo").style = "display:block";
                document.getElementById("tiempos").style = "display:block";
                document.getElementById("medico").style = "display:none";
                document.getElementById("observacion").style = "display:block";
            }
            if(tipo==55)
            {

                document.getElementById("tipo_mision").style = "display:none";
                document.getElementById("mision_label").style = "display:none";
                document.getElementById("tipo").style = "display:none";
                document.getElementById("tiempos").style = "display:none";
                document.getElementById("medico").style = "display:none";
                document.getElementById("observacion").style = "display:block";
            }
            if(tipo==55 || tipo==57)
            {

                document.getElementById("tipo_mision").style = "display:none";
                document.getElementById("mision_label").style = "display:none";
                document.getElementById("tipo").style = "display:block";
                document.getElementById("tiempos").style = "display:block";
                document.getElementById("medico").style = "display:none";
                document.getElementById("observacion").style = "display:block";
            }
            if(tipo==61)
            {

                document.getElementById("tipo_mision").style = "display:none";
                document.getElementById("mision_label").style = "display:none";
                document.getElementById("tipo").style = "display:block";
                document.getElementById("tiempos").style = "display:block";
                document.getElementById("medico").style = "display:none";
                document.getElementById("observacion").style = "display:block";
            }
            cargar_saldo_ajuste_tiempo();
            
        }
        
        function cargar_saldo_ajuste_tiempo()
        {
            var tipo=document.getElementById('tipo_tiporegistro').value;
            var tipo_justificacion=0;
            if(tipo==55)
            {
                tipo_justificacion=1;
            }
             if(tipo==56)
            {
                tipo_justificacion=2;  
            }
             if(tipo==57)
            {
                tipo_justificacion=3;
            }
             if(tipo==58)
            {
                tipo_justificacion=4;
            }
             if(tipo==59)
            {
                tipo_justificacion=5;
            }
             if(tipo==60)
            {
                tipo_justificacion=6;
            }
             if(tipo==61)
            {
                tipo_justificacion=7;
            }
             if(tipo==62)
            {
                tipo_justificacion=8;
            }
            //alert(tipo_justificacion);
            var cedula=document.getElementById('cedula').value;
            $.ajax({
                    type: "POST",
                    url:"ajax/cargar_saldo_ajuste_tiempo.php?tipo_justificacion="+tipo_justificacion+"&cedula="+cedula,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='')
                        {   
                            var saldo=datos;
                            document.getElementById('restante').value=saldo;
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            }); 
        }
        
        function calcular_duracion_tiempo()
        {            
            
            var horas = document.getElementById('horas').value;
            var minutos = document.getElementById('minutos').value;
            var dias = document.getElementById('dias').value;
            var tiempo = 0;
            if(minutos>59)
            {
                alert('Cantidad de Minutos Excede 59. Verifique');
                return false;
            }
             if(horas>8)
            {
                alert('Cantidad de Horas Excede 8. Verifique');
                return false;
            }
            if (dias=='')
                dias=0;
            else
                dias=parseFloat(dias);
            
            if (horas=='')
                horas=0;
            else
                horas=parseFloat(horas);
            
            if (minutos=='')
                minutos=0;
            else
                minutos=parseFloat(minutos);
            
            tiempo = (dias * 8) + horas + minutos/60;
           
            
            document.getElementById('duracion').value=tiempo.toFixed(2);
        }  
        
         function calcular_duracion()
        {            
            var tipo=document.getElementById('tipo_tiporegistro').value;
            var horas = document.getElementById('horas').value;
            var minutos = document.getElementById('minutos').value;
            var dias = document.getElementById('dias').value;
            var tiempo = 0;
//            if(minutos>59)
//            {
//                alert('Cantidad de Minutos Excede 59. Verifique');
//                return false;
//            }
//             if(horas>8)
//            {
//                alert('Cantidad de Horas Excede 8. Verifique');
//                return false;
//            }
            if (dias=='')
                dias=0;
            else
                dias=parseFloat(dias);
            
            if (horas=='')
                horas=0;
            else
                horas=parseFloat(horas);
            
            if (minutos=='')
                minutos=0;
            else
                minutos=parseFloat(minutos);
            
            tiempo = (dias * 8) + horas + minutos/60;
             if(tipo==61)
            {
                tiempo = dias;
            }
           
            
            document.getElementById('duracion').value=tiempo.toFixed(2);
        }  
        
        function validar_saldo_ajuste()
        {            
            
        }  
        
        
        
        function calcular_desempenio_porcentaje()
        {
            var ascenso_desempenio_1_1=parseFloat(document.getElementById('ascenso_desempenio_1_1').value);
            var ascenso_desempenio_1_2=parseFloat(document.getElementById('ascenso_desempenio_1_2').value); 
            var ascenso_desempenio_2_1=parseFloat(document.getElementById('ascenso_desempenio_2_1').value); 
            var ascenso_desempenio_2_2=parseFloat(document.getElementById('ascenso_desempenio_2_2').value); 
            var suma = ascenso_desempenio_1_1+ascenso_desempenio_1_2+ascenso_desempenio_2_1+ascenso_desempenio_2_2;
            var porcentaje=(suma/4)*0.4;
            document.getElementById('ascenso_desempenio_porcentaje').value=porcentaje;
            
        }
        
        function calcular_conducta_porcentaje()
        {
            var ascenso_conducta_1=parseFloat(document.getElementById('ascenso_conducta_1').value);
            var ascenso_conducta_2=parseFloat(document.getElementById('ascenso_conducta_2').value);
            var ascenso_conducta_actual=parseFloat(document.getElementById('ascenso_conducta_actual').value);
            var porcentaje = 30-(ascenso_conducta_1+ascenso_conducta_2+ascenso_conducta_actual);
            document.getElementById('ascenso_conducta_porcentaje').value=porcentaje;
            
        }
        
        function calcular_participativa_porcentaje()
        {
            var ascenso_participativa_actual=parseFloat(document.getElementById('ascenso_participativa_actual').value);
           
            var porcentaje=ascenso_participativa_actual*0.3;
            document.getElementById('ascenso_participativa_porcentaje').value=porcentaje;
            
        }
        
         function calcular_total_porcentaje()
        {
            var observacion="";
            var ascenso_desempenio_porcentaje=parseFloat(document.getElementById('ascenso_desempenio_porcentaje').value);
            var ascenso_conducta_porcentaje=parseFloat(document.getElementById('ascenso_conducta_porcentaje').value); 
            var ascenso_participativa_porcentaje=parseFloat(document.getElementById('ascenso_participativa_porcentaje').value); 
            var porcentaje = ascenso_desempenio_porcentaje+ascenso_conducta_porcentaje+ascenso_participativa_porcentaje;
            document.getElementById('ascenso_puntaje_total').value=porcentaje;
            if(parseFloat(porcentaje)>=parseFloat(71))
            {
                observacion="CUMPLE CON LOS REQUISITOS";
            }
            else
            {
                observacion="NO CUMPLE CON LOS REQUISITOS";
            }
            document.getElementById('descripcion').value=observacion;
            
        }
        
        function buscar_salario_cargo()
        {          
            var cargo=document.getElementById('cargo_nuevo').value;         
            
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_salario_cargo.php?cargo="+cargo,
                    async: true,
                    success: function(datos){
                        /*
                        var dataJson = eval(datos);

                        for(var i in dataJson){
                            alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                        }*/
                        //alert(datos);
                        if (datos!='' && datos != 0.00)
                        {   
                            var salario=datos;
                            document.getElementById('salario_nuevo').value=salario;
                        }else{
                            buscar_salario_empresa();
                        }

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            }); 
        }  
        
        function buscar_salario_empresa()
        {          
            var cargo=document.getElementById('cargo_nuevo').value;         
            
            $.ajax({
                    type: "POST",
                    url:"ajax/buscar_salario_empresa.php",
                    async: true,
                    success: function(datos){
                        if (datos!='')
                        {   
                            var salario=datos;
                            document.getElementById('salario_nuevo').value=salario;
                        }
                        alert("El cargo seleccionado no posee un salario asignado");

                    },
                    error: function (obj, error, objError){
                        //avisar que ocurrió un error
                    }
            }); 
        } 
</script>

<?php



if($_POST['opcion']==1)
{
    
    if($_POST['editar']!=1)
	{              
            include 'expediente_guardar.php';
	}
	else
	{ 
        include 'expediente_editar.php';               

	}
	activar_pagina("expediente_list.php?cedula=$cedula");
}

$editar="";
if(isset($_GET['codigo']))
{
	$modulo="Editar Expediente: ".utf8_encode ($nombre);
	$consulta="SELECT * FROM expediente WHERE cod_expediente_det='$_GET[codigo]'";
            
	$resultado=query($consulta,$conexion);
	$fetch33=fetch_array($resultado);        
	$editar=1;
        $tipo_registro=$fetch33['tipo']; 
        $sub_tipo_registro=$fetch33['subtipo'];
        //$monto_reclamo=$fetch33['monto'];
        
}
//<?php echo $_SERVER['PHP_SELF']; 



?>

<style type="text/css">

    .acciones {
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }

    .acciones i{
        color: #157fcc;
        font-size: 22px;
        margin: 0 5px;
        cursor: pointer;
        opacity: 0.8;
    }

    .acciones i:hover{
        opacity: 1;
    }
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
                                                    <div class="caption">
                                                        <?=$modulo;?>
                                                    </div>
                                                </div>
                                            <div class="portlet-body form ">
                                                 
                                                    <FORM name="formulario1" id="formulario1" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                                                        
                                                        <div class="form-body">
                                                            <div clss="col-sm-12">
                                                                <!--<TD height="25" class="tb-head" align="left"><strong> TIPO DE REGISTRO </strong>-->
                                                                <input type="hidden" name="editar" id="editar" value="<? echo $editar;?>">
                                                                <input type="hidden" name="cedula" id="cedula" value="<? echo $cedula;?>">
                                                                <input type="hidden" name="codigo" id="codigo" value="<? echo $_GET['codigo'];?>">
                                                                <input type="hidden" name="aprobado" id="aprobado" value="<? echo $aprobado;?>">
                                                                
                                                                <input type="hidden" name="opcion" id="opcion" value="0">
                                                                
 
                                                                <br />

                                                                <div class="form-group">
                                                                    <label class="col-sm-2 control-label">Tipo: </label>
                                                                    <div class="col-md-7">
                                                                        <select onchange="cargar_tipo_local();" name="tipo_registro" id="tipo_registro" class="select2 form-control">
                                                                            <option value="0">Seleccione</option>                                                                           
                                                                            <?                                                                                
                                                                               
                                                                            while($fila=fetch_array($resultadoExpTipo))
                                                                            {
                                                                                if(($fila['id_expediente_tipo']==1 && isset($_SESSION['exp_est'])) ||
                                                                                   ($fila['id_expediente_tipo']==2 && isset($_SESSION['exp_cap'])) ||
                                                                                   ($fila['id_expediente_tipo']==4 && isset($_SESSION['exp_per'])) ||
                                                                                   ($fila['id_expediente_tipo']==5 && isset($_SESSION['exp_amo'])) ||
                                                                                   ($fila['id_expediente_tipo']==6 && isset($_SESSION['exp_sus'])) ||
                                                                                   ($fila['id_expediente_tipo']==7 && isset($_SESSION['exp_ren'])) ||
                                                                                   ($fila['id_expediente_tipo']==8 && isset($_SESSION['exp_des'])) ||
                                                                                   ($fila['id_expediente_tipo']==9 && isset($_SESSION['exp_mov'])) ||
                                                                                   ($fila['id_expediente_tipo']==10 && isset($_SESSION['exp_eva'])) ||
                                                                                   ($fila['id_expediente_tipo']==11 && isset($_SESSION['exp_vac'])) ||
                                                                                   ($fila['id_expediente_tipo']==12 && isset($_SESSION['exp_tie'])) ||
                                                                                   ($fila['id_expediente_tipo']==13 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==14 && isset($_SESSION['exp_exp'])) ||
                                                                                   ($fila['id_expediente_tipo']==15 && isset($_SESSION['exp_liccs'])) ||
                                                                                   ($fila['id_expediente_tipo']==16 && isset($_SESSION['exp_licss'])) ||
                                                                                   ($fila['id_expediente_tipo']==17 && isset($_SESSION['exp_lices'])) ||
                                                                                   ($fila['id_expediente_tipo']==18 && isset($_SESSION['exp_orb'])) ||   
                                                                                   ($fila['id_expediente_tipo']==19 && isset($_SESSION['exp_baj'])) ||
                                                                                   ($fila['id_expediente_tipo']==20 && isset($_SESSION['exp_susp'])) ||
                                                                                   ($fila['id_expediente_tipo']==21 && isset($_SESSION['exp_cat'])) ||
                                                                                   ($fila['id_expediente_tipo']==22 && isset($_SESSION['exp_eta'])) ||
                                                                                   ($fila['id_expediente_tipo']==23 && isset($_SESSION['exp_vig'])) ||
                                                                                   ($fila['id_expediente_tipo']==24 && isset($_SESSION['exp_rea'])) ||
                                                                                   ($fila['id_expediente_tipo']==25 && isset($_SESSION['exp_rot'])) ||
                                                                                   ($fila['id_expediente_tipo']==26 && isset($_SESSION['exp_apo'])) ||
                                                                                   ($fila['id_expediente_tipo']==27 && isset($_SESSION['exp_aju'])) ||
                                                                                   ($fila['id_expediente_tipo']==28 && isset($_SESSION['exp_mis'])) ||
                                                                                   ($fila['id_expediente_tipo']==29 && isset($_SESSION['exp_cer'])) ||
                                                                                   ($fila['id_expediente_tipo']==30 && isset($_SESSION['exp_asc'])) ||
                                                                                   ($fila['id_expediente_tipo']==31 && isset($_SESSION['exp_aum'])) ||
                                                                                   ($fila['id_expediente_tipo']==32 && isset($_SESSION['exp_rev'])) ||
                                                                                   ($fila['id_expediente_tipo']==33 && isset($_SESSION['exp_mod'])) ||
                                                                                   ($fila['id_expediente_tipo']==34 && isset($_SESSION['exp_sob'])) ||
                                                                                   ($fila['id_expediente_tipo']==35 && isset($_SESSION['exp_jub'])) ||
                                                                                   ($fila['id_expediente_tipo']==36 && isset($_SESSION['exp_pro'])) ||
                                                                                   ($fila['id_expediente_tipo']==37 && isset($_SESSION['exp_ini'])) ||
                                                                                   ($fila['id_expediente_tipo']==38 && isset($_SESSION['exp_nomap'])) ||
                                                                                   ($fila['id_expediente_tipo']==39 && isset($_SESSION['exp_def'])) ||
                                                                                   ($fila['id_expediente_tipo']==40 && isset($_SESSION['exp_rei'])) ||
                                                                                   ($fila['id_expediente_tipo']==41 && isset($_SESSION['exp_rc'])) ||
                                                                                   ($fila['id_expediente_tipo']==42 && isset($_SESSION['exp_aumh'])) ||
                                                                                   ($fila['id_expediente_tipo']==43 && isset($_SESSION['exp_lib'])) ||
                                                                                   ($fila['id_expediente_tipo']==44 && isset($_SESSION['exp_cam'])) ||
                                                                                   ($fila['id_expediente_tipo']==45 && isset($_SESSION['exp_ret'])) ||
                                                                                   ($fila['id_expediente_tipo']==46 && isset($_SESSION['exp_ters'])) ||
                                                                                   ($fila['id_expediente_tipo']==47 && isset($_SESSION['exp_apl'])) ||
                                                                                   ($fila['id_expediente_tipo']==48 && isset($_SESSION['exp_pen'])) ||
                                                                                   ($fila['id_expediente_tipo']==49 && isset($_SESSION['exp_terl'])) ||
                                                                                   ($fila['id_expediente_tipo']==50 && isset($_SESSION['exp_terp'])) ||
                                                                                   ($fila['id_expediente_tipo']==51 && isset($_SESSION['exp_tern'])) ||
                                                                                   ($fila['id_expediente_tipo']==52 && isset($_SESSION['exp_ces'])) ||
                                                                                   ($fila['id_expediente_tipo']==53 && isset($_SESSION['exp_int'])) ||
                                                                                   ($fila['id_expediente_tipo']==54 && isset($_SESSION['exp_aba'])) ||
                                                                                   ($fila['id_expediente_tipo']==56 && isset($_SESSION['exp_aat'])) ||
                                                                                   ($fila['id_expediente_tipo']==57 && isset($_SESSION['exp_baja'])) ||
                                                                                   ($fila['id_expediente_tipo']==58 && isset($_SESSION['exp_reint'])) ||
                                                                                   ($fila['id_expediente_tipo']==59 && isset($_SESSION['exp_acm'])) ||
                                                                                   ($fila['id_expediente_tipo']==60 && isset($_SESSION['exp_nom'])) ||
                                                                                   ($fila['id_expediente_tipo']==61 && isset($_SESSION['exp_inv'])) ||
                                                                                   ($fila['id_expediente_tipo']==62 && isset($_SESSION['exp_ext'])) ||
                                                                                   ($fila['id_expediente_tipo']==63 && isset($_SESSION['exp_dia'])) ||
                                                                                   ($fila['id_expediente_tipo']==64 && isset($_SESSION['exp_ant'])) ||
                                                                                   ($fila['id_expediente_tipo']==65 && isset($_SESSION['exp_trb'])) ||
                                                                                   ($fila['id_expediente_tipo']==66 && isset($_SESSION['exp_con'])) ||
                                                                                   ($fila['id_expediente_tipo']==67 && isset($_SESSION['exp_car'])) ||
                                                                                   ($fila['id_expediente_tipo']==68 && isset($_SESSION['exp_con'])) ||
                                                                                   ($fila['id_expediente_tipo']==69 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==70 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==71 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==72 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==73 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==74 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==75 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==76 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==77 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==78 && isset($_SESSION['exp_doc'])) ||
                                                                                   ($fila['id_expediente_tipo']==79 && isset($_SESSION['exp_doc']))||
                                                                                   ($fila['id_expediente_tipo']==80 && isset($_SESSION['exp_doc']))||
                                                                                   ($fila['id_expediente_tipo']==81 && isset($_SESSION['exp_doc']))||
                                                                                   ($fila['id_expediente_tipo']==82 && isset($_SESSION['exp_doc']))||
                                                                                   ($fila['id_expediente_tipo']==83 && isset($_SESSION['exp_doc']))||    
                                                                                   ($fila['id_expediente_tipo']==84 && isset($_SESSION['exp_doc']))
                                                                                )
                                                                                {
                                                                                    if($editar!=1)
                                                                                    {?>
                                                                                        <option  value="<?=$fila['id_expediente_tipo'];?>"><?=utf8_encode($fila['nombre_tipo']);?></option>
                                                                                    <?}
                                                                                    else
                                                                                    { 
                                                                                       if($tipo_registro==$fila['id_expediente_tipo'])
                                                                                       {?>
                                                                                            <option  value="<?=$fila['id_expediente_tipo'];?>" selected><?=utf8_encode($fila['nombre_tipo']);?></option> 
                                                                                       <?}
                                                                                       else
                                                                                       {?>
                                                                                            <option  value="<?=$fila['id_expediente_tipo'];?>"><?=utf8_encode($fila['nombre_tipo']);?></option>
                                                                                       <?}
                                                                                    }
                                                                                }
                                                                            }                                                                                                                                                        
                                                                            ?>                                                                                                                                                           
                                                                        </select>                                                                                                                                
                                                                        
                                                                    </div>
                                                                    
                                                                </div>                                                          

                                                                <div id="registro">  

                                                                </div>

                                                                <div class="row">
                                                                    <div class="col-md-4">
                                                                    </div>                    
                                                                    <div class="col-md-2">                            
                                                                              <?php if($opt!=3) {boton_metronic('cancel',"expediente_list.php?cedula=$cedula",0);} ?>
                                                                    </div>                    

                                                                    <div class="col-md-2">
                                                                            <?php if($opt!=3) {boton_metronic('ok',"confirmar2('\u00BFSeguro desea registrar estos datos?');",2);} ?>
                                                                    </div>                    
                                                                    <div class="col-md-4">                            
                                                                    </div>

                                                                </div>

                                                                <br />




                                                        <?php
                                                        cerrar_conexion($conexion);
                                                        ?>
                                                        </FORM>
                                                    </div>
                                                </div>                                                     
                                            </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>

<div id="modal_datatable" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Implemento / Herramienta</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_tipo"><b>Articulo</b></label>
                    <select name="articulo" class="form-control" id="articulo" readonly>
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_articulo="SELECT * FROM implemento";
                    $resultado_articulo=sql_ejecutar($consulta_articulo);
                    while($fila_articulo=fetch_array($resultado_articulo))
                    {
                        
                            {?>
                                <option  value="<?=$fila_articulo['id_implemento'];?>"><?=utf8_encode($fila_articulo['nombre']);?></option>
                            <?}
                    }
                    ?>
            </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="articulo"><b>Marca</b></label>
                    <input type="text" class="form-control" id="marca" placeholder="Marca">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="articulo"><b>Modelo</b></label>
                    <input type="text" class="form-control" id="modelo" placeholder="Modelo">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="articulo"><b>Talla</b></label>
                    <input type="text" class="form-control" id="talla" placeholder="Talla">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="articulo"><b>Color</b></label>
                    <input type="text" class="form-control" id="color" placeholder="Color">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="editor_fecha"><b>Fecha Entrega</b></label>
                    <input type="text" class="form-control" id="entrega" placeholder="Fecha Entrega">
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label for="editor_fecha"><b>Fecha Vencimiento</b></label>
                    <input type="text" class="form-control" id="vencimiento" placeholder="Fecha Vencimiento">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="articulo"><b>Cantidad</b></label>
                    <input type="text" class="form-control" id="cantidad" placeholder="Cantidad">
                </div>
            </div>

            
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="onAceptar()">Aceptar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php
if($editar==1)
{
	?>
	<script type="text/javascript">        
	cargar_tipo_local();        
    buscar_correlativo(document.getElementById('tipo_registro').value);          
	</script>
	<?php
}
?>
        
        
</BODY>

</html>