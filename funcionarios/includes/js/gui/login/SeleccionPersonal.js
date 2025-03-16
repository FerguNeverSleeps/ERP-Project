Ext.onReady(function(){

	Ext.define('Empresa',{
        extend: 'Ext.data.Model',
        fields: [
            { name: 'foto', type: 'string' },
	        { name: 'cedula', type: 'string' },
	        { name: 'nombre', type: 'string' },
	        { name: 'apellido', type: 'string' },
	        { name: 'estado', type: 'string' },
	        { name: 'ficha', type: 'string' }
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
	    title: TITULO,
	    
	    
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
				            xtype: 'component',
				            id: 'app-login-logo',
				            width:300,
				            height:80,
				            html: '<img src="../../includes/assets/img/logox.png" width="260" height="80" alt="logo">'
				        },				        
		        		{
					        xtype: 'textfield',
					        name:'seleccion_nomina',
					        id:'seleccion_nomina',
					        hidden:true,
					        value:1
					    },
		        		{
					        xtype: 'radiogroup',
					        id:'tipo_nomina',
					        // Arrange radio buttons into two columns, distributed vertically
					        columns: 1,
					        vertical: true,
					        items: ARRAY_NOMINAS
					    }
		        	]
		        }
	        ]

	        
	        ,
	        buttons: [
	        	{
	            text: 'Continuar',
	            formBind: true,
	            listeners: {
	                click: function(){   
				    	//var controller = this;
				    	var form = Ext.getCmp('form-login');//this.lookupReference('form-login');
				    	if (form.isValid()) {
				            var params = new Array();
				            //var paramsForm = form.getValues();
				            //var paramsSubmit = new Array();
				            var seleccion_nomina = Ext.getCmp("seleccion_nomina").getValue();
				            var valNom = Ext.getCmp("tipo_nomina").getValue();
				            //var valNom = opt.getValue();
				            //console.log(opt)

				            document.location.href = "seleccionar_nomina.php?seleccion_nomina="+seleccion_nomina+"&opt="+valNom;
				        }
				    }
	            }
	        }]
	    }
	});

});