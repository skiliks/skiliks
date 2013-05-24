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
            this.windowLog.getAndClear();
            if (cb) {
                cb();
            }
        },

        getGameTime: function(is_seconds) {
            var sh = this.timeStringToMinutes(this.get('app').get('start')) * 60;
            var h = Math.floor(sh / 3600);
            var m = Math.floor((sh - (h * 3600)) / 60);
            var s = sh - (h * 3600) - (m * 60);
            if (h   < 10) {h   = "0"+h;}
            if (m < 10) {m = "0"+m;}
            if (s < 10) {s = "0"+s;}
            return h + ':' + m + (is_seconds ? ':' + s : '');
        },

        getTutorialTime: function() {
            var passed = this.getGameSeconds() - this.timeStringToMinutes(this.get('app').get('start')) * 60,
                from = this.timeStringToMinutes(this.get('app').get('start')) * 60,
                to   = this.timeStringToMinutes(this.get('app').get('end')) * 60,
                left = Math.max(0, (to - from - passed) / this.get('app').get('skiliksSpeedFactor')),
                minutes = Math.floor(left / 60),
                seconds = Math.floor(left % 60);

            return minutes + ':' + (seconds < 10 ? '0' + seconds : seconds);
        },

        stop: function () {
            var me = this;

            SKApp.server.api('simulation/stop', {}, function () {
                $.each(SKDocument._excel_cache, function(id, url){
                    $('#excel-preload-' + id).remove();
                });

                me.trigger('before-stop');
                me.trigger('force-stop');
                $('.mail-popup-button').show();
                localStorage.removeItem('lastGetState');
            });
        },

        onEndTime: function() {},

        onFinishTime: function() {
            this._stopTimer();
            this.stop();
        }
    });

    return SKTutorial;
});