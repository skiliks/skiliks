/*global SKApp, Backbone, _, SKTodoTask */
var SKTodoCollection;
define(["game/models/SKTodoTask"], function () {
    "use strict";
    /**
     * @class SKTodoCollection
     * @augments Backbone.Collection
     */
    SKTodoCollection = Backbone.Collection.extend({

        availableTasks:[],

        /**
         * @property model
         * @type SKTodoTask
         * @default SKTodoTask
         */
        model: SKTodoTask,

        /**
         * @method parse
         * @param data
         * @returns array
         */
        parse: function (data) {
            return _.values(data.data);
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            try {
                var me = this;
                if ('read' === method) {
                    SKApp.server.api('todo/get', {}, function (data) {
                        options.success(data);
                    });
                    var hasNewTask = false;
                    me.each(function(model) {
                        if(-1 === me.availableTasks.indexOf(model.get('id'))) {
                            me.availableTasks.push(model.get('id'));
                            hasNewTask = true;
                        }
                    });
                    if(hasNewTask) {
                        me.trigger('onNewTask');
                    }
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKTodoCollection;
});