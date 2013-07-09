/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    var SKSheet = Backbone.Model.extend({
        initialize: function () {
            this.on('change:content', function () {

            });
        },
        
        activate: function () {
            var me = this;
            this.collection.each(function (sheet) {
                if (sheet.get('name') === me.get('name')) {
                    sheet.set('active', true);
                    sheet.trigger('activate');
                } else {
                    sheet.set('active', false);
                    sheet.trigger('deactivate');
                }
            });
        },

        sync: function (method, collection, options) {
            if ('create' === method) {
                SKApp.server.api('/myDocuments/saveSheet/' + this.collection.document.id, {}, function (data) {
                    options.success(data.data);
                });
            }
        }
    });
    return SKSheet;
});