Ext.onReady(function(){

	Ext.define('Empresa',{
        extend: 'Ext.data.Model',
        fields: [
            { mapping: 'codigo',name: 'codigo', type: 'int' },
			{ mapping: 'nombre',name: 'nombre', type: 'string' },
			{ mapping: 'bd_nomina',name: 'bd_nomina', type: 'string'  }
		]
    });

	Ext.create("widget.window",{
	    xtype: 'login',

	    /*requires: [
	        'Ext.form.Panel',
	        'Ext.layout.container.Form',
	        'Ext.layout.container.Column',
	        'Ext.form.field.Text',
	        'Ext.form.field.ComboBox'
	    ],*/
	    bodyPadding: 10,
	    title: 'Login',
	    
	    
	    closable: false,
	    draggable:false,
	    autoShow: true,
	    plain: true,
	    items: {
	        xtype: 'form',
	        id:'form-login',
        	reference: 'form-login',
	        layout: 'column',
		    
		    defaults: {
		        layout: 'form',
		        xtype: 'container',
		        defaultType: 'textfield',
		        style: 'width: 50%;padding: 5 5 5 5;'
		    },
	        items: [
		        {
		        	items:[
		        		{
				            xtype: 'textfield',
				        	//labelAlign:'left',
				            name: 'txtUsuario',
				            id: 'txtUsuario',
				            fieldLabel: 'Usuario',
				            allowBlank: false
				        }, {
				            xtype: 'textfield',
				        	//labelAlign:'left',
				            name: 'txtContrasena',
				            inputType: 'password',
				            fieldLabel: 'Password',
				            allowBlank: false
				        },{
				            xtype: 'combobox',
				            allowBlank: false,
				            name: 'empresaSeleccionada',
				            fieldLabel: 'Organizacion',
				            reference: 'organizacion',
				            queryMode: 'remote',
				            triggerAction :'all',
				            editable: false,
				            forceSelection: true,
				            displayField: 'nombre',
				            valueField: 'bd_nomina',
				            store: {
				                model: 'Empresa',
				                proxy: {
				                    type: 'ajax',
				                    url: '../../../includes/clases/controladores/ControladorApp.php',
				                    reader: {
				                        type: 'json',
				                        rootProperty: 'matches'
				                    },
				                    extraParams:{
				                    	user:'',
				                    	accion:'Accion/Empresa/Get_Empresas',

				                    }
				                },
				                isolated: false,
				                autoDestroy: true,
				                autoLoad: false,
				                listeners:{
				                	beforeload:function( store, operation, eOpts ){
				                		//console.log(this)
				                		//console.log(this.proxy.config.extraParams.user,Ext.getCmp("txtUsuario").getValue())
				                		this.proxy.config.extraParams.user = Ext.getCmp("txtUsuario").getValue();
				                		//console.log(this.proxy.config.extraParams.user)
				                	}
				                }
				            }
				        }
		        	]
		        },
		        {
		        	items:[
		        		{
				            xtype: 'component',
				            id: 'app-login-logo',
				            width:300,
				            height:80,
				            html: '<img src="../../../includes/assets/img/logox	.png" width="260" height="80" alt="logo">'
				        }
		        	]
		        }
	        ]

	        
	        ,
	        buttons: [
	        	{
	        		xtype:'displayfield',
	        		id:'mensaje'
	        	},
	        	{
	            text: 'Login',
	            formBind: true,
	            listeners: {
	                click: function(){   
				    	//var controller = this;
				    	var form = Ext.getCmp('form-login');//this.lookupReference('form-login');
				    	if (form.isValid()) {
				            Ext.getBody().mask("Verificando Identidad");
				            var params = new Array();
				            //var paramsForm = form.getValues();
				            //var paramsSubmit = new Array();
				            
				            form.submit({
					            url: 'login.php',
					            method: 'POST',
						        params:{
					            	accion:'User/Login'
					            },
				            	success: function(form,action){
				            		//console.log(action)
				            		if(action.result.login == 1){
				            			document.location.href = "../../paginas/seleccionar_nomina.php"
				            		}
				            		else{
				            			Ext.getCmp('mensaje').setValue("No Autorizado");
				            		}
				            	},
				                failure: function(form,action){
				            		         // 		
				            	}
				            });
				        }
				    }
	            }
	        }]
	    }
	});

});