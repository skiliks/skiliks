/*global console, Backbone, SKWindow, SKDialogWindow, _*/
(function() {
    "use strict";
    window.SKWindowSet = Backbone.Collection.extend({
        model: SKWindow,
        window_classes: {
            'phone/phoneTalk': SKDialogWindow
        },
        comparator:function(window) {
            return this.get('zindex')
        },
        'initialize':function () {
        },
        'showWindow':function (win) {
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
            var windows = this.where({name:name, subname:subname});
            if (windows.length !== 0) {
                if ((this.at(this.length-1).id === subname)) { // If this is top window
                    windows[0].close();
                } else {
                    windows[0].setOnTop();
                }
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
                    // we can`t close already closed windows
                    if (false === win.is_opened) {
                        win.close();
                    }
                }
            });
        }
    });
})();