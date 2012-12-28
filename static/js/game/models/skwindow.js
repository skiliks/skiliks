/*global Backbone:false, console */

(function () {
    "use strict";
    window.SKWindow = Backbone.Model.extend({
        initialize: function (name, subname) {
            var window_id = name + "/" + subname;
            if (window_id in window.SKWindow.window_set) {
                throw "Window " + window_id + " already exists";
            }
            if (! (name in window.simulation.screens)) {
                throw 'Unknown screen';
            }
            if (! (subname in window.simulation.screensSub)) {
                throw 'Unknown subscreen';
            }
            window_id[window_id] = this;
            this.is_opened = false;
            this.name = name;
            this.subname = subname;
            this.simulation = window.simulation;
        },
        /**
         * Opens a window
         */
        open: function() {
            if (this.is_opened) {
                throw "Window is already opened";
            }
            this.is_opened = true;
            this.simulation.window_set.showWindow(this);
        },
        close: function() {
            if (!this.is_opened) {
                throw "Window is already closed";
            }
            this.is_opened = false;
            this.simulation.window_set.hideWindow(this);
        },
        deactivate: function () {
            console.log('[SKWindow] Deactivated window ' + this.name + '/' + this.subname + ' at ' + window.timer.getCurTimeFormatted() +
                (this.get('params') ? ' ' + JSON.stringify(this.get('params')):'')
            );
            window.simulation.frontEventLog(this.name, this.subname, 'deactivated', this.get('params'));
        },
        activate: function () {
            console.log('[SKWindow] Activated window ' + this.name + '/' + this.subname + ' at ' + window.timer.getCurTimeFormatted() +
                (this.get('params') ? ' ' + JSON.stringify(this.get('params')):'')
            );
            window.simulation.frontEventLog(this.name, this.subname, 'activated', this.get('params'));
        }
    });
    window.SKWindow.window_set = {};

})();
