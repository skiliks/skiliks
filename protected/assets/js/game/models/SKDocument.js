/*global Backbone:false, console, SKApp, session */

var SKDocument;
define([], function () {
    "use strict";

    /**
     * Call by SKDocument._excel_cache - works like "singleton"
     * @parameter _excel_cache
     * @type array of string
     * @default []
     */
    var _excel_cache = {};

    /**
     * @class SKDocument
     * @augments Backbone.Model
     */
    SKDocument = Backbone.Model.extend({

        /**
         * @property isHasZoho500
         * @type boolean
         * @default false
         */
        isHasZoho500: false,

        /**
         * Shows is document initialized or not
         * 1. Zoho excel is initialized if we have iframe URL
         * 2. Other docs is initialized already after initialization
         *
         * @property isInitialized
         * @type boolean
         * @default false
         */
        isInitialized: false,

        /**
         * Constructor
         * @method initialize
         * @return void
         */
        initialize: function () {
            var me = this;

            if (this.get('mime') === "application/vnd.ms-excel") {
                if (_excel_cache[this.get('id')] === undefined) {
                    SKApp.server.api('myDocuments/getExcel', {
                        'id': decodeURIComponent(this.get('id'))
                    }, function (data) {
                        me.set('excel_url', data.excelDocumentUrl);

                        //me.set('isInitialized', true);

                        me.trigger('document:excel_uploaded');
                        _excel_cache[me.get('id')] = data.excelDocumentUrl.replace('\r', '');
                    });
                } else {
                    me.set('excel_url', _excel_cache[this.get('id')]);
                }
            }

            //me.set('isInitialized', true);
        },

        /**
         * @method combineIframeId
         * @return {string}
         */
        combineIframeId: function () {
            return '#excel-preload-' + this.id;
        }
    },
    {
        /**
         * @parameter _excel_cache
         * @type array of string
         * @default []
         */
        _excel_cache: _excel_cache
    });


});