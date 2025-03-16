$(document).ready(function() {
    
    var triggers = $(".modalInput").overlay({
 
      // some mask tweaks suitable for modal dialogs
      mask: {
        color: '#ebecff',
        loadSpeed: 100,
        opacity: 0.9
      },
 
       closeOnClick: false
  });

    $("#prompt form").submit(function(e) {
 
      // close the overlay
      triggers.eq(1).overlay().close();
 
      // get user input
      var input = $("input", this).val();
 
      // do something with the answer
      triggers.eq(1).html(input);
 
      // do not submit the form
      return e.preventDefault();
  });
  
  
    $('tr.edocuenta').mouseover(function() {
        if ($(this).attr("bgcolor") != "#b6ceff") {
            $(this).attr("bgcolor", "#fbf6f6");
        }
    }).mouseout(function() {
        if ($(this).attr("bgcolor") != "#b6ceff") {
            $(this).attr("bgcolor", "#ececec");
        }
    });
    $(".eliminarAsiento").live("click", function(e) {
        if (confirm("¿Est&aacute; seguro(a) que desea eliminar este asiento?")) {
            det_transaccion = $(this).parents("td").find("input[name='detalle_asiento']").val();
            $.ajax({
                type: 'GET',
                data: 'opt=eliminar_asientoCXC&cod=' + det_transaccion,
                url: '../../libs/php/ajax/ajax.php',
                beforeSend: function() {
                },
                success: function(data) {
                    //objeto.after(data);
                    if (data == 1) {
                    }
                }
            });
            window.location.href = $("input[name='url_delete_asientos']").val();
        }
    });
    $('tr.detalle').click(function() {
        objeto = $(this);
        //Deseleccionamos cualquier fila cambiandole el color del tr
        objeto.parents("tbody").find(".detalle").attr("bgcolor", "#ececec");
        //Seleccionamos la fila a la cual se dio click para conocer detalles
        $(this).attr("bgcolor", "#b6ceff");
        //Removemos cualquier detalle que este cargado en la tabla de estado de cuenta
        objeto.parents("tbody").find(".detalle_items").remove();
        //Le colocamos la imagen que indica que puede hacer click para desplegar informacion
        objeto.parents("tbody").find(".boton_detalle").attr("src", "../../libs/imagenes/drop-add.gif");
        //Le coloca la imagenes a la fila tr que disparo el evento click.
        objeto.find(".boton_detalle").attr("src", "../../libs/imagenes/drop-add2.gif");
        /*objeto.find("input[name='desplegado']").attr("value","true");
        if(objeto.find("input[name='desplegado']").val()==="true")
            objeto.parents("tbody").find(".detalle_items").remove();*/
        //Cargamos el codigo del cliente y el codigo del estado de cuenta de X factura.
        id_transaccion = objeto.find("input[name='id_transaccion']").val();
        id_tipo_movimiento_almacen = objeto.find("input[name='id_tipo_movimiento_almacen']").val();
        //Cargamos los debitos y creditos
        $.ajax({
            type: 'GET',
            data: 'opt=det_items&id_transaccion=' + id_transaccion + '&id_tipo_movimiento_almacen=' + id_tipo_movimiento_almacen,
            url: '../../libs/php/ajax/ajax.php',
            beforeSend: function() {
            },
            success: function(data) {
                objeto.after(data);
            }
        });
    });
});

