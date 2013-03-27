/*global Backbone:false, console, SKApp, SKConfig, SKWindowSet, SKWindow, SKEventCollection, SKEvent, SKWindowLog, SKMailClient */
/*global SKTodoCollection, SKDayTaskCollection, SKPhoneHistoryCollection, SKDocumentCollection */

var SKSimulation;

define([
    "game/models/SKMailClient",
    "game/views/develop_mode/SKFlagStateView",

    "game/collections/SKEventCollection",
    "game/models/SKEvent",
    "game/collections/SKTodoCollection",
    "game/collections/SKPhoneHistoryCollection",
    "game/collections/SKDayTaskCollection",
    "game/collections/SKDocumentCollection",
    "game/models/window/SKWindowLog",
    "game/collections/SKWindowSet"

],function (
    SKMailClient
) {
    "use strict";
    function timeStringToMinutes(str) {
        if (str === undefined) {
            throw 'Time string is not defined';
        }
        var parts = str.split(':');
        return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
    }

    /**
     * Simulation class
     *
     * Объект симуляции, при создании инициализирует все коллекции. В нем также находятся (или должны находиться обработчики входящих
     * события)
     *
     * @class SKSimulation
     * @augments Backbone.Model
     */
    SKSimulation = Backbone.Model.extend(
        /** @lends SKSimulation.prototype */
        {
            /**
             * Тип симуляции. 'real' — real-режим, 'developer' — debug-режим
             * @attribute stype
             */
            /**;
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                var me = this;

                /**
                 * Список событий в данной симуляции. Правила, по которым работают события смотрим в документации по
                 * SKEventCollection
                 *
                 * @property events
                 * @type {SKEventCollection}
                 */
                this.events = new SKEventCollection();

                /**
                 * Список задач в To Do
                 *
                 * @property todo_tasks
                 * @type {SKTodoCollection}
                 */
                this.todo_tasks = new SKTodoCollection();

                /**
                 * Список звонков
                 * @type {SKPhoneHistoryCollection}
                 * @property phone_history
                 */
                this.phone_history = new SKPhoneHistoryCollection();
                this.handleEvents();
                this.on('tick', function () {
                //noinspection JSUnresolvedVariable
                    if (me.getGameMinutes() >= timeStringToMinutes(SKApp.get('end'))) {
                        me.stop();
                    }

                    // 11-00
                    if (660 === me.getGameMinutes()) {
                        me.trigger('time:11-00');
                    }
                });
                this.dayplan_tasks = new SKDayTaskCollection();
                this.documents = new SKDocumentCollection();
                this.windowLog = new SKWindowLog();
                this.skipped_seconds = 0;
                this.mailClient = new SKMailClient();
                this.window_set = new SKWindowSet([], {events:this.events});

                this.config = [];
                this.config.isMuteVideo = false;

                this.once('time:11-00', function () {
                    SKApp.server.api('dayPlan/CopyPlan', {
                        minutes:me.getGameMinutes()
                    }, function () {
                    });
                });
            },

            /**
             * Returns number of minutes past from the start of game
             *
             * @method getGameMinutes
             */
            'getGameMinutes':function () {
                return Math.floor(this.getGameSeconds()/60);
            },

            /**
             * Обработка приходящих событий:
             *
             * 1. Если приходит событие плана — обновляем список задач в ToDo
             * 2. Если приходит событие почты — заново запрашиваем список событий (чтобы почта приходила быстро)
             *
             * @method handleEvents
             */
            handleEvents: function () {
                var me = this;
                this.events.on('event:plan', function () {
                    SKApp.simulation.todo_tasks.fetch();
                });
                this.events.on('event:mail', function () {
                    me.getNewEvents();
                });
            },

            /**
             * Return game time in seconds
             *
             * @method getGameSeconds
             * @return {Number}
             */
            'getGameSeconds':function () {
                var current_time_string = new Date();
                var game_start_time = timeStringToMinutes(this.get('app').get('start')) * 60;
                return game_start_time +
                    Math.floor(
                        ((current_time_string - this.start_time) / 1000 + this.skipped_seconds) * this.get('app').get('skiliksSpeedFactor')
                    );
            },

            /**
             * Returns game time in human readable format (e.g. 09:00)
             *
             * @method getGameTime
             * @optional @param {boolean} is_seconds show seconds
             * @return {string}
             */
            'getGameTime':function (is_seconds) {
                    var sh    = this.getGameSeconds();
                    var h   = Math.floor(sh / 3600);
                    var m = Math.floor((sh - (h * 3600)) / 60);
                    var s = sh - (h * 3600) - (m * 60);
                    if (h   < 10) {h   = "0"+h;}
                    if (m < 10) {m = "0"+m;}
                    if (s < 10) {s = "0"+s;}
                return h + ':' + m + (is_seconds ? ':' + s : '');
            },

            /**
             * Parses new events and adds them to event collection
             *
             * @method parseNewEvents
             * @param events
             */
            parseNewEvents:function (events) {
                var me = this;
                events.forEach(function (event) {
                    // console.log('[SKSimulation] new event ', event.eventType);
                    if (event.eventType === 1 && (event.data === undefined || event.data.length === 0)) {
                        // Crutch, sometimes server returns empty events
                        me.events.trigger('dialog:end');
                        return;
                    }
                    var event_params = event;
                    event_params.type = event_params.eventType;
                    delete event_params.result;
                    var event_model = new SKEvent(event_params);
                    if (me.events.canAddEvent(event_model)) {
                        me.events.push(event_model);
                        me.events.trigger('event:' + event_model.getTypeSlug(), event_model);
                    } else {
                        me.events.wait(event.data[0].code, event.eventTime);
                    }
                });
            },

            /**
             * Запрашивает список новых событий, обновляет флаги и вызывает метод parseNewEvents для парсинга ада с
             * сервера
             *
             * @method getNewEvents
             */
            'getNewEvents':function (cb) {
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
                        me.parseNewEvents(data.events);
                    }
                    if (cb !== undefined) {
                        cb();
                    }
                });
            },

            /**
             * Запускает симуляцию:
             *
             * 1. Все коллекции скачиваются с сервера
             * 2. Время устанавливается в текущее
             * 3. Запускается таймер
             *
             * @method start
             * @async
             */
            'start':function () {
                var me = this;
                if (me.start_time !== undefined) {
                    throw 'Simulation already started';
                }
                me.start_time = new Date();
                SKApp.server.api('simulation/start', {
                    'mode':this.get('mode'),
                    'type':this.get('type'),
                    'invite_id': SKApp.get('invite_id')
                }, function (data) {

                    if (data.result === 0) {
                        window.location = '/';
                    }
                    
                    if ('undefined' !== typeof data.simId) {
                        me.id = data.simId;
                    }

                    var win = me.window = new SKWindow({name:'mainScreen', subname:'mainScreen'});
                    win.open();
                    me.todo_tasks.fetch();
                    me.dayplan_tasks.fetch();
                    if (!me.isDebug()) {
                        me.documents.fetch();
                    }
                    /**
                     * Срабатывает, когда симуляция уже запущена
                     * @event start
                     */
                    me.getNewEvents(function () {
                        me.trigger('start');
                    });
                    me._startTimer();
                });
            },

            /**
             * Останавливает симуляцию, останавливает таймер, отправляет оставшиеся логи
             *
             * Симуляцию второй раз запускать нельзя после этого — нужно создать новый объект (что логично). Проверки вё
             * коде на это нет
             *
             * @method stop
             * @async
             */
            'stop':function () {
                var me = this;
                me._stopTimer();

                this.window_set.deactivateActiveWindow();

                var logs = this.windowLog.getAndClear();

                SKApp.server.api('simulation/stop', {'logs':logs}, function () {
                    /**
                     * Симуляция уже остановлена
                     * @event stop
                     */
                    if(SKApp.simulation.get('result-url') === undefined){
                        SKApp.simulation.set('result-url', '/results');
                    }

                    me.trigger('stop');
                });
            },

            /**
             * Ставит симуляцию на паузу, останавливает таймер, скрывает интерфейс
             *
             * @method startPause
             * @async
             */
            startPause: function() {
                var me = this;

                me._stopTimer();
                me.paused_time = new Date();
                me.trigger('pause:start');

                SKApp.server.api('simulation/startPause', {}, function () {

                });
            },

            /**
             * Возобновляет установленную на паузу симуляцию
             *
             * @method stopPause
             * @async
             */
            stopPause: function() {
                var me = this;

                SKApp.server.api('simulation/stopPause', {}, function () {
                    me._startTimer();
                    me.skipped_seconds -= (new Date() - me.paused_time) / 1000;
                    delete me.paused_time;
                    me.trigger('pause:stop');
                });
            },

            /**
             * Начинает блокировать все действия пользователя
             */
            startInputLock: function () {
                this.trigger('input-lock:start');
            },

            /**
             * Прекращает блокировать все действия пользователя
             */
            stopInputLock: function () {
                this.trigger('input-lock:stop');
            },

            /**
             * Обновляет время в симуляции и вызывает событие tick по завершению
             *
             * @method setTime
             * @param hour
             * @param minute
             * @async
             */
            'setTime':function (hour, minute) {
                var me = this;
                SKApp.server.api('simulation/changeTime', {
                    'hour':hour,
                    'min':minute
                }, function () {
                    me.skipped_seconds +=
                        (parseInt(hour, 10) * 3600 + parseInt(minute, 10) * 60 - me.getGameSeconds()) /
                        me.get('app').get('skiliksSpeedFactor');
                    me.trigger('tick');
                });
            },

            /**
             * @method isDebug
             * @protected
             */
            _startTimer: function() {
                var me = this;

                if (me.events_timer) {
                    me._stopTimer();
                }

                me.events_timer = setInterval(function () {
                    me.getNewEvents();
                    /**
                     * Срабатывает каждую игровую минуту. Во время этого события запрашивается список событий
                     * @event tick
                     */
                    me.trigger('tick');
                }, 60000 / me.get('app').get('skiliksSpeedFactor'));
            },

            _stopTimer: function() {
                if (this.events_timer) {
                    clearInterval(this.events_timer);
                    delete this.events_timer;
                }
            },

            /**
             * @method isDebug
             * @returns {boolean}
             */
            'isDebug':function () {
                return this.get('mode') === 'developer';
            },

            /**
             * Код, который обновляет флаги. TODO: превратить его в события
             *
             * @method
             * @param flagsState
             * @param serverTime
             */
            updateFlagsForDev:function (flagsState, serverTime) {
                // Please, don't do that
                if (this.isDebug()) {
                    var flagStateView = new SKFlagStateView();
                    flagStateView.updateValues(flagsState, serverTime);
                }
            }
        });
    return SKSimulation;
});