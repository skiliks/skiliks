/*global Backbone:false, console, SKApp, session, define */

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
            try {
                if ('read' === method) {
                    SKApp.server.api('character/list', {}, function (data) {
                        options.success(data.data);
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        withoutHero: function () {
            try {
                return this.filter(function (model) {
                    return model.get('code') !== "1";
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return SKCharacterCollection;
});