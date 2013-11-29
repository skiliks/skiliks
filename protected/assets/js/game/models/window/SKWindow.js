/*global Backbone:false, console, SKApp, _ */

var SKWindow;
define([], function () {
    "use strict";
    var screens = {
        'mainScreen':1,
        'plan':3,
        'mailEmulator':10,
        'phone':20,
        'visitor':30,
        'documents':40,
        'browser': 50
    };
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
    /**
     * Окно, открывается, закрывается, ведет за собой логи
     * @augments Backbone.Model
     * @class SKWindow
     */
    SKWindow = Backbone.Model.extend({

        single: true,

        name: undefined,

        subname: undefined,

        window_set: {},

        window_uid: undefined,

        /**
         * Constructor
         * @method initialize
         * @constructs
         */
        initialize: function () {
            try {

                // иногда обьект SKWindow приходит с id - что удивительно {
                // и mainScreen и subname == undefined
                // причину этого бага мы пока не нашли,
                // но если такое случается, то игра становится заблокированной, что недопустимо
                //
                // - поэтому лечим последствия
                if ('mainScreen' == this.get('id') || 'mainScreen' == this.get('subname')) {
                    this.set('id', 'mainScreen');
                    this.set('name', undefined);
                    this.set('subname', undefined);
                    console.error('Undefined name or subname.');
                }
                if ('manual' == this.get('id') || 'manual' == this.get('subname')) {
                    this.set('id', 'manual');
                    this.set('name', undefined);
                    this.set('subname', undefined);
                    console.error('Undefined name or subname.');
                }

                if ('undefined' == typeof this.get('name') && 'undefined' == typeof this.get('subname')) {
                    if ('mainScreen' == this.get('id')) {
                        this.set('name', 'mainScreen');
                        this.set('subname', 'mainScreen');
                    }
                    if ('manual' == this.get('id')) {
                        this.set('name', 'mainScreen');
                        this.set('subname', 'manual');
                    }

                    if (window.Raven) {
                        window.Raven.captureMessage(
                            'Warning! SKWindow name and subname is undefined. Id is ' + this.get('id')
                        );
                    }
                }
                // иногда обьект SKWindow приходит с id - что удивительно }

                var window_id = this.get('name') + "/" + this.get('subname');
                if (window_id in SKApp.simulation.window_set) {
                    throw new Error ("Window " + window_id + " already exists");
                }
                if (! (this.get('name') in screens)) {
                    console.log('initialize: ', this);
                    throw new Error ('Unknown screen ' + this.get('name') + ', window: ' + window_id
                        + ', subname: ' + this.get('subname')+ ', id: ' + this.get('id'));
                }
                if (! (this.get('subname') in screensSub)) {
                    throw new Error ('Unknown subscreen ' + this.get('subname') + ', window: ' + window_id + ', screen: ' +  + this.get('name'));
                }
                if (!this.has('id')) {
                    this.set('id', this.get('subname'));
                }

                this.updateUid();

                this.is_opened = false;
                this.simulation = SKApp.simulation;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @returns {*}
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
         * @method
         * @returns {*}
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
         * @method
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
         * Opens a window
         * @method open
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
         * @method
         */
        close: function() {
            try {
                if (!this.is_opened) {
                    throw new Error ("Window is already closed");
                }
                this.trigger('pre_close');
                if (this.prevent_close === true) {
                    delete this.prevent_close;
                    return;
                }
                this.is_opened = false;
                SKApp.simulation.window_set.hideWindow(this);
                this.trigger('close');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
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
         * @param params
         * @method deactivate
         */
        deactivate: function (params) {
            try {
                params = params || {};

                if (!params.silent) {
                    this.trigger('deactivate');
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
         * @param params
         * @method activate
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
