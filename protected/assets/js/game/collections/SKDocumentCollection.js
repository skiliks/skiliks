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
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            try {
                var me = this;

                if ('read' === method) {
                    SKApp.server.api('myDocuments/getList', {}, function (response) {
                        var cache = me.clone();
                        var models = [];
                        _.each(response.data, function(data){
                            if(!cache.where({'id': data.id}).length){
                                models.push(new SKDocument(data));
                                if(data.name === 'План на завтра.xls'){
                                    SKApp.simulation.dayPlanDocId = data.id;
                                }
                            }
                        });
                        me.add(models);
                        /*options.success(data.data);
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
                        });*/
                        me.trigger('afterReset');
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKDocumentCollection;
});