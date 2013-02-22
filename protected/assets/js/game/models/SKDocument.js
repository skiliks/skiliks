/*global Backbone:false, console, SKApp, session */

var SKDocument;
define([], function () {
    "use strict";
    var _excel_cache = {};
    /**
     * @class SKDocument
     * @augments Backbone.Model
     */
    SKDocument = Backbone.Model.extend(
        /** @lends SKDocument.prototype */
        {
            initialize:function () {
                var me = this;
                if (this.get('mime') === "application/vnd.ms-excel") {
                    if (_excel_cache[this.get('id')] === undefined) {
                        SKApp.server.api('excelDocument/get', {
                            'id':decodeURIComponent(this.get('id'))
                        }, function (data) {
                            me.set('excel_url', data.excelDocumentUrl);
                            me.trigger('document:excel_uploaded');
                            _excel_cache[me.get('id')] = data.excelDocumentUrl;
                        });
                    } else {
                        me.set('excel_url', _excel_cache[this.get('id')]);
                    }
                }
            },
            sync:function (method, model, options) {
            }
        });
});