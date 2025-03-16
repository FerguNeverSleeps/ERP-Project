
Ext.define('Personal.view.CuentaForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.container.Container',
    plugins: 'viewport',
    //controller: 'viewport',
    requires: [
        'Personal.view.CuentaFormController',
        'Personal.model.Cuenta',
        'Ext.form.Panel'
    ],    
    controller: 'concueform',
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'concue-form',
    items: [
        {
           xtype: 'form',           
           title: 'Cuenta Contable',
           id:'form-concue',
           frame: true,
           resizable: true,
           layout: 'column',
           defaults: {
                layout: 'form',
                xtype: 'container',
                defaultType: 'textfield'//,
                //style: 'width: 50%'
            },
            reader: {
                type: 'xml',
                model: 'Personal.model.Cuenta',
                record: 'cuenta',
                //rootProperty:'matches',
                successProperty: '@success'
            },
            listeners:{
                afterrender:function(thisComp,opts){
                    if(EDITAR_CONCUE == 1){
                        var form = thisComp.getForm();
                        form.load({
                            url: '../../includes/clases/controladores/ControladorApp.php',
                            waitMsg: 'Cargando...',
                            params:{
                                output:'xml',
                                accion:"Accion/Cwconcue/Get_Cuenta",
                                id:ACTUAL
                            },
                            success: function (form, action) {
                                    //console.log(action.result.data)  
                                    ///Ext.getCmp("cmplogoimg").setSrc(action.result.data.foto);
                                
                            }
                        });
                    }
                }
            },
            items: [
               
            {
                columnWidth:.7,
                items: [
                        { 
                            name:"id",
                            id:"id",
                            hidden:true,
                            value:0
                        },
                        { 
                            fieldLabel: 'Cuenta',
                            name:"Cuenta" ,
                            allowBlank: false
                        },
                        { 
                            fieldLabel: 'Descripcion',
                            name:"Descrip" ,
                            allowBlank: false
                        }
                ]
            }, {
                columnWidth:.3,
                items: []
            }],
    
            buttons: [
                { 
                    text: 'Guardar',
                    handler: function() {
                        //'
                        //var form = ;
                        var form = Ext.getCmp("form-concue").getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: '../../includes/clases/controladores/ControladorApp.php',
                                submitEmptyText: false,
                                waitMsg: 'Guardando Datos...',
                                params:{
                                    accion:"Accion/Cwconcue/Save",
                                    op:OPERACION
                                },
                                reset: true,
                                failure: function(form_instance, action) {
                                    Ext.MessageBox.alert('Alerta', "Hay Errores en el proceso");
                                },
                                success: function(form_instance, action){
                                    document.location.href = "cuenta_contable_form.php"+RELOAD_URL_ADD;
                                }
                            });
                        }
                        else{
                            Ext.MessageBox.alert('Alerta', "Debe llenar los campos obligatorios");
                        }
                    } 
                },
                { 
                    text: 'Cancelar',
                    handler: function() {
                        document.location.href = "cuenta_contable.php";
                    } 
                }
            ]
       }
    ],
    
    initComponent: function() {
        //this.width = 590;
        this.callParent();
    }
});