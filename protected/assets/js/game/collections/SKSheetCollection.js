/*global SKApp*/
define(["game/models/documents/SKSheet"], function (SKSheet) {
    "use strict";
    var SKSheetCollection = Backbone.Collection.extend({
        model: SKSheet,
        initialize: function (model, options) {
            this.document = options.document;
        },
        sync: function (method, collection, options) {
            try {
                if ('read' === method) {
                    SKApp.server.api('myDocuments/getExcel', {'id': this.document.get('id')}, function (data) {
                        options.success(data.data);
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return SKSheetCollection;
});
