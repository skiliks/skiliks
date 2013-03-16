/*global Backbone:false, console, SKApp, session */

define(["game/models/window/SKWindow"],function () {
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
                throw 'window.window_uid is NAN!';
            }

            this.log.push({
                0: window.getWindowId(),
                1: window.getSubwindowId(),
                2: 'activated',
                3: time,
                4: window.get('params'),
                'window_uid': window.window_uid
            });
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

            this.log.push({
                0: window.getWindowId(),
                1: window.getSubwindowId(),
                2: 'deactivated',
                3: time,
                4: window.get('params'),
                'window_uid': window.window_uid
            });
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
});