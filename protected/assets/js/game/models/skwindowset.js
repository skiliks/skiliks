/*global console, Backbone*/
(function() {
    "use strict";
    window.SKWindowSet = Backbone.Model.extend({
        'initialize':function () {
            this.window_zindex = [];
        },
        'showWindow':function (win) {
            this.window_zindex.filter(function (v) {
                if (v === win) {
                    throw 'Window already displayed';
                }
            });
            if (this.window_zindex.length) {
                this.window_zindex[this.window_zindex.length - 1].deactivate();
            }
            this.window_zindex.push(win);
            win.activate();
        },

        'hideWindow':function (win) {
            var windows_found = 0;
            this.window_zindex = this.window_zindex.filter(function (v) {
                if (v === win) {
                    windows_found++;
                    return false;
                } else {
                    return true;
                }
            });
            if (windows_found !== 1) {
                throw 'Found ' + windows_found + ' window(s) ' + win.name + '/' + win.subname + ' expected 1';
            }
            win.deactivate();
            if (this.window_zindex.length) {
                this.window_zindex[this.window_zindex.length - 1].activate();
            }
        },

        'closeAll':function () {
            var name;
            if (arguments.length === 1) {
                name = arguments[0];
            }
            var windows_list = this.window_zindex.filter(function () {
                return true;
            });
            for (var i = windows_list.length - 1; i >= 0; i--) {
                var win = windows_list[i];
                if (name ? win.name === name : win.name !== 'mainScreen') {
                    win.close();
                }
            }
        }
    });
})();