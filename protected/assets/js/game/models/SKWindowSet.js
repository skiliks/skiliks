/*global console, Backbone, SKWindow, SKDialogWindow*/
(function() {
    "use strict";
    window.SKWindowSet = Backbone.Collection.extend({
        model: SKWindow,
        window_classes: {
            'phone/phoneTalk': SKDialogWindow
        },
        'initialize':function () {
            this.window_zindex = [];
        },
        'showWindow':function (win) {
            if (this.get(win)) {
                    throw 'Window already displayed';
            }
            if (this.length) {
                this.at(this.length - 1).deactivate();
            }
            this.add(win);
            win.activate();
        },

        toggle: function (name, subname, params) {
            var windows = this.where({name:name, subname:subname});
            if (windows.length !== 0) {
                windows[0].close();
            } else {
                var WindowType = this.window_classes[name + '/' + subname] || SKWindow;
                var win = new WindowType(_.extend({name:name, subname:subname}, params));
                win.open();
            }
        },

        'hideWindow':function (win) {
            this.remove(win);
            win.deactivate();
            if (this.length) {
                this.at(this.length - 1).activate();
            }
        },

        'closeAll':function () {
            var name;
            if (arguments.length === 1) {
                name = arguments[0];
            }
            this.each(function (win) {
                if (name ? win.name === name : win.name !== 'mainScreen') {
                    win.close();
                }
            });
        }
    });
})();