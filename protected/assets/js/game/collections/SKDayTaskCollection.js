/*global Backbone:false, console, SKApp, session, SKDayTask */

(function () {
    "use strict";
    window.SKDayTaskCollection = Backbone.Collection.extend({
        model: SKDayTask,
        parse:function(data) {
            return data.data;
        },
        sync:function (method, collection, options) {
            if ('read' === method){
                SKApp.server.api('dayPlan/get', {}, function (data) {
                    options.success(data);
                });
            }

        }
    });
})();