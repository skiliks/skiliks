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
            console.log('window: ', window);
            window.addEventListener(" ", me.handlePostMessage(), false);
        },

        /**
         * @method handlePostMessage
         * @param postMessage event
         * @return void
         */
        handlePostMessage: function(event) {
            console.log('handlePostMessage');
            if (undefined != typeof event && event.origin !== "*") {
                console.log('event!');
                console.log('event', event.origin);
                return;
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