/*global Backbone:false, console, SKApp, session */

define([],function () {
    "use strict";
    /**
     * @class SKWindowLog
     * @augments Backbone.Model
     */
    window.SKWindowLog = Backbone.Model.extend({
        /**
         * Constructor
         * @method initialize
         */
        'initialize': function () {
            this.log = [];
        },

        /**
         * @method
         * @param window
         */
        'activate': function (window) {
            var time = SKApp.simulation.getGameSeconds();
            if (isNaN(window.window_uid)) {
                throw 'window.window_uid is NaN!';
            }

            var log_raw_data = {
                0:            window.getWindowId(),
                1:            window.getSubwindowId(),
                2:            'activated',
                3:            time,
                4:            window.get('params'),
                'window_uid': window.window_uid
            };

            //console.log(log_raw_data);

            this.log.push(log_raw_data);
        },

        /**
         * @method
         * @param window
         */
        'deactivate': function (window) {
            var time = SKApp.simulation.getGameSeconds();

            if (isNaN(window.window_uid)) {
                throw 'window.window_uid is NAN!';
            }

            var log_raw_data = {
                0:            window.getWindowId(),
                1:            window.getSubwindowId(),
                2:            'deactivated',
                3:            time,
                4:            window.get('params'),
                'window_uid': window.window_uid
            };
//            console.log(log_raw_data);

            this.log.push(log_raw_data);
        },

        /**
         * @method
         * @returns {Array}
         */
        'getAndClear': function () {
            var log = this.log;
            this.log = [];
            return log;
        }
    });
    return window.SKWindowLog;
});