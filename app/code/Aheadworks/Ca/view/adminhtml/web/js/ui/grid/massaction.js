/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/tree-massactions'
], function (ko, _, Massactions) {
    'use strict';

    return Massactions.extend({
        defaults: {
            submenuTemplate: 'Aheadworks_Ca/ui/grid/massaction/submenu',
            origSubmenuTemplate: 'ui/grid/submenu',
            searchableSubmenuTemplate: 'Aheadworks_Ca/ui/grid/massaction/submenu/searchable',
            resultLimit: 5
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .recursiveObserveActions(this.actions());

            return this;
        },

        /**
         * @inheritdoc
         */
        recursiveObserveActions: function (actions, prefix) {
            _.each(actions, function (action) {
                if (prefix) {
                    action.type = prefix + '.' + action.type;
                }

                if (action.actions) {
                    action.observableActions = ko.observableArray(action.actions.slice(0, action.resultLimit || this.resultLimit));
                    action.visible = ko.observable(false);
                    action.parent = actions;
                    this.recursiveObserveActions(action.actions, action.type);
                }
            }, this);

            return this;
        },

        /**
         * Filter options
         */
        filterOptions: function (parentAction, $component, event) {
            var searchValue = event.target.value,
                resultActions = parentAction.observableActions,
                limit = parentAction.resultLimit || this.resultLimit;

            if (searchValue !== '') {
                resultActions([]);
                _.each(parentAction.actions, function (action) {
                    if (this._match(searchValue, action.label) && resultActions().length <= limit) {
                        resultActions.push(action);
                    }
                }, this);
            } else {
                resultActions(parentAction.actions.slice(0, limit));
            }
        },

        /**
         * Match string value
         *
         * @param {string} needle
         * @param {string} str
         * @returns {Boolean}
         * @private
         */
        _match: function (needle, str) {

            /**
             * Prepare string value to match
             *
             * @param {string} value
             * @returns {string}
             */
            var prepareStr = function (value) {
                    value = value.toLowerCase();
                    return value.replace(/,/g, '');
                },
                needlePrepared = prepareStr(needle),
                strPrepared = prepareStr(str);

            return strPrepared.indexOf(needlePrepared) > -1;
        }
    });
});
