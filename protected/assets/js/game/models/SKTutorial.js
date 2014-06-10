/*global define, $, _ */

var SKTutorial;

define([
    "game/models/SKSimulation"
], function (
    SKSimulation
) {
    "use strict";

    SKTutorial = SKSimulation.extend({

        /** @var number */
        timerSpeed: 6000,

        /**
         * В тутириале нет флагов, дев режима, прочее
         * - упрощаем приём событий чтоб не эмулировать всё чего нет
         *
         * @param function callback
         */
        getNewEvents: function (callback) {
            try {
                this.windowLog.getAndClear();
                var me = this;

                SKApp.server.apiQueue('events/getState', {
                    logs:             [],
                    timeString:       this.getGameMinutes(),
                    eventsQueueDepth: $("#events-queue-depth").val()
                }, function (data) {});

                if (callback) {
                    callback();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Делаем основные часы недвижимимы, на чих всегда 9:45.
         * Можно было бы просто вернуть 9:45, а не считать,
         * но время когда "стартует" может быть изменено в сценарии
         *
         * @param boolean is_seconds
         * @returns {string}
         */
        getGameTime: function(is_seconds) {
            try {
                var sh = this.timeStringToMinutes(this.get('app').get('start')) * 60;
                var h = Math.floor(sh / 3600);
                var m = Math.floor((sh - (h * 3600)) / 60);
                var s = sh - (h * 3600) - (m * 60);
                if (h   < 10) { h  = "0" + h; }
                if (m < 10) { m = "0" + m; }
                if (s < 10) { s = "0" + s; }
                return h + ':' + m + (is_seconds ? ':' + s : '');
                return h + ':' + m + (is_seconds ? ':' + s : '');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Для реализации таймера используется стандартный метод this.getGameSeconds(),
         * но так как это всемя идёт в сторону увеличения, то нам нужно "развернуть"
         * для таймера обратного отсчёта
         *
         * @returns {string}
         */
        getTutorialTime: function() {
            try {
                var passed = this.getGameSeconds() - this.timeStringToMinutes(this.get('app').get('start')) * 60,
                    from = this.timeStringToMinutes(this.get('app').get('start')) * 60,
                    to   = this.timeStringToMinutes(this.get('app').get('end')) * 60,
                    left = Math.max(0, (to - from - passed) / 6),
                    minutes = Math.floor(left / 60),
                    seconds = Math.floor(left % 60);

                return minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * В туториале симстоп проще
         */
        stop: function () {
            try {
                var me = this;
                SKApp.server.requests_timeout = SKApp.get("simStopTimeout");
                SKApp.server.api('simulation/stop', {}, function () {
                    $.each(SKDocument._excel_cache, function(id, url){
                        $('#excel-preload-' + id).remove();
                    });

                    me.trigger('before-stop');
                    me.trigger('user-agree-with-sim-stop');
                    $('.mail-popup-button').show();
                    localStorage.removeItem('lastGetState');
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * В 18:00 ничего не происхлдит, это примерно 7:00 по таймеру
         */
        onEndTime: function() {},

        /**
         * 0:00
         * Игроку не предоставляется никакого выбора
         */
        onFinishTime: function() {
            try {
                this._stopTimer();
                this.stop();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKTutorial;
});