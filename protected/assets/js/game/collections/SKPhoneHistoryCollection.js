/*global SKApp, Backbone, _, SKTodoTask, SKPhoneHistory */
var SKPhoneHistoryCollection;
define(["game/models/SKPhoneHistory"], function () {
    "use strict";
    /**
     * @class SKPhoneHistoryCollection
     * @augments Backbone.Collection
     */
    SKPhoneHistoryCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKPhoneHistory
         * @default SKPhoneHistory
         */
        model: SKPhoneHistory,

        /**
         * @method parse
         * @param data
         * @returns array
         */
        parse: function (data) {
            return _.values(data.data);
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('phone/getlist', {}, function (data) {
                    options.success(data);
                });
            }
        },

        /**
         * @method readHistory
         */
        readHistory: function () {
            this.each(function (model) {
                model.set('is_displayed', true);
            });
        }
    });

    return SKPhoneHistoryCollection;
});