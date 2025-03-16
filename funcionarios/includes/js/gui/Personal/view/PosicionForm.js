
Ext.define('Personal.view.PosicionForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.container.Container',
    plugins: 'viewport',
    viewModel: true,
    //controller: 'viewport',
    requires: [
        'Personal.view.PosicionFormController',
        'Personal.model.Posicion',
        'Ext.form.Panel'
    ],    
    controller: 'posicionform',
    style: 'padding:25px',
    layout: 'fit',
    xtype: 'posicion-form',
    items: [
        {
           xtype: 'form',           
           title: 'Posicion',
           id:'form-posicion',
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
                model: 'Personal.model.Posicion',
                record: 'posicion',
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
                        },
                        {
                                    xtype: 'combobox',
                                    fieldLabel:'Partida',
                                    name: 'partida',
                                    reference: 'partida',
                                    publishes: 'value',
                                    displayField: 'CuentaDescrip',
                                    valueField: 'Cuenta',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Partida',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Cuenta',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:1,
                                                accion:'Accion/Cwprecue/Get_list_Combo'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                        },
                        { 
                            xtype:'numberfield',
                            fieldLabel: 'Gastos de representacion',
                            name:"gastos_representacion" ,
                            allowBlank: false,
                            decimalPresicion:2,
                            decimalSeparator:'.'
                        },
                        {
                            xtype: 'combobox',
                            //anchor:'50%',
                            fieldLabel:'Paga Gastos Representacion',
                            //name: 'cboEstadocivil',
                            name: 'paga_gr',
                            displayField: 'descripcion',
                            valueField: 'paga_gr',
                            queryMode: 'local',
                            emptyText: 'Seleccion paga GR',
                            margin: '0 6 0 0',
                            store: new Ext.data.Store({
                                fields: ['paga_gr', 'descripcion'],
                                data: (function() {
                                    var data = [];
                                    data[0] = {paga_gr: "0", descripcion: "No"};
                                    data[1] = {paga_gr: "1", descripcion: "Si"};
                                    
                                    return data;
                                })()
                            }),
                            //width: 100,
                            allowBlank: false,
                            forceSelection: true
                        },
                        {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Categoria',
                                    ///name: 'cboCategorias',
                                    name: 'categoria_id',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Categoria',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Categoria',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Categoria'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Cargo',
                                    //name: 'cboCargos',
                                    name: 'cargo_id',
                                    displayField: 'des_car',
                                    valueField: 'cod_car',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Cargo',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Cargo',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Cargo'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true
                                }/*,
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel1,
                                    hidden:nivel1,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel1',
                                    reference: 'cod_nivel1',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel1,
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:1,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel2,
                                    hidden:nivel2,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel2',
                                    reference: 'cod_nivel2',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel2,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel1.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:2,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel3,
                                    hidden:nivel3,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel3',
                                    reference: 'cod_nivel3',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel3,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel2.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:3,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel4,
                                    hidden:nivel4,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel4',
                                    reference: 'cod_nivel4',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel4,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel3.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:4,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel5,
                                    hidden:nivel5,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel5',
                                    reference: 'cod_nivel5',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel5,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel4.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:5,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel6,
                                    hidden:nivel6,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel6',
                                    reference: 'cod_nivel6',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel6,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel5.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:6,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel7,
                                    hidden:nivel7,
                                    //name: 'cboNivel1',
                                    name: 'cod_nivel7',
                                    reference: 'cod_nivel7',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel7,
                                    margin: '0 6 0 0',
                                    bind: {
                                        //visible: '{country.value}',
                                        filters: {
                                            property: 'gerencia',
                                            value: '{cod_nivel6.value}'
                                        }
                                    },
                                    store: {
                                        model: 'Personal.model.Departamento',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                nivel:7,
                                                accion:'Accion/Maestro/Get_list_Nivel'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true
                                }*/
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