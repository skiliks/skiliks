/*global Backbone:false, console, SKApp, session, SKDocument */

define(["game/models/SKDocument"],function () {
    "use strict";
    window.SKDocumentCollection = Backbone.Collection.extend({
        model: SKDocument,
        sync:function (method, collection, options) {
            if ('read' === method){
                SKApp.server.api('myDocuments/getList', {}, function (data) {
                    options.success(data.data);
                });
            }
        }
    });
});