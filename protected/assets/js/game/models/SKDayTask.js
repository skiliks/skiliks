/*global Backbone:false, console, SKApp, session, define, $ */

define([], function () {
    "use strict";
    /**
     * @class SKDayTask
     * @augments Backbone.Model
     */
    window.SKDayTask = Backbone.Model.extend({
        idAttribute: 'task_id',

        /**
         * @method sync
         *
         * @param {string} method, 'update'|delete'|...
         * @param {SKMailTask} model
         * @param {Object} options
         */
        sync: function (method, model, options) {
            try {
                if ('update' === method) {
                    model.set('uniqueId', undefined);
                    SKApp.server.api('dayPlan/add', model.toJSON(), function (data) {
                        options.success(data);
                    });
                }
                if ('delete' === method) {
                    model.set('uniqueId', undefined);
                    SKApp.server.api('dayPlan/delete', model.toJSON(), function (data) {
                        options.success(data);
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
});