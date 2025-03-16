$(document).ready(function() {

    $('#fecha').datepicker({
        language: 'es',
        autoclose: true
    });

    $('#hora').timepicker({
        minuteStep: 1,
        secondStep: 1,
        showSeconds: true,
        showMeridian: true,
        defaultTime: '7:30:00'
    });

	$('#formPrincipal').validate({
            rules: {
                ficha: { required: true },
                tipo_movimiento: { required: true },
                fecha: { required: true },
                hora:  { required: true }
            },
            messages: {
                ficha: { required: " " },
                tipo_movimiento: { required: " " },
                fecha: { required: " " },
                hora:  { required: " " }
            },
            highlight: function (element) { 
                $(element)
                    .closest('.form-group2').addClass('has-error'); 
            },
            success: function (label) {
                label.closest('.form-group2').removeClass('has-error');
                label.remove();
            },
    });

    $("#btn-guardar").click(function(){

    });
});