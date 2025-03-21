/**
 * Series is the abstract class containing the common logic to all chart series. Series includes
 * methods from Labels, Highlights, and Callouts mixins. This class implements the logic of
 * animating, hiding, showing all elements and returning the color of the series to be used as a legend item.
 *
 * ## Listeners
 *
 * The series class supports listeners via the Observable syntax. Some of these listeners are:
 *
 *  - `itemmouseup` When the user interacts with a marker.
 *  - `itemmousedown` When the user interacts with a marker.
 *  - `itemmousemove` When the user interacts with a marker.
 *  - (similar `item*` events occur for many raw mouse and touch events)
 *  - `afterrender` Will be triggered when the animation ends or when the series has been rendered completely.
 *
 * For example:
 *
 *     series: [{
 *         type: 'bar',
 *         axis: 'left',
 *         listeners: {
 *             'afterrender': function() {
 *                 console('afterrender');
 *             }
 *         },
 *         xField: 'category',
 *         yField: 'data1'
 *     }]
 *
 */
Ext.define('Ext.chart.series.Series', {

    requires: ['Ext.chart.Markers', 'Ext.chart.label.Label'],

    mixins: {
        observable: 'Ext.mixin.Observable'
    },

    /**
     * @property {String} type
     * The type of series. Set in subclasses.
     * @protected
     */
    type: null,

    /**
     * @property {String} seriesType
     * Default series sprite type.
     */
    seriesType: 'sprite',

    identifiablePrefix: 'ext-line-',

    observableType: 'series',

    /**
     * @event chartattached
     * Fires when the {@link Ext.chart.AbstractChart} has been attached to this series.
     * @param {Ext.chart.AbstractChart} chart
     * @param {Ext.chart.series.Series} series
     */
    /**
     * @event chartdetached
     * Fires when the {@link Ext.chart.AbstractChart} has been detached from this series.
     * @param {Ext.chart.AbstractChart} chart
     * @param {Ext.chart.series.Series} series
     */

    config: {
        /**
         * @private
         * @cfg {Object} chart The chart that the series is bound.
         */
        chart: null,

        /**
         * @cfg {String|String[]} title
         * The human-readable name of the series (displayed in the legend).
         */
        title: null,

        /**
         * @cfg {Function} renderer
         * A function that can be provided to set custom styling properties to each rendered element.
         * It receives `(sprite, config, rendererData, index)` as parameters.
         *
         * @param {Object} sprite The sprite affected by the renderer. The visual attributes are in `sprite.attr`.
         * The data field is available in `sprite.getField()`.
         * @param {Object} config The sprite configuration. It varies with the series and the type of sprite:
         * for instance, a Line chart sprite might have just the `x` and `y` properties while a Bar
         * chart sprite also has `width` and `height`. A `type` might be present too. For instance to
         * draw each marker and each segment of a Line chart, the renderer is called with the
         * `config.type` set to either `marker` or `line`.
         * @param {Object} rendererData A record with different properties depending on the type of chart.
         * The only guaranteed property is `rendererData.store`, the store used by the series.
         * In some cases, a store may not exist: for instance a Gauge chart may read its value directly
         * from its configuration; in this case rendererData.store is null and the value is
         * available in rendererData.value.
         * @param {Number} index The index of the sprite. It is usually the index of the store record associated
         * with the sprite, in which case the record can be obtained with `store.getData().items[index]`.
         * If the chart is not associated with a store, the index represents the index of the sprite within
         * the series. For instance a Gauge chart may have as many sprites as there are sectors in the
         * background of the gauge, plus one for the needle.
         *
         * @return {Object} The attributes that have been changed or added. Note: it is usually possible to
         * add or modify the attributes directly into the `config` parameter and not return anything,
         * but returning an object with only those attributes that have been changed may allow for
         * optimizations in the rendering of some series. Example to draw every other item in red:
         *
         *      renderer: function (sprite, config, rendererData, index) {
         *          if (index % 2 == 0) {
         *              return { strokeStyle: 'red' };
         *          }
         *      }
         */
        renderer: null,

        /**
         * @cfg {Boolean} showInLegend
         * Whether to show this series in the legend.
         */
        showInLegend: true,

        //@private triggerdrawlistener flag
        triggerAfterDraw: false,

        /**
         * @private
         * Not supported.
         */
        themeStyle: {},

        /**
         * @cfg {Object} style Custom style configuration for the sprite used in the series.
         */
        style: {},

        /**
         * @cfg {Object} subStyle This is the cyclic used if the series has multiple sprites.
         */
        subStyle: {},

        /**
         * @cfg {Array} colors
         * An array of color values which will be used, in order, as the pie slice fill colors.
         */
        colors: null,

        /**
         * @protected
         * @cfg {Object} store The store of values used in the series.
         */
        store: null,

        /**
         * @cfg {Object} label
         * The style object for labels.
         */

        /**
         * @cfg {Object} label
         * Object with the following properties:
         *
         * @cfg {String} label.display
         *
         * Specifies the presence and position of the labels. The possible values depend on the chart type.
         * For Line charts: 'under' | 'over' | 'rotate'.
         * For Bar charts: 'insideStart' | 'insideEnd' | 'outside'.
         * For Pie charts: 'outside' | 'rotate'.
         * For all charts: 'none' hides the labels.
         *
         * Default value: 'none'.
         *
         * @cfg {String} label.color
         *
         * The color of the label text.
         *
         * Default value: '#000' (black).
         *
         * @cfg {String|String[]} label.field
         *
         * The name(s) of the field(s) to be displayed in the labels. If your chart has 3 series
         * that correspond to the fields 'a', 'b', and 'c' of your model and you only want to
         * display labels for the series 'c', you must still provide an array `[null, null, 'c']`.
         *
         * Default value: null.
         *
         * @cfg {String} label.font
         *
         * The font used for the labels.
         *
         * Default value: '14px Helvetica'.
         *
         * @cfg {String} label.orientation
         *
         * Either 'horizontal' or 'vertical'. If not set (default), the orientation is inferred
         * from the value of the flipXY property of the series.
         *
         * Default value: ''.
         *
         * @cfg {Function} label.renderer
         *
         * Optional function for formatting the label into a displayable value.
         *
         * The arguments to the method are:
         *
         *   - *`text`*, *`sprite`*, *`config`*, *`rendererData`*, *`index`*
         *
         *     Label's renderer is passed the same arguments as {@link #renderer}
         *     plus one extra 'text' argument which comes first.
         *
         * @return {Object|String} The attributes that have been changed or added, or the text for the label.
         * Example to enclose every other label in parentheses:
         *
         *      renderer: function (text) {
         *          if (index % 2 == 0) {
         *              return '(' + text + ')'
         *          }
         *      }
         *
         * Default value: null.
         */
        label: {textBaseline: 'middle', textAlign: 'center', font: '14px Helvetica'},

        /**
         * @cfg {Number} labelOverflowPadding
         * Extra distance value for which the labelOverflow listener is triggered.
         */
        labelOverflowPadding: 5,

        /**
         * @cfg {String|String[]} labelField
         * @deprecated Use 'field' property of {@link Ext.chart.series.Series#label} instead.
         * The store record field name to be used for the series labels.
         */
        labelField: null,

        /**
         * @cfg {Object} marker
         * The sprite template used by marker instances on the series.
         */
        marker: null,

        /**
         * @cfg {Object} markerSubStyle
         * This is cyclic used if series have multiple marker sprites.
         */
        markerSubStyle: null,

        /**
         * @protected
         * @cfg {Object} itemInstancing The sprite template used to create sprite instances in the series.
         */
        itemInstancing: null,

        /**
         * @cfg {Object} background Sets the background of the surface the series is attached.
         */
        background: null,

        /**
         * @cfg {Object} highlightItem The item currently highlighted in the series.
         */
        highlightItem: null,

        /**
         * @protected
         * @cfg {Object} surface The surface that the series is attached.
         */
        surface: null,

        /**
         * @protected
         * @cfg {Object} overlaySurface The surface that series markers are attached.
         */
        overlaySurface: null,

        /**
         * @cfg {Boolean|Array} hidden
         */
        hidden: false,

        /**
         * @cfg {Object} highlightCfg The sprite configuration used when highlighting items in the series.
         */
        highlightCfg: null,

        /**
         * @cfg {Object} animate The series animation configuration.
         */
        animate: null
    },

    directions: [],

    sprites: null,

    getFields: function (fieldCategory) {
        var me = this,
            fields = [], fieldsItem,
            i, ln;
        for (i = 0, ln = fieldCategory.length; i < ln; i++) {
            fieldsItem = me['get' + fieldCategory[i] + 'Field']();
            fields.push(fieldsItem);
        }
        return fields;
    },

    updateAnimate: function (animate) {
        var sprites = this.getSprites(), i = -1, ln = sprites.length;
        while (++i < ln) {
            sprites[i].fx.setConfig(animate);
        }
    },

    updateTitle: function (newTitle) {
        if (newTitle) {
            var chart = this.getChart(),
                series = chart.getSeries(),
                legendStore = chart.getLegendStore(),
                index, rec;

            if (series) {
                index = Ext.Array.indexOf(series, this);

                if (index !== -1) {
                    rec = legendStore.getAt(index);
                    rec.set('name', newTitle);
                }
            }
        }
    },

    updateColors: function (colorSet) {
        this.setSubStyle({fillStyle: colorSet});
        this.doUpdateStyles();
    },

    applyHighlightCfg: function (highlight, oldHighlight) {
        return Ext.apply(oldHighlight || {}, highlight);
    },

    applyItemInstancing: function (instancing, oldInstancing) {
        return Ext.merge(oldInstancing || {}, instancing);
    },

    setAttributesForItem: function (item, change) {
        if (item && item.sprite) {
            if (item.sprite.itemsMarker && item.category === 'items') {
                item.sprite.putMarker(item.category, change, item.index, false, true);
            }
            if (item.sprite.isMarkerHolder && item.category === 'markers') {
                item.sprite.putMarker(item.category, change, item.index, false, true);
            } else if (item.sprite instanceof Ext.draw.sprite.Instancing) {
                item.sprite.setAttributesFor(item.index, change);
            } else {

                item.sprite.setAttributes(change);
            }
        }
    },

    applyHighlightItem: function (newHighlightItem, oldHighlightItem) {
        if (newHighlightItem === oldHighlightItem) {
            return;
        }
        if (Ext.isObject(newHighlightItem) && Ext.isObject(oldHighlightItem)) {
            if (newHighlightItem.sprite === oldHighlightItem.sprite &&
                newHighlightItem.index === oldHighlightItem.index
                ) {
                return;
            }
        }
        return newHighlightItem;
    },

    updateHighlightItem: function (newHighlightItem, oldHighlightItem) {
        this.setAttributesForItem(oldHighlightItem, {highlighted: false});
        this.setAttributesForItem(newHighlightItem, {highlighted: true});
    },

    constructor: function (config) {
        var me = this;
        me.getId();
        me.sprites = [];
        me.dataRange = [];
        Ext.ComponentManager.register(me);
        me.mixins.observable.constructor.apply(me, arguments);
    },

    applyStore: function (store) {
        return Ext.StoreManager.lookup(store);
    },

    getStore: function () {
        return this._store || this.getChart() && this.getChart().getStore();
    },

    updateStore: function (newStore, oldStore) {
        var me = this,
            chartStore = this.getChart() && this.getChart().getStore(),
            sprites = me.getSprites(),
            ln = sprites.length,
            i, sprite;
        newStore = newStore || chartStore;
        oldStore = oldStore || chartStore;

        if (oldStore) {
            oldStore.un('updaterecord', 'onUpdateRecord', me);
            oldStore.un('refresh', 'refresh', me);
        }
        if (newStore) {
            newStore.on('updaterecord', 'onUpdateRecord', me);
            newStore.on('refresh', 'refresh', me);
            for (i = 0; i < ln; i++) {
                sprite = sprites[i];
                if (sprite.setStore) {
                    sprite.setStore(newStore);
                }
            }
            me.refresh();
        }
    },

    onStoreChanged: function (store, oldStore) {
        if (!this._store) {
            this.updateStore(store, oldStore);
        }
    },

    coordinateStacked: function (direction, directionOffset, directionCount) {
        var me = this,
            store = me.getStore(),
            items = store.getData().items,
            axis = me['get' + direction + 'Axis'](),
            hidden = me.getHidden(),
            range = {min: 0, max: 0},
            directions = me['fieldCategory' + direction],
            fieldCategoriesItem,
            i, j, k, fields, field, data, style = {},
            dataStart = [], dataEnd, posDataStart = [], negDataStart = [],
            stacked = me.getStacked(),
            sprites = me.getSprites();

        if (sprites.length > 0) {
            for (i = 0; i < directions.length; i++) {
                fieldCategoriesItem = directions[i];
                fields = me.getFields([fieldCategoriesItem]);
                for (j = 0; j < items.length; j++) {
                    dataStart[j] = 0;
                    posDataStart[j] = 0;
                    negDataStart[j] = 0;
                }
                for (j = 0; j < fields.length; j++) {
                    style = {};
                    field = fields[j];
                    if (hidden[j]) {
                        style['dataStart' + fieldCategoriesItem] = dataStart;
                        style['data' + fieldCategoriesItem] = dataStart;
                        sprites[j].setAttributes(style);
                        continue;
                    }
                    data = me.coordinateData(items, field, axis);
                    if (stacked) {
                        dataEnd = [];
                        for (k = 0; k < items.length; k++) {
                            if (!data[k]) {
                                data[k] = 0;
                            }
                            if (data[k] >= 0) {
                                dataStart[k] = posDataStart[k];
                                posDataStart[k] += data[k];
                                dataEnd[k] = posDataStart[k];
                            } else {
                                dataStart[k] = negDataStart[k];
                                negDataStart[k] += data[k];
                                dataEnd[k] = negDataStart[k];
                            }
                        }
                        style['dataStart' + fieldCategoriesItem] = dataStart;
                        style['data' + fieldCategoriesItem] = dataEnd;
                        me.getRangeOfData(dataStart, range);
                        me.getRangeOfData(dataEnd, range);
                    } else {
                        style['dataStart' + fieldCategoriesItem] = dataStart;
                        style['data' + fieldCategoriesItem] = data;
                        me.getRangeOfData(data, range);
                    }
                    sprites[j].setAttributes(style);
                }
            }
            me.dataRange[directionOffset] = range.min;
            me.dataRange[directionOffset + directionCount] = range.max;
            style = {};
            style['dataMin' + direction] = range.min;
            style['dataMax' + direction] = range.max;
            for (i = 0; i < sprites.length; i++) {
                sprites[i].setAttributes(style);
            }
        }
    },

    coordinate: function (direction, directionOffset, directionCount) {
        var me = this,
            store = me.getStore(),
            hidden = me.getHidden(),
            items = store.getData().items,
            axis = me['get' + direction + 'Axis'](),
            range = {min: Infinity, max: -Infinity},
            fieldCategory = me['fieldCategory' + direction] || [direction],
            fields = me.getFields(fieldCategory),
            i, field, data, style = {},
            sprites = me.getSprites();
        if (sprites.length > 0) {
            if (!Ext.isBoolean(hidden) || !hidden) {
                for (i = 0; i < fieldCategory.length; i++) {
                    field = fields[i];
                    data = me.coordinateData(items, field, axis);
                    me.getRangeOfData(data, range);
                    style['data' + fieldCategory[i]] = data;
                }
            }
            me.dataRange[directionOffset] = range.min;
            me.dataRange[directionOffset + directionCount] = range.max;
            style['dataMin' + direction] = range.min;
            style['dataMax' + direction] = range.max;
            if (axis) {
                axis.range = null;
                style['range' + direction] = axis.getRange();
            }
            for (i = 0; i < sprites.length; i++) {
                sprites[i].setAttributes(style);
            }
        }
    },

    /**
     * @private
     * This method will return an array containing data coordinated by a specific axis.
     * @param {Array} items
     * @param {String} field
     * @param {Ext.chart.axis.Axis} axis
     * @return {Array}
     */
    coordinateData: function (items, field, axis) {
        var data = [],
            length = items.length,
            layout = axis && axis.getLayout(),
            coord = axis ? function (x, field, idx, items) {
                return layout.getCoordFor(x, field, idx, items);
            } : function (x) { return +x; },
            i, x;
        for (i = 0; i < length; i++) {
            x = items[i].data[field];
            data[i] = !Ext.isEmpty(x) ? coord(x, field, i, items) : x;
        }
        return data;
    },

    getRangeOfData: function (data, range) {
        var i, length = data.length,
            value, min = range.min, max = range.max;
        for (i = 0; i < length; i++) {
            value = data[i];
            if (value < min) {
                min = value;
            }
            if (value > max) {
                max = value;
            }
        }
        range.min = min;
        range.max = max;
    },

    updateLabelData: function () {
        var me = this,
            store = me.getStore(),
            items = store.getData().items,
            sprites = me.getSprites(),
            labelTpl = me.getLabel().getTemplate(),
            labelFields = Ext.Array.from(labelTpl.getField() || me.getLabelField()),
            i, j, ln, labels,
            sprite, field;

        if (!sprites.length || !labelFields.length) {
            return;
        }

        for (i = 0; i < sprites.length; i++) {
            labels = [];
            sprite = sprites[i];
            field = sprite.getField();
            if (labelFields.indexOf(field) < 0) {
                field = labelFields[i];
            }
            for (j = 0, ln = items.length; j < ln; j++) {
                labels.push(items[j].get(field));
            }
            sprite.setAttributes({labels: labels});
        }
    },

    updateLabelField: function (labelField) {
        var labelTpl = this.getLabel().getTemplate();
        if (!labelTpl.config.field) {
            labelTpl.setField(labelField)
        }
    },

    processData: function () {
        if (!this.getStore()) {
            return;
        }

        var me = this,
            directions = this.directions,
            i, ln = directions.length,
            fieldCategory, axis;

        for (i = 0; i < ln; i++) {
            fieldCategory = directions[i];
            if (me['get' + fieldCategory + 'Axis']) {
                axis = me['get' + fieldCategory + 'Axis']();
                if (axis) {
                    axis.processData(me);
                    continue;
                }
            }
            if (me['coordinate' + fieldCategory]) {
                me['coordinate' + fieldCategory]();
            }
        }
        me.updateLabelData();
    },

    applyBackground: function (background) {
        if (this.getChart()) {
            this.getSurface().setBackground(background);
            return this.getSurface().getBackground();
        } else {
            return background;
        }
    },

    updateChart: function (newChart, oldChart) {
        var me = this;
        if (oldChart) {
            oldChart.un('axeschanged', 'onAxesChanged', me);
            // TODO: destroy them
            me.sprites = [];
            me.setSurface(null);
            me.setOverlaySurface(null);
            me.onChartDetached(oldChart);
        }
        if (newChart) {
            me.setSurface(newChart.getSurface('series-surface', 'series'));
            me.setOverlaySurface(newChart.getSurface('overlay-surface', 'overlay'));

            newChart.on('axeschanged', 'onAxesChanged', me);
            if (newChart.getAxes()) {
                me.onAxesChanged(newChart);
            }
            me.onChartAttached(newChart);
        }

        me.updateStore(me._store, null);
    },

    onAxesChanged: function (chart) {
        var me = this,
            axes = chart.getAxes(), axis,
            directionMap = {}, directionMapItem,
            fieldMap = {}, fieldMapItem,
            needHighPrecision = false,
            directions = this.directions, direction,
            i, ln, j, ln2, k, ln3;

        for (i = 0, ln = directions.length; i < ln; i++) {
            direction = directions[i];
            fieldMap[direction] = me.getFields(me['fieldCategory' + direction]);
        }

        for (i = 0, ln = axes.length; i < ln; i++) {
            axis = axes[i];
            if (!directionMap[axis.getDirection()]) {
                directionMap[axis.getDirection()] = [axis];
            } else {
                directionMap[axis.getDirection()].push(axis);
            }
        }

        for (i = 0, ln = directions.length; i < ln; i++) {
            direction = directions[i];
            if (directionMap[direction]) {
                directionMapItem = directionMap[direction];
                for (j = 0, ln2 = directionMapItem.length; j < ln2; j++) {
                    axis = directionMapItem[j];
                    if (axis.getFields().length === 0) {
                        me['set' + direction + 'Axis'](axis);
                        if (axis.getNeedHighPrecision()) {
                            needHighPrecision = true;
                        }
                    } else {
                        fieldMapItem = fieldMap[direction];
                        if (fieldMapItem) {
                            for (k = 0, ln3 = fieldMapItem.length; k < ln3; k++) {
                                if (axis.fieldsMap[fieldMapItem[k]]) {
                                    me['set' + direction + 'Axis'](axis);
                                    if (axis.getNeedHighPrecision()) {
                                        needHighPrecision = true;
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        this.getSurface().setHighPrecision(needHighPrecision);
    },

    onChartDetached: function (oldChart) {
        var me = this;
        me.fireEvent('chartdetached', oldChart, me);
        oldChart.un('storechanged', 'onStoreChanged', me);
    },

    onChartAttached: function (chart) {
        var me = this;
        me.setBackground(me.getBackground());
        me.fireEvent('chartattached', chart, me);
        chart.on('storechanged', 'onStoreChanged', me);
        me.processData();
    },

    updateOverlaySurface: function (overlaySurface) {
        var me = this;
        if (overlaySurface) {
            if (me.getLabel()) {
                me.getOverlaySurface().add(me.getLabel());
            }
        }
    },

    applyLabel: function (newLabel, oldLabel) {
        if (!oldLabel) {
            oldLabel = new Ext.chart.Markers({zIndex: 10});
            oldLabel.setTemplate(new Ext.chart.label.Label(newLabel));
        } else {
            oldLabel.getTemplate().setAttributes(newLabel);
        }
        return oldLabel;
    },

    createItemInstancingSprite: function (sprite, itemInstancing) {
        var me = this,
            template,
            markers = new Ext.chart.Markers();

        markers.setAttributes({zIndex: Number.MAX_VALUE});
        var config = Ext.apply({}, itemInstancing);
        if (me.getHighlightCfg()) {
            config.highlightCfg = me.getHighlightCfg();
            config.modifiers = ['highlight'];
        }
        markers.setTemplate(config);
        template = markers.getTemplate();
        template.setAttributes(me.getStyle());
        template.fx.on('animationstart', 'onSpriteAnimationStart', this);
        template.fx.on('animationend', 'onSpriteAnimationEnd', this);
        sprite.bindMarker('items', markers);

        me.getSurface().add(markers);
        return markers;
    },

    getDefaultSpriteConfig: function () {
        return {
            type: this.seriesType,
            renderer: this.getRenderer()
        };
    },

    createSprite: function () {
        var me = this,
            surface = me.getSurface(),
            itemInstancing = me.getItemInstancing(),
            marker, config,
            sprite = surface.add(me.getDefaultSpriteConfig());

        sprite.setAttributes(this.getStyle());

        if (itemInstancing) {
            sprite.itemsMarker = me.createItemInstancingSprite(sprite, itemInstancing);
        }

        if (sprite.bindMarker) {
            if (me.getMarker()) {
                marker = new Ext.chart.Markers();
                config = Ext.merge({}, me.getMarker());
                if (me.getHighlightCfg()) {
                    config.highlightCfg = me.getHighlightCfg();
                    config.modifiers = ['highlight'];
                }
                marker.setTemplate(config);
                marker.getTemplate().fx.setCustomDuration({
                    translationX: 0,
                    translationY: 0
                });
                sprite.dataMarker = marker;
                sprite.bindMarker('markers', marker);
                me.getOverlaySurface().add(marker);
            }
            if (me.getLabel().getTemplate().getField() || me.getLabelField()) {
                sprite.bindMarker('labels', me.getLabel());
            }
        }

        if (sprite.setStore) {
            sprite.setStore(me.getStore());
        }

        sprite.fx.on('animationstart', 'onSpriteAnimationStart', me);
        sprite.fx.on('animationend', 'onSpriteAnimationEnd', me);

        me.sprites.push(sprite);

        return sprite;
    },

    /**
     * Performs drawing of this series.
     */
    getSprites: Ext.emptyFn,

    onUpdateRecord: function () {
        // TODO: do something REALLY FAST.
        this.processData();
    },

    refresh: function () {
        this.processData();
    },

    isXType: function (xtype) {
        return xtype === 'series';
    },

    getItemId: function () {
        return this.getId();
    },

    applyStyle: function (style, oldStyle) {
        // TODO: Incremental setter
        var cls = Ext.ClassManager.get(Ext.ClassManager.getNameByAlias('sprite.' + this.seriesType));
        if (cls && cls.def) {
            style = cls.def.normalize(style);
        }
        return Ext.apply(oldStyle || Ext.Object.chain(this.getThemeStyle()), style);
    },

    applyMarker: function (marker, oldMarker) {
        var type = (marker && marker.type) || (oldMarker && oldMarker.type) || this.seriesType,
            cls;
        if (type) {
            cls = Ext.ClassManager.get(Ext.ClassManager.getNameByAlias('sprite.' + type));
            if (cls && cls.def) {
                marker = cls.def.normalize(marker, true);
                marker.type = type;
                return Ext.merge(oldMarker || {}, marker);
            }
            return Ext.merge(oldMarker || {}, marker);
        }
    },

    applyMarkerSubStyle: function (marker, oldMarker) {
        var type = (marker && marker.type) || (oldMarker && oldMarker.type) || this.seriesType,
            cls;
        if (type) {
            cls = Ext.ClassManager.get(Ext.ClassManager.getNameByAlias('sprite.' + type));
            if (cls && cls.def) {
                marker = cls.def.batchedNormalize(marker, true);
                return Ext.merge(oldMarker || {}, marker);
            }
            return Ext.merge(oldMarker || {}, marker);
        }
    },

    applySubStyle: function (subStyle, oldSubStyle) {
        var cls = Ext.ClassManager.get(Ext.ClassManager.getNameByAlias('sprite.' + this.seriesType));
        if (cls && cls.def) {
            subStyle = cls.def.batchedNormalize(subStyle, true);
            return Ext.merge(oldSubStyle || {}, subStyle);
        }
        return Ext.merge(oldSubStyle || {}, subStyle);
    },

    updateHidden: function (hidden) {
        // TODO: remove this when jacky fix the problem.
        this.getColors();
        this.getSubStyle();
        this.setSubStyle({hidden: hidden});
        this.processData();
        this.doUpdateStyles();
    },

    /**
     *
     * @param {Number} index
     * @param {Boolean} value
     */
    setHiddenByIndex: function (index, value) {
        if (Ext.isArray(this.getHidden())) {
            this.getHidden()[index] = value;
            this.updateHidden(this.getHidden());
        } else {
            this.setHidden(value);
        }
    },

    updateStyle: function () {
        this.doUpdateStyles();
    },

    updateSubStyle: function () {
        this.doUpdateStyles();
    },

    doUpdateStyles: function () {
        var sprites = this.sprites,
            itemInstancing = this.getItemInstancing(),
            i = 0, ln = sprites && sprites.length,
            markerCfg = this.getMarker(),
            style;
        for (; i < ln; i++) {
            style = this.getStyleByIndex(i);
            if (itemInstancing) {
                sprites[i].itemsMarker.getTemplate().setAttributes(style);
            }
            sprites[i].setAttributes(style);
            if (markerCfg && sprites[i].dataMarker) {
                sprites[i].dataMarker.getTemplate().setAttributes(this.getMarkerStyleByIndex(i));
            }
        }
    },

    getMarkerStyleByIndex: function (i) {
        return this.getOverriddenStyleByIndex(i, this.getOverriddenStyleByIndex(i, this.getMarkerSubStyle(), this.getMarker()), this.getStyleByIndex(i));
    },

    getStyleByIndex: function (i) {
        return this.getOverriddenStyleByIndex(i, this.getSubStyle(), this.getStyle());
    },

    getOverriddenStyleByIndex: function (i, subStyle, baseStyle) {
        var subStyleItem,
            result = Ext.Object.chain(baseStyle || {});
        for (var name in subStyle) {
            subStyleItem = subStyle[name];
            if (Ext.isArray(subStyleItem)) {
                result[name] = subStyleItem[i % subStyle[name].length];
            } else {
                result[name] = subStyleItem;
            }
        }
        return result;
    },

    /**
     * For a given x/y point relative to the main region, find a corresponding item from this
     * series, if any.
     * @param {Number} x
     * @param {Number} y
     * @param {Object} [target] optional target to receive the result
     * @return {Object} An object describing the item, or null if there is no matching item. The exact contents of
     * this object will vary by series type, but should always contain at least the following:
     *
     * @return {Ext.data.Model} return.record the record of the item.
     * @return {Array} return.point the x/y coordinates relative to the chart box of a single point
     * for this data item, which can be used as e.g. a tooltip anchor point.
     * @return {Ext.draw.sprite.Sprite} return.sprite the item's rendering Sprite.
     * @return {Number} return.subSprite the index if sprite is an instancing sprite.
     */
    getItemForPoint: Ext.emptyFn,

    onSpriteAnimationStart: function (sprite) {
        this.fireEvent('animationstart', sprite);
    },

    onSpriteAnimationEnd: function (sprite) {
        this.fireEvent('animationend', sprite);
    },

    /**
     * Provide legend information to target array.
     *
     * @param {Array} target
     *
     * The information consists:
     * @param {String} target.name
     * @param {String} target.markColor
     * @param {Boolean} target.disabled
     * @param {String} target.series
     * @param {Number} target.index
     */
    provideLegendInfo: function (target) {
        target.push({
            name: this.getTitle() || this.getId(),
            mark: 'black',
            disabled: false,
            series: this.getId(),
            index: 0
        });
    },

    destroy: function () {
        this.clearListeners();
        Ext.ComponentManager.unregister(this);
        var store = this.getStore();
        if (store && store.getAutoDestroy()) {
            Ext.destroy(store);
        }
        this.setStore(null);
        this.callSuper();
    }
});
