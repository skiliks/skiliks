/*global SKApp, Backbone, _, SKTodoTask */

(function () {
    "use strict";
    window.SKPhoneHistoryCollection = Backbone.Collection.extend({
        model:SKPhoneHistory,
        parse:function(data) {
            return _.values(data.data);
        },
        sync:function (method, collection, options) {
            if ('read' === method){
                SKApp.server.api('phone/getList', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });
})();