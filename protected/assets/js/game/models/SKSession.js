/*global Backbone:false, console, SKApp */

define(["game/models/SKUser"], function (SKUser) {
    "use strict";
    var SKSession = Backbone.Model.extend({
        'check':function () {
            var me = this;
            SKApp.server.api('auth/checkSession', {}, function (data) {
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
         * Authenticates user
         *
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
                    throw 'Incorrect password';
                }
            });
        }
    });
    return SKSession;
});