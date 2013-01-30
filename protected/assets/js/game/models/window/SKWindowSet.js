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
                    windows[0].trigger('refresh');
                }
            } else {
                var WindowType = this.window_classes[name + '/' + subname] || SKWindow;
                var win = new WindowType(_.extend({name:name, subname:subname}, params));
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
            var windows = this.where({name:name, subname:subname});
            if (windows.length !== 0) {
                if (this.at(this.length-1).id !== subname) { // If this is top window
                    windows[0].setOnTop();
                }
                if (params !== undefined) {
                    _.each(_.pairs(params), function(i) {
                        windows[0].set(i[0],i[1]);
                    });
                }
                windows[0].trigger('refresh');
                return windows[0];
            } else {
                var WindowType = this.window_classes[name + '/' + subname] || SKWindow;
                var win = new WindowType(_.extend({name:name, subname:subname}, params));
                win.open();
                return win;
            }
        },

        'hideWindow':function (win) {
            this.remove(win);
            win.deactivate();
            if (this.length) {
                this.at(this.length - 1).activate();
            }
        },
        //TODO:работает?
        'closeAll':function () {
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
        'deactivateActiveWindow':function () {
            this.getActiveWindow().deactivate();
        },
        'getActiveWindow':function () {
            var count = this.models.length;
            if(count > 0) {
                return this.models[count-1];
            }else{
                throw new Error("No active windows!!");
            }
        }

    });
})();