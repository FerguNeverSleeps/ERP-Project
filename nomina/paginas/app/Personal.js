Ext.application({
	appFolder: '../../includes/js/gui/Personal',
    name: 'Personal',
    models: ['Integrante'],
    stores: ['Integrantes'],
    views: ['Viewport','ViewportController'],
    autoCreateViewport: true,    
    launch: function () {

    }
});