/*global Backbone:false, console, SKApp, session, SKPhoneTheme */

(function () {
    "use strict";
    window.SKPhoneThemeCollection = Backbone.Collection.extend({
        model:SKPhoneTheme,
        sync:function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('phone/getThemes', function (data) {
                    options.success(data);
                });
            }
        }
    });
})();