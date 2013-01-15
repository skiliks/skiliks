/*global Backbone:false, console, SKApp, SKConfig, SKWindowSet, SKWindow, SKEventCollection, SKEvent, SKWindowLog, SKMailClient */
/*global SKTodoCollection, SKDayTaskCollection, SKPhoneHistoryCollection */

(function () {
    "use strict";
    function timeStringToMinutes(str) {
        var parts = str.split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    /**
     * Simulation class
     * TODO: enable logging
     * @type {SKSimulation}
     */
    window.SKSimulation = Backbone.Model.extend({
        'initialize':function () {
            this.events = new SKEventCollection();
            this.todo_tasks = new SKTodoCollection();
            this.phone_history = new SKPhoneHistoryCollection();
            this.events.on('event:plan', function () {
                SKApp.user.simulation.todo_tasks.fetch();
            });
            this.on('tick', function () {
                //noinspection JSUnresolvedVariable
                if (this.getGameMinutes() >= timeStringToMinutes(SKConfig.simulationEndTime)) {
                    this.stop();
                }
            });
            this.dayplan_tasks = new SKDayTaskCollection();
            this.windowLog = new SKWindowLog();
            this.skipped_minutes = 0;
            this.mailClient = new SKMailClient();
        },
        /**
         * Returns number of minutes past from the start of game
         */
        'getGameMinutes':function () {
            var time_string = new Date();
            var game_start_time = timeStringToMinutes(SKConfig.simulationStartTime);
            return game_start_time +
                Math.floor((time_string - this.start_time) / ( 1000 * 60) * SKConfig.skiliksSpeedFactor) +
                this.skipped_minutes;
        },
        'getGameTime':function () {
            function pad(num) {
                var s = "0" + num;
                return s.substr(s.length - 2);
            }

            var mins = this.getGameMinutes();
            var hours = Math.floor(mins / 60);
            if (hours > 24) {
                throw 'Simulation must be stopped at this time';
            }
            var minutes = (mins % 60);
            return pad(hours) + ':' + pad(minutes);
        },

        parseNewEvents:function (events) {
            var me = this;
            events.forEach(function (event) {
                console.log('[SKSimulation] new event ' + event.eventType);
                console.log(event.data);
                if (event.eventType === 1 && (event.data === undefined || event.data.length === 0)) {
                    // Crutch, sometimes server returns empty events
                    me.events.trigger('dialog:end');
                    return;
                }
                var event_model = new SKEvent({
                    type:event.eventType,
                    data:event.data
                });
                me.events.push(event_model);
                me.events.trigger('event:' + event_model.getTypeSlug(), event_model);

            });
        },
        'getNewEvents':function () {
            var me = this;
            var logs = this.windowLog.getAndClear();
            SKApp.server.api('events/getState', {
                logs:logs,
                timeString:this.getGameMinutes()
            }, function (data) {
                if (data.result === 1 && data.events !== undefined) {
                    me.parseNewEvents(data.events, 'new');
                    me.getNewEvents();
                }
            });
        },
        'start':function () {
            var me = this;
            me.start_time = new Date();
            this.window_set = new SKWindowSet();
            var win = this.window = new SKWindow({name:'mainScreen', subname:'mainScreen'});
            win.open();
            SKApp.server.api('simulation/start', {'stype':this.get('stype')}, function () {
                me.todo_tasks.fetch();
                me.dayplan_tasks.fetch();
                me.trigger('start');
                
                me.events_timer = setInterval(function () {
                    me.getNewEvents();
                    me.trigger('tick');
                }, 60000 / SKConfig.skiliksSpeedFactor);
            });
        },
        'stop':function () {
            var me = this;
            clearInterval(this.events_timer);
            this.window_set.closeAll();
            this.window.close();
            var logs = this.windowLog.getAndClear();
            SKApp.server.api('simulation/stop', {'logs':logs}, function () {
                me.trigger('stop');
            });
        },
        'setTime':function (hour, minute) {
            var me = this;
            SKApp.server.api('simulation/changeTime', {
                'hour':hour,
                'min':minute
            }, function () {
                me.skipped_minutes =
                    parseInt(hour, 10) * 60 + parseInt(minute, 10) - me.getGameMinutes() + me.skipped_minutes;
                me.trigger('tick');
            });
        },
        'isDebug':function () {
            return parseInt(this.get('stype'), 10) === 2;
        }
    });
})();