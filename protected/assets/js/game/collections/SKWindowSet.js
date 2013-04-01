/*global console, Backbone, SKWindow, SKDialogWindow, SKApp, _*/
define([
    "game/models/window/SKWindow",
    "game/models/window/SKDialogWindow"
], function () {
    "use strict";
    /**
     * Оконный менеджер, содержит в себе все окна
     *
     * @class SKWindowSet
     * @augments Backbone.Collection
     */
    window.SKWindowSet = Backbone.Collection.extend({

        /**
         * @property model
         * @type SKWindow
         * @default SKWindow
         */
        model:          SKWindow,

        /**
         * @property window_classes
         * @type array
         * @default array
         */
        window_classes: {
            'phone/phoneTalk':     SKDialogWindow,
            'phone/phoneCall':     SKDialogWindow,
            'visitor/visitorTalk': SKDialogWindow
        },

        /**
         * Constructor
         *
         * @method initialize
         * @param models
         * @param options
         * @return void
         * @throw Exception
         */
        'initialize': function (models, options) {
            if (options.events === undefined) {
                throw 'SKWindowSet requires events';
            }
            options.events.on('event:phone:in_progress', function (event) {
                this.toggle('phone', 'phoneCall', {sim_event: event});
            }, this);
            options.events.on('event:visit:in_progress', function (event) {
                this.toggle('visitor', 'visitorEntrance', {sim_event: event});
            }, this);
            options.events.on('event:immediate-visit', function (event) {
                this.open('visitor', 'visitorTalk', {sim_event: event});
                event.setStatus('in progress');
            }, this);
            options.events.on('event:immediate-phone', function (event) {
                this.open('phone', 'phoneTalk', {sim_event: event});
                event.setStatus('in progress');
            }, this);
            options.events.on('event:mail-send', function (event) {
                if (event.get('fantastic')) {
                    var simulation = SKApp.simulation;
                    simulation.startInputLock();

                    if (!simulation.mailClient.view || !simulation.mailClient.view.render_finished) {
                        simulation.window_set.open(
                            'mailEmulator',
                            simulation.mailClient.getActiveSubscreenName()
                        );
                        simulation.mailClient.view.on('render_finished', function () {
                            SKApp.simulation.mailClient.sendFantasticMail(event.get('mailFields'));
                        });
                    } else {
                        SKApp.simulation.mailClient.sendFantasticMail(event.get('mailFields'));
                    }
                    simulation.mailClient.on('mail:fantastic-send:complete', function () {
                        SKApp.simulation.stopInputLock();
                    });


                }
            });
            options.events.on('event:mail', function (event) {
                if (event.get('fantastic')) {
                    var simulation = SKApp.simulation;
                    simulation.startInputLock();

                    if (!simulation.mailClient.view || !simulation.mailClient.view.render_finished) {
                        simulation.window_set.open(
                            'mailEmulator',
                            simulation.mailClient.getActiveSubscreenName()
                        );
                        simulation.mailClient.view.on('render_finished', function () {
                            SKApp.simulation.mailClient.openFantasticMail(event.get('mailFields'));
                        });
                    } else {
                        SKApp.simulation.mailClient.openFantasticMail(event.get('mailFields'));
                    }
                    simulation.mailClient.on('mail:fantastic-open:complete', function () {
                        SKApp.simulation.stopInputLock();
                    });


                }
            });
            this.on('add', function (win) {
                var zIndex = -1;
                this.each(function (window) {
                    zIndex = Math.max(window.get('zindex') !== undefined ? window.get('zindex') : -1, zIndex);
                });
                win.set('zindex', zIndex + 1);

            }, this);

        },

        /**
         * @method comparator
         * @param window
         * @returns integer
         */
        comparator: function (window) {
            return this.get('zindex');
        },

        /**
         * Добавляет в список окон окно, активирует его и деактивирует предыдущее (если было)
         *
         * @method showWindow
         * @param {SKWindow} win
         * @method showWindow
         * @return void
         */
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

        /**
         * @method toggle
         * @param name
         * @param subname
         * @param params
         * @return void
         */
        toggle: function (name, subname, params) {
            // protect against 2 open phone windows at the same time
            if (name === 'phone') {
                this.closeAllPhoneInstances();
            }

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
         *
         * @method open
         * @param name
         * @param subname
         * @param params
         * @return SKWindow
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

        /**
         * @method hideWindow
         * @param win
         * @return void
         */
        'hideWindow': function (win) {
            this.remove(win);
            win.deactivate();
            if (this.length) {
                this.at(this.length - 1).activate();
            }
        },

        /**
         * @method closeAll
         * @return void
         */
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
        },

        /**
         * We need it to protect against two opened phone windows in same time
         *
         * @method closeAllPhoneInstances
         * @returns void
         */
        closeAllPhoneInstances: function () {
            for (var i in SKApp.simulation.window_set.models) {
                if ('phone' == SKApp.simulation.window_set.models[i].get('name')){
                    SKApp.simulation.window_set.models[i].close();
                }
            }
        }

    });
});
