Ext.define('Personal.view.IntegranteForm', {
    //extend: 'Ext.container.Viewport',
    extend: 'Ext.tab.Panel',
    plugins: 'viewport',
    viewModel: true,
    //controller: 'viewport',
    requires: [
        'Personal.view.IntegranteFormController',
        'Personal.model.Planilla',
        'Personal.model.Profesion',
        'Personal.model.Situacion',
        'Personal.model.Banco',
        'Personal.model.Turno',
        'Personal.model.Categoria',
        'Personal.model.Cargo',
        'Personal.model.Departamento',
        'Personal.model.Posicion',
        'Personal.model.Puestos',
        'Ext.form.Panel',
        'Ext.tab.Panel'
    ],    
    controller: 'form',
    style: 'padding:25px',
    //layout: 'fit',
    xtype: 'personal-form',
    items: [
        {
            title: 'Personal',
            items:[
                //INICIO FORM
                {
                   xtype: 'form',           
                   //title: 'Personal',
                   id:'form-integrantes',
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
                        model: 'Personal.model.Integrante',
                        record: 'integrante',
                        //rootProperty:'matches',
                        successProperty: '@success'
                    },
                    listeners:{
                        afterrender:function(thisComp,opts){
                            if(TIPO_EMPRESA == 2)
                            {
                                Ext.getCmp('horario_puesto').setValue(''); 
                                Ext.getCmp('horario_puesto').hide();                                 
                            }
                            if(EDITAR_PERSONAL == 1){
                                var form = thisComp.getForm();
                                form.load({
                                    url: '../../includes/clases/controladores/ControladorApp.php',
                                    waitMsg: 'Cargando...',
                                    params:{
                                        output:'xml',
                                        accion:"Accion/Integrante/Get_Integrante",
                                        ficha:ACTUAL
                                    },
                                    success: function (form, action) {
                                            // console.log(action.result.data)  
                                            if(TIPO_EMPRESA == 2)
                                            {
                                                var recordPuesto = Ext.getCmp("puesto_id").getStore().getAt(action.result.data.puesto_id-1); 
                                                var combo = Ext.getCmp('puesto_id');
                                                //combo.select(90);
                                                combo.fireEvent('select', combo, recordPuesto);
                                                //console.log(recordPuesto);
                                            }

                                            Ext.getCmp("cmpcedulaimg").setSrc(action.result.data.imagen_cedula);
                                            Ext.getCmp("cmplogoimg").setSrc(action.result.data.foto);
                                            Ext.getCmp("suesal").setSueldoOriginal(action.result.data.suesal);
                                            Ext.getCmp("codcargo").setCargoOriginal(action.result.data.codcargo);

                                             if(TIPO_EMPRESA == 1)
                                                Ext.getCmp("nomposicion_id").setPosicionOriginal(action.result.data.nomposicion_id); 
                                    }
                                });
                            }
                            //var recordSelected = Ext.getCmp("tipnom").getStore().getAt(0); 
                            //console.log("INIICAN",recordSelected)                    
                            //Ext.getCmp("tipnom").setValue(recordSelected.get('codtip'));
                        }
                    },
                    items: [
                                   
                    {
                        columnWidth:.7,
                        items: [
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Planilla',
                                    //name: 'cboTipoNomina',
                                    name:'tipnom',
                                    id:'tipnom',
                                    displayField: 'descrip',
                                    valueField: 'codtip',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Planilla',
                                    margin: '0 6 0 0',
                                    store: {
                                        extend: 'Ext.data.Store',
                                        model: 'Personal.model.Planilla',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Planilla',
                                                cod_session:1,
                                                operacion:OPERACION
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true,
                                        listeners:{
                                            load: function(sender, node, records) {
                                                Ext.getCmp("tipnom").setValue(sender.data.items[0].data.codtip);
                                            }
                                        }
                                    },
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true
                                },
                                { 
                                    name:"personal_id",
                                    id:"personal_id",
                                    hidden:true,
                                    value:0
                                },
                                { 
                                    fieldLabel: 'N° Ficha',
                                    //name:"txtficha" ,
                                    id:"ficha",
                                    name:"ficha" ,
                                    allowBlank: false,
                                    readOnly:true,
                                    value:ACTUAL
                                },
                                { 
                                    xtype: 'filefield',
                                    //name:"mifichero",
                                    name:"foto",
                                    fieldLabel: 'Foto',
                                    reference: 'basicFile' ,
                                    listeners:{
                                        change:function(t,n,o){
                                            //console.log(t.value)
                                            ////actualizar(t);
                                            Ext.getCmp("cmplogoimg").setSrc(t.value);
                                        }
                                    }
                                },
                                { 
                                    fieldLabel: 'Apellidos * ' ,
                                    //name:"txtapellidos",
                                    name:"apellidos",
                                    allowBlank: false
                                },
                                { 
                                    fieldLabel: 'Nombres * ' ,
                                    //name:"txtnombres",
                                    name:'nombres',
                                    allowBlank: false
                                },
                                { 
                                    fieldLabel: 'Cedula * ' ,
                                    //name:"txtcedula",
                                    name:"cedula",
                                    id:"cedula",
                                    allowBlank: false,
                                    value:CEDULA_ACTUAL
                                },
                                { 
                                    xtype: 'filefield',
                                    name:"imagen_cedula",
                                    fieldLabel: 'Foto Cédula',
                                    //reference: 'basicFile' ,
                                    listeners:{
                                        change:function(t,n,o){
                                            Ext.getCmp("cmpcedulaimg").setSrc(t.value);
                                        }
                                    }
                                },
                                {
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Nacionalidad',
                                    defaults: {
                                        //name: 'optNacionalidad',
                                        name:'nacionalidad',
                                        margin: '0 15 0 0'
                                    },
                                    items: [
                                        {boxLabel: 'Panameño',  inputValue: 1,checked: true},
                                        {boxLabel: 'Extranjero',  inputValue: 2},
                                        {boxLabel: 'Nacionalizado',  inputValue: 3}
                                    ]
                                },
                                {
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Sexo',
                                    defaults: {
                                        //name: 'optSexo',
                                        name: 'sexo',
                                        margin: '0 15 0 0'
                                    },
                                    items: [
                                        {boxLabel: 'Masculino',  inputValue: "Masculino",checked: true},
                                        {boxLabel: 'Femenino',  inputValue: "Femenino"}/*,
                                        {boxLabel: 'Otro',  inputValue: "Otro"}*/
                                    ]
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Estado Civil * ',
                                    //name: 'cboEstadocivil',
                                    name: 'estado_civil',
                                    displayField: 'descripcion',
                                    valueField: 'edo_civil',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Estado Civil',
                                    margin: '0 6 0 0',
                                    store: new Ext.data.Store({
                                        fields: ['edo_civil', 'descripcion'],
                                        data: (function() {
                                            var data = [];
                                            data[0] = {edo_civil: "Soltero/a", descripcion: "Soltero/a"};
                                            data[1] = {edo_civil: "Casado/a", descripcion: "Casado/a"};
                                            data[2] = {edo_civil: "Viudo/a", descripcion: "Viudo/a"};
                                            data[3] = {edo_civil: "Divorciado/a", descripcion: "Divorciado/a"};
                                            //data[4] = {edo_civil: "Otro", descripcion: "Otro"};
                                            data[4] = {edo_civil: "Unido", descripcion: "Unido"};
                                            return data;
                                        })()
                                    }),
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha de Nacimiento * ',
                                    //name: 'txtFechaNac',
                                    name: 'fecnac',
                                    allowBlank: false,
                                    maxValue: new Date(),
                                    format:"d/m/Y",
                                    listeners:{
                                        change:function(t,n,o){
                                            //console.log("feh: ",t)
                                            actualizar(t.rawValue);
                                        }
                                    }
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Edad',
                                    value: 'Años de Edad',
                                    name: 'edad',
                                    id: 'edad',
                                    style:{
                                        fontWeight:'bolder'
                                    }
                                },
                                { 
                                    fieldLabel: 'Lugar de Nacimiento * ' ,
                                    //name:"txtlugarNac",
                                    name:"lugarnac",
                                    allowBlank: false},
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Profesion * ',
                                    //name: 'cboProfesion',
                                    name: 'codpro',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Profesion',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Planilla',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Profesion'
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
                                { xtype:'textarea',fieldLabel: 'Direccion * ' ,name:"direccion",allowBlank: false},
                                { 
                                    fieldLabel: 'Telefonos * ' ,
                                    //name:"txttelefonos",
                                    name:"telefonos",
                                    emptyText: 'xxxx-xxxx',
                                    maskRe: /[\d\-]/,
                                    regex: /^\d{3,4}-\d{4}$/,
                                    regexText: 'Debe tener el formato formato xxx-xxxx',
                                    allowBlank: false
                                },
                                { 
                                    fieldLabel: 'e-mail Sugerido' ,
                                    //name:"txtemail"
                                    name:"email"
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Situacion * ',
                                    //name: 'cbosituacion',
                                    name: 'estado',
                                    id: 'estado',
                                    displayField: 'situacion',
                                    valueField: 'situacion',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Situacion',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Situacion',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Situacion'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true,
                                        listeners:{
                                            load: function(sender, node, records) {
                                                Ext.getCmp("estado").setValue(sender.data.items[0].data.codigo);
                                            }
                                        }
                                    },
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true
                                },
                                { 
                                    fieldLabel: 'Digito Verificador' ,
                                    //name:"txtdv"
                                    name:"dv"
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha de Ingreso * ',
                                    //name: 'txtFechaIngreso',
                                    name: 'fecing',
                                    allowBlank: false,
                                    maxValue: new Date(),
                                    format:"d/m/Y",
                                    listeners:{
                                        change:function(t,n,o){
                                            //console.log("feh: ",t)
                                            calcular_antiguedad(t.rawValue);
                                        }
                                    }
                                },
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Antiguedad',
                                    value: 'Antiguedad',
                                    name: 'antiguedad',
                                    id: 'antiguedad',
                                    style:{
                                        fontWeight:'bolder'
                                    }
                                },
                                { 
                                    fieldLabel: 'Numero Decreto' ,
                                    //name:"txtcuenta",
                                    name:"num_decreto",
                                    id:"num_decreto",
                                    allowBlank: true
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Decreto',
                                    //name: 'txtFechaIngreso',
                                    name: 'fecha_decreto',
                                    allowBlank: true,
                                    maxValue: new Date(),
                                    format:"d/m/Y"
                                },
                                { 
                                    fieldLabel: 'Decreto Baja' ,
                                    //name:"txtcuenta",
                                    name:"num_decreto_baja",
                                    id:"num_decreto_baja",
                                    allowBlank: true
                                },
                                {
                                    xtype: 'datefield',
                                    fieldLabel: 'Fecha Decreto Baja',
                                    //name: 'txtFechaIngreso',
                                    name: 'fecha_decreto_baja',
                                    allowBlank: true,
                                    maxValue: new Date(),
                                    format:"d/m/Y"
                                },
                                { 
                                    fieldLabel: 'Siacap' ,
                                    //name:"txtcuenta",
                                    name:"siacap",
                                    id:"siacap",
                                    allowBlank: true
                                },
                                /*(TIPO_EMPRESA == 1)?{
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Prestaciones',
                                    defaults: {
                                        name: 'tipopres',
                                        margin: '0 15 0 0'
                                    },
                                    items: [
                                        {boxLabel: 'Fideicomiso',  inputValue: 1,checked: true},
                                        {boxLabel: 'Fondo',  inputValue: 2},
                                        {boxLabel: 'Contabilidad',  inputValue: 3}
                                    ]
                                }:null
                                ,*/
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Forma de Pago * ',
                                    //name: 'cboTipocobro',
                                    name: 'forcob',
                                    displayField: 'descripcion',
                                    valueField: 'cod',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Forma de Pago',
                                    margin: '0 6 0 0',
                                    store: new Ext.data.Store({
                                        fields: ['cod', 'descripcion'],
                                        data: (function() {
                                            var data = [];
                                            data[0] = {cod: "Efectivo", descripcion: "Efectivo"};
                                            data[1] = {cod: "Cheque", descripcion: "Cheque"};
                                            data[2] = {cod: "Cuenta Ahorro", descripcion: "Cuenta Ahorro"};
                                            data[3] = {cod: "Cuenta Corriente", descripcion: "Cuenta Corriente"}/*;
                                            data[4] = {cod: "Deposito F.A.L", descripcion: "Deposito F.A.L"};*/
                                            return data;
                                        })()
                                    }),
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true,
                                        listeners:{
                                            change:function( thisc, newValue, oldValue, eOpts ) {
                                                if(newValue == "Efectivo"){
                                                    Ext.getCmp("cuentacob").setValue("");
                                                    Ext.getCmp("codbancob").setValue("");
                                                    Ext.getCmp("cuentacob").disable();
                                                    Ext.getCmp("codbancob").disable();                                            
                                                }
                                                else{
                                                    Ext.getCmp("cuentacob").enable();
                                                    Ext.getCmp("codbancob").enable();    
                                                }
                                            }
                                        }
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Bancos',
                                    //name: 'cboBancos',
                                    name: 'codbancob',
                                    id: 'codbancob',
                                    displayField: 'des_ban',
                                    valueField: 'cod_ban',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Bancos',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Banco',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Banco'
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
                                    fieldLabel: 'Cuenta * ' ,
                                    //name:"txtcuenta",
                                    name:"cuentacob",
                                    id:"cuentacob",
                                    allowBlank: false
                                },
                                { 
                                    fieldLabel: 'Seguro Social' ,
                                    //name:"txtsegurosocial"
                                    name:"seguro_social"
                                },
                                { 
                                    fieldLabel: 'Codigo Seguro Social Sipe' ,
                                    //name:"txtsegurosocialsipe"
                                    name:"segurosocial_sipe"
                                },
                                {
                                    xtype: 'radiogroup',
                                    fieldLabel: 'Tipo de Contrato',
                                    defaults: {
                                        //name: 'optContrato',
                                        name:'tipemp',
                                        margin: '0 15 0 0'
                                    },
                                    items: [
                                        {boxLabel: 'Permanente',  inputValue: "Fijo",checked: true,
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').disable ();  
                                                      Ext.getCmp('fecha_fin_contrato').disable ();  
                                                      Ext.getCmp('fecha_inicio_contrato').setValue ("");  
                                                      Ext.getCmp('fecha_fin_contrato').setValue ("");
                                                    } 
                                                }
                                            }
                                        },
                                        /*{boxLabel: 'Temporal',  inputValue: 'Temporal',
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').enable ();  
                                                      Ext.getCmp('fecha_fin_contrato').enable ();  
                                                    } 
                                                }
                                            }
                                        },*/
                                        {boxLabel: 'Contrato Transitorio',  inputValue: 'Contratado Transitorio',
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').enable ();  
                                                      Ext.getCmp('fecha_fin_contrato').enable ();  
                                                    } 
                                                }
                                            }
                                        },
                                        {boxLabel: 'Contrato Contingente',  inputValue: 'Contratado Contingente',
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').enable ();  
                                                      Ext.getCmp('fecha_fin_contrato').enable ();  
                                                    } 
                                                }
                                            }
                                        },
                                        {boxLabel: 'Contrato Servicios Profesionales',  inputValue: 'Contratado Servicios',
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').enable ();  
                                                      Ext.getCmp('fecha_fin_contrato').enable ();  
                                                    } 
                                                }
                                            }
                                        }
                                        /*{boxLabel: 'Pasante',  inputValue: "Pasante",
                                            listeners:{
                                                change:function(r,n,o){
                                                    if (n){
                                                      Ext.getCmp('fecha_inicio_contrato').enable ();  
                                                      Ext.getCmp('fecha_fin_contrato').enable ();  
                                                    } 
                                                }
                                            }
                                        }*/
                                    ]
                                },
                                {
                                    xtype: 'container',
                                    layout: 'form',
                                    margin: '0 0 5 0',
                                    defaults: {
                                        //layout: 'form',
                                        //xtype: 'container',
                                        //defaultType: 'textfield',
                                        //style: 'width: 50%'
                                    },
                                    items:[ 
                                        {
                                                    xtype: 'datefield',
                                                    fieldLabel: 'Fecha Inicio',
                                                    //name: 'fecha_inicio_contrato',
                                                    name:'inicio_periodo',
                                                    id: 'fecha_inicio_contrato',
                                                    allowBlank: true,
                                                    disabled:true,
                                                    maxValue: new Date()
                                        },
                                        {
                                                    xtype: 'datefield',
                                                    fieldLabel: 'Fecha Fin',
                                                    //name: 'fecha_fin_contrato',
                                                    name:'fin_periodo',
                                                    id: 'fecha_fin_contrato',
                                                    allowBlank: true,
                                                    disabled:true
                                        }
                                        
                                    ]
                                },
                                (TIPO_EMPRESA == 2)?
                                {
                                    xtype: 'combobox',
                                    fieldLabel: 'Puesto de Trabajo',
                                    id: 'puesto_id',
                                    name: 'puesto_id',
                                    displayField: 'desc_puesto',
                                    valueField: 'id_puesto',
                                    queryMode: 'local',
                                    emptyText: 'Seleccione un puesto',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Puestos',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Puesto'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true,
                                    },
                                    allowBlank: false,
                                    forceSelection: true,
                                    listeners:{
                                        select: function(element, record, eOpts) {

                                            registro = (undefined != record[0]) ? record[0].data : record.data;

                                            if(element.getValue() != '')
                                            {
                                                //console.log(registro);
                                                var tabla = obtener_tabla_horario_puesto(registro);
                                                Ext.getCmp("horario_puesto").setValue(tabla);
                                                Ext.getCmp('horario_puesto').show(); 
                                            }
                                        }
                                    }
                                }:null,
                                (TIPO_EMPRESA == 2)?
                                {
                                    xtype: 'displayfield',
                                    fieldLabel: 'Horario del Puesto',
                                    value: '',
                                    name:  'horario_puesto',
                                    id:    'horario_puesto',
                                    hidden:(EDITAR_PERSONAL != 1)
                                }:null,
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Turnos * ',
                                    //name: 'cboTurno',
                                    name:'turno_id',
                                    displayField: 'descripcion',
                                    valueField: 'turno_id',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Turno',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Turno',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Turno'
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
                                (TIPO_EMPRESA == 1)?
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Posicion',
                                    //name: 'cboTurno',
                                    name:'nomposicion_id',
                                    id:'nomposicion_id',
                                    posicion_original:-1,
                                    setPosicionOriginal:function(posicionOriginal){
                                        this.posicion_original = posicionOriginal;
                                    },
                                    getPosicionOriginal:function(){
                                        return this.posicion_original;
                                    },
                                    displayField: 'nomposicion_id', //'descripcion_posicion',
                                    valueField: 'nomposicion_id',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion Posicion',
                                    margin: '0 6 0 0',
                                    store: {
                                        model: 'Personal.model.Posicion',
                                        proxy: {
                                            type: 'ajax',
                                            url: '../../includes/clases/controladores/ControladorApp.php',
                                            reader: {
                                                type: 'json',
                                                rootProperty: 'matches'
                                            },
                                            extraParams:{
                                                accion:'Accion/Maestro/Get_list_Posicion'
                                            }
                                        },
                                        isolated: false,
                                        autoDestroy: true,
                                        autoLoad: true
                                    },
                                    //width: 100,
                                    allowBlank: false,
                                    forceSelection: true,
                                    //enableKeyEvents: true,
                                    listeners:{
                                        //select:function(thisComp, records, eOpts){//function(thisComp,newValue,oldValue){
                                        beforeselect:function( thisComp, record, index, eOpts ){
                                            //console.log(thisComp.valueModels[0].data)
                                            //console.log(thisComp.getValue(),record.data.nomposicion_id)
                                            if(thisComp.getValue() != record.data.nomposicion_id){ 
                                                var recordSelected = record.data;//thisComp.valueModels[0].data;  
                                                // console.log(recordSelected)
                                                Ext.getCmp('suesal').setMaxValue(recordSelected.sueldo_propuesto);    
                                                Ext.getCmp("suesal").setValue(recordSelected.sueldo_propuesto);  
                                                Ext.getCmp("codcat").setValue(recordSelected.categoria_id);  
                                                Ext.getCmp("codcargo").setValue(recordSelected.cargo_id);
                                                //codnivel1
                                                var partida = recordSelected.partida;
                                                var partidaSplit = partida.split("-");
                                                var ini = partidaSplit[0]+"-"+partidaSplit[1]+"-";
                                                //console.log(Ext.getCmp('store-nivel1'))
                                                if(!nivel1){// && typeof Ext.getCmp('store-nivel1') != "undefined"){
                                                    //Ext.getCmp("codnivel1").setValue(recordSelected.cargo_id);
                                                    //Ext.getCmp('store-nivel1').
                                                    var n1 = Ext.getCmp("codnivel1");
                                                    var val1 = "";
                                                    /*
                                                    n1.getStore().load({
                                                        params:{
                                                            cascada_partida:ini+partidaSplit[2]+"-00-00-000"
                                                        },
                                                        callback: function(records, operation, success) {
                                                            // the operation object
                                                            // contains all of the details of the load operation
                                                            //console.log("DATA=",records);
                                                            val1 = records[0].data.codorg;
                                                            n1.getStore().load();
                                                            n1.setValue(val1);
                                                        }
                                                    });
*/
                                                    //val1 = n1.getStore().data.items[0].data.codorg;
                                                    //console.log(n1.getStore().data.items[0].data.codorg)
                                                    //var n1S = n1.valueModels[0].data;
                                                    //console.log(n1.getStore())
                                                    //console.log(n1.getStore().data.items[0])
                                                    
                                                }
                                                if(!nivel2){// && typeof Ext.getCmp('store-nivel2') != "undefined"){
                                                    //Ext.getCmp("codnivel2").setValue(recordSelected.cargo_id);
                                                    //Ext.getCmp('store-nivel2')
                                                    var n2 = Ext.getCmp("codnivel2");
                                                    var val2 = ""
                                                    /*
                                                    n2.getStore().load({
                                                        params:{
                                                            cascada_partida:ini+partidaSplit[2]+"-"+partidaSplit[3]+"-00-000"
                                                        },
                                                        callback: function(records, operation, success) {
                                                            // the operation object
                                                            // contains all of the details of the load operation
                                                            //console.log("DATA=",records);
                                                            val2 = records[0].data.codorg;
                                                            n2.getStore().load();
                                                            n2.setValue(val2);
                                                        }
                                                    });
*/
                                                    //val2 = n2.getStore().data.items[0].data.codorg;
                                                    
                                                }
                                                if(!nivel3){// && typeof Ext.getCmp('store-nivel3') != "undefined"){
                                                    //Ext.getCmp("codnivel3").setValue(recordSelected.cargo_id);
                                                    //Ext.getCmp('store-nivel3').
                                                    var n3 = Ext.getCmp("codnivel3");
                                                    var val3 = "";
                                                    n3.getStore().load({
                                                        params:{
                                                            cascada_partida:ini+partidaSplit[2]+"-"+partidaSplit[3]+"-"+partidaSplit[4]+"-000"
                                                        },
                                                        callback: function(records, operation, success) {
                                                            // the operation object
                                                            // contains all of the details of the load operation
                                                            //console.log("DATA=",records);
                                                            val3 = records[0].data.codorg;
                                                            n3.getStore().load();
                                                            n3.setValue(val3);
                                                        }
                                                    });
                                                    //val3 = n3.getStore().data.items[0].data.codorg;
                                                    
                                                }
                                                if(!nivel4){// && typeof Ext.getCmp('store-nivel4') != "undefined"){
                                                    //Ext.getCmp("codnivel4").setValue(recordSelected.cargo_id);
                                                    //Ext.getCmp('store-nivel4').
                                                    var n4 = Ext.getCmp("codnivel4");
                                                    var val4 = "";
                                                    n4.getStore().load({
                                                        params:{
                                                            cascada_partida:ini+partidaSplit[2]+"-"+partidaSplit[3]+"-"+partidaSplit[4]+"-"+partidaSplit[5]
                                                        },
                                                        callback: function(records, operation, success) {
                                                            // the operation object
                                                            // contains all of the details of the load operation
                                                            //console.log("DATA=",records);
                                                            val4 = records[0].data.codorg;
                                                            n4.getStore().load();
                                                            n4.setValue(val4);
                                                        }
                                                    });
                                                    //val4 = n4.getStore().data.items[0].data.codorg;
                                                    
                                                }
                                                /*if(!nivel5){
                                                    //Ext.getCmp("codnivel5").setValue(recordSelected.cargo_id);
                                                }
                                                if(!nivel6){
                                                    //Ext.getCmp("codnivel6").setValue(recordSelected.cargo_id);
                                                }
                                                if(!nivel7){
                                                    //Ext.getCmp("codnivel7").setValue(recordSelected.cargo_id);
                                                }*/
                                            }
                                        }
                                    },
                                }:null,
                                {
                                    xtype:'numberfield', 
                                    fieldLabel: 'Salario * ' ,
                                    //name:"txtmonto",
                                    name:"suesal",
                                    id:"suesal",
                                    decimalPrecision :2,
                                    decimalSeparator :'.',
                                    allowBlank: ((USUARIO_ACCESO_SUELDO==1)?false:true), // ((TIPO_EMPRESA==1) ? false : true),
                                    sueldo_original:-1,
                                    //maxValue: 8000,
                                    hidden:((USUARIO_ACCESO_SUELDO==1)?false:true),
                                    setSueldoOriginal:function(sueldoOriginal){
                                        this.sueldo_original = sueldoOriginal;
                                    },
                                    getSueldoOriginal:function(){
                                        return this.sueldo_original;
                                    }
                                },
                                { 
                                    fieldLabel: 'Hora Base Trabajada' ,
                                    //name:"txthorabase"
                                    name:"hora_base"
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:'Categoria * ',
                                    ///name: 'cboCategorias',
                                    name: 'codcat',
                                    id: 'codcat',
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
                                    fieldLabel:'Cargo * ',
                                    //name: 'cboCargos',
                                    name: 'codcargo',
                                    id: 'codcargo',
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
                                    forceSelection: true,
                                    cargo_original:-1,
                                    setCargoOriginal:function(cargoOriginal){
                                        this.cargo_original = cargoOriginal;
                                    },
                                    getCargoOriginal:function(){
                                        return this.cargo_original;
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel1,
                                    hidden:nivel1,
                                    //name: 'cboNivel1',
                                    name: 'codnivel1',
                                    id: 'codnivel1',
                                    reference: 'cod_nivel1',
                                    publishes: 'value',
                                    displayField: 'descrip',
                                    valueField: 'codorg',
                                    queryMode: 'local',
                                    emptyText: 'Seleccion '+nomNivel1,
                                    margin: '0 6 0 0',
                                    store: {
                                        storeId:'store-nivel1',
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
                                        autoLoad: true,
                                    },
                                    //width: 100,
                                    allowBlank: true,
                                    forceSelection: true,
                                    listeners:{
                                        select:function(){
                                            if(!nivel2)
                                            {
                                                //console.log("Limpiar");
                                                combo2 = Ext.getCmp("codnivel2");
                                                combo2.clearValue();
                                                combo2.enable();
                                            }
                                        }
                                    }
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel2,
                                    hidden:nivel2,
                                    //name: 'cboNivel1',
                                    disabled:(EDITAR_PERSONAL != 1), 
                                    name: 'codnivel2',
                                    id: 'codnivel2',
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
                                        storeId:'store-nivel2',
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
                                    forceSelection: true,
                                },
                                {
                                    xtype: 'combobox',
                                    //anchor:'50%',
                                    fieldLabel:nomNivel3,
                                    hidden:nivel3,
                                    //name: 'cboNivel1',
                                    name: 'codnivel3',
                                    id: 'codnivel3',
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
                                        storeId:'store-nivel3',
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
                                    name: 'codnivel4',
                                    id: 'codnivel4',
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
                                        storeId:'store-nivel4',
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
                                    name: 'codnivel5',
                                    id: 'codnivel5',
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
                                        storeId:'store-nivel5',
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
                                    name: 'codnivel6',
                                    id: 'codnivel6',
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
                                        storeId:'store-nivel6',
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
                                    name: 'codnivel7',
                                    id: 'codnivel7',
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
                                        storeId:'store-nivel7',
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
                                }
                        ]
                    }, {
                        columnWidth:.2,
                        items: [
                            {
                                xtype: 'image',
                                style:{
                                    border:'1px'
                                },
                                anchor:'100%',
                                height: 158,
                                itemId: 'cmplogoimg',
                                id: 'cmplogoimg',
                                margin: 10,
                                //width: 287,
                                width:177,height:179,
                                src:'fotos/silueta.gif'
                            },
                            { 
                                xtype:'button',
                                disabled:(EDITAR_PERSONAL != 1),
                                icon:'../../includes/imagenes/icons/printer.png',
                                text: 'Imprimir',
                                margin: 10,
                                handler: function() {
                                    document.location.href = "../fpdf/datos_personal.php?cedula="+CEDULA_ACTUAL+"&ficha="+ACTUAL;
                                    //document.location.href = "maestro_personal.php";
                                } 
                            },
                            
                            {
                                xtype: 'image',
                                anchor:'100%',
                                itemId: 'cmpcedulaimg',
                                id: 'cmpcedulaimg',
                                margin: 10,
                                width:280,
                                height:210,
                                hidden: true
                            },
                            
                            { 
                                xtype:'button',
                                hidden:(EDITAR_PERSONAL != 1),
                                text: 'Foto Cedula',
                                margin: 10,
                                handler: function() {

                                    var myForm = new Ext.form.Panel({
                                        width: 401,
                                        height: 293,
                                        title: 'Foto Cédula',
                                        floating: true,
                                        closable : true,
                                        items: [
                                            {
                                                xtype: 'image',
                                                margin: 10,
                                                width:'95%',
                                                height:235,
                                                src: document.getElementById("cmpcedulaimg").src //'fotos/silueta.gif'
                                            },
                                        ]                                        
                                    });

                                   myForm.show();
                                } 
                            },
                        ]
                    }],
            
                    buttons: [
                        { 
                            text: 'Guardar',
                            handler: function() {

                                if(TIPO_EMPRESA == 1)
                                {
                                    var valorPosicion     = Ext.getCmp('nomposicion_id').getValue();
                                    var recordPosicion    = Ext.getCmp("nomposicion_id").getStore().findRecord('nomposicion_id', valorPosicion);
                                    //console.log(recordPosicion);
                                    if(recordPosicion)
                                    {
                                       // console.log(recordPosicion.data.sueldo_propuesto);                                
                                        Ext.getCmp('suesal').setMaxValue(recordPosicion.data.sueldo_propuesto);                                                    
                                    }
                                }

                                var form = Ext.getCmp("form-integrantes").getForm();

                                if (form.isValid()) {
                                    form.submit({
                                        url: '../../includes/clases/controladores/ControladorApp.php',
                                        submitEmptyText: false,
                                        waitMsg: 'Guardando Datos...',
                                        params:{
                                            accion:"Accion/Integrante/Save",
                                            op:OPERACION,
                                            sueldo_original:Ext.getCmp("suesal").getSueldoOriginal(),
                                            cargo_original:Ext.getCmp("codcargo").getCargoOriginal(),
                                            posicion_original:((TIPO_EMPRESA == 1)?Ext.getCmp("nomposicion_id").getPosicionOriginal():0)
                                        },
                                        reset: true,
                                        failure: function(form_instance, action) {
                                            Ext.MessageBox.alert('Alerta', "Hay Errores en el proceso");
                                        },
                                        success: function(form_instance, action){
                                            document.location.href = "IntegrantesForm.php"+RELOAD_URL_ADD;
                                        }
                                    });
                                }
                                else{
                                    var errors = [];
                                    var fields = form.getFields(); 
                                    fields.each(function (field) {
                                        errors = errors.concat(Ext.Array.map(field.getErrors(), function (error) {
                                           //console.log('field: ' + field.getName() + ' error: '+ error );
                                        }));
                                    });

                                    Ext.MessageBox.alert('Alerta', "Debe llenar los campos obligatorios");
                                }
                            } 
                        },
                        { 
                            text: 'Cancelar',
                            handler: function() {
                                document.location.href = "maestro_personal.php";
                            } 
                        }
                    ]
               }
                //FIN FORM
            ]
        }, 
        {
            title: 'Calendario',
            disabled:(EDITAR_PERSONAL != 1),
            id:'tab-calendario',
            loader: {
                url: "calendarios_personal_ajax.php?anio="+(new Date().getFullYear())+"&ficha="+ACTUAL,
                contentType: 'html',
                loadMask: true
            }
        }, 
        {
            title: 'Carga Familiar',
            disabled:(EDITAR_PERSONAL != 1),
            layout: 'fit',
            items:[
                {
                    xtype: 'box',
                    anchor:'100%',
                    autoEl: {
                        tag: 'iframe',
                        src: "familiares_ajax.php?cedula="+CEDULA_ACTUAL+"&txtficha="+ACTUAL,
                    },
                }
            ],
            id:'tab-cargas-familiares'
        }, 
        {
            title: 'Campos Adicionales',
            disabled:(EDITAR_PERSONAL != 1),
            layout: 'fit',
            items:[
                {
                    xtype: 'box',
                    anchor:'100%',
                    autoEl: {
                        tag: 'iframe',
                        src: 'otrosdatos_integrantes_ajax.php?txtficha='+ACTUAL,
                    },
                }
            ]
        }, 
        {
            title: 'Expediente',
            disabled:(EDITAR_PERSONAL != 1),
            layout: 'fit',
            items:[
                {
                    xtype: 'box',
                    anchor:'100%',
                    autoEl: {
                        tag: 'iframe',
                        src: '../expediente/expediente_list_ajax.php?cedula='+CEDULA_ACTUAL,
                    },
                }
            ]
        }
        
    ],
    listeners: {
        tabchange: function(tabPanel, newItem, oldItem) {
            newItem.loader.load();
            
        }
    },
    
    initComponent: function() {
        //this.width = 590;
        this.callParent();
    }
});