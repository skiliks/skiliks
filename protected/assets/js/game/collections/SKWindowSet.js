/*global console, Backbone, SKWindow, SKDialogWindow, SKApp, _, define */
define([
    "game/models/window/SKWindow",
    "game/models/window/SKDialogWindow"
], function (SKWindow, SKDialogWindow) {
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
            'visitor/visitorTalk': SKDialogWindow,
            'visitor/visitorEntrance':SKDialogWindow
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
            try {
                if (options.events === undefined) {
                    throw new Error('SKWindowSet requires events');
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
                            var windows = SKApp.simulation.window_set.where({name:'mailEmulator'});
                            windows[0].setOnTop();
                            SKApp.simulation.mailClient.sendFantasticMail(event.get('mailFields'));
                        }
                        simulation.mailClient.on('mail:fantastic-send:complete', function () {
                            SKApp.simulation.stopInputLock();
                        });


                    }
                });
                options.events.on('event:mail', function (event) {
                    options.events.getUnreadMailCount();
                    if (event.get('fantastic')) {
                        var simulation = SKApp.simulation;
                        simulation.startInputLock();

                        if (!simulation.mailClient.view || !simulation.mailClient.view.render_finished) {
                            // nothing
                        } else {
                            var windows = SKApp.simulation.window_set.where({name:'mailEmulator'});
                            simulation.mailClient.view.setForcedClosing();

                            windows[0].setOnTop();
                            windows[0].close(
                                'mailEmulator',
                                simulation.mailClient.getActiveSubscreenName()
                            );
                        }

                        simulation.window_set.open(
                            'mailEmulator',
                            simulation.mailClient.getActiveSubscreenName()
                        );
                        simulation.mailClient.view.on('render_finished', function () {

                            var email_row = simulation.mailClient.view.$('.email-list-line[data-email-id=' + event.get('id') + ']');
                            if (!email_row.hasClass('active')) {
                                email_row.click();
                            }

                            SKApp.simulation.mailClient.openFantasticMail(event.get('mailFields'));
                        });

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

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }

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
            try {
                if (win.single === true && this.get(win)) {
                    throw 'Window already displayed';
                }
                if (this.length) {
                    this.at(this.length - 1).deactivate();
                }
                if (this.get(win.id)) {
                    throw 'Trying to add window with same ID';
                }
                this.add(win);
                win.activate();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        getWindow: function(name, subname) {
            try {
                var windows = this.where(subname ? {name: name, subname: subname} : {name: name});

                return windows.length ? windows[0] : null;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method toggle
         * @param name
         * @param subname
         * @param params
         * @return void
         */
        toggle: function (name, subname, params) {
            try {
                // protect against 2 open phone windows at the same time
                if (name === 'phone') {
                    this.closeAllPhoneInstances(name, subname);
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        makeCloseAndOpen: function (name, subname, params) {
            try {

                console.log("lol");
                // protect against 2 open phone windows at the same time
                var windows = this.where({name: name, subname: subname});
                console.log(windows);
                if (windows.length !== 0) {
                        windows[0].trigger('refresh');
                        windows[0].close();
                        windows[0].open();
                    }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        isOpen:function(name, subname) {
            try {
                var windows = this.where(subname ? {name: name, subname: subname} : {name: name});
                if (windows.length === 0) {
                    return false;
                }else{
                    return true;
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        isActive:function(name, subname) {
            try {
                var window = this.getActiveWindow();

                return (window.get('name') === name && window.get('subname') === subname);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**doActivate
         * Just opens window or nothing if opened
         *
         * @method open
         * @param name
         * @param subname
         * @param params
         * @return SKWindow
         */
        open: function (name, subname, params) {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method hideWindow
         * @param win
         * @return void
         */
        'hideWindow': function (win) {
            try {
                this.remove(win);
                win.deactivate();
                if (this.length) {
                    this.at(this.length - 1).activate();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method closeAll
         * @return void
         */
        'closeAll':   function () {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        deactivateActiveWindow: function () {
            try {
                this.getActiveWindow().deactivate();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        getActiveWindow: function () {
            try {
                var count = this.models.length;
                if (count > 0) {
                    return this.models[count - 1];
                } else {
                    throw new Error("No active windows!!");
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        hasActiveXLSWindow: function () {
            try {
                var count = this.models.length;
                if (count > 0) {
                    var model = this.models[count - 1];
                    return (model.get('document') !== undefined) && (model.get('document').get('mime') === 'application/vnd.ms-excel');
                } else {
                    throw new Error("No active windows!!");
                }

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * We need it to protect against two opened phone windows in same time
         *
         * Но самоубийство этот метод при этом не делает — если скрывается окно, то его не закрывает
         *
         * @method closeAllPhoneInstances
         * @returns void
         */
        closeAllPhoneInstances: function (name, subname) {
            try {
                SKApp.simulation.window_set.each(function (window) {
                    if ('phone' === window.get('name') && 'phone' === name && window.get('subname') !== subname){
                        window.close();
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }

    });
    return window.SKWindowSet;
});
