/*global SKApp, Backbone, _, SKTodoTask */

(function () {
    "use strict";
    window.SKPhoneContactsCollection = Backbone.Collection.extend({
        model:SKPhoneContact,
        parse:function(data) {
            return _.values(data.data);
        },
        sync:function (method, collection, options) {
            if ('read' === method){
                SKApp.server.api('phone/getContacts', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });
})();