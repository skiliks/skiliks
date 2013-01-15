/*global Backbone:false, console, SKApp, session, SKPhoneTheme */

(function () {
    "use strict";
    window.SKPhoneThemeCollection = Backbone.Collection.extend({
        model:SKPhoneTheme,
        contact:{},
        initialize:function(contact) {
            this.contact = contact;
        },
        parse:function(data) {
            return _.values(data.data);
        },
        sync:function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('phone/getThemes', this.contact, function (data) {
                    options.success(data);
                });
            }
        }
    });
})();