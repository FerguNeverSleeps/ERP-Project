
Ext.define('Personal.view.Log_TransaccionesForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.container.Container',
    plugins: 'viewport',
    //controller: 'viewport',
    requires: [
        'Personal.view.Log_TransaccionesFormController',
        'Personal.model.Log_Transacciones',
        'Ext.form.Panel'
    ],    
    controller: 'log_transaccionesform',
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'log_transacciones-form',
    items: [
        {
           xtype: 'form',           
           title: 'Log_Transacciones',
           id:'form-log_transacciones',
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
                model: 'Personal.model.Log_Transacciones',
                record: 'log_transacciones',
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
                                accion:"Accion/Log_transacciones/Get_Log_transacciones",
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
                            name:"cod_log",
                            id:"cod_log",
                            hidden:true,
                            value:0
                        },
                        { 
                            fieldLabel: 'Descripcion',
                            name:"descripcion" ,
                            allowBlank: false
                        },
                        { 
                            
                            fieldLabel: 'fecha_hora',
                            name:"fecha_hora" ,
                            allowBlank: false 
                            
                        },
                        { 
                            
                            fieldLabel: 'modulo',
                            name:"modulo" ,
                            allowBlank: false 
                            
                        },
                        { 
                            
                            fieldLabel: 'accion',
                            name:"accion" ,
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
                        var form = Ext.getCmp("form-log_transacciones").getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: '../../includes/clases/controladores/ControladorApp.php',
                                submitEmptyText: false,
                                waitMsg: 'Guardando Datos...',
                                params:{
                                    accion:"Accion/Maestro/Save_Log_transacciones",
                                    op:OPERACION
                                },
                                reset: true,
                                failure: function(form_instance, action) {
                                    Ext.MessageBox.alert('Alerta', "Hay Errores en el proceso");
                                },
                                success: function(form_instance, action){
                                    document.location.href = "maestro_log_form.php"+RELOAD_URL_ADD;
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
                        document.location.href = "maestro_log.php";
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