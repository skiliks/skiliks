/*global Backbone:false, console */

(function () {
    "use strict";
    var screens = {
        'mainScreen':1,
            'plan':3,
            'mailEmulator':10,
            'phone':20,
            'visitor':30,
            'documents':40
    };
    var screensSub = {
        'mainScreen':1,
            'plan':3,
            'mailMain':11,
            'mailPreview':12,
            'mailNew':13,
            'mailPlan':14,
            'phoneMain':21,
            'phoneTalk':23,
            'phoneCall':24,
            'visitorEntrance':31,
            'visitorTalk':32,
            'documents':41,
            'documentsFiles':42
    };
    window.SKWindow = Backbone.Model.extend({
        initialize: function (name, subname) {
            var window_id = name + "/" + subname;
            if (window_id in window.SKWindow.window_set) {
                throw "Window " + window_id + " already exists";
            }
            if (! (name in screens)) {
                throw 'Unknown screen';
            }
            if (! (subname in screensSub)) {
                throw 'Unknown subscreen';
            }
            window_id[window_id] = this;
            this.is_opened = false;
            this.name = name;
            this.subname = subname;
            this.simulation = SKApp.user.simulation;
        },
        'getWindowId': function () {
            return screens[this.name];
        },
        'getSubwindowId': function () {
            return screensSub[this.subname];
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
            console.log('[SKWindow] Deactivated window ' + this.name + '/' + this.subname + ' at ' + this.simulation.getGameTime() +
                (this.get('params') ? ' ' + JSON.stringify(this.get('params')):'')
            );
            this.simulation.windowLog.deactivate(this);
        },
        activate: function () {
            console.log('[SKWindow] Activated window ' + this.name + '/' + this.subname + ' at ' + this.simulation.getGameTime() +
                (this.get('params') ? ' ' + JSON.stringify(this.get('params')):'')
            );
            this.simulation.windowLog.activate(this);
        }
    });
    window.SKWindow.window_set = {};

})();
