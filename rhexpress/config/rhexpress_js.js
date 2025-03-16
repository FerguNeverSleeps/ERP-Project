$(document).ready(function(){
	TableDatatablesAjax.init(0);
    $('#detalles').on('show.bs.modal', function (event) {
        var button        = $(event.relatedTarget);
        var idtipo        = button.data('idtipo');
        var justificacion = button.data('justificacion');
        var modal  = $(this);
        modal.find('#TituloDetalles').text("JUSTIFICACION : "+justificacion);
        TableDatatablesAjax.recarga(idtipo);
    });
    $('#incidencias').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var ficha  = button.data('ficha');
        var fecha  = button.data('fecha');
        var modal  = $(this);
        modal.find('#TituloIncidencias').text( "FECHA : "+fecha );
        TableDatatablesAjax.recarga(fecha);
    });
});