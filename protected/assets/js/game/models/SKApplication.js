/*global Backbone:false, console, SKServer, SKSession */

var SKApplication, SKApp;
(function () {
    "use strict";
    /**
     * @class SKApplication
     *
     * @augments Backbone.Model
     * @property {SKServer} server
     */
    SKApplication = Backbone.Model.extend(
        /** @lends SKApplication.prototype */
        {
            server:new SKServer(),
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
            'clearUser':function () {
                this.user.logout();
                delete this.user;
            },

            /**
             * What is it?
             * @param object
             * @return {*}
             */
            clone:function (object) {
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
            }
        });

    /**
     *
     * @type {SKApplication}
     */
    SKApp = new window.SKApplication();
})();