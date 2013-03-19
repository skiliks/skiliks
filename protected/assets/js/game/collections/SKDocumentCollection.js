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
         * Constructor
         * @method initialize
         * @return void
         */
        initialize: function() {
            var me = this;
            if (window.addEventListener){
                window.addEventListener("message", me.handlePostMessage,false);
            } else {
                window.attachEvent("onmessage", me.handlePostMessage);
            }

        },

        /**
         * @method handlePostMessage
         * @param postMessage event
         * @return void
         */
        handlePostMessage: function(event) {
            console.log('handlePostMessage');
            if (undefined != typeof event && event.origin !== "*") {
                console.log('zoho-500');
                SKApp.simulation.trigger('zoho-500-xxx');
            }
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('myDocuments/getList', {}, function (data) {
                    options.success(data.data);
                });
            }
        }
    });

    return SKDocumentCollection;
});