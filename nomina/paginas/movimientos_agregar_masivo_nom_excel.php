<?php 
session_start();
ob_start();
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

require_once '../lib/common.php';
//include ("../header4.php");
include("../../includes/dependencias.php");
include ("funciones_nomina.php");
include ("func_bd.php") ;

require_once("../../procesos/funciones_importacion.php");
require_once("../../includes/phpexcel/Classes/PHPExcel.php");
require_once("../../includes/phpexcel/Classes/PHPExcel/IOFactory.php");
date_default_timezone_set('America/Panama');

$url     ="movimientos_agregar_masivo_nom_excel";
$modulo  ="Agregar Movimientos Nomina - Formato Excel";
$tabla   ="nomconceptos";

$titulos =array("Ficha","Cedula","Nombre","Agregar?");
$indices =array("ficha","cedula","apenom");

$ficha   =$_GET['ficha'];
$todo    =$_GET['todo'];

if(!isset($_POST['nomina']))
{
	$nombre_nomina=$_GET['nomina'];
}
else
{
	$nombre_nomina=$_POST['nomina'];
}


$conexion   =conexion();

$tabla="";

$consulta         ="SELECT * FROM nom_nominas_pago "
                . "WHERE codnom='".$nombre_nomina."' AND tipnom='".$_SESSION['codigo_nomina']."'";
$resultado_nom    =query($consulta,$conexion);
$fila_nom         =fetch_array($resultado_nom);
$CODNOM           =$nombre_nomina;
$FECHANOMINA      =$fila_nom['periodo_ini'];
$FECHAFINNOM      =$fila_nom['periodo_fin'];
$LUNES            =lunes($FECHANOMINA);	
$LUNESPER         =lunes_per($FECHANOMINA,$FECHAFINNOM);
$consulta         ="select monsalmin from nomempresa";
$resultado_salmin =query($consulta,$conexion);
$fila_salmin      =fetch_array($resultado_salmin);

if(isset($_POST['opcion']) and $_POST['opcion']=="Guardar")
{
//    echo "0";
//    print_r($_FILES);
    $tabla='<table class="table table-striped table-bordered table-hover" style="font-size: 10px;" id="table_datatable">
	<thead>
			<tr>
			<th>FICHA</th>                        
			<th>NOMBRE Y APELLIDO</th>
			<th>CONCEPTO</th>
			<th>VALOR</th>
			<th>RESULTADO</th>
                        </tr>
	</thead>
	<tbody>';
    if($_FILES['archivo']['name'] != '')
    {
//	echo "1";
//	print_r($_FILES);
	$name	  = $_FILES['archivo']['name'];
	$tname 	  = $_FILES['archivo']['tmp_name'];
	$type 	  = $_FILES['archivo']['type'];

	if($type == 'application/vnd.ms-excel')
		$ext = 'xls'; // Extension excel 97
	else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
	{
		$ext = 'xlsx'; // Extension excel 2007 y 2010
	}else{
		if (function_exists('finfo_file')) 
		{
			$type  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tname);

			if($type == 'application/vnd.ms-excel'){
				$ext = 'xls'; // Extension excel 97
			}else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
				$ext = 'xlsx'; // Extension excel 2007 y 2010		
			}
		}
		if(!isset($ext))
		{
			$mensaje = "¡Error! Extensión de archivo inválida.";
			$success = false;
		}
	}
	if ($ext == "") {
		$archivo_tmp = explode(".", $name);
		$nombre_archivo = $archivo_tmp[0];
		$ext = $archivo_tmp[1];
	}

	if(isset($ext))
	{
		//echo "2";

		$xls         = 'Excel5';
		$xlsx        = 'Excel2007';
		// Creando el lector
		$objReader   = PHPExcel_IOFactory::createReader($$ext);
                $objPHPExcel = $objReader->load($tname);
                $sheet = $objPHPExcel->setActiveSheetIndex(0);
                $i=2;
                foreach ($sheet->getRowIterator() as $row)
                {
                    
                    $A = $sheet->getCell("A$i")->getValue();
                    $B = $sheet->getCell("B$i")->getValue();
                    $C = $sheet->getCell("C$i")->getValue();

                    $ficha = trim($A);
                    if(!$ficha) continue;

                    $concepto = trim($B);
                    if(!$concepto) continue;
                    
                    $valor = trim($C);
                    if(!$valor) continue;
                    $referencia=$valor;
//                    print "$ficha | $concepto | $valor \n";
                    
                   

                    $consulta         ="select * from nompersonal where ficha='".$ficha."' and tipnom='".$_SESSION['codigo_nomina']."'";
                    $resultado        =query($consulta,$conexion);
                    $fila             =fetch_array($resultado);
                    $CEDULA           = $fila[cedula];
                    $NOMBRE           = $fila[apenom];
                    $FICHA            = $fila[ficha];
                    $SUELDO           =$fila[suesal];//LISTO
                    $SEXO             =".".$fila[sexo]."'";
                    $HORABASE         = $fila[hora_base];
                    $FECHANACIMIENTO  =date("d/m/Y",strtotime($fila[$fecnac]));
                    $EDAD             =date("Y")-date("Y",$fila[$fecnac]);
                    $TIPONOMINA       =$fila[tipnom];//LISTO
                    $FECHAINGRESO     =$fila[fecing];//LISTO
                    $CODPROFESION     =$fila[codpro];
                    $CODCATEGORIA     =$fila[codcat];
                    $CODCARGO         =$fila[codcargo];
                    $SITUACION        =$fila[estado];
                    $SUELDOPROPUESTO  =$fila[sueldopro];
                    $TIPOCONTRATO     =$fila[contrato];
                    $FORMACOBRO       =$fila[forcob];
                    $NIVEL1           =$fila[codnivel1];
                    $NIVEL2           =$fila[codnivel2];
                    $NIVEL3           =$fila[codnivel3];
                    $NIVEL4           =$fila[codnivel4];
                    $NIVEL5           =$fila[codnivel5];
                    $NIVEL6           =$fila[codnivel6];
                    $NIVEL7           =$fila[codnivel7];
                    $FECHAAPLICACION  =$fila[fechaplica];
                    $TIPOPRESENTACION =$fila[tipopres];
                    $FECHAFINSUS      =$fila[fechasus];
                    $FECHAINISUS      =$fila[fechareisus];
                    $FECHAFINCONTRATO =$fila[fecharetiro];
                    $FECHAVAC         =$fila[fechavac];
                    $FECHAREIVAC      =$fila[fechareivac];
                    $CONTRACTUAL      =$fila[contractual];
                    $PRT              =$fila[proratea];
                    $REF              =0;
                    $SALARIOMIN       =$fila_salmin['monsalmin'];
                    if($SITUACION!="Egresado")
                    {


                        $consulta_mov="SELECT * FROM nom_movimientos_nomina "
                                . "WHERE codcon='".$concepto."' AND codnom='".$nombre_nomina."' "
                                . "AND ficha ='".$ficha."' AND tipnom='".$_SESSION['codigo_nomina']."'";

                        $resultado_mov=mysqli_query($conexion, $consulta_mov);
                        if(num_rows($resultado_mov)==0)
                        {
                                $consulta="SELECT * FROM nomconceptos WHERE codcon='".$concepto."'";
                                $resultado_con=mysqli_query($conexion, $consulta);
                                $fila=fetch_array($resultado_con);
                                $REF=$referencia;
                                //echo $formula[$valor];
                                eval($fila['formula']);

                                //echo $consulta," <br>";

                                if($MONTO<=0 && $fila['montocero']==1)
                                {
                                        $entrar=0;
                                }
                                else
                                {
                                        $entrar=1;
                                }

                                if($entrar==1)
                                {
                                        $consulta="INSERT INTO nom_movimientos_nomina "
                                                . "(codnom, "
                                                . "codcon,"
                                                . "ficha,"
                                                . "mes,"
                                                . "anio,"
                                                . "tipcon,"
                                                . "valor,"
                                                . "monto,"
                                                . "cedula,"
                                                . "unidad,"
                                                . "descrip,"
                                                . "codnivel1,"
                                                . "codnivel2,"
                                                . "codnivel3,"
                                                . "codnivel4,"
                                                . "codnivel5,"
                                                . "codnivel6,"
                                                . "codnivel7,"
                                                . "tipnom,"
                                                . "contractual) "
                                                . "VALUES ('".$_POST['nomina']."',"
                                                . " '".$concepto."',"
                                                . "'".$ficha."',"
                                                . "'".$fila_nom['mes']."',"
                                                . "'".$fila_nom['anio']."',"
                                                . "'".$fila['tipcon']."',"
                                                . "'".$REF."',"
                                                . "'".$MONTO."',"
                                                . "'$CEDULA',"
                                                . "'".$fila['unidad']."',"
                                                . "'".$fila['descrip']."',"
                                                . "'$NIVEL1',"
                                                . "'$NIVEL2',"
                                                . "'$NIVEL3',"
                                                . "'$NIVEL4',"
                                                . "'$NIVEL5',"
                                                . "'$NIVEL6',"
                                                . "'$NIVEL7',"
                                                . "'".$_SESSION['codigo_nomina']."',"
                                                . "'".$fila['contractual']."')";
                                        //echo $consulta,"<br>";
                                        $resultado=mysqli_query($conexion, $consulta);
                                        if(!$resultado)
                                        {
                                                echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
                                                alert('No se puede calcular conceptos a esta persona')
                                                </SCRIPT>";
                                        }
                                        $tabla.= '<tr>';
                                        $tabla.=  "<td>".$FICHA."</td>";
                                        $tabla.=  "<td>".utf8_decode($NOMBRE)."</td>";
                                        $tabla.=  "<td>".$concepto."</td>";
                                        $tabla.=  "<td>".$valor."</td>";
                                        $tabla.=  "<td>INSERTADO</td>";
                                        $tabla.=  '</tr>';
                                }
                        }	
                    }
                    $i++;
                }
        }       
    }
    $tabla.= '</tbody>  
        </table>';
}
/*
else
{
	echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	alert('No se puede calcular')
	</SCRIPT>";
}
*/
$consulta="SELECT ficha, cedula, apenom FROM nompersonal WHERE tipnom='$_SESSION[codigo_nomina]' AND estado='Activo'";
$result=query($consulta,$conexion);
?>
<script language="JavaScript" type="text/javascript">


function enviar(op)
{
	document.frmPrincipal.opcion.value=op;
	
//	if((val1.value==0)||(val1.value=='')||(val3.value==0)||(val3.value==''))
//	{
//		alert("DEBE INTRODUCIR DATOS VALIDOS... VERIFIQUE");
//		return
//	}
	document.frmPrincipal.submit();
}


$(document).ready(function(){
            $("#form_excel").validate({
			rules: {
				archivo: {
					required: true,
					extension: "xls|xlsx"
				}
			},
			messages: {
				archivo: {
					// required:  "Por favor, seleccione un archivo",
					 extension: "Extensión de archivo inválida"
				}
			},
			errorPlacement: function (error, element) {
				//console.log(error.text());
                $(element).closest('.form-group').addClass('has-error').find('.help-block').html(error.text()); 
		    },
		    highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error'); 
		    },
		    success: function (label, element) {
		    	$(element).closest('.form-group').removeClass('has-error'); 
                label.closest('.form-group').removeClass('has-error');
                label.text('');
               // label.remove();                
		    }
	});
        
	var fileinput = $('.fileinput').fileinput();
	fileinput.on('change.bs.fileinput', function(e, files){
	    $("#archivo").closest('.form-group').removeClass('has-error');
        $(".help-block").closest('.form-group').removeClass('has-error');
        $(".help-block").text('');
	});
        
        $('#table_datatable').DataTable({
    	"iDisplayLength": 10,
    	"bStateSave" : true,
    	"sPaginationType": "bootstrap_extended",
        "aaSorting": [[0, 'asc']],
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
//        "aoColumnDefs": [
//        	{ 'bSortable': false, 'aTargets': [1, 3] }, // 'bVisible': false,
//            { 'bSortable': false, 'bSearchable': false, 'aTargets': [7, 8, 9, 10, 11, 12, 13, 14,15] }
//        ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		}
	});

   	$('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");
	$('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
	$('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
});

</script>
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo $modulo; ?>
							</div>
							<div class="actions">
								

								<?php boton_metronic('cancel',"window.close();",2); ?>
							</div>
						</div>
						<div class="portlet-body">
							<FORM name="frmPrincipal"  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" target="_self" enctype="multipart/form-data">
								
								<input name="opcion" id="opcion" type="hidden" value="">
								<input name="nomina" id="nomina" type="hidden" value="<?echo $nombre_nomina?>">
								<div class="row">
                                                                    <div class="form-group hide" style="margin-top: 20px">
                                                                                <label for="archivo" class="col-md-3 control-label">Archivo:</label>
                                                                                <div class="col-md-8">
                                                                                        <input type="file" id="archivo1" name="archivo1" style="outline: none" class="required">
                                                                                </div>
                                                                        </div>

                                                                        <div class="form-group" style="margin-top: 20px">
                                                                                <label class="control-label col-md-2">Archivo</label>
                                                                                <div class="col-md-9">
                                                                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                                                <div class="input-group">
                                                                                                        <div class="form-control uneditable-input span3" data-trigger="fileinput">
                                                                                                                <!--<i class="fa fa-file fileinput-exists"></i>&nbsp;-->
                                                                                                                <span class="fileinput-filename">
                                                                                                                </span>
                                                                                                        </div>
                                                                                                        <span class="input-group-addon btn default btn-file">
                                                                                                                <span class="fileinput-new">
                                                                                                                         SELECCIONE
                                                                                                                </span>
                                                                                                                <span class="fileinput-exists">
                                                                                                                         CAMBIAR
                                                                                                                </span>
                                                                                                                <input type="file" id="archivo" name="archivo" class="required">														
                                                                                                        </span>
                                                                                                        <a href="#" class="input-group-addon btn default fileinput-exists" data-dismiss="fileinput">
                                                                                                                 ELIMINAR
                                                                                                        </a>
                                                                                                </div>
                                                                                        </div>
                                                                                        <span class="help-block"></span>
                                                                                </div>
                                                                        </div>
									
									
								</div>
								<div class="row">&nbsp;</div>

								<div class="row">&nbsp;</div>
								<div class="row text-center">
									
										<?php boton_metronic2("ok","enviar('Guardar');",2) ?>
                                                                                <?php boton_metronic2('cancel','window.close();',2) ?>
									
								</div>
                                                                <div class="row">&nbsp;</div>
                                                                
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                            <?php if($_POST['opcion']=="Guardar") echo $tabla; ?>
                                                                    </div>
                                                                </div>
                                                                
                                                                
								
								
							</FORM>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</BODY>
</html>

<?php
//include ("../footer4.php");

cerrar_conexion($conexion);?>
