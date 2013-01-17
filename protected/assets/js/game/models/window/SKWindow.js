/*global Backbone:false, console, SKApp */

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
        idAttribute: 'subname',
        initialize: function () {
            var window_id = this.get('name') + "/" + this.get('subname');
            if (window_id in SKApp.user.simulation.window_set) {
                throw "Window " + window_id + " already exists";
            }
            if (! (this.get('name') in screens)) {
                throw 'Unknown screen';
            }
            if (! (this.get('subname') in screensSub)) {
                throw 'Unknown subscreen';
            }
            window_id[window_id] = this;
            this.is_opened = false;
            this.simulation = SKApp.user.simulation;
        },
        'getWindowId': function () {
            return screens[this.get('name')];
        },
        'getSubwindowId': function () {
            return screensSub[this.get('subname')];
        },
        /**
         * Opens a window
         */
        open: function() {
            if (this.is_opened) {
                throw "Window is already opened";
            }
            this.set('zindex', Math.max(Math.max.apply(this, this.simulation.window_set.pluck('zindex')),0));
            this.is_opened = true;
            this.simulation.window_set.showWindow(this);
            this.trigger('open', this.get('name'), this.get('subname'));
        },
        close: function() {
            if (!this.is_opened) {
                throw "Window is already closed";
            }
            this.trigger('pre_close');
            if (this.prevent_close === true) {
                delete this.prevent_close;
                return;
            }
            this.is_opened = false;
            SKApp.user.simulation.window_set.hideWindow(this);
            this.trigger('close');
        },
        setOnTop:function () {
            var me = this;
            var window_set = SKApp.user.simulation.window_set;
            if (window_set.length === 1 || window_set.at(window_set.length - 1).id === this.id ||
                !window_set.get(me)) {
                return;
            }
            window_set.at(window_set.length - 1).deactivate();
            window_set.remove(me, {silent:true});
            var zIndex = 0;
            window_set.each(function (window) {
                window.set('zindex', zIndex);
                zIndex ++;
            });
            me.set('zindex', zIndex);
            window_set.add(me, {silent:true});
            window_set.sort();
            me.activate();
        },
        deactivate: function (params) {
            params = params || {};

            if (!params.silent) {
                this.trigger('deactivate');
            }
            this.simulation.windowLog.deactivate(this);
        },
        activate: function (params) {
            params = params || {};

            if (!params.silent) {
                this.trigger('activate');
            }
            this.simulation.windowLog.activate(this);
        }
    });
    window.SKWindow.window_set = {};

})();
