Ext.application({
    name: 'Sencha',

    launch: function() {
    	Ext.define('Login.model.Empresa',{
	        extend: 'Ext.data.Model',
	        fields: [
	            { mapping: 'codigo',name: 'codigo', type: 'int' },
				{ mapping: 'nombre',name: 'nombre', type: 'string' },
				{ mapping: 'bd_nomina',name: 'bd_nomina', type: 'string'  }
			]
	    });

        var empresasStore = Ext.create('Ext.data.Store', {    
        	storeId:'store-empresa',
        	requires: [
			    'Login.model.Empresa',
			    'Ext.data.proxy.Rest'
			],
			//model: 'Login.model.Empresa',
			proxy: {
			        type: 'ajax',
			        url: '../../../includes/clases/controladores/ControladorApp.php',
			        extraParams: {
			            user:'',
					    accion:'Accion/Empresa/Get_Empresas',         
			        },          
			        reader: {
			            type: 'json',
			            rootProperty: 'matches'
			        }         
			},
			fields: [
	           	{ mapping: 'codigo',name: 'codigo', type: 'int' },
				{ mapping: 'nombre',name: 'nombre', type: 'string' },
				{ mapping: 'bd_nomina',name: 'bd_nomina', type: 'string'  }
	        ],
			autoLoad: true,
			listeners:{
				beforeload:function( store, operation, eOpts ){
					//console.log(this._proxy.config.extraParams)
					if(Ext.getCmp("txtUsuario")!=null)
						this._proxy.config.extraParams.user = Ext.getCmp("txtUsuario").getValue();					
				}
			}
		});


        Ext.create('Ext.form.Panel', {
		    fullscreen: true,
            tabBarPosition: 'bottom',
            title:'Login',
            id:'form-login',
		    items: [
		    				{
				                xtype: 'image',
				                src: '../../../includes/assets/img/logox.png',
				                style: 'width:260px;height:80px;margin:20px auto'
				            },
				            {
					            xtype: 'fieldset',
					        	title: 'Login',
					            defaults:{
					            	xtype:'radiofield',
					            	labelWidth:'50%'
					            },
					            items:[
					            	{
							            xtype: 'textfield',
							        	//labelAlign:'left',
							            name: 'txtUsuario',
							            id: 'txtUsuario',
							            label: 'Usuario',
							            allowBlank: false,
							            listeners:{
							            	blur:function( thisf, e, eOpts ){
							            		empresasStore.load({});
							            	}
							            }
							        }, {
							            xtype: 'passwordfield',
							        	//labelAlign:'left',
							            name: 'txtContrasena',
							            id: 'txtContrasena',
							            //inputType: 'password',
							            label: 'Password',
							            allowBlank: false
							        },{
							            xtype: 'selectfield',
							            allowBlank: false,
							            name: 'empresaSeleccionada',
							            label: 'Organizacion',
							            reference: 'organizacion',
							            queryMode: 'remote',
							            triggerAction :'all',
							            editable: false,
							            forceSelection: true,
							            displayField: 'nombre',
							            valueField: 'bd_nomina',
							            //store: 'StoreEmpresa',
							            store:empresasStore
							        }
					            ]
					        },
					        {
					        	xtype:'container',
					        	defaults:{
					        		xtype:'button'//,
					        		//flex:1
					        	},
					        	layout:{
					        		type:'hbox',
					        		align:'right'
					        	},
					        	items:[
					        		{
					        			ui:'confirm',
							            text: 'Login',
							            formBind: true,							            
							                handler: function(btn){   
										    	//var controller = this;
										    	var form = Ext.getCmp('form-login');//this.lookupReference('form-login');
										    	if (Ext.getCmp('txtUsuario').getValue()!="" || 
										    		Ext.getCmp('txtContrasena').getValue()!="") {
										            //Ext.getBody().mask("Verificando Identidad");
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
										            		//console.log(form,action)
										            		if(action.login == 1){
										            			document.location.href = "../../paginas/seleccionar_nomina.php?mobile=1"
										            		}
										            		else{
										            			Ext.getCmp('mensaje').setHtml("&nbsp;&nbsp;&nbsp;&nbsp;Acceso No Autorizado");
										            		}
										            	},
										                failure: function(form,action){
										            		         // 		
										            	}
										            });
										        }
										        else{
										        	Ext.getCmp('mensaje').setValue("Debe llenar campos");
										        }
										    }
							        },
							        {
							        	xtype:'label',
							        	id:'mensaje'	
							        }
					        	]
					        }
		    ]
		});
    }
});