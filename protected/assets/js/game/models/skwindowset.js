/*global console, Backbone, SKWindow*/
(function() {
    "use strict";
    window.SKWindowSet = Backbone.Collection.extend({
        model: SKWindow,
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

        toggle: function (name, subname) {
            var windows = this.where({name:name, subname:subname});
            if (windows.length !== 0) {
                windows[0].close();
            } else {
                var win = new SKWindow({name:name, subname:subname});
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