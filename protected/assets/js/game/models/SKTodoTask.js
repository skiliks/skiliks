/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    /**
     * @class SKTodoTask
     * @augments Backbone.Model
     */
    window.SKTodoTask = Backbone.Model.extend({

        /**
         * String title
         * String date
         * Number id
         * Number duration, в игровіх минутах
         * String day, 'day-1 - сегодня, 'day-2' - завтра, 'after-vacation'
         */

        /** @var boolean */
        isNewTask: false,

        /**
         * @param String method: 'create', 'update', 'delete'
         * @param SKTodoTask model
         * @param Array options
         */
        sync: function (method, model, options) {
            try {
                if (method === 'create' || method === 'update') {
                    if (method === 'create') {
                        this.set('isDisplayed', false);
                    }
                    model.set('uniqueId', undefined);
                    SKApp.server.api('todo/add', {taskId:model.id}, function (data) {
                        options.success(data);
                    });
                } else if (method !== 'delete') {
                    Backbone.Model.prototype.sync.apply(this, arguments);
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return window.SKTodoTask;
});