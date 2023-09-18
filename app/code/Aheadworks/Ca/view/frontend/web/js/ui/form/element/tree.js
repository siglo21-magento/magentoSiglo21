/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'awCaJsTree'
], function($, _, Element) {
    "use strict";

    return Element.extend({
        defaults: {
            elementTmpl: 'Aheadworks_Ca/form/element/tree',
            treeConfig: {
                plugins: ['checkbox'],
                core: {
                    data: []
                },
                checkbox: {
                    three_state: false,
                    cascade: '',
                    keep_selected_style: false
                },
            }
        },

        /**
         * Handler function which is supposed to be invoked when
         * jsTree element has been rendered
         *
         * @param {HTMLInputElement} treeElement
         */
        onElementRender: function (treeElement) {
            this._initJsTree(treeElement);
        },

        /**
         * Clear value
         *
         * @returns {Object} Chainable.
         */
        clear: function () {
            this.value([]);

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Tree} Chainable
         */
        setInitialValue: function () {
            if (_.isUndefined(this.value()) && !this.default) {
                this.clear();
            }

            return this._super();
        },

        /**
         * Initializes jsTree plugin on provided input element
         *
         * @param {HTMLInputElement} treeElement
         * @returns {Tree} Chainable
         */
        _initJsTree: function (treeElement) {
            this.$treeElement = $(treeElement);

            _.extend(this.treeConfig, {

            });

            this.$treeElement.jstree(this.treeConfig);
            this.$treeElement.on('loaded.jstree', $.proxy(this._selectTreeNode, this));
            this.$treeElement.on('changed.jstree', $.proxy(this._cascadeTree, this));
            this.$treeElement.on('changed.jstree', $.proxy(this._updateValue, this));

            return this;
        },

        /**
         * Select tree nodes
         *
         * @private
         */
        _selectTreeNode: function () {
            var valueToSelect = this.value();

            this.initTree = true;
            _.each(valueToSelect, function (node) {
                this.$treeElement.jstree(true).select_node(node);
            }, this);
            this.initTree = false;
        },

        /**
         * Cascade tree
         *
         * @param {Object} e
         * @param {Object} data
         * @private
         */
        _cascadeTree: function (e, data) {
            var childNodes,
                node;

            if (!this.initTree && data && data.node && data.action) {
                childNodes = _.toArray(this.$treeElement.jstree('get_children_dom', data.node));

                if (data.action === 'deselect_node') {
                    this.$treeElement.jstree('deselect_node', childNodes);
                } else if (data.action === 'select_node') {
                    node = data.node;

                    do {
                        node = this.$treeElement.jstree('get_parent', node);
                        this.$treeElement.jstree('select_node', node, true);
                    } while (node);
                    this.$treeElement.jstree('select_node', childNodes);
                }
            }
        },

        /**
         * Update value after tree change
         *
         * @private
         */
        _updateValue: function () {
            var selected = this.$treeElement.jstree('get_selected');

            this.value(selected);
        }
    });
});