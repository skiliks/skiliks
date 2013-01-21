/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    window.SKDocument = Backbone.Model.extend({
        initialize:function () {
            var me = this;
            if (this.get('mime') === "application/vnd.ms-excel") {
                SKApp.server.api('excelDocument/get', {
                    'id':decodeURIComponent(this.get('id'))
                }, function (data) {
                    me.set('excel_url', data.excelDocumentUrl);
                });
            }
        },
        sync:function (method, model, options) {
        }
    });
})();