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
            'documents':41,
            'documentsFiles':42,
            'browserMain': 51
    };
    /**
     * Окно, открывается, закрывается, ведет за собой логи
     * @class SKWindow
     * @augments Backbone.Model
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
         */
        initialize: function () {
            var window_id = this.get('name') + "/" + this.get('subname');
            if (window_id in SKApp.simulation.window_set) {
                throw "Window " + window_id + " already exists";
            }
            if (! (this.get('name') in screens)) {
                throw 'Unknown screen';
            }
            if (! (this.get('subname') in screensSub)) {
                throw 'Unknown subscreen';
            }
            if (!this.has('id')) {
                this.set('id', this.get('subname'));
            }

            this.updateUid();

            this.is_opened = false;
            this.simulation = SKApp.simulation;
        },

        /**
         * @method
         * @returns {*}
         */
        'getWindowId': function () {
            return screens[this.get('name')];
        },

        /**
         * @method
         * @returns {*}
         */
        'getSubwindowId': function () {
            return screensSub[this.get('subname')];
        },

        /**
         * @method
         */
        'updateUid': function() {
            this.window_uid = _.uniqueId();
        },

        /**
         * Opens a window
         * @method open
         */
        open: function() {
            if (this.is_opened) {
                throw "Window is already opened";
            }
            this.is_opened = true;
            this.simulation.window_set.showWindow(this);
            /**
             * Вызывается в момент открытия окна. View должен отрисовать окно в этот момент
             * @event open
             */
            this.trigger('open', this.get('name'), this.get('subname'));
        },

        /**
         * @method
         */
        close: function() {
            if (!this.is_opened) {
                throw "Window is already closed";
            }
            this.trigger('pre_close');
            if (this.prevent_close === true) {
                delete this.prevent_close;
                return;
            }
            this.is_opened = false;
            SKApp.simulation.window_set.hideWindow(this);
            this.trigger('close');
        },

        /**
         * @method
         */
        setOnTop:function () {
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
                zIndex ++;
            });
            me.set('zindex', zIndex);
            window_set.add(me, {silent:true});
            window_set.sort();
            me.activate();
        },

        /**
         * Деактивирует окно (посылает лог об этом)
         * @param params
         * @method deactivate
         */
        deactivate: function (params) {
            params = params || {};

            if (!params.silent) {
                this.trigger('deactivate');
            }
            this.simulation.windowLog.deactivate(this);
        },

        /**
         * Активирует окно и пишет об этом в лог
         *
         * @param params
         * @method activate
         */
        activate: function (params) {
            params = params || {};

            if (!params.silent) {
                this.trigger('activate');
            }
            this.simulation.windowLog.activate(this);
        }
    });

    return SKWindow;
});
