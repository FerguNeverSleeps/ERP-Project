
Ext.define('Personal.view.PreCuentaForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.container.Container',
    plugins: 'viewport',
    //controller: 'viewport',
    requires: [
        'Personal.view.PreCuentaFormController',
        'Personal.model.Cuenta',
        'Ext.form.Panel'
    ],    
    controller: 'precueform',
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'precue-form',
    items: [
        {
           xtype: 'form',           
           title: 'Cuenta Presupuestaria',
           id:'form-precue',
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
                                accion:"Accion/Cwprecue/Get_Cuenta",
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
                        var form = Ext.getCmp("form-precue").getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: '../../includes/clases/controladores/ControladorApp.php',
                                submitEmptyText: false,
                                waitMsg: 'Guardando Datos...',
                                params:{
                                    accion:"Accion/Cwprecue/Save",
                                    op:OPERACION
                                },
                                reset: true,
                                failure: function(form_instance, action) {
                                    Ext.MessageBox.alert('Alerta', "Hay Errores en el proceso");
                                },
                                success: function(form_instance, action){
                                    document.location.href = "cuenta_presupuesto_form.php"+RELOAD_URL_ADD;
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
                        document.location.href = "cuenta_presupuesto.php";
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