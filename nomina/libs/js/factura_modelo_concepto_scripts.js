var App =  {
    collections: {
        instancias:{}
    },
    models: {
        instancias:{}
    },
    views:{
        instancias:{}
    }
};

$(function(){

    var rows = $("#tipo_factura_concepto").val();

    $("#comprobante").change(function(e){
        var tipo = $(this).val();
        var tipo_comprobante = JSON.parse(rows);
        $("#id_tipo_factura_concepto").find("option").remove();

        if(tipo=="factura"){
            $(".detalle_concepto").hide();
            return false;
        }

        $(".detalle_concepto").show();
        if(tipo=="n_credito"){
            for(tc in tipo_comprobante){
                if(tipo_comprobante[tc].tipo=="n_debito" || tipo_comprobante[tc].tipo=="ambos"){
                    $("#id_tipo_factura_concepto").append("<option value='"+tipo_comprobante[tc].id+"'>"+tipo_comprobante[tc].descripcion+"</option>");
                    $(".panel-banco").show();
                    $(".panel-cuenta-contable").hide();
                }
            }
        }
        if(tipo=="n_debito"){
            for(tc in tipo_comprobante){
                if(tipo_comprobante[tc].tipo=="n_credito" || tipo_comprobante[tc].tipo=="ambos"){
                    $("#id_tipo_factura_concepto").append("<option value='"+tipo_comprobante[tc].id+"'>"+tipo_comprobante[tc].descripcion+"</option>");
                    $(".panel-banco").hide();
                    $(".panel-cuenta-contable").show();
                }
            }
        }
    });

    App.views.ProductoTr = Backbone.View.extend({

        tagName: "tr",

        template: _.template($('#template-tr-producto').html()),

        events: {
            "click a.btn_eliminar" : "eliminar_fila",
            "change input[type='text']" : "actualizar_campo"
        },

        initialize: function(opt) {
            var self = this;
            this.model.on("change:cantidad change:precio", function(){
                var cantidad = parseInt(this.model.get("cantidad"));
                var precio = parseFloat(this.model.get("precio"));

                var importe = (cantidad * precio).toFixed(2);

                this.model.set("importe", importe);
                this.$el.find("input[name='_importe[]']").val(importe);
                this.totalizar_();
            }, this);
        },

        totalizar_: function(){
            var total = 0;
            _.each(this.collection.models, function(model, index){  
                total += parseFloat(model.get("importe")); 
            });
            $("input[name='total']").val(total.toFixed(2));
        },

        actualizar_campo: function(ev) {
            var $input = $(ev.currentTarget);
            var campo = $input.attr("data-name");
            var tipo = $input.data("tipo");
            var value = $input.val();
            switch(tipo){
                case "string": break;
                case "float":
                    value = (_.isNaN(parseFloat(value))) ?  0 : parseFloat(value);
                    $input.val(value.toFixed(2));
                    break;
                case "entero":
                    value = (_.isNaN(parseInt(value))) ?  0 : parseFloat(value);
                    $input.val(value);
                    break;
            }
            this.model.set(campo, value);
        },

        eliminar_fila: function(ev) {
            ev.preventDefault();
            this.collection.remove(this.model);
            this.totalizar_();
            this.remove();
        },

        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        }
    });

    App.models.producto = Backbone.Model.extend({
        defaults: {
            correlativo:0,
            descripcion: "",
            cantidad:0,
            precio:0,
            importe: 0
        }
    });

    App.collections.Productos = Backbone.Collection.extend({
        model: App.models.productos,
    });

    App.collections.instancias.productos = new App.collections.Productos();
    App.collections.instancias.productos.on("add", function(currentCollection){
        currentCollection.set("correlativo",this.length);
        productoTr = new App.views.ProductoTr({model: currentCollection, collection: this});
        $("#productos").find("tbody").append(productoTr.render().el);
    });

    var producto = new App.models.producto();

    App.collections.instancias.productos.add(producto);
    
    $("#agregar_fila").click(function(ev){
        var producto = new App.models.producto();
        App.collections.instancias.productos.add(producto);
    });

    $("input[name='procesar_factura']").click(function(){
        var total = parseFloat($("input[name='total']").val());
        var validador = true;
        var mensaje = "";
        
        if(App.collections.instancias.productos.models.length==0){
            mensaje = "Debe cargar un producto!";
            validador=false;
        }else{
            _.each(App.collections.instancias.productos.models, function(model, index){  
                if(_.str.isBlank(model.get("descripcion"))) {
                    mensaje = "Debe llenar todos los campos";
                    validador=false;
                    return false;
                }
            });
        }

        if(validador){
            if(total==0){
                alert("El total debe ser mayor a cero (0), verifique.");
                return false;
            }

            if(confirm("¿Desea salvar esta operación?")){
                $("input[name='cantidad_items']").val(App.collections.instancias.productos.length);
                $("#formulario").submit();
            }
        }else{
            alert(mensaje);
        }
    });

    $(".numeric").numeric();
});