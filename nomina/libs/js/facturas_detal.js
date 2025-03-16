var collection_factura_detal = {};
$(function(){
    App = {};
    // Funcion comun
    App.Model_ItemFacturasDetal = Backbone.Model.extend({
        defaults: {
            _checked: false,
            _item_cantidad: "",
            _item_preciosiniva: "",
            _item_totalconiva: "",
            cod_item: "",
            descripcion: "",
            fecha_fin: "",
            fecha_inicio: "",
            id_item: ""
        }
    });

    App.Collection_ItemFacturasDetal = Backbone.Collection.extend({
        model: App.Model_ItemFacturasDetal
    });

    App.mostarVentana = function(param){
        var viewExterno = (!_.isUndefined(param.contentEl)) ? true : false;
        var winClave = new Ext.Window({
            title: param.title,
            height: param.height,
            width: param.width,
            autoScroll:true,
            modal:true,
            bodyStyle:"background:white;padding:5px;",
            closeAction:'hide',
            contentEl: param.contentEl,
            buttonAlign:'center',
            buttons:[
                {
                    text: param.labelOk,
                    // icon: '../../libs/imagenes/save.gif',
                    handler:function()
                    {
                        param.accionOk(winClave);
                    }
                },
                {
                    text:'Cerrar',
                    icon: '../../libs/imagenes/cancel.gif',
                    handler:function()
                    {
                        winClave.hide();
                    }
                }
            ]
        }).show();
    }   

    App.TrFacturaDetal = Backbone.View.extend({
        tagName: "tr",
        template: _.template($('#template_tr_factura_detal').html()),
        events: {
            "click input[name='check_item']": "btnCheck"
        },
        btnCheck: function(ev){
           var checked = $(ev.currentTarget).is(":checked");
           this.model.set("_checked", checked);
           if(checked==false){
            $("input[name='check_all']").prop("checked",false);
           }
        },
        render: function() {
            this.$el.html(this.template(this.model.toJSON()));
            return this;
        }
    });

    // Inicio de Vista
    App.MainFacturaDetal = Backbone.View.extend({
        tagName: "div",
        className: "factura_detal",
        template: _.template($('#template_main_factura_detal').html()),
        events: {
            "click .btn_filtro_factura_detal": "btnFiltro",
            "click input[name='check_all']": "btnCheckAll"
        },
        initialize: function() {
            this.valor_defecto = "<tr><td colspan='5'><strong style='padding: 5px;display: block;font-weight: bold;'>No se encontraron resultados.</strong></td><tr>";
            this.buscando = "<tr class='buscando'><td colspan='5'><strong style='padding: 5px;display: block;font-weight: bold;'>Buscando...</strong></td><tr>";
        },
        btnCheckAll: function(ev){
            var checked = $(ev.currentTarget).is(":checked");
            $("input[name='check_item']").each(function(){
                $(this).prop('checked',checked);
            });

            this.collection.each(function(model){
                model.set("_checked",checked);
            });
        },

        btnFiltro: function(ev){
            ev.preventDefault();
            this.collection = new App.Collection_ItemFacturasDetal();
            collection_factura_detal = this.collection;
            var fecha_inicio = this.$el.find("input[name='fecha_inicio']").val();
            var fecha_fin = this.$el.find("input[name='fecha_fin']").val();

            moment(fecha_inicio,"DD-MM-YYYY").format("YYYY-MM-DD"),
            moment(fecha_fin,"DD-MM-YYYY").format("YYYY-MM-DD")

            self_view = this;
            $.ajax({
                type: 'POST',
                data: {
                    opt: 'filtro_facturas_detal',
                    fecha_inicio: moment(fecha_inicio,"DD-MM-YYYY").format("YYYY-MM-DD"),
                    fecha_fin: moment(fecha_fin,"DD-MM-YYYY").format("YYYY-MM-DD")
                },
                url:  '../../libs/php/ajax/ajax.php',
                beforeSend: function(){
                    self_view.$el.find("table tbody tr").remove();
                    self_view.$el.find("table tbody").append(self_view.buscando);
                },
                success: function(data){
                    var grupo_items = {};
                    try{
                        grupo_items = JSON.parse(data);
                    }catch(e){
                        grupo_items = [];
                    }
                    
                    if(grupo_items.length>0){
                        _.each(grupo_items, function(item){
                            var model = new App.Model_ItemFacturasDetal()
                            model.set(item);
                            self_view.collection.add(model);
                        });
                        self_view.collection.each(function(model){
                            var tr_view = new App.TrFacturaDetal({model:model, collection: self_view.collection});
                            self_view.$el.find("table tbody").append( tr_view.render().el );
                        });
                    }else{
                        self_view.$el.find("table tbody").append( self_view.valor_defecto );
                    }

                    self_view.$el.find("table tbody tr.buscando").remove();
                }
            });
        },
        render: function() {
            var hoy = moment().format("DD/MM/YYYY");
            this.$el.html(this.template({hoy:hoy}));

            this.$el.find("table tbody").append(this.valor_defecto);
            return this;
        }
    });

    // Ejecución
    $("#facturas_detal").click(function(ev) {
        var view = new App.MainFacturaDetal();

        var html = view.render().el;

        $(html).find("input[name='fecha_inicio']").datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths:true,
                selectOtherMonths: true,
                numberOfMonths: 1,
                dateFormat: "dd/mm/yy"
        });

        $(html).find("input[name='fecha_fin']").datepicker({
                changeMonth: true,
                changeYear: true,
                showOtherMonths:true,
                selectOtherMonths: true,
                numberOfMonths: 1,
                dateFormat: "dd/mm/yy"
        });

        $("#contenedor-factura_detal").html(html);

        App.mostarVentana({
            title:"Factura Detal",
            contentEl: "contenedor-factura_detal",
            width: 900,
            height: 400,
            labelOk: 'Incluir',
            accionOk: function(){
                var productos = collection_factura_detal.where({"_checked":true})
                $("#factura_detal_cargada").val("si");
                _.each(productos, function(producto, index){
                    var ALMACEN_SHOWROOM = 5; //ID: 5 => TABLA: almacen
                    iva = parseFloat(producto.get("_item_piva"));
                    data.productoSeleccionado = producto.attributes;
                    $.addTabla(
                        producto.get("id_item"),
                        producto.get("_item_descripcion"),
                        producto.get("_item_cantidad"),
                        producto.get("_item_preciosiniva"),
                        producto.get("_item_descuento"),
                        producto.get("_item_montodescuento"),
                        producto.get("_item_totalsiniva"),
                        iva,
                        producto.get("_item_preciosiniva")*producto.get("_item_cantidad")*iva/100,
                        parseFloat(producto.get("_item_totalconiva")),
                        ALMACEN_SHOWROOM,
                        producto.get("cod_item")
                    );
                });
            }
        });
    });
    
});