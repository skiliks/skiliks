/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    window.SKWindowLog = Backbone.Model.extend({
        'initialize': function () {
            this.log = [];
        },
        'activate': function (window) {
            var time = SKApp.user.simulation.getGameMinutes();
            this.log.push([window.getWindowId(), window.getSubwindowId(), 'activated', time, window.get('params')]);
        },
        'deactivate': function (window) {
            var time = SKApp.user.simulation.getGameMinutes();
            this.log.push([window.getWindowId(), window.getSubwindowId(), 'deactivated', time, window.get('params')]);
        },
        'getAndClear': function () {
            var log = this.log;
            this.log = [];
            return log;
        }
    });
})();