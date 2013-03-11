/*global Backbone:false, console, SKApp, session, SKDayTask */

define(["game/models/SKDayTask"], function () {
    "use strict";
    window.SKDayTaskCollection = Backbone.Collection.extend({
        model:SKDayTask,

        /**
         * constructor
         * @method initialize
         */
        'initialize': function () {},

        /**
         * @method parse
         * @param data
         * @returns {array}
         */
        parse:function (data) {
            return data.data;
        },
        /**
         * Returns true if time slot has no reserved tasks
         * @param time
         * @param day
         * @param duration
         */
        isTimeSlotFree:function (time, day, duration) {
            var result = true;
            var start_hour = time.split(':')[0];
            var start_minute = time.split(':')[1];
            var start_time = parseInt(start_hour, 10) * 60 + parseInt(start_minute, 10);
            var end_time = start_time + parseInt(duration, 10);
            if (parseInt(day,10) === 1 && start_time < SKApp.user.simulation.getGameMinutes()) {
                return false;
            }
            if (end_time > 22 * 60) {
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
                var task_end_time = task_start_time + parseInt(task.get('duration'),10);
                if (!(task_start_time >= end_time || task_end_time <= start_time)) {
                    result = false;
                }
            });

            return result;
        },
        sync:function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('dayPlan/get', {}, function (data) {
                    options.success(data);
                });
            }

        }
    });
});