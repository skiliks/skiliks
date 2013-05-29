/*global Backbone:false, console, SKApp, session */

var SKDocument;
define(["game/collections/SKSheetCollection"], function (SKSheetCollection) {
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
            if (this.get('mime') === 'application/vnd.ms-excel') {
                me.set('sheets', new SKSheetCollection([], {'document': this}));
                me.get('sheets').fetch();
            }
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

    return SKDocument;
});