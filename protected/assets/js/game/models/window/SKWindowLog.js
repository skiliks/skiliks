/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    window.SKWindowLog = Backbone.Model.extend({
        'initialize': function () {
            this.log = [];
        },
        'activate': function (window) {
            var time = SKApp.user.simulation.getGameSeconds();
            this.log.push([window.getWindowId(), window.getSubwindowId(), 'activated', time, window.get('params')]);
            
            console.log('[SKWindow] Activated window ' + window.get('name') + '/' + window.get('subname') + ' at ' 
                + SKApp.user.simulation.getGameTime() + (window.get('params') ? ' ' + JSON.stringify(window.get('params')):'')
            );
        },
        'deactivate': function (window) {
            var time = SKApp.user.simulation.getGameSeconds();
            this.log.push([window.getWindowId(), window.getSubwindowId(), 'deactivated', time, window.get('params')]);
            
            console.log('[SKWindow] Deactivated window ' + window.get('name') + '/' + window.get('subname') + ' at ' 
                + SKApp.user.simulation.getGameTime() + (window.get('params') ? ' ' + JSON.stringify(window.get('params')):'')
            );
        },
        'getAndClear': function () {
            var log = this.log;
            this.log = [];
            return log;
        }
    });
})();