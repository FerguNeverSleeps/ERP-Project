$(document).ready(function() {

    if( (typeof errorCargando != 'undefined')   &&   errorCargando === true)
    {
        window.setTimeout(function () {
            App.unblockUI('#blockui_portlet_body');
        }, 3000);          
    }

    $('#fecha_inicio, #fecha_fin').datepicker({
        language: "es",
        autoclose: true,
        endDate: "0d",
    });

    $('#hora_inicio').timepicker({
        minuteStep: 1,
        secondStep: 1,
        showSeconds: true,
        showMeridian: false,
        defaultTime: '00:00:00'
    });

    $('#hora_fin').timepicker({
        minuteStep:   1,
        secondStep: 1,
        showSeconds:  true,
        showMeridian: false,
        defaultTime: '23:59:59'
    });

	$('#formPrincipal').validate({
            rules: {
                configuracion: { required: true },
                archivo: { 
                	required: true,
                	//extension: "txt|csv|log|xls"
                },
                //fecha_inicio: { required: true},
                //fecha_fin: { required: true}
            },
            messages: {
                configuracion: { required: " " },
                archivo: { 
                	required: " ",
                	//extension: "Archivo inválido"
                },
                //fecha_inicio: { required: " "},
                //fecha_fin: { required: " " }
            },
            highlight: function (element) { 
                $(element)
                    .closest('.form-group').addClass('has-error'); 

                if( $(element).attr('id') == 'archivo' )
                	$( "input:file" ).css({ color: "#b94a48" });
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
    });

    $("#configuracion").change(function(){
    	var valor = $(this).val();

    	if(valor!='')
            $(this).closest('.form-group').removeClass('has-error');     		
    });

	$('#archivo').on("change", function(){ 
		var valor = $(this).val();

        var fileName = this.files[0].name;
        var fileSize = this.files[0].size;
        var fileType = this.files[0].type;
        //console.log('FileName: ' + fileName + '\nFileSize: ' + fileSize + ' bytes' + '\nFileType: ' + fileType);

    	if(valor!=''){
            $(this).closest('.form-group').removeClass('has-error');  	
            $( "input:file" ).css({ color: "#000" });	
    	}
	});

    $("#btn-guardar").click(function(){

        var validacion = $('#formPrincipal').valid();

        var fecha_ini = $("#fecha_inicio").val();
        var fecha_fin = $("#fecha_fin").val();
        var hora_ini  = $("#hora_inicio").val();
        var hora_fin  = $("#hora_fin").val();

        if( (fecha_ini!='' && fecha_fin=='') || (fecha_ini=='' && fecha_fin!='') )
        {
            alert("Debe indicar tanto la fecha de inicio como la fecha fin");
            return false;
        }

        if( fecha_ini!='' && fecha_fin!='' && ( hora_ini == '' || hora_fin == '') )
        {
            alert("Debe indicar tanto la hora de inicio como la hora fin");
            return false;
        }

        if(!validacion) return false;

        fecha_ini = fecha_ini.split('-').reverse();  
        fecha_fin = fecha_fin.split('-').reverse();       
        hora_ini  = hora_ini.split(':');
        hora_fin  = hora_fin.split(':');
        fecha_ini = new Date(fecha_ini[0], (fecha_ini[1]-1), fecha_ini[2], hora_ini[0], hora_ini[1], hora_ini[2], 0); 
        fecha_fin = new Date(fecha_fin[0], (fecha_fin[1]-1), fecha_fin[2], hora_fin[0], hora_fin[1], hora_fin[2], 0); 

        //console.log("Fecha inicio: " + fecha_ini + " / Fecha fin:" + fecha_fin);

        if ( fecha_ini.getTime() > fecha_fin.getTime() ) 
        {
            alert("La fecha de inicio no puede ser mayor que la fecha fin");
            return false;
        }

        var filename  = $("#archivo").val();
        var extension = filename.split('.').pop();

        var elemento = $("#configuracion").find('option:selected');         
        var formato  = elemento.attr('data-id');

        if(extension != formato)
        {
            // console.log("Extension Archivo: " + extension + "\nExtension Permitida: " + formato);
            alert("El archivo tiene un formato diferente a la configuraci\u00F3n seleccionada");
            return false;
        }

        App.blockUI({
            target: '#blockui_portlet_body',
            boxed: true,
            message: 'Procesando...',
        });

        //if (!confirm("\u00BFEst\u00E1 seguro de cargar el archivo?")) return false; 
    });
});