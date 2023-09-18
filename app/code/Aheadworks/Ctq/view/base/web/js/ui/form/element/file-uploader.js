/**
 * Copyright 2020 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/file-uploader'
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            elementToDisable: ''
        },
        lastenter: '',

        /**
         * {@inheritdoc}
         */
        initUploader: function (fileInput) {
            $(this.dropZone).on('dragenter', this.onDragEnter.bind(this));
            $(this.dropZone).on('dragleave', this.onDragLeave.bind(this));
            $(this.dropZone).on('drop', this.onDrop.bind(this));
            this._super();

            return this;
        },

        /**
         * Browse file
         */
        browseFile: function () {
            $('#' + this.uid).click();
        },

        /**
         * Handler for drag enter of the dropZone
         *
         * @param {Event} event - Event object
         */
        onDragEnter: function (event) {
            event.preventDefault();
            this.lastenter = event.target;
            $(this.dropZone).addClass('aw-ctq__file-uploader-dragging');
        },

        /**
         * Handler for drag leave of the dropZone
         *
         * @param {Event} event - Event object
         */
        onDragLeave: function (event) {
            event.preventDefault();
            if (this.lastenter === event.target) {
                $(this.dropZone).removeClass('aw-ctq__file-uploader-dragging');
            }
        },

        /**
         * Handler for drag leave of the dropZone
         *
         * @param {Event} event - Event object
         */
        onDrop: function (event) {
            event.preventDefault();
            $(this.dropZone).removeClass('aw-ctq__file-uploader-dragging');
        },

        /**
         * Load start event handler.
         */
        onLoadingStart: function () {
            this._super();
            if ($(this.elementToDisable).length) {
                $(this.elementToDisable).attr('disabled', true);
            }
        },

        /**
         * Load stop event handler.
         */
        onLoadingStop: function () {
            this._super();
            if ($(this.elementToDisable).length) {
                $(this.elementToDisable).attr('disabled', false);
            }
        },

        /**
         * Retrieve input name
         *
         * @param {String} inputName
         * @param {String} fileName
         * @return {String}
         */
        getInputName: function (inputName, fileName) {
            return this.inputName + '[' + fileName + '][' + inputName + ']';
        }
    });
});
