$(document).ready(function() {      
    //mostrarListado();
    function mostrarListado(){
        var html = '';
        html='  <table class="table table-bordered table-striped dt-responsive" id="tabla_nomina_individual" width="100%">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="width:10px">#</th>';
        html += '<th>Nombre</th>';
        html += '<th>Acciones</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tfoot>';
        html += '<tr>';
        html += '<th style="width:10px">#</th>';
        html += '<th>Nombre</th>';
        html += '<th>Acciones</th>';
        html += '</tr>'
        html += '</tfoot>'
        html += '</table>';

        $("#listadoNominaIndividual").empty();
        $("#listadoNominaIndividual").append( html );

        $('#tabla_nomina_individual').DataTable( {
            "ajax": "ajax/tabla_nomina_individual.ajax.php",
            "defenRender":true,
            "retrieve":true,
            "processing":true,
                "language": {

                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
                },
                "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                }

            }
        } );
    }
    $("#generarNomina").on("click", function(){
        $("#modalGenerarNominaIndividual").modal("show");
        $("#modal_ver").modal("show");
        listarAnio();

        $("#btn-procesar").off().on("click",function()
        {
        
            $.blockUI({ 
                message: '<span><img src="../../paginas/imagenes/loader_1.gif"  width="25%" height="25%"></span><br><span><h4>Enviando Nómina Emisión...<h4></b></span>',
                css: { top: '8%', left: '35%', right: '' } 
            });
    
            $("#modalGenerarNominaIndividual").modal("hide");
        
            var datos = new FormData();
            datos.append("enviarNominaIndividual","yes");
            datos.append("codtip",$("#codtip").val());
            datos.append("anio",$("#anio").val());
            datos.append("mes",$("#mes").val());
            datos.append("fecha_inicio",$("#fecha_inicio").val());
            datos.append("fecha_fin",$("#fecha_fin").val());
            datos.append("descripcion",$("#descripcion").val());
            $.ajax({
                url : "ajax/Payroll.ajax.php",
                method: "POST",
                data : datos,
                cache: false,
                contentType : false,
                processData : false ,
                dataType : "json" ,
                success: function(respuesta){
                    $(document).ajaxStop($.unblockUI);
                    alert("Nominas generadas con éxito de los colaboradores");
                    $("#modal_ver").modal("hide");
                    location.href = "nomina_individual.php";
                }
            });

        });

    });
    function listarAnio(){
        
        var datos = new FormData();
        datos.append("listarAnioNomina","yes");
        $.ajax({
            url : "ajax/Nomina.ajax.php",
            method: "POST",
            data : datos,
            cache: false,
            contentType : false,
            processData : false ,
            dataType : "json" ,
            success: function(respuesta){
                console.log(respuesta);
                var html = "";
                respuesta.forEach(element => {
                    html += "<option value='"+element.anio+"'>"+element.anio+"</option>";
                });
                $("#anio").empty();
                $("#anio").append(html);
            }
        });
    }
            
});