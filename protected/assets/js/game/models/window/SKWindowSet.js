/*global console, Backbone, SKWindow, SKDialogWindow, _*/
define(["game/models/window/SKWindow", "game/models/window/SKDialogWindow"], function () {
    "use strict";
    /**
     * Оконный менеджер, содержит в себе все окна
     *
     * @class SKWindowSet
     * @constructs
     * @type {*}
     */
    window.SKWindowSet = Backbone.Collection.extend({
        model:          SKWindow,
        window_classes: {
            'phone/phoneTalk':     SKDialogWindow,
            'phone/phoneCall':     SKDialogWindow,
            'visitor/visitorTalk': SKDialogWindow
        },

        'initialize': function (models, options) {
            options.events.on('event:phone:in_progress', function (event) {
                this.toggle('phone', 'phoneCall', {sim_event: event});
            }, this);
            options.events.on('event:visit:in_progress', function (event) {
                this.toggle('visitor', 'visitorEntrance', {sim_event: event});
            });
            options.events.on('event:immediate-visit', function (event) {
                var win = this.open('visitor', 'visitorTalk', {sim_event: event});
                event.setStatus('in progress');
            }, this);
            options.events.on('event:immediate-phone', function (event) {
                var win = this.open('phone', 'phoneTalk', {sim_event: event});
                event.setStatus('in progress');
            }, this);

        },

        comparator: function (window) {
            return this.get('zindex');
        },

        'showWindow': function (win) {
            if (win.single === true && this.get(win)) {
                throw 'Window already displayed';
            }

            if (this.length) {
                this.at(this.length - 1).deactivate();
            }
            this.add(win);
            win.activate();
        },

        toggle: function (name, subname, params) {
            var windows = this.where({name: name, subname: subname});
            if (windows.length !== 0) {
                if ((this.at(this.length - 1).id === subname)) { // If this is top window
                    windows[0].close();
                } else {
                    windows[0].setOnTop();
                    windows[0].trigger('refresh');
                }
            } else {


                var WindowType = this.window_classes[name + '/' + subname] || SKWindow;
                var win = new WindowType(_.extend({name: name, subname: subname}, params));
                win.open();
            }
        },

        /**
         * Just opens window or nothing if opened
         * @param name
         * @param subname
         * @param params
         */
        open: function (name, subname, params) {
            var windows = this.where({name: name, subname: subname});
            if (windows.length !== 0) {
                if (this.at(this.length - 1).id !== subname) { // If this is top window
                    windows[0].setOnTop();
                }
                if (params !== undefined) {
                    _.each(_.pairs(params), function (i) {
                        windows[0].set(i[0], i[1]);
                    });
                }
                windows[0].trigger('refresh');
                return windows[0];
            } else {
                var WindowType = this.window_classes[name + '/' + subname] || SKWindow;
                var win = new WindowType(_.extend({name: name, subname: subname}, params));
                win.open();
                return win;
            }
        },

        'hideWindow': function (win) {
            this.remove(win);
            win.deactivate();
            if (this.length) {
                this.at(this.length - 1).activate();
            }
        },

        //TODO:работает?
        'closeAll':   function () {
            var name;
            if (arguments.length === 1) {
                name = arguments[0];
            }
            var reverse_list = this.models.slice();
            reverse_list.forEach(function (win) {
                if (name ? win.get('name') === name : win.get('name') !== 'mainScreen') {
                    // we can`t close already closed windows
                    if (true === win.is_opened) {
                        win.close();
                    }
                }
            });
        },

        'deactivateActiveWindow': function () {
            this.getActiveWindow().deactivate();
        },

        'getActiveWindow': function () {
            var count = this.models.length;
            if (count > 0) {
                return this.models[count - 1];
            } else {
                throw new Error("No active windows!!");
            }
        }

    });
});
