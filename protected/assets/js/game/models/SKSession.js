/*global Backbone:false, console, SKApp */

define(["game/models/SKUser"], function (SKUser) {
    "use strict";
    /**
     * Сессия текущего пользователя. Есть у всех — авторизованных и неавторизованных пользователей
     *
     * @class SKSession
     * @augments Backbone.Model
     */
    var SKSession = Backbone.Model.extend({
        /**
         * Произошла ошибка авторизации
         * @param {*} error тип ошибки (неверная сессия или неверно введен пароль)
         * @event login:failure
         *
         * Случается если пользователь успешно авторизован и с ним можно делать все, что угодно
         * @event login:success
         */
        'check':function () {
            var me = this;
            SKApp.server.api('static/auth/checkSession', {}, function (data) {
                if (data.result === 1) {
                    SKApp.user = new SKUser(data.simulations);
                    console.debug('[SKSession] User authenticated');
                    me.trigger('login:success');
                } else {
                    me.trigger('login:failure');
                }
            });
        },
        /**
         * Авторизует пользователя и записывает его в SKApp.user. Более правильно было бы записываеть его как SKApp.session.user,
         * но сессия появилась позже
         *
         * @method login
         * @param username
         * @param pass
         * @async
         */
        'login':function (email, pass) {
            var me = this;
            if (SKApp.user) {
                throw 'Trying to login user twice';
            }
            SKApp.server.api('auth/auth', {
                'email':email,
                'pass':pass
            }, function (data) {
                if (data.result === 1) {
                    SKApp.user = new SKUser(data.simulations);
                    SKApp.user.on('logout', function () {
                        delete SKApp.user;
                    });

                    me.trigger('login:success');
                } else {
                    me.trigger('login:failure', 'incorrect_password');
                }
            });
        }
    });
    return SKSession;
});