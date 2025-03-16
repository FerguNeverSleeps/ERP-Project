/**
* @style_tabs.js funcion para crear de forma genera los panel para los formularios
*/
Ext.ns('Selectra.pyme.tabs');
Selectra.pyme.tabs.TabPanelElements = {
   init: function(){
      var panelsData = [];
      var elements = $('.tab');
      _.each(elements, function(object, key){
         var content = 'div_'+ $(object).attr('id');
         var title = $(object).html();
         addPanel(title, content);
      });

      /**
      * Crea agrega al array un nuevo panel
      * @param  title {String}: Titulo del panel
      * @param content {String}: id del contenido del panel
      */
      function addPanel(title, content){
         var panel = new Ext.Panel({contentEl: content, title: title});
         panelsData.push(panel);
      }

      this.tabs = new Ext.TabPanel({
         renderTo:'contenedorTAB',
         activeTab:0,
         plain:true,
         defaults:{
            autoHeight: true
         },
         items:[
            panelsData
         ]
        });

    }
}
Ext.onReady(Selectra.pyme.tabs.TabPanelElements.init, Selectra.pyme.tabs.TabPanelElements);