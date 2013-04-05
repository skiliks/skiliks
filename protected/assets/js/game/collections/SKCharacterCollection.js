/*global Backbone:false, console, SKApp, session */

define(['game/models/SKCharacter'], function (SKCharacter) {
    "use strict";
    /**
     * Список персонажей
     * @class SKCharacterCollection
     */
    var SKCharacterCollection = Backbone.Collection.extend({
        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        model: SKCharacter,
        sync: function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('character/list', {}, function (data) {
                    options.success(data);
                });
            }
        },
        withoutHero: function () {
            return this.filter(function (model) {
                return model.get('code') !== "1";
            });
        }
    });
    return SKCharacterCollection;
});