/**
 * @class Ext.chart.series.Bar
 * @extends Ext.chart.series.StackedCartesian
 * 
 * Creates a Bar Chart.
 * 
 *     @example preview
 *     var chart = new Ext.chart.Chart({
 *         store: {
 *           fields: ['name', 'data1', 'data2', 'data3', 'data4', 'data5'],
 *           data: [
 *               {'name':'metric one', 'data1':10, 'data2':12, 'data3':14, 'data4':8, 'data5':13},
 *               {'name':'metric two', 'data1':7, 'data2':8, 'data3':16, 'data4':10, 'data5':3},
 *               {'name':'metric three', 'data1':5, 'data2':2, 'data3':14, 'data4':12, 'data5':7},
 *               {'name':'metric four', 'data1':2, 'data2':14, 'data3':6, 'data4':1, 'data5':23},
 *               {'name':'metric five', 'data1':27, 'data2':38, 'data3':36, 'data4':13, 'data5':33}
 *           ]
 *         },
 *         axes: [{
 *             type: 'numeric',
 *             position: 'left',
 *             title: {
 *                 text: 'Sample Values',
 *                 fontSize: 15
 *             },
 *             fields: 'data1'
 *         }, {
 *             type: 'category',
 *             position: 'bottom',
 *             title: {
 *                 text: 'Sample Values',
 *                 fontSize: 15
 *             },
 *             fields: 'name'
 *         }],
 *         series: [{
 *             type: 'bar',
 *             xField: 'name',
 *             yField: 'data1',
 *             style: {
 *               fill: 'blue'
 *             }
 *         }]
 *     });
 *     Ext.Viewport.setLayout('fit');
 *     Ext.Viewport.add(chart);
 */
Ext.define('Ext.chart.series.Bar', {

    extend: 'Ext.chart.series.StackedCartesian',

    alias: 'series.bar',
    type: 'bar',
    seriesType: 'barSeries',

    requires: [
        'Ext.chart.series.sprite.Bar',
        'Ext.draw.sprite.Rect'
    ],

    config: {
        /**
         * @private
         * @cfg {Object} itemInstancing Sprite template used for series.
         */
        itemInstancing: {
            type: 'rect',
            fx: {
                customDuration: {
                    x: 0,
                    y: 0,
                    width: 0,
                    height: 0,
                    radius: 0
                }
            }
        }
    },

    getItemForPoint: function (x, y) {
        if (this.getSprites()) {
            var me = this,
                chart = me.getChart(),
                padding = chart.getInnerPadding();

            // Convert the coordinates because the "items" sprites that draw the bars ignore the chart's InnerPadding.
            // See also Ext.chart.series.sprite.Bar.getItemForPoint(x,y) regarding the series's vertical coordinate system.
            //
            // TODO: Cleanup the bar sprites.
            arguments[0] = x - padding.left;
            arguments[1] = y + padding.bottom;
            return me.callParent(arguments);
        }
    },

    updateXAxis: function (axis) {
        axis.setLabelInSpan(true);
        this.callSuper(arguments);
    },

    updateHidden: function (hidden) {
        this.callParent(arguments);
        this.updateStacked();
    },

    updateStacked: function (stacked) {
        var sprites = this.getSprites(),
            ln = sprites.length,
            visible = [],
            attrs = {}, i;

        for (i = 0; i < ln; i++) {
            if (!sprites[i].attr.hidden) {
                visible.push(sprites[i]);
            }
        }
        ln = visible.length;

        if (this.getStacked()) {
            attrs.groupCount = 1;
            attrs.groupOffset = 0;
            for (i = 0; i < ln; i++) {
                visible[i].setAttributes(attrs);
            }
        } else {
            attrs.groupCount = visible.length;
            for (i = 0; i < ln; i++) {
                attrs.groupOffset = i;
                visible[i].setAttributes(attrs);
            }
        }
        this.callSuper(arguments);
    }
});
