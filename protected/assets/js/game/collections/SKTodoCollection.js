/*global SKApp, Backbone, _, SKTodoTask */

define(["game/models/SKTodoTask"], function () {
    "use strict";
    window.SKTodoCollection = Backbone.Collection.extend({
        model:SKTodoTask,
        parse:function(data) {
            return _.values(data.data);
        },
        sync:function (method, collection, options) {
            if ('read' === method){
                SKApp.server.api('todo/get', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });
});