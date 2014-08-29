/*global Backbone:false, console, SKApp, _ */

var SKWindow;
define([], function () {

    "use strict";

    /** @var Array */
    var screens = {
        'mainScreen':1,
        'plan':3,
        'mailEmulator':10,
        'phone':20,
        'visitor':30,
        'documents':40
    };

    /** @var Array */
    var screensSub = {
        'mainScreen':1,
        'manual':2,
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
        'meetingChoice':33,
        'meetingGone':34,
        'documents':41,
        'documentsFiles':42
    };

    var screensSubToScreen = {
        'mainScreen':'mainScreen',
        'manual':'mainScreen',
        'plan':'plan',
        'mailMain':'mailEmulator',
        'mailPreview':'mailEmulator',
        'mailNew':'mailEmulator',
        'mailPlan':'mailEmulator',
        'phoneMain':'phone',
        'phoneTalk':'phone',
        'phoneCall':'phone',
        'visitorEntrance':'visitor',
        'visitorTalk':'visitor',
        'meetingChoice':'visitor',
        'meetingGone':'visitor',
        'documents':'documents',
        'documentsFiles':'documents'
    };

    /**
     * Окно, открывается, закрывается, ведет за собой логи
     * @augments Backbone.Model
     * @class SKWindow
     */
    SKWindow = Backbone.Model.extend({

        /** @var Boolean */
        single: true,

        /** @var String */
        name: undefined,

        /** @var String */
        subname: undefined,

        /** @var Array */
        window_set: {},

        /** @var Number */
        window_uid: undefined,

        /**
         * Constructor
         * @method initialize
         * @constructs
         */
        initialize: function () {
            try {
                var me = this;
                var message = "SKWindow.initialize " + me.get('subname')
                    + " cid: " + me.cid
                    + ". game time: " + SKApp.simulation.getGameTime();
                // иногда обьект SKWindow приходит с id - что удивительно {
                // и mainScreen и subname == undefined
                // причину этого бага мы пока не нашли,
                // но если такое случается, то игра становится заблокированной, что недопустимо
                //
                // - поэтому лечим последствия
                if ('undefined' == typeof me.get('name') && 'undefined' == typeof me.get('subname')) {
                    if ('mainScreen' == me.get('id')) {
                        me.set('name', 'mainScreen');
                        me.set('subname', 'mainScreen');
                              }
                    if ('manual' == me.get('id')) {
                        me.set('name', 'mainScreen');
                        me.set('subname', 'manual');
                    }
                }
                // иногда обьект SKWindow приходит с id - что удивительно }

                var window_id = me.get('name') + "/" + me.get('subname');
                if (window_id in SKApp.simulation.window_set) {
                    if (window.Raven) {
                        window.Raven.captureMessage("Window " + window_id + " already exists");
                    }
                }

                if (!(me.get('name') in screens)) {
                    if (window.Raven) {
                        window.Raven.captureMessage('Unknown screen ' + me.get('name') + ', window: ' + window_id
                            + ', subname: ' + me.get('subname')+ ', id: ' + me.get('id'));
                    }
                    this.set('name', screensSubToScreen[me.get('id')]);
                }

                if (!(me.get('subname') in screensSub)) {
                    if (window.Raven) {
                        window.Raven.captureMessage('Unknown subscreen ' + me.get('subname')
                            + ', window: ' + window_id + ', screen: ' +  + me.get('name'));
                    }
                    me.set('subname', me.get('id'));
                }

                if (!me.has('id')) {
                    me.set('id', me.get('subname'));
                }

                me.updateUid();

                me.is_opened = false;

                me.simulation = SKApp.simulation;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @returns {Number}
         */
        'getWindowId': function () {
            try {
                return screens[this.get('name')];
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @returns {Number}
         */
        'getSubwindowId': function () {
            try {
                return screensSub[this.get('subname')];
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Генерирует window_uid
         */
        'updateUid': function() {
            try {
                this.window_uid = _.uniqueId();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается перед открытием окна
         */
        open: function() {
            try {
                var me = this;
                if (me.is_opened) {
                    throw new Error ("Window is already opened");
                }
                me.is_opened = true;

                me.simulation.window_set.showWindow(me);
                /**
                 * Вызывается в момент открытия окна. View должен отрисовать окно в этот момент
                 * @event open
                 */
                me.trigger('open', me.get('name'), me.get('subname'));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается перед закрытием окна
         */
        close: function() {
            try {
                var me = this;

                if (!me.is_opened) {
                    var message = "Window is already closed. Name: " + this.get('name')
                        + " subname: " + this.get('subname')
                        + " id: " + this.get('id')
                        + ". game time: " + SKApp.simulation.getGameTime();
                }

                // пока это мобытие слушает только MailClient
                me.trigger('pre_close');

                if (me.prevent_close === true) {
                    delete me.prevent_close;

                    if (window.Raven) {
                        window.Raven.captureMessage(
                            'L. prevent_close ' + SKApp.simulation.is
                            + '. ' + this.get('name')
                            + " subname: " + this.get('subname')
                            + " id: " + this.get('id')
                            + ". game time: " + SKApp.simulation.getGameTime()
                        );
                    }

                    return;
                }

                me.is_opened = false;

                SKApp.simulation.window_set.hideWindow(this);

                me.trigger('close');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Служебный сетод, вызывется при открытии окна,
         * меняет z-index так чтобы окно стало самым верхним
         */
        setOnTop:function () {
            try {
                var me = this;
                var window_set = SKApp.simulation.window_set;
                if (window_set.length === 1 || window_set.at(window_set.length - 1).id === this.id) {
                    return;
                }
                window_set.at(window_set.length - 1).deactivate();
                window_set.remove(me, {silent:true});
                var zIndex = 0;
                window_set.each(function (window) {
                    window.set('zindex', zIndex);
                    zIndex += 1;
                });
                me.set('zindex', zIndex);
                window_set.add(me, {silent:true});
                window_set.sort();
                me.activate();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Деактивирует окно (посылает лог об этом)
         *
         * @param Array params, по сути params.silent
         */
        deactivate: function (params) {
            try {
                params = params || {};

                if (!params.silent) {
                    this.trigger('deactivate');
                }
                if (undefined == typeof this.simulation) {
                    if (window.Raven) {
                        window.Raven.captureMessage('simulation is undefined for ' + JSON.stringify(this));
                    }

                    this.simulation = SKApp.simulation;
                }
                this.simulation.windowLog.deactivate(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Активирует окно и пишет об этом в лог
         *
         * @param Array params, по сути params.silent
         */
        activate: function (params) {
            try {
                params = params || {};

                if (!params.silent) {
                    this.trigger('activate');
                }
                this.simulation.windowLog.activate(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKWindow;
});
