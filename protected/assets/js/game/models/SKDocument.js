/*global Backbone:false, console, SKApp, session */

var SKDocument;
define([], function () {
    "use strict";
    /**
     * Call by SKDocument._excel_cache - works like "singleton"
     * @todo: move to SKDocumentCollection
     * @class SKDocument
     * @augments Backbone.Model
     */
    var _excel_cache = {};

    /**
     * @class SKDocument
     * @augments Backbone.Model
     */
    SKDocument = Backbone.Model.extend({

        /**
         * @type boolean
         * @default false
         */
        isHasZoho500: false,

        /**
         * Constructor
         * @method initialize
         */
        initialize: function () {
            var me = this;
            if (this.get('mime') === "application/vnd.ms-excel") {
                if (_excel_cache[this.get('id')] === undefined) {
                    SKApp.server.api('myDocuments/getExcel', {
                        'id': decodeURIComponent(this.get('id'))
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

        combineIframeId: function () {
            return '#excel-preload-' + this.id;
        }
    },
    {
        _excel_cache: _excel_cache
    });


});