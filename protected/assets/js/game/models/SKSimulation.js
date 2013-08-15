/*global Backbone:false, console, SKApp, SKConfig, SKWindowSet, SKWindow, SKEventCollection, SKEvent, SKWindowLog, SKMailClient */
/*global SKTodoCollection, SKDialogPanNotificationView, SKDayTaskCollection, SKPhoneHistoryCollection, SKDocumentCollection, SKDocument, $, SKDialogView, define */

var SKSimulation;

define([
    "game/models/SKMailClient",
    "game/views/develop_mode/SKFlagStateView",

    "game/collections/SKCharacterCollection",
    "game/views/SKCrashOptionsPanelView",
    "game/collections/SKEventCollection",
    "game/models/SKEvent",
    "game/collections/SKTodoCollection",
    "game/collections/SKPhoneHistoryCollection",
    "game/collections/SKDayTaskCollection",
    "game/collections/SKDocumentCollection",
    "game/models/window/SKWindowLog",
    "game/collections/SKWindowSet",
    "game/views/SKDialogPanNotificationView",
    "jquery/jquery.hotkeys"

],function (
    SKMailClient,
    SKFlagStateView,
    SKCharacterCollection,
    SKCrashOptionsPanelView
) {
    "use strict";

    /**
     * Simulation class
     *
     * Объект симуляции, при создании инициализирует все коллекции. В нем также находятся (или должны находиться обработчики входящих
     * события)
     *
     * @augments Backbone.Model
     * @class SKSimulation
     * @constructs
     */
    SKSimulation = Backbone.Model.extend(
        /** @lends SKSimulation.prototype */
        {

            timerSpeed: 60000,

            constTutorialScenario: 'tutorial',

            popups: {},

            system_options:null,

            is_paused:false,

            /**
             * Тип симуляции. 'real' — real-режим, 'developer' — debug-режим
             * @attribute stype
             */
            /**;
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                try {
                    console.log('5');
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

                    this.loadDocsDialog = null;

                    try {
                        document.domain = 'skiliks.com'; // to easy work with zoho iframes from our JS
                    } catch(e) {
                        // this to protect against busted-sj test crash
                    }

                    this.set('isZohoDocumentSuccessfullySaved', null);
                    this.set('isZohoSavedDocTestRequestSent', false);

                    this.set('ZohoDocumentSaveCheckAttempt', 1);
                    this.set('scenarioName', null);

                    this.on('tick', function () {
                        var hours = parseInt(me.getGameMinutes() / 60, 10),
                            minutes = parseInt(me.getGameMinutes() % 60, 10),
                            tasks;

                        if (me.getGameMinutes() >= me.timeStringToMinutes(SKApp.get('end'))) {
                            me.onEndTime();
                        }

                        if (me.getGameMinutes() >= me.timeStringToMinutes(SKApp.get('finish'))) {
                            me.onFinishTime();
                        }

                        me.trigger('time:' + hours + '-' + (minutes < 10 ? '0' : '') + minutes);

                        minutes += 5;
                        if (minutes >= 60) {
                            minutes = minutes % 60;
                            hours += 1;
                        }

                        tasks = me.dayplan_tasks.where({day: '1', date: hours + ':' + (minutes < 10 ? '0' : '') + minutes});
                        if (tasks.length && true !== tasks[0].get('isDisplayed')) {
                            me.showTaskNotification(tasks[0]);
                            tasks[0].set('isDisplayed', true);
                        }
                    });

                    this.dayplan_tasks = new SKDayTaskCollection();
                    this.documents = new SKDocumentCollection();
                    this.documents.bind('afterReset', this.onAddDocument, this);
                    this.windowLog = new SKWindowLog();
                    this.skipped_seconds = 0;
                    this.mailClient = new SKMailClient();
                    this.window_set = new SKWindowSet([], {events:this.events});
                    this.characters = new SKCharacterCollection();

                    this.config = [];
                    this.config.isMuteVideo = false;
                    this.manual_is_first_closed = false;

                    this.isPlayIncomingCallSound = true;
                    this.isPlayIncomingMailSound = true;

                    this.once('time:11-00', function () {
                        SKApp.server.api('dayPlan/CopyPlan', {
                            minutes:me.getGameMinutes()
                        }, function () {
                        });
                    });

                    $(window).on('message', function(event) {
                        event = event.originalEvent;
                        if (event.data) {
                            if ('DocumentLoaded' === event.data.type) {
                                me.onDocumentLoaded(event);
                            } else if ('Zoho_500' === event.data.type) {
                                me.onZoho500(event);
                            }
                        }
                    });

                    this.bindEmergencyHotkey();

                    // расскоментировать когда подчиним копирование.
                    this.initSocialcalcHotkeys();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            onAddDocument:function() {
                try {
                    if (window.elfinderInstace !== undefined) {
                        window.elfinderInstace.exec('reload');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            timeStringToMinutes: function(str) {
                try {
                    if (str === undefined) {
                        throw 'Time string is not defined';
                    }
                    var parts = str.split(':');
                    return parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            savePlan: function(callback) {
                try {
                    var me = this,
                        doc;

                    if (me.dayPlanDocId) {
                        doc = me.documents.where({id: me.dayPlanDocId})[0];
                        delete SKDocument._excel_cache[me.dayPlanDocId];
                        me.documents.remove(doc);
                    }

                    SKApp.server.api('dayPlan/save', {}, function(response) {
                        me.documents.fetch();

                        if (typeof callback === 'function') {
                            callback(response);
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            showTaskNotification: function(task) {
                try {
                    if (SKApp.isTutorial()) {
                        return;
                    }
                    var notification = new SKDialogPanNotificationView({
                        'class': 'task-notification-dialog',
                        'message': '<span class="task-time">' + task.get('date') + '</span>' +
                                   '<span class="task-description">' + task.get('title') + '</span>',
                        'modal': true,
                        'buttons': [],
                         addCloseButton: true
                    });

                    setTimeout(function() {
                        notification.remove();
                    }, 5000);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Returns number of minutes past from the start of game
             *
             * @method getGameMinutes
             */
            'getGameMinutes':function () {
                try {
                    return Math.floor(this.getGameSeconds()/60);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
                    var me = this;
                    this.events.on('event:plan', function (event) {
                        me.todo_tasks.fetch({update: true});
                    });
                    this.events.on('event:mail', function () {
                        me.getNewEvents();
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Return game time in seconds
             *
             * @method getGameSeconds
             * @return {Number}
             */
            'getGameSeconds':function () {
                try {
                    var me = this;
                    var current_time_string = me.paused_time || new Date();
                    var game_start_time = me.timeStringToMinutes(this.get('app').get('start')) * 60;
                    return game_start_time + (me.start_time ?
                        Math.floor(
                            ((current_time_string - this.start_time) / 1000 + this.skipped_seconds) * this.get('app').get('skiliksSpeedFactor')
                        ) : 0);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Returns game time in human readable format (e.g. 09:00)
             *
             * @method getGameTime
             * @optional @param {boolean} is_seconds show seconds
             * @return {string}
             */
            'getGameTime':function (params) {
                try {
                    if(params === undefined) {params = {};}
                        var sh    = this.getGameSeconds();
                        var h   = Math.floor(sh / 3600);
                        var m = Math.floor((sh - (h * 3600)) / 60);
                        var s = sh - (h * 3600) - (m * 60);
                        if (h   < 10) {h   = "0"+h;}
                        if (m < 10) {m = "0"+m;}
                        if (s < 10) {s = "0"+s;}
                    return h + ':' + m + (params.with_seconds === true ? ':' + s : '');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Parses new events and adds them to event collection
             *
             * @method parseNewEvents
             * @param events
             */
            parseNewEvents:function (events, url) {
                try {
                    var me = this;
                    events.forEach(function (event) {
                        if (event.eventType === 1 && (event.data === undefined || event.data.length === 0)) {
                            // Crutch, sometimes server returns empty events
                            me.events.trigger('dialog:end');
                            return;
                        }
                        event.type = event.eventType;
                        delete event.result;
                        var event_model = new SKEvent(event);
                        if (me.events.canAddEvent(event_model, url)) {
                            me.events.push(event_model);
                            me.events.trigger('event:' + event_model.getTypeSlug(), event_model);
                        } else if (event.data[0].code !== 'None' && event.eventTime) {
                            me.events.wait(event.data[0].code, event.eventTime);
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Запрашивает список новых событий, обновляет флаги и вызывает метод parseNewEvents для парсинга ада с
             * сервера
             *
             * @method getNewEvents
             */
            'getNewEvents':function (cb) {
                try {
                    var nowDate = new Date();
                    localStorage.setItem('lastGetState', nowDate.getTime());
                    var me = this;
                    var logs = this.windowLog.getAndClear();

                    SKApp.server.apiQueue('events', 'events/getState', {
                        logs:             logs,
                        timeString:       this.getGameMinutes(),
                        eventsQueueDepth: $("#events-queue-depth").val()
                    }, function (data) {
                        //console.log('time: ', data.serverGameTime);
                        //console.log('x: ', data.speedFactor);
                        // update flags for dev mode
                        if (undefined !== data && null !== data && undefined !== data.flagsState && undefined !== data.serverTime) {
                            me.updateFlagsForDev(data.flagsState, data.serverTime);
                            me.updateEventsListTableForDev(data.eventsQueue);
                        }

                        if (null !== data && data.result === 1 && data.events !== undefined) {
                            me.parseNewEvents(data.events, 'events/getState');
                        }
                        if (cb !== undefined) {
                            cb();
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
            'start':function (onDocsLoad) {
                try {
                    var me = this;

                    SKApp.server.api('simulation/start', {
                        mode:this.get('mode'),
                        type:this.get('type'),
                        invite_id: SKApp.get('invite_id'),
                        screen_resolution:window.screen.availWidth+'x'+window.screen.availHeight,
                        window_resolution:window.screen.width+'x'+window.screen.height
                    }, function (data) {
                        var nowDate = new Date(),
                            win;

                        if (me.start_time !== undefined) {
                            throw 'Simulation already started';
                        }

                        me.start_time = new Date();
                        localStorage.setItem('lastGetState', nowDate.getTime());

                        win = me.window = new SKWindow({name:'mainScreen', subname:'mainScreen'});
                        win.open();

                        me.todo_tasks.fetch();
                        me.dayplan_tasks.fetch();
                        me.characters.fetch();
                        me.events.getUnreadMailCount();

                        me.getNewEvents();
                        me._startTimer();
                        me.trigger('start');

                        if (data.result === 0) {
                            window.location = '/';
                        }

                        if ('undefined' !== typeof data.simId) {
                            me.id = data.simId;
                        }

                        me.set('scenarioName', data.scenarioName);

                        me.documents.fetch();
                        onDocsLoad.apply(me);
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
                    var me = this;
                    me._stopTimer();

                    this.window_set.deactivateActiveWindow();

                    var logs = this.windowLog.getAndClear();

                    SKApp.server.apiQueue('events', 'simulation/stop', {'logs':logs}, function () {
                        /**
                         * Симуляция уже остановлена
                         * @event stop
                         */
                        if(SKApp.get('result-url') === undefined){
                            SKApp.set('result-url', '/dashboard');
                            document.cookie = 'display_result_for_simulation_id=' + SKApp.simulation.id + '; path = /;';
                        }

                        $.each(SKDocument._excel_cache, function(id, url){
                            // @todo: ruge - but efficient. We didn`t care about
                            $('#excel-preload-' + id).remove();

                        });
                        me.trigger('before-stop');
                        me.trigger('stop');

                        // trick for sim-stop at 20:00
                        // see SKSimulationView.stopSimulation();
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
             * Ставит симуляцию на паузу, останавливает таймер, скрывает интерфейс
             *
             * @method startPause
             * @async
             */
            startPause: function(callback) {
                try {
                    var me = this;

                    me._stopTimer();
                    me.paused_time = new Date();
                    me.trigger('pause:start');
                    me.is_paused = true;
                    if (typeof callback === 'function') {
                        SKApp.server.api('simulation/startPause', {}, function (responce) {
                            callback(responce);
                        });
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

            },

            /**
             * Возобновляет установленную на паузу симуляцию
             *
             * @method stopPause
             * @async
             */
            stopPause: function(callback) {
                try {
                    var me = this;
                    me.is_paused = false;
                    SKApp.server.api('simulation/stopPause', {}, function (responce) {
                        if( me.paused_time !== undefined )
                        {
                            me._startTimer();
                            me.skipped_seconds -= (new Date() - me.paused_time) / 1000;
                            delete me.paused_time;
                            me.trigger('pause:stop');

                            if (typeof callback === 'function') {
                                callback(responce);
                            }
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            updatePause: function(params) {
                try {
                    if(params === undefined) {params = {};}

                    var me = this;
                    var skipped = (new Date() - me.paused_time) / 1000;
                    SKApp.server.api('simulation/updatePause', {skipped:skipped}, function (responce) {
                            if (typeof params.callback === 'function') {
                                params.callback(responce);
                            }

                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            isPaused:function() {
              return this.is_paused;
            },

            /**
             * Начинает блокировать все действия пользователя
             */
            startInputLock: function () {
                try {
                    this.trigger('input-lock:start');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Прекращает блокировать все действия пользователя
             */
            stopInputLock: function () {
                try {
                    this.trigger('input-lock:stop');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method isDebug
             * @protected
             */
            _startTimer: function() {
                try {
                    var me = this;

                    if (me.events_timer) {
                        me._stopTimer();
                    }
                    if (me.events_timer !== undefined || me.events_timer !== null){
                        me.events_timer = setInterval(function () {
                            me.getNewEvents();
                            /**
                             * Срабатывает каждую игровую минуту. Во время этого события запрашивается список событий
                             * @event tick
                             */
                            me.trigger('tick');
                        }, me.timerSpeed / me.get('app').get('skiliksSpeedFactor'));
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _stopTimer: function() {
                try {
                    if (this.events_timer) {
                        clearInterval(this.events_timer);
                        delete this.events_timer;
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            onEndTime: function() {
                try {
                    if (!this.popups.end) {
                        this.popups.end = true;
                        this.trigger('before-end');
                        this.trigger('end');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            onFinishTime: function() {
                try {
                    if (!this.popups.finish) {
                        this.popups.finish = true;
                        this.trigger('stop-time');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method isDebug
             * @returns {boolean}
             */
            'isDebug':function () {
                try {
                    return this.get('mode') === 'developer';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Код, который обновляет флаги. TODO: превратить его в события
             *
             * @method
             * @param flagsState
             * @param serverTime
             */
            updateFlagsForDev:function (flagsState, serverTime) {
                try {
                    // Please, don't do that
                    if (this.isDebug()) {
                        var flagStateView = new SKFlagStateView();
                        flagStateView.updateValues(flagsState, serverTime);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             *
             */
            updateEventsListTableForDev:function (eventsQueue) {
                try {
                    if (this.isDebug()) {
                        var flagStateView = new SKFlagStateView();
                        window.AppView.frame.debug_view.doUpdateEventsList(eventsQueue);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            initSocialcalcHotkeys: function() {
                    try {
                        var me = this;
                        $(window).bind('keydown', 'ctrl+c', function() {
                            console.log('ctrl+c');
                            me.clickSCButton('-button_copy');
                            return false;
                        });

                        $(window).bind('keydown', 'ctrl+v', function() {
                            console.log('ctrl+v');
                            me.clickSCButton('-button_paste');
                            return false;
                        });

                        $(window).bind('keydown', 'ctrl+z', function() {
                            console.log('ctrl+z');
                            me.clickSCButton('-button_undo');
                            return false;
                        });

                        $(window).bind('keydown', 'ctrl+y', function() {
                            console.log('ctrl+y');
                            me.clickSCButton('-button_redo');
                            return false;
                        });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            clickSCButton:function(selector){
                if(SKApp.simulation.window_set.hasActiveXLSWindow()){
                    console.log(selector);
                    var event = document.createEvent("MouseEvents");
                    event.initMouseEvent("mousedown", true, true, window, 1, 0, 0, 0, 0,
                        false, false, false, false, 0, null);

                    // get button for current active window
                    var data_editor_id = $('.sim-window-id-' + SKApp.simulation.window_set.getActiveWindow().window_uid).find(".sheet-tabs .active").attr('data-editor-id');
                    var id = data_editor_id+selector;//'-button_paste';
                    var buttonElement = document.getElementById(id);
                    buttonElement.dispatchEvent(event);
                }
            },
            initSystemHotkeys: function() {
                try {
                    var me = this;
                    $(window).bind('keydown', 'esc', function() {
                        console.log('esc');
                        var sim_window = $('.sim-window-id-' + SKApp.simulation.window_set.getActiveWindow().window_uid);
                        if(sim_window.length === 0) {
                            throw new Error("Window not found!");
                        }
                        var win_close = sim_window.find('.win-close');
                        console.log(win_close);
                        if(win_close.length !== 0){
                            win_close.click();
                        }
                        return false;
                    });
                    /*$(window).bind('keydown', 'esc', function() {
                        console.log('esc');

                        return false;
                    });*/
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            bindEmergencyHotkey: function() {
                var me = this;

                $(window).bind('keydown', 'ctrl+k', function() {
                    if (me.system_options === null) {
                        SKApp.server.api('simulation/isEmergencyAllowed', {}, function (data) {
                            if (data.result) {
                                me.system_options = new SKCrashOptionsPanelView({
                                    'message':'',
                                    'buttons':[]
                                });

                                me.system_options.on('close', function() {
                                    me.system_options = null;
                                });
                            }
                        });
                    } else {
                        me.system_options.remove();
                    }

                    return false;
                });
            }
        });
    return SKSimulation;
});