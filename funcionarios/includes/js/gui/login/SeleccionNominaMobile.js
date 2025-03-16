Ext.application({
    name: 'Sencha',

    launch: function() {
    	
        Ext.create('Ext.form.Panel', {
		    fullscreen: true,
            tabBarPosition: 'bottom',
            title:'Seleccionar Planilla',
            id:'form-seleccion-planilla',
		    items: [
		    				{
				                xtype: 'image',
				                src: '../../includes/assets/img/logox.png',
				                style: 'width:260px;height:80px;margin:20px auto'
				            },
		    				{
					            xtype: 'fieldset',
					        	title: 'Seleccionar Planilla',
					            defaults:{
					            	xtype:'radiofield',
					            	labelWidth:'80%'
					            },
					            items:ARRAY_NOMINAS
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
							            text: 'Continuar',
							            formBind: true,							            
							                handler: function(btn){   
										    	//var controller = this;
										    	var form = Ext.getCmp('form-seleccion-planilla');//this.lookupReference('form-login');
										    	//if (form.isValid()) {
										            var params = new Array();
										            //var paramsForm = form.getValues();
										            //var paramsSubmit = new Array();
										            var seleccion_nomina = 1;//Ext.getCmp("seleccion_nomina").getValue();
										            var valNom = form.getValues().opt;//Ext.getCmp("tipo_nomina").getValue();
										            //var valNom = opt.getValue();
										            //console.log(valNom.opt)

										            document.location.href = "seleccionar_nomina.php?seleccion_nomina="+seleccion_nomina+"&opt="+valNom;
										        //}
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