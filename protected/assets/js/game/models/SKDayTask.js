/*global Backbone:false, console, SKApp, session */

(function () {
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
         * @param string method, 'update'|delete'|...
         * @param SKMailTask model
         * @param mixed array options
         */
        sync: function (method, model, options) {
            if ('update' === method){
                SKApp.server.api('dayPlan/add', model.toJSON(), function (data) {
                    options.success(data);
                });
            }
            if ('delete' === method){
                SKApp.server.api('dayPlan/delete', model.toJSON(), function (data) {
                    options.success(data);
                });
            }
        }
    });
})();