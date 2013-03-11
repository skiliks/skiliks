/*global Backbone:false, console, SKApp, session, SKPhoneTheme */
var SKPhoneThemeCollection;

(function () {
    "use strict";
    /**
     * @class SKPhoneThemeCollection
     * @constructor initialize
     */
    SKPhoneThemeCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKPhoneTheme
         * @default SKPhoneTheme
         */
        model: SKPhoneTheme,

        /**
         * @property characterId
         * @type integer
         * @default undefined
         */
        characterId: undefined,

        /**
         * Constructor
         *
         * @method initialize
         * @param options
         */
        initialize: function (options) {
            this.characterId = options.characterId;
        },

        /**
         * @method parse
         * @param data
         * @returns array
         */
        parse: function (data) {
            return data.data;
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            var phoneCollection = this;

            if ('read' === method) {
                SKApp.server.api(
                    'phone/getThemes',
                    {id: phoneCollection.characterId }
                    , function (data) {
                        options.success(data);
                    }
                );
            }
        }
    });

    return SKPhoneThemeCollection;
})();