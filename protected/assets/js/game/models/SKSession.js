/*global Backbone:false, console, SKApp, SKUser */

(function () {
    "use strict";
    window.SKSession = Backbone.Model.extend({
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
                SKApp.user = new SKUser(data.simulations);
                me.trigger('login:success');
            });
        }
    });
})();