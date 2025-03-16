Ext.application({
    appFolder: '../../includes/js/gui/Personal',
    name: 'Personal',
    models: ['Integrante'/*,'Cuenta'*/],
    stores: ['Integrantes'/*,'Cuentas','PreCuentas','Posicion','Profesion','Log_Transacciones','Funcion','PlanillaHorizontal'*/],
    //views: ['Viewport','ViewportController','IntegranteForm','IntegranteFormController'],
    autoCreateViewport: false,//true,    

    views: [
        //'Personal.view.Viewport',
        'Personal.view.IntegranteForm'/*,
        'Personal.view.Cuenta',
        'Personal.view.CuentaForm',
        'Personal.view.PreCuenta',
        'Personal.view.PreCuentaForm',
        'Personal.view.Posicion',
        'Personal.view.PosicionForm',
        'Personal.view.Profesion',
        'Personal.view.ProfesionForm',
        'Personal.view.Log_Transacciones',
        'Personal.view.Log_TransaccionesForm',
        'Personal.view.Funcion',
        'Personal.view.FuncionForm',
        'Personal.view.PlanillaHorizontal',*/
    ],
    launch: function () {
        /*
        if(TIPO_VIEW == "lista"){
            Ext.widget('personal-lista');
        }
        else */
        if(TIPO_VIEW == "form"){
            Ext.widget('personal-form');
        }
        /*
        else if(TIPO_VIEW == "concue-list"){
            Ext.widget('concue-lista');
        }
        else if(TIPO_VIEW == "concue-form"){
            Ext.widget('concue-form');
        }
        else if(TIPO_VIEW == "precue-list"){
            Ext.widget('precue-lista');
        }
        else if(TIPO_VIEW == "precue-form"){
            Ext.widget('precue-form');
        }
        else if(TIPO_VIEW == "posicion-list"){
            Ext.widget('posicion-lista');
        }
        else if(TIPO_VIEW == "posicion-form"){
            Ext.widget('posicion-form');
        }
        else if(TIPO_VIEW == "profesion-list"){
            Ext.widget('profesion-lista');
        }
        else if(TIPO_VIEW == "profesion-form"){
            Ext.widget('profesion-form');
        }
        else if(TIPO_VIEW == "log_transacciones-list"){
            Ext.widget('log_transacciones-lista');
        }
        else if(TIPO_VIEW == "log_transacciones-form"){
            Ext.widget('log_transacciones-form');
        }
        else if(TIPO_VIEW == "funcion-list"){
            Ext.widget('funcion-lista');
        }
        else if(TIPO_VIEW == "funcion-form"){
            Ext.widget('funcion-form');
        }        
        else if(TIPO_VIEW == "planilla_horizontal"){
            Ext.widget('planilla-horizontal');
        }
        */
    }
});
