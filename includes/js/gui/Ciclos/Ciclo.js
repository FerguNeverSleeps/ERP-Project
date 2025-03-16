Ext.application({
	appFolder: '../../includes/js/gui/Ciclos',
    name: 'Ciclo',
    models: ['Ciclo'],
    stores: ['Ciclos'],
    autoCreateViewport: false,    

    views: [
        //'Ciclos.view.Viewport',
        'Ciclos.view.Ciclo'
    ],
    launch: function () {
    		Ext.widget('ciclo-lista');
    	    	
    }
});