/*global Backbone:false*/
var SKApplication;

define(["game/models/SKServer","game/models/SKSimulation"], function (SKServer, SKSimulation) {
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

                this.simulation = new SKSimulation({'app': this, 'mode': this.get('mode'), 'type': this.get('type')});
            },

            /**
             * Очищает текущего пользователя симуляции
             * @method clearUser
             * @return void
             */
            'clearUser':function () {
                this.user.logout();
                delete this.user;
            }
        });

    return SKApplication;
});