/*global Backbone:false*/
var SKApplication;

define(["game/models/SKServer","game/models/SKSession"], function (SKServer, SKSession) {
    "use strict";
    /**
     * Корневой класс нашей игры. Инстанциируется в начале игры и инстанс доступен под именем SKApp
     *
     * @class SKApplication
     * @constructor initialize
     * @augments Backbone.Model
     */
    SKApplication = Backbone.Model.extend(
        /** @lends SKApplication.prototype */
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

            /**
             * Какая-то неведомая фигня, которую стоит выпилить
             * @method clone
             * @param object
             * @return {*}
             */
            /*clone:function (object) {
                if (!object || 'object' !== typeof object) {
                    return object;
                }
                var cloned = 'function' === typeof object.pop ? [] : {};
                var p, v;
                for (p in object) {
                    if (object.hasOwnProperty(p)) {
                        v = object[p];
                        if (v && 'object' === typeof v) {
                            cloned[p] = this.clone(v);
                        }
                        else {
                            cloned[p] = v;
                        }
                    }
                }
                return cloned;
            }*/
        });

    /**
     * @object
     * @type {SKApplication}
     */

    window.SKApp = new SKApplication();

    return SKApplication;
});