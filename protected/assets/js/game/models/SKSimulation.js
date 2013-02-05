/*global Backbone:false, console, SKApp, SKConfig, SKWindowSet, SKWindow, SKEventCollection, SKEvent, SKWindowLog, SKMailClient */
/*global SKTodoCollection, SKDayTaskCollection, SKPhoneHistoryCollection, SKDocumentCollection */

(function () {
    "use strict";
    function timeStringToMinutes(str) {
        var parts = str.split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    /**
     * Simulation class
     * @type {Backbone.Model}
     */
    window.SKSimulation = Backbone.Model.extend(
        /** @lends SKSimulation.prototype */
        {
            'initialize':function () {
                var me = this;

                this.events = new SKEventCollection();
                this.todo_tasks = new SKTodoCollection();
                this.phone_history = new SKPhoneHistoryCollection();
                this.events.on('event:plan', function () {
                    SKApp.user.simulation.todo_tasks.fetch();
                });
                this.on('tick', function () {
                    //noinspection JSUnresolvedVariable
                    if (me.getGameMinutes() >= timeStringToMinutes(SKConfig.simulationEndTime)) {
                        SKApp.user.stopSimulation();
                    }

                    // 11-00
                    if (660 === me.getGameMinutes()) {
                        me.trigger('time:11-00');
                    }
                });
                this.dayplan_tasks = new SKDayTaskCollection();
                this.documents = new SKDocumentCollection();
                this.windowLog = new SKWindowLog();
                this.skipped_minutes = 0;
                this.mailClient = new SKMailClient();

                this.config = [];
                this.config.isMuteVideo = false;

                this.on('time:11-00', function () {
                    me.off('time:11-00');
                    SKApp.server.api('dayPlan/CopyPlan', {
                        minutes:me.getGameMinutes()
                    }, function () {
                    });
                });
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
            /**
             * Return game time in seconds
             * @return {Number}
             */
            'getGameSeconds':function () {
                var current_time_string = new Date();
                var game_start_time = timeStringToMinutes(SKConfig.simulationStartTime) * 60;
                return game_start_time +
                    Math.floor((current_time_string - this.start_time) / 1000 * SKConfig.skiliksSpeedFactor) +
                    this.skipped_minutes * 60;
            },
            'getGameTime':function (is_seconds) {
                is_seconds = is_seconds || false;
                function pad(num) {
                    var s = "0" + num;
                    return s.substr(s.length - 2);
                }
                if(is_seconds) {

                    var sh    = this.getGameSeconds();
                    var h   = Math.floor(sh / 3600);
                    var m = Math.floor((sh - (h * 3600)) / 60);
                    var s = sh - (h * 3600) - (m * 60);

                    if (h   < 10) {h   = "0"+h;}
                    if (m < 10) {m = "0"+m;}
                    if (s < 10) {s = "0"+s;}
                    var time    = h+':'+m+':'+s;
                    return time;

                }else{
                    var mins = this.getGameMinutes();
                    var hours = Math.floor(mins / 60);
                    if (hours > 24) {
                        throw 'Simulation must be stopped at this time';
                    }
                    var minutes = (mins % 60);
                    return pad(hours) + ':' + pad(minutes);
                }

            },

            /**
             * Parses new events and adds them to event collection
             *
             * @param {Array.<Object>} events
             */
            parseNewEvents:function (events) {
                var me = this;
                events.forEach(function (event) {
                    //console.log('[SKSimulation] new event ', event.eventType, event.data);
                    if (event.eventType === 1 && (event.data === undefined || event.data.length === 0)) {
                        // Crutch, sometimes server returns empty events
                        me.events.trigger('dialog:end');
                        return;
                    }
                    var event_model = new SKEvent({
                        type:event.eventType,
                        data:event.data
                    });
                    if (me.events.canAddEvent(event_model)) {
                        me.events.push(event_model);
                    } else {
                        me.events.triggerEvent(event.data[0].code, 3, 0, 0);
                    }
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
                    // update flags for dev mode
                    if (undefined !== data.flagsState && undefined !== data.serverTime) {
                        me.updateFlagsForDev(data.flagsState, data.serverTime);
                    }

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
                SKApp.server.api('simulation/start', {'stype':this.get('stype')}, function (data) {
                    if (data.result === 0) {
                        alert('Ошибка при запуске симуляции.');
                        SKApp.user.stopSimulation();
                    }
                    
                    if ('undefined' !== typeof data.simId) {
                        me.id = data.simId;
                    }
                    me.todo_tasks.fetch();
                    me.dayplan_tasks.fetch();
                    if (!me.isDebug()) {
                        me.documents.fetch();
                    }
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

                this.window_set.deactivateActiveWindow();

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
            },
            updateFlagsForDev:function (flagsState, serverTime) {
                // Please, don't do that
                var flagStateView = new SKFlagStateView();
                flagStateView.updateValues(flagsState, serverTime);
            }
        });
})();