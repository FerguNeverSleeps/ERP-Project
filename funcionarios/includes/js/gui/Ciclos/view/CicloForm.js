
Ext.define('Ciclo.view.CicloForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.container.Container',
    plugins: 'viewport',
    //controller: 'viewport',
    requires: [
        'Ciclo.view.CicloFormController',
        'Ciclo.model.CicloForm',
        'Ext.form.Panel'
    ],    
    controller: 'Cicloform',
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'planilla-form',
    items: [
        {
           xtype: 'form',           
           title: 'Tipo Planilla',
           id:'form-Ciclo',
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
                model: 'Ciclo.model.Ciclo',
                record: 'Ciclo',
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
                                accion:"Accion/Maestro/Get_Posicion",
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
                            name:"nomposicion_id",
                            id:"nomposicion_id",
                            hidden:true,
                            value:0
                        },
                        { 
                            fieldLabel: 'Descripcion',
                            name:"descripcion_posicion" ,
                            allowBlank: false
                        },
                        { 
                            xtype:'numberfield',
                            fieldLabel: 'Sueldo Propuesto',
                            name:"sueldo_propuesto" ,
                            allowBlank: false,
                            decimalPresicion:2,
                            decimalSeparator:'.'
                        },
                        { 
                            xtype:'numberfield',
                            fieldLabel: 'Sueldo Anual',
                            name:"sueldo_anual" ,
                            allowBlank: false,
                            decimalPresicion:2,
                            decimalSeparator:'.'
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
                        var form = Ext.getCmp("form-posicion").getForm();
                        if (form.isValid()) {
                            form.submit({
                                url: '../../includes/clases/controladores/ControladorApp.php',
                                submitEmptyText: false,
                                waitMsg: 'Guardando Datos...',
                                params:{
                                    accion:"Accion/Maestro/Save_Posicion",
                                    op:OPERACION
                                },
                                reset: true,
                                failure: function(form_instance, action) {
                                    Ext.MessageBox.alert('Alerta', "Hay Errores en el proceso");
                                },
                                success: function(form_instance, action){
                                    document.location.href = "maestro_posicion_form.php"+RELOAD_URL_ADD;
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
                        document.location.href = "maestro_posicion.php";
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