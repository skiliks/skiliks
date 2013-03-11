/*global Backbone:false*/
var SKApplication;

define(["game/models/SKServer","game/models/SKSession"], function (SKServer, SKSession) {
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

                /**
                 * Текущая браузерная сессия
                 *
                 * @attribute session
                 * @type SKSession
                 */
                this.session = new SKSession();
                /**
                 * Текущий пользователь (если есть)
                 *
                 * @type SKUser
                 * @attribute user
                 */
                this.__defineSetter__('user', function (user) {
                    if (typeof(this._user) !== 'undefined') {
                        throw 'User is already exists';
                    }
                    this._user = user;
                });
                this.__defineGetter__('user', function () {
                    return this._user;
                });
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

    /**
     * @object
     * @type {SKApplication}
     */

    window.SKApp = new SKApplication();

    return SKApplication;
});