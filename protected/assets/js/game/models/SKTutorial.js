/*global define, $, _ */

var SKTutorial;

define([
    "game/models/SKSimulation"
], function (
    SKSimulation
) {
    "use strict";

    SKTutorial = SKSimulation.extend({
        timerSpeed: 6000,

        getNewEvents: function (cb) {
            try {
                this.windowLog.getAndClear();
                if (cb) {
                    cb();
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        getGameTime: function(is_seconds) {
            try {
                var sh = this.timeStringToMinutes(this.get('app').get('start')) * 60;
                var h = Math.floor(sh / 3600);
                var m = Math.floor((sh - (h * 3600)) / 60);
                var s = sh - (h * 3600) - (m * 60);
                if (h   < 10) {h   = "0"+h;}
                if (m < 10) {m = "0"+m;}
                if (s < 10) {s = "0"+s;}
                return h + ':' + m + (is_seconds ? ':' + s : '');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

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

        stop: function () {
            try {
                var me = this;

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

        onEndTime: function() {},

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