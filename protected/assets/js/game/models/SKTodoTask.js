/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    /**
     * @class SKTodoTask
     * @augments Backbone.Model
     */
    window.SKTodoTask = Backbone.Model.extend({
        sync: function (method, model, options) {
            if (method === 'create' || method === 'update') {
                SKApp.server.api('todo/add', {taskId:model.id}, function (data) {
                    options.success(data);
                });
            } else if (method !== 'delete') {
                Backbone.Model.prototype.sync.apply(this, arguments);
            }
        }
    });
})();