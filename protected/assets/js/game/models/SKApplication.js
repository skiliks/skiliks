/*global Backbone:false*/
var SKApplication;

define([
    "game/models/SKServer",
    "game/models/SKSimulation",
    "game/models/SKTutorial"
], function (SKServer, SKSimulation, SKTutorial) {
    "use strict";
    /**
     * Корневой класс нашей игры. Инстанциируется в начале игры и инстанс доступен под именем SKApp
     *
     * @class SKApplication
     * @augments Backbone.Model
     */
    SKApplication = Backbone.Model.extend(
        {
            /**
             * Constructor
             * @method initialize
             * @return void
             */
            'initialize':function () {
                /**
                 * Ссылка на API-сервер
                 * @attribute server
                 * @type SKServer
                 */
                this.server = new SKServer();

                var SimClass = this.isTutorial() ? SKTutorial : SKSimulation;
                this.simulation = new SimClass({'app': this, 'mode': this.get('mode'), 'type': this.get('type')});
            },

            run: function() {
                this.simulation.start();
            },

            /**
             * Очищает текущего пользователя симуляции
             * @method clearUser
             * @return void
             */
            'clearUser':function () {
                this.user.logout();
                delete this.user;
            },

            isLite: function() {
                return this.get('type') === 'lite';
            },

            isTutorial: function() {
                return this.get('type') === 'tutorial';
            }
        });

    return SKApplication;
});