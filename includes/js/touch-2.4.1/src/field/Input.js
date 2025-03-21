/**
 * @private
 */
Ext.define('Ext.field.Input', {
    extend: 'Ext.Component',
    xtype : 'input',

    /**
     * @event clearicontap
     * Fires whenever the clear icon is tapped.
     * @param {Ext.field.Input} this
     * @param {Ext.EventObject} e The event object
     */

    /**
     * @event masktap
     * @preventable doMaskTap
     * Fires whenever a mask is tapped.
     * @param {Ext.field.Input} this
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event focus
     * @preventable doFocus
     * Fires whenever the input get focus.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event blur
     * @preventable doBlur
     * Fires whenever the input loses focus.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event click
     * Fires whenever the input is clicked.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event keyup
     * Fires whenever keyup is detected.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event paste
     * Fires whenever paste is detected.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @event mousedown
     * Fires whenever the input has a mousedown occur.
     * @param {Ext.EventObject} e The event object.
     */

    /**
     * @property {String} tag The el tag.
     * @private
     */
    tag: 'input',

    cachedConfig: {
        /**
         * @cfg {String} cls The `className` to be applied to this input.
         * @accessor
         */
        cls: Ext.baseCSSPrefix + 'form-field',

        /**
         * @cfg {String} focusCls The CSS class to use when the field receives focus.
         * @accessor
         */
        focusCls: Ext.baseCSSPrefix + 'field-focus',

        // @private
        maskCls: Ext.baseCSSPrefix + 'field-mask',

        /**
          * @cfg {String/Boolean} useMask
         * `true` to use a mask on this field, or `auto` to automatically select when you should use it.
         * @private
         * @accessor
         */
        useMask: 'auto',

        /**
         * @cfg {String} type The type attribute for input fields -- e.g. radio, text, password.
         *
         * If you want to use a `file` input, please use the {@link Ext.field.File} component instead.
         * @accessor
         */
        type: 'text',

        /**
         * @cfg {Boolean} checked `true` if the checkbox should render initially checked.
         * @accessor
         */
        checked: false
    },

    config: {
        /**
         * @cfg
         * @inheritdoc
         */
        baseCls: Ext.baseCSSPrefix + 'field-input',

        /**
         * @cfg {String} name The field's HTML name attribute.
         * __Note:__ This property must be set if this field is to be automatically included with
         * {@link Ext.form.Panel#method-submit form submit()}.
         * @accessor
         */
        name: null,

        /**
         * @cfg {Mixed} value A value to initialize this field with.
         * @accessor
         */
        value: null,

        /**
         * @property {Boolean} `true` if the field currently has focus.
         * @accessor
         */
        isFocused: false,

        /**
         * @cfg {Number} tabIndex The `tabIndex` for this field.
         *
         * __Note:__ This only applies to fields that are rendered, not those which are built via `applyTo`.
         * @accessor
         */
        tabIndex: null,

        /**
         * @cfg {String} placeHolder A string value displayed in the input (if supported) when the control is empty.
         * @accessor
         */
        placeHolder: null,

        /**
         * @cfg {Number} [minValue=undefined] The minimum value that this Number field can accept (defaults to `undefined`, e.g. no minimum).
         * @accessor
         */
        minValue: null,

        /**
         * @cfg {Number} [maxValue=undefined] The maximum value that this Number field can accept (defaults to `undefined`, e.g. no maximum).
         * @accessor
         */
        maxValue: null,

        /**
         * @cfg {Number} [stepValue=undefined] The amount by which the field is incremented or decremented each time the spinner is tapped.
         * Defaults to `undefined`, which means that the field goes up or down by 1 each time the spinner is tapped.
         * @accessor
         */
        stepValue: null,

        /**
         * @cfg {Number} [maxLength=0] The maximum number of permitted input characters.
         * @accessor
         */
        maxLength: null,

        /**
         * @cfg {Boolean} [autoComplete=undefined]
         * `true` to set the field's DOM element `autocomplete` attribute to `"on"`, `false` to set to `"off"`. Defaults to `undefined`, leaving the attribute unset.
         * @accessor
         */
        autoComplete: null,

        /**
         * @cfg {Boolean} [autoCapitalize=undefined]
         * `true` to set the field's DOM element `autocapitalize` attribute to `"on"`, `false` to set to `"off"`. Defaults to `undefined`, leaving the attribute unset
         * @accessor
         */
        autoCapitalize: null,

        /**
         * `true` to set the field DOM element `autocorrect` attribute to `"on"`, `false` to set to `"off"`. Defaults to `undefined`, leaving the attribute unset.
         * @cfg {Boolean} autoCorrect
         * @accessor
         */
        autoCorrect: null,

        /**
         * @cfg {Boolean} [readOnly=undefined]
         * `true` to set the field DOM element `readonly` attribute to `"true"`. Defaults to `undefined`, leaving the attribute unset.
         * @accessor
         */
        readOnly: null,

        /**
         * @cfg {Number} [maxRows=undefined]
         * Sets the field DOM element `maxRows` attribute. Defaults to `undefined`, leaving the attribute unset.
         * @accessor
         */
        maxRows: null,

        /**
         * @cfg {String} pattern The value for the HTML5 `pattern` attribute.
         * You can use this to change which keyboard layout will be used.
         *
         *     Ext.define('Ux.field.Pattern', {
         *         extend : 'Ext.field.Text',
         *         xtype  : 'patternfield',
         *
         *         config : {
         *             component : {
         *                 pattern : '[0-9]*'
         *             }
         *         }
         *     });
         *
         * Even though it extends {@link Ext.field.Text}, it will display the number keyboard.
         *
         * @accessor
         */
        pattern: null,

        /**
         * @cfg {Boolean} [disabled=false] `true` to disable the field.
         *
         * Be aware that conformant with the [HTML specification](http://www.w3.org/TR/html401/interact/forms.html),
         * disabled Fields will not be {@link Ext.form.Panel#method-submit submitted}.
         * @accessor
         */

        /**
         * @cfg {Mixed} startValue
         * The value that the Field had at the time it was last focused. This is the value that is passed
         * to the {@link Ext.field.Text#change} event which is fired if the value has been changed when the Field is blurred.
         *
         * __This will be `undefined` until the Field has been visited.__ Compare {@link #originalValue}.
         * @accessor
         */
        startValue: false,

        /**
         * @cfg {Boolean} fastFocus
         *
         * Enable Fast Input Focusing on iOS, using this workaround will stop some touchstart events in order to prevent
         * delayed focus issues.
         *
         * @accessor
         */
        fastFocus: true
    },

    /**
     * @cfg {String/Number} originalValue The original value when the input is rendered.
     * @private
     */

    // @private
    getTemplate: function() {
        var items = [
            {
                reference: 'input',
                tag: this.tag
            },
            {
                reference: 'mask',
                classList: [this.config.maskCls]
            },
            {
                reference: 'clearIcon',
                cls: 'x-clear-icon'
            }
        ];

        return items;
    },

    initElement: function() {
        var me = this;

        me.callParent();

        me.input.on({
            scope: me,

            keyup: 'onKeyUp',
            keydown: 'onKeyDown',
            focus: 'onFocus',
            blur: 'onBlur',
            input: 'onInput',
            paste: 'onPaste',
            tap: 'onInputTap'
        });


        // Stock android has a delayed mousedown event that is dispatched
        // this prevents the mousedown from focus's an input when not intended (click a message box button or picker button that lays over an input)
        // we then force focus on touchend.
        if(Ext.browser.is.AndroidStock) {
            me.input.dom.addEventListener("mousedown", function(e) {
                if(document.activeElement != e.target) {
                    e.preventDefault();
                }
            } );
            me.input.dom.addEventListener("touchend", function() { me.focus(); });
        }

        me.mask.on({
            scope: me,
            tap: 'onMaskTap'
        });

        if (me.clearIcon) {
            me.clearIcon.on({
                tap: 'onClearIconTap',
                touchstart: 'onClearIconPress',
                touchend: 'onClearIconRelease',
                scope: me
            });
        }

        // Hack for IE10. Seems like keyup event is not fired for 'enter' keyboard button, so we use keypress event instead to handle enter.
        if(Ext.browser.is.ie && Ext.browser.version.major >=10){
            me.input.on({
                scope: me,
                keypress: 'onKeyPress'
            });
        }
    },

    updateFastFocus: function(newValue) {
       // This is used to prevent 300ms delayed focus bug on iOS
       if (newValue) {
           if (this.getFastFocus() && Ext.os.is.iOS) {
               this.input.on({
                   scope: this,
                   touchstart: "onTouchStart"
               });
           }
       } else {
           this.input.un({
               scope: this,
               touchstart: "onTouchStart"
           });
       }
    },

    /**
     * Manual Max Length processing is required for the stock "Browser" on Android
     * @private
     * @return {Boolean} 'true' if non-chrome browser is detected on Android
     */
    useManualMaxLength: function() {
        return Boolean((Ext.os.is.Android && !Ext.browser.is.Chrome));
    },

    applyUseMask: function(useMask) {
        if (useMask === 'auto') {
            useMask = Ext.os.is.iOS && Ext.os.version.lt('5');
        }

        return Boolean(useMask);
    },

    /**
     * Updates the useMask configuration
     */
    updateUseMask: function(newUseMask) {
        this.mask[newUseMask ? 'show' : 'hide']();
    },

    updatePattern : function (pattern) {
        this.updateFieldAttribute('pattern', pattern);
    },

    /**
     * Helper method to update a specified attribute on the `fieldEl`, or remove the attribute all together.
     * @private
     */
    updateFieldAttribute: function(attribute, newValue) {
        var input = this.input;

        if (!Ext.isEmpty(newValue, true)) {
            input.dom.setAttribute(attribute, newValue);
        } else {
            input.dom.removeAttribute(attribute);
        }
    },

    /**
     * Updates the {@link #cls} configuration.
     */
    updateCls: function(newCls, oldCls) {
        this.input.addCls(Ext.baseCSSPrefix + 'input-el');
        this.input.replaceCls(oldCls, newCls);
    },

    /**
     * Updates the type attribute with the {@link #type} configuration.
     * @private
     */
    updateType: function(newType, oldType) {
        var prefix = Ext.baseCSSPrefix + 'input-';

        this.input.replaceCls(prefix + oldType, prefix + newType);
        this.updateFieldAttribute('type', newType);
    },

    /**
     * Updates the name attribute with the {@link #name} configuration.
     * @private
     */
    updateName: function(newName) {
        this.updateFieldAttribute('name', newName);
    },

    /**
     * Returns the field data value.
     * @return {Mixed} value The field value.
     */
    getValue: function() {
        var input = this.input;

        if (input) {
            this._value = input.dom.value;
        }

        return this._value;
    },

    // @private
    applyValue: function(value) {
        return (Ext.isEmpty(value)) ? '' : value;
    },

    /**
     * Updates the {@link #value} configuration.
     * @private
     */
    updateValue: function(newValue) {
        var input = this.input;

        if (input) {
            input.dom.value = newValue;
        }
    },

    setValue: function(newValue) {
        var oldValue = this._value;

        this.updateValue(this.applyValue(newValue));

        newValue = this.getValue();

        if (String(newValue) != String(oldValue) && this.initialized) {
            this.onChange(this, newValue, oldValue);
        }

        return this;
    },

    //<debug>
    // @private
    applyTabIndex: function(tabIndex) {
        if (tabIndex !== null && typeof tabIndex != 'number') {
            throw new Error("Ext.field.Field: [applyTabIndex] trying to pass a value which is not a number");
        }
        return tabIndex;
    },
    //</debug>

    /**
     * Updates the tabIndex attribute with the {@link #tabIndex} configuration
     * @private
     */
    updateTabIndex: function(newTabIndex) {
        this.updateFieldAttribute('tabIndex', newTabIndex);
    },

    // @private
    testAutoFn: function(value) {
        return [true, 'on'].indexOf(value) !== -1;
    },

    //<debug>
    applyMaxLength: function(maxLength) {
        if (maxLength !== null && typeof maxLength != 'number') {
            throw new Error("Ext.field.Text: [applyMaxLength] trying to pass a value which is not a number");
        }
        return maxLength;
    },
    //</debug>

    /**
     * Updates the `maxlength` attribute with the {@link #maxLength} configuration.
     * @private
     */
    updateMaxLength: function(newMaxLength) {
        if (!this.useManualMaxLength()) {
            this.updateFieldAttribute('maxlength', newMaxLength);
        }
    },

    /**
     * Updates the `placeholder` attribute with the {@link #placeHolder} configuration.
     * @private
     */
    updatePlaceHolder: function(newPlaceHolder) {
        this.updateFieldAttribute('placeholder', newPlaceHolder);
    },

    // @private
    applyAutoComplete: function(autoComplete) {
        return this.testAutoFn(autoComplete);
    },

    /**
     * Updates the `autocomplete` attribute with the {@link #autoComplete} configuration.
     * @private
     */
    updateAutoComplete: function(newAutoComplete) {
        var value = newAutoComplete ? 'on' : 'off';
        this.updateFieldAttribute('autocomplete', value);
    },

    // @private
    applyAutoCapitalize: function(autoCapitalize) {
        return this.testAutoFn(autoCapitalize);
    },

    /**
     * Updates the `autocapitalize` attribute with the {@link #autoCapitalize} configuration.
     * @private
     */
    updateAutoCapitalize: function(newAutoCapitalize) {
        var value = newAutoCapitalize ? 'on' : 'off';
        this.updateFieldAttribute('autocapitalize', value);
    },

    // @private
    applyAutoCorrect: function(autoCorrect) {
        return this.testAutoFn(autoCorrect);
    },

    /**
     * Updates the `autocorrect` attribute with the {@link #autoCorrect} configuration.
     * @private
     */
    updateAutoCorrect: function(newAutoCorrect) {
        var value = newAutoCorrect ? 'on' : 'off';
        this.updateFieldAttribute('autocorrect', value);
    },

    /**
     * Updates the `min` attribute with the {@link #minValue} configuration.
     * @private
     */
    updateMinValue: function(newMinValue) {
        this.updateFieldAttribute('min', newMinValue);
    },

    /**
     * Updates the `max` attribute with the {@link #maxValue} configuration.
     * @private
     */
    updateMaxValue: function(newMaxValue) {
        this.updateFieldAttribute('max', newMaxValue);
    },

    /**
     * Updates the `step` attribute with the {@link #stepValue} configuration
     * @private
     */
    updateStepValue: function(newStepValue) {
        this.updateFieldAttribute('step', newStepValue);
    },

    // @private
    checkedRe: /^(true|1|on)/i,

    /**
     * Returns the `checked` value of this field
     * @return {Mixed} value The field value
     */
    getChecked: function() {
        var el = this.input,
            checked;

        if (el) {
            checked = el.dom.checked;
            this._checked = checked;
        }

        return checked;
    },

    // @private
    applyChecked: function(checked) {
        return !!this.checkedRe.test(String(checked));
    },

    setChecked: function(newChecked) {
        this.updateChecked(this.applyChecked(newChecked));
        this._checked = newChecked;
    },

    /**
     * Updates the `autocorrect` attribute with the {@link #autoCorrect} configuration
     * @private
     */
    updateChecked: function(newChecked) {
        this.input.dom.checked = newChecked;
    },

    /**
     * Updates the `readonly` attribute with the {@link #readOnly} configuration
     * @private
     */
    updateReadOnly: function(readOnly) {
        this.updateFieldAttribute('readonly', readOnly ? true : null);
    },

    //<debug>
    // @private
    applyMaxRows: function(maxRows) {
        if (maxRows !== null && typeof maxRows !== 'number') {
            throw new Error("Ext.field.Input: [applyMaxRows] trying to pass a value which is not a number");
        }

        return maxRows;
    },
    //</debug>

    updateMaxRows: function(newRows) {
        this.updateFieldAttribute('rows', newRows);
    },

    doSetDisabled: function(disabled) {
        this.callParent(arguments);

        if (Ext.browser.is.Safari && !Ext.os.is.BlackBerry) {
            this.input.dom.tabIndex = (disabled) ? -1 : 0;
        }

        this.input.dom.disabled = (Ext.browser.is.Safari && !Ext.os.is.BlackBerry) ? false : disabled;

        if (!disabled) {
            this.blur();
        }
    },

    /**
     * Returns `true` if the value of this Field has been changed from its original value.
     * Will return `false` if the field is disabled or has not been rendered yet.
     * @return {Boolean}
     */
    isDirty: function() {
        if (this.getDisabled()) {
            return false;
        }

        return String(this.getValue()) !== String(this.originalValue);
    },

    /**
     * Resets the current field value to the original value.
     */
    reset: function() {
        this.setValue(this.originalValue);
    },

    // @private
    onInputTap: function(e) {
        this.fireAction('inputtap', [this, e], 'doInputTap');
    },

    // @private
    doInputTap: function(me, e) {
        if (me.getDisabled()) {
            return false;
        }

        // Fast focus switching
        if (this.getFastFocus() && Ext.os.is.iOS) {
            me.focus();
        }
    },

    // @private
    onMaskTap: function(e) {
        this.fireAction('masktap', [this, e], 'doMaskTap');
    },

    // @private
    doMaskTap: function(me, e) {
        if (me.getDisabled()) {
            return false;
        }

        me.focus();
    },

    // @private
    showMask: function() {
        if (this.getUseMask()) {
            this.mask.setStyle('display', 'block');
        }
    },

    // @private
    hideMask: function() {
        if (this.getUseMask()) {
            this.mask.setStyle('display', 'none');
        }
    },

    /**
     * Attempts to set the field as the active input focus.
     * @return {Ext.field.Input} this
     */
    focus: function() {
        var me = this,
            el = me.input;

        if (el && el.dom.focus) {
            el.dom.focus();
        }
        return me;
    },

    /**
     * Attempts to forcefully blur input focus for the field.
     * @return {Ext.field.Input} this
     * @chainable
     */
    blur: function() {
        var me = this,
            el = this.input;

        if (el && el.dom.blur) {
            el.dom.blur();
        }
        return me;
    },

    /**
     * Attempts to forcefully select all the contents of the input field.
     * @return {Ext.field.Input} this
     * @chainable
     */
    select: function() {
        var me = this,
            el = me.input;

        if (el && el.dom.setSelectionRange) {
            el.dom.setSelectionRange(0, 9999);
        }
        return me;
    },

    onFocus: function(e) {
        this.fireAction('focus', [e], 'doFocus');
    },

    // @private
    doFocus: function(e) {
        var me = this;

        me.hideMask();

        if (!me.getIsFocused()) {
            me.setStartValue(me.getValue());
        }
        me.setIsFocused(true);
    },

    onTouchStart: function(e) {
        // This will prevent 300ms delayed focus from occurring on iOS
        if(document.activeElement != e.target) {
            e.preventDefault();
        }
    },

    onBlur: function(e) {
        this.fireAction('blur', [e], 'doBlur');
    },

    // @private
    doBlur: function(e) {
        var me = this,
            value = me.getValue(),
            startValue = me.getStartValue();

        me.showMask();

        me.setIsFocused(false);

        if (String(value) != String(startValue)) {
            me.onChange(me, value, startValue);
        }

    },

    // @private
    onClearIconTap: function(e) {
        this.fireEvent('clearicontap', this, e);

        //focus the field after cleartap happens, but only on android.
        //this is to stop the keyboard from hiding. TOUCH-2064
        if (Ext.os.is.Android) {
            this.focus();
        }
    },

    onClearIconPress: function() {
        this.clearIcon.addCls(Ext.baseCSSPrefix + 'pressing');
    },

    onClearIconRelease: function() {
        this.clearIcon.removeCls(Ext.baseCSSPrefix + 'pressing');
    },

    onClick: function(e) {
        this.fireEvent('click', e);
    },

    onChange: function(me, value, startValue) {
        if (this.useManualMaxLength()) {
            this.trimValueToMaxLength();
        }
        this.fireEvent('change', me, value, startValue);
    },

    onPaste: function(e) {
        if (this.useManualMaxLength()) {
            this.trimValueToMaxLength();
        }
        this.fireEvent('paste', e);
    },

    onKeyUp: function(e) {
        if (this.useManualMaxLength()) {
            this.trimValueToMaxLength();
        }
        this.fireEvent('keyup', e);
    },

    onKeyDown: function() {
        // tell the class to ignore the input event. this happens when we want to listen to the field change
        // when the input autocompletes
        this.ignoreInput = true;
    },

    onInput: function(e) {
        var me = this;

        // if we should ignore input, stop now.
        if (me.ignoreInput) {
            me.ignoreInput = false;
            return;
        }

        // set a timeout for 10ms to check if we want to stop the input event.
        // if not, then continue with the event (keyup)
        setTimeout(function() {
            if (!me.ignoreInput) {
                me.fireEvent('keyup', e);
                me.ignoreInput = false;
            }
        }, 10);
    },

    // Hack for IE10 mobile. Handle pressing 'enter' button and fire keyup event in this case.
    onKeyPress: function(e) {
        if(e.browserEvent.keyCode == 13){
            this.fireEvent('keyup', e);
        }
    },

    onMouseDown: function(e) {
        this.fireEvent('mousedown', e);
    },

    trimValueToMaxLength: function() {
        var maxLength = this.getMaxLength();
        if (maxLength) {
            var value = this.getValue();
            if (value.length > this.getMaxLength()) {
                this.setValue(value.slice(0, maxLength));
            }
        }
    }
});
