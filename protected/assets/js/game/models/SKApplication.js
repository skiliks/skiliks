/*global Backbone:false, console, SKServer */

(function () {
    "use strict";
    window.SKApplication = Backbone.Model.extend({
        'server':new SKServer(),
        'session':new SKSession(),
        'initialize':function () {
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
        'clearUser': function () {
            this.user.logout();
            delete this.user;
        }
    });

    window.SKApp = new window.SKApplication();
})();