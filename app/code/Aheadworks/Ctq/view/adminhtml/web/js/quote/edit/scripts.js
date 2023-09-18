/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'mage/template',
    'text!Aheadworks_Ctq/templates/order/edit/shipping/method.html',
    'mage/translate',
    'prototype',
    'Magento_Catalog/catalog/product/composite/configure',
    'Magento_Ui/js/lib/view/utils/async'
], function (jQuery, confirm, alert, template, shippingTemplate) {

    //todo future refactoring
    window.AwCtqQuote = new Class.create();

    AwCtqQuote.prototype = {
        initialize : function(data){
            if(!data) data = {};
            this.loadBaseUrl    = false;
            this.customerId     = data.customer_id ? data.customer_id : null;
            this.storeId        = data.store_id ? data.store_id : null;
            this.quoteId        = data.quote_id ? data.quote_id : null;
            this.currencyId     = false;
            this.currencySymbol = data.currency_symbol ? data.currency_symbol : '';
            this.addresses      = data.addresses ? data.addresses : $H({});
            this.isEditQuote    = data.is_quote_can_be_edited !== undefined ? data.is_quote_can_be_edited : true;
            this.shippingAsBilling = false;
            this.gridProducts   = $H({});
            this.gridProductsGift = $H({});
            this.billingAddressContainer = '';
            this.shippingAddressContainer = '';
            this.isShippingMethodReseted = data.shipping_method_reseted ? data.shipping_method_reseted : false;
            this.negotiatedDiscountType = data.negotiated_discount_type ? data.negotiated_discount_type : null;
            this.negotiatedDiscountValue = data.negotiated_discount_value ? data.negotiated_discount_value : null;
            this.overlayData = $H({});
            this.giftMessageDataChanged = false;
            this.productConfigureAddFields = {};
            this.productPriceBase = {};
            this.collectElementsValue = true;
            this.isOnlyVirtualProduct = false;
            this.excludedPaymentMethods = [];
            this.summarizePrice = true;
            this.discountTypeSelector = 'input[name="quote[negotiated_discount_type]"]:checked';
            this.discountValueSelector = 'input[name="quote[negotiated_discount_value]"]:enabled';
            this.shippingTemplate = template(shippingTemplate, {
                data: {
                    title: jQuery.mage.__('Shipping Method'),
                    linkText: jQuery.mage.__('Get shipping methods and rates')
                }
            });
            jQuery.async('#quote-items', (function(){
                this.dataArea = new OrderFormArea('data', $(this.getAreaId('data')), this);
                this.itemsArea = Object.extend(new OrderFormArea('items', $(this.getAreaId('items')), this), {
                    addControlButton: function(button){
                        var controlButtonArea = $(this.node).select('.actions')[0];
                        if (typeof controlButtonArea != 'undefined') {
                            var buttons = controlButtonArea.childElements();
                            for (var i = 0; i < buttons.length; i++) {
                                if (buttons[i].innerHTML.include(button.getLabel())) {
                                    return;
                                }
                            }
                            button.insertIn(controlButtonArea, 'top');
                        }
                    }
                });

                var searchButtonId = 'add_products',
                    searchButton = new ControlButton(jQuery.mage.__('Add Products'), searchButtonId),
                    searchAreaId = this.getAreaId('search');
                searchButton.onClick = function() {
                    $(searchAreaId).show();
                    var el = this;
                    window.setTimeout(function () {
                        el.remove();
                    }, 10);
                };

                if (this.isEditQuote) {
                    if (jQuery('#' + this.getAreaId('items')).is(':visible')) {
                        this.dataArea.onLoad = this.dataArea.onLoad.wrap(function (proceed) {
                            proceed();
                            this._parent.itemsArea.setNode($(this._parent.getAreaId('items')));
                            this._parent.itemsArea.onLoad();
                        });

                        this.itemsArea.onLoad = this.itemsArea.onLoad.wrap(function (proceed) {
                            proceed();
                            if ($(searchAreaId) && !$(searchAreaId).visible() && !$(searchButtonId)) {
                                this.addControlButton(searchButton);
                            }
                        });
                        this.areasLoaded();
                        this.itemsArea.onLoad();
                    }
                }
            }).bind(this));

            $(this.getAreaId('totals')).callback = 'negotiationSectionUpdated';

            jQuery('#edit_form')
                .on('submitOrder', function(){
                    jQuery(this).trigger('realOrder');
                })
                .on('realOrder', this._realSubmit.bind(this));
        },

        areasLoaded: function(){
        },

        itemsLoaded: function(){
        },

        dataLoaded: function(){
            this.dataShow();
            $(this.getAreaId('totals')).callback = 'negotiationSectionUpdated';
            jQuery('#' + this.getAreaId('data')).trigger('contentUpdated');
            jQuery('#' + this.getAreaId('data')).applyBindings();
        },

        setLoadBaseUrl : function(url){
            this.loadBaseUrl = url;
        },

        setAddresses : function(addresses){
            this.addresses = addresses;
        },

        addExcludedPaymentMethod : function(method){
            this.excludedPaymentMethods.push(method);
        },

        setCustomerId : function(id){
            this.customerId = id;
            this.loadArea('header', true);
            $(this.getAreaId('header')).callback = 'setCustomerAfter';
            $('save_quote_button').show();
        },

        setCustomerAfter : function () {
            this.customerSelectorHide();
            $(this.getAreaId('data')).callback = 'dataLoaded';
            this.loadArea(['data'], true);
            /*if (this.storeId) {
                $(this.getAreaId('data')).callback = 'dataLoaded';
                this.loadArea(['data'], true);
            }
            else {
                this.storeSelectorShow();
            }*/
        },

        negotiationSectionUpdated : function () {
            jQuery('#' + this.getAreaId('totals')).trigger('contentUpdated');
        },

        setStoreId : function(id){
            this.storeId = id;
            this.storeSelectorHide();
            this.sidebarShow();
            //this.loadArea(['header', 'sidebar','data'], true);
            this.dataShow();
            this.loadArea(['header', 'data'], true);
        },

        setCurrencyId : function(id){
            this.currencyId = id;
            //this.loadArea(['sidebar', 'data'], true);
            this.loadArea(['data'], true);
        },

        setCurrencySymbol : function(symbol){
            this.currencySymbol = symbol;
        },

        /**
         * Select shipping address from drop down selector
         *
         * @param {Object} el
         * @param {Object} container
         */
        selectAddress : function(el, container) {
            var addressId = el.value,
                data;

            if (addressId.length === 0) {
                addressId = '0';
            }
            if (this.addresses[addressId]) {
                this.fillAddressFields(container, this.addresses[addressId]);
            } else {
                this.fillAddressFields(container, {});
            }
            data = this.serializeData(container);
            data[el.name] = addressId;

            if (this.isShippingField(container) && !this.isShippingMethodReseted) {
                this.resetShippingMethod(data);
            } else {
                this.saveData(data);
            }
        },

        /**
         * Checks if the field belongs to the shipping address.
         *
         * @param {String} fieldId
         * @return {Boolean}
         */
        isShippingField: function (fieldId) {
            return fieldId.include('shipping');
        },

        /**
         * Binds events on container form fields.
         *
         * @param {String} container
         */
        bindAddressFields: function (container) {
            var fields = $(container).select('input', 'select', 'textarea'),
                i;

            for (i = 0; i < fields.length; i++) {
                jQuery(fields[i]).change(this.changeAddressField.bind(this));
            }
        },

        /**
         * Triggers on each form's element changes.
         *
         * @param {Event} event
         */
        changeAddressField: function (event) {
            var field = Event.element(event),
                re = /[^\[]*\[([^\]]*)_address\]\[([^\]]*)\](\[(\d)\])?/,
                matchRes = field.name.match(re),
                name,
                data;

            if (!matchRes) {
                return;
            }

            name = matchRes[2];

            data = this.serializeData(this.shippingAddressContainer);
            data = data.toObject();

            data['reset_shipping'] = true;
            data['quote[shipping_address][customer_address_id]'] = null;

            if (name === 'customer_address_id') {
                data['quote[shipping_address][customer_address_id]'] =
                    $('quote-shipping_address_customer_address_id').value;
            }

            if (data['reset_shipping']) {
                this.resetShippingMethod();
            } else {
                this.saveData(data);

                if (name === 'country_id' || name === 'customer_address_id') {
                    this.loadArea(['shipping_method', 'totals', 'order_totals', 'items'], true, data);
                }
            }
        },

        fillAddressFields: function(container, data) {
            var fields = $(container).select('input', 'select', 'textarea');
            var re = /[^\[]*\[[^\]]*\]\[([^\]]*)\](\[(\d)\])?/;

            for (var i=0; i<fields.length; i++){
                if (fields[i].tagName.toLowerCase() == 'input' && fields[i].type.toLowerCase() == 'file') {
                    continue;
                }
                var matchRes = fields[i].name.match(re);
                if (matchRes === null) {
                    continue;
                }
                var name = matchRes[1];
                var index = matchRes[3];

                if (index){
                    // multiply line
                    if (data[name]){
                        var values = data[name].split("\n");
                        fields[i].value = values[index] ? values[index] : '';
                    } else {
                        fields[i].value = '';
                    }
                } else if (fields[i].tagName.toLowerCase() == 'select' && fields[i].multiple) {
                    // multiselect
                    if (data[name]) {
                        values = [''];
                        if (Object.isString(data[name])) {
                            values = data[name].split(',');
                        } else if (Object.isArray(data[name])) {
                            values = data[name];
                        }
                        fields[i].setValue(values);
                    }
                } else {
                    fields[i].setValue(data[name] ? data[name] : '');
                }

                if (fields[i].changeUpdater) {
                    fields[i].changeUpdater();
                }

                if (name == 'region' && data['region_id'] && !data['region']){
                    fields[i].value = data['region_id'];
                }

                jQuery(fields[i]).trigger('change');
            }
        },

        /**
         * Loads shipping options according to address data.
         *
         * @return {Boolean}
         */
        loadShippingRates: function () {
            var addressContainer = 'shippingAddressContainer',
                data = this.serializeData(this[addressContainer]).toObject();

            data['collect_shipping_rates'] = 1;
            this.isShippingMethodReseted = false;
            this.loadArea(['shipping_method', 'totals', 'order_totals'], true, data);

            return false;
        },

        /**
         * Replace shipping method area.
         */
        resetShippingMethod: function () {
            if (!this.isOnlyVirtualProduct && this.isEditQuote) {
                $(this.getAreaId('shipping_method')).update(this.shippingTemplate);
            }
        },

        /**
         * Set shipping method
         *
         * @param {String} method
         */
        setShippingMethod: function(method) {
            var data = {};

            data['shipping[shipping_method]'] = method;
            this.loadArea(['shipping_method', 'totals', 'order_totals'], true, data);
        },

        /**
         * Set negotiation discount
         *
         * @param {String} type
         * @param {String} price
         */
        setNegotiationDiscount: function(type, price) {
            this.negotiatedDiscountType = type;
            this.negotiatedDiscountValue = price;

            if (this.isEditQuote) {
                this.loadArea(['items', 'totals', 'order_totals'], true, {
                    negotiated_discount_type: type,
                    negotiated_discount_value: price,
                    reset_calculation: true
                });
            }
        },

        productGridShow : function(buttonElement){
            this.productGridShowButton = buttonElement;
            Element.hide(buttonElement);
            this.showArea('search');
        },

        productGridRowInit : function(grid, row){
            var checkbox = $(row).select('.checkbox')[0];
            var inputs = $(row).select('.input-text');
            if (checkbox && inputs.length > 0) {
                checkbox.inputElements = inputs;
                for (var i = 0; i < inputs.length; i++) {
                    var input = inputs[i];
                    input.checkboxElement = checkbox;

                    var product = this.gridProducts.get(checkbox.value);
                    if (product) {
                        var defaultValue = product[input.name];
                        if (defaultValue) {
                            if (input.name == 'giftmessage') {
                                input.checked = true;
                            } else {
                                input.value = defaultValue;
                            }
                        }
                    }

                    input.disabled = !checkbox.checked || input.hasClassName('input-inactive');

                    Event.observe(input,'keyup', this.productGridRowInputChange.bind(this));
                    Event.observe(input,'change',this.productGridRowInputChange.bind(this));
                }
            }
        },

        productGridRowInputChange : function(event){
            var element = Event.element(event);
            if (element && element.checkboxElement && element.checkboxElement.checked){
                if (element.name!='giftmessage' || element.checked) {
                    this.gridProducts.get(element.checkboxElement.value)[element.name] = element.value;
                } else if (element.name=='giftmessage' && this.gridProducts.get(element.checkboxElement.value)[element.name]) {
                    delete(this.gridProducts.get(element.checkboxElement.value)[element.name]);
                }
            }
        },

        productGridRowClick : function(grid, event){
            var trElement = Event.findElement(event, 'tr');
            var qtyElement = trElement.select('input[name="qty"]')[0];
            var eventElement = Event.element(event);
            var isInputCheckbox = eventElement.tagName == 'INPUT' && eventElement.type == 'checkbox';
            var isInputQty = eventElement.tagName == 'INPUT' && eventElement.name == 'qty';
            if (trElement && !isInputQty) {
                var checkbox = Element.select(trElement, 'input[type="checkbox"]')[0];
                var confLink = Element.select(trElement, 'a')[0];
                var priceColl = Element.select(trElement, '.price')[0];
                if (checkbox) {
                    // processing non composite product
                    if (confLink.readAttribute('disabled')) {
                        var checked = isInputCheckbox ? checkbox.checked : !checkbox.checked;
                        grid.setCheckboxChecked(checkbox, checked);
                        // processing composite product
                    } else if (isInputCheckbox && !checkbox.checked) {
                        grid.setCheckboxChecked(checkbox, false);
                        // processing composite product
                    } else if (!isInputCheckbox || (isInputCheckbox && checkbox.checked)) {
                        var listType = confLink.readAttribute('list_type');
                        var productId = confLink.readAttribute('product_id');
                        if (typeof this.productPriceBase[productId] == 'undefined') {
                            var priceBase = priceColl.innerHTML.match(/.*?([\d,]+\.?\d*)/);
                            if (!priceBase) {
                                this.productPriceBase[productId] = 0;
                            } else {
                                this.productPriceBase[productId] = parseFloat(priceBase[1].replace(/,/g,''));
                            }
                        }
                        productConfigure.setConfirmCallback(listType, function() {
                            // sync qty of popup and qty of grid
                            var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
                            if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                                qtyElement.value = confirmedCurrentQty.value;
                            }
                            // calc and set product price
                            var productPrice = this._calcProductPrice();
                            if (this._isSummarizePrice()) {
                                productPrice += this.productPriceBase[productId];
                            }
                            productPrice = parseFloat(Math.round(productPrice + "e+2") + "e-2");
                            priceColl.innerHTML = this.currencySymbol + productPrice.toFixed(2);
                            // and set checkbox checked
                            grid.setCheckboxChecked(checkbox, true);
                        }.bind(this));
                        productConfigure.setCancelCallback(listType, function() {
                            if (!$(productConfigure.confirmedCurrentId) || !$(productConfigure.confirmedCurrentId).innerHTML) {
                                grid.setCheckboxChecked(checkbox, false);
                            }
                        });
                        productConfigure.setShowWindowCallback(listType, function() {
                            // sync qty of grid and qty of popup
                            var formCurrentQty = productConfigure.getCurrentFormQtyElement();
                            if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                                formCurrentQty.value = qtyElement.value;
                            }
                        }.bind(this));
                        productConfigure.showItemConfiguration(listType, productId);
                    }
                }
            }
        },

        /**
         * Is need to summarize price
         */
        _isSummarizePrice: function(elm) {
            if (elm && elm.hasAttribute('summarizePrice')) {
                this.summarizePrice = parseInt(elm.readAttribute('summarizePrice'));
            }
            return this.summarizePrice;
        },
        /**
         * Calc product price through its options
         */
        _calcProductPrice: function () {
            var productPrice = 0;
            var getPriceFields = function (elms) {
                var productPrice = 0;
                var getPrice = function (elm) {
                    var optQty = 1;
                    if (elm.hasAttribute('qtyId')) {
                        if (!$(elm.getAttribute('qtyId')).value) {
                            return 0;
                        } else {
                            optQty = parseFloat($(elm.getAttribute('qtyId')).value);
                        }
                    }
                    if (elm.hasAttribute('price') && !elm.disabled) {
                        return parseFloat(elm.readAttribute('price')) * optQty;
                    }
                    return 0;
                };
                for(var i = 0; i < elms.length; i++) {
                    if (elms[i].type == 'select-one' || elms[i].type == 'select-multiple') {
                        for(var ii = 0; ii < elms[i].options.length; ii++) {
                            if (elms[i].options[ii].selected) {
                                if (this._isSummarizePrice(elms[i].options[ii])) {
                                    productPrice += getPrice(elms[i].options[ii]);
                                } else {
                                    productPrice = getPrice(elms[i].options[ii]);
                                }
                            }
                        }
                    }
                    else if (((elms[i].type == 'checkbox' || elms[i].type == 'radio') && elms[i].checked)
                        || ((elms[i].type == 'file' || elms[i].type == 'text' || elms[i].type == 'textarea' || elms[i].type == 'hidden')
                            && Form.Element.getValue(elms[i]))
                    ) {
                        if (this._isSummarizePrice(elms[i])) {
                            productPrice += getPrice(elms[i]);
                        } else {
                            productPrice = getPrice(elms[i]);
                        }
                    }
                }
                return productPrice;
            }.bind(this);
            productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('input'));
            productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('select'));
            productPrice += getPriceFields($(productConfigure.confirmedCurrentId).getElementsByTagName('textarea'));
            return productPrice;
        },

        productGridCheckboxCheck : function(grid, element, checked){
            if (checked) {
                if(element.inputElements) {
                    this.gridProducts.set(element.value, {});
                    var product = this.gridProducts.get(element.value);
                    for (var i = 0; i < element.inputElements.length; i++) {
                        var input = element.inputElements[i];
                        if (!input.hasClassName('input-inactive')) {
                            input.disabled = false;
                            if (input.name == 'qty' && !input.value) {
                                input.value = 1;
                            }
                        }

                        if (input.checked || input.name != 'giftmessage') {
                            product[input.name] = input.value;
                        } else if (product[input.name]) {
                            delete(product[input.name]);
                        }
                    }
                }
            } else {
                if(element.inputElements){
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = true;
                    }
                }
                this.gridProducts.unset(element.value);
            }
            grid.reloadParams = {'products[]':this.gridProducts.keys()};
        },

        /**
         * Add selected products to quote
         */
        productGridAddSelected : function() {
            var area = ['items', 'totals', 'order_totals', 'shipping_method'],
                products = this.gridProducts.toObject(),
                fieldsPrepare = {},
                itemsFilter = [];

            if (this.productGridShowButton) {
                Element.show(this.productGridShowButton);
            }

            for (var productId in products) {
                itemsFilter.push(productId);
                var paramKey = 'item[' + productId + ']';
                for (var productParamKey in products[productId]) {
                    paramKey += '[' + productParamKey + ']';
                    fieldsPrepare[paramKey] = products[productId][productParamKey];
                }
            }
            this.productConfigureSubmit('product_to_add', area, fieldsPrepare, itemsFilter);
            productConfigure.clean('quote_items');
            this.hideArea('search');
            this.gridProducts = $H({});
        },

        selectCustomer : function(grid, event){
            var element = Event.findElement(event, 'tr');
            if (element.title){
                this.setCustomerId(element.title);
            }
        },

        customerSelectorHide : function(){
            this.hideArea('customer-selector');
        },

        customerSelectorShow : function(){
            this.showArea('customer-selector');
        },

        storeSelectorHide : function(){
            this.hideArea('store-selector');
        },

        storeSelectorShow : function(){
            this.showArea('store-selector');
        },

        dataHide : function(){
            this.hideArea('data');
        },

        dataShow : function(){
            if ($('submit_order_top_button')) {
                $('submit_order_top_button').show();
            }
            this.showArea('data');
        },

        itemsUpdate: function() {
            var area = ['items', 'totals', 'order_totals', 'shipping_method'],
                info = $('quote-items_grid').select('input', 'select', 'textarea'),
                fieldsPrepare = {update_items: 1};

            for(var i = 0; i < info.length; i++) {
                if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                    fieldsPrepare[info[i].name] = info[i].getValue();
                }
            }
            fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
            this.productConfigureSubmit('quote_items', area, fieldsPrepare);
            this.orderItemChanged = false;
        },

        itemsOnchangeBind: function() {
            var elems = $('quote-items_grid').select('input', 'select', 'textarea');

            for(var i = 0; i < elems.length; i++) {
                if (!elems[i].bindOnchange) {
                    elems[i].bindOnchange = true;
                    elems[i].observe('change', this.itemChange.bind(this))
                }
            }
        },

        itemChange: function() {
            this.orderItemChanged = true;
        },

        /**
         * Submit batch of configured products
         *
         * @param listType
         * @param area
         * @param fieldsPrepare
         * @param itemsFilter
         */
        productConfigureSubmit : function(listType, area, fieldsPrepare, itemsFilter) {
            // prepare loading areas and build url
            area = this.prepareArea(area);
            this.loadingAreas = area;
            var url = this.loadBaseUrl + 'block/' + area + '?isAjax=true';

            // prepare additional fields
            fieldsPrepare = this.prepareParams(fieldsPrepare);
            fieldsPrepare.reset_shipping = 1;
            fieldsPrepare.json = 1;

            // create fields
            var fields = [];
            for (var name in fieldsPrepare) {
                fields.push(new Element('input', {type: 'hidden', name: name, value: fieldsPrepare[name]}));
            }
            productConfigure.addFields(fields);

            // filter items
            if (itemsFilter) {
                productConfigure.addItemsFilter(listType, itemsFilter);
            }

            // prepare and do submit
            productConfigure.addListType(listType, {urlSubmit: url});
            productConfigure.setOnLoadIFrameCallback(listType, function(response){
                this.loadAreaResponseHandler(response);
            }.bind(this));
            productConfigure.submit(listType);
            // clean
            this.productConfigureAddFields = {};
        },

        /**
         * Show configuration of quote item
         *
         * @param itemId
         */
        showQuoteItemConfiguration: function(itemId){
            var listType = 'quote_items';
            var qtyElement = $('quote-items_grid').select('input[name="item\['+itemId+'\]\[qty\]"]')[0];
            productConfigure.setConfirmCallback(listType, function() {
                // sync qty of popup and qty of grid
                var confirmedCurrentQty = productConfigure.getCurrentConfirmedQtyElement();
                if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                    qtyElement.value = confirmedCurrentQty.value;
                }
                this.productConfigureAddFields['item['+itemId+'][configured]'] = 1;
                this.itemsUpdate();

            }.bind(this));
            productConfigure.setShowWindowCallback(listType, function() {
                // sync qty of grid and qty of popup
                var formCurrentQty = productConfigure.getCurrentFormQtyElement();
                if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                    formCurrentQty.value = qtyElement.value;
                }
            }.bind(this));
            productConfigure.showItemConfiguration(listType, itemId);
        },

        accountFieldsBind : function(container){
            if($(container)){
                var fields = $(container).select('input', 'select', 'textarea');
                for(var i=0; i<fields.length; i++){
                    if(fields[i].id == 'group_id'){
                        fields[i].observe('change', this.accountGroupChange.bind(this))
                    }
                    else{
                        fields[i].observe('change', this.accountFieldChange.bind(this))
                    }
                }
            }
        },

        accountFieldChange : function(){
            this.saveData(this.serializeData('order-form_account'));
        },

        commentFieldsBind : function(container){
            if($(container)){
                var fields = $(container).select('input', 'textarea');
                for(var i=0; i<fields.length; i++)
                    fields[i].observe('change', this.commentFieldChange.bind(this))
            }
        },

        commentFieldChange : function(){
            this.saveData(this.serializeData('order-comment'));
        },

        loadArea : function(area, indicator, params){
            var deferred = new jQuery.Deferred();
            var url = this.loadBaseUrl;
            if (area) {
                area = this.prepareArea(area);
                url += 'block/' + area;
            }
            if (indicator === true) indicator = 'html-body';
            params = this.prepareParams(params);
            params.json = true;
            if (!this.loadingAreas) this.loadingAreas = [];
            if (indicator) {
                this.loadingAreas = area;
                new Ajax.Request(url, {
                    parameters:params,
                    loaderArea: indicator,
                    onSuccess: function(transport) {
                        var response = transport.responseText.evalJSON();
                        this.loadAreaResponseHandler(response);
                        deferred.resolve();
                    }.bind(this)
                });
            }
            else {
                new Ajax.Request(url, {
                    parameters:params,
                    loaderArea: indicator,
                    onSuccess: function(transport) {
                        deferred.resolve();
                    }
                });
            }
            if (typeof productConfigure != 'undefined' && area instanceof Array && area.indexOf('items') != -1) {
                productConfigure.clean('quote_items');
            }
            return deferred.promise();
        },

        loadAreaResponseHandler : function (response) {
            if (response.error) {
                alert({
                    content: response.message
                });
            }
            if (response.ajaxExpired && response.ajaxRedirect) {
                setLocation(response.ajaxRedirect);
            }
            if (!this.loadingAreas) {
                this.loadingAreas = [];
            }
            if (typeof this.loadingAreas == 'string') {
                this.loadingAreas = [this.loadingAreas];
            }
            if (this.loadingAreas.indexOf('message') == -1) {
                this.loadingAreas.push('message');
            }
            if (response.header) {
                jQuery('.page-actions-inner').attr('data-title', response.header);
            }

            for (var i = 0; i < this.loadingAreas.length; i++) {
                var id = this.loadingAreas[i];
                if ($(this.getAreaId(id))) {
                    if ('message' != id || response[id]) {
                        $(this.getAreaId(id)).update(response[id]);
                    }
                    if ($(this.getAreaId(id)).callback) {
                        this[$(this.getAreaId(id)).callback]();
                    }
                }
            }
        },

        prepareArea : function(area) {
            if (this.giftMessageDataChanged) {
                return area.without('giftmessage');
            }
            return area;
        },

        saveData : function(data){
            this.loadArea(false, false, data);
        },

        showArea : function(area){
            var id = this.getAreaId(area);
            if($(id)) {
                $(id).show();
                this.areaOverlay();
            }
        },

        hideArea : function(area){
            var id = this.getAreaId(area);
            if($(id)) {
                $(id).hide();
                this.areaOverlay();
            }
        },

        areaOverlay : function()
        {
            $H(quote.overlayData).each(function(e){
                e.value.fx();
            });
        },

        getAreaId: function(area) {
            return 'quote-' + area;
        },

        getCurrentDiscountType: function() {
            return jQuery(this.discountTypeSelector).val();
        },

        getCurrentDiscountValue: function() {
            return jQuery(this.discountValueSelector).val();
        },

        prepareParams : function(params){
            if (!params) {
                params = {};
            }

            if (!params.quoteId && this.quoteId) {
                params['quote[quote_id]'] = this.quoteId;
            }
            if (!params.customer_id && this.customerId) {
                params.customer_id = this.customerId;
            }
            if (!params.store_id && this.storeId) {
                params.store_id = this.storeId;
            }
            if (this.getCurrentDiscountType()) {
                params['quote[negotiated_discount_type]'] = this.getCurrentDiscountType();
            }
            if (this.getCurrentDiscountValue()) {
                params['quote[negotiated_discount_value]'] = this.getCurrentDiscountValue();
            }
            if (!params.currency_id) {
                params.currency_id = this.currencyId;
            }
            if (!params.form_key) {
                params.form_key = FORM_KEY;
            }
            return params;
        },

        /**
         * Serializes container form elements data.
         *
         * @param {String} container
         * @return {Object}
         */
        serializeData: function (container) {
            var fields = $(container).select('input', 'select', 'textarea'),
                data = Form.serializeElements(fields, true);

            return $H(data);
        },

        toggleCustomPrice: function(checkbox, elemId, tierBlock) {
            if (checkbox.checked) {
                $(elemId).disabled = false;
                $(elemId).show();
                if($(tierBlock)) $(tierBlock).hide();
            }
            else {
                $(elemId).disabled = true;
                $(elemId).hide();
                if($(tierBlock)) $(tierBlock).show();
            }
        },

        submit: function(config)
        {
            var $editForm = jQuery('#edit_form');

            if ($editForm.valid()) {
                $editForm.trigger('processStart');
                this._realSubmit(config);
            }
        },

        _realSubmit: function (config) {
            var disableAndSave = function(config) {
                disableElements('save');
                jQuery('#edit_form').on('invalid-form.validate', function() {
                    enableElements('save');
                    jQuery('#edit_form').trigger('processStop');
                    jQuery('#edit_form').off('invalid-form.validate');
                });
                if (config.url) {
                    jQuery('#edit_form').attr('action', config.url);
                }
                jQuery('#edit_form').triggerHandler('save');
            }
            if (this.orderItemChanged) {
                var self = this;

                jQuery('#edit_form').trigger('processStop');

                confirm({
                    content: jQuery.mage.__('You have item changes'),
                    actions: {
                        confirm: function() {
                            jQuery('#edit_form').trigger('processStart');
                            disableAndSave(config);
                        },
                        cancel: function() {
                            self.itemsUpdate();
                        }
                    }
                });
            } else {
                disableAndSave(config);
            }
        },

        overlay : function(elId, show, observe) {
            if (typeof(show) == 'undefined') { show = true; }

            var quoteObj = this;
            var obj = this.overlayData.get(elId);
            if (!obj) {
                obj = {
                    show: show,
                    el: elId,
                    quote: quoteObj,
                    fx: function(event) {
                        this.quote.processOverlay(this.el, this.show);
                    }
                };
                obj.bfx = obj.fx.bindAsEventListener(obj);
                this.overlayData.set(elId, obj);
            } else {
                obj.show = show;
                Event.stopObserving(window, 'resize', obj.bfx);
            }

            Event.observe(window, 'resize', obj.bfx);

            this.processOverlay(elId, show);
        },

        processOverlay : function(elId, show) {
            var el = $(elId);

            if (!el) {
                return;
            }

            var parentEl = el.up(1);
            if (show) {
                parentEl.removeClassName('ignore-validate');
            } else {
                parentEl.addClassName('ignore-validate');
            }

            if (Prototype.Browser.IE) {
                parentEl.select('select').each(function (elem) {
                    if (show) {
                        elem.needShowOnSuccess = false;
                        elem.style.visibility = '';
                    } else {
                        elem.style.visibility = 'hidden';
                        elem.needShowOnSuccess = true;
                    }
                });
            }

            parentEl.setStyle({position: 'relative'});
            el.setStyle({
                display: show ? 'none' : ''
            });
        }
    };

    window.OrderFormArea = Class.create();
    OrderFormArea.prototype = {
        _name: null,
        _node: null,
        _parent: null,
        _callbackName: null,

        initialize: function(name, node, parent){
            if(!node)
                return;
            this._name = name;
            this._parent = parent;
            this._callbackName = node.callback;
            if (typeof this._callbackName == 'undefined') {
                this._callbackName = name + 'Loaded';
                node.callback = this._callbackName;
            }
            parent[this._callbackName] = parent[this._callbackName].wrap((function (proceed){
                proceed();
                this.onLoad();
            }).bind(this));

            this.setNode(node);
        },

        setNode: function(node){
            if (!node.callback) {
                node.callback = this._callbackName;
            }
            this.node = node;
        },

        onLoad: function(){
        }
    };

    window.ControlButton = Class.create();

    ControlButton.prototype = {
        _label: '',
        _node: null,

        initialize: function(label, id){
            this._label = label;
            this._node = new Element('button', {
                'class': 'action-secondary action-add',
                'type':  'button'
            });
            if (typeof id !== 'undefined') {
                this._node.setAttribute('id', id)
            }
        },

        onClick: function(){
        },

        insertIn: function(element, position){
            var node = Object.extend(this._node),
                content = {};
            node.observe('click', this.onClick);
            node.update('<span>' + this._label + '</span>');
            content[position] = node;
            Element.insert(element, content);
        },

        getLabel: function(){
            return this._label;
        }
    };
});
