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
                    var cache = me.clone();
                    options.success(data.data);
                    me.each(function(model) {
                       var found = cache.where({'id': model.get('id')});
                       if(1 === found.length){
                          var isInitialized = found[0].get('isInitialized');
                          if(true === isInitialized) {
                              model.set('isInitialized', true);
                          }else{
                              model.set('isInitialized', false);
                          }
                       }
                    });
                    me.trigger('afterReset');
                });
            }
        }
    });

    return SKDocumentCollection;
});