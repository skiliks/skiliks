/*global Backbone:false, console, SKApp, session, SKDocument */
var SKDocumentCollection;
define(["game/models/SKDocument"], function () {
    "use strict";
    /**
     * @class SKEventCollection
     * @augments Backbone.Collection
     */
    SKDocumentCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKDocument
         * @default SKDocument
         */
        model: SKDocument,

        /**
         * @property model
         * @type SKDocument
         * @default SKDocument
         */
        zoho_500: [],

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            var me = this;

            if ('read' === method) {
                SKApp.server.api('myDocuments/getList', {}, function (data) {
                    console.log(data);
                    console.log(me;
                    options.success(data.data);

                    me.waitForDocumentsInitialization();
                });
            }
        },

        isDocumentsInitialized: function() {
            var me = this;

            var isDocumentsInitialized = true;

            for (var key in this.models) {
                if (false == this.models[key].isInitialized) {
                    isDocumentsInitialized = false;
                }
            }

            if (true == isDocumentsInitialized) {
                this.trigger('documents-initialized');
            }

            return isDocumentsInitialized;
        },

        waitForDocumentsInitialization: function() {
            var me = this;

            if (false == me.isDocumentsInitialized()) {
                // setTimeout(me.waitForDocumentsInitialization(), 1000);
            }
        }
    });

    return SKDocumentCollection;
});