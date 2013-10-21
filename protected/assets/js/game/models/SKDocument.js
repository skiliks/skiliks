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
         * Shows is document initialized or not
         * Other docs is initialized already after initialization
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
            try {
                var me = this;
                if (this.get('mime') === 'application/vnd.ms-excel') {
                    me.set('sheets', new SKSheetCollection([], {'document': this}));
                    me.get('sheets').fetch();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        getCssId: function() {
            return 'doc-' + this.id;
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