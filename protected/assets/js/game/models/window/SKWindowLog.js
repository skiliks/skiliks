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
        'activate': function (window) {
            var time = SKApp.user.simulation.getGameSeconds();
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
            
            //console.log('[SKWindow] Activated window ' + window.get('name') + '/' + window.get('subname') +
            //    ' :: ' + window.window_uid + ' at ' +
            //    SKApp.user.simulation.getGameTime(true) + (window.get('params') ? ' ' + JSON.stringify(window.get('params')):'')
            //);
        },
        'deactivate': function (window) {
            var time = SKApp.user.simulation.getGameSeconds();

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
            
            //console.log('[SKWindow] Deactivated window ' + window.get('name') + '/' + window.get('subname') +
            //    ' :: ' + window.window_uid + ' at '
            //    + SKApp.user.simulation.getGameTime(true) + (window.get('params') ? ' ' + JSON.stringify(window.get('params')):'')
            //);
        },
        'getAndClear': function () {
            var log = this.log;
            this.log = [];
            return log;
        }
    });
});