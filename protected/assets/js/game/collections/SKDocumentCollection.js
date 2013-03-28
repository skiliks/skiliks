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
                    //console.log('1');
                    //alert('1*');
                    options.success(data.data);
                    //console.log('2');

                    //me.waitForDocumentsInitialization();
                });
            }
        },

        isDocumentsInitialized: function() {
            var me = this;

            var isDocumentsInitialized = true;

            for (var key in this.models) {
                console.log(this.models[key].get('isInitialized'));
                if (false == this.models[key].get('isInitialized')) {
                    isDocumentsInitialized = false;
                    //alert('false!');
                }
            }

            if (true == isDocumentsInitialized) {
                this.trigger('documents-initialized');
                //console.log('documents-initialized!');
                //alert('documents-initialized!');
            }

            return isDocumentsInitialized;
        },

        waitForDocumentsInitialization: function() {
            var me = this;

            //console.log('waitForDocumentsInitialization RUN ... ');

            if (false == me.waitForDocumentsInitialization()) {
                setTimeout(me.waitForDocumentsInitialization(), 2000);
            }
        }
    });

    return SKDocumentCollection;
});