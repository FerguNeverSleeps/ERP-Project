<?php 
header('X-XSS-Protection:0');
include_once('clases/database.class.php');
include('obj_conexion.php');

require_once '../lib/common.php';


if(isset($_REQUEST["info_tabla"])){
    $data=$_REQUEST["info_tabla"];
    $data=json_decode($data,true);
    for($i=0; $i < count($data); $i++) { 
        print $data[$i]["codigo"]." ".$data[$i]["fecha"]." ".$data[$i]["tipo"]." ".$data[$i]["nombre_apellido"]." ".$data[$i]["monto"]."<br>";

    }
}


 
?>

<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
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
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12" >
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Plantilla Editor Datatable
							</div>
                            <div class="actions">
                                <a class="btn btn-sm blue" onclick="onAgregar()">
                                    <i class="fa fa-plus"></i> Agregar
                                </a>
                            </div>
						</div>
						<div class="portlet-body">	
                            <form method="post" onsubmit="return onSubmit()">
    							<table class="table table-striped table-bordered table-hover" id="table_datatable">
    							<thead>	                          
                                    <th>Codigo</th>
                                    <th>Fecha</th>                                                          
    								<th>Tipo</th>
                                    <th>Nombre/Apellido</th>
                                    <th>Monto</th>
                                    <th width="1%">Acciones</th>
    							</thead>
    							<tbody></tbody>
    							</table>


                                <input type="hidden" name="info_tabla" id="info_tabla" value="">
                                <button class="btn btn-primary" type="submit">Guardar</button>
                            </form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>


<div id="modal_datatable" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Plantilla Editor Datatable</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_codigo"><b>Código</b></label>
                    <input type="text" class="form-control" id="editor_codigo" placeholder="Código">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_fecha"><b>Fecha</b></label>
                    <input type="text" class="form-control" id="editor_fecha" placeholder="Fecha">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_tipo"><b>Tipo</b></label>
                    <select class="form-control" id="editor_tipo">
                        <option>Grupo A</option>
                        <option>Grupo B</option>
                        <option>Grupo C</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_nombre_apellido"><b>Nombre / Apellido</b></label>
                    <input type="text" class="form-control" id="editor_nombre_apellido" placeholder="Nombre / Apellido">
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="editor_monto"><b>Monto</b></label>
                    <input type="number" class="form-control" id="editor_monto" placeholder="Monto" min="0">
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

<?php include("../footer4.php"); ?>
<script type="text/javascript">
        $.fn.datepicker.dates.es={"days":["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"],"daysShort":["Dom","Lun","Mar","Mié","Jue","Vie","Sáb"],"daysMin":["Do","Lu","Ma","Mi","Ju","Vi","Sá"],"months":["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Deciembre"],"monthsShort":["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],"today":"Hoy","clear":"Limpiar","titleFormat":"MM yyyy"};
        $.fn.datepicker.defaults.language="es";
        $.fn.datepicker.defaults.todayHighlight=true;
        $.fn.datepicker.defaults.autoclose=true;
        $.fn.datepicker.defaults.format="dd/mm/yyyy";

        var editor_index="";

        function onAgregar(){
            editor_index="";
            $("#editor_codigo").val("");
            $("#editor_fecha").val("");
            $("#editor_tipo").val("");
            $("#editor_nombre_apellido").val("");
            $("#editor_monto").val("0");

            $("#modal_datatable").modal("show");
        }

        function onEditar(index){
            editor_index=index;
            var row=$('#table_datatable').DataTable().fnGetData(index);

            $("#editor_codigo").val(row["codigo"]);
            $("#editor_fecha").val(row["fecha"]);
            $("#editor_tipo").val(row["tipo"]);
            $("#editor_nombre_apellido").val(row["nombre_apellido"]);
            $("#editor_monto").val(row["monto"]);

            $("#modal_datatable").modal("show");
        }        

        function onEliminar(index){
            if(!confirm("Se eliminará el registro.\n¿Desea continuar?")) return;
            $('#table_datatable').DataTable().fnDeleteRow(index);
            //codigo para forzar refrescamiento de la tabla al borrar
            var data=$('#table_datatable').DataTable().fnGetData();
            for(var i=0; i<data.length; i++){
                data[i].accion="";
                $('#table_datatable').DataTable().fnUpdate( data[i], i );
            }
        }

        function onAceptar(){
            var data={
                codigo:          $("#editor_codigo").val(),
                fecha:           $("#editor_fecha").val(),
                tipo:            $("#editor_tipo").val(),
                nombre_apellido: $("#editor_nombre_apellido").val(),
                monto:           $("#editor_monto").val(),
                accion:          ""
            };

            if(editor_index==="")//si es agregar
                $('#table_datatable').DataTable().fnAddData(data);
            else//si es editar
                $('#table_datatable').DataTable().fnUpdate( data, editor_index );
            $("#modal_datatable").modal("hide");
        }

        //metodo 1, enviar data con ajax para el guardado
        function onGuardar(){
            var data=$('#table_datatable').DataTable().fnGetData();
            console.log(data);

        }

        //metodo 2, enviar data en el submit del formulario
        function onSubmit(){
            var data=$('#table_datatable').DataTable().fnGetData();
            $("#info_tabla").val(JSON.stringify(data));
            return true;
        }

        function onInit(){
            //armar el array con la data inicial de la tabla
            //llamado ajax para obtener la data o json_encode con php
            var data=[
                {
                  codigo: "001",
                  fecha: "01/02/2018",
                  tipo: "Grupo A",
                  nombre_apellido: "Pedro Perez",
                  monto: "1000.00",
                  accion: "",
                },              
                {
                  codigo: "002",
                  fecha: "16/04/2019",
                  tipo: "Grupo B",
                  nombre_apellido: "Pepito Pregunton",
                  monto: "100.00",
                  accion: "",
                }
            ];

            $('#table_datatable').DataTable().fnClearTable();
            $('#table_datatable').DataTable().fnAddData(data);
        }

    
	    $(document).ready(function() { 	 		
            $("#editor_fecha").datepicker();

            $('#table_datatable').DataTable({
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
                "aoColumns": [
					{"mDataProp": "codigo"},
					{"mDataProp": "fecha"},
					{"mDataProp": "tipo"},
					{"mDataProp": "nombre_apellido"},
					{"mDataProp": "monto"},
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
                    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");

            onInit();
	    });           
</script>
</body>
</html>