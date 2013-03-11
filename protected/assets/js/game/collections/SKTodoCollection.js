/*global SKApp, Backbone, _, SKTodoTask */
var SKTodoCollection;
define(["game/models/SKTodoTask"], function () {
    "use strict";
    /**
     * @class SKTodoCollection
     * @constructor void
     */
    SKTodoCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKTodoTask
         * @default SKTodoTask
         */
        model: SKTodoTask,

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
                SKApp.server.api('todo/get', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });

    return SKTodoCollection;
});