/*global Backbone, _, $, SKApp, SKDebugView, SKIconPanelView, SKPhoneDialogView, SKVisitView,
SKImmediateVisitView, SKPhoneView, SKMailClientView, SKDialogView,
 SKPhoneCallView, SKDocumentsListView, SKXLSDisplayView, SKPDFDisplayView, SKDayPlanView,
 SKMailClientView, SKPhoneCallView, SKManualView, SKMeetingView, SKMeetingGoneView, define */

var SKSimulationView;


define([
    "text!game/jst/world/simulation_template.jst",
    "text!game/jst/world/tutorial.jst",
    "text!game/jst/dialogs/how_to_leave.jst",

    "game/views/mail/SKMailClientView",
    "game/views/documents/SKXLSDisplayView",
    "game/views/documents/SKDocumentListView",
    "game/views/plan/SKDayPlanView",
    "game/views/documents/SKPDFDisplayView",
    "game/views/phone/SKPhoneView",
    "game/views/phone/SKPhoneCallView",
    "game/views/phone/SKPhoneDialogView",
    "game/views/dialogs/SKVisitView",
    "game/views/dialogs/SKImmediateVisitView",
    "game/views/meetings/SKMeetingView",
    "game/views/meetings/SKMeetingGoneView",
    "game/views/world/SKDebugView",
    "game/views/world/SKIconPanelView",
    "game/views/world/SKManualView",
    "game/views/SKDialogView",
    "game/views/SKCrashOptionsPanelView"
], function (simulation_template, tutorial_template, how_to_leave_tpl, SKMailClientView, SKXLSDisplayView) {
    "use strict";
    /**
     * @class SKSimulationView
     * @augments Backbone.View
     */
    SKSimulationView = Backbone.View.extend(
        /**
         * @lends SKSimulationView
         */
        {
            'attributes': {
                'style': 'width: 100%; height: 100%; '
                    + ' -moz-user-select: none; '
                    + ' -webkit-user-select: none;'
                    + ' -ms-user-select:none; user-select:none;'
                    + '  line-height: 0px; '
            },

            /**
             * События DOM на которые должна реагировать данная view
             * @var Array events
             */
            'events':          {
                'click .btn-simulation-stop': 'doSimulationStop',
                'click .fullscreen':          'doToggleFullscreen',
                'click .start':               'doStartFullSimulation',
                'click .pause-control, .paused-screen .resume, .finish > a': 'doTogglePause'
            },

            'window_views':    {
                'mainScreen/manual':       SKManualView,
                'plan/plan':               SKDayPlanView,
                'phone/phoneMain':         SKPhoneView,
                'mailEmulator/mailMain':   SKMailClientView,
                'phone/phoneCall':         SKPhoneCallView,
                'phone/phoneTalk':         SKPhoneDialogView,
                'documents/documents':     SKDocumentsListView,
                'visitor/visitorEntrance': SKVisitView,
                'visitor/visitorTalk':     SKImmediateVisitView,
                'visitor/meetingChoice':   SKMeetingView,
                'visitor/meetingGone':     SKMeetingGoneView
            },
            /**
             * Массив окон, которые открыты в симуляции
             * @property windows
             */
            'windows':  {},

            /**
             * Constructor
             *
             * @method initialize
             */
            'initialize':      function () {
                try {
                    var me = this;
                    var simulation = this.simulation = SKApp.simulation;
                    this.listenTo(simulation, 'tick', this.updateTime);
                    this.listenTo(simulation, 'end', this.endWorkday);
                    this.listenTo(simulation.window_set, 'add', this.setupWindowEvents);
                    this.listenTo(simulation, 'input-lock:start', this.doStartInputLock);
                    this.listenTo(simulation, 'input-lock:stop', this.doStopInputLock);
                    this.listenTo(simulation, 'start', this.startExitProtection);
                    this.listenTo(simulation, 'start', this.startObservFullScreenMode);
                    this.listenTo(simulation, 'before-stop', this.stopExitProtection);
                    this.listenTo(simulation, 'stop-time', this.stopSimulation);
                    this.listenTo(simulation, 'documents:error', this.documentsLoadError);

                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            startExitProtection: function () {
                try {
                    $(window).on('beforeunload', function () {
                        return "Вы действительно хотите прервать симуляцию? Вы не получите оценку и запуск симуляции будет израсходован";
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            /* Generates message, that if You leave the game you will lose your data */
            stopExitProtection: function () {
                try {
                    $(window).off('beforeunload');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            startObservFullScreenMode: function() {
                try {
                $(document).on('fullscreenchange mozfullscreenchange webkitfullscreenchange', function() {
                    $('.fullscreen').toggleClass('enabled');
                });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param window
             */
            setupWindowEvents: function (window) {
                try {
                    var window_full_name = (window.get('name') + '/' + window.get('subname'));
                    if (this.window_views[window_full_name]) {
                        var WindowClass = this.window_views[window_full_name];
                        var view = new WindowClass({model_instance: window, event: window.get('sim_event')});
                        view.render();
                        this.windows[window_full_name] = view;
                    }
                    if (window.get('name') === 'documents' && window.get('subname') === 'documentsFiles') {
                        var file = window.get('document').get('name');
                        var document_view;
                        if (file.match(/\.xlsx$/) || file.match(/\.xls$/)) {
                            document_view = new SKXLSDisplayView({model_instance: window});
                        } else {
                            document_view = new SKPDFDisplayView({model_instance: window});
                        }

                        if (false === document_view.isRender) {
                            SKApp.simulation.window_set.remove(
                                SKApp.simulation.window_set.where({subname: 'documentsFiles'})
                            );
                        } else {
                            document_view.render();
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            getOpenedWindowView: function(name) {
                try {
                    return this.windows[name];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            documentsLoadError: function() {
                try {
                    var me = this;

                    new SKDialogView({
                        message: 'Сервер перегружен. Нам очень жаль... Попробуйте пожалуйста позже.',
                        buttons: []
                    });
                    if(window.gameConfig.invite_id !== null){
                        SKApp.server.api('simulation/markTutorialNotStarted', {invite_id:window.gameConfig.invite_id, location:window.location.href}, function () {});
                    }

                    setTimeout(function() {
                        me.stopExitProtection();
                        me.simulation.trigger('force-stop');
                    }, 3000);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             */
            'render':  function () {
                try {
                    var login_html = _.template(simulation_template, {
                        showPause: SKApp.isLite(),
                        showStart: SKApp.isTutorial(),
                        simulationLabel: SKApp.simulation.get("scenarioLabel")
                    });

                    this.$el.html(login_html).appendTo('body');
                    this.icon_view = new SKIconPanelView({'el': this.$('.main-screen-icons')});

                    if (this.simulation.isDebug()) {
                        this.debug_view = new SKDebugView({'el': this.$('.debug-panel')});
                    }

                    var canvas = this.$('.canvas');
                    this.updateTime();
                    this.undelegateEvents();
                    this.delegateEvents();

                    if (undefined !== SKApp.simulation) {
                        this.$('#sim-id').text(SKApp.simulation.get('id'));
                    }

                    if (undefined !== SKApp.get('skiliksSpeedFactor')) {
                        this.$('#speed-factor').text(SKApp.get('skiliksSpeedFactor'));
                    }
                    this.renderSupportBlock();
                    // hiding loading screen and setting backgorund color to white for development mode
                    $("#loading-cup").remove();
                    $("body").css("background-color", "#ffffff");

                    if ($.browser['msie']) {
                        $('body>img').height(0);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Renders siteheart block
             */
            renderSupportBlock: function renderSupportBlock() {
                if (false == SKApp.get('isDisplaySupportChat')) {
                    return;
                }
                window._shcp = [];
                window._shcp.push({
                    link_wrap_off: true,
                    widget_id : SKApp.get('SiteHeartWidgetCode'),
                    widget : "Chat",
                    side : "bottom",
                    position : "right",
                    template : "orange",
                    title : SKApp.get('SiteHeartWidgetTitle'),
                    title_offline : "Оставьте сообщение",
                    auth: window.siteHeartAuth
                });
                (function() {
                    var hcc = document.createElement("script");
                    hcc.type = "text/javascript";
                    hcc.async = true;
                    hcc.src = ("https:" === document.location.protocol ? "https" : "http")+"://widget.siteheart.com/apps/js/sh.js";
                    var s = document.head;
                    s.parentNode.insertBefore(hcc, null); }
                )();
            },

            /**
             * @method
             */
            'updateTime':      function () {
                try {
                    var parts = this.simulation.getGameTime().split(':');
                    this.$('.time .hour').text(parts[0]);
                    this.$('.time .minute').text(parts[1]);

                    if (this.$('.tutorial-mode').length) {
                        parts = this.simulation.getTutorialTime().split(':');
                        this.$('.tutorial-mode .minutes').text(parts[0]);
                        this.$('.tutorial-mode .seconds').text(parts[1]);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            endWorkday: function() {
                try {
                    SKApp.server.api('events/userSeeWorkdayEndMessage', {});
                    var me = this;

                    me.simulation.startPause(function(){});
                    me._showPausedScreen();
                        var d = new SKDialogView({
                            message: 'Рабочий день закончен',
                            buttons: [
                                {
                                    // 18:00 End work day
                                    value: 'Завершить работу',
                                    onclick: function() {
                                        // me._hidePausedScreen();
                                        me.stopExitProtection();
                                        me.stopSimulation();
                                    }
                                },
                                {
                                    value: 'Продолжить работу',
                                    onclick: function() {
                                        me._hidePausedScreen();

                                        // кнопка заменена на дверь
                                        //me.$('.canvas .finish').removeClass('hidden');

                                        var notice = new SKDialogView({
                                            'class': 'how-to-leave',
                                            'message': _.template(how_to_leave_tpl)(),
                                            'buttons': [{
                                                value: 'OK',
                                                onclick: function() {
                                                    SKApp.simulation.stopPause();
                                                }
                                            }]
                                        });


                                    }
                                }
                            ]
                        });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            'doSimulationStop':  function () {
                try {
                    window.scrollTo(0, 0);
                    SKApp.simulation.onFinishTime();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            stopSimulation: function() {
                try {
                    var me = this;
                    me.simulation.stop();
                    me._showPausedScreen();
                    if(SKApp.isFull()) {
                        var d = new SKDialogView({
                            message: 'Спасибо, симуляция завершена. <br/>'+
                                'Дождитесь, пожалуйста, расчета оценки (до 2 минут)<br/>'+
                                'и нажмите на кнопку «Перейти к результатам».',
                            buttons: [
                                {
                                    value: 'Перейти к результатам',
                                    onclick: function() {
                                        me.simulation.trigger('user-agree-with-sim-stop');
                                    }
                                }
                            ]
                        });
                    } else if (SKApp.isLite()) {
                        var d = new SKDialogView({
                            message: 'Спасибо! Демо завершена. <br/> Оценка ваших действий не проводилась.',
                            buttons: [
                                {
                                    value: 'Перейти к примеру оценки',
                                    onclick: function() {
                                        me.simulation.trigger('user-agree-with-sim-stop');
                                    }
                                }
                            ]
                        });
                    }
                    $('.mail-popup-button').hide();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doTogglePause: function() {
                try {
                    var me = this,
                        paused = me.$('.time').hasClass('paused');

                    if (paused) {
                        me._hidePausedScreen();
                        me.pausedDialog.remove();
                        SKApp.simulation.stopPause();
                    } else {
                        me._showPausedScreen(true);
                        me.pausedDialog = new SKDialogView({
                            modal: false,
                            message: 'Симуляция остановлена',
                            buttons: [
                                {
                                    value: 'Вернуться к симуляции',
                                    onclick: function() {
                                        me._hidePausedScreen();
                                        me.simulation.stopPause();
                                    }
                                },
                                {
                                    value: 'Завершить симуляцию',
                                    onclick: function() {
                                        me._hidePausedScreen();
                                        me.stopExitProtection();
                                        if(SKApp.isFull()) {
                                            var d = new SKDialogView({
                                                message: 'Спасибо, симуляция завершена.<br/>' +
                                                    'Дождитесь, пожалуйста, расчета оценки (до 2 минут)<br/>'+
                                                    'и нажмите на кнопку «Перейти к результатам».',
                                                buttons: [
                                                    {
                                                        value: 'Перейти к результатам',
                                                        onclick: function() {
                                                            me.simulation.trigger('user-agree-with-sim-stop');
                                                        }
                                                    }
                                                ]
                                            });
                                            $('.mail-popup-button').hide();
                                        } else if(SKApp.isLite()) {
                                            var d = new SKDialogView({
                                                message: 'Спасибо! Демо завершена. <br/> Оценка ваших действий не проводилась.',
                                                buttons: [
                                                    {
                                                        value: 'Перейти к примеру оценки',
                                                        onclick: function() {
                                                            me.simulation.trigger('user-agree-with-sim-stop');
                                                        }
                                                    }
                                                ]
                                            });
                                            $('.mail-popup-button').hide();
                                        } else {
                                            // tutorial
                                            var d = new SKDialogView({
                                                message: 'Завершение симуляции.',
                                                buttons: []
                                            });
                                        }
                                        me.simulation.stop();
                                    }
                                }
                            ]
                        });

                        SKApp.simulation.startPause(function(){});
                    }

                    return false;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doStartInputLock: function () {
                try {
                    this.locking_element = this.make('div',{'class': 'display-lock'});
                    this.$el.append(this.locking_element);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            doStopInputLock: function () {
                try {
                    $(this.locking_element).remove();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _showPausedScreen: function(showTopIcons) {
                try {
                    this._toggleClockFreeze(false);
                    this.$('.canvas .paused-screen')
                        .removeClass('hidden')
                        .find('.top-icons')
                        .toggleClass('hidden', !showTopIcons);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _hidePausedScreen: function() {
                try {
                    this._toggleClockFreeze(true);
                    this.$('.canvas .paused-screen')
                        .addClass('hidden');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            _toggleClockFreeze: function(run) {
                this.$('.time').toggleClass('paused', !run);
            },

            /**
             * @link http://www.sitepoint.com/html5-full-screen-api/
             *
             * @param event
             */
            doToggleFullscreen: function(event) {
                try {
                    event.preventDefault();
                    var enabled = $(event.target).hasClass('enabled'),
                        canvas = $('body')[0],
                        onMethods = ['requestFullscreen', 'mozRequestFullScreen', 'webkitRequestFullscreen'],
                        offMethods = ['cancelFullscreen', 'mozCancelFullScreen', 'webkitCancelFullScreen'];

                    _.each(enabled ? offMethods : onMethods, function(methodName) {
                        var context = enabled ? document : canvas;
                        if (typeof context[methodName] === 'function') {
                            context[methodName]();
                        }
                    });

                    // switch body class
                    if ($('body').hasClass("simulation-full-screen-mode")) {
                        $('body').removeClass("simulation-full-screen-mode");
                    } else {
                        $('body').addClass("simulation-full-screen-mode");
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doStartFullSimulation: function(e) {
                try {
                    this.message_window = new SKDialogView({
                        'message':'Завершение ознакомительной симуляции.',
                        'buttons':[]
                    });
                    e.preventDefault();
                    SKApp.simulation.stop();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKSimulationView;
});