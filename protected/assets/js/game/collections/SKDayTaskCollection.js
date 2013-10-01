/*global Backbone:false, console, SKApp, session, SKDayTask */
var SKDayTaskCollection;

define(["game/models/SKDayTask"], function () {
    "use strict";
    /**
     * @class SKDayTaskCollection
     * @augments Backbone.Collection
     */
    SKDayTaskCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKDayTask
         * @default SKDayTask
         */
        model: SKDayTask,
        isActiveNow : false,

        /**
         * Constructor
         *
         * @method initialize
         */
        'initialize': function () {
        },

        /**
         * @method parse
         * @param data
         * @returns {array}
         */
        parse: function (data) {
            return data.data;
        },
        /**
         * Returns true if time slot has no reserved tasks
         *
         * @method isTimeSlotFree
         * @param time
         * @param day
         * @param duration
         */
        isTimeSlotFree: function (time, day, duration) {

            try {
                var result = true;
                var start_hour = time.split(':')[0];
                var start_minute = time.split(':')[1];
                var start_time = parseInt(start_hour, 10) * 60 + parseInt(start_minute, 10);
                var end_time = start_time + parseInt(duration, 10);
                if (day === 'day-1' && start_time < SKApp.simulation.getGameMinutes()) {
                    return false;
                }
                if (day !== 'after-vacation' && end_time > 22 * 60) {
                    return false;
                }
                this.each(function (task) {
                    if (task.get('day') !== day) {
                        return;
                    }
                    if (task.get('moving') === true) {
                        return;
                    }
                    var task_start_hour = task.get('date').split(':')[0];
                    var task_start_minute = task.get('date').split(':')[1];
                    var task_start_time = parseInt(task_start_hour, 10) * 60 + parseInt(task_start_minute, 10);
                    var task_end_time = task_start_time + parseInt(task.get('duration'), 10);
                    if (!(task_start_time >= end_time || task_end_time <= start_time)) {
                        result = false;
                    }
                });

                return result;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        /**
         * Returns true if time slot has no reserved tasks
         *
         * @method isTimeSlotFree
         * @param time
         * @param day
         * @param duration
         */
        canPlanedTaskOnTimeToday: function (time, day, duration) {

            try {
                var planed_time = parseInt(time.split(':')[0], 0)*60+parseInt(time.split(':')[1], 0);
                var current_time = SKApp.simulation.getGameMinutes();
                if(day !== 'day-1'){
                    return true;
                }else{
                    if(planed_time > current_time){
                        return true;
                    }
                    return false;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            try {
                if ('read' === method) {
                    SKApp.server.api('dayPlan/get', {}, function (data) {
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

    return SKDayTaskCollection;
});